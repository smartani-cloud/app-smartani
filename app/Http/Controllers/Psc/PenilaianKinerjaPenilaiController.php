<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Http\Controllers\Psc\AspekEvaluasiController;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\JabatanUnit;
use App\Models\Psc\PscGradeSet;
use App\Models\Psc\PscIndicatorPosition;
use App\Models\Psc\PscScore;
use App\Models\Psc\PscScoreIndicator;
use App\Models\Psc\PscScoreIndicatorGrader;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiJabatan;
use App\Models\Rekrutmen\PegawaiUnit;
use App\Models\LoginUser;
use App\Models\Unit;

class PenilaianKinerjaPenilaiController extends Controller
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

            if($isStaticGrader) $allUnit = Unit::all();
            else{
                $checkPositions = null;
                if($indicatorGraderQuery->count() > 0){
                    $targetPositions = null;
                    $positions = PscIndicatorPosition::select('position_id')->whereIn('indicator_id',$indicatorGraderQuery->get()->pluck('id'))->pluck('position_id')->unique()->flatten()->filter(function($value, $key)use($request){
                        return !in_array($value,$request->user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->pluck('id')->unique()->toArray());
                    })->all();
                    if($targetsQuery->count() > 0){
                        $targetPositions = $targetsQuery->pluck('target_position_id');
                        $checkPositions = collect($positions)->concat($targetPositions)->unique()->flatten()->all();
                    }
                    else{
                        $checkPositions = collect($positions);
                    }
                }
                elseif($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                    $checkPositions = $positions;
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
                $pegawais = $this->checkEmployees($request,$isStaticGrader,$unitAktif,$positions);

                if($pegawais && $pegawais->count() > 0){
                    $targets = null;
                    $isDoubleRole = false;

                    // Is a validator in this unit?
                    if($targetsQuery->count() > 0){
                        $checkPositions = $targetsQuery->pluck('target_position_id');

                        $targets = $this->checkUnvalidateEmployees($request,$unitAktif,$checkPositions);
                    }

                    if($targetsQuery->count() > 0 && $indicatorGraderQuery->count() > 0)
                        $isDoubleRole = true;

                    if(in_array($role,['ketuayys']))
                        $folder = $role;
                    else $folder = 'pa';

                    if($indicatorGraderQuery->count() > 0)
                        return view('kepegawaian.'.$folder.'.psc.penilaian_penilai_detail', compact('tahun','tahunPelajaran','allUnit','unitAktif','pegawais','targets','isDoubleRole'));
                    elseif($targetsQuery->count() > 0)
                       return redirect()->route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                }
                elseif($targetsQuery->count() > 0)
                    return redirect()->route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
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
            $allUnit = $positions = null;

            $isStaticGrader = $indicatorGraderQuery->pluck('parent_id')->unique()->contains(function($value,$key){return in_array($value,[1,4]);});

            if($isStaticGrader) $allUnit = Unit::all();
            else{
                $checkPositions = null;
                if($indicatorGraderQuery->count() > 0){
                    $targetPositions = null;
                    $positions = PscIndicatorPosition::select('position_id')->whereIn('indicator_id',$indicatorGraderQuery->get()->pluck('id'))->pluck('position_id')->unique()->flatten()->filter(function($value, $key)use($request){
                        return !in_array($value,$request->user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->pluck('id')->unique()->toArray());
                    })->all();
                    if($targetsQuery->count() > 0){
                        $targetPositions = $targetsQuery->pluck('target_position_id');
                        $checkPositions = collect($positions)->concat($targetPositions)->unique()->flatten()->all();
                    }
                    else{
                        $checkPositions = collect($positions);
                    }
                }
                elseif($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                    $checkPositions = $positions;
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
                $pegawaiAktif = $this->checkEmployee($request,$isStaticGrader,$unitAktif,$positions,$pegawai);

                if($pegawaiAktif){
                    // Inti Function
                    $jabatan = (object) $pegawaiAktif->units()->where('unit_id',$unitAktif->id)->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->unique()->first();

                    $indicators = AspekEvaluasiController::getIndicators($jabatan->id);

                    $nilai = $tahun->nilaiPsc()->where([
                        'unit_id' => $unitAktif->id,
                        'position_id' => $jabatan->id,
                        'employee_id' => $pegawaiAktif->id
                    ])->first();

                    $gradeSet = PscGradeSet::select('id')->aktif()->orderBy('created_at')->first();

                    if(in_array($role,['ketuayys']))
                        $folder = $role;
                    else $folder = 'pa';

                    return view('kepegawaian.'.$folder.'.psc.penilaian_penilai_show', compact('tahun','unitAktif','pegawaiAktif','jabatan','indicators','nilai','gradeSet'));
                }
                elseif($indicatorGraderQuery->count() > 0)
                    return redirect()->route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                elseif($targetsQuery->count() > 0)
                    return redirect()->route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
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
    public function update(Request $request, $tahun, $unit, $pegawai)
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

            if($isStaticGrader) $allUnit = Unit::all();
            else{
                $checkPositions = null;
                if($indicatorGraderQuery->count() > 0){
                    $targetPositions = null;
                    $positions = PscIndicatorPosition::select('position_id')->whereIn('indicator_id',$indicatorGraderQuery->get()->pluck('id'))->pluck('position_id')->unique()->flatten()->filter(function($value, $key)use($request){
                        return !in_array($value,$request->user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->pluck('id')->unique()->toArray());
                    })->all();
                    if($targetsQuery->count() > 0){
                        $targetPositions = $targetsQuery->pluck('target_position_id');
                        $checkPositions = collect($positions)->concat($targetPositions)->unique()->flatten()->all();
                    }
                    else{
                        $checkPositions = collect($positions);
                    }
                }
                elseif($targetsQuery->count() > 0){
                    $positions = $targetsQuery->pluck('target_position_id');
                    $checkPositions = $positions;
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
                $pegawaiAktif = $this->checkEmployee($request,$isStaticGrader,$unitAktif,$positions,$pegawai);

                if($pegawaiAktif){
                    $jabatan = (object) $pegawaiAktif->units()->where('unit_id',$unitAktif->id)->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->unique()->first();

                    $allIndicators = AspekEvaluasiController::getIndicators($jabatan->id);

                    $indicators = $allIndicators->count() > 0 ? $allIndicators->filter(function($value,$key)use($request){return $value->is_fillable && in_array($request->user()->pegawai->jabatan->id,$value->penilai()->pluck('grader_id')->toArray());}) : null;

                    if($indicators && $indicators->count() > 0){
                        $nilai = $tahun->nilaiPsc()->where([
                            'unit_id' => $unitAktif->id,
                            'position_id' => $jabatan->id,
                            'employee_id' => $pegawaiAktif->id
                        ])->first();

                        if(!$nilai || ($nilai && $nilai->acc_status_id != 1)){
                            if(!$nilai){
                                $nilai = new PscScore();
                                $nilai->unit_id = $unitAktif->id;
                                $nilai->position_id = $jabatan->id;
                                $nilai->academic_year_id = $tahun->id;
                                $nilai->employee_id = $pegawaiAktif->id;
                                $nilai->total_score = 0;
                                $nilai->save();
                                $nilai->fresh();
                            }

                            // Inti Function
                            $successCount = 0;
                            foreach($indicators->sortByDesc('level')->all() as $i){
                                $item = (object) $i;

                                $inputName = 'score-'.$i->id;
                                $requestValue = $request->{$inputName};

                                $nilaiIndikator = $nilai ? $nilai->detail()->where('indicator_id',$i->id)->first() : null;

                                if(!$nilaiIndikator){
                                    $nilaiIndikator = new PscScoreIndicator();
                                    $nilaiIndikator->psc_score_id = $nilai->id;
                                    $nilaiIndikator->indicator_id = $i->id;
                                    $nilaiIndikator->save();
                                    $nilaiIndikator->fresh();
                                }

                                $nilaiIndikatorDetail = $nilaiIndikator ? $nilaiIndikator->penilai()->where(['grader_id' => $request->user()->pegawai->id,'position_id' => $request->user()->pegawai->jabatan->id])->first() : null;

                                if(!$nilaiIndikatorDetail){
                                    $nilaiIndikatorDetail = new PscScoreIndicatorGrader();
                                    $nilaiIndikatorDetail->psi_id = $nilaiIndikator->id;
                                    $nilaiIndikatorDetail->grader_id = $request->user()->pegawai->id;
                                    $nilaiIndikatorDetail->position_id = $request->user()->pegawai->jabatan->id;
                                    $nilaiIndikatorDetail->save();
                                }

                                if($nilaiIndikatorDetail){
                                    $nilaiIndikatorDetail->score = $requestValue;
                                    if(!$nilaiIndikatorDetail->position_desc){
                                        if(in_array($request->user()->pegawai->jabatan->category_id, [1,2])){
                                            $details = '';
                                            if($request->user()->pegawai->jabatan->code == '14.11'){
                                                $details = '';
                                            }
                                            elseif($request->user()->pegawai->jabatan->code == '14.12'){
                                                $details = '';
                                            }
                                            elseif($request->user()->pegawai->jabatan->code == '14.13'){
                                                $details = '';
                                            }
                                            $nilaiIndikatorDetail->position_desc = $request->user()->pegawai->jabatan->name.$details.' '.$request->user()->pegawai->unit->name;
                                        }
                                        else{
                                            $nilaiIndikatorDetail->position_desc = $request->user()->pegawai->jabatan->name;
                                        }
                                    }
                                    $nilaiIndikatorDetail->save();
                                }

                                $thisScore = $nilaiIndikator->penilai()->avg('score');
                                $nilaiIndikator->score = $thisScore;

                                $thisPercentage = null;
                                if($item->target()->where('position_id',$jabatan->id)->count() > 0){
                                    $thisPercentage = $item->target()->select('id','percentage')->where('position_id',$jabatan->id)->first();
                                    $thisPercentage = $thisPercentage->percentage;
                                }
                                else{
                                    $thisPercentage = $i->percentage;
                                }
                                $nilaiIndikator->percentage = $thisPercentage;

                                $nilaiIndikator->total_score = $thisScore * ($thisPercentage/100);
                                $nilaiIndikator->save();

                                $nilaiIndikator->fresh();

                                $this->updateParentValues($allIndicators,$nilaiIndikator);

                                $successCount++;
                            }

                            $pscTotalScore = $nilai->detail()->whereHas('indikator',function($query){
                                $query->where('level',1);
                            })->sum('total_score');

                            $nilai->total_score = $pscTotalScore;

                            $pscGrade = null;

                            $pscGradeSet = PscGradeSet::aktif()->first();

                            foreach($pscGradeSet->grade()->orderBy('end','desc')->get() as $g){
                                if($pscTotalScore >= $g->start && $pscTotalScore <= $g->end){
                                    $pscGrade = $g;
                                }
                            }

                            if($pscGrade){
                                $nilai->grade_id = $pscGrade->id;
                                $nilai->grade_name = $pscGrade->name;
                            }

                            $nilai->save();

                            if($successCount >= $indicators->count()) Session::flash('success','Data penilaian berhasil disimpan');
                            elseif($successCount > 0) Session::flash('warning','Sebagian data penilaian gagal disimpan');
                            else Session::flash('danger','Data penilaian gagal disimpan');
                        }
                        else Session::flash('danger','Data penilaian tidak bisa disimpan');
                    }
                    else{
                        Session::flash('danger','Data penilaian untuk jabatan ini tidak ditemukan');
                    }
                    return redirect()->route('psc.penilaian.penilai.show', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name,'pegawai' => $pegawaiAktif->nip]);
                }
                elseif($indicatorGraderQuery->count() > 0)
                    return redirect()->route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                elseif($targetsQuery->count() > 0)
                    return redirect()->route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
                else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
            }
            else return redirect()->route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]);
        }

        else return redirect()->route('kepegawaian.index');
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
     * Check the specified resources from storage.
     *
     */
    public function checkEmployee(Request $request,$isStaticGrader,$unitAktif,$positions,$pegawai){
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

    /**
     * Update the specified resources from storage.
     *
     * @param  \App\Models\Psc\PscIndicator       $indikators
     * @param  \App\Models\Psc\PscScoreIndicator  $nilaiIndikator
     */
    public function updateParentValues($indikators, $nilaiIndikator)
    {
        $childScore = $nilaiIndikator;
        $nilai = $nilaiIndikator->nilai;
        while($childScore->indikator->parent_id){
            $parent = $childScore->indikator->parent;
            if($parent){
                if($parent->level == 1){
                    $childs = $parent->childs()->select('id')->pluck('id');
                }
                else{
                    $childs = $parent->childs()->select('id')->whereIn('id',$indikators->pluck('id'))->pluck('id');   
                }
                $childsScore = $nilai->detail()->whereHas('indikator',function($query)use($childs){
                    $query->whereIn('id',$childs);
                })->sum('total_score');

                $parentScore = $nilai->detail()->whereHas('indikator',function($query)use($parent){
                    $query->where('id',$parent->id);
                })->first();

                if(!$parentScore){
                    $parentScore = new PscScoreIndicator();
                    $parentScore->psc_score_id = $nilai->id;
                    $parentScore->indicator_id = $parent->id;
                    $parentScore->save();
                    $parentScore->fresh();
                }

                $parentScore->score = $childsScore;

                $thisPercentage = null;
                if($parent->target()->where('position_id',$nilai->position_id)->count() > 0){
                    $thisPercentage = $parent->target()->select('id','percentage')->where('position_id',$nilai->position_id)->first();
                    $thisPercentage = $thisPercentage->percentage;
                }
                else{
                    $thisPercentage = $parent->percentage;
                }
                $parentScore->percentage = $thisPercentage;

                $parentScore->total_score = $childsScore * ($thisPercentage/100);

                $parentScore->save();

                $parentScore->fresh();

                $childScore = $parentScore;
            }
        }
    }
}
