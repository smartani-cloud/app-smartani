<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;

use App\Http\Controllers\Psc\AspekEvaluasiController;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Psc\PscGradeRecord;
use App\Models\Psc\PscIndicatorPosition;
use App\Models\Psc\PscScoreIndicator;
use App\Models\Psc\PscValidator;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiJabatan;
use App\Models\Rekrutmen\PegawaiUnit;
use App\Models\LoginUser;
use App\Models\Unit;

class PenilaianKinerjaValidatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun, $unit)
    {
        $role = $request->user()->role->name;

        // Is a validator?
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',2);
        // Is a grader?
        $indicatorGraderQuery = $request->user()->pegawai->jabatan->penilaiPsc();

        // Check targets
        if($targetsQuery->count() > 0 || $indicatorGraderQuery->count() > 0){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('psc.penilaian.index');
            $tahunPelajaran = TahunAjaran::where('is_active',1)->orHas('nilaiPsc')->orderBy('created_at')->get();

            // Check available units
            $allUnit = $positions = null;

            $isStaticGrader = $indicatorGraderQuery->pluck('parent_id')->unique()->contains(function($value,$key){return in_array($value,[1,4]);});

            if($isStaticGrader){
                $allUnit = Unit::all();
                $positions = null;
                if($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                }
            }
            else{
                $checkPositions = null;
                if($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                    $checkPositions = $positions;
                }
                elseif($indicatorGraderQuery->count() > 0){
                    $positions = PscIndicatorPosition::select('position_id')->whereIn('indicator_id',$indicatorGraderQuery->get()->pluck('id'))->pluck('position_id')->unique()->flatten()->filter(function($value, $key)use($request){
                        return !in_array($value,$request->user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->pluck('id')->unique()->toArray());
                    })->all();
                }
                if($role == 'kepsek'){
                    $allUnit = JabatanUnit::whereIn('position_id',$checkPositions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($request){
                        return ($value->is_school == 1 && $value->id == $request->user()->pegawai->unit_id) || (!$value->is_school);
                    })->all();
                }
                else{
                    $allUnit = JabatanUnit::whereIn('position_id',$checkPositions)->with('unit')->get()->pluck('unit')->unique()->all();
                }
                $allUnit = collect($allUnit);
            }

            $unitAktif = $allUnit->where('name','LIKE',str_replace('-',' ',$unit))->first();
            if($unitAktif){
                $pegawais = $this->checkUnvalidateEmployees($request,$unitAktif,$positions);

                if($pegawais && $pegawais->count() > 0){
                    $targets = null;
                    $isDoubleRole = false;

                    // Is a grader in this unit?
                    if($indicatorGraderQuery->count() > 0){
                        $checkPositions = PscIndicatorPosition::select('position_id')->whereIn('indicator_id',$indicatorGraderQuery->get()->pluck('id'))->pluck('position_id')->unique()->flatten()->filter(function($value, $key)use($request){
                            return !in_array($value,$request->user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->pluck('id')->unique()->toArray());
                        })->all();

                        $targets = $this->checkEmployees($request,$isStaticGrader,$unitAktif,$checkPositions);
                    }

                    if($targetsQuery->count() > 0 && $indicatorGraderQuery->count() > 0)
                        $isDoubleRole = true;

                    $pegawais = $pegawais->filter(function($value,$key)use($tahun,$unitAktif){
                        return $value->pscScore()->where([
                            'unit_id' => $unitAktif->id,
                            'academic_year_id' => $tahun->id
                        ])->count() > 0;
                    })->all();

                    if($targetsQuery->count() > 0)
                        return view('kepegawaian.pa.psc.penilaian_validator_detail', compact('tahun','tahunPelajaran','allUnit','unitAktif','pegawais','targets','isDoubleRole'));
                    elseif($indicatorGraderQuery->count() > 0)
                        return redirect()->route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                }
                elseif($indicatorGraderQuery->count() > 0)
                    return redirect()->route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]);
            }
            else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]);
        }
        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $tahun, $unit, $pegawai)
    {
        $role = $request->user()->role->name;

        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',2);
        $indicatorGraderQuery = $request->user()->pegawai->jabatan->penilaiPsc();

        // Check targets
        if($targetsQuery->count() > 0 || $indicatorGraderQuery->count() > 0){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('psc.penilaian.index');

            // Check available units
            $allUnit = null;

            $isStaticGrader = $indicatorGraderQuery->pluck('parent_id')->unique()->contains(function($value,$key){return in_array($value,[1,4]);});

            if($isStaticGrader){
                $allUnit = Unit::all();
                $positions = null;
                if($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                }
            }
            else{
                $checkPositions = null;
                if($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                    $checkPositions = $positions;
                }
                elseif($indicatorGraderQuery->count() > 0){
                    $positions = PscIndicatorPosition::select('position_id')->whereIn('indicator_id',$indicatorGraderQuery->get()->pluck('id'))->pluck('position_id')->unique()->flatten()->filter(function($value, $key)use($request){
                        return !in_array($value,$request->user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->pluck('id')->unique()->toArray());
                    })->all();
                }
                if($role == 'kepsek'){
                    $allUnit = JabatanUnit::whereIn('position_id',$checkPositions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($request){
                        return ($value->is_school == 1 && $value->id == $request->user()->pegawai->unit_id) || (!$value->is_school);
                    })->all();
                }
                else{
                    $allUnit = JabatanUnit::whereIn('position_id',$checkPositions)->with('unit')->get()->pluck('unit')->unique()->all();
                }
                $allUnit = collect($allUnit);
            }

            $unitAktif = $allUnit->where('name','LIKE',str_replace('-',' ',$unit))->first();
            if($unitAktif){
                $pegawaiAktif = $this->checkUnvalidateEmployee($request,$unitAktif,$positions,$pegawai);

                if($pegawaiAktif){
                    // Inti Function
                    $jabatan = (object) $pegawaiAktif->units()->where('unit_id',$unitAktif->id)->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->unique()->first();

                    $indicators = AspekEvaluasiController::getIndicators($jabatan->id);

                    $nilai = $tahun->nilaiPsc()->where([
                        'unit_id' => $unitAktif->id,
                        'position_id' => $jabatan->id,
                        'employee_id' => $pegawaiAktif->id
                    ])->first();

                    return view('kepegawaian.pa.psc.penilaian_validator_show', compact('tahun','unitAktif','pegawaiAktif','jabatan','indicators','nilai'));
                }
                elseif($targetsQuery->count() > 0)
                    return redirect()->route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                elseif($indicatorGraderQuery->count() > 0)
                    return redirect()->route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
            }
            else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]);
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Accept the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $tahun, $unit, $pegawai)
    {
        $role = $request->user()->role->name;

        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',2);
        $indicatorGraderQuery = $request->user()->pegawai->jabatan->penilaiPsc();

        // Check targets
        if($targetsQuery->count() > 0 || $indicatorGraderQuery->count() > 0){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('psc.penilaian.index');

            // Check available units
            $allUnit = $positions = null;

            $isStaticGrader = $indicatorGraderQuery->pluck('parent_id')->unique()->contains(function($value,$key){return in_array($value,[1,4]);});

            if($isStaticGrader){
                $allUnit = Unit::all();
                $positions = null;
                if($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                }
            }
            else{
                $checkPositions = null;
                if($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                    $checkPositions = $positions;
                }
                elseif($indicatorGraderQuery->count() > 0){
                    $positions = PscIndicatorPosition::select('position_id')->whereIn('indicator_id',$indicatorGraderQuery->get()->pluck('id'))->pluck('position_id')->unique()->flatten()->filter(function($value, $key)use($request){
                        return !in_array($value,$request->user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->pluck('id')->unique()->toArray());
                    })->all();
                }
                if($role == 'kepsek'){
                    $allUnit = JabatanUnit::whereIn('position_id',$checkPositions)->with('unit')->get()->pluck('unit')->unique()->filter(function($value, $key)use($request){
                        return ($value->is_school == 1 && $value->id == $request->user()->pegawai->unit_id) || (!$value->is_school);
                    })->all();
                }
                else{
                    $allUnit = JabatanUnit::whereIn('position_id',$checkPositions)->with('unit')->get()->pluck('unit')->unique()->all();
                }
                $allUnit = collect($allUnit);
            }

            $unitAktif = $allUnit->where('name','LIKE',str_replace('-',' ',$unit))->first();
            if($unitAktif){
                $pegawaiAktif = $this->checkUnvalidateEmployee($request,$unitAktif,$positions,$pegawai);

                if($pegawaiAktif){
                    $jabatan = (object) $pegawaiAktif->units()->where('unit_id',$unitAktif->id)->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->unique()->first();

                    $allIndicators = AspekEvaluasiController::getIndicators($jabatan->id);

                    if($allIndicators && $allIndicators->count() > 0){
                        $indicators = AspekEvaluasiController::getIndicators($jabatan->id);

                        $nilai = $tahun->nilaiPsc()->where([
                            'unit_id' => $unitAktif->id,
                            'position_id' => $jabatan->id,
                            'employee_id' => $pegawaiAktif->id,
                        ])->first();

                        if($nilai && ($nilai->acc_status_id != 1) && ($nilai->detail()->count() >= $allIndicators->count())){
                            $level = null;
                            if($indicators->count() > 0){
                                for($i=1;$i<=$indicators->max('level');$i++){
                                    $no[$i] = 1;
                                }
                            }
                            foreach($indicators as $i){
                                $item = (object) $i;

                                if(!$level) $level = $i['level'];
                                elseif($level == $i['level']) $no[$i['level']]++;
                                elseif($level != $i['level']){
                                    if(($level > $i['level']) && ($i['level'] >= 1)){
                                        $no[$level] = 1;
                                        $no[$i['level']]++;
                                    }
                                    $level = $i['level'];
                                }
                                $number = null;
                                for($j=$i['level'];$j>0;$j--){
                                    if($j == $i['level']){
                                        $number = $no[$j];
                                    }
                                    else{
                                        $number = $no[$j].'.'.$number;
                                    }
                                }

                                $nilaiIndikator = $nilai->detail()->where('indicator_id',$i->id)->first();

                                if(!$nilaiIndikator){
                                    $nilaiIndikator = new PscScoreIndicator();
                                    $nilaiIndikator->psc_score_id = $nilai->id;
                                    $nilaiIndikator->indicator_id = $i->id;
                                    $nilaiIndikator->score = 0;

                                    $thisPercentage = null;
                                    if($item->target()->where('position_id',$jabatan->id)->count() > 0){
                                        $thisPercentage = $item->target()->select('id','percentage')->where('position_id',$jabatan->id)->first();
                                        $thisPercentage = $thisPercentage->percentage;
                                    }
                                    else{
                                        $thisPercentage = $i->percentage;
                                    }
                                    $nilaiIndikator->percentage = $thisPercentage;

                                    $nilaiIndikator->total_score = 0;
                                }
                                
                                $nilaiIndikator->code = $number;
                                $nilaiIndikator->save();
                                //$nilaiIndikator->fresh();
                            }

                            $validator = PscValidator::where(['position_desc' => (isset($request->user()->pegawai->jabatan->desc) ? $request->user()->pegawai->jabatan->desc : $request->user()->pegawai->jabatan->name), 'validator_name' => $request->user()->pegawai->name])->first();
                            if(!$validator){
                                $validator = new PscValidator();
                                $validator->position_desc = (isset($request->user()->pegawai->jabatan->desc) ? $request->user()->pegawai->jabatan->desc : $request->user()->pegawai->jabatan->name);
                                $validator->validator_name = $request->user()->pegawai->name;
                                $validator->save();

                                $validator->fresh();
                            }

                            $rentangJson = $nilai->grade->set->grade()->select('name','start','end')->orderBy('end','desc')->get()->toJson();

                            $record = PscGradeRecord::where('grades',$rentangJson)->first();
                            if(!$record){
                                $record = new PscGradeRecord();
                                $record->grades = $rentangJson;
                                $record->save();

                                $record->fresh();
                            }

                            $nilai->position_name = isset($jabatan->desc) ? $jabatan->desc : $jabatan->name;
                            $nilai->employee_name = $pegawaiAktif->name;
                            $nilai->psc_grade_record_id = $record ? $record->id : null;
                            $nilai->validator_id = $validator ? $validator->id : null;
                            $nilai->acc_employee_id = $request->user()->pegawai->id;
                            $nilai->acc_status_id = 1;
                            $nilai->acc_time = Date::now('Asia/Jakarta');
                            $nilai->save();

                            Session::flash('success','Data penilaian berhasil disetujui');
                        }
                        else{
                            if(!$nilai) Session::flash('danger','Data penilaian tidak ditemukan');
                            elseif($nilai && ($nilai->acc_status_id == 1)) Session::flash('danger','Data penilaian sudah disetujui');
                            else Session::flash('danger','Data penilaian gagal disetujui');
                        }
                    }
                    else{
                        Session::flash('danger','Data penilaian untuk jabatan ini tidak ditemukan');
                    }
                    return redirect()->route('psc.penilaian.validator.show', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name,'pegawai' => $pegawaiAktif->nip]);
                }
                elseif($targetsQuery->count() > 0)
                    return redirect()->route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                elseif($indicatorGraderQuery->count() > 0)
                    return redirect()->route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
            }
            else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]);
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Check the specified resources from storage.
     *
     */
    public function checkUnvalidateEmployee(Request $request,$unitAktif,$positions,$pegawai){
        $role = $request->user()->role->name;

        $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');
        $nonpegawai = LoginUser::select('user_id')->whereHas('role',function($q){
            $q->whereIn('code',['10','29']);
        })->pluck('user_id');

        $pegawaiUnits = PegawaiUnit::select('employee_id')->where('unit_id',$unitAktif->id)->whereHas('jabatans',function($q)use($positions){
                $q->whereIn('position_id',$positions);
            })->pluck('employee_id');

        $pegawaiAktif = Pegawai::select('id','name','nip')->where('nip',$pegawai)->whereIn('id',$pegawaiUnits);

        if(in_array($role,['pembinayys','ketuayys','direktur'])){
            $pegawaiAktif = $pegawaiAktif->where('nip','!=','0')->whereNotIn('id',$nonpegawai);
        }
        elseif($role != 'admin'){
            $pegawaiAktif = $pegawaiAktif->where('nip','!=','0')->where('id','!=',$request->user()->pegawai->id)->whereNotIn('id',$pejabat->concat($nonpegawai));
        }

        $pegawaiAktif = $pegawaiAktif->aktif()->orderBy('created_at','desc')->first();

        return $pegawaiAktif;
    }


    /**
     * Check the specified resources from storage.
     *
     */
    public function checkEmployees(Request $request,$isStaticGrader,$unitAktif,$positions){
        $role = $request->user()->role->name;

        $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');
        $nonpegawai = LoginUser::select('user_id')->whereHas('role',function($q){
            $q->whereIn('code',['10','29']);
        })->pluck('user_id');

        if($isStaticGrader){
            $pegawaiUnits = PegawaiUnit::select('employee_id')->where('unit_id',$unitAktif->id)->has('jabatans')->pluck('employee_id');
        }
        else{
            $pegawaiUnits = PegawaiUnit::select('employee_id')->where('unit_id',$unitAktif->id)->whereHas('jabatans',function($q)use($positions){
                $q->whereIn('position_id',$positions);
            })->pluck('employee_id');
        }

        $pegawais = Pegawai::select('id','name','photo','nip','join_date','employee_status_id')->whereIn('id',$pegawaiUnits);

        if(in_array($role,['pembinayys','ketuayys','direktur'])){
            $pegawais = $pegawais->where('nip','!=','0')->whereNotIn('id',$nonpegawai);
        }
        elseif($role != 'admin'){
            $pegawais = $pegawais->where('nip','!=','0')->where('id','!=',$request->user()->pegawai->id)->whereNotIn('id',$pejabat->concat($nonpegawai));
        }

        $pegawais = $pegawais->aktif()->orderBy('created_at','desc')->get();

        return $pegawais;
    }

    /**
     * Check the specified resources from storage.
     *
     */
    public function checkUnvalidateEmployees(Request $request,$unitAktif,$positions){
        $role = $request->user()->role->name;

        $pejabat = PegawaiJabatan::whereIn('position_id',['15','16','17'])->with('pegawaiUnit')->get()->pluck('pegawaiUnit')->pluck('employee_id');
        $nonpegawai = LoginUser::select('user_id')->whereHas('role',function($q){
            $q->whereIn('code',['10','29']);
        })->pluck('user_id');

        $pegawaiUnits = PegawaiUnit::select('employee_id')->where('unit_id',$unitAktif->id)->whereHas('jabatans',function($q)use($positions){
                $q->whereIn('position_id',$positions);
            })->pluck('employee_id');

        $pegawais = Pegawai::select('id','name','photo','nip','join_date','employee_status_id')->whereIn('id',$pegawaiUnits);

        if(in_array($role,['pembinayys','ketuayys','direktur'])){
            $pegawais = $pegawais->where('nip','!=','0')->whereNotIn('id',$nonpegawai);
        }
        elseif($role != 'admin'){
            $pegawais = $pegawais->where('nip','!=','0')->where('id','!=',$request->user()->pegawai->id)->whereNotIn('id',$pejabat->concat($nonpegawai));
        }

        $pegawais = $pegawais->aktif()->orderBy('created_at','desc')->get();

        return $pegawais;
    }
}
