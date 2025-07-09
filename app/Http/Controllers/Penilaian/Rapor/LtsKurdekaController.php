<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Rapor;

use App\Http\Controllers\Controller;
use App\Helpers\StringHelper;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\PtsTK;
use App\Models\Penilaian\RaporPts;
use App\Models\Penilaian\SertifIklas;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\IndikatorKurikulumIklas;
use App\Models\Penilaian\Tk\FormatifKualitatif;
use App\Models\Penilaian\Tk\Objective;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class LtsKurdekaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'cetak';
        $this->modul = $modul;
        $this->active = 'Cetak LTS';
        $this->route = $this->subsystem.'.penilaian.lts.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $siswa = null)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }
        
        $semesterList = Semester::whereHas('curricula',function($q){
            $q->where('curriculum_id',2);
        })->where(function($q)use($role,$managementRoles){
            $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                $q->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                    $q->where('curriculum_id',2);
                });
            });
        })->get();

        $kelasList = $unit = $riwayatKelas = $mataPelajaranList = $objectives = $kelompok = $rapor = $acceptable = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::whereHas('semester',function($q)use($role,$managementRoles){
                $q->whereHas('curricula',function($q){
                    $q->where('curriculum_id',2);
                })->where(function($q)use($role,$managementRoles){
                    $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                        $q->whereHas('riwayatKelas.kelas.level.curricula',function($q){
                            $q->where('curriculum_id',2);
                        });
                    });
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::whereHas('curricula',function($q){
                $q->where('curriculum_id',2);
            })->where(function($q)use($role,$managementRoles){
                $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                    $q->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                        $q->where('curriculum_id',2);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas();
                if(!in_array($role,$managementRoles)){
                    $kelasList = $kelasList->where('unit_id',auth()->user()->pegawai->unit_id);
                    if($isWali){
                        $kelasList = $kelasList->where([
                            'academic_year_id' => $tahun->id,
                            'teacher_id' => auth()->user()->pegawai->id
                        ]);
                    }
                }
                $kelasList = $kelasList->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                    ])->where('curriculum_id',2);
                })->with('level:id,level','namakelases:id,class_name')->get()->sortBy('levelName',SORT_NATURAL);
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
                    if(!in_array($role,$managementRoles)){
                        $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                        if($isWali){
                            $kelas = $kelas->where([
                                'academic_year_id' => $tahun->id,
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        }
                    }
                    $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                        ])->where('curriculum_id',2);
                    })->first();

                    if($kelas){
                        // Inti function
                        $unit = $kelas->unit()->select('id','name')->first();

                        if($unit->id == 1){
                            // Tujuan Pembelajaran                        
                            $objectives = $kelas->level->objectiveElements()->where([
                                'semester_id' => $semester->id
                            ])->orderBy('sort_order')->get();
                        }
                        else{
                            $kelompok_umum = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                            if($kelas->major_id){
                                $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                                $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                            }
                            else $kelompok = $kelompok_umum;

                            $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                                $q->when($role == 'guru',function($q){
                                    return $q->where('employee_id',auth()->user()->pegawai->id);
                                })->whereHas('skbm',function($q)use($tahun){
                                    $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                                });
                            })->whereHas('jadwalPelajaran',function($q)use($role,$semester,$kelas){
                                $q->when($role == 'guru',function($q){
                                    return $q->where('teacher_id', auth()->user()->pegawai->id);
                                })->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            });

                            if($unit->name == 'SD'){
                                $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($kelas){
                                    $q->where('level_id',$kelas->level_id);
                                });
                            }

                            if($semester->is_active == 0){
                                $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                    $q->where('semester_id',$semester->id);
                                });
                            }

                            $mapel = clone $mapelFiltered;
                            $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                            if($mapel->count() > 0){
                                $mataPelajaranList = $mapel->get();
                            }

                            $mapelMulok = clone $mapelFiltered;
                            $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                            if($mapelMulok->count() > 0){
                                $mataPelajaranList = $mataPelajaranList ? $mataPelajaranList->concat($mapelMulok->get()) : $mapelMulok->get();
                            }
                        }

                        $riwayatKelas = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                        $raporQuery = $kelas->rapor()->where([
                            'semester_id' => $semester->id
                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        });

                        if($riwayatKelas && count($riwayatKelas) > 0){
                            foreach($riwayatKelas as $r){
                                $rapor[$r->id] = clone $raporQuery;
                                $rapor[$r->id] = $rapor[$r->id]->where('student_id',$r->id)->first();

                                if($role == 'kepsek'){
                                    $acceptable[$r->id] = $rapor[$r->id] && $rapor[$r->id]->report_status_pts_id == 0 ? true : false;
                                }
                            }
                        }

                        if($siswa){
                            $siswa = str_replace("-","/",$siswa);
                            $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            })->where('student_nis', $siswa)->has('identitas')->with('identitas:id,student_name')->first();

                            if($siswa){
                                if($rapor[$siswa->id]){
                                    // Do something here

                                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                                }
                            }
                            else{
                                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                            }
                        }
                    }
                    else{
                        return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
            }
        }
        else{
            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','unit','riwayatKelas','siswa','rapor','acceptable'));
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
    public function show($id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q)use($role,$managementRoles){
            $q->whereHas('curricula',function($q){
                $q->where('curriculum_id',2);
            })->where(function($q)use($role,$managementRoles){
                $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                    $q->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                        $q->where('curriculum_id',2);
                    });
                });
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::whereHas('curricula',function($q){
            $q->where('curriculum_id',2);
        })->where(function($q)use($role,$managementRoles){
            $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                $q->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                    $q->where('curriculum_id',2);
                });
            });
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
            if(!in_array($role,$managementRoles)){
                $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                if($isWali){
                    $kelas = $kelas->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                }
            }
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->where('curriculum_id',2);
            })->first();

            if($kelas){
                $siswa = str_replace("-","/",$siswa);
                $siswa = Siswa::select('id','student_nis','student_nisn','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                    $q->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                })->where('student_nis', $siswa)->has('identitas')->first();

                if($siswa){
                    // Inti Function
                    $unit = $kelas->unit()->select('id','name','long_desc','desc')->first();
                    
                    $rapor = $kelas->rapor()->where([
                        'semester_id' => $semester->id
                    ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->where('student_id',$siswa->id)->first();

                    if($rapor){
                        $mataPelajaranList = $objectives = $kelompok = $tpDescs = $descs = $nilai = null;
                        $maxCount = 0;
                        // Nilai Akhir
                        if($unit->id == 1){
                            // Elemen Capaian Pembelajaran
                            $objectives = $kelas->level->objectiveElements()->where([
                                'semester_id' => $semester->id
                            ])->orderBy('sort_order')->get();
                            $elements = AspekPerkembangan::select('id','dev_aspect')->where('curriculum_id',2)->whereHas('objectives.objective.predicates',function($q)use($rapor,$siswa){
                                $q->where('report_score_id',$rapor->id)->where('score','>',0);
                            })->get();
                            if($elements && count($elements) > 0){
                                foreach($elements as $element){
                                    $scoreQuery = $rapor->formatifKualitatif()->where('score','>',0)->whereHas('objective.elements',function($q)use($element){
                                        $q->where('element_id', $element->id);
                                    });
                                    $maxScore[$element->id] = clone $scoreQuery;
                                    $maxScore[$element->id] = $maxScore[$element->id]->max('score');
                                    $minScore[$element->id] = clone $scoreQuery;
                                    $minScore[$element->id] = $minScore[$element->id]->min('score');

                                    if($maxScore[$element->id]){
                                        $descsQuery = PredikatDeskripsi::select('id','predicate','description')->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id,
                                            'subject_id' => $element->id,
                                            'rpd_type_id' => 15
                                        ])->whereNotNull('description');

                                        foreach(['max','min'] as $m){
                                            $descs[$element->id][$m] = clone $descsQuery;
                                            $descs[$element->id][$m] = $descs[$element->id][$m]->where('predicate',$m)->first();
                                            $descs[$element->id][$m] = $descs[$element->id][$m] ? $descs[$element->id][$m]->description : null;
                                        }

                                        $maxScores[$element->id] = Objective::select('id','desc')->whereHas('elements',function($q)use($element){
                                            $q->where('element_id',$element->id);
                                        })->whereHas('predicates',function($q)use($rapor,$siswa,$maxScore,$element){
                                            $q->where([
                                                'report_score_id' => $rapor->id,
                                                'score' => $maxScore[$element->id]
                                            ]);
                                        })->with('elements:id,sort_order')->get()->sortBy('sort_order');
                                        if($minScore[$element->id] && ($maxScore[$element->id] != $minScore[$element->id])){
                                            $minScores[$element->id] = Objective::select('id','desc')->whereHas('elements',function($q)use($element){
                                                $q->where('element_id',$element->id);
                                            })->whereHas('predicates',function($q)use($rapor,$siswa,$minScore,$element){
                                                $q->where([
                                                    'report_score_id' => $rapor->id,
                                                    'score' => $minScore[$element->id]
                                                ]);
                                            })->with('elements:id,sort_order')->get()->sortBy('sort_order');

                                            if(isset($descs[$element->id]['min'])){
                                                $nilai['perkembangan'][$element->id]['rendah'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs[$element->id]['min']);
                                                $nilai['perkembangan'][$element->id]['rendah'] = str_replace("@elemen",$element->dev_aspect,$nilai['perkembangan'][$element->id]['rendah']);
                                                $nilai['perkembangan'][$element->id]['rendah'] = str_replace("@capaian",'<b>'.StringHelper::natural_language_join($minScores[$element->id]->pluck('desc')->toArray(),'dan').'</b>',$nilai['perkembangan'][$element->id]['rendah']);
                                            }
                                        }
                                        if(isset($descs[$element->id]['max'])){
                                            $nilai['perkembangan'][$element->id]['tinggi'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs[$element->id]['max']);
                                            $nilai['perkembangan'][$element->id]['tinggi'] = str_replace("@elemen",$element->dev_aspect,$nilai['perkembangan'][$element->id]['tinggi']);
                                            $nilai['perkembangan'][$element->id]['tinggi'] = str_replace("@capaian",'<b>'.StringHelper::natural_language_join($maxScores[$element->id]->pluck('desc')->toArray(),'dan').'</b>',$nilai['perkembangan'][$element->id]['tinggi']);
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            $kelompok_umum = $unit->kelompokMataPelajaran()->select('id','group_subject_name','major_id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                            if($kelas->major_id){
                                $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                                $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                            }
                            else $kelompok = $kelompok_umum;

                            $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'));

                            if($unit->name == 'SD'){
                                $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($kelas){
                                    $q->where('level_id',$kelas->level_id);
                                });
                            }

                            if($semester->is_active == 0){
                                $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                    $q->where('semester_id',$semester->id);
                                });
                            }

                            $mapel = clone $mapelFiltered;
                            $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                            if($mapel->count() > 0){
                                $mataPelajaranList = $mapel->get();
                            }

                            $mapelMulok = clone $mapelFiltered;
                            $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                            if($mapelMulok->count() > 0){
                                $mataPelajaranList = $mataPelajaranList ? $mataPelajaranList->concat($mapelMulok->get()) : $mapelMulok->get();
                            }

                            if($mataPelajaranList && count($mataPelajaranList) > 0){
                                foreach($mataPelajaranList as $mataPelajaran){
                                    $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                        'subject_id' => $mataPelajaran->id
                                    ])->first();
                                    $nilai['mapel'][$mataPelajaran->id]['tengah'] = 0;
                                    if($nilaiAkhir){
                                        $maxCount = !$maxCount || ($maxCount < $nilaiAkhir->nilaiSumatif()->count()) ? $nilaiAkhir->nilaiSumatif()->count() : $maxCount;
                                        $nilai['mapel'][$mataPelajaran->id]['tengah'] = $nilaiAkhir->ntssWithSeparator;

                                        $descsQuery = $mataPelajaran->predicate()->select('predicate','description')->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id,
                                            'rpd_type_id' => 10
                                        ])->whereNotNull('description');

                                        foreach(['max','min'] as $m){
                                            $descs[$m] = clone $descsQuery;
                                            $descs[$m] = $descs[$m]->where('predicate',$m)->first();
                                            $descs[$m] = $descs[$m] ? $descs[$m]->description : null;
                                        }

                                        $tpDescs[$mataPelajaran->id] = $mataPelajaran->tpsDescs()->select('id','code','desc')->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id
                                        ])->get();

                                        if($tpDescs[$mataPelajaran->id] && count($tpDescs[$mataPelajaran->id]) > 0){
                                            foreach($tpDescs[$mataPelajaran->id] as $t){
                                                $nilaiTp = $nilaiAkhir->nilaiSumatif()->where('tps_desc_id',$t->id)->first();
                                                $nilai['mapel'][$mataPelajaran->id]['sumatif'][$t->id] = $nilaiTp ? $nilaiTp->scoreWithSeparator : 0;
                                            }

                                            $maxScore = $nilaiAkhir->nilaiSumatif()->max('score');
                                            $minScore = $nilaiAkhir->nilaiSumatif()->min('score');

                                            if($maxScore){
                                                $maxScores = $mataPelajaran->tpsDescs()->select('code','desc')->where([
                                                    'semester_id' => $semester->id,
                                                    'level_id' => $kelas->level_id
                                                ])->whereHas('nilai',function($q)use($nilaiAkhir,$maxScore){
                                                    $q->where([
                                                        'rkd_score_id' => $nilaiAkhir->id,
                                                        'score' => $maxScore
                                                    ]);
                                                })->get();
                                                if($minScore && ($maxScore != $minScore)){
                                                    $minScores = $mataPelajaran->tpsDescs()->select('code','desc')->where([
                                                        'semester_id' => $semester->id,
                                                        'level_id' => $kelas->level_id
                                                    ])->whereHas('nilai',function($q)use($nilaiAkhir,$minScore){
                                                        $q->where([
                                                            'rkd_score_id' => $nilaiAkhir->id,
                                                            'score' => $minScore
                                                        ]);
                                                    })->get();

                                                    if($descs['min']){
                                                        $nilai['mapel'][$mataPelajaran->id]['rendah'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs['min']);
                                                        $nilai['mapel'][$mataPelajaran->id]['rendah'] = str_replace("@kompetensi",StringHelper::natural_language_join($minScores->pluck('desc')->toArray(),'dan'),$nilai['mapel'][$mataPelajaran->id]['rendah']);
                                                    }
                                                }
                                                if($descs['max']){
                                                    $nilai['mapel'][$mataPelajaran->id]['tinggi'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs['max']);
                                                    $nilai['mapel'][$mataPelajaran->id]['tinggi'] = str_replace("@kompetensi",StringHelper::natural_language_join($maxScores->pluck('desc')->toArray(),'dan'),$nilai['mapel'][$mataPelajaran->id]['tinggi']);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if($kelas->major_id){
                                $kelompok_master = $kelompok_umum->take(2);
                                $kelompok_lain = $kelompok_umum->skip(2);
                                $kelompok = $kelompok_master->concat($kelompok_peminatan)->concat($kelompok_lain);
                            }
                        }

                        // Old
                        $pts_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pts()->first();
                        $pts_date = $pts_date ? $pts_date->report_date : null;

                        // Digital Signature
                        $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                        return view($this->route.'-report', compact('tahun','semester','kelas','unit','siswa','objectives','kelompok','tpDescs','descs','rapor','nilai','maxCount','pts_date','digital'));
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data LTS peserta didik yang ditemukan');
                    }
                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->studentNisLink]);
                }
                else{
                    Session::flash('danger', 'Tidak ada data peserta didik yang ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q){
            $q->where(function($q){
                $q->where('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q){
            $q->where('is_active',1);
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
            if(!in_array($role,$managementRoles)){
                $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                if($isWali){
                    $kelas = $kelas->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                }
            }
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->where('curriculum_id',2);
            })->first();

            if($kelas){
                $siswa = str_replace("-","/",$siswa);
                $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                    $q->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                })->where('student_nis', $siswa)->has('identitas')->first();

                if($siswa){
                    // Inti Function
                    $unit = $kelas->unit()->select('id','name')->first();
                    
                    $objectives = $kelas->level->objectiveElements()->where([
                        'semester_id' => $semester->id
                    ])->orderBy('sort_order')->get();

                    if($objectives && count($objectives) > 0){
                        $rapor = $kelas->rapor()->where([
                            'semester_id' => $semester->id
                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->where('student_id',$siswa->id)->first();

                        if(!$rapor){
                            $jabatan = Jabatan::where('code','11')->first();
                            $kepsek = $jabatan->pegawaiUnit()->where('unit_id',$kelas->unit_id)->whereHas('pegawai',function($q){
                                $q->aktif();
                            })->first();

                            $rapor = new NilaiRapor();
                            $rapor->student_id = $siswa->id;
                            $rapor->semester_id = $semester->id;
                            $rapor->class_id = $kelas->id;
                            $rapor->report_status_id = 0;
                            $rapor->acc_id = 0;
                            $rapor->unit_id = $kelas->unit_id;
                            $rapor->hr_name = $kelas->walikelas ? $kelas->walikelas->name : '-';
                            $rapor->hm_name = $kepsek ? $kepsek->pegawai->name : '-';
                            $rapor->save();
                            $rapor->fresh();
                        }
                        if($rapor){
                            $savedCount = 0;
                            $predicateList = ['A' => 4,'B' => 3,'C' => 2,'D' => 1];
                            foreach($objectives as $o){
                                $predicate = isset($request->predicate[$o->sort_order]) ? $request->predicate[$o->sort_order] : null;
                                $nilaiFormatif = $rapor->formatifKualitatif()->where('objective_id',$o->objective_id)->first();                            
                                if(!$nilaiFormatif && $predicate && in_array($predicate,['A','B','C','D'])){
                                    $nilaiFormatif = new FormatifKualitatif();
                                    $nilaiFormatif->report_score_id = $rapor->id;
                                    $nilaiFormatif->objective_id = $o->objective_id;
                                    $nilaiFormatif->predicate = null;
                                    $nilaiFormatif->score = 0;
                                    $nilaiFormatif->save();
                                    $nilaiFormatif->fresh();
                                }
                                if($nilaiFormatif){
                                    if($predicate && in_array($predicate,['A','B','C','D'])){
                                        $nilaiFormatif->predicate = $predicate;
                                        $score = $predicateList[$predicate];
                                        if(isset($score) && $score > 0){
                                            $nilaiFormatif->score = $score;
                                        }
                                        $nilaiFormatif->save();
                                        $savedCount++;
                                    }
                                }
                            }
                            if($savedCount == count($objectives)){
                                Session::flash('success', 'Semua perubahan perkembangan berhasil disimpan');
                            }
                            elseif($savedCount > 0 && ($savedCount < count($objectives))){
                                Session::flash('success', 'Beberapa perubahan perkembangan berhasil disimpan');
                            }
                            else{
                                Session::flash('danger', $role == 'kepsek' ? 'Belum ada tujuan pembelajaran yang dapat dinilai' : 'Tidak dapat menyimpan perubahan perkembangan');
                            }
                        }
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data tujuan pembelajaran yang ditemukan');
                    }
                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->studentNisLink]);
                }
                else{
                    Session::flash('danger', 'Tidak ada data peserta didik yang ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
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
     * Validate the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = auth()->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q){
            $q->whereHas('curricula',function($q){
                $q->where('curriculum_id',2);
            })->where(function($q){
                $q->where('is_active',1)->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                    $q->where('curriculum_id',2);
                });
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::whereHas('curricula',function($q){
            $q->where('curriculum_id',2);
        })->where(function($q){
            $q->where('is_active',1)->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                $q->where('curriculum_id',2);
            });
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas)->where('unit_id',auth()->user()->pegawai->unit_id)->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->where('curriculum_id',2);
            })->first();

            if($kelas){
                $siswa = str_replace("-","/",$siswa);
                $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                    $q->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                })->where('student_nis', $siswa)->has('identitas')->first();

                if($siswa){
                    // Inti Function
                    $unit = $kelas->unit()->select('id','name')->first();

                    $rapor = $kelas->rapor()->where([
                        'semester_id' => $semester->id,
                        'student_id' => $siswa->id
                    ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->first();

                    if($rapor){
                        if($rapor->report_status_pts_id == 0){
                            if($kelas->unit_id == 1){
                                $pts = $rapor->pts_tk;
                                if(!$pts){
                                    $pts = new PtsTK();
                                    $pts->save();
                                }
                            }
                            else{
                                $pts = $rapor->pts;
                                if(!$pts){
                                    $pts = new RaporPts();
                                    $pts->save();
                                }
                            }
                            if($rapor->kehadiran){
                                $pts->absent = $rapor->kehadiran->absent;
                                $pts->sick = $rapor->kehadiran->sick;
                                $pts->leave = $rapor->kehadiran->leave;
                            }
                            else{
                                $pts->absent = $pts->sick = $pts->leave = 0;
                            }
                            $pts_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pts()->first();
                            if($pts_date){
                                $pts->report_date = $pts_date->report_date;
                            }
                            $pts->save();

                            $rapor->report_status_pts_id = 1;
                            $rapor->acc_id = auth()->user()->pegawai->id;
                            $rapor->save();
                            $rapor->fresh();

                            Session::flash('success', 'Data LTS ananda '.$siswa->identitas->student_name.' berhasil divalidasi');
                        }
                        elseif($rapor->report_status_pts_id == 1) Session::flash('danger', 'Data LTS ananda '.$siswa->identitas->student_name.' sudah divalidasi');
                    }
                    else Session::flash('danger', 'LTS ananda '.$siswa->identitas->student_name.' tidak ditemukan');
                }
                else Session::flash('danger', 'Tidak ada data peserta didik yang ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian\Rapor;

use App\Http\Controllers\Controller;
use App\Helpers\StringHelper;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\HafalanType;
use App\Models\Penilaian\NilaiRapor;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\PtsTK;
use App\Models\Penilaian\RaporPts;
use App\Models\Penilaian\SertifIklas;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\IndikatorKurikulumIklas;
use App\Models\Penilaian\Tk\FormatifKualitatif;
use App\Models\Penilaian\Tk\Objective;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class LtsKurdekaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'cetak';
        $this->modul = $modul;
        $this->active = 'Cetak LTS';
        $this->route = $this->subsystem.'.penilaian.lts.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $siswa = null)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }
        
        $semesterList = Semester::whereHas('curricula',function($q){
            $q->where('curriculum_id',2);
        })->where(function($q)use($role,$managementRoles){
            $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                $q->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                    $q->where('curriculum_id',2);
                });
            });
        })->get();

        $kelasList = $unit = $riwayatKelas = $mataPelajaranList = $objectives = $kelompok = $rapor = $acceptable = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::whereHas('semester',function($q)use($role,$managementRoles){
                $q->whereHas('curricula',function($q){
                    $q->where('curriculum_id',2);
                })->where(function($q)use($role,$managementRoles){
                    $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                        $q->whereHas('riwayatKelas.kelas.level.curricula',function($q){
                            $q->where('curriculum_id',2);
                        });
                    });
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::whereHas('curricula',function($q){
                $q->where('curriculum_id',2);
            })->where(function($q)use($role,$managementRoles){
                $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                    $q->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                        $q->where('curriculum_id',2);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $semester->tahunAjaran->kelas();
                if(!in_array($role,$managementRoles)){
                    $kelasList = $kelasList->where('unit_id',auth()->user()->pegawai->unit_id);
                    if($isWali){
                        $kelasList = $kelasList->where([
                            'academic_year_id' => $tahun->id,
                            'teacher_id' => auth()->user()->pegawai->id
                        ]);
                    }
                }
                $kelasList = $kelasList->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                    ])->where('curriculum_id',2);
                })->with('level:id,level','namakelases:id,class_name')->get()->sortBy('levelName',SORT_NATURAL);
                if($kelas){
                    $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
                    if(!in_array($role,$managementRoles)){
                        $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                        if($isWali){
                            $kelas = $kelas->where([
                                'academic_year_id' => $tahun->id,
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        }
                    }
                    $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                        ])->where('curriculum_id',2);
                    })->first();

                    if($kelas){
                        // Inti function
                        $unit = $kelas->unit()->select('id','name')->first();

                        if($unit->id == 1){
                            // Tujuan Pembelajaran                        
                            $objectives = $kelas->level->objectiveElements()->where([
                                'semester_id' => $semester->id
                            ])->orderBy('sort_order')->get();
                        }
                        else{
                            $kelompok_umum = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                            if($kelas->major_id){
                                $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                                $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                            }
                            else $kelompok = $kelompok_umum;

                            $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                                $q->when($role == 'guru',function($q){
                                    return $q->where('employee_id',auth()->user()->pegawai->id);
                                })->whereHas('skbm',function($q)use($tahun){
                                    $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                                });
                            })->whereHas('jadwalPelajaran',function($q)use($role,$semester,$kelas){
                                $q->when($role == 'guru',function($q){
                                    return $q->where('teacher_id', auth()->user()->pegawai->id);
                                })->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            });

                            if($unit->name == 'SD'){
                                $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($kelas){
                                    $q->where('level_id',$kelas->level_id);
                                });
                            }

                            if($semester->is_active == 0){
                                $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                    $q->where('semester_id',$semester->id);
                                });
                            }

                            $mapel = clone $mapelFiltered;
                            $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                            if($mapel->count() > 0){
                                $mataPelajaranList = $mapel->get();
                            }

                            $mapelMulok = clone $mapelFiltered;
                            $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                            if($mapelMulok->count() > 0){
                                $mataPelajaranList = $mataPelajaranList ? $mataPelajaranList->concat($mapelMulok->get()) : $mapelMulok->get();
                            }
                        }

                        $riwayatKelas = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                        $raporQuery = $kelas->rapor()->where([
                            'semester_id' => $semester->id
                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        });

                        if($riwayatKelas && count($riwayatKelas) > 0){
                            foreach($riwayatKelas as $r){
                                $rapor[$r->id] = clone $raporQuery;
                                $rapor[$r->id] = $rapor[$r->id]->where('student_id',$r->id)->first();

                                if($role == 'kepsek'){
                                    $acceptable[$r->id] = $rapor[$r->id] && $rapor[$r->id]->report_status_pts_id == 0 ? true : false;
                                }
                            }
                        }

                        if($siswa){
                            $siswa = str_replace("-","/",$siswa);
                            $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            })->where('student_nis', $siswa)->has('identitas')->with('identitas:id,student_name')->first();

                            if($siswa){
                                if($rapor[$siswa->id]){
                                    // Do something here

                                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                                }
                            }
                            else{
                                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                            }
                        }
                    }
                    else{
                        return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
                    }
                }
            }
        }
        else{
            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','unit','riwayatKelas','siswa','rapor','acceptable'));
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
    public function show($id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q)use($role,$managementRoles){
            $q->whereHas('curricula',function($q){
                $q->where('curriculum_id',2);
            })->where(function($q)use($role,$managementRoles){
                $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                    $q->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                        $q->where('curriculum_id',2);
                    });
                });
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::whereHas('curricula',function($q){
            $q->where('curriculum_id',2);
        })->where(function($q)use($role,$managementRoles){
            $q->where('is_active',1)->when(in_array($role,['kepsek','wakasek']) || in_array($role,$managementRoles),function($q){
                $q->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                    $q->where('curriculum_id',2);
                });
            });
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
            if(!in_array($role,$managementRoles)){
                $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                if($isWali){
                    $kelas = $kelas->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                }
            }
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->where('curriculum_id',2);
            })->first();

            if($kelas){
                $siswa = str_replace("-","/",$siswa);
                $siswa = Siswa::select('id','student_nis','student_nisn','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                    $q->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                })->where('student_nis', $siswa)->has('identitas')->first();

                if($siswa){
                    // Inti Function
                    $unit = $kelas->unit()->select('id','name','long_desc','desc')->first();
                    
                    $rapor = $kelas->rapor()->where([
                        'semester_id' => $semester->id
                    ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->where('student_id',$siswa->id)->first();

                    if($rapor){
                        $mataPelajaranList = $objectives = $kelompok = $tpDescs = $descs = $nilai = null;
                        $maxCount = 0;
                        // Nilai Akhir
                        if($unit->id == 1){
                            // Elemen Capaian Pembelajaran
                            $objectives = $kelas->level->objectiveElements()->where([
                                'semester_id' => $semester->id
                            ])->orderBy('sort_order')->get();
                            $elements = AspekPerkembangan::select('id','dev_aspect')->where('curriculum_id',2)->whereHas('objectives.objective.predicates',function($q)use($rapor,$siswa){
                                $q->where('report_score_id',$rapor->id)->where('score','>',0);
                            })->get();
                            if($elements && count($elements) > 0){
                                foreach($elements as $element){
                                    $scoreQuery = $rapor->formatifKualitatif()->where('score','>',0)->whereHas('objective.elements',function($q)use($element){
                                        $q->where('element_id', $element->id);
                                    });
                                    $maxScore[$element->id] = clone $scoreQuery;
                                    $maxScore[$element->id] = $maxScore[$element->id]->max('score');
                                    $minScore[$element->id] = clone $scoreQuery;
                                    $minScore[$element->id] = $minScore[$element->id]->min('score');

                                    if($maxScore[$element->id]){
                                        $descsQuery = PredikatDeskripsi::select('id','predicate','description')->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id,
                                            'subject_id' => $element->id,
                                            'rpd_type_id' => 15
                                        ])->whereNotNull('description');

                                        foreach(['max','min'] as $m){
                                            $descs[$element->id][$m] = clone $descsQuery;
                                            $descs[$element->id][$m] = $descs[$element->id][$m]->where('predicate',$m)->first();
                                            $descs[$element->id][$m] = $descs[$element->id][$m] ? $descs[$element->id][$m]->description : null;
                                        }

                                        $maxScores[$element->id] = Objective::select('id','desc')->whereHas('elements',function($q)use($element){
                                            $q->where('element_id',$element->id);
                                        })->whereHas('predicates',function($q)use($rapor,$siswa,$maxScore,$element){
                                            $q->where([
                                                'report_score_id' => $rapor->id,
                                                'score' => $maxScore[$element->id]
                                            ]);
                                        })->with('elements:id,sort_order')->get()->sortBy('sort_order');
                                        if($minScore[$element->id] && ($maxScore[$element->id] != $minScore[$element->id])){
                                            $minScores[$element->id] = Objective::select('id','desc')->whereHas('elements',function($q)use($element){
                                                $q->where('element_id',$element->id);
                                            })->whereHas('predicates',function($q)use($rapor,$siswa,$minScore,$element){
                                                $q->where([
                                                    'report_score_id' => $rapor->id,
                                                    'score' => $minScore[$element->id]
                                                ]);
                                            })->with('elements:id,sort_order')->get()->sortBy('sort_order');

                                            if(isset($descs[$element->id]['min'])){
                                                $nilai['perkembangan'][$element->id]['rendah'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs[$element->id]['min']);
                                                $nilai['perkembangan'][$element->id]['rendah'] = str_replace("@elemen",$element->dev_aspect,$nilai['perkembangan'][$element->id]['rendah']);
                                                $nilai['perkembangan'][$element->id]['rendah'] = str_replace("@capaian",'<b>'.StringHelper::natural_language_join($minScores[$element->id]->pluck('desc')->toArray(),'dan').'</b>',$nilai['perkembangan'][$element->id]['rendah']);
                                            }
                                        }
                                        if(isset($descs[$element->id]['max'])){
                                            $nilai['perkembangan'][$element->id]['tinggi'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs[$element->id]['max']);
                                            $nilai['perkembangan'][$element->id]['tinggi'] = str_replace("@elemen",$element->dev_aspect,$nilai['perkembangan'][$element->id]['tinggi']);
                                            $nilai['perkembangan'][$element->id]['tinggi'] = str_replace("@capaian",'<b>'.StringHelper::natural_language_join($maxScores[$element->id]->pluck('desc')->toArray(),'dan').'</b>',$nilai['perkembangan'][$element->id]['tinggi']);
                                        }
                                    }
                                }
                            }
                        }
                        else{
                            $kelompok_umum = $unit->kelompokMataPelajaran()->select('id','group_subject_name','major_id')->where('group_subject_name', 'like', 'kelompok%')->whereDoesntHave('jurusan')->get();
                            if($kelas->major_id){
                                $kelompok_peminatan = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->where('major_id', $kelas->major_id)->get();
                                $kelompok = $kelompok_umum->concat($kelompok_peminatan);
                            }
                            else $kelompok = $kelompok_umum;

                            $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'));

                            if($unit->name == 'SD'){
                                $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($kelas){
                                    $q->where('level_id',$kelas->level_id);
                                });
                            }

                            if($semester->is_active == 0){
                                $mapelFiltered = $mapelFiltered->whereHas('kkm',function($q)use($semester){
                                    $q->where('semester_id',$semester->id);
                                });
                            }

                            $mapel = clone $mapelFiltered;
                            $mapel = $mapel->whereNull('is_mulok')->orderBy('subject_number');

                            if($mapel->count() > 0){
                                $mataPelajaranList = $mapel->get();
                            }

                            $mapelMulok = clone $mapelFiltered;
                            $mapelMulok = $mapelMulok->mulok()->orderBy('subject_number');

                            if($mapelMulok->count() > 0){
                                $mataPelajaranList = $mataPelajaranList ? $mataPelajaranList->concat($mapelMulok->get()) : $mapelMulok->get();
                            }

                            if($mataPelajaranList && count($mataPelajaranList) > 0){
                                foreach($mataPelajaranList as $mataPelajaran){
                                    $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                        'subject_id' => $mataPelajaran->id
                                    ])->first();
                                    $nilai['mapel'][$mataPelajaran->id]['tengah'] = 0;
                                    if($nilaiAkhir){
                                        $maxCount = !$maxCount || ($maxCount < $nilaiAkhir->nilaiSumatif()->count()) ? $nilaiAkhir->nilaiSumatif()->count() : $maxCount;
                                        $nilai['mapel'][$mataPelajaran->id]['tengah'] = $nilaiAkhir->ntssWithSeparator;

                                        $descsQuery = $mataPelajaran->predicate()->select('predicate','description')->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id,
                                            'rpd_type_id' => 10
                                        ])->whereNotNull('description');

                                        foreach(['max','min'] as $m){
                                            $descs[$m] = clone $descsQuery;
                                            $descs[$m] = $descs[$m]->where('predicate',$m)->first();
                                            $descs[$m] = $descs[$m] ? $descs[$m]->description : null;
                                        }

                                        $tpDescs[$mataPelajaran->id] = $mataPelajaran->tpsDescs()->select('id','code','desc')->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id
                                        ])->get();

                                        if($tpDescs[$mataPelajaran->id] && count($tpDescs[$mataPelajaran->id]) > 0){
                                            foreach($tpDescs[$mataPelajaran->id] as $t){
                                                $nilaiTp = $nilaiAkhir->nilaiSumatif()->where('tps_desc_id',$t->id)->first();
                                                $nilai['mapel'][$mataPelajaran->id]['sumatif'][$t->id] = $nilaiTp ? $nilaiTp->scoreWithSeparator : 0;
                                            }

                                            $maxScore = $nilaiAkhir->nilaiSumatif()->max('score');
                                            $minScore = $nilaiAkhir->nilaiSumatif()->min('score');

                                            if($maxScore){
                                                $maxScores = $mataPelajaran->tpsDescs()->select('code','desc')->where([
                                                    'semester_id' => $semester->id,
                                                    'level_id' => $kelas->level_id
                                                ])->whereHas('nilai',function($q)use($nilaiAkhir,$maxScore){
                                                    $q->where([
                                                        'rkd_score_id' => $nilaiAkhir->id,
                                                        'score' => $maxScore
                                                    ]);
                                                })->get();
                                                if($minScore && ($maxScore != $minScore)){
                                                    $minScores = $mataPelajaran->tpsDescs()->select('code','desc')->where([
                                                        'semester_id' => $semester->id,
                                                        'level_id' => $kelas->level_id
                                                    ])->whereHas('nilai',function($q)use($nilaiAkhir,$minScore){
                                                        $q->where([
                                                            'rkd_score_id' => $nilaiAkhir->id,
                                                            'score' => $minScore
                                                        ]);
                                                    })->get();

                                                    if($descs['min']){
                                                        $nilai['mapel'][$mataPelajaran->id]['rendah'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs['min']);
                                                        $nilai['mapel'][$mataPelajaran->id]['rendah'] = str_replace("@kompetensi",StringHelper::natural_language_join($minScores->pluck('desc')->toArray(),'dan'),$nilai['mapel'][$mataPelajaran->id]['rendah']);
                                                    }
                                                }
                                                if($descs['max']){
                                                    $nilai['mapel'][$mataPelajaran->id]['tinggi'] = str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$descs['max']);
                                                    $nilai['mapel'][$mataPelajaran->id]['tinggi'] = str_replace("@kompetensi",StringHelper::natural_language_join($maxScores->pluck('desc')->toArray(),'dan'),$nilai['mapel'][$mataPelajaran->id]['tinggi']);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if($kelas->major_id){
                                $kelompok_master = $kelompok_umum->take(2);
                                $kelompok_lain = $kelompok_umum->skip(2);
                                $kelompok = $kelompok_master->concat($kelompok_peminatan)->concat($kelompok_lain);
                            }
                        }

                        // Old
                        $pts_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pts()->first();
                        $pts_date = $pts_date ? $pts_date->report_date : null;

                        // Digital Signature
                        $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                        return view($this->route.'-report', compact('tahun','semester','kelas','unit','siswa','objectives','kelompok','tpDescs','descs','rapor','nilai','maxCount','pts_date','digital'));
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data LTS peserta didik yang ditemukan');
                    }
                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->studentNisLink]);
                }
                else{
                    Session::flash('danger', 'Tidak ada data peserta didik yang ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
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
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->count() > 0 ? true : false;

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q){
            $q->where(function($q){
                $q->where('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q){
            $q->where('is_active',1);
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas);
            if(!in_array($role,$managementRoles)){
                $kelas = $kelas->where('unit_id',auth()->user()->pegawai->unit_id);
                if($isWali){
                    $kelas = $kelas->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                }
            }
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->where('curriculum_id',2);
            })->first();

            if($kelas){
                $siswa = str_replace("-","/",$siswa);
                $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                    $q->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                })->where('student_nis', $siswa)->has('identitas')->first();

                if($siswa){
                    // Inti Function
                    $unit = $kelas->unit()->select('id','name')->first();
                    
                    $objectives = $kelas->level->objectiveElements()->where([
                        'semester_id' => $semester->id
                    ])->orderBy('sort_order')->get();

                    if($objectives && count($objectives) > 0){
                        $rapor = $kelas->rapor()->where([
                            'semester_id' => $semester->id
                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                            $q->where([
                                'semester_id' => $semester->id,
                                'class_id' => $kelas->id
                            ]);
                        })->where('student_id',$siswa->id)->first();

                        if(!$rapor){
                            $jabatan = Jabatan::where('code','11')->first();
                            $kepsek = $jabatan->pegawaiUnit()->where('unit_id',$kelas->unit_id)->whereHas('pegawai',function($q){
                                $q->aktif();
                            })->first();

                            $rapor = new NilaiRapor();
                            $rapor->student_id = $siswa->id;
                            $rapor->semester_id = $semester->id;
                            $rapor->class_id = $kelas->id;
                            $rapor->report_status_id = 0;
                            $rapor->acc_id = 0;
                            $rapor->unit_id = $kelas->unit_id;
                            $rapor->hr_name = $kelas->walikelas ? $kelas->walikelas->name : '-';
                            $rapor->hm_name = $kepsek ? $kepsek->pegawai->name : '-';
                            $rapor->save();
                            $rapor->fresh();
                        }
                        if($rapor){
                            $savedCount = 0;
                            $predicateList = ['A' => 4,'B' => 3,'C' => 2,'D' => 1];
                            foreach($objectives as $o){
                                $predicate = isset($request->predicate[$o->sort_order]) ? $request->predicate[$o->sort_order] : null;
                                $nilaiFormatif = $rapor->formatifKualitatif()->where('objective_id',$o->objective_id)->first();                            
                                if(!$nilaiFormatif && $predicate && in_array($predicate,['A','B','C','D'])){
                                    $nilaiFormatif = new FormatifKualitatif();
                                    $nilaiFormatif->report_score_id = $rapor->id;
                                    $nilaiFormatif->objective_id = $o->objective_id;
                                    $nilaiFormatif->predicate = null;
                                    $nilaiFormatif->score = 0;
                                    $nilaiFormatif->save();
                                    $nilaiFormatif->fresh();
                                }
                                if($nilaiFormatif){
                                    if($predicate && in_array($predicate,['A','B','C','D'])){
                                        $nilaiFormatif->predicate = $predicate;
                                        $score = $predicateList[$predicate];
                                        if(isset($score) && $score > 0){
                                            $nilaiFormatif->score = $score;
                                        }
                                        $nilaiFormatif->save();
                                        $savedCount++;
                                    }
                                }
                            }
                            if($savedCount == count($objectives)){
                                Session::flash('success', 'Semua perubahan perkembangan berhasil disimpan');
                            }
                            elseif($savedCount > 0 && ($savedCount < count($objectives))){
                                Session::flash('success', 'Beberapa perubahan perkembangan berhasil disimpan');
                            }
                            else{
                                Session::flash('danger', $role == 'kepsek' ? 'Belum ada tujuan pembelajaran yang dapat dinilai' : 'Tidak dapat menyimpan perubahan perkembangan');
                            }
                        }
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data tujuan pembelajaran yang ditemukan');
                    }
                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->studentNisLink]);
                }
                else{
                    Session::flash('danger', 'Tidak ada data peserta didik yang ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
                }
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
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
     * Validate the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request, $tahun, $semester, $kelas, $siswa)
    {
        $role = auth()->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::whereHas('semester',function($q){
            $q->whereHas('curricula',function($q){
                $q->where('curriculum_id',2);
            })->where(function($q){
                $q->where('is_active',1)->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                    $q->where('curriculum_id',2);
                });
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::whereHas('curricula',function($q){
            $q->where('curriculum_id',2);
        })->where(function($q){
            $q->where('is_active',1)->orWhereHas('riwayatKelas.kelas.level.curricula',function($q){
                $q->where('curriculum_id',2);
            });
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();

        if($semester){
            $kelas = $semester->tahunAjaran->kelas()->where('id',$kelas)->where('unit_id',auth()->user()->pegawai->unit_id)->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->where('curriculum_id',2);
            })->first();

            if($kelas){
                $siswa = str_replace("-","/",$siswa);
                $siswa = Siswa::select('id','student_nis','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                    $q->where([
                        'semester_id' => $semester->id,
                        'class_id' => $kelas->id
                    ]);
                })->where('student_nis', $siswa)->has('identitas')->first();

                if($siswa){
                    // Inti Function
                    $unit = $kelas->unit()->select('id','name')->first();

                    $rapor = $kelas->rapor()->where([
                        'semester_id' => $semester->id,
                        'student_id' => $siswa->id
                    ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->first();

                    if($rapor){
                        if($rapor->report_status_pts_id == 0){
                            if($kelas->unit_id == 1){
                                $pts = $rapor->pts_tk;
                                if(!$pts){
                                    $pts = new PtsTK();
                                    $pts->save();
                                }
                            }
                            else{
                                $pts = $rapor->pts;
                                if(!$pts){
                                    $pts = new RaporPts();
                                    $pts->save();
                                }
                            }
                            if($rapor->kehadiran){
                                $pts->absent = $rapor->kehadiran->absent;
                                $pts->sick = $rapor->kehadiran->sick;
                                $pts->leave = $rapor->kehadiran->leave;
                            }
                            else{
                                $pts->absent = $pts->sick = $pts->leave = 0;
                            }
                            $pts_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pts()->first();
                            if($pts_date){
                                $pts->report_date = $pts_date->report_date;
                            }
                            $pts->save();

                            $rapor->report_status_pts_id = 1;
                            $rapor->acc_id = auth()->user()->pegawai->id;
                            $rapor->save();
                            $rapor->fresh();

                            Session::flash('success', 'Data LTS ananda '.$siswa->identitas->student_name.' berhasil divalidasi');
                        }
                        elseif($rapor->report_status_pts_id == 1) Session::flash('danger', 'Data LTS ananda '.$siswa->identitas->student_name.' sudah divalidasi');
                    }
                    else Session::flash('danger', 'LTS ananda '.$siswa->identitas->student_name.' tidak ditemukan');
                }
                else Session::flash('danger', 'Tidak ada data peserta didik yang ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            }
            else{
                Session::flash('danger', 'Kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
