<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Kurdeka\NilaiSumatif;
use App\Models\Penilaian\Kurdeka\TpsDesc;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class DeskripsiTpsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'deskripsi-tps';
        $this->modul = $modul;
        $this->active = 'Deskripsi TP Sumatif';
        $this->route = $this->subsystem.'.penilaian.mapel.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $tingkat = null, $mataPelajaran = null)
    {
        $role = auth()->user()->role->name;

        $tingkatList = $mataPelajaranList = null;
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $data = null;

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
                $tingkatList = auth()->user()->pegawai->unit->levels()->whereHas('classes',function($q)use($semester){
                    $q->whereHas('jadwal',function($q)use($semester){
                        $q->where([
                            'teacher_id' => auth()->user()->pegawai->id,
                            'semester_id' => $semester->id,
                        ]);
                    });
                })->whereHas('curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                        'curriculum_id' => 2,
                    ]);
                })->get();
                if($tingkat){
                    $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                        $q->whereHas('jadwal',function($q)use($semester){
                            $q->where([
                                'teacher_id' => auth()->user()->pegawai->id,
                                'semester_id' => $semester->id,
                            ]);
                        });
                    })->whereHas('curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                            'curriculum_id' => 2,
                        ]);
                    })->first();

                    if($tingkat){
                        $unit = auth()->user()->pegawai->unit;

                        $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                        $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                            $q->when($role == 'guru',function($q){
                                return $q->where('employee_id',auth()->user()->pegawai->id);
                            })->whereHas('skbm',function($q)use($tahun){
                                $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                            });
                        });

                        if($unit->name == 'SD'){
                            $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                                $q->where('level_id',$tingkat->id);
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
                                $data = $mataPelajaran->tpsDescs()->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $tingkat->id
                                ])->get();
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
            // $tingkatList = auth()->user()->pegawai->unit->levels()->get();

            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','tingkatList','tingkat','mataPelajaranList','mataPelajaran','data'));
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
    public function store(Request $request, $tahun, $semester, $tingkat, $mataPelajaran)
    {
        $role = auth()->user()->role->name;

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
            $tingkat = auth()->user()->pegawai->unit->levels()->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->where(['id' => $tingkat])->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $messages = [
                        'desc.required' => 'Mohon tuliskan nama tujuan pembelajaran',
                    ];

                    $this->validate($request, [
                        'desc' => 'required',
                    ], $messages);

                    $count = TpsDesc::where([
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id,
                        'desc' => $request->desc
                    ]);
                    
                    if($count->count() < 1){
                        $tpsExist = TpsDesc::where([
                            'semester_id' => $semester->id,
                            'level_id' => $tingkat->id,
                            'subject_id' => $mataPelajaran->id
                        ]);
                        $isFirstInput = $tpsExist->count() == 0 ? true : false;
                        $tps = new TpsDesc();
                        $tps->semester_id = $semester->id;
                        $tps->level_id = $tingkat->id;
                        $tps->subject_id = $mataPelajaran->id;
                        $tps->employee_id = auth()->user()->pegawai->id;
                        $tps->code = 'TP-'.($tpsExist->count()+1);
                        $tps->desc = $request->desc;
                        $tps->save();
                        $tps->fresh();

                        $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where([
                                'unit_id' => $request->user()->pegawai->unit_id,
                                'level_id' => $tingkat->id
                            ])->get() : Kelas::where('level_id',$tingkat->id)->whereHas('jadwal',function($q)use($semester){
                            $q->where([
                                //'teacher_id' => auth()->user()->pegawai->id,
                                'semester_id' => $semester->id,
                            ]);
                        })->get();

                        foreach($kelasList as $kelas){
                            $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                            $tpDescCount = $mataPelajaran->tpsDescs()->where([
                                'semester_id' => $semester->id,
                                'level_id' => $kelas->level_id
                            ])->count();

                            $percentages = $mataPelajaran->finalScorePercentages()->where([
                                'semester_id' => $semester->id,
                                'level_id' => $kelas->level_id
                            ])->first();

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
                                        if($nilaiAkhir){
                                            $nilaiAkhir->nas = $tpDescCount && $tpDescCount > 0 ? ($nilaiAkhir->nilaiSumatif()->sum('score')/$tpDescCount) : 0;
                                            if($percentages){
                                                $nar = 0;
                                                $finalScores = ['naf','nas','ntss','nass'];
                                                foreach($finalScores as $f){
                                                    $attr = $f.'_percentage';
                                                    $nar += (($percentages->{$attr}/100)*$nilaiAkhir->{$f});
                                                }
                                                $nilaiAkhir->nar = $nar;
                                            }
                                            else $nilaiAkhir->nar = null;
                                            $nilaiAkhir->save();
                                        }
                                    }
                                }
                            }
                        }

                        Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
                    }

                    else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
                }
            }
            else{
                Session::flash('danger', 'Tingkat kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
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
    public function edit(Request $request, $tahun, $semester, $tingkat, $mataPelajaran)
    {
        $role = auth()->user()->role->name;

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $item = TpsDesc::where([
                        'id' => $request->id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id
                    ])->first();

                    if($item){
                        $route = $this->route;

                        return view($this->route.'-edit', compact('route','semester','tingkat','mataPelajaran','item'));
                    }
                    else return 'Ups, sepertinya ada kesalahan';
                }
                else return 'Ups, mata pelajaran tidak ditemukan';
            }
            else return 'Ups, tingkat kelas tidak ditemukan';
        }
        else return 'Ups, tahun pelajaran tidak valid';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahun, $semester, $tingkat, $mataPelajaran)
    {
        $role = auth()->user()->role->name;

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $messages = [
                        'editDesc.required' => 'Mohon tuliskan nama tujuan pembelajaran',
                    ];

                    $this->validate($request, [
                        'editDesc' => 'required',
                    ], $messages);

                    $item = TpsDesc::where([
                        'id' => $request->id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id
                    ])->first();
                    $count = TpsDesc::where([
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id,
                        'desc' => $request->editDesc
                    ])->where('id','!=',$request->id)->count();

                    if($item && $count < 1){
                        $old = $item->desc;
                        $item->desc = $request->editDesc;
                        $item->save();

                       Session::flash('success','Data '.$old.' berhasil diubah'.($old != $item->desc ? ' menjadi '.$item->desc : ''));
                    }

                    else Session::flash('danger','Perubahan data gagal disimpan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
                }
            }
            else{
                Session::flash('danger', 'Tingkat kelas tidak ditemukan');

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
    public function destroy(Request $request, $tahun, $semester, $tingkat, $mataPelajaran, $id)
    {
        $role = auth()->user()->role->name;

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $item = TpsDesc::where([
                        'id' => $id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id
                    ])->first();

                    //$scoreCount = $item->nilai()->count();
                    if($item){
                        $name = $item->desc;

                        // Delete relations
                        $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where([
                                'unit_id' => $request->user()->pegawai->unit_id,
                                'level_id' => $tingkat->id
                            ])->get() : Kelas::where('level_id',$tingkat->id)->whereHas('jadwal',function($q)use($semester){
                            $q->where([
                                //'teacher_id' => auth()->user()->pegawai->id,
                                'semester_id' => $semester->id,
                            ]);
                        })->get();

                        foreach($kelasList as $kelas){
                            $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                            $tpDescCount = $mataPelajaran->tpsDescs()->where([
                                'semester_id' => $semester->id,
                                'level_id' => $kelas->level_id
                            ])->count();

                            $percentages = $mataPelajaran->finalScorePercentages()->where([
                                'semester_id' => $semester->id,
                                'level_id' => $kelas->level_id
                            ])->first();

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
                                $item->nilai()->whereHas('nilaiAkhir',function($q)use($semester,$kelas,$mataPelajaran){
                                    $q->whereHas('rapor',function($q)use($semester,$kelas){
                                        $q->where([
                                            'semester_id' => $semester->id,
                                            'class_id' => $kelas->id
                                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                                            $q->where([
                                                'semester_id' => $semester->id,
                                                'class_id' => $kelas->id
                                            ]);
                                        });
                                    })->where('subject_id',$mataPelajaran->id);
                                })->delete();

                                foreach($riwayatKelas as $r){
                                    $rapor = clone $raporQuery;
                                    $rapor = $rapor->where('student_id',$r->id)->first();
                                    if($rapor){
                                        $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                            'subject_id' => $mataPelajaran->id
                                        ])->first();
                                        if($nilaiAkhir){
                                            $nilaiAkhir->nas = $tpDescCount && ($tpDescCount-1) > 0 ? ($nilaiAkhir->nilaiSumatif()->sum('score')/($tpDescCount-1)) : 0;
                                            if($percentages){
                                                $nar = 0;
                                                $finalScores = ['naf','nas','ntss','nass'];
                                                foreach($finalScores as $f){
                                                    $attr = $f.'_percentage';
                                                    $nar += (($percentages->{$attr}/100)*$nilaiAkhir->{$f});
                                                }
                                                $nilaiAkhir->nar = $nar;
                                            }
                                            else $nilaiAkhir->nar = null;
                                            $nilaiAkhir->save();
                                        }
                                    }
                                }
                            }
                        }

                        $item->forceDelete();

                        $tpsExist = TpsDesc::where([
                            'semester_id' => $semester->id,
                            'level_id' => $tingkat->id,
                            'subject_id' => $mataPelajaran->id
                        ])->get();

                        $i = 1;
                        foreach($tpsExist as $t){
                            $t->code = 'TP-'.$i++;
                            $t->save(['timestamps' => false]);
                        }

                        Session::flash('success','Data '.$name.' berhasil dihapus');
                    }
                    else Session::flash('danger','Data gagal dihapus');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
                }
            }
            else{
                Session::flash('danger', 'Tingkat kelas tidak ditemukan');

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

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Kurdeka\NilaiSumatif;
use App\Models\Penilaian\Kurdeka\TpsDesc;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class DeskripsiTpsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'deskripsi-tps';
        $this->modul = $modul;
        $this->active = 'Deskripsi TP Sumatif';
        $this->route = $this->subsystem.'.penilaian.mapel.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $tingkat = null, $mataPelajaran = null)
    {
        $role = auth()->user()->role->name;

        $tingkatList = $mataPelajaranList = null;
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $data = null;

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
                $tingkatList = auth()->user()->pegawai->unit->levels()->whereHas('classes',function($q)use($semester){
                    $q->whereHas('jadwal',function($q)use($semester){
                        $q->where([
                            'teacher_id' => auth()->user()->pegawai->id,
                            'semester_id' => $semester->id,
                        ]);
                    });
                })->whereHas('curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                        'curriculum_id' => 2,
                    ]);
                })->get();
                if($tingkat){
                    $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                        $q->whereHas('jadwal',function($q)use($semester){
                            $q->where([
                                'teacher_id' => auth()->user()->pegawai->id,
                                'semester_id' => $semester->id,
                            ]);
                        });
                    })->whereHas('curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                            'curriculum_id' => 2,
                        ]);
                    })->first();

                    if($tingkat){
                        $unit = auth()->user()->pegawai->unit;

                        $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                        $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                            $q->when($role == 'guru',function($q){
                                return $q->where('employee_id',auth()->user()->pegawai->id);
                            })->whereHas('skbm',function($q)use($tahun){
                                $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                            });
                        });

                        if($unit->name == 'SD'){
                            $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                                $q->where('level_id',$tingkat->id);
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
                                $data = $mataPelajaran->tpsDescs()->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $tingkat->id
                                ])->get();
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
            // $tingkatList = auth()->user()->pegawai->unit->levels()->get();

            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','tingkatList','tingkat','mataPelajaranList','mataPelajaran','data'));
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
    public function store(Request $request, $tahun, $semester, $tingkat, $mataPelajaran)
    {
        $role = auth()->user()->role->name;

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
            $tingkat = auth()->user()->pegawai->unit->levels()->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->where(['id' => $tingkat])->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $messages = [
                        'desc.required' => 'Mohon tuliskan nama tujuan pembelajaran',
                    ];

                    $this->validate($request, [
                        'desc' => 'required',
                    ], $messages);

                    $count = TpsDesc::where([
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id,
                        'desc' => $request->desc
                    ]);
                    
                    if($count->count() < 1){
                        $tpsExist = TpsDesc::where([
                            'semester_id' => $semester->id,
                            'level_id' => $tingkat->id,
                            'subject_id' => $mataPelajaran->id
                        ]);
                        $isFirstInput = $tpsExist->count() == 0 ? true : false;
                        $tps = new TpsDesc();
                        $tps->semester_id = $semester->id;
                        $tps->level_id = $tingkat->id;
                        $tps->subject_id = $mataPelajaran->id;
                        $tps->employee_id = auth()->user()->pegawai->id;
                        $tps->code = 'TP-'.($tpsExist->count()+1);
                        $tps->desc = $request->desc;
                        $tps->save();
                        $tps->fresh();

                        $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where([
                                'unit_id' => $request->user()->pegawai->unit_id,
                                'level_id' => $tingkat->id
                            ])->get() : Kelas::where('level_id',$tingkat->id)->whereHas('jadwal',function($q)use($semester){
                            $q->where([
                                //'teacher_id' => auth()->user()->pegawai->id,
                                'semester_id' => $semester->id,
                            ]);
                        })->get();

                        foreach($kelasList as $kelas){
                            $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                            $tpDescCount = $mataPelajaran->tpsDescs()->where([
                                'semester_id' => $semester->id,
                                'level_id' => $kelas->level_id
                            ])->count();

                            $percentages = $mataPelajaran->finalScorePercentages()->where([
                                'semester_id' => $semester->id,
                                'level_id' => $kelas->level_id
                            ])->first();

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
                                        if($nilaiAkhir){
                                            $nilaiAkhir->nas = $tpDescCount && $tpDescCount > 0 ? ($nilaiAkhir->nilaiSumatif()->sum('score')/$tpDescCount) : 0;
                                            if($percentages){
                                                $nar = 0;
                                                $finalScores = ['naf','nas','ntss','nass'];
                                                foreach($finalScores as $f){
                                                    $attr = $f.'_percentage';
                                                    $nar += (($percentages->{$attr}/100)*$nilaiAkhir->{$f});
                                                }
                                                $nilaiAkhir->nar = $nar;
                                            }
                                            else $nilaiAkhir->nar = null;
                                            $nilaiAkhir->save();
                                        }
                                    }
                                }
                            }
                        }

                        Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
                    }

                    else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
                }
            }
            else{
                Session::flash('danger', 'Tingkat kelas tidak ditemukan');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
        }
        else{
            Session::flash('danger', 'Pilih tahun pelajaran yang valid');

            return redirect()->route($this->route.'.index');
        }
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
    public function edit(Request $request, $tahun, $semester, $tingkat, $mataPelajaran)
    {
        $role = auth()->user()->role->name;

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $item = TpsDesc::where([
                        'id' => $request->id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id
                    ])->first();

                    if($item){
                        $route = $this->route;

                        return view($this->route.'-edit', compact('route','semester','tingkat','mataPelajaran','item'));
                    }
                    else return 'Ups, sepertinya ada kesalahan';
                }
                else return 'Ups, mata pelajaran tidak ditemukan';
            }
            else return 'Ups, tingkat kelas tidak ditemukan';
        }
        else return 'Ups, tahun pelajaran tidak valid';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tahun, $semester, $tingkat, $mataPelajaran)
    {
        $role = auth()->user()->role->name;

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $messages = [
                        'editDesc.required' => 'Mohon tuliskan nama tujuan pembelajaran',
                    ];

                    $this->validate($request, [
                        'editDesc' => 'required',
                    ], $messages);

                    $item = TpsDesc::where([
                        'id' => $request->id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id
                    ])->first();
                    $count = TpsDesc::where([
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id,
                        'desc' => $request->editDesc
                    ])->where('id','!=',$request->id)->count();

                    if($item && $count < 1){
                        $old = $item->desc;
                        $item->desc = $request->editDesc;
                        $item->save();

                       Session::flash('success','Data '.$old.' berhasil diubah'.($old != $item->desc ? ' menjadi '.$item->desc : ''));
                    }

                    else Session::flash('danger','Perubahan data gagal disimpan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
                }
            }
            else{
                Session::flash('danger', 'Tingkat kelas tidak ditemukan');

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
    public function destroy(Request $request, $tahun, $semester, $tingkat, $mataPelajaran, $id)
    {
        $role = auth()->user()->role->name;

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('classes',function($q)use($semester){
                $q->whereHas('jadwal',function($q)use($semester){
                    $q->where([
                        'teacher_id' => auth()->user()->pegawai->id,
                        'semester_id' => $semester->id,
                    ]);
                });
            })->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                    'curriculum_id' => 2,
                ]);
            })->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                $mapelFiltered = MataPelajaran::select('id','subject_name')->whereIn('group_subject_id', $kelompok->pluck('id'))->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                if($unit->name == 'SD'){
                    $mapelFiltered = $mapelFiltered->whereHas('mapelKelas',function($q)use($tingkat){
                        $q->where('level_id',$tingkat->id);
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

                $mapelCount = $mataPelajaranList->where('id',$mataPelajaran)->count();
                $mataPelajaran = $mapelCount ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $item = TpsDesc::where([
                        'id' => $id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'subject_id' => $mataPelajaran->id
                    ])->first();

                    //$scoreCount = $item->nilai()->count();
                    if($item){
                        $name = $item->desc;

                        // Delete relations
                        $kelasList = $role == 'kepsek' ? $semester->tahunAjaran->kelas()->where([
                                'unit_id' => $request->user()->pegawai->unit_id,
                                'level_id' => $tingkat->id
                            ])->get() : Kelas::where('level_id',$tingkat->id)->whereHas('jadwal',function($q)use($semester){
                            $q->where([
                                //'teacher_id' => auth()->user()->pegawai->id,
                                'semester_id' => $semester->id,
                            ]);
                        })->get();

                        foreach($kelasList as $kelas){
                            $riwayatKelas = Siswa::select('id','student_id')->whereHas('riwayatKelas',function($q)use($semester,$kelas){
                                $q->where([
                                    'semester_id' => $semester->id,
                                    'class_id' => $kelas->id
                                ]);
                            })->has('identitas')->with('identitas:id,student_name')->get()->sortBy('identitas.student_name');

                            $tpDescCount = $mataPelajaran->tpsDescs()->where([
                                'semester_id' => $semester->id,
                                'level_id' => $kelas->level_id
                            ])->count();

                            $percentages = $mataPelajaran->finalScorePercentages()->where([
                                'semester_id' => $semester->id,
                                'level_id' => $kelas->level_id
                            ])->first();

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
                                $item->nilai()->whereHas('nilaiAkhir',function($q)use($semester,$kelas,$mataPelajaran){
                                    $q->whereHas('rapor',function($q)use($semester,$kelas){
                                        $q->where([
                                            'semester_id' => $semester->id,
                                            'class_id' => $kelas->id
                                        ])->whereHas('siswa.riwayatKelas',function($q)use($semester,$kelas){
                                            $q->where([
                                                'semester_id' => $semester->id,
                                                'class_id' => $kelas->id
                                            ]);
                                        });
                                    })->where('subject_id',$mataPelajaran->id);
                                })->delete();

                                foreach($riwayatKelas as $r){
                                    $rapor = clone $raporQuery;
                                    $rapor = $rapor->where('student_id',$r->id)->first();
                                    if($rapor){
                                        $nilaiAkhir = $rapor->nilaiAkhirKurdeka()->where([
                                            'subject_id' => $mataPelajaran->id
                                        ])->first();
                                        if($nilaiAkhir){
                                            $nilaiAkhir->nas = $tpDescCount && ($tpDescCount-1) > 0 ? ($nilaiAkhir->nilaiSumatif()->sum('score')/($tpDescCount-1)) : 0;
                                            if($percentages){
                                                $nar = 0;
                                                $finalScores = ['naf','nas','ntss','nass'];
                                                foreach($finalScores as $f){
                                                    $attr = $f.'_percentage';
                                                    $nar += (($percentages->{$attr}/100)*$nilaiAkhir->{$f});
                                                }
                                                $nilaiAkhir->nar = $nar;
                                            }
                                            else $nilaiAkhir->nar = null;
                                            $nilaiAkhir->save();
                                        }
                                    }
                                }
                            }
                        }

                        $item->forceDelete();

                        $tpsExist = TpsDesc::where([
                            'semester_id' => $semester->id,
                            'level_id' => $tingkat->id,
                            'subject_id' => $mataPelajaran->id
                        ])->get();

                        $i = 1;
                        foreach($tpsExist as $t){
                            $t->code = 'TP-'.$i++;
                            $t->save(['timestamps' => false]);
                        }

                        Session::flash('success','Data '.$name.' berhasil dihapus');
                    }
                    else Session::flash('danger','Data gagal dihapus');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id, 'mataPelajaran' => $mataPelajaran->id]);
                }
                else{
                    Session::flash('danger', 'Mata pelajaran tidak ditemukan');

                    return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
                }
            }
            else{
                Session::flash('danger', 'Tingkat kelas tidak ditemukan');

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
