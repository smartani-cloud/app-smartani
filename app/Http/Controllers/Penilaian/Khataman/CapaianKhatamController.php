<?php

namespace App\Http\Controllers\Penilaian\Khataman;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Kurdeka\KhatamType;
use App\Models\Penilaian\Kurdeka\LevelKhatamType;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class CapaianKhatamController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'capaian';
        $this->modul = $modul;
        $this->active = 'Capaian Khatam';
        $this->route = $this->subsystem.'.penilaian.khataman.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;

        $tingkatList = $typeList = $isReadOnly = null;
        $isReadOnly = true;

        $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();
        
        $semesterList = Semester::where(function($q)use($semesterActive){
            $q->whereHas('khatamTypes.level',function($q){
                $q->where('unit_id',auth()->user()->pegawai->unit_id);
            })->when($semesterActive,function($q)use($semesterActive){
                $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                    $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            });
        })->get();

        $data = null;

        if($tahun){
            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('khatamTypes.level',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('khatamTypes.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                if($semesterActive && ($tahun->academic_year_start >= $semesterActive->tahunAjaran->academic_year_start)){
                    $isReadOnly = false;
                }

                $typeList = KhatamType::select('id','name')->get();
                $tingkatList = auth()->user()->pegawai->unit->levels()->get();
                if($tingkatList && count($tingkatList) > 0){
                    foreach($tingkatList as $tingkat){
                        if($typeList && count($typeList) > 0){ 
                            foreach($typeList as $type){                   
                                $data[$tingkat->id][$type->id] = $tingkat->khatamTypes()->where([
                                    'semester_id' => $semester->id,
                                    'khatam_type_id' => $type->id
                                ])->count() > 0 ? true : false;
                            }
                        }
                    }                    
                }
            }
        }
        else{
            $semester = null;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','semesterActive','tahun','semesterList','semester','tingkatList','typeList','data','isReadOnly'));
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
    public function update(Request $request, $tahun, $semester)
    {
        $role = auth()->user()->role->name;

        $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

        $tahun = str_replace("-","/",$tahun);
        $tahun = TahunAjaran::where(function($q)use($semesterActive){
            $q->whereHas('semester',function($q)use($semesterActive){
                $q->whereHas('khatamTypes.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                });
            })->when($semesterActive,function($q)use($semesterActive){
                $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q)use($semesterActive){
            $q->whereHas('khatamTypes.level',function($q){
                $q->where('unit_id',auth()->user()->pegawai->unit_id);
            })->when($semesterActive,function($q)use($semesterActive){
                $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                    $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            });
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
        if($semester){
            $tingkatList = auth()->user()->pegawai->unit->levels()->get();
            foreach($tingkatList as $tingkat){
                $types = $tingkat->khatamTypes()->where([
                    'semester_id' => $semester->id
                ]);
                if($types->count() > 0) $types->delete();

                if(isset($request->typeCheck[$tingkat->id]) && count($request->typeCheck[$tingkat->id]) > 0){
                    foreach($request->typeCheck[$tingkat->id] as $item){
                        $typeInput = isset($item) ? KhatamType::find($item) : null;
                        if($typeInput){
                            LevelKhatamType::create([
                                'semester_id' => $semester->id,
                                'level_id' => $tingkat->id,
                                'khatam_type_id' => $typeInput->id
                            ]);
                        }
                    }
                }
            }

            Session::flash('success', 'Perubahan capaian khatam berhasil disimpan');

            return redirect()->route($this->route.'.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
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
