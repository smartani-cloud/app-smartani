<?php

namespace App\Http\Controllers\Penilaian\Tk;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Models\Penilaian\Tk\Objective;
use App\Models\Penilaian\Tk\ObjectiveElement;

use Session;
use Illuminate\Http\Request;

class DeskripsiPerkembanganController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'deskripsi-perkembangan';
        $this->modul = $modul;
        $this->active = 'Deskripsi Perkembangan';
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
        
        $semesterList = $tingkatList = $elementList = $objectiveList = $data = null;
        $editable = false;
        
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
                $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
                    $q->aktif();
                })->whereHas('level.curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id
                    ])->whereHas('kurikulum',function($q){
                        $q->where('name','Kurdeka');
                    });
                })->first();
                $tingkatList = auth()->user()->pegawai->unit->levels()->when($isWali,function($q)use($semester){
                    return $q->whereHas('classes',function($q)use($semester){
                        $q->where([
                            'academic_year_id' => $semester->academic_year_id,
                            'teacher_id' => auth()->user()->pegawai->id
                        ]);
                    });
                })->whereHas('curricula',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id,
                        'curriculum_id' => 2,
                    ]);
                })->get();
                if($tingkat){
                    $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->when($isWali,function($q)use($semester){
                        return $q->whereHas('classes',function($q)use($semester){
                            $q->where([
                                'academic_year_id' => $semester->academic_year_id,
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        });
                    })->whereHas('curricula',function($q)use($semester){
                        $q->where([
                            'semester_id' => $semester->id,
                            'curriculum_id' => 2,
                        ]);
                    })->first();

                    if($tingkat){
                        if($semester->is_active == 1 && $isWali) $editable = true;

                        $elementList = AspekPerkembangan::select('id','dev_aspect')->where('curriculum_id',2)->whereHas('objectives',function($q)use($semester){
                            $q->where('semester_id', $semester->id);
                        })->aktif()->orderBy('dev_aspect')->get();

                        if($elementList && count($elementList) > 0){
                            foreach($elementList as $element){
                                $descsQuery = PredikatDeskripsi::select('id','predicate','description')->where([
                                    'semester_id' => $semester->id,
                                    'level_id' => $tingkat->id,
                                    'subject_id' => $element->id,
                                    'rpd_type_id' => 15
                                ])->whereNotNull('description');

                                foreach(['max','min'] as $m){
                                    $data[$element->id][$m] = clone $descsQuery;
                                    $data[$element->id][$m] = $data[$element->id][$m]->where('predicate',$m)->first();
                                    $data[$element->id][$m] = $data[$element->id][$m] ? $data[$element->id][$m]->description : null;
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

        return view($this->route.'-index', compact('active','route','tahun','semesterList','semester','tingkatList','tingkat','elementList','objectiveList','data','editable'));
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
    public function update(Request $request, $tahun, $semester, $tingkat)
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
            $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
                $q->aktif();
            })->whereHas('level.curricula',function($q)use($semester){
                $q->where([
                    'semester_id' => $semester->id
                ])->whereHas('kurikulum',function($q){
                    $q->where('name','Kurdeka');
                });
            })->first();
            $tingkat = auth()->user()->pegawai->unit->levels()->when($isWali,function($q)use($semester){
                return $q->whereHas('classes',function($q)use($semester){
                    $q->where([
                        'academic_year_id' => $semester->academic_year_id,
                        'teacher_id' => auth()->user()->pegawai->id
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
                $elementList = AspekPerkembangan::select('id','dev_aspect')->where('curriculum_id',2)->whereHas('objectives',function($q)use($semester){
                    $q->where('semester_id', $semester->id);
                })->aktif()->orderBy('dev_aspect')->get();

                if($elementList && count($elementList) > 0){
                    foreach($elementList as $element){
                        $descsQuery = PredikatDeskripsi::select('id','predicate','description')->where([
                            'semester_id' => $semester->id,
                            'level_id' => $tingkat->id,
                            'subject_id' => $element->id,
                            'rpd_type_id' => 15
                        ]);

                        foreach(['max','min'] as $m){
                            $attr = $m.'Desc';
                            $desc = isset($request->{$attr}[$element->id]) ? $request->{$attr}[$element->id] : null;

                            $descs[$m] = clone $descsQuery;
                            $descs[$m] = $descs[$m]->where('predicate',$m)->first();

                            if(!$descs[$m] && $desc){
                                $descs[$m] = new PredikatDeskripsi();
                                $descs[$m]->semester_id = $semester->id;
                                $descs[$m]->level_id = $tingkat->id;
                                $descs[$m]->subject_id = $element->id;
                                $descs[$m]->predicate = $m;
                                $descs[$m]->description = null;
                                $descs[$m]->employee_id = auth()->user()->pegawai->id;
                                $descs[$m]->rpd_type_id = 15;
                                $descs[$m]->save();
                                $descs[$m]->fresh();
                            }
                            if($descs[$m]){
                                $descs[$m]->description = $desc;
                                $descs[$m]->save();
                            }
                        }

                        Session::flash('success', 'Perubahan deskripsi capaian pembelajaran berhasil disimpan');
                    }
                }
                else Session::flash('danger', 'Elemen capaian pembelajaran tidak ditemukan');

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