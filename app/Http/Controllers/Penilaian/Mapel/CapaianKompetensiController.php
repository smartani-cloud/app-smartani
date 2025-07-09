<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Helpers\StringHelper;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class CapaianKompetensiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'capaian-kompetensi';
        $this->modul = $modul;
        $this->active = 'Capaian Kompetensi';
        $this->route = $this->subsystem.'.penilaian.mapel.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $mataPelajaran = null)
    {
        $role = $request->user()->role->name;

        $kelasList = $mataPelajaranList = null;
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $riwayatKelas = $descs = $nilai = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::whereHas('semester',function($q){
                $q->where(function($q){
                    $q->where('is_active',1);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q){
                $q->where('is_active',1);
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id) : Kelas::whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
                $kelasList = $kelasList->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                        'curriculum_id' => 2,
                    ]);
                })->get();
                if($kelas){
                    $kelas = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]) : Kelas::whereHas('jadwal',function($q)use($semester){
                        $q->where([
                            'teacher_id' => auth()->user()->pegawai->id,
                            'semester_id' => $semester->id,
                        ]);
                    })->where('id', $kelas);
                    $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                            'curriculum_id' => 2,
                        ]);
                    })->first();

                    if($kelas){
                        $unit = $kelas->unit()->select('id','name')->first();

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

                        if($mataPelajaran){
                            $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                            if($mataPelajaran){
                                // Inti Function
                                $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'class_id' => $kelas->id
                                    ]);
                                })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

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

                                $tpDescs = $mataPelajaran->tpsDescs()->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $kelas->level_id
                                ])->count();

                                $raporQuery = $kelas->rapor()->where([
                                    'semester_id' => $semester->id
                                ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'class_id' => $kelas->id
                                    ]);
                                })->whereHas('nilaiAkhirKurdeka',function($q)use($mataPelajaran){
                                    $q->where([
                                        'subject_id' => $mataPelajaran->id
                                    ]);
                                });

                                if($riwayatKelas && count($riwayatKelas) > 0){
                                    foreach($riwayatKelas as $r){
                                        $rapor = clone $raporQuery;
                                        $rapor = $rapor->where('student_id',$r->id)->first();
                                        if($rapor){
                                            $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                                'subject_id' => $mataPelajaran->id
                                            ])->first();
                                            $nilai[$r->id]['akhir'] = 0;
                                            if($nilaiAkhir){
                                                $nilai[$r->id]['akhir'] = $nilaiAkhir->narWithSeparator;
                                                if($tpDescs > 0){
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
                                                            $nilai[$r->id]['min'] = $minScore.' ('.implode(', ',$minScores->pluck('code')->toArray()).')';

                                                            if($descs['min']){
                                                                $nilai[$r->id]['rendah'] = str_replace("@nama",$r->identitas->student_name ? $r->identitas->student_name : '',$descs['min']);
                                                                $nilai[$r->id]['rendah'] = str_replace("@kompetensi",StringHelper::natural_language_join($minScores->pluck('desc')->toArray(),'dan'),$nilai[$r->id]['rendah']);
                                                            }
                                                        }
                                                        $nilai[$r->id]['maks'] = $maxScore.' ('.implode(', ',$maxScores->pluck('code')->toArray()).')';
                                                        if($descs['max']){
                                                            $nilai[$r->id]['tinggi'] = str_replace("@nama",$r->identitas->student_name ? $r->identitas->student_name : '',$descs['max']);
                                                            $nilai[$r->id]['tinggi'] = str_replace("@kompetensi",StringHelper::natural_language_join($maxScores->pluck('desc')->toArray(),'dan'),$nilai[$r->id]['tinggi']);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
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
            // $semester = Semester::aktif()->first();
            // $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get() : $semester->jadwalPelajarans()->where('teacher_id', auth()->user()->pegawai->id)->with('kelas:id,level_id,class_name_id')->get()->pluck('kelas')->unique();
            // if($role == 'guru'){
            //     $kelas = $semester->jadwalPelajarans()->where('teacher_id', auth()->user()->pegawai->id)->with('kelas:id,level_id,class_name_id')->get()->pluck('kelas')->unique()->first();
            //     if($kelas){
            //         return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            //     }
            //     else return redirect()->route($this->route.'.index');
            // }

            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','mataPelajaranList','mataPelajaran','riwayatKelas','descs','nilai'));
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
    public function update(Request $request, $tahun, $semester, $kelas, $mataPelajaran)
    {
        $role = $request->user()->role->name;

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
            $kelas = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]) : Kelas::whereHas('jadwal',function($q)use($semester){
                $q->where([
                    'teacher_id' => auth()->user()->pegawai->id,
                    'semester_id' => $semester->id,
                ]);
            })->where('id', $kelas);
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($kelas){
                $unit = $kelas->unit()->select('id','name')->first();

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

                $mataPelajaranList = null;

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

                $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                    $descsQuery = $mataPelajaran->predicate()->select('id','predicate','description')->where([
                        'semester_id' => $semester->id,
                        'level_id' => $kelas->level_id,
                        'rpd_type_id' => 10
                    ]);

                    foreach(['max','min'] as $m){
                        $descs[$m] = clone $descsQuery;
                        $descs[$m] = $descs[$m]->where('predicate',$m)->first();

                        if(!$descs[$m]){
                            $descs[$m] = new PredikatDeskripsi();
                            $descs[$m]->semester_id = $semester->id;
                            $descs[$m]->level_id = $kelas->level_id;
                            $descs[$m]->subject_id = $mataPelajaran->id;
                            $descs[$m]->predicate = $m;
                            $descs[$m]->description = null;
                            $descs[$m]->employee_id = auth()->user()->pegawai->id;
                            $descs[$m]->rpd_type_id = 10;
                            $descs[$m]->save();
                            $descs[$m]->fresh();
                        }

                        $attr = $m.'Desc';
                        $descs[$m]->description = isset($request->{$attr}) ? $request->{$attr} : null;
                        $descs[$m]->save();
                    }

                    Session::flash('success', 'Perubahan deskripsi capaian kompetensi berhasil disimpan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

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
}
=======
<?php

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Helpers\StringHelper;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class CapaianKompetensiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'capaian-kompetensi';
        $this->modul = $modul;
        $this->active = 'Capaian Kompetensi';
        $this->route = $this->subsystem.'.penilaian.mapel.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $kelas = null, $mataPelajaran = null)
    {
        $role = $request->user()->role->name;

        $kelasList = $mataPelajaranList = null;
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $riwayatKelas = $descs = $nilai = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::whereHas('semester',function($q){
                $q->where(function($q){
                    $q->where('is_active',1);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q){
                $q->where('is_active',1);
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id) : Kelas::whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
                $kelasList = $kelasList->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                        'curriculum_id' => 2,
                    ]);
                })->get();
                if($kelas){
                    $kelas = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]) : Kelas::whereHas('jadwal',function($q)use($semester){
                        $q->where([
                            'teacher_id' => auth()->user()->pegawai->id,
                            'semester_id' => $semester->id,
                        ]);
                    })->where('id', $kelas);
                    $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                            'curriculum_id' => 2,
                        ]);
                    })->first();

                    if($kelas){
                        $unit = $kelas->unit()->select('id','name')->first();

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

                        if($mataPelajaran){
                            $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                            if($mataPelajaran){
                                // Inti Function
                                $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'class_id' => $kelas->id
                                    ]);
                                })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

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

                                $tpDescs = $mataPelajaran->tpsDescs()->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $kelas->level_id
                                ])->count();

                                $raporQuery = $kelas->rapor()->where([
                                    'semester_id' => $semester->id
                                ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'class_id' => $kelas->id
                                    ]);
                                })->whereHas('nilaiAkhirKurdeka',function($q)use($mataPelajaran){
                                    $q->where([
                                        'subject_id' => $mataPelajaran->id
                                    ]);
                                });

                                if($riwayatKelas && count($riwayatKelas) > 0){
                                    foreach($riwayatKelas as $r){
                                        $rapor = clone $raporQuery;
                                        $rapor = $rapor->where('student_id',$r->id)->first();
                                        if($rapor){
                                            $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                                'subject_id' => $mataPelajaran->id
                                            ])->first();
                                            $nilai[$r->id]['akhir'] = 0;
                                            if($nilaiAkhir){
                                                $nilai[$r->id]['akhir'] = $nilaiAkhir->narWithSeparator;
                                                if($tpDescs > 0){
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
                                                            $nilai[$r->id]['min'] = $minScore.' ('.implode(', ',$minScores->pluck('code')->toArray()).')';

                                                            if($descs['min']){
                                                                $nilai[$r->id]['rendah'] = str_replace("@nama",$r->identitas->student_name ? $r->identitas->student_name : '',$descs['min']);
                                                                $nilai[$r->id]['rendah'] = str_replace("@kompetensi",StringHelper::natural_language_join($minScores->pluck('desc')->toArray(),'dan'),$nilai[$r->id]['rendah']);
                                                            }
                                                        }
                                                        $nilai[$r->id]['maks'] = $maxScore.' ('.implode(', ',$maxScores->pluck('code')->toArray()).')';
                                                        if($descs['max']){
                                                            $nilai[$r->id]['tinggi'] = str_replace("@nama",$r->identitas->student_name ? $r->identitas->student_name : '',$descs['max']);
                                                            $nilai[$r->id]['tinggi'] = str_replace("@kompetensi",StringHelper::natural_language_join($maxScores->pluck('desc')->toArray(),'dan'),$nilai[$r->id]['tinggi']);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
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
            // $semester = Semester::aktif()->first();
            // $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where('unit_id',$request->user()->pegawai->unit_id)->get() : $semester->jadwalPelajarans()->where('teacher_id', auth()->user()->pegawai->id)->with('kelas:id,level_id,class_name_id')->get()->pluck('kelas')->unique();
            // if($role == 'guru'){
            //     $kelas = $semester->jadwalPelajarans()->where('teacher_id', auth()->user()->pegawai->id)->with('kelas:id,level_id,class_name_id')->get()->pluck('kelas')->unique()->first();
            //     if($kelas){
            //         return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]);
            //     }
            //     else return redirect()->route($this->route.'.index');
            // }

            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','kelasList','kelas','mataPelajaranList','mataPelajaran','riwayatKelas','descs','nilai'));
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
    public function update(Request $request, $tahun, $semester, $kelas, $mataPelajaran)
    {
        $role = $request->user()->role->name;

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
            $kelas = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where(['unit_id' => $request->user()->pegawai->unit_id, 'id' => $kelas]) : Kelas::whereHas('jadwal',function($q)use($semester){
                $q->where([
                    'teacher_id' => auth()->user()->pegawai->id,
                    'semester_id' => $semester->id,
                ]);
            })->where('id', $kelas);
            $kelas = $kelas->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($kelas){
                $unit = $kelas->unit()->select('id','name')->first();

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

                $mataPelajaranList = null;

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

                $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                        $q->where([
                            'semester_id' => $semester->id,
                            'class_id' => $kelas->id
                        ]);
                    })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                    $descsQuery = $mataPelajaran->predicate()->select('id','predicate','description')->where([
                        'semester_id' => $semester->id,
                        'level_id' => $kelas->level_id,
                        'rpd_type_id' => 10
                    ]);

                    foreach(['max','min'] as $m){
                        $descs[$m] = clone $descsQuery;
                        $descs[$m] = $descs[$m]->where('predicate',$m)->first();

                        if(!$descs[$m]){
                            $descs[$m] = new PredikatDeskripsi();
                            $descs[$m]->semester_id = $semester->id;
                            $descs[$m]->level_id = $kelas->level_id;
                            $descs[$m]->subject_id = $mataPelajaran->id;
                            $descs[$m]->predicate = $m;
                            $descs[$m]->description = null;
                            $descs[$m]->employee_id = auth()->user()->pegawai->id;
                            $descs[$m]->rpd_type_id = 10;
                            $descs[$m]->save();
                            $descs[$m]->fresh();
                        }

                        $attr = $m.'Desc';
                        $descs[$m]->description = isset($request->{$attr}) ? $request->{$attr} : null;
                        $descs[$m]->save();
                    }

                    Session::flash('success', 'Perubahan deskripsi capaian kompetensi berhasil disimpan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

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
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
