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
use App\Models\Penilaian\SertifIklas;
use App\Models\Penilaian\Iklas\DeskripsiIklas;
use App\Models\Penilaian\Iklas\IndikatorKurikulumIklas;
use App\Models\Penilaian\Tk\FormatifKualitatif;
use App\Models\Penilaian\Tk\Objective;
use App\Models\Penempatan\Jabatan;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class RaporKurdekaController extends Controller
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
        $this->active = 'Cetak Rapor';
        $this->route = $this->subsystem.'.penilaian.rapor.'.$this->modul;
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

        $kelasList = $unit = $riwayatKelas = $objectives = $competencies = $kategoriList = $rapor = $count = $nilai = $capaian = $acceptable = null;
        $canOverview = false;

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

                        $competencies = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();

                        $kategoriList['khataman'] = ['kelancaran','kebagusan'];

                        $kategoriList['hafalan'] = HafalanType::whereIn('mem_type',['hadits','doa'])->get();

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

                                $nilai[$r->id]['akhir'] = '-';

                                if($role == 'kepsek'){
                                    $acceptable[$r->id] = $rapor[$r->id] && $rapor[$r->id]->report_status_id == 0 ? true : false;
                                    $canOverview = true;
                                }

                                if($rapor[$r->id] && $canOverview){
                                    if($unit->id == 1){
                                        // Elemen Capaian Pembelajaran
                                        if($objectives && $objectives->count() > 0){
                                            $ntp[$r->id] = 0;
                                            foreach($objectives as $o){
                                                $nilaiFormatif = $rapor[$r->id]->formatifKualitatif()->where('objective_id',$o->objective_id)->where('score','>',0)->first();
                                                if($nilaiFormatif) $ntp[$r->id]++;
                                            }
                                            $nilai[$r->id]['akhir'] = $ntp[$r->id].'/'.$objectives->count();
                                        }
                                    }
                                    else{
                                        // Mata Pelajaran
                                        if($mataPelajaranList && $mataPelajaranList->count() > 0){
                                            $nar[$r->id] = 0;
                                            foreach($mataPelajaranList as $mataPelajaran){
                                                $nilaiAkhir = $rapor[$r->id]->nilaiAkhirKurdeka()->where([
                                                    'subject_id' => $mataPelajaran->id
                                                ])->where('nar','>',0)->first();
                                                if($nilaiAkhir) $nar[$r->id]++;
                                            }
                                            $nilai[$r->id]['akhir'] = $nar[$r->id].'/'.$mataPelajaranList->count();
                                        }
                                    }

                                    // IKLaS
                                    if($competencies && count($competencies) > 0){
                                        $count[$r->id]['iklas'] = $rapor[$r->id]->nilaiIklas()->whereHas('kompetensi.categories',function($q)use($unit,$semester){
                                            $q->where([
                                                'semester_id' => $semester->id,
                                                'unit_id' => $unit->id
                                            ]);
                                        })->where('predicate','>',0)->count();
                                    }

                                    // Khataman
                                    $capaian[$r->id]['khataman']['status'] = 0;
                                    $capaian[$r->id]['khataman']['type'] = $rapor[$r->id]->khatamKurdeka;
                                    if($capaian[$r->id]['khataman']['type'] && $capaian[$r->id]['khataman']['type']->type){
                                        if($capaian[$r->id]['khataman']['type']->type_id == 1){
                                            $capaian[$r->id]['khataman']['quran'] = $rapor[$r->id]->khatamQuran()->get();
                                            if($capaian[$r->id]['khataman']['quran'] && count($capaian[$r->id]['khataman']['quran']) >= 2){
                                                $capaian[$r->id]['khataman']['status']++;
                                            }
                                        }
                                        elseif($capaian[$r->id]['khataman']['type']->type_id == 2){
                                            $capaian[$r->id]['khataman']['buku'] = null;
                                            if($rapor[$r->id]->khatamBuku && $rapor[$r->id]->khatamBuku->buku){
                                                $capaian[$r->id]['khataman']['buku'] = $rapor[$r->id]->khatamBuku->buku->title;
                                                $capaian[$r->id]['khataman']['status']++;
                                            }
                                        }
                                    }
                                    foreach($kategoriList['khataman'] as $kategori){
                                        $capaianDeskripsi[$r->id][$kategori] = $rapor[$r->id]->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                                            $q->where('rpd_type',ucwords($kategori).' Tilawah');
                                        })->first();
                                        $capaian[$r->id]['khataman'][$kategori]['desc'] = null;
                                        if($capaianDeskripsi[$r->id][$kategori] && $capaianDeskripsi[$r->id][$kategori]->deskripsi){
                                            $capaian[$r->id]['khataman'][$kategori]['desc'] = $capaianDeskripsi[$r->id][$kategori]->deskripsi->description;
                                            $capaian[$r->id]['khataman']['status']++;
                                        }
                                    }

                                    // Hafalan Qur'an
                                    $capaian[$r->id]['quran']['hafalan'] = $rapor[$r->id]->quranKurdeka()->count();
                                    $capaianDeskripsi[$r->id]['quran'] = $rapor[$r->id]->deskripsiKurdeka()->whereHas('jenis',function($q){
                                        $q->where('rpd_type','Hafalan');
                                    })->first();
                                    $capaian[$r->id]['quran']['desc'] = $capaianDeskripsi[$r->id]['quran'] && $capaianDeskripsi[$r->id]['quran']->deskripsi ? $capaianDeskripsi[$r->id]['quran']->deskripsi->description : null;

                                    // Hafalan Hadits & Doa
                                    if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0){
                                        foreach($kategoriList['hafalan'] as $kategori){
                                            $kategori = $kategori->mem_type;
                                            $capaian[$r->id][$kategori]['hafalan'] = $rapor[$r->id]->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                                                $q->where('mem_type',ucwords($kategori));
                                            })->count();
                                            $capaianDeskripsi[$r->id][$kategori] = $rapor[$r->id]->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                                                $q->where('rpd_type','Hafalan '.ucwords($kategori));
                                            })->first();
                                            $capaian[$r->id][$kategori]['desc'] = $capaianDeskripsi[$r->id][$kategori] && $capaianDeskripsi[$r->id][$kategori]->deskripsi ? $capaianDeskripsi[$r->id][$kategori]->deskripsi->description : null;
                                        }
                                    }
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

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','unit','riwayatKelas','siswa','objectives','competencies','kategoriList','rapor','count','nilai','capaian','canOverview','acceptable'));
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
                        $mataPelajaranList = $objectives = $kelompok = $tpDescs = $descs = $mergedRows = $iklas = $nilai = $capaian = null;
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
                                    $nilai['mapel'][$mataPelajaran->id]['akhir'] = 0;
                                    if($nilaiAkhir){
                                        $nilai['mapel'][$mataPelajaran->id]['akhir'] = $nilaiAkhir->narWithSeparator;

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

                                        $tpDescs[$mataPelajaran->id] = $mataPelajaran->tpsDescs()->select('code','desc')->where([
                                            'semester_id' => $semester->id,
                                            'level_id' => $kelas->level_id
                                        ])->get();

                                        if($tpDescs[$mataPelajaran->id] && count($tpDescs[$mataPelajaran->id]) > 0){
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

                        // IKLaS
                        $competencies = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();

                        if($competencies && count($competencies) > 0){
                            $catActive = $parentCompetence = null;
                            foreach($competencies as $c){
                                $nilaiKompetensi = $rapor->nilaiIklas()->where('competence_id',$c->competence_id)->first();
                                $nilai['iklas'][$c->competence_id] = $nilaiKompetensi ? $nilaiKompetensi->predicate : 0;

                                $deskripsiKompetensi = DeskripsiIklas::where([
                                    'class_id' => $kelas->id,
                                    'iklas_curriculum_id' => $c->id,
                                ])->whereHas('kurikulum',function($q)use($semester,$unit){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'unit_id' => $unit->id,
                                    ]);
                                })->first();

                                $mergedRows['iklas'][$c->competence_id] = 1;
                                if($deskripsiKompetensi){
                                    $descs['iklas'][$c->competence_id] = $deskripsiKompetensi;
                                    if($deskripsiKompetensi->is_merged == 0) $parentCompetence = $c->competence_id;
                                    if($catActive == $c->category_id && $deskripsiKompetensi->is_merged == 1){
                                        $mergedRows['iklas'][$parentCompetence]++;
                                    }
                                }

                                if($catActive != $c->category_id) $iklas['rows'][$c->category_id] = 0;
                                $indicators = IndikatorKurikulumIklas::where([
                                    'level_id' => $kelas->level_id,
                                    'iklas_curriculum_id' => $c->id,
                                ])->whereHas('kurikulum',function($q)use($semester,$unit){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'unit_id' => $unit->id,
                                    ]);
                                })->get();
                                $iklas['indikator'][$c->id] = null;
                                if($indicators && count($indicators) > 0){
                                    $iklas['indikator'][$c->id] = $indicators;
                                    $iklas['rows'][$c->category_id] += count($indicators);
                                }
                                else{
                                    $iklas['rows'][$c->category_id]++;
                                }

                                if($catActive != $c->category_id) $catActive = $c->category_id;
                            }
                        }

                        // Khataman
                        $capaian['khataman']['type'] = $rapor->khatamKurdeka;
                        if($capaian['khataman']['type'] && $capaian['khataman']['type']->type){
                            if($capaian['khataman']['type']->type_id == 1){
                                $capaian['khataman']['quran'] = $rapor->khatamQuran()->get();
                            }
                            elseif($capaian['khataman']['type']->type_id == 2){
                                $capaian['khataman']['buku'] = $rapor->khatamBuku && $rapor->khatamBuku->buku ? $rapor->khatamBuku->buku->title : null;
                            }
                        }
                        $kategoriList['khataman'] = ['kelancaran','kebagusan'];
                        foreach($kategoriList['khataman'] as $kategori){
                            $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                                $q->where('rpd_type',ucwords($kategori).' Tilawah');
                            })->first();
                            $capaian['khataman'][$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                        }

                        // Hafalan Qur'an
                        $capaian['quran']['hafalan'] = $rapor->quranKurdeka()->get();
                        $capaianDeskripsi['quran'] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q){
                            $q->where('rpd_type','Hafalan');
                        })->first();
                        $capaian['quran']['desc'] = $capaianDeskripsi['quran'] && $capaianDeskripsi['quran']->deskripsi ? $capaianDeskripsi['quran']->deskripsi->description : null;

                        // Hafalan Hadits & Doa
                        $kategoriList['hafalan'] = HafalanType::whereIn('mem_type',['hadits','doa'])->get();

                        if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0){
                            foreach($kategoriList['hafalan'] as $kategori){
                                $kategori = $kategori->mem_type;
                                $capaian[$kategori]['hafalan'] = $rapor->hafalanKurdeka()->whereHas('jenis',function($q)use($kategori){
                                    $q->where('mem_type',ucwords($kategori));
                                })->get();
                                $capaianDeskripsi[$kategori] = $rapor->deskripsiKurdeka()->whereHas('jenis',function($q)use($kategori){
                                    $q->where('rpd_type','Hafalan '.ucwords($kategori));
                                })->first();
                                $capaian[$kategori]['desc'] = $capaianDeskripsi[$kategori] && $capaianDeskripsi[$kategori]->deskripsi ? $capaianDeskripsi[$kategori]->deskripsi->description : null;
                            }
                        }

                        // Old
                        $pas_date = $semester->tanggalRapor()->where('unit_id', $unit->id)->pas()->first();
                        $pas_date = $pas_date ? $pas_date->report_date : null;

                        // Digital Signature
                        $digital = isset($request->digital) && $request->digital == 1 ? true : false;

                        return view($this->route.'-report', compact('tahun','semester','kelas','unit','siswa','objectives','competencies','kelompok','tpDescs','kategoriList','descs','rapor','nilai','capaian','mergedRows','iklas','pas_date','digital'));
                    }
                    else{
                        Session::flash('danger', 'Tidak ada data rapor peserta didik yang ditemukan');
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
                        'student_id' => $siswa->id,
                        'report_status_pts_id' => 1,
                        'report_status_id' => 0
                    ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->first();

                    if($rapor){
                        if($rapor->report_status_pts_id == 1){
                            if($rapor->report_status_id == 0){
                                $rapor->report_status_id = 1;
                                $rapor->acc_id = auth()->user()->pegawai->id;
                                $rapor->save();
                                $rapor->fresh();

                                if($rapor->pas && $rapor->pas->conclusion == 'lulus'){
                                    $jabatan = Jabatan::where('code','11')->first();
                                    $kepsek = $jabatan->pegawaiUnit()->where('unit_id',$kelas->unit_id)->whereHas('pegawai',function($q){
                                        $q->aktif();
                                    })->first();

                                    $sertifIklas = SertifIklas::where(['academic_year_id' => $tahun->id,'unit_id' => $unit->id,'student_id' => $siswa->id]);

                                    if($sertifIklas->count() < 1){
                                        SertifIklas::create([
                                            'academic_year_id' => $tahun->id,
                                            'unit_id' => $unit->id,
                                            'student_id' => $siswa->id,
                                            'hm_name' => $kepsek ? $kepsek->pegawai->name : '-'
                                        ]);
                                    }
                                    else{
                                        $sertifIklas = $sertifIklas->first();
                                        $sertifIklas->academic_year_id = $tahun->id;
                                        $sertifIklas->hm_name = $kepsek ? $kepsek->pegawai->name : '-';
                                        $sertifIklas->save();
                                    }
                                }
                                Session::flash('success', 'Data rapor ananda '.$siswa->identitas->student_name.' berhasil divalidasi');
                            }
                            elseif($rapor->report_status_id == 1) Session::flash('danger', 'Data rapor ananda '.$siswa->identitas->student_name.' sudah divalidasi');
                        }
                        else Session::flash('danger', 'Data LTS ananda '.$siswa->identitas->student_name.' perlu divalidasi terlebih dahulu');
                    }
                    else Session::flash('danger', 'Rapor ananda '.$siswa->identitas->student_name.' tidak ditemukan');
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
