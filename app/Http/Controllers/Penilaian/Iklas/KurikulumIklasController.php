<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Iklas;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Iklas\KategoriIklas;
use App\Models\Penilaian\Iklas\KompetensiIklas;
use App\Models\Penilaian\Iklas\KompetensiKategoriIklas;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class KurikulumIklasController extends Controller
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
        $this->active = 'Kurikulum IKLaS';
        $this->route = $this->subsystem.'.penilaian.iklas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $unit = null, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;
        
        $semesterList = $categoryList = $competenceList = $isReadOnly = $data = null;
        $isReadOnly = true;        

        $unitList = Unit::select('id','name')->sekolah();

        if($role == 'kepsek'){
            $myUnit = $request->user()->pegawai->unit->name;
            if($unit && $unit != $myUnit){
                $unit = null;
            }
            else $unit = $myUnit;
            $unitList = $unitList->where('name',$myUnit);
        }
        $unitList = $unitList->get();

        if($unit){
            $unit = Unit::sekolah()->where('name',$unit)->first();
            
            if($unit){
                $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();
        
                $semesterList = Semester::where(function($q)use($semesterActive){
                    $q->whereHas('kompetensiKategoriIklas',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    })->when($semesterActive,function($q)use($semesterActive){
                        $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                            $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                        });
                    });
                })->get();

                if($tahun){
                    $tahun = str_replace("-","/",$tahun);
                    $tahun = TahunAjaran::where(function($q)use($semesterActive){
                        $q->whereHas('semester',function($q)use($semesterActive){
                            $q->whereHas('kompetensiKategoriIklas',function($q){
                                $q->where('unit_id',auth()->user()->pegawai->unit_id);
                            });
                        })->when($semesterActive,function($q)use($semesterActive){
                            $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                        });
                    })->where('academic_year',$tahun)->first();
                }

                if($tahun){
                    $semester = Semester::where(function($q)use($semesterActive){
                        $q->whereHas('kompetensiKategoriIklas',function($q){
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
                            $categoryList = KategoriIklas::select('id','name')->get();
                            $competenceList = $unit->kompetensiIklas()->select('id','name')->whereDoesntHave('categories',function($q)use($semester){
                                $q->where('semester_id', $semester->id);
                            })->get();
                        }

                        $data = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();
                    }
                }
                else{
                    $semester = null;
                }
            }
            else{
                if($role == 'kepsek'){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route($this->route.'.index',['unit' => $unit->name]);
                }
                else return redirect()->route($this->route.'.index');
            }
        }
        else{
            if($role == 'kepsek'){
                $unit = $request->user()->pegawai->unit;
                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','unitList','unit','tahun','semesterList','semester','categoryList','competenceList','data','isReadOnly'));
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
    public function store(Request $request, $unit, $tahun, $semester)
    {
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('kompetensiKategoriIklas',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('kompetensiKategoriIklas',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                // Inti function
                $messages = [
                    'category.required' => 'Mohon pilih salah satu kategori IKLaS',
                    'name.required' => 'Mohon tuliskan kompetensi IKLaS',
                    'name.max' => 'Panjang kompetensi IKLaS maksimal 50 karakter'
                ];

                $this->validate($request, [
                    'category' => 'required',
                    'name' => 'required|max:50'
                ], $messages);

                $category = KategoriIklas::select('id')->where('id',$request->category)->first();

                if($category){
                    $isRelate = false;

                    $count = KompetensiIklas::where([
                        'unit_id' => $unit->id,
                        'name' => $request->name
                    ]);
                    
                    if($count->count() < 1){
                        $competence = new KompetensiIklas();
                        $competence->unit_id = $unit->id;
                        $competence->name = $request->name;
                        $competence->save();
                        $competence->fresh();

                        $isRelate = true;
                    }
                    elseif($count->count() > 0){
                        $competence = $count->first();

                        $kurikulum = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'category_id'=> $category->id,
                            'competence_id' => $competence->id
                        ])->count();

                        $relationCount = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'competence_id' => $competence->id
                        ])->count();

                        if($kurikulum < 1 && $relationCount < 1) $isRelate = true;
                        elseif($kurikulum >= 1) Session::flash('danger','Data kompetensi '.$request->name.' sudah pernah ditambahkan');
                        else Session::flash('danger','Data kompetensi '.$request->name.' tidak dapat ditambahkan');
                    }
                    else Session::flash('danger','Data kompetensi '.$request->name.' sudah pernah ditambahkan');

                    if($competence && $isRelate){
                        $thisCategoryCompetencies = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'category_id'=> $category->id
                        ]);

                        $kurikulum = new KompetensiKategoriIklas();
                        $kurikulum->semester_id = $semester->id;
                        $kurikulum->unit_id = $unit->id;
                        $kurikulum->sort_order = $unit->kompetensiKategoriIklas()->count();
                        $kurikulum->number = $thisCategoryCompetencies->count()+1;
                        $kurikulum->category_id = $category->id;
                        $kurikulum->competence_id = $competence->id;
                        $kurikulum->employee_id = auth()->user()->pegawai->id;
                        $kurikulum->save();

                        $competenceList = $unit->kompetensiKategoriIklas()->where(['semester_id' => $semester->id])->get();
                        if($competenceList && count($competenceList) > 0){
                            $categories = KategoriIklas::select('id')->whereHas('competencies',function($q)use($unit,$semester){
                                $q->where([
                                    'unit_id' => $unit->id,
                                    'semester_id' => $semester->id
                                ]);
                            })->orderBy('number')->get();
                            $i = 1;
                            foreach($categories as $category){
                                $categoryCompetencies = $category->competencies()->where([
                                    'unit_id' => $unit->id,
                                    'semester_id' => $semester->id
                                ])->orderBy('number')->get();
                                foreach($categoryCompetencies as $c){
                                    $c->sort_order = $i++;
                                    $c->save();
                                }
                            }
                        }

                        Session::flash('success','Data kompetensi '.$competence->name.' berhasil ditambahkan');
                    }
                }
                else Session::flash('danger', 'Mohon pilih salah satu kategori IKLaS yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
            else{
                Session::flash('danger', 'Pilih tahun pelajaran yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Relate a created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function relate(Request $request, $unit, $tahun, $semester)
    {
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('kompetensiKategoriIklas',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('kompetensiKategoriIklas',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                // Inti function
                $messages = [
                    'category.required' => 'Mohon pilih salah satu kategori IKLaS',
                    'competence.required' => 'Mohon pilih salah satu kompetensi IKLaS'
                ];

                $this->validate($request, [
                    'category' => 'required',
                    'competence' => 'required'
                ], $messages);

                $category = KategoriIklas::select('id')->where('id',$request->category)->first();

                if($category){
                    $competence = KompetensiIklas::where([
                        'id' => $request->competence,
                        'unit_id' => $unit->id
                    ])->first();
                    
                    if($competence){
                        $kurikulum = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'category_id'=> $category->id,
                            'competence_id' => $competence->id
                        ])->count();

                        $relationCount = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'competence_id' => $competence->id
                        ])->count();

                        if($kurikulum < 1 && $relationCount < 1){
                            $thisCategoryCompetencies = $unit->kompetensiKategoriIklas()->where([
                                'semester_id' => $semester->id,
                                'category_id'=> $category->id
                            ]);

                            $kurikulum = new KompetensiKategoriIklas();
                            $kurikulum->semester_id = $semester->id;
                            $kurikulum->unit_id = $unit->id;
                            $kurikulum->sort_order = $unit->kompetensiKategoriIklas()->count();
                            $kurikulum->number = $thisCategoryCompetencies->count()+1;
                            $kurikulum->category_id = $category->id;
                            $kurikulum->competence_id = $competence->id;
                            $kurikulum->employee_id = auth()->user()->pegawai->id;
                            $kurikulum->save();

                            $competenceList = $unit->kompetensiKategoriIklas()->where(['semester_id' => $semester->id])->get();
                            if($competenceList && count($competenceList) > 0){
                                $categories = KategoriIklas::select('id')->whereHas('competencies',function($q)use($unit,$semester){
                                    $q->where([
                                        'unit_id' => $unit->id,
                                        'semester_id' => $semester->id
                                    ]);
                                })->orderBy('number')->get();
                                $i = 1;
                                foreach($categories as $category){
                                    $categoryCompetencies = $category->competencies()->where([
                                        'unit_id' => $unit->id,
                                        'semester_id' => $semester->id
                                    ])->orderBy('number')->get();
                                    foreach($categoryCompetencies as $c){
                                        $c->sort_order = $i++;
                                        $c->save();
                                    }
                                }
                            }

                            Session::flash('success','Data kompetensi '.$competence->name.' berhasil ditambahkan');
                        }
                        elseif($kurikulum >= 1) Session::flash('danger','Data kompetensi '.$competence->name.' sudah pernah ditambahkan');
                        else Session::flash('danger','Data kompetensi '.$competence->name.' tidak dapat ditambahkan');
                    }
                    else Session::flash('danger','Data kompetensi tidak ditemukan');
                }
                else Session::flash('danger', 'Mohon pilih salah satu kategori IKLaS yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
            else{
                Session::flash('danger', 'Pilih tahun pelajaran yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

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
    public function destroy(Request $request, $unit, $tahun, $semester, $id)
    {
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('kompetensiKategoriIklas',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('kompetensiKategoriIklas',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                // Inti function
                $item = $unit->kompetensiKategoriIklas()->where([
                    'id' => $request->id,
                    'semester_id' => $semester->id,
                ])->first();

                //$used_count = $item ? $item->categories()->count() : 0;
                $used_count = 0;
                if($item && $used_count < 1){
                    $name = $item->competence->name;
                    $unit->kompetensiKategoriIklas()->where([
                        'semester_id' => $semester->id,
                        'category_id' => $item->category_id
                    ])->where('number','>',$item->number)->decrement('number');
                    $item->delete();

                    $competenceList = $unit->kompetensiKategoriIklas()->where(['semester_id' => $semester->id])->get();
                    if($competenceList && count($competenceList) > 0){
                        $categories = KategoriIklas::select('id')->whereHas('competencies',function($q)use($unit,$semester){
                            $q->where([
                                'unit_id' => $unit->id,
                                'semester_id' => $semester->id
                            ]);
                        })->orderBy('number')->get();
                        $i = 1;
                        foreach($categories as $category){
                            $categoryCompetencies = $category->competencies()->where([
                                'unit_id' => $unit->id,
                                'semester_id' => $semester->id
                            ])->orderBy('number')->get();
                            foreach($categoryCompetencies as $c){
                                $c->sort_order = $i++;
                                $c->save();
                            }
                        }
                    }

                    Session::flash('success','Data '.$name.' berhasil dihapus');
                }
                else Session::flash('danger','Data gagal dihapus');

                return redirect()->route($this->route.'.index',['unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
            else{
                Session::flash('danger', 'Pilih tahun pelajaran yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian\Iklas;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Iklas\KategoriIklas;
use App\Models\Penilaian\Iklas\KompetensiIklas;
use App\Models\Penilaian\Iklas\KompetensiKategoriIklas;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class KurikulumIklasController extends Controller
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
        $this->active = 'Kurikulum IKLaS';
        $this->route = $this->subsystem.'.penilaian.iklas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $unit = null, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;
        
        $semesterList = $categoryList = $competenceList = $isReadOnly = $data = null;
        $isReadOnly = true;        

        $unitList = Unit::select('id','name')->sekolah();

        if($role == 'kepsek'){
            $myUnit = $request->user()->pegawai->unit->name;
            if($unit && $unit != $myUnit){
                $unit = null;
            }
            else $unit = $myUnit;
            $unitList = $unitList->where('name',$myUnit);
        }
        $unitList = $unitList->get();

        if($unit){
            $unit = Unit::sekolah()->where('name',$unit)->first();
            
            if($unit){
                $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();
        
                $semesterList = Semester::where(function($q)use($semesterActive){
                    $q->whereHas('kompetensiKategoriIklas',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    })->when($semesterActive,function($q)use($semesterActive){
                        $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                            $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                        });
                    });
                })->get();

                if($tahun){
                    $tahun = str_replace("-","/",$tahun);
                    $tahun = TahunAjaran::where(function($q)use($semesterActive){
                        $q->whereHas('semester',function($q)use($semesterActive){
                            $q->whereHas('kompetensiKategoriIklas',function($q){
                                $q->where('unit_id',auth()->user()->pegawai->unit_id);
                            });
                        })->when($semesterActive,function($q)use($semesterActive){
                            $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                        });
                    })->where('academic_year',$tahun)->first();
                }

                if($tahun){
                    $semester = Semester::where(function($q)use($semesterActive){
                        $q->whereHas('kompetensiKategoriIklas',function($q){
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
                            $categoryList = KategoriIklas::select('id','name')->get();
                            $competenceList = $unit->kompetensiIklas()->select('id','name')->whereDoesntHave('categories',function($q)use($semester){
                                $q->where('semester_id', $semester->id);
                            })->get();
                        }

                        $data = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id
                        ])->orderBy('sort_order')->get();
                    }
                }
                else{
                    $semester = null;
                }
            }
            else{
                if($role == 'kepsek'){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route($this->route.'.index',['unit' => $unit->name]);
                }
                else return redirect()->route($this->route.'.index');
            }
        }
        else{
            if($role == 'kepsek'){
                $unit = $request->user()->pegawai->unit;
                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','unitList','unit','tahun','semesterList','semester','categoryList','competenceList','data','isReadOnly'));
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
    public function store(Request $request, $unit, $tahun, $semester)
    {
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('kompetensiKategoriIklas',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('kompetensiKategoriIklas',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                // Inti function
                $messages = [
                    'category.required' => 'Mohon pilih salah satu kategori IKLaS',
                    'name.required' => 'Mohon tuliskan kompetensi IKLaS',
                    'name.max' => 'Panjang kompetensi IKLaS maksimal 50 karakter'
                ];

                $this->validate($request, [
                    'category' => 'required',
                    'name' => 'required|max:50'
                ], $messages);

                $category = KategoriIklas::select('id')->where('id',$request->category)->first();

                if($category){
                    $isRelate = false;

                    $count = KompetensiIklas::where([
                        'unit_id' => $unit->id,
                        'name' => $request->name
                    ]);
                    
                    if($count->count() < 1){
                        $competence = new KompetensiIklas();
                        $competence->unit_id = $unit->id;
                        $competence->name = $request->name;
                        $competence->save();
                        $competence->fresh();

                        $isRelate = true;
                    }
                    elseif($count->count() > 0){
                        $competence = $count->first();

                        $kurikulum = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'category_id'=> $category->id,
                            'competence_id' => $competence->id
                        ])->count();

                        $relationCount = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'competence_id' => $competence->id
                        ])->count();

                        if($kurikulum < 1 && $relationCount < 1) $isRelate = true;
                        elseif($kurikulum >= 1) Session::flash('danger','Data kompetensi '.$request->name.' sudah pernah ditambahkan');
                        else Session::flash('danger','Data kompetensi '.$request->name.' tidak dapat ditambahkan');
                    }
                    else Session::flash('danger','Data kompetensi '.$request->name.' sudah pernah ditambahkan');

                    if($competence && $isRelate){
                        $thisCategoryCompetencies = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'category_id'=> $category->id
                        ]);

                        $kurikulum = new KompetensiKategoriIklas();
                        $kurikulum->semester_id = $semester->id;
                        $kurikulum->unit_id = $unit->id;
                        $kurikulum->sort_order = $unit->kompetensiKategoriIklas()->count();
                        $kurikulum->number = $thisCategoryCompetencies->count()+1;
                        $kurikulum->category_id = $category->id;
                        $kurikulum->competence_id = $competence->id;
                        $kurikulum->employee_id = auth()->user()->pegawai->id;
                        $kurikulum->save();

                        $competenceList = $unit->kompetensiKategoriIklas()->where(['semester_id' => $semester->id])->get();
                        if($competenceList && count($competenceList) > 0){
                            $categories = KategoriIklas::select('id')->whereHas('competencies',function($q)use($unit,$semester){
                                $q->where([
                                    'unit_id' => $unit->id,
                                    'semester_id' => $semester->id
                                ]);
                            })->orderBy('number')->get();
                            $i = 1;
                            foreach($categories as $category){
                                $categoryCompetencies = $category->competencies()->where([
                                    'unit_id' => $unit->id,
                                    'semester_id' => $semester->id
                                ])->orderBy('number')->get();
                                foreach($categoryCompetencies as $c){
                                    $c->sort_order = $i++;
                                    $c->save();
                                }
                            }
                        }

                        Session::flash('success','Data kompetensi '.$competence->name.' berhasil ditambahkan');
                    }
                }
                else Session::flash('danger', 'Mohon pilih salah satu kategori IKLaS yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
            else{
                Session::flash('danger', 'Pilih tahun pelajaran yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Relate a created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function relate(Request $request, $unit, $tahun, $semester)
    {
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('kompetensiKategoriIklas',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('kompetensiKategoriIklas',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                // Inti function
                $messages = [
                    'category.required' => 'Mohon pilih salah satu kategori IKLaS',
                    'competence.required' => 'Mohon pilih salah satu kompetensi IKLaS'
                ];

                $this->validate($request, [
                    'category' => 'required',
                    'competence' => 'required'
                ], $messages);

                $category = KategoriIklas::select('id')->where('id',$request->category)->first();

                if($category){
                    $competence = KompetensiIklas::where([
                        'id' => $request->competence,
                        'unit_id' => $unit->id
                    ])->first();
                    
                    if($competence){
                        $kurikulum = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'category_id'=> $category->id,
                            'competence_id' => $competence->id
                        ])->count();

                        $relationCount = $unit->kompetensiKategoriIklas()->where([
                            'semester_id' => $semester->id,
                            'competence_id' => $competence->id
                        ])->count();

                        if($kurikulum < 1 && $relationCount < 1){
                            $thisCategoryCompetencies = $unit->kompetensiKategoriIklas()->where([
                                'semester_id' => $semester->id,
                                'category_id'=> $category->id
                            ]);

                            $kurikulum = new KompetensiKategoriIklas();
                            $kurikulum->semester_id = $semester->id;
                            $kurikulum->unit_id = $unit->id;
                            $kurikulum->sort_order = $unit->kompetensiKategoriIklas()->count();
                            $kurikulum->number = $thisCategoryCompetencies->count()+1;
                            $kurikulum->category_id = $category->id;
                            $kurikulum->competence_id = $competence->id;
                            $kurikulum->employee_id = auth()->user()->pegawai->id;
                            $kurikulum->save();

                            $competenceList = $unit->kompetensiKategoriIklas()->where(['semester_id' => $semester->id])->get();
                            if($competenceList && count($competenceList) > 0){
                                $categories = KategoriIklas::select('id')->whereHas('competencies',function($q)use($unit,$semester){
                                    $q->where([
                                        'unit_id' => $unit->id,
                                        'semester_id' => $semester->id
                                    ]);
                                })->orderBy('number')->get();
                                $i = 1;
                                foreach($categories as $category){
                                    $categoryCompetencies = $category->competencies()->where([
                                        'unit_id' => $unit->id,
                                        'semester_id' => $semester->id
                                    ])->orderBy('number')->get();
                                    foreach($categoryCompetencies as $c){
                                        $c->sort_order = $i++;
                                        $c->save();
                                    }
                                }
                            }

                            Session::flash('success','Data kompetensi '.$competence->name.' berhasil ditambahkan');
                        }
                        elseif($kurikulum >= 1) Session::flash('danger','Data kompetensi '.$competence->name.' sudah pernah ditambahkan');
                        else Session::flash('danger','Data kompetensi '.$competence->name.' tidak dapat ditambahkan');
                    }
                    else Session::flash('danger','Data kompetensi tidak ditemukan');
                }
                else Session::flash('danger', 'Mohon pilih salah satu kategori IKLaS yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
            else{
                Session::flash('danger', 'Pilih tahun pelajaran yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

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
    public function destroy(Request $request, $unit, $tahun, $semester, $id)
    {
        $role = $request->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('kompetensiKategoriIklas',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('kompetensiKategoriIklas',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                // Inti function
                $item = $unit->kompetensiKategoriIklas()->where([
                    'id' => $request->id,
                    'semester_id' => $semester->id,
                ])->first();

                //$used_count = $item ? $item->categories()->count() : 0;
                $used_count = 0;
                if($item && $used_count < 1){
                    $name = $item->competence->name;
                    $unit->kompetensiKategoriIklas()->where([
                        'semester_id' => $semester->id,
                        'category_id' => $item->category_id
                    ])->where('number','>',$item->number)->decrement('number');
                    $item->delete();

                    $competenceList = $unit->kompetensiKategoriIklas()->where(['semester_id' => $semester->id])->get();
                    if($competenceList && count($competenceList) > 0){
                        $categories = KategoriIklas::select('id')->whereHas('competencies',function($q)use($unit,$semester){
                            $q->where([
                                'unit_id' => $unit->id,
                                'semester_id' => $semester->id
                            ]);
                        })->orderBy('number')->get();
                        $i = 1;
                        foreach($categories as $category){
                            $categoryCompetencies = $category->competencies()->where([
                                'unit_id' => $unit->id,
                                'semester_id' => $semester->id
                            ])->orderBy('number')->get();
                            foreach($categoryCompetencies as $c){
                                $c->sort_order = $i++;
                                $c->save();
                            }
                        }
                    }

                    Session::flash('success','Data '.$name.' berhasil dihapus');
                }
                else Session::flash('danger','Data gagal dihapus');

                return redirect()->route($this->route.'.index',['unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]);
            }
            else{
                Session::flash('danger', 'Pilih tahun pelajaran yang valid');

                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
