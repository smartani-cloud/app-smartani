<?php

namespace App\Http\Controllers\Penilaian\Mapel;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kelas;
use App\Models\Kbm\MataPelajaran;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\Kurdeka\NilaiSumatif;
use App\Models\Penilaian\Kurdeka\TpsDesc;
use App\Models\Siswa\Siswa;

use Session;
use Illuminate\Http\Request;

class DeskripsiQuranController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'deskripsi-quran';
        $this->modul = $modul;
        $this->active = 'Deskripsi Hafalan Quran';
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

        $kategoriList = ['hadits','doa'];
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

        $data = $used = null;

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
                    ])->whereIn('curriculum_id',[1,2]);
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
                        ])->whereIn('curriculum_id',[1,2]);
                    })->first();

                    if($tingkat){
                        $unit = auth()->user()->pegawai->unit;

                        //$kelompok = $unit->kelompokMataPelajaran()->select('id')->where('group_subject_name', 'like', 'kelompok%')->get();

                        //$mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', '%Qur\'an%')->whereIn('group_subject_id', $kelompok->pluck('id'));
                        $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', '%Qur\'an%')->whereHas('skbmDetail',function($q)use($role,$tahun){
                            $q->when($role == 'guru',function($q){
                                return $q->where('employee_id',auth()->user()->pegawai->id);
                            })->whereHas('skbm',function($q)use($tahun){
                                $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                            });
                        });

                        $mapel = clone $mapelFiltered;
                        $mapel = $mapel;

                        if($mapel->count() > 0){
                            $mataPelajaranList = $mapel->orderBy('subject_number')->get();
                        }

                        if($mataPelajaran){
                            $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                            if($mataPelajaran){
                                // Inti Function
                                $data = $mataPelajaran->predicate()->select('id','description','rpd_type_id')->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $tingkat->id
                                ])->when($role == 'guru',function($q){
                                    return $q->where('employee_id', auth()->user()->pegawai->id);
                                })->where('rpd_type_id',6)->get();

                                foreach($data as $d){
                                    if($d->descriptions()->count() > 0) $used[$d->id] = 1;
                                    else $used[$d->id] = 0;
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
            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','tahun','kategoriList','semesterList','semester','tingkatList','tingkat','mataPelajaranList','mataPelajaran','data','used'));
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
                ])->whereIn('curriculum_id',[1,2]);
            })->where(['id' => $tingkat])->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', '%Qur\'an%')->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                $mataPelajaranList = null;

                $mapel = clone $mapelFiltered;
                $mapel = $mapel;

                if($mapel->count() > 0){
                    $mataPelajaranList = $mapel->orderBy('subject_number')->get();
                }

                $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $messages = [
                        'desc.required' => 'Mohon tuliskan deskripsi',
                    ];

                    $this->validate($request, [
                        'desc' => 'required',
                    ], $messages);
                    
                    $count = $mataPelajaran->predicate()->select('id','description')->where([
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'description' => $request->desc,
                        'employee_id' => auth()->user()->pegawai->id,
                        'rpd_type_id' => 6
                    ]);
                    
                    if($count->count() < 1){
                        $desc = new PredikatDeskripsi();
                        $desc->semester_id = $semester->id;
                        $desc->level_id = $tingkat->id;
                        $desc->subject_id = $mataPelajaran->id;
                        $desc->predicate = null;
                        $desc->description = $request->desc;
                        $desc->employee_id = auth()->user()->pegawai->id;
                        $desc->rpd_type_id = 6;
                        $desc->save();
                        $desc->fresh();

                        Session::flash('success','Data deskripsi '.$request->category.' berhasil ditambahkan');
                    }

                    else Session::flash('danger','Data deskripsi '.$request->category.' sudah pernah ditambahkan');

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
                ])->whereIn('curriculum_id',[1,2]);
            })->where(['id' => $tingkat])->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', '%Qur\'an%')->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                $mataPelajaranList = null;

                $mapel = clone $mapelFiltered;
                $mapel = $mapel;

                if($mapel->count() > 0){
                    $mataPelajaranList = $mapel->orderBy('subject_number')->get();
                }

                $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $item = $mataPelajaran->predicate()->select('id','description','rpd_type_id')->where([
                        'id' => $request->id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'employee_id' => auth()->user()->pegawai->id
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
                ])->whereIn('curriculum_id',[1,2]);
            })->where(['id' => $tingkat])->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', '%Qur\'an%')->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                $mataPelajaranList = null;

                $mapel = clone $mapelFiltered;
                $mapel = $mapel;

                if($mapel->count() > 0){
                    $mataPelajaranList = $mapel->orderBy('subject_number')->get();
                }

                $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $messages = [
                        'editDesc.required' => 'Mohon tuliskan deskripsi',
                    ];

                    $this->validate($request, [
                        'editDesc' => 'required',
                    ], $messages);

                    $item = $mataPelajaran->predicate()->where([
                        'id' => $request->id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'employee_id' => auth()->user()->pegawai->id
                    ])->first();
                    $count = $mataPelajaran->predicate()->where([
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'description' => $request->editDesc,
                        'employee_id' => auth()->user()->pegawai->id
                    ])->where('id','!=',$request->id)->count();

                    if($item && $count < 1){
                        $item->description = $request->editDesc;
                        $item->save();

                       Session::flash('success','Perubahan data berhasil disimpan');
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
                ])->whereIn('curriculum_id',[1,2]);
            })->where(['id' => $tingkat])->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;

                $mapelFiltered = MataPelajaran::select('id','subject_name')->where('subject_name', 'like', '%Qur\'an%')->whereHas('skbmDetail',function($q)use($role,$tahun){
                    $q->when($role == 'guru',function($q){
                        return $q->where('employee_id',auth()->user()->pegawai->id);
                    })->whereHas('skbm',function($q)use($tahun){
                        $q->where(['unit_id' => auth()->user()->pegawai->unit_id,'academic_year_id' => $tahun->id]);
                    });
                });

                $mataPelajaranList = null;

                $mapel = clone $mapelFiltered;
                $mapel = $mapel;

                if($mapel->count() > 0){
                    $mataPelajaranList = $mapel->orderBy('subject_number')->get();
                }

                $mataPelajaran = $mataPelajaranList && $mataPelajaranList->where('id',$mataPelajaran)->count() > 0 ? MataPelajaran::select('id','subject_name')->where('id',$mataPelajaran)->first() : null;

                if($mataPelajaran){
                    // Inti function
                    $item = $mataPelajaran->predicate()->where([
                        'id' => $request->id,
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'employee_id' => auth()->user()->pegawai->id
                    ])->first();

                    $used_count = $item ? $item->descriptions()->count() : 0;
                    if($item && $used_count < 1){
                        $desc = $item->description;
                        $item->delete();

                        Session::flash('success','Data '.$desc.' berhasil dihapus');
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
