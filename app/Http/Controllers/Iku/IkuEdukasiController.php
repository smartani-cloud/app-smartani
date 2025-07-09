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
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\RefIklas;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiJabatan;
use App\Models\LoginUser;
use App\Models\Unit;

use Illuminate\Http\Request;

class IkuEdukasiController extends Controller
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

            $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

            if($request->tahun){
                $tahun = str_replace("-","/",$request->tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            }
            else{
                $tahun = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');
            $tahunPelajaran = TahunAjaran::orderBy('created_at')->get();

            $unitList = null;
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
                    $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->first();
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

                    if($role == 'kepsek' || ($role != 'kepsek' && count($aspectUnits) > 0)){
                        $nilai = $tahun->nilaiIku()->where([
                            'iku_category_id' => $category->id,
                            'unit_id' => $unitAktif->id
                        ])->first();

                        // Grafik
                        $chart = isset($request->chart) ? $request->chart : 'mapel';

                        $semester = isset($request->semester) ? $request->semester : '1';

                        $chartList = collect([
                            [
                                'name' => 'Mapel',
                                'link' => 'mapel'
                            ],
                            [
                                'name' => 'IKLaS',
                                'link' => 'iklas'
                            ],
                            [
                                'name' => 'USP',
                                'link' => 'usp'
                            ],
                        ]);

                        $semesterList = $tahun->semester;

                        $kelasList = $mataPelajaran = $datasets = null;

                        if($chart == 'iklas'){
                            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 5) ? $request->score : null;
                        }
                        else{
                            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 100) ? $request->score : null;
                        }

                        if($chart && $chartList->where('link',$chart)->count() > 0){
                            if($chart == 'usp'){
                                $semester = Semester::where(['semester_id' => $tahun->academic_year.'-2', 'semester' => 'Genap'])->first();
                            }
                            else{
                                $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
                            }
                            if($semester){
                                if(isset($score)){
                                    if(in_array($chart,['mapel','usp'])){
                                        $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unitAktif->id);

                                        if($chart == 'usp'){
                                            $kelasList = $kelasList->whereHas('level',function($q){
                                                $q->whereIn('level',['6','9','12']);
                                            });
                                        }

                                        $kelasList = $kelasList->get();

                                        $kelompok_umum = $unitAktif->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                                        if($unitAktif->name == "SMA"){
                                            $kelompok_peminatan = $unitAktif->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->has('jurusan')->get();
                                            $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                                        }
                                        else $kelompok = $kelompok_umum;

                                        $mapelFiltered = MataPelajaran::select(['id','subject_name','subject_acronym','group_subject_id'])->whereIn('group_subject_id', $kelompok->pluck('id'));

                                        if($semester->is_active == 0){
                                            $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                                $q->where('semester_id',$semester->id);
                                            });
                                        }

                                        $mapel = clone $mapelFiltered;
                                        $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                                        if($mapel->count() > 0){
                                            $mataPelajaran = $mapel->get();
                                        }

                                        $mapelMulok = clone $mapelFiltered;
                                        $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                                        if($mapelMulok->count() > 0){
                                            $mataPelajaran = $mataPelajaran->concat($mapelMulok->get());
                                        }

                                        // Counting scores
                                        $classes = null;
                                        foreach($kelasList as $k){
                                            foreach($mataPelajaran as $m){
                                                $totalKelas = $totalSiswa = 0;
                                                $checked = true;

                                                if($unitAktif->name == 'SD'){
                                                    if($m->mapelKelas()->where('level_id',$k->level_id)->count() > 0){
                                                        $checked = true;
                                                    }
                                                    else $checked = false;
                                                }

                                                if($unitAktif->name == 'SMA'){
                                                    if($m->kmps && ($m->kmps->major_id && ($m->kmps->major_id != $k->major_id))){
                                                        $checked = false;
                                                    }
                                                }

                                                // Students history in this class
                                                $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                                foreach($riyawatKelas as $r){
                                                    $siswa = $r->siswa()->select('id')->first();
                                                    if($chart == 'mapel'){
                                                        $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                                                        $pengetahuan = $rapor ? $rapor->pengetahuan()->select('score_knowledge')->where('subject_id',$m->id)->whereNotNull('score_knowledge')->first() : null;
                                                        $score_knowledge = $pengetahuan ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '-';
                                                        if($score_knowledge != '-'){
                                                            if($score_knowledge >= $score) $totalKelas++;
                                                        }
                                                        $totalSiswa++;
                                                    }
                                                    elseif($chart == 'usp'){
                                                        $usp = $siswa->usp()->select('score')->where('semester_id', $semester->id)->where('subject_id',$m->id)->first();

                                                        $score_usp = $usp ? number_format((float)$usp->score, 0, ',', '') : '-';

                                                        if($score_usp != '-'){
                                                            if($score_usp >= $score) $totalKelas++;
                                                        }
                                                        $totalSiswa++;
                                                    }
                                                }
                                                $kelas = collect([
                                                    [
                                                        'id' => $k->id,
                                                        'subject_id' => $m->id,
                                                        'total' => $totalKelas,
                                                        'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0,
                                                        'checked' => $checked
                                                    ]
                                                ]);
                                                if($classes){
                                                    $classes = $classes->concat($kelas);
                                                }
                                                else{
                                                    $classes = $kelas;
                                                }
                                            }
                                        }

                                        $matapelajarans = null;
                                        foreach($kelompok as $kel){
                                            if($kel->matapelajarans()->count()){
                                                $mapel = $kel->matapelajarans()->select('id','subject_name','subject_acronym')->whereNull('is_mulok')->orderBy('subject_number')->get();
                                                $mulok = $kel->matapelajarans()->select('id','subject_name','subject_acronym')->mulok()->orderBy('subject_number');
                                                if($mulok->count() > 0){
                                                    $mapel = $mapel->concat($mulok->get());
                                                }
                                                if(!$matapelajarans){
                                                    $matapelajarans = collect($mapel);
                                                }
                                                else{
                                                    $matapelajarans = $matapelajarans->concat(collect($mapel));
                                                }
                                            }
                                        }

                                        $num = 12;
                                        $skippedNum = ['24','25','26','28','29','30','31','36','37','38','39','41','47'];
                                        foreach($kelasList->sortBy('levelName')->all() as $k){
                                            $dataArr = null;
                                            foreach($matapelajarans as $m){
                                                $percentage = $classes->where('id',$k->id)->where('subject_id',$m->id)->first();
                                                $percentage = $percentage ? $percentage['percentage'] : 0;
                                                if(!$dataArr)
                                                    $dataArr = array();
                                                $dataArr[] = $percentage;
                                            }
                                            if(!$datasets){
                                                $datasets = collect([
                                                    [   
                                                        'label' => $k->levelName,
                                                        'backgroundColor' => $this->getColor($num),
                                                        'data' => $dataArr
                                                    ]
                                                ]);
                                            }
                                            else{
                                                $dataset = collect([
                                                    [   
                                                        'label' => $k->levelName,
                                                        'backgroundColor' => $this->getColor($num),
                                                        'data' => $dataArr
                                                    ]
                                                ]);
                                                $datasets = $datasets->concat($dataset);
                                            }
                                            $num++;
                                            while(in_array($num,$skippedNum)){
                                                $num++;
                                            }
                                        }

                                        if($role == 'direktur')
                                            $folder = $role;
                                        elseif(in_array($role,['pembinayys','ketuayys']))
                                            $folder = 'read-only';
                                        else $folder = 'pa';

                                        return view('kepegawaian.'.$folder.'.iku.'.$category->nameLc.'_detail', compact('ikuAspects','fillableIkuAspects','category','tahun','tahunPelajaran','unitAktif','aspectUnits','nilai','chartList','chart','semesterList','semester','score','kelasList','kelompok','mataPelajaran','matapelajarans','classes','datasets'));
                                    }
                                    elseif($chart == 'iklas'){
                                        $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unitAktif->id)->get();

                                        $refIklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->get();

                                        // Counting scores
                                        $classes = null;
                                        foreach($kelasList as $k){
                                            foreach($refIklas as $i){
                                                $totalKelas = $totalSiswa = 0;
                                                $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                                foreach($riyawatKelas as $r){
                                                    $siswa = $r->siswa()->select('id')->first();
                                                    $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                                                    $raporIklas = $rapor ? $rapor->iklas : null;

                                                    $score_iklas = $raporIklas ? $raporIklas->detail()->where('iklas_ref_id',$i->id)->first() : null;

                                                    if($score_iklas){
                                                        if($score_iklas->predicate >= $score) $totalKelas++;
                                                    }
                                                    $totalSiswa++;
                                                }
                                                $kelas = collect([
                                                    [
                                                        'id' => $k->id,
                                                        'iklas_ref_id' => $i->id,
                                                        'total' => $totalKelas,
                                                        'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0
                                                    ]
                                                ]);
                                                if($classes){
                                                    $classes = $classes->concat($kelas);
                                                }
                                                else{
                                                    $classes = $kelas;
                                                }
                                            }
                                        }

                                        $num = 12;
                                        $skippedNum = ['24','25','26','28','29','30','31','36','37','38','39','41','47'];
                                        foreach($kelasList->sortBy('levelName')->all() as $k){
                                            $dataArr = null;
                                            foreach($refIklas as $i){
                                                $percentage = $classes->where('id',$k->id)->where('iklas_ref_id',$i->id)->first();
                                                $percentage = $percentage ? $percentage['percentage'] : 0;
                                                if(!$dataArr)
                                                    $dataArr = array();
                                                $dataArr[] = $percentage;
                                            }
                                            if(!$datasets){
                                                $datasets = collect([
                                                    [   
                                                        'label' => $k->levelName,
                                                        'backgroundColor' => $this->getColor($num),
                                                        'data' => $dataArr
                                                    ]
                                                ]);
                                            }
                                            else{
                                                $dataset = collect([
                                                    [   
                                                        'label' => $k->levelName,
                                                        'backgroundColor' => $this->getColor($num),
                                                        'data' => $dataArr
                                                    ]
                                                ]);
                                                $datasets = $datasets->concat($dataset);
                                            }
                                            $num++;
                                            while(in_array($num,$skippedNum)){
                                                $num++;
                                            }
                                        }

                                        if($role == 'direktur')
                                            $folder = $role;
                                        elseif(in_array($role,['pembinayys','ketuayys']))
                                            $folder = 'read-only';
                                        else $folder = 'pa';

                                        return view('kepegawaian.'.$folder.'.iku.'.$category->nameLc.'_detail', compact('ikuAspects','fillableIkuAspects','category','tahun','tahunPelajaran','unitAktif','aspectUnits','nilai','chartList','chart','semesterList','semester','score','kelasList','refIklas','classes','datasets'));
                                    }
                                }
                            }
                        }

                        if($role == 'direktur')
                            $folder = $role;
                        elseif(in_array($role,['pembinayys','ketuayys']))
                            $folder = 'read-only';
                        else $folder = 'pa';

                        return view('kepegawaian.'.$folder.'.iku.'.$category->nameLc.'_detail', compact('ikuAspects','fillableIkuAspects','category','tahun','tahunPelajaran','unitAktif','aspectUnits','nilai','chartList', 'chart','semesterList','semester','score','datasets'));
                    }
                    else return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
                }
                else{
                    if($role == 'kepsek'){
                        return redirect()->route('kepegawaian.index');
                    }
                    else return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
                }
            }
            else{
                if($role == 'kepsek'){
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

            $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

            $tahun = str_replace("-","/",$request->tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.edukasi.index');

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

            $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

            $tahun = str_replace("-","/",$request->tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.edukasi.index');

            if($role == 'kepsek'){
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

            $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

            $tahun = str_replace("-","/",$request->tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.edukasi.index');

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
            };

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

        $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

        $tahun = str_replace("-","/",$request->tahun);
        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route('iku.edukasi.index');

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
    
    /**
     * Get RGB colors.
     */
    
    function getColor($num) {
        $hash = md5('color' . $num); // modify 'color' to get a different palette
        return 'rgb('.
            hexdec(substr($hash, 0, 2)).','. // r
            hexdec(substr($hash, 2, 2)).','. // g
            hexdec(substr($hash, 4, 2)).')'; //b
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
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\RefIklas;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Rekrutmen\PegawaiJabatan;
use App\Models\LoginUser;
use App\Models\Unit;

use Illuminate\Http\Request;

class IkuEdukasiController extends Controller
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

            $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

            if($request->tahun){
                $tahun = str_replace("-","/",$request->tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            }
            else{
                $tahun = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$tahun) return redirect()->route('iku.'.$category->nameLc.'.index');
            $tahunPelajaran = TahunAjaran::orderBy('created_at')->get();

            $unitList = null;
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
                    $unitAktif = Unit::sekolah()->where('name','LIKE',$unit)->first();
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

                    if($role == 'kepsek' || ($role != 'kepsek' && count($aspectUnits) > 0)){
                        $nilai = $tahun->nilaiIku()->where([
                            'iku_category_id' => $category->id,
                            'unit_id' => $unitAktif->id
                        ])->first();

                        // Grafik
                        $chart = isset($request->chart) ? $request->chart : 'mapel';

                        $semester = isset($request->semester) ? $request->semester : '1';

                        $chartList = collect([
                            [
                                'name' => 'Mapel',
                                'link' => 'mapel'
                            ],
                            [
                                'name' => 'IKLaS',
                                'link' => 'iklas'
                            ],
                            [
                                'name' => 'USP',
                                'link' => 'usp'
                            ],
                        ]);

                        $semesterList = $tahun->semester;

                        $kelasList = $mataPelajaran = $datasets = null;

                        if($chart == 'iklas'){
                            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 5) ? $request->score : null;
                        }
                        else{
                            $score = isset($request->score) && ($request->score >= 1 && $request->score <= 100) ? $request->score : null;
                        }

                        if($chart && $chartList->where('link',$chart)->count() > 0){
                            if($chart == 'usp'){
                                $semester = Semester::where(['semester_id' => $tahun->academic_year.'-2', 'semester' => 'Genap'])->first();
                            }
                            else{
                                $semester = Semester::where('semester_id',$tahun->academic_year.'-'.$semester)->first();
                            }
                            if($semester){
                                if(isset($score)){
                                    if(in_array($chart,['mapel','usp'])){
                                        $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unitAktif->id);

                                        if($chart == 'usp'){
                                            $kelasList = $kelasList->whereHas('level',function($q){
                                                $q->whereIn('level',['6','9','12']);
                                            });
                                        }

                                        $kelasList = $kelasList->get();

                                        $kelompok_umum = $unitAktif->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                                        if($unitAktif->name == "SMA"){
                                            $kelompok_peminatan = $unitAktif->kelompokMataPelajaran()->where('group_subject_name', 'like', 'kelompok%')->has('jurusan')->get();
                                            $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                                        }
                                        else $kelompok = $kelompok_umum;

                                        $mapelFiltered = MataPelajaran::select(['id','subject_name','subject_acronym','group_subject_id'])->whereIn('group_subject_id', $kelompok->pluck('id'));

                                        if($semester->is_active == 0){
                                            $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                                $q->where('semester_id',$semester->id);
                                            });
                                        }

                                        $mapel = clone $mapelFiltered;
                                        $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                                        if($mapel->count() > 0){
                                            $mataPelajaran = $mapel->get();
                                        }

                                        $mapelMulok = clone $mapelFiltered;
                                        $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                                        if($mapelMulok->count() > 0){
                                            $mataPelajaran = $mataPelajaran->concat($mapelMulok->get());
                                        }

                                        // Counting scores
                                        $classes = null;
                                        foreach($kelasList as $k){
                                            foreach($mataPelajaran as $m){
                                                $totalKelas = $totalSiswa = 0;
                                                $checked = true;

                                                if($unitAktif->name == 'SD'){
                                                    if($m->mapelKelas()->where('level_id',$k->level_id)->count() > 0){
                                                        $checked = true;
                                                    }
                                                    else $checked = false;
                                                }

                                                if($unitAktif->name == 'SMA'){
                                                    if($m->kmps && ($m->kmps->major_id && ($m->kmps->major_id != $k->major_id))){
                                                        $checked = false;
                                                    }
                                                }

                                                // Students history in this class
                                                $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                                foreach($riyawatKelas as $r){
                                                    $siswa = $r->siswa()->select('id')->first();
                                                    if($chart == 'mapel'){
                                                        $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                                                        $pengetahuan = $rapor ? $rapor->pengetahuan()->select('score_knowledge')->where('subject_id',$m->id)->whereNotNull('score_knowledge')->first() : null;
                                                        $score_knowledge = $pengetahuan ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '-';
                                                        if($score_knowledge != '-'){
                                                            if($score_knowledge >= $score) $totalKelas++;
                                                        }
                                                        $totalSiswa++;
                                                    }
                                                    elseif($chart == 'usp'){
                                                        $usp = $siswa->usp()->select('score')->where('semester_id', $semester->id)->where('subject_id',$m->id)->first();

                                                        $score_usp = $usp ? number_format((float)$usp->score, 0, ',', '') : '-';

                                                        if($score_usp != '-'){
                                                            if($score_usp >= $score) $totalKelas++;
                                                        }
                                                        $totalSiswa++;
                                                    }
                                                }
                                                $kelas = collect([
                                                    [
                                                        'id' => $k->id,
                                                        'subject_id' => $m->id,
                                                        'total' => $totalKelas,
                                                        'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0,
                                                        'checked' => $checked
                                                    ]
                                                ]);
                                                if($classes){
                                                    $classes = $classes->concat($kelas);
                                                }
                                                else{
                                                    $classes = $kelas;
                                                }
                                            }
                                        }

                                        $matapelajarans = null;
                                        foreach($kelompok as $kel){
                                            if($kel->matapelajarans()->count()){
                                                $mapel = $kel->matapelajarans()->select('id','subject_name','subject_acronym')->whereNull('is_mulok')->orderBy('subject_number')->get();
                                                $mulok = $kel->matapelajarans()->select('id','subject_name','subject_acronym')->mulok()->orderBy('subject_number');
                                                if($mulok->count() > 0){
                                                    $mapel = $mapel->concat($mulok->get());
                                                }
                                                if(!$matapelajarans){
                                                    $matapelajarans = collect($mapel);
                                                }
                                                else{
                                                    $matapelajarans = $matapelajarans->concat(collect($mapel));
                                                }
                                            }
                                        }

                                        $num = 12;
                                        $skippedNum = ['24','25','26','28','29','30','31','36','37','38','39','41','47'];
                                        foreach($kelasList->sortBy('levelName')->all() as $k){
                                            $dataArr = null;
                                            foreach($matapelajarans as $m){
                                                $percentage = $classes->where('id',$k->id)->where('subject_id',$m->id)->first();
                                                $percentage = $percentage ? $percentage['percentage'] : 0;
                                                if(!$dataArr)
                                                    $dataArr = array();
                                                $dataArr[] = $percentage;
                                            }
                                            if(!$datasets){
                                                $datasets = collect([
                                                    [   
                                                        'label' => $k->levelName,
                                                        'backgroundColor' => $this->getColor($num),
                                                        'data' => $dataArr
                                                    ]
                                                ]);
                                            }
                                            else{
                                                $dataset = collect([
                                                    [   
                                                        'label' => $k->levelName,
                                                        'backgroundColor' => $this->getColor($num),
                                                        'data' => $dataArr
                                                    ]
                                                ]);
                                                $datasets = $datasets->concat($dataset);
                                            }
                                            $num++;
                                            while(in_array($num,$skippedNum)){
                                                $num++;
                                            }
                                        }

                                        if($role == 'direktur')
                                            $folder = $role;
                                        elseif(in_array($role,['pembinayys','ketuayys']))
                                            $folder = 'read-only';
                                        else $folder = 'pa';

                                        return view('kepegawaian.'.$folder.'.iku.'.$category->nameLc.'_detail', compact('ikuAspects','fillableIkuAspects','category','tahun','tahunPelajaran','unitAktif','aspectUnits','nilai','chartList','chart','semesterList','semester','score','kelasList','kelompok','mataPelajaran','matapelajarans','classes','datasets'));
                                    }
                                    elseif($chart == 'iklas'){
                                        $kelasList = $semester->tahunAjaran->kelas()->where('unit_id',$unitAktif->id)->get();

                                        $refIklas = RefIklas::select(['id','iklas_cat','iklas_no','competence','category'])->get();

                                        // Counting scores
                                        $classes = null;
                                        foreach($kelasList as $k){
                                            foreach($refIklas as $i){
                                                $totalKelas = $totalSiswa = 0;
                                                $riyawatKelas = $k->riwayat()->select('student_id')->where('semester_id',$semester->id)->get();
                                                foreach($riyawatKelas as $r){
                                                    $siswa = $r->siswa()->select('id')->first();
                                                    $rapor = $siswa->nilaiRapor()->where('semester_id', $semester->id)->first();

                                                    $raporIklas = $rapor ? $rapor->iklas : null;

                                                    $score_iklas = $raporIklas ? $raporIklas->detail()->where('iklas_ref_id',$i->id)->first() : null;

                                                    if($score_iklas){
                                                        if($score_iklas->predicate >= $score) $totalKelas++;
                                                    }
                                                    $totalSiswa++;
                                                }
                                                $kelas = collect([
                                                    [
                                                        'id' => $k->id,
                                                        'iklas_ref_id' => $i->id,
                                                        'total' => $totalKelas,
                                                        'percentage' => $totalKelas > 0 && $totalSiswa > 0 ? number_format((float)(($totalKelas/$totalSiswa)*100), 0, ',', '') : 0
                                                    ]
                                                ]);
                                                if($classes){
                                                    $classes = $classes->concat($kelas);
                                                }
                                                else{
                                                    $classes = $kelas;
                                                }
                                            }
                                        }

                                        $num = 12;
                                        $skippedNum = ['24','25','26','28','29','30','31','36','37','38','39','41','47'];
                                        foreach($kelasList->sortBy('levelName')->all() as $k){
                                            $dataArr = null;
                                            foreach($refIklas as $i){
                                                $percentage = $classes->where('id',$k->id)->where('iklas_ref_id',$i->id)->first();
                                                $percentage = $percentage ? $percentage['percentage'] : 0;
                                                if(!$dataArr)
                                                    $dataArr = array();
                                                $dataArr[] = $percentage;
                                            }
                                            if(!$datasets){
                                                $datasets = collect([
                                                    [   
                                                        'label' => $k->levelName,
                                                        'backgroundColor' => $this->getColor($num),
                                                        'data' => $dataArr
                                                    ]
                                                ]);
                                            }
                                            else{
                                                $dataset = collect([
                                                    [   
                                                        'label' => $k->levelName,
                                                        'backgroundColor' => $this->getColor($num),
                                                        'data' => $dataArr
                                                    ]
                                                ]);
                                                $datasets = $datasets->concat($dataset);
                                            }
                                            $num++;
                                            while(in_array($num,$skippedNum)){
                                                $num++;
                                            }
                                        }

                                        if($role == 'direktur')
                                            $folder = $role;
                                        elseif(in_array($role,['pembinayys','ketuayys']))
                                            $folder = 'read-only';
                                        else $folder = 'pa';

                                        return view('kepegawaian.'.$folder.'.iku.'.$category->nameLc.'_detail', compact('ikuAspects','fillableIkuAspects','category','tahun','tahunPelajaran','unitAktif','aspectUnits','nilai','chartList','chart','semesterList','semester','score','kelasList','refIklas','classes','datasets'));
                                    }
                                }
                            }
                        }

                        if($role == 'direktur')
                            $folder = $role;
                        elseif(in_array($role,['pembinayys','ketuayys']))
                            $folder = 'read-only';
                        else $folder = 'pa';

                        return view('kepegawaian.'.$folder.'.iku.'.$category->nameLc.'_detail', compact('ikuAspects','fillableIkuAspects','category','tahun','tahunPelajaran','unitAktif','aspectUnits','nilai','chartList', 'chart','semesterList','semester','score','datasets'));
                    }
                    else return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
                }
                else{
                    if($role == 'kepsek'){
                        return redirect()->route('kepegawaian.index');
                    }
                    else return redirect()->route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]);
                }
            }
            else{
                if($role == 'kepsek'){
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

            $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

            $tahun = str_replace("-","/",$request->tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.edukasi.index');

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

            $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

            $tahun = str_replace("-","/",$request->tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.edukasi.index');

            if($role == 'kepsek'){
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

            $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

            $tahun = str_replace("-","/",$request->tahun);
            $tahun = TahunAjaran::where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route('iku.edukasi.index');

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
            };

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

        $category = IkuCategory::select('id','name')->where('name','Edukasi')->first();

        $tahun = str_replace("-","/",$request->tahun);
        $tahun = TahunAjaran::where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route('iku.edukasi.index');

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
    
    /**
     * Get RGB colors.
     */
    
    function getColor($num) {
        $hash = md5('color' . $num); // modify 'color' to get a different palette
        return 'rgb('.
            hexdec(substr($hash, 0, 2)).','. // r
            hexdec(substr($hash, 2, 2)).','. // g
            hexdec(substr($hash, 4, 2)).')'; //b
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
