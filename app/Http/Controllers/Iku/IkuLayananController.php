<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Iku;

use App\Http\Controllers\Controller;

use File;
use Session;
use Jenssegers\Date\Date;

use App\Models\Iku\IkuAchievement;
use App\Models\Iku\IkuAchievementDetail;
use App\Models\Iku\IkuCategory;
use App\Models\Iku\IkuIndicator;
use App\Models\Kbm\TahunAjaran;
use App\Models\Unit;

use Illuminate\Http\Request;

class IkuLayananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $unit = null)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',3);

        $isTopManagements = in_array($role,['pembinayys','ketuayys','direktur']) ? true : false;

        if($isTopManagements || (!$isTopManagements && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();
            $fillableIkuAspects = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',4)->get();

            $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

            if($tahun){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            }
            else{
                $tahun = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');
            $tahunPelajaran = TahunAjaran::orderBy('created_at')->get();

            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else{
                if($tahun->is_active == 1){
                    $unitList = Unit::sekolah();
                    if($isTopManagements){
                        $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun){
                            $q->whereHas('aspek',function($q)use($category){
                                $q->where('iku_category_id', $category->id);
                            })->whereHas('indikator',function($q)use($category,$tahun){
                                $q->whereHas('nilai.iku',function($q)use($category,$tahun){
                                    $q->where([
                                        'iku_category_id' => $category->id,
                                        'academic_year_id' => $tahun->id,
                                    ]);
                                })->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        });
                    }
                    else{
                        $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                            $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                                $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                            })->whereHas('indikator',function($q){
                                $q->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        });
                    }
                    $unitList = $unitList->get();
                }
                else{
                    $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                        $q->where([
                            'iku_category_id' => $category->id,
                            'academic_year_id' => $tahun->id,
                            'director_acc_status_id' => 1
                        ]);
                    })->get();
                }
            }

            if($unit){
                if(in_array($role,['kepsek','wakasek'])){
                    $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                            $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                                $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                            })->whereHas('indikator',function($q){
                                $q->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        })->first();
                }
                else{
                    $unitAktif = $unitList->where('name','LIKE',$unit)->first();
                }

                if($unitAktif){
                    $aspectUnits = $tahun->ikuAspekUnit()->where('unit_id',$unitAktif->id);

                    if($isTopManagements){
                        $aspectUnits = $aspectUnits->whereHas('aspek',function($q)use($category){
                            $q->where('iku_category_id',$category->id);
                        })->whereHas('indikator',function($q)use($category,$tahun,$unitAktif){
                            $q->where('director_acc_status_id', 1)->whereHas('nilai.iku',function($q)use($category,$tahun,$unitAktif){
                                $q->where([
                                    'iku_category_id' => $category->id,
                                    'academic_year_id' => $tahun->id,
                                    'unit_id' => $unitAktif->id
                                ]);
                            });
                        });
                    }
                    else{
                        $aspectUnits = $aspectUnits->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        })->whereHas('indikator',function($q){
                            $q->where('director_acc_status_id', 1);
                        });
                    }

                    $aspectUnits = $aspectUnits->get();

                    if(count($aspectUnits) > 0){
                        $nilai = $tahun->nilaiIku()->where([
                            'iku_category_id' => $category->id,
                            'unit_id' => $unitAktif->id
                        ])->first();

                        if($role == 'direktur')
                            $folder = $role;
                        elseif(in_array($role,['pembinayys','ketuayys']))
                            $folder = 'read-only';
                        else $folder = 'pa';

                        return view('kepegawaian.'.$folder.'.iku.'.$category->nameLc.'_detail', compact('ikuAspects','fillableIkuAspects','category','tahun','tahunPelajaran','unitAktif','aspectUnits','nilai'));
                    }
                    else return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
                }
                else{
                    if(in_array($role,['kepsek','wakasek'])){
                        return redirect()->route('kepegawaian.index');
                    }
                    else return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
                }
            }
            else{
                if(in_array($role,['kepsek','wakasek'])){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route('iku.'.$category->nameLc.'.index',['tahun' => $tahun->academicYearLink, 'unit' => $unit->name]);
                }
            }

            return view('kepegawaian.pa.iku.capaian_index', compact('category','tahun','tahunPelajaran','unitList'));
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
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $tahun, $unit)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',4);

        if($role == 'direktur' || ($role != 'direktur' && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();

            $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');

            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else{
                if($tahun->is_active == 1){
                    $unitList = Unit::sekolah();
                    if($role == 'direktur'){
                        $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun){
                            $q->whereHas('aspek',function($q)use($category){
                                $q->where('iku_category_id', $category->id);
                            })->whereHas('indikator',function($q)use($category,$tahun){
                                $q->whereHas('nilai.iku',function($q)use($category,$tahun){
                                    $q->where([
                                        'iku_category_id' => $category->id,
                                        'academic_year_id' => $tahun->id,
                                    ]);
                                })->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        });
                    }
                    else{
                        $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                            $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                                $q->where('iku_category_id', $category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                            })->whereHas('indikator',function($q){
                                $q->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        });
                    }
                    $unitList = $unitList->get();
                }
                else{
                    $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                        $q->where([
                            'iku_category_id' => $category->id,
                            'academic_year_id' => $tahun->id,
                            'director_acc_status_id' => 1
                        ]);
                    })->get();
                }
            }

            if(in_array($role,['kepsek','wakasek'])){
                $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                    $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                        $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    })->whereHas('indikator',function($q){
                        $q->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                })->first();
            }
            else{
                $unitAktif = $unitList->where('name','LIKE',$unit)->first();
            }

            if($unitAktif){
                $indikator = null;
                if(isset($request->id)){
                    $indikator = IkuIndicator::where([
                        'id' => $request->id,
                        'director_acc_status_id' => 1
                    ])->whereHas('aspek',function($q)use($tahun,$unitAktif,$category,$ikuAspects){
                        $q->where([
                            'academic_year_id' => $tahun->id,
                            'unit_id' => $unitAktif->id
                        ])->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        });
                    })->first();
                }

                if($indikator){
                    $nilai = $tahun->nilaiIku()->where([
                        'iku_category_id' => $category->id,
                        'unit_id' => $unitAktif->id
                    ])->first();

                    if(in_array($role,['ketuayys','direktur']))
                        $folder = $role;
                    else $folder = 'pa';

                    return view('kepegawaian.'.$folder.'.iku.capaian_ubah', compact('category','tahun','unitAktif','indikator','nilai'));
                }
                else return "IKU tidak ditemukan";
            }
            else return "Unit tidak valid";

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
    public function update(Request $request, $tahun, $unit)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',4);

        if($role == 'direktur' || ($role != 'direktur' && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();

            $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');

            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else{
                if($tahun->is_active == 1){
                    $unitList = Unit::sekolah()->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                        $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id', $category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        })->whereHas('indikator',function($q){
                            $q->where('director_acc_status_id',1);
                        })->where('academic_year_id',$tahun->id);
                    })->get();
                }
                else{
                    $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                        $q->where([
                            'iku_category_id' => $category->id,
                            'academic_year_id' => $tahun->id,
                            'director_acc_status_id' => 1
                        ]);
                    })->get();
                }
            }

            if(in_array($role,['kepsek','wakasek'])){
                $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                    $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                        $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    })->whereHas('indikator',function($q){
                        $q->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                })->first();
            }
            else{
                $unitAktif = $unitList->where('name','LIKE',$unit)->first();
            }

            if($unitAktif){
                $indikator = null;
                if(isset($request->id)){
                    $indikator = IkuIndicator::where([
                        'id' => $request->id,
                        'director_acc_status_id' => 1
                    ])->whereHas('aspek',function($q)use($tahun,$unitAktif,$category,$ikuAspects){
                        $q->where([
                            'academic_year_id' => $tahun->id,
                            'unit_id' => $unitAktif->id
                        ])->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        });
                    })->first();
                }

                if($indikator){
                    $messages = [
                        'editAttachment.file' => 'Pastikan berkas Anda adalah berkas yang valid',
                        'editAttachment.max' => 'Ukuran berkas yang boleh diunggah maksimum 5 MB'
                    ];

                    $this->validate($request, [
                        'editAttachment' => 'nullable|file|max:5120'
                    ], $messages);

                    $isNew = false;

                    $nilai = $tahun->nilaiIku()->where([
                        'iku_category_id' => $category->id,
                        'unit_id' => $unitAktif->id
                    ])->first();

                    if(!$nilai){
                        $nilai = new IkuAchievement();
                        $nilai->iku_category_id = $category->id;
                        $nilai->academic_year_id = $tahun->id;
                        $nilai->unit_id = $unitAktif->id;
                        $nilai->status_id = 1;
                        $nilai->save();

                        $isNew = true;
                    }

                    if($isNew){
                        if($request->file('editAttachment') && $request->file('editAttachment')->isValid()) {
                        // Pindah berkas lampiran ke folder public
                            $file = $request->file('editAttachment');
                            $attachment = $request->id . '_' . time() . '_file.' . $file->extension();
                            $file->move('upload/iku/'.$category->nameLc.'/'.$tahun->academicYearLink.'/'.$unitAktif->name.'/', $attachment);
                        }

                        $nilaiIndikator = new IkuAchievementDetail();
                        $nilaiIndikator->indicator_id = $indikator->id;
                        $nilaiIndikator->is_achieved = 0;
                        if(isset($attachment)) $nilaiIndikator->attachment = $attachment;
                        if(isset($request->editLink)) $nilaiIndikator->link = $request->editLink;
                        if(isset($request->editNote)) $nilaiIndikator->note = $request->editNote;
                        $nilaiIndikator->employee_id = $request->user()->pegawai->id;

                        $nilai->detail()->save($nilaiIndikator);
                    }
                    else{
                        $nilaiIndikator = $nilai->detail()->where('indicator_id',$indikator->id)->first();

                        if($request->file('editAttachment') && $request->file('editAttachment')->isValid()) {
                            $dir = 'upload/iku/'.$category->nameLc.'/'.$tahun->academicYearLink.'/'.$unitAktif->name.'/';
                        // Hapus berkas lampiran di folder public
                            if(File::exists($dir.$nilaiIndikator->attachment)) File::delete($dir.$nilaiIndikator->attachment);

                        // Pindah berkas lampiran ke folder public
                            $file = $request->file('editAttachment');
                            $attachment = $request->id . '_' . time() . '_file.' . $file->extension();
                            $file->move($dir, $attachment);
                        }

                        $nilaiIndikator->attachment = isset($attachment) ? $attachment : $nilaiIndikator->attachment;
                        $nilaiIndikator->link = isset($request->editLink) ? $request->editLink : null;
                        $nilaiIndikator->note = isset($request->editNote) ? $request->editNote : null;
                        $nilaiIndikator->save();
                    }

                    Session::flash('success','Data IKU '. $indikator->name .' berhasil '.($isNew ? 'ditambahkan' : 'diubah'));
                }
                else Session::flash('danger','IKU tidak ditemukan');

                return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
            }
            else Session::flash('danger','Unit tidak valid');

            return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
        }
        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request, $tahun, $unit)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',4);

        if($role == 'direktur' || ($role != 'direktur' && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();

            $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');

            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else{
                if($tahun->is_active == 1){
                    $unitList = Unit::sekolah()->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                        $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id', $category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        })->whereHas('indikator',function($q){
                            $q->where('director_acc_status_id',1);
                        })->where('academic_year_id',$tahun->id);
                    })->get();
                }
                else{
                    $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                        $q->where([
                            'iku_category_id' => $category->id,
                            'academic_year_id' => $tahun->id,
                            'director_acc_status_id' => 1
                        ]);
                    })->get();
                }
            }

            if(in_array($role,['kepsek','wakasek'])){
                $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                    $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                        $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    })->whereHas('indikator',function($q){
                        $q->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                })->first();
            }
            else{
                $unitAktif = $unitList->where('name','LIKE',$unit)->first();
            }

            if($unitAktif){
            // Inti function
                $aspectUnits = $tahun->ikuAspekUnit()->where('unit_id',$unitAktif->id)->whereHas('aspek',function($q)use($category,$ikuAspects){
                    $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                })->whereHas('indikator',function($q){
                    $q->where('director_acc_status_id', 1);
                })->get();

                if(count($aspectUnits) > 0){
                    $nilai = $tahun->nilaiIku()->where([
                        'iku_category_id' => $category->id,
                        'unit_id' => $unitAktif->id
                    ])->first();

                    if(!$nilai){
                        $nilai = new IkuAchievement();
                        $nilai->iku_category_id = $category->id;
                        $nilai->academic_year_id = $tahun->id;
                        $nilai->unit_id = $unitAktif->id;
                        $nilai->status_id = 1;
                        $nilai->save();
                    }

                    $successCount = 0;
                    $indicatorCount = 0;
                    foreach($aspectUnits as $a){
                        $indicatorCount += $a->indikator()->where('director_acc_status_id',1)->count();
                        foreach($a->indikator()->where('director_acc_status_id',1)->get() as $i){
                            $inputName = 'indicator-'.$i->id;
                            $requestValue = $request->{$inputName};
                            $isAchieved = isset($requestValue) && $requestValue == 'on' ? 1 : 0;

                            $nilaiIndikator = $nilai->detail()->where('indicator_id',$i->id)->first();

                            if(!$nilaiIndikator){
                                $nilaiIndikator = new IkuAchievementDetail();
                                $nilaiIndikator->indicator_id = $i->id;
                                $nilaiIndikator->is_achieved = $isAchieved;
                                $nilaiIndikator->employee_id = $request->user()->pegawai->id;

                                $nilai->detail()->save($nilaiIndikator);
                            }
                            elseif($nilaiIndikator->director_acc_status_id != 1){
                                $nilaiIndikator->is_achieved = $isAchieved;
                                $nilaiIndikator->save();
                            }

                            $successCount++;
                        }
                    }

                    if($successCount >= $indicatorCount) Session::flash('success','Data capaian IKU berhasil disimpan');
                    elseif($successCount > 0) Session::flash('warning','Sebagian data capaian IKU gagal disimpan');
                    else Session::flash('danger','Data capaian IKU gagal disimpan');
                }
                else  Session::flash('danger','Data indikator kinerja utama tidak ditemukan');

                return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
            }
            else Session::flash('danger','Unit tidak valid');

            return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
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
     * Accept the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request,$tahun,$unit)
    {
        $role = $request->user()->role->name;

        $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');

        if($tahun->is_active == 1){
            $unitList = Unit::sekolah();
            if($role == 'direktur'){
                $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun){
                    $q->whereHas('aspek',function($q)use($category){
                        $q->where('iku_category_id', $category->id);
                    })->whereHas('indikator',function($q)use($category,$tahun){
                        $q->whereHas('nilai.iku',function($q)use($category,$tahun){
                            $q->where([
                                'iku_category_id' => $category->id,
                                'academic_year_id' => $tahun->id,
                            ]);
                        })->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                });
            }
            else{
                $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun){
                    $q->whereHas('aspek',function($q)use($category){
                        $q->where('iku_category_id', $category->id);
                    })->whereHas('indikator',function($q){
                        $q->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                });
            }
            $unitList = $unitList->get();
        }
        else{
            $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                $q->where([
                    'iku_category_id' => $category->id,
                    'academic_year_id' => $tahun->id,
                    'director_acc_status_id' => 1
                ]);
            })->get();
        }

        $unitAktif = $unitList->where('name','LIKE',$unit)->first();

        if($unitAktif){
            $indikator = null;
            if(isset($request->id)){
                $indikator = IkuIndicator::where([
                    'id' => $request->id,
                    'director_acc_status_id' => 1
                ])->whereHas('aspek',function($q)use($tahun,$unitAktif,$category){
                    $q->where([
                        'academic_year_id' => $tahun->id,
                        'unit_id' => $unitAktif->id
                    ])->whereHas('aspek',function($q)use($category){
                        $q->where('iku_category_id',$category->id);
                    });
                })->first();
            }

            if($indikator){
                $nilai = $tahun->nilaiIku()->where([
                    'iku_category_id' => $category->id,
                    'unit_id' => $unitAktif->id
                ])->first();

                if($nilai){
                    $nilaiIndikator = $nilai->detail()->where('indicator_id',$indikator->id)->first();

                    if(!$nilaiIndikator->director_acc_status_id){
                        $nilaiIndikator->director_acc_id = $request->user()->pegawai->id;
                        $nilaiIndikator->director_acc_status_id = 1;
                        $nilaiIndikator->director_acc_time = Date::now('Asia/Jakarta');
                        $nilaiIndikator->save();

                        Session::flash('success','Data capaian IKU '. $indikator->name .' berhasil setujui');
                    }
                    else Session::flash('danger','Data capaian IKU '. $indikator->name .' telah disetujui');
                }
                else Session::flash('danger','Data capaian IKU belum bisa disetujui');
            }
            else Session::flash('danger','IKU tidak ditemukan');

            return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
        }
        else Session::flash('danger','Unit tidak valid');

        return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
    }
}
=======
<?php

