<?php

namespace App\Http\Controllers\Penilaian\Iklas;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Iklas\IndikatorIklas;
use App\Models\Penilaian\Iklas\IndikatorKurikulumIklas;

use Session;
use Illuminate\Http\Request;

class IndikatorKurikulumIklasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'indikator-kurikulum';
        $this->modul = $modul;
        $this->active = 'Indikator Kurikulum IKLaS';
        $this->route = $this->subsystem.'.penilaian.iklas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $tingkat = null)
    {
        $role = auth()->user()->role->name;
        
        $tingkatList = $competencies = $curriculumList = $indicatorList = $isReadOnly = $data = null;
        $isReadOnly = true;

        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q){
            $q->whereHas('semester',function($q){
                $q->aktif();
            })->whereIn('curriculum_id',[1,2]);
        })->first();

        $managementRoles = ['pembinayys','ketuayys','direktur','etl','etm'];

        if(!in_array($role,['kepsek','wakasek']) && !in_array($role,$managementRoles) && !$isWali){
            return redirect()->route($this->subsystem.'.index');
        }
        
        $semesterList = Semester::where(function($q){
            $q->where('is_active',1);
        })->get();

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
                $tingkatList = auth()->user()->pegawai->unit->levels()->whereHas('curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                    ])->whereIn('curriculum_id',[1,2]);
                });
                if($isWali){
                    $tingkatList = $tingkatList->whereHas('classes',function($q)use($tahun){
                        $q->where([
                            'academic_year_id' => $tahun->id,
                            'teacher_id' => auth()->user()->pegawai->id
                        ]);
                    });
                }
                $tingkatList = $tingkatList->get();

                if($tingkat){
                    $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                        ])->whereIn('curriculum_id',[1,2]);
                    });
                    if($isWali){
                        $tingkat = $tingkat->whereHas('classes',function($q)use($tahun){
                            $q->where([
                                'academic_year_id' => $tahun->id,
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        });
                    }
                    $tingkat = $tingkat->first();

                    if($tingkat){
                        $unit = auth()->user()->pegawai->unit;

                        $competencies = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();

                        if($competencies && count($competencies) > 0){
                            foreach($competencies as $c){
                                $indicators = IndikatorKurikulumIklas::where([
                                    'level_id' => $tingkat->id,
                                    'iklas_curriculum_id' => $c->id,
                                ])->whereHas('kurikulum',function($q)use($semester,$unit){
                                    $q->where([
                                        'semester_id' => $semester->id,
                                        'unit_id' => $unit->id,
                                    ]);
                                })->get();
                                $data[$c->id] = $indicators ? $indicators : null;
                            }
                        }

                        if($semester->is_active == 1){
                            $isReadOnly = false;
                            $curriculumList = $unit->kompetensiKategoriIklas()->where([
                                'semester_id' => $semester->id
                            ])->orderBy('sort_order')->get();
                            $indicatorList = $unit->indikatorIklas()->select('id','indicator')->whereDoesntHave('curricula',function($q)use($semester){
                                $q->where('semester_id', $semester->id);
                            })->get();
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

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','tingkatList','tingkat','competencies','curriculumList','indicatorList','data','isReadOnly'));
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
    public function store(Request $request, $tahun, $semester, $tingkat)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q){
            $q->whereHas('semester',function($q){
                $q->aktif();
            })->whereIn('curriculum_id',[1,2]);
        })->first();

        if(!$isWali) return redirect()->route($this->subsystem.'.index');

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->whereIn('curriculum_id',[1,2]);
            });
            if($isWali){
                $tingkat = $tingkat->whereHas('classes',function($q)use($tahun){
                    $q->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                });
            }
            $tingkat = $tingkat->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;
                
                // Inti function
                $messages = [
                    'competence.required' => 'Mohon pilih salah satu kompetensi IKLaS',
                    'name.required' => 'Mohon tuliskan indikator IKLaS',
                    'name.max' => 'Panjang indikator IKLaS maksimal 100 karakter'
                ];

                $this->validate($request, [
                    'competence' => 'required',
                    'name' => 'required|max:100'
                ], $messages);

                $competence = $unit->kompetensiKategoriIklas()->select('id')->where([
                    'id' => $request->competence,
                    'semester_id' => $semester->id
                ])->first();

                if($competence){
                    $isRelate = false;

                    $count = IndikatorIklas::where([
                        'unit_id' => $unit->id,
                        'indicator' => $request->name
                    ]);
                    
                    if($count->count() < 1){
                        $indicator = new IndikatorIklas();
                        $indicator->unit_id = $unit->id;
                        $indicator->indicator = $request->name;
                        $indicator->save();
                        $indicator->fresh();

                        $isRelate = true;
                    }
                    elseif($count->count() > 0){
                        $indicator = $count->first();

                        $indikatorKurikulum = $tingkat->indikatorKurikulumIklas()->where([
                            'semester_id' => $semester->id,
                            'iklas_curriculum_id'=> $competence->id,
                            'indicator_id' => $indicator->id
                        ])->count();

                        $relationCount = $tingkat->indikatorKurikulumIklas()->where([
                            'semester_id' => $semester->id,
                            'indicator_id' => $indicator->id
                        ])->count();

                        if($indikatorKurikulum < 1 && $relationCount < 1) $isRelate = true;
                        elseif($indikatorKurikulum >= 1) Session::flash('danger','Data indikator '.$request->name.' sudah pernah ditambahkan');
                        else Session::flash('danger','Data indikator '.$request->name.' tidak dapat ditambahkan');
                    }
                    else Session::flash('danger','Data indikator '.$request->name.' sudah pernah ditambahkan');

                    if($indicator && $isRelate){
                        $thisCompetencyIndicators = $tingkat->indikatorKurikulumIklas()->where([
                            'semester_id' => $semester->id,
                            'iklas_curriculum_id'=> $competence->id
                        ]);

                        $indikatorKurikulum = new IndikatorKurikulumIklas();
                        $indikatorKurikulum->semester_id = $semester->id;
                        $indikatorKurikulum->level_id = $tingkat->id;
                        $indikatorKurikulum->number = $thisCompetencyIndicators->count()+1;
                        $indikatorKurikulum->iklas_curriculum_id = $competence->id;
                        $indikatorKurikulum->indicator_id = $indicator->id;
                        $indikatorKurikulum->employee_id = auth()->user()->pegawai->id;
                        $indikatorKurikulum->save();

                        Session::flash('success','Data indikator '.$indicator->indicator.' berhasil ditambahkan');
                    }
                }
                else Session::flash('danger', 'Mohon pilih salah satu kompetensi IKLaS yang valid');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
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
     * Relate a created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function relate(Request $request, $tahun, $semester, $tingkat)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q){
            $q->whereHas('semester',function($q){
                $q->aktif();
            })->whereIn('curriculum_id',[1,2]);
        })->first();

        if(!$isWali) return redirect()->route($this->subsystem.'.index');

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->whereIn('curriculum_id',[1,2]);
            });
            if($isWali){
                $tingkat = $tingkat->whereHas('classes',function($q)use($tahun){
                    $q->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                });
            }
            $tingkat = $tingkat->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;
                
                // Inti function
                $messages = [
                    'competence.required' => 'Mohon pilih salah satu kompetensi IKLaS',
                    'indicator.required' => 'Mohon pilih salah satu indikator IKLaS'
                ];

                $this->validate($request, [
                    'competence' => 'required',
                    'indicator' => 'required'
                ], $messages);

                $competence = $unit->kompetensiKategoriIklas()->select('id')->where([
                    'id' => $request->competence,
                    'semester_id' => $semester->id
                ])->first();

                if($competence){
                    $indicator = IndikatorIklas::where([
                        'id' => $request->indicator,
                        'unit_id' => $unit->id
                    ])->first();
                    
                    if($indicator){
                        $indikatorKurikulum = $tingkat->indikatorKurikulumIklas()->where([
                            'semester_id' => $semester->id,
                            'iklas_curriculum_id'=> $competence->id,
                            'indicator_id' => $indicator->id
                        ])->count();

                        $relationCount = $tingkat->indikatorKurikulumIklas()->where([
                            'semester_id' => $semester->id,
                            'indicator_id' => $indicator->id
                        ])->count();

                        if($indikatorKurikulum < 1 && $relationCount < 1){
                            $thisCompetencyIndicators = $tingkat->indikatorKurikulumIklas()->where([
                                'semester_id' => $semester->id,
                                'iklas_curriculum_id'=> $competence->id
                            ]);

                            $indikatorKurikulum = new IndikatorKurikulumIklas();
                            $indikatorKurikulum->semester_id = $semester->id;
                            $indikatorKurikulum->level_id = $tingkat->id;
                            $indikatorKurikulum->number = $thisCompetencyIndicators->count()+1;
                            $indikatorKurikulum->iklas_curriculum_id = $competence->id;
                            $indikatorKurikulum->indicator_id = $indicator->id;
                            $indikatorKurikulum->employee_id = auth()->user()->pegawai->id;
                            $indikatorKurikulum->save();

                            Session::flash('success','Data indikator '.$indicator->indicator.' berhasil ditambahkan');
                        }
                        elseif($indikatorKurikulum >= 1) Session::flash('danger','Data indikator '.$indicator->indicator.' sudah pernah ditambahkan');
                        else Session::flash('danger','Data indikator '.$indicator->indicator.' tidak dapat ditambahkan');
                    }
                    else Session::flash('danger','Data indikator tidak ditemukan');
                }
                else Session::flash('danger', 'Mohon pilih salah satu kompetensi IKLaS yang valid');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
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
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $tahun, $semester, $tingkat, $id)
    {
        $role = auth()->user()->role->name;

        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q){
            $q->whereHas('semester',function($q){
                $q->aktif();
            })->whereIn('curriculum_id',[1,2]);
        })->first();

        if(!$isWali) return redirect()->route($this->subsystem.'.index');

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
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id,
                ])->whereIn('curriculum_id',[1,2]);
            });
            if($isWali){
                $tingkat = $tingkat->whereHas('classes',function($q)use($tahun){
                    $q->where([
                        'academic_year_id' => $tahun->id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                });
            }
            $tingkat = $tingkat->first();

            if($tingkat){
                $unit = auth()->user()->pegawai->unit;
                
                // Inti function
                $item = $tingkat->indikatorKurikulumIklas()->where([
                    'id' => $request->id,
                    'semester_id' => $semester->id,
                ])->first();

                //$used_count = $item ? $item->kurikulum()->count() : 0;
                $used_count = 0;
                if($item && $used_count < 1){
                    $name = $item->indicator->indicator;
                    $tingkat->indikatorKurikulumIklas()->where([
                        'semester_id' => $semester->id,
                        'iklas_curriculum_id' => $item->iklas_curriculum_id
                    ])->where('number','>',$item->number)->decrement('number');
                    $item->delete();

                    Session::flash('success','Data '.$name.' berhasil dihapus');
                }
                else Session::flash('danger','Data gagal dihapus');

                return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]);
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
