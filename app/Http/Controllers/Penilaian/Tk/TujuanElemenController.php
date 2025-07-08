<?php

namespace App\Http\Controllers\Penilaian\Tk;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\Tk\Objective;
use App\Models\Penilaian\Tk\ObjectiveElement;

use Session;
use Illuminate\Http\Request;

class TujuanElemenController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'tujuan-elemen';
        $this->modul = $modul;
        $this->active = 'Tujuan Elemen Capaian Pembelajaran';
        $this->route = $this->subsystem.'.penilaian.tk.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null, $tingkat = null)
    {
        $role = auth()->user()->role->name;
        
        $semesterList = $tingkatList = $elementList = $objectiveList = $isReadOnly = $data = null;
        $isReadOnly = true;
        
        $semesterList = Semester::where(function($q){
            $q->whereHas('objectiveElements.level',function($q){
                $q->where('unit_id',auth()->user()->pegawai->unit_id);
            })->orWhere('is_active',1);
        })->get();

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q){
                $q->whereHas('semester',function($q){
                    $q->whereHas('objectiveElements.level',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    })->orWhere('is_active',1);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q){
                $q->whereHas('objectiveElements.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->orWhere('is_active',1);
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
                        if($semester->is_active == 1){
                            $isReadOnly = false;
                            $elementList = AspekPerkembangan::select('id','dev_aspect')->where('curriculum_id',2)->aktif()->orderBy('dev_aspect')->get();
                            $objectiveList = $tingkat->objectives()->select('id','desc')->whereDoesntHave('elements',function($q)use($semester){
                                $q->where('semester_id', $semester->id);
                            })->get();
                        }

                        $data = $tingkat->objectiveElements()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();
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

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','tingkatList','tingkat','elementList','objectiveList','data','isReadOnly'));
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
        $role = $request->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where(function($q){
            $q->whereHas('semester',function($q){
                $q->whereHas('objectiveElements.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->orWhere('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q){
            $q->whereHas('objectiveElements.level',function($q){
                $q->where('unit_id',auth()->user()->pegawai->unit_id);
            })->orWhere('is_active',1);
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
                // Inti function
                $messages = [
                    'element.required' => 'Mohon pilih salah satu elemen capaian pembelajaran',
                    'desc.required' => 'Mohon tuliskan tujuan pembelajaran',
                    'desc.max' => 'Panjang tujuan pembelajaran maksimal 150 karakter'
                ];

                $this->validate($request, [
                    'element' => 'required',
                    'desc' => 'required|max:150'
                ], $messages);

                $element = AspekPerkembangan::select('id')->where('id',$request->element)->first();

                if($element){
                    $isRelate = false;

                    $count = Objective::where([
                        'level_id' => $tingkat->id,
                        'desc' => $request->desc
                    ]);
                    
                    if($count->count() < 1){
                        $objective = new Objective();
                        $objective->level_id = $tingkat->id;
                        $objective->employee_id = auth()->user()->pegawai->id;
                        $objective->desc = $request->desc;
                        $objective->save();
                        $objective->fresh();

                        $isRelate = true;
                    }
                    elseif($count->count() > 0){
                        $objective = $count->first();

                        $tujuanElemen = $tingkat->objectiveElements()->where([
                            'semester_id' => $semester->id,
                            'element_id'=> $element->id,
                            'objective_id' => $objective->id
                        ])->count();

                        $relationCount = $tingkat->objectiveElements()->where([
                            'semester_id' => $semester->id,
                            'objective_id' => $objective->id
                        ])->count();

                        if($tujuanElemen < 1 && $relationCount < 1) $isRelate = true;
                        elseif($tujuanElemen >= 1) Session::flash('danger','Data tujuan pembelajaran '.$request->desc.' sudah pernah ditambahkan');
                        else Session::flash('danger','Data tujuan pembelajaran '.$request->desc.' tidak dapat ditambahkan');
                    }
                    else Session::flash('danger','Data tujuan pembelajaran '.$request->desc.' sudah pernah ditambahkan');

                    if($objective && $isRelate){
                        $thisElementObjectives = $tingkat->objectiveElements()->where([
                            'semester_id' => $semester->id,
                            'element_id'=> $element->id
                        ]);

                        $tujuanElemen = new ObjectiveElement();
                        $tujuanElemen->semester_id = $semester->id;
                        $tujuanElemen->level_id = $tingkat->id;
                        $tujuanElemen->sort_order = $tingkat->objectiveElements()->count();
                        $tujuanElemen->number = $thisElementObjectives->count()+1;
                        $tujuanElemen->element_id = $element->id;
                        $tujuanElemen->objective_id = $objective->id;
                        $tujuanElemen->employee_id = auth()->user()->pegawai->id;
                        $tujuanElemen->save();

                        // Sorting
                        $objectiveElementList = $tingkat->objectiveElements()->where(['semester_id' => $semester->id])->get();
                        if($objectiveElementList && count($objectiveElementList) > 0){
                            $i = 1;
                            $elementGroup = $tingkat->objectiveElements()->select('element_id')->where(['semester_id' => $semester->id])->get()->pluck('element_id')->unique();
                            foreach($elementGroup as $group){
                                $element = AspekPerkembangan::select('id')->where('id',$group)->has('objectives')->first();
                                if($element){
                                    foreach($element->objectives()->orderBy('number')->get() as $o){
                                        $o->sort_order = $i++;
                                        $o->save();
                                    }
                                }
                            }
                        }

                        Session::flash('success','Data kompetensi '.$objective->desc.' berhasil ditambahkan');
                    }
                }
                else Session::flash('danger', 'Mohon pilih salah satu elemen capaian pembelajaran yang valid');

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
        $role = $request->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where(function($q){
            $q->whereHas('semester',function($q){
                $q->whereHas('objectiveElements.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->orWhere('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q){
            $q->whereHas('objectiveElements.level',function($q){
                $q->where('unit_id',auth()->user()->pegawai->unit_id);
            })->orWhere('is_active',1);
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
                // Inti function
                $messages = [
                    'element.required' => 'Mohon pilih salah satu elemen capaian pembelajaran',
                    'objective.required' => 'Mohon pilih salah satu tujuan pembelajaran'
                ];

                $this->validate($request, [
                    'element' => 'required',
                    'objective' => 'required'
                ], $messages);

                $element = AspekPerkembangan::select('id')->where('id',$request->element)->first();

                if($element){
                    $objective = Objective::where([
                        'id' => $request->objective,
                        'level_id' => $tingkat->id,
                    ])->first();
                    
                    if($objective){
                        $tujuanElemen = $tingkat->objectiveElements()->where([
                            'semester_id' => $semester->id,
                            'element_id'=> $element->id,
                            'objective_id' => $objective->id
                        ])->count();

                        $relationCount = $tingkat->objectiveElements()->where([
                            'semester_id' => $semester->id,
                            'objective_id' => $objective->id
                        ])->count();

                        if($tujuanElemen < 1 && $relationCount < 1){
                            $thisElementObjectives = $tingkat->objectiveElements()->where([
                                'semester_id' => $semester->id,
                                'element_id'=> $element->id
                            ]);

                            $tujuanElemen = new ObjectiveElement();
                            $tujuanElemen->semester_id = $semester->id;
                            $tujuanElemen->level_id = $tingkat->id;
                            $tujuanElemen->sort_order = $tingkat->objectiveElements()->count();
                            $tujuanElemen->number = $thisElementObjectives->count()+1;
                            $tujuanElemen->element_id = $element->id;
                            $tujuanElemen->objective_id = $objective->id;
                            $tujuanElemen->employee_id = auth()->user()->pegawai->id;
                            $tujuanElemen->save();

                            // Sorting
                            $objectiveElementList = $tingkat->objectiveElements()->where(['semester_id' => $semester->id])->get();
                            if($objectiveElementList && count($objectiveElementList) > 0){
                                $i = 1;
                                $elementGroup = $tingkat->objectiveElements()->select('element_id')->where(['semester_id' => $semester->id])->get()->pluck('element_id')->unique();
                                foreach($elementGroup as $group){
                                    $element = AspekPerkembangan::select('id')->where('id',$group)->has('objectives')->first();
                                    if($element){
                                        foreach($element->objectives()->orderBy('number')->get() as $o){
                                            $o->sort_order = $i++;
                                            $o->save();
                                        }
                                    }
                                }
                            }

                            Session::flash('success','Data tujuan pembelajaran '.$objective->desc.' berhasil ditambahkan');
                        }
                        elseif($tujuanElemen >= 1) Session::flash('danger','Data tujuan pembelajaran '.$objective->desc.' sudah pernah ditambahkan');
                        else Session::flash('danger','Data tujuan pembelajaran '.$objective->desc.' tidak dapat ditambahkan');
                    }
                    else Session::flash('danger','Data tujuan pembelajaran tidak ditemukan');
                }
                else Session::flash('danger', 'Mohon pilih salah satu elemen capaian pembelajaran yang valid');

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
        $role = $request->user()->role->name;

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where(function($q){
            $q->whereHas('semester',function($q){
                $q->whereHas('objectiveElements.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->orWhere('is_active',1);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q){
            $q->whereHas('objectiveElements.level',function($q){
                $q->where('unit_id',auth()->user()->pegawai->unit_id);
            })->orWhere('is_active',1);
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
                // Inti function
                $item = $tingkat->objectiveElements()->where([
                    'id' => $request->id,
                    'semester_id' => $semester->id,
                ])->has('objective')->first();

                //$used_count = $item ? $item->objectiveElements()->count() : 0;
                $used_count = 0;
                if($item && $used_count < 1){
                    $desc = $item->objective->desc;
                    $tingkat->objectiveElements()->where([
                        'semester_id' => $semester->id,
                        'element_id' => $item->element_id
                    ])->where('number','>',$item->number)->decrement('number');
                    $item->delete();

                    // Sorting
                    $objectiveElementList = $tingkat->objectiveElements()->where(['semester_id' => $semester->id])->get();
                    if($objectiveElementList && count($objectiveElementList) > 0){
                        $i = 1;
                        $elementGroup = $tingkat->objectiveElements()->select('element_id')->where(['semester_id' => $semester->id])->get()->pluck('element_id')->unique();
                        foreach($elementGroup as $group){
                            $element = AspekPerkembangan::select('id')->where('id',$group)->has('objectives')->first();
                            if($element){
                                foreach($element->objectives()->orderBy('number')->get() as $o){
                                    $o->sort_order = $i++;
                                    $o->save();
                                }
                            }
                        }
                    }

                    Session::flash('success','Data '.$desc.' berhasil dihapus');
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