namespace App\Http\Controllers\Iku;

use App\Http\Controllers\Controller;

use File;
use Session;
use Jenssegers\Date\Date;

use App\Models\Iku\IkuAchievement;
use App\Models\Iku\IkuAchievementDetail;
use App\Models\Iku\IkuCategory;
use App\Models\Iku\IkuIndicator;
use App\Models\Kbm\TahunAjaran;
use App\Models\Unit;

use Illuminate\Http\Request;

class IkuLayananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $unit = null)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',3);

        $isTopManagements = in_array($role,['pembinayys','ketuayys','direktur']) ? true : false;

        if($isTopManagements || (!$isTopManagements && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();
            $fillableIkuAspects = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',4)->get();

            $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

            if($tahun){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            }
            else{
                $tahun = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');
            $tahunPelajaran = TahunAjaran::orderBy('created_at')->get();

            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else{
                if($tahun->is_active == 1){
                    $unitList = Unit::sekolah();
                    if($isTopManagements){
                        $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun){
                            $q->whereHas('aspek',function($q)use($category){
                                $q->where('iku_category_id', $category->id);
                            })->whereHas('indikator',function($q)use($category,$tahun){
                                $q->whereHas('nilai.iku',function($q)use($category,$tahun){
                                    $q->where([
                                        'iku_category_id' => $category->id,
                                        'academic_year_id' => $tahun->id,
                                    ]);
                                })->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        });
                    }
                    else{
                        $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                            $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                                $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                            })->whereHas('indikator',function($q){
                                $q->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        });
                    }
                    $unitList = $unitList->get();
                }
                else{
                    $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                        $q->where([
                            'iku_category_id' => $category->id,
                            'academic_year_id' => $tahun->id,
                            'director_acc_status_id' => 1
                        ]);
                    })->get();
                }
            }

            if($unit){
                if(in_array($role,['kepsek','wakasek'])){
                    $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                            $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                                $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                            })->whereHas('indikator',function($q){
                                $q->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        })->first();
                }
                else{
                    $unitAktif = $unitList->where('name','LIKE',$unit)->first();
                }

                if($unitAktif){
                    $aspectUnits = $tahun->ikuAspekUnit()->where('unit_id',$unitAktif->id);

                    if($isTopManagements){
                        $aspectUnits = $aspectUnits->whereHas('aspek',function($q)use($category){
                            $q->where('iku_category_id',$category->id);
                        })->whereHas('indikator',function($q)use($category,$tahun,$unitAktif){
                            $q->where('director_acc_status_id', 1)->whereHas('nilai.iku',function($q)use($category,$tahun,$unitAktif){
                                $q->where([
                                    'iku_category_id' => $category->id,
                                    'academic_year_id' => $tahun->id,
                                    'unit_id' => $unitAktif->id
                                ]);
                            });
                        });
                    }
                    else{
                        $aspectUnits = $aspectUnits->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        })->whereHas('indikator',function($q){
                            $q->where('director_acc_status_id', 1);
                        });
                    }

                    $aspectUnits = $aspectUnits->get();

                    if(count($aspectUnits) > 0){
                        $nilai = $tahun->nilaiIku()->where([
                            'iku_category_id' => $category->id,
                            'unit_id' => $unitAktif->id
                        ])->first();

                        if($role == 'direktur')
                            $folder = $role;
                        elseif(in_array($role,['pembinayys','ketuayys']))
                            $folder = 'read-only';
                        else $folder = 'pa';

                        return view('kepegawaian.'.$folder.'.iku.'.$category->nameLc.'_detail', compact('ikuAspects','fillableIkuAspects','category','tahun','tahunPelajaran','unitAktif','aspectUnits','nilai'));
                    }
                    else return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
                }
                else{
                    if(in_array($role,['kepsek','wakasek'])){
                        return redirect()->route('kepegawaian.index');
                    }
                    else return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
                }
            }
            else{
                if(in_array($role,['kepsek','wakasek'])){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route('iku.'.$category->nameLc.'.index',['tahun' => $tahun->academicYearLink, 'unit' => $unit->name]);
                }
            }

            return view('kepegawaian.pa.iku.capaian_index', compact('category','tahun','tahunPelajaran','unitList'));
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
    public function show($id){
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $tahun, $unit)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',4);

        if($role == 'direktur' || ($role != 'direktur' && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();

            $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');

            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else{
                if($tahun->is_active == 1){
                    $unitList = Unit::sekolah();
                    if($role == 'direktur'){
                        $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun){
                            $q->whereHas('aspek',function($q)use($category){
                                $q->where('iku_category_id', $category->id);
                            })->whereHas('indikator',function($q)use($category,$tahun){
                                $q->whereHas('nilai.iku',function($q)use($category,$tahun){
                                    $q->where([
                                        'iku_category_id' => $category->id,
                                        'academic_year_id' => $tahun->id,
                                    ]);
                                })->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        });
                    }
                    else{
                        $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                            $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                                $q->where('iku_category_id', $category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                            })->whereHas('indikator',function($q){
                                $q->where('director_acc_status_id',1);
                            })->where('academic_year_id',$tahun->id);
                        });
                    }
                    $unitList = $unitList->get();
                }
                else{
                    $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                        $q->where([
                            'iku_category_id' => $category->id,
                            'academic_year_id' => $tahun->id,
                            'director_acc_status_id' => 1
                        ]);
                    })->get();
                }
            }

            if(in_array($role,['kepsek','wakasek'])){
                $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                    $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                        $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    })->whereHas('indikator',function($q){
                        $q->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                })->first();
            }
            else{
                $unitAktif = $unitList->where('name','LIKE',$unit)->first();
            }

            if($unitAktif){
                $indikator = null;
                if(isset($request->id)){
                    $indikator = IkuIndicator::where([
                        'id' => $request->id,
                        'director_acc_status_id' => 1
                    ])->whereHas('aspek',function($q)use($tahun,$unitAktif,$category,$ikuAspects){
                        $q->where([
                            'academic_year_id' => $tahun->id,
                            'unit_id' => $unitAktif->id
                        ])->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        });
                    })->first();
                }

                if($indikator){
                    $nilai = $tahun->nilaiIku()->where([
                        'iku_category_id' => $category->id,
                        'unit_id' => $unitAktif->id
                    ])->first();

                    if(in_array($role,['ketuayys','direktur']))
                        $folder = $role;
                    else $folder = 'pa';

                    return view('kepegawaian.'.$folder.'.iku.capaian_ubah', compact('category','tahun','unitAktif','indikator','nilai'));
                }
                else return "IKU tidak ditemukan";
            }
            else return "Unit tidak valid";

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
    public function update(Request $request, $tahun, $unit)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',4);

        if($role == 'direktur' || ($role != 'direktur' && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();

            $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');

            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else{
                if($tahun->is_active == 1){
                    $unitList = Unit::sekolah()->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                        $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id', $category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        })->whereHas('indikator',function($q){
                            $q->where('director_acc_status_id',1);
                        })->where('academic_year_id',$tahun->id);
                    })->get();
                }
                else{
                    $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                        $q->where([
                            'iku_category_id' => $category->id,
                            'academic_year_id' => $tahun->id,
                            'director_acc_status_id' => 1
                        ]);
                    })->get();
                }
            }

            if(in_array($role,['kepsek','wakasek'])){
                $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                    $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                        $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    })->whereHas('indikator',function($q){
                        $q->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                })->first();
            }
            else{
                $unitAktif = $unitList->where('name','LIKE',$unit)->first();
            }

            if($unitAktif){
                $indikator = null;
                if(isset($request->id)){
                    $indikator = IkuIndicator::where([
                        'id' => $request->id,
                        'director_acc_status_id' => 1
                    ])->whereHas('aspek',function($q)use($tahun,$unitAktif,$category,$ikuAspects){
                        $q->where([
                            'academic_year_id' => $tahun->id,
                            'unit_id' => $unitAktif->id
                        ])->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        });
                    })->first();
                }

                if($indikator){
                    $messages = [
                        'editAttachment.file' => 'Pastikan berkas Anda adalah berkas yang valid',
                        'editAttachment.max' => 'Ukuran berkas yang boleh diunggah maksimum 5 MB'
                    ];

                    $this->validate($request, [
                        'editAttachment' => 'nullable|file|max:5120'
                    ], $messages);

                    $isNew = false;

                    $nilai = $tahun->nilaiIku()->where([
                        'iku_category_id' => $category->id,
                        'unit_id' => $unitAktif->id
                    ])->first();

                    if(!$nilai){
                        $nilai = new IkuAchievement();
                        $nilai->iku_category_id = $category->id;
                        $nilai->academic_year_id = $tahun->id;
                        $nilai->unit_id = $unitAktif->id;
                        $nilai->status_id = 1;
                        $nilai->save();

                        $isNew = true;
                    }

                    if($isNew){
                        if($request->file('editAttachment') && $request->file('editAttachment')->isValid()) {
                        // Pindah berkas lampiran ke folder public
                            $file = $request->file('editAttachment');
                            $attachment = $request->id . '_' . time() . '_file.' . $file->extension();
                            $file->move('upload/iku/'.$category->nameLc.'/'.$tahun->academicYearLink.'/'.$unitAktif->name.'/', $attachment);
                        }

                        $nilaiIndikator = new IkuAchievementDetail();
                        $nilaiIndikator->indicator_id = $indikator->id;
                        $nilaiIndikator->is_achieved = 0;
                        if(isset($attachment)) $nilaiIndikator->attachment = $attachment;
                        if(isset($request->editLink)) $nilaiIndikator->link = $request->editLink;
                        if(isset($request->editNote)) $nilaiIndikator->note = $request->editNote;
                        $nilaiIndikator->employee_id = $request->user()->pegawai->id;

                        $nilai->detail()->save($nilaiIndikator);
                    }
                    else{
                        $nilaiIndikator = $nilai->detail()->where('indicator_id',$indikator->id)->first();

                        if($request->file('editAttachment') && $request->file('editAttachment')->isValid()) {
                            $dir = 'upload/iku/'.$category->nameLc.'/'.$tahun->academicYearLink.'/'.$unitAktif->name.'/';
                        // Hapus berkas lampiran di folder public
                            if(File::exists($dir.$nilaiIndikator->attachment)) File::delete($dir.$nilaiIndikator->attachment);

                        // Pindah berkas lampiran ke folder public
                            $file = $request->file('editAttachment');
                            $attachment = $request->id . '_' . time() . '_file.' . $file->extension();
                            $file->move($dir, $attachment);
                        }

                        $nilaiIndikator->attachment = isset($attachment) ? $attachment : $nilaiIndikator->attachment;
                        $nilaiIndikator->link = isset($request->editLink) ? $request->editLink : null;
                        $nilaiIndikator->note = isset($request->editNote) ? $request->editNote : null;
                        $nilaiIndikator->save();
                    }

                    Session::flash('success','Data IKU '. $indikator->name .' berhasil '.($isNew ? 'ditambahkan' : 'diubah'));
                }
                else Session::flash('danger','IKU tidak ditemukan');

                return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
            }
            else Session::flash('danger','Unit tidak valid');

            return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
        }
        else return redirect()->route('kepegawaian.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request, $tahun, $unit)
    {
        $role = $request->user()->role->name;

        $aspectCreatorQuery = $request->user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',4);

        if($role == 'direktur' || ($role != 'direktur' && $aspectCreatorQuery->count() > 0)){
            $ikuAspects = $aspectCreatorQuery->get();

            $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');

            if(in_array($role,['kepsek','wakasek'])){
                $myUnit = $request->user()->pegawai->unit->name;
                if($unit && $unit != $myUnit){
                    $unit = null;
                }
                else $unit = $myUnit;
            }
            else{
                if($tahun->is_active == 1){
                    $unitList = Unit::sekolah()->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                        $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                            $q->where('iku_category_id', $category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                        })->whereHas('indikator',function($q){
                            $q->where('director_acc_status_id',1);
                        })->where('academic_year_id',$tahun->id);
                    })->get();
                }
                else{
                    $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                        $q->where([
                            'iku_category_id' => $category->id,
                            'academic_year_id' => $tahun->id,
                            'director_acc_status_id' => 1
                        ]);
                    })->get();
                }
            }

            if(in_array($role,['kepsek','wakasek'])){
                $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->whereHas('ikuAspek',function($q)use($category,$tahun,$ikuAspects){
                    $q->whereHas('aspek',function($q)use($category,$ikuAspects){
                        $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                    })->whereHas('indikator',function($q){
                        $q->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                })->first();
            }
            else{
                $unitAktif = $unitList->where('name','LIKE',$unit)->first();
            }

            if($unitAktif){
            // Inti function
                $aspectUnits = $tahun->ikuAspekUnit()->where('unit_id',$unitAktif->id)->whereHas('aspek',function($q)use($category,$ikuAspects){
                    $q->where('iku_category_id',$category->id)->whereIn('id',$ikuAspects->pluck('iku_aspect_id'));
                })->whereHas('indikator',function($q){
                    $q->where('director_acc_status_id', 1);
                })->get();

                if(count($aspectUnits) > 0){
                    $nilai = $tahun->nilaiIku()->where([
                        'iku_category_id' => $category->id,
                        'unit_id' => $unitAktif->id
                    ])->first();

                    if(!$nilai){
                        $nilai = new IkuAchievement();
                        $nilai->iku_category_id = $category->id;
                        $nilai->academic_year_id = $tahun->id;
                        $nilai->unit_id = $unitAktif->id;
                        $nilai->status_id = 1;
                        $nilai->save();
                    }

                    $successCount = 0;
                    $indicatorCount = 0;
                    foreach($aspectUnits as $a){
                        $indicatorCount += $a->indikator()->where('director_acc_status_id',1)->count();
                        foreach($a->indikator()->where('director_acc_status_id',1)->get() as $i){
                            $inputName = 'indicator-'.$i->id;
                            $requestValue = $request->{$inputName};
                            $isAchieved = isset($requestValue) && $requestValue == 'on' ? 1 : 0;

                            $nilaiIndikator = $nilai->detail()->where('indicator_id',$i->id)->first();

                            if(!$nilaiIndikator){
                                $nilaiIndikator = new IkuAchievementDetail();
                                $nilaiIndikator->indicator_id = $i->id;
                                $nilaiIndikator->is_achieved = $isAchieved;
                                $nilaiIndikator->employee_id = $request->user()->pegawai->id;

                                $nilai->detail()->save($nilaiIndikator);
                            }
                            elseif($nilaiIndikator->director_acc_status_id != 1){
                                $nilaiIndikator->is_achieved = $isAchieved;
                                $nilaiIndikator->save();
                            }

                            $successCount++;
                        }
                    }

                    if($successCount >= $indicatorCount) Session::flash('success','Data capaian IKU berhasil disimpan');
                    elseif($successCount > 0) Session::flash('warning','Sebagian data capaian IKU gagal disimpan');
                    else Session::flash('danger','Data capaian IKU gagal disimpan');
                }
                else  Session::flash('danger','Data indikator kinerja utama tidak ditemukan');

                return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
            }
            else Session::flash('danger','Unit tidak valid');

            return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
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
     * Accept the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request,$tahun,$unit)
    {
        $role = $request->user()->role->name;

        $category = IkuCategory::select('id','name')->where('name','Layanan')->first();

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');

        if($tahun->is_active == 1){
            $unitList = Unit::sekolah();
            if($role == 'direktur'){
                $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun){
                    $q->whereHas('aspek',function($q)use($category){
                        $q->where('iku_category_id', $category->id);
                    })->whereHas('indikator',function($q)use($category,$tahun){
                        $q->whereHas('nilai.iku',function($q)use($category,$tahun){
                            $q->where([
                                'iku_category_id' => $category->id,
                                'academic_year_id' => $tahun->id,
                            ]);
                        })->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                });
            }
            else{
                $unitList = $unitList->whereHas('ikuAspek',function($q)use($category,$tahun){
                    $q->whereHas('aspek',function($q)use($category){
                        $q->where('iku_category_id', $category->id);
                    })->whereHas('indikator',function($q){
                        $q->where('director_acc_status_id',1);
                    })->where('academic_year_id',$tahun->id);
                });
            }
            $unitList = $unitList->get();
        }
        else{
            $unitList = Unit::sekolah()->whereHas('ikuNilai',function($q)use($category,$tahun){
                $q->where([
                    'iku_category_id' => $category->id,
                    'academic_year_id' => $tahun->id,
                    'director_acc_status_id' => 1
                ]);
            })->get();
        }

        $unitAktif = $unitList->where('name','LIKE',$unit)->first();

        if($unitAktif){
            $indikator = null;
            if(isset($request->id)){
                $indikator = IkuIndicator::where([
                    'id' => $request->id,
                    'director_acc_status_id' => 1
                ])->whereHas('aspek',function($q)use($tahun,$unitAktif,$category){
                    $q->where([
                        'academic_year_id' => $tahun->id,
                        'unit_id' => $unitAktif->id
                    ])->whereHas('aspek',function($q)use($category){
                        $q->where('iku_category_id',$category->id);
                    });
                })->first();
            }

            if($indikator){
                $nilai = $tahun->nilaiIku()->where([
                    'iku_category_id' => $category->id,
                    'unit_id' => $unitAktif->id
                ])->first();

                if($nilai){
                    $nilaiIndikator = $nilai->detail()->where('indicator_id',$indikator->id)->first();

                    if(!$nilaiIndikator->director_acc_status_id){
                        $nilaiIndikator->director_acc_id = $request->user()->pegawai->id;
                        $nilaiIndikator->director_acc_status_id = 1;
                        $nilaiIndikator->director_acc_time = Date::now('Asia/Jakarta');
                        $nilaiIndikator->save();

                        Session::flash('success','Data capaian IKU '. $indikator->name .' berhasil setujui');
                    }
                    else Session::flash('danger','Data capaian IKU '. $indikator->name .' telah disetujui');
                }
                else Session::flash('danger','Data capaian IKU belum bisa disetujui');
            }
            else Session::flash('danger','IKU tidak ditemukan');

            return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]);
        }
        else Session::flash('danger','Unit tidak valid');

        return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
