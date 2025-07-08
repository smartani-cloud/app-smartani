<?php

namespace App\Http\Controllers\Psc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use App\Models\Psc\PscIndicator;
use App\Models\Psc\PscIndicatorPosition;
use App\Models\Psc\PscRoleMapping;
use App\Models\Setting;
use App\Models\Unit;

class AspekEvaluasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $position = null)
    {
        $role = $request->user()->role->name;

        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1);

        $isTopManagements = in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','etl','etm']);

        // Check targets
        if($isTopManagements || (!$isTopManagements && $targetsQuery->count() > 0)){
            if($isTopManagements){
                $targetsQuery = PscRoleMapping::select('target_position_id')->where('pa_role_mapping_id',1);
            }
            $targets = clone $targetsQuery;
            $targets = $targets->get();

            if($position && in_array($position,$targets->pluck('target_position_id')->toArray())){
                $target = clone $targetsQuery;
                $target = $target->where('target_position_id',$position)->first();
            }
            elseif(!$position){
                $target = clone $targetsQuery;
                $target = $target->first();
            }
            else{
                return redirect()->route('psc.aspek.index');
            }

            $indicators = $this->getIndicators($target->target_position_id);

            $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code) ASC')->get();

            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            if(($isTopManagements && $targetsQuery->count() < 1) || (!$isTopManagements && $lock && $lock->value != 1))
                $folder = 'read-only';
            else
                $folder = 'pa';

            return view('kepegawaian.'.$folder.'.psc.aspek_index', compact('targets','target','indicators','penempatan'));
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
    public function store(Request $request, $position)
    {
        $messages = [
            'parent.required' => 'Mohon pilih salah satu IKU induk',
            'name.required' => 'Mohon tuliskan indikator kinerja utama',
            'percentage.required' => 'Mohon isi bobot nilai indikator',
            'grader.required' => 'Mohon pilih setidaknya satu jabatan yang dapat menilai',
        ];

        $this->validate($request, [
            'parent' => 'required',
            'name' => 'required',
            'percentage' => 'required',
            'grader' => 'required'
        ], $messages);

        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1);

        // Check targets
        if($targetsQuery->count() > 0){
            $targets = clone $targetsQuery;
            $targets = $targets->get();

            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            if($position && in_array($position,$targets->pluck('target_position_id')->toArray()) && ($lock && $lock->value == 1)){
                $count = PscIndicator::static()->where('level',1)->count();
                $skip = 2;
                $limit = $count - $skip;
                $parents = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip($skip)->take($limit)->get();
                if(in_array($request->parent, $parents->pluck('id')->toArray())){
                    $parent = (object) $parents->where('id',$request->parent)->first();

                    $indikator = new PscIndicator();
                    $indikator->parent_id = $parent->childs()->select('id')->first()->id;
                    $indikator->name = $request->name;
                    $indikator->level = 3;
                    $indikator->employee_id = $request->user()->pegawai->id;
                    $indikator->position_id = $request->user()->pegawai->jabatan->id;
                    $indikator->save();

                    $indikator->fresh();

                    if(isset($request->grader)){
                        $indikator->penilai()->sync($request->grader);
                    }

                    $indikatorJabatan = new PscIndicatorPosition();
                    $indikatorJabatan->position_id = $position;
                    $indikatorJabatan->percentage = $request->percentage;

                    $indikator->target()->save($indikatorJabatan);
                }

                Session::flash('success','Data indikator '.$request->name.' berhasil ditambahkan');
                
                return redirect()->route('psc.aspek.index', ['position' => $position]);
            }
            else{
                Session::flash('success','Data indikator '.$request->name.' gagal ditambahkan');

                return redirect()->route('psc.aspek.index');
            }
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $position)
    {
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1);

        // Check targets
        if($targetsQuery->count() > 0){
            $targets = clone $targetsQuery;
            $targets = $targets->get();

            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            if($position && in_array($position,$targets->pluck('target_position_id')->toArray()) && ($lock && $lock->value == 1)){
                // Inti function
                $indikatorJabatan = PscIndicatorPosition::where(['id' => $request->id,'position_id' => $position])->first();

                if($indikatorJabatan){
                    $count = PscIndicator::static()->where('level',1)->count();
                    $skip = 2;
                    $limit = $count - $skip;
                    $indicators = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip($skip)->take($limit)->get();
                    $penempatan = Jabatan::select('id','name')->aktif()->orderBy('category_id')->orderBy('code')->orderByRaw('LENGTH(code)','ASC')->get();
                    
                    $tahun = TahunAjaran::where('is_active',1)->latest()->first();

                    $usedCount = $indikatorJabatan->indikator->nilaiDetail()->whereHas('nilai',function($q)use($tahun){$q->where('academic_year_id', $tahun->id)->where(function($q){$q->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');});})->count();

                    return view('kepegawaian.pa.psc.aspek_ubah', compact('position','indikatorJabatan','indicators','penempatan','usedCount'));
                }
                else return "Data indikator tidak ditemukan";
            }
            else return "Ups, sepertinya ada yang tidak beres";
        }

        else return "Ups, tidak dapat memuat data";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $position)
    {
        $messages = [
            'editParent.required' => 'Mohon pilih salah satu IKU induk',
            'editName.required' => 'Mohon tuliskan indikator kinerja utama'
        ];

        $this->validate($request, [
            'editParent' => 'required',
            'editName' => 'required'
        ], $messages);

        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1);

        // Check targets
        if($targetsQuery->count() > 0){
            $targets = clone $targetsQuery;
            $targets = $targets->get();

            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            if($position && in_array($position,$targets->pluck('target_position_id')->toArray()) && ($lock && $lock->value == 1)){
                // Inti function
                $indikatorJabatan = PscIndicatorPosition::has('indikator')->where(['id' => $request->id,'position_id' => $position])->first();

                if($indikatorJabatan){
                    $count = PscIndicator::static()->where('level',1)->count();
                    $skip = 2;
                    $limit = $count - $skip;
                    $parents = PscIndicator::static()->whereNull('parent_id')->where('level',1)->skip($skip)->take($limit)->get();

                    if(in_array($request->editParent, $parents->pluck('id')->toArray())){
                        $parent = (object) $parents->where('id',$request->editParent)->first();

                        $indikator = $indikatorJabatan->indikator;
                        $indikator->parent_id = $parent->childs()->select('id')->first()->id;
                        $indikator->name = $request->editName;
                        $indikator->save();

                        $indikator->fresh();

                        $tahun = TahunAjaran::where('is_active',1)->latest()->first();

                        $usedCount = $indikator->nilaiDetail()->whereHas('nilai',function($q)use($tahun){$q->where('academic_year_id', $tahun->id)->where(function($q){$q->where('acc_status_id','!=',1)->orWhereNull('acc_status_id');});})->count();

                        if(isset($request->editGrader) && $usedCount < 1){
                            $indikator->penilai()->sync($request->editGrader);
                        }

                        Session::flash('success','Data indikator '.$indikator->name.' berhasil diubah');
                    }
                    else Session::flash('danger','Data IKU Induk tidak ditemukan');
                }
                else Session::flash('danger','Data indikator tidak ditemukan');
                
                return redirect()->route('psc.aspek.index', ['position' => $position]);
            }
            else{
                Session::flash('danger','Data indikator gagal diubah');

                return redirect()->route('psc.aspek.index');
            }
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Update all related resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request, $position)
    {
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1);

        // Check targets
        if($targetsQuery->count() > 0){
            $targets = clone $targetsQuery;
            $targets = $targets->get();

            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            if($position && in_array($position,$targets->pluck('target_position_id')->toArray()) && ($lock && $lock->value == 1)){
                // Inti function
                $indikator = PscIndicatorPosition::where('position_id',$position)->get();

                if($indikator){
                    $successCount = 0;
                    foreach($indikator as $i){
                        $inputName = 'value-'.$i->id;
                        $requestValue = $request->{$inputName};
                        if($requestValue && ($requestValue >= 0 && $requestValue <= 100) && ($requestValue != $i->percentage)){
                            $i->percentage = $requestValue;
                            $i->save();

                            $successCount++;
                        }
                    }

                    if($successCount > 0) Session::flash('success', $successCount.' data indikator berhasil diperbarui');
                }
                else Session::flash('success','Data indikator gagal diperbarui');
                
                return redirect()->route('psc.aspek.index', ['position' => $position]);
            }
            else{
                Session::flash('success','Data indikator gagal diperbarui');

                return redirect()->route('psc.aspek.index');
            }
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
        // Belum diedit
        $jabatan = Jabatan::find($id);

        if($jabatan){
            $jabatan->pscRoleMapping()->delete();

            Session::flash('success','Data pemetaan peran '.$jabatan->name.' berhasil dihapus');
        }
        else Session::flash('danger','Data pemetaan peran gagal dihapus');
        
        return redirect()->route('psc.peran.index');
    }

    /**
     * Relate a oldly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function relate(Request $request, $position)
    {
        $messages = [
            'indicator.required' => 'Mohon pilih salah satu indikator kinerja utama',
        ];

        $this->validate($request, [
            'indicator' => 'required'
        ], $messages);

        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1);

        // Check targets
        if($targetsQuery->count() > 0){
            $targets = clone $targetsQuery;
            $targets = $targets->get();

            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            if($position && in_array($position,$targets->pluck('target_position_id')->toArray()) && ($lock && $lock->value == 1)){
                // Inti function
                $indikator = PscIndicator::find($request->indicator);

                if($indikator && $indikator->target()->where('position_id',$position)->count() < 1){
                    $indikatorJabatan = new PscIndicatorPosition();
                    $indikatorJabatan->position_id = $position;

                    $indikator->target()->save($indikatorJabatan);

                    Session::flash('success','Data indikator '.$indikator->name.' berhasil dimasukkan');
                }
                else Session::flash('danger','Data indikator gagal dimasukkan');
                
                return redirect()->route('psc.aspek.index', ['position' => $position]);
            }
            else{
                Session::flash('danger','Data indikator gagal dimasukkan');

                return redirect()->route('psc.aspek.index');
            }
        }

        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Unrelate a oldly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unrelate(Request $request, $position, $id)
    {
        $targetsQuery = $request->user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1);

        // Check targets
        if($targetsQuery->count() > 0){
            $targets = clone $targetsQuery;
            $targets = $targets->get();

            $lock = Setting::where('name','psc_aspect_lock_status')->first();

            if($position && in_array($position,$targets->pluck('target_position_id')->toArray()) && ($lock && $lock->value == 1)){
                // Inti function
                $indikator = PscIndicatorPosition::find($id);

                if($indikator && $indikator->position_id == $position){
                    $name = $indikator->indikator->name;
                    $indikator->delete();

                    Session::flash('success','Data indikator '.$name.' berhasil dihapus dari daftar Aspek Evaluasi dan IKU');
                }
                else Session::flash('danger','Data indikator gagal dihapus dari daftar Aspek Evaluasi dan IKU');
                
                return redirect()->route('psc.aspek.index', ['position' => $position]);
            }
            else{
                Session::flash('danger','Data indikator gagal dihapus dari daftar Aspek Evaluasi dan IKU');

                return redirect()->route('psc.aspek.index');
            }
        }

        else return redirect()->route('kepegawaian.index');
    }

    public static function getIndicators($position, $parent = null){
        $indicators = null;

        $staticIndicators = PscIndicator::static();
        $nonstaticIndicators = PscIndicator::nonstatic()->fillable()->whereHas('target',function($q)use($position){
            $q->where('position_id',$position);
        });

        if($parent){
            $staticIndicators = $staticIndicators->where([
                'parent_id' => $parent
            ]);
            $nonstaticIndicators = $nonstaticIndicators->where([
                'parent_id' => $parent
            ]);
        }
        else{
            $staticIndicators = $staticIndicators->whereNull('parent_id');
            $nonstaticIndicators = $nonstaticIndicators->whereNull('parent_id');
        }

        $staticIndicators = $staticIndicators->get();
        $nonstaticIndicators = $nonstaticIndicators->get();

        $allIndikators = null;
        if($staticIndicators && count($staticIndicators) > 0){
            $allIndikators = $staticIndicators;
        }

        if($nonstaticIndicators && count($nonstaticIndicators) > 0){
            if(!$allIndikators){
                $allIndikators = $nonstaticIndicators;
            }
            else{
                $allIndikators = $allIndikators->concat($nonstaticIndicators);
            }
        }

        if($allIndikators && count($allIndikators) > 0){
            foreach($allIndikators as $i){
                if(!$indicators){
                    $indicators = collect();
                }
                $indicators = $indicators->push($i);

                if($i->childs()->count() > 0){
                    $childs = self::getIndicators($position, $i->id);
                    if($childs && count($childs) > 0){
                        $indicators = $indicators->concat($childs);
                    }
                }
            }
        }

        return $indicators;
    }
}
