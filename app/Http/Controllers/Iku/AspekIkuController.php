<?php

namespace App\Http\Controllers\Iku;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;

use App\Models\Kbm\TahunAjaran;
use App\Models\Iku\IkuAspectUnit;
use App\Models\Iku\IkuCategory;
use App\Models\Iku\IkuIndicator;
use App\Models\Unit;

class AspekIkuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $iku = null, $unit = null)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1);

        $isTopManagements = in_array($role,['pembinayys','ketuayys','direktur']) ? true : false;

        if($isTopManagements || (!$isTopManagements && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();

            $categories = null;
            if($isTopManagements) $categories = IkuCategory::select('id','name')->get();
            else{
                $categories = IkuCategory::select('id','name')->whereHas('aspek',function($q)use($ikuAspects){
                    $q->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                })->get();
            }
            if(!$categories) return redirect()->route('kepegawaian.index');

            $units = Unit::sekolah()->get();

            if($iku){
                $iku = ucwords($iku);
                $iku = $categories && count($categories) > 0 ? $categories->where('name',$iku)->first() : null;
            }
            else{
                $iku = $categories && count($categories) > 0 ? $categories->first() : null;
            }
            if(!$iku) return redirect()->route('iku.aspek.index');

            if($unit){
                $unitAktif = $units->where('name','LIKE',$unit)->first();
                if($unitAktif){
                    $tahunAktif = TahunAjaran::select('id')->aktif()->first();
                    $aspects = IkuAspectUnit::where(['academic_year_id' => $tahunAktif->id,'unit_id' => $unitAktif->id])->whereHas('aspek',function($q)use($iku,$ikuAspects){
                        $q->where('iku_category_id',$iku->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    })->get();

                    if(in_array($role,['pembinayys','ketuayys','direktur'])){
                        $aspectUnits = $tahunAktif->ikuAspekUnit()->has('indikator')->where('unit_id',$unitAktif->id)->whereHas('aspek',function($q)use($iku){
                            $q->where('iku_category_id',$iku->id);
                        })->get();
                    }
                    else{
                        $aspectUnits = $tahunAktif->ikuAspekUnit()->has('indikator')->where('unit_id',$unitAktif->id)->whereHas('aspek',function($q)use($iku,$ikuAspects){
                            $q->where('iku_category_id',$iku->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        })->get();
                    }

                    if($role == 'direktur')
                        $folder = $role;
                    elseif(in_array($role,['pembinayys','ketuayys']))
                        $folder = 'read-only';
                    else
                        $folder = 'pa';

                    return view('kepegawaian.'.$folder.'.iku.aspek_iku_detail', compact('iku','categories','unitAktif','aspects','aspectUnits'));
                }
                elseif($iku) return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc]);
                else return redirect()->route('iku.aspek.index');
            }
            else return view('kepegawaian.pa.iku.aspek_iku_index', compact('iku','categories','units'));
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
    public function store(Request $request, $iku, $unit)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1);

        if($aspectCreatorQuery->count() > 0){
            $ikuAspects = $aspectCreatorQuery->get();

            $categories = IkuCategory::select('id','name')->whereHas('aspek',function($q)use($ikuAspects){
                $q->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
            })->get();
            if(!$categories) return redirect()->route('kepegawaian.index');

            $units = Unit::sekolah()->get();

            $iku = ucwords($iku);
            $iku = $categories && count($categories) > 0 ? $categories->where('name',$iku)->first() : null;
            if(!$iku) return redirect()->route('iku.aspek.index');

            $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->first();
            if($unitAktif){
                $messages = [
                    'aspect.required' => 'Mohon pilih salah satu aspek',
                    'name.required' => 'Mohon tuliskan indikator kinerja utama (IKU)',
                    'object.required' => 'Mohon tuliskan objek IKU',
                    'mt.required' => 'Mohon tuliskan alat ukur IKU',
                    'target.required' => 'Mohon tuliskan target IKU'
                ];

                $this->validate($request, [
                    'aspect' => 'required',
                    'name' => 'required',
                    'object' => 'required',
                    'mt' => 'required',
                    'target' => 'required'
                ], $messages);

                $tahunAktif = TahunAjaran::select('id')->aktif()->first();
                $aspects = IkuAspectUnit::where(['academic_year_id' => $tahunAktif->id, 'unit_id' => $unitAktif->id])->whereHas('aspek',function($q)use($iku,$ikuAspects){
                    $q->where('iku_category_id',$iku->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                })->get();

                if(in_array($request->aspect,$aspects->pluck('id')->toArray())){
                    $selectedAspect = (object) $aspects->where('id',$request->aspect)->first();
                    if($selectedAspect){
                        $indikator = $selectedAspect->indikator()->where([
                            'name' => $request->name,
                            'object' => $request->object,
                            'mt' => $request->mt,
                            'target' => $request->target,
                        ])->first();
                        if(!$indikator){
                            $indikator = new IkuIndicator();
                            $indikator->iku_aspect_unit_id = $selectedAspect->id;
                            $indikator->name = $request->name;
                            $indikator->object = $request->object;
                            $indikator->mt = $request->mt;
                            $indikator->target = $request->target;
                            $indikator->employee_id = $request->user()->pegawai->id;
                            $indikator->save();

                            Session::flash('success','Indikator kinerja utama berhasil ditambahkan');
                        }
                        else Session::flash('danger','Indikator kinerja utama sudah pernah ditambahkan');
                    }
                    else Session::flash('danger','Aspek tidak ditemukan');
                }
                else Session::flash('danger','Aspek yang dipilih tidak valid');

                return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc, 'unit' => $unitAktif->name]);
            }
            elseif($iku) return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc]);
            else return redirect()->route('iku.aspek.index');
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
    public function edit(Request $request, $iku, $unit)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1);

        if($aspectCreatorQuery->count() > 0){
            $ikuAspects = $aspectCreatorQuery->get();

            $categories = IkuCategory::select('id','name')->whereHas('aspek',function($q)use($ikuAspects){
                $q->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
            })->get();
            if(!$categories) return redirect()->route('kepegawaian.index');

            $units = Unit::sekolah()->get();

            $iku = ucwords($iku);
            $iku = $categories && count($categories) > 0 ? $categories->where('name',$iku)->first() : null;
            if(!$iku) return redirect()->route('iku.aspek.index');

            $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->first();
            if($unitAktif){
                $messages = [
                    'editAspect.required' => 'Mohon pilih salah satu aspek',
                    'editName.required' => 'Mohon tuliskan indikator kinerja utama (IKU)',
                    'editObject.required' => 'Mohon tuliskan objek IKU',
                    'editMt.required' => 'Mohon tuliskan alat ukur IKU',
                    'editTarget.required' => 'Mohon tuliskan target IKU'
                ];

                $this->validate($request, [
                    'editAspect' => 'required',
                    'editName' => 'required',
                    'editObject' => 'required',
                    'editMt' => 'required',
                    'editTarget' => 'required'
                ], $messages);

                $tahunAktif = TahunAjaran::select('id')->aktif()->first();
                $indikator = IkuIndicator::where('id',$request->editId)->whereHas('aspek',function($q)use($iku,$unitAktif,$tahunAktif,$ikuAspects){
                    $q->where(['academic_year_id' => $tahunAktif->id, 'unit_id' => $unitAktif->id])->whereHas('aspek',function($q)use($iku,$ikuAspects){
                        $q->where('iku_category_id',$iku->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    });
                })->first();

                if($indikator){
                    $aspects = IkuAspectUnit::where(['academic_year_id' => $tahunAktif->id, 'unit_id' => $unitAktif->id])->whereHas('aspek',function($q)use($iku,$ikuAspects){
                        $q->where('iku_category_id',$iku->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    })->get();

                    if(in_array($request->editAspect,$aspects->pluck('id')->toArray())){
                        $selectedAspect = $aspects->where('id',$request->editAspect)->first();
                        if($selectedAspect){
                            $checkIndikator = $selectedAspect->indikator()->where([
                                'name' => $request->editName,
                                'object' => $request->editObject,
                                'mt' => $request->editMt,
                                'target' => $request->editTarget,
                            ])->where('id','!=',$indikator->id)->first();
                            if(!$checkIndikator){
                                $indikator->iku_aspect_unit_id = $selectedAspect->id;
                                $indikator->name = $request->editName;
                                $indikator->object = $request->editObject;
                                $indikator->mt = $request->editMt;
                                $indikator->target = $request->editTarget;
                                $indikator->save();

                                Session::flash('success','Indikator kinerja utama berhasil diubah');
                            }
                            else Session::flash('danger','Indikator kinerja utama sudah pernah ditambahkan');
                        }
                        else Session::flash('danger','Aspek tidak ditemukan');
                    }
                    else Session::flash('danger','Aspek yang dipilih tidak valid');
                }
                else Session::flash('danger','IKU tidak ditemukan');

                return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc, 'unit' => $unitAktif->name]);
            }
            elseif($iku) return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc]);
            else return redirect()->route('iku.aspek.index');
        }
        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$iku,$unit,$id)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1);

        if($aspectCreatorQuery->count() > 0){
            $ikuAspects = $aspectCreatorQuery->get();

            $categories = IkuCategory::select('id','name')->whereHas('aspek',function($q)use($ikuAspects){
                $q->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
            })->get();
            if(!$categories) return redirect()->route('kepegawaian.index');

            $units = Unit::sekolah()->get();

            $iku = ucwords($iku);
            $iku = $categories && count($categories) > 0 ? $categories->where('name',$iku)->first() : null;
            if(!$iku) return redirect()->route('iku.aspek.index');

            $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->first();
            if($unitAktif){
                $tahunAktif = TahunAjaran::select('id')->aktif()->first();
                $indikator = IkuIndicator::where('id',$id)->whereHas('aspek',function($q)use($iku,$unitAktif,$tahunAktif,$ikuAspects){
                    $q->where(['academic_year_id' => $tahunAktif->id, 'unit_id' => $unitAktif->id])->whereHas('aspek',function($q)use($iku,$ikuAspects){
                        $q->where('iku_category_id',$iku->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    });
                })->first();

                if($indikator){
                    $name = $indikator->name;
                    $counter = 0;
                    if($indikator->nilai()->count() > 0) $counter++;

                    if($counter > 0){
                        $indikator->delete();
                    }
                    else{
                        $indikator->forceDelete();
                    }
                    Session::flash('success','IKU '.$name.' berhasil dihapus');
                }
                else Session::flash('danger','IKU tidak ditemukan');

                return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc, 'unit' => $unitAktif->name]);
            }
            elseif($iku) return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc]);
            else return redirect()->route('iku.aspek.index');
        }
        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Accept the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request,$iku,$unit,$id)
    {
        $role = $request->user()->role->name;

        $categories = null;
        if($role == 'direktur'){
            $categories = IkuCategory::select('id','name')->get();
        }
        else return redirect()->route('kepegawaian.index');

        $units = Unit::sekolah()->get();

        $iku = ucwords($iku);
        $iku = $categories && count($categories) > 0 ? $categories->where('name',$iku)->first() : null;
        if(!$iku) return redirect()->route('iku.aspek.index');

        $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->first();
        if($unitAktif){
            $tahunAktif = TahunAjaran::select('id')->aktif()->first();
            $indikator = IkuIndicator::where('id',$id)->whereHas('aspek',function($q)use($iku,$unitAktif,$tahunAktif){
                $q->where(['academic_year_id' => $tahunAktif->id, 'unit_id' => $unitAktif->id])->whereHas('aspek',function($q)use($iku){
                    $q->where('iku_category_id',$iku->id);
                });
            })->first();

            if($indikator){                
                $name = $indikator->name;
                if(!$indikator->director_acc_status_id || $indikator->director_acc_status_id != 1){
                    $indikator->director_acc_id = $request->user()->pegawai->id;
                    $indikator->director_acc_status_id = 1;
                    $indikator->director_acc_time = Date::now('Asia/Jakarta');
                    $indikator->save();

                    Session::flash('success','IKU '.$name.' berhasil disetujui');
                }
                else Session::flash('danger','IKU '.$name.' telah disetujui');
            }
            else Session::flash('danger','IKU tidak ditemukan');

            return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc, 'unit' => $unitAktif->name]);
        }
        elseif($iku) return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc]);
        else return redirect()->route('iku.aspek.index');
    }

    /**
     * Accept all resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function acceptAll(Request $request,$iku,$unit)
    {
        $role = $request->user()->role->name;

        $categories = null;
        if($role == 'direktur'){
            $categories = IkuCategory::select('id','name')->get();
        }
        else return redirect()->route('kepegawaian.index');

        $units = Unit::sekolah()->get();

        $iku = ucwords($iku);
        $iku = $categories && count($categories) > 0 ? $categories->where('name',$iku)->first() : null;
        if(!$iku) return redirect()->route('iku.aspek.index');

        $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->first();
        if($unitAktif){
            $tahunAktif = TahunAjaran::select('id')->aktif()->first();
            $indikator = IkuIndicator::whereHas('aspek',function($q)use($iku,$unitAktif,$tahunAktif){
                $q->where(['academic_year_id' => $tahunAktif->id, 'unit_id' => $unitAktif->id])->whereHas('aspek',function($q)use($iku){
                    $q->where('iku_category_id',$iku->id);
                });
            })->where(function($q){
                $q->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
            });

            $indicatorCount = $indikator->count();

            if($indicatorCount > 0){
                $indikator->update([
                    'director_acc_id' => $request->user()->pegawai->id,
                    'director_acc_status_id' => 1,
                    'director_acc_time' => Date::now('Asia/Jakarta')
                ]);

                Session::flash('success', $indicatorCount.' IKU berhasil disetujui');
            }
            else Session::flash('danger','Tidak IKU yang perlu disetujui');

            return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc, 'unit' => $unitAktif->name]);
        }
        elseif($iku) return redirect()->route('iku.aspek.index',['iku' =>$iku->nameLc]);
        else return redirect()->route('iku.aspek.index');
    }
}
