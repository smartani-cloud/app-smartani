<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kurikulum;
use App\Models\Kbm\TingkatKurikulum;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;

use Session;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'kurikulum';
        $this->modul = $modul;
        $this->active = 'Kurikulum';
        $this->route = $this->subsystem.'.penilaian.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;

        $tingkatList = $kurikulumList = $isReadOnly = null;
        $isReadOnly = true;

        $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();
        
        $semesterList = Semester::where(function($q)use($semesterActive){
            $q->whereHas('curricula.level',function($q){
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
                    $q->whereHas('curricula.level',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('curricula.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                if($semesterActive && ($tahun->academic_year_start >= $semesterActive->tahunAjaran->academic_year_start)){
                    $kurikulumList = Kurikulum::select('id','name')->get();
                    $isReadOnly = false;
                }

                $tingkatList = auth()->user()->pegawai->unit->levels()->get();
                foreach($tingkatList as $t){
                    $kurikulum[$t->id] = $t->curricula()->where([
                        'semester_id' => $semester->id
                    ])->first();

                    $data[$t->id] = $kurikulum[$t->id] ? ($isReadOnly ? $kurikulum[$t->id]->kurikulum->name : $kurikulum[$t->id]->curriculum_id) : null;
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

        return view($this->route.'-index', compact('active','route','semesterActive','tahun','semesterList','semester','tingkatList','kurikulumList','data','isReadOnly'));
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
                $q->whereHas('curricula.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                });
            })->when($semesterActive,function($q)use($semesterActive){
                $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q)use($semesterActive){
            $q->whereHas('curricula.level',function($q){
                $q->where('unit_id',auth()->user()->pegawai->unit_id);
            })->when($semesterActive,function($q)use($semesterActive){
                $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                    $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            });
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
        if($semester){
            $tingkatList = auth()->user()->pegawai->unit->levels()->get();
            $successCount = 0;
            foreach($tingkatList as $tingkat){
                $inputName = 'value-'.$tingkat->id;

                $kurikulumCount = Kurikulum::select('id')->where('id',$request->{$inputName})->count();

                $kurikulum = $tingkat->curricula()->where([
                    'semester_id' => $semester->id
                ])->first();

                if(!$kurikulum){
                    TingkatKurikulum::create([
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'curriculum_id' => $kurikulumCount ? $request->{$inputName} : 1
                    ]);

                    $successCount++;
                }
                else{
                    $kurikulum->curriculum_id = $kurikulumCount ? $request->{$inputName} : 1;
                    $kurikulum->save();
                    $kurikulum->fresh();

                    $successCount++;
                }

                if($successCount == count($tingkatList)){
                    Session::flash('success', 'Semua perubahan kurikulum berhasil disimpan');
                }
                elseif($successCount > 0 && ($successCount < count($tingkatList))){
                    Session::flash('success', 'Beberapa perubahan kurikulum berhasil disimpan');
                }
                else{
                    Session::flash('danger', 'Ups, sepertinya ada yang tidak beres');
                }
            }

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
=======
<?php

namespace App\Http\Controllers\Penilaian;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Kurikulum;
use App\Models\Kbm\TingkatKurikulum;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;

use Session;
use Illuminate\Http\Request;

class KurikulumController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'kurikulum';
        $this->modul = $modul;
        $this->active = 'Kurikulum';
        $this->route = $this->subsystem.'.penilaian.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;

        $tingkatList = $kurikulumList = $isReadOnly = null;
        $isReadOnly = true;

        $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();
        
        $semesterList = Semester::where(function($q)use($semesterActive){
            $q->whereHas('curricula.level',function($q){
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
                    $q->whereHas('curricula.level',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
        }

        if($tahun){
            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('curricula.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                if($semesterActive && ($tahun->academic_year_start >= $semesterActive->tahunAjaran->academic_year_start)){
                    $kurikulumList = Kurikulum::select('id','name')->get();
                    $isReadOnly = false;
                }

                $tingkatList = auth()->user()->pegawai->unit->levels()->get();
                foreach($tingkatList as $t){
                    $kurikulum[$t->id] = $t->curricula()->where([
                        'semester_id' => $semester->id
                    ])->first();

                    $data[$t->id] = $kurikulum[$t->id] ? ($isReadOnly ? $kurikulum[$t->id]->kurikulum->name : $kurikulum[$t->id]->curriculum_id) : null;
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

        return view($this->route.'-index', compact('active','route','semesterActive','tahun','semesterList','semester','tingkatList','kurikulumList','data','isReadOnly'));
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
                $q->whereHas('curricula.level',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                });
            })->when($semesterActive,function($q)use($semesterActive){
                $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
            });
        })->where('academic_year',$tahun)->first();
        if(!$tahun) return redirect()->route($this->route.'.index');

        $semester = Semester::where(function($q)use($semesterActive){
            $q->whereHas('curricula.level',function($q){
                $q->where('unit_id',auth()->user()->pegawai->unit_id);
            })->when($semesterActive,function($q)use($semesterActive){
                $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                    $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            });
        })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
        if($semester){
            $tingkatList = auth()->user()->pegawai->unit->levels()->get();
            $successCount = 0;
            foreach($tingkatList as $tingkat){
                $inputName = 'value-'.$tingkat->id;

                $kurikulumCount = Kurikulum::select('id')->where('id',$request->{$inputName})->count();

                $kurikulum = $tingkat->curricula()->where([
                    'semester_id' => $semester->id
                ])->first();

                if(!$kurikulum){
                    TingkatKurikulum::create([
                        'semester_id' => $semester->id,
                        'level_id' => $tingkat->id,
                        'curriculum_id' => $kurikulumCount ? $request->{$inputName} : 1
                    ]);

                    $successCount++;
                }
                else{
                    $kurikulum->curriculum_id = $kurikulumCount ? $request->{$inputName} : 1;
                    $kurikulum->save();
                    $kurikulum->fresh();

                    $successCount++;
                }

                if($successCount == count($tingkatList)){
                    Session::flash('success', 'Semua perubahan kurikulum berhasil disimpan');
                }
                elseif($successCount > 0 && ($successCount < count($tingkatList))){
                    Session::flash('success', 'Beberapa perubahan kurikulum berhasil disimpan');
                }
                else{
                    Session::flash('danger', 'Ups, sepertinya ada yang tidak beres');
                }
            }

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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
