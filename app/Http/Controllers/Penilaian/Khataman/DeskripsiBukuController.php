<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Khataman;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Kurdeka\Buku;
use App\Models\Penilaian\Kurdeka\UnitBuku;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class DeskripsiBukuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'deskripsi-buku';
        $this->modul = $modul;
        $this->active = 'Deskripsi Buku';
        $this->route = $this->subsystem.'.penilaian.khataman.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $unit = null, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;
        
        $semesterList = $bookList = $isReadOnly = $data = $used = null;
        $isReadOnly = true;        

        $unitList = Unit::select('id','name')->sekolah();

        if(in_array($role,['kepsek','wakasek','guru'])){
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
                $semesterActive = Semester::where('is_active',1)->has('tahunAjaran');
                if(in_array($role,['guru'])){
                    $semesterActive = $semesterActive->whereHas('khatamTypes',function($q){
                        $q->whereHas('type',function($q){
                            $q->where('name','Buku');
                        })->whereHas('level.classes.jadwal',function($q){
                            $q->where([
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        });
                    });
                }
                $semesterActive = $semesterActive->first();
        
                $semesterList = Semester::where(function($q)use($role,$semesterActive){
                    $q->where(function($q)use($role){
                        $q->whereHas('books',function($q){
                            $q->where('unit_id',auth()->user()->pegawai->unit_id);
                        })->when(in_array($role,['guru']),function($q){
                            return $q->where('is_active','!=',1);
                        });
                    })->when($semesterActive,function($q)use($role,$semesterActive){
                        return $q->orWhere(function($q)use($role,$semesterActive){
                            $q->when(in_array($role,['guru']),function($q){
                                return $q->whereHas('khatamTypes',function($q){
                                    $q->whereHas('type',function($q){
                                        $q->where('name','Buku');
                                    })->whereHas('level.classes.jadwal',function($q){
                                        $q->where([
                                            'teacher_id' => auth()->user()->pegawai->id
                                        ]);
                                    });
                                })->aktif();
                            },function($q){
                                $q->whereHas('tahunAjaran',function($q)use($semesterActive){
                                    $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                                });
                            });
                        });
                    });
                })->get();

                if($tahun){
                    $tahun = str_replace("-","/",$tahun);
                    $tahun = TahunAjaran::where(function($q)use($semesterActive){
                        $q->whereHas('semester',function($q)use($semesterActive){
                            $q->whereHas('books',function($q){
                                $q->where('unit_id',auth()->user()->pegawai->unit_id);
                            });
                        })->when($semesterActive,function($q)use($semesterActive){
                            $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                        });
                    })->where('academic_year',$tahun)->first();
                }

                if($tahun){
                    $semester = Semester::where(function($q)use($role,$semesterActive){
                        $q->where(function($q)use($role){
                            $q->whereHas('books',function($q){
                                $q->where('unit_id',auth()->user()->pegawai->unit_id);
                            })->when(in_array($role,['guru']),function($q){
                                return $q->where('is_active','!=',1);
                            });
                        })->when($semesterActive,function($q)use($role,$semesterActive){
                            return $q->orWhere(function($q)use($role,$semesterActive){
                                $q->when(in_array($role,['guru']),function($q){
                                    return $q->whereHas('khatamTypes',function($q){
                                        $q->whereHas('type',function($q){
                                            $q->where('name','Buku');
                                        })->whereHas('level.classes.jadwal',function($q){
                                            $q->where([
                                                'teacher_id' => auth()->user()->pegawai->id
                                            ]);
                                        });
                                    })->aktif();
                                },function($q){
                                    $q->whereHas('tahunAjaran',function($q)use($semesterActive){
                                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                                    });
                                });
                            });
                        });
                    })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
                    if($semester){
                        if($semesterActive && ($tahun->academic_year_start >= $semesterActive->tahunAjaran->academic_year_start)){
                            $isReadOnly = false;
                            $bookList = Buku::select('id','title','total_pages')->whereDoesntHave('units',function($q)use($semester){
                                $q->where('semester_id', $semester->id);
                            })->orderBy('title')->get();
                        }

                        $data = $unit->books()->where([
                            'semester_id' => $semester->id
                        ])->has('buku')->get()->sortBy('buku.title');

                        foreach($data as $d){
                            $count = $d->buku()->whereHas('khatam.rapor',function($q)use($semester){
                                $q->where([
                                    'semester_id' => $semester->id
                                ])->whereHas('khatamKurdeka.type',function($q)use($semester){
                                    $q->where('name','Buku');
                                });
                            })->count();
                            if($count > 0) $used[$d->id] = 1;
                            else $used[$d->id] = 0;
                        }
                    }
                }
                else{
                    $semester = null;
                }
            }
            else{
                if(in_array($role,['kepsek','guru'])){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route($this->route.'.index',['unit' => $unit->name]);
                }
                else return redirect()->route($this->route.'.index');
            }
        }
        else{
            if(in_array($role,['kepsek','guru'])){
                $unit = $request->user()->pegawai->unit;
                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','unitList','unit','tahun','semesterList','semester','bookList','data','used','isReadOnly'));
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

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('books',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('books',function($q){
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
                    'title.required' => 'Mohon tuliskan kompetensi IKLaS',
                    'title.max' => 'Panjang kompetensi IKLaS maksimal 150 karakter'
                ];

                $this->validate($request, [
                    'title' => 'required|max:150'
                ], $messages);

                $isRelate = false;

                $count = Buku::where([
                    'title' => $request->title
                ]);
                
                if($count->count() < 1){
                    $buku = new Buku();
                    $buku->title = $request->title;
                    $buku->save();
                    $buku->fresh();

                    $isRelate = true;
                }
                else{
                    $buku = $count->first();

                    $relationCount = $unit->books()->where([
                        'semester_id' => $semester->id,
                        'book_id' => $buku->id
                    ])->count();

                    if($relationCount < 1) $isRelate = true;
                    else Session::flash('danger','Data buku '.$request->title.' tidak dapat ditambahkan');
                }

                if($buku && $isRelate){
                    $bukuUnit = new UnitBuku();
                    $bukuUnit->semester_id = $semester->id;
                    $bukuUnit->unit_id = $unit->id;
                    $bukuUnit->book_id = $buku->id;
                    $bukuUnit->employee_id = auth()->user()->pegawai->id;
                    $bukuUnit->save();

                    Session::flash('success','Data buku '.$buku->title.' berhasil ditambahkan');
                }

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

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('books',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('books',function($q){
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
                    'book.required' => 'Mohon pilih salah satu judul buku'
                ];

                $this->validate($request, [
                    'book' => 'required'
                ], $messages);

                $buku = Buku::where([
                    'id' => $request->book
                ])->first();
                
                if($buku){
                    $relationCount = $unit->books()->where([
                        'semester_id' => $semester->id,
                        'book_id' => $buku->id
                    ])->count();

                    if($relationCount < 1){
                        $bukuUnit = new UnitBuku();
                        $bukuUnit->semester_id = $semester->id;
                        $bukuUnit->unit_id = $unit->id;
                        $bukuUnit->book_id = $buku->id;
                        $bukuUnit->employee_id = auth()->user()->pegawai->id;
                        $bukuUnit->save();

                        Session::flash('success','Data buku '.$buku->title.' berhasil ditambahkan');
                    }
                    else Session::flash('danger','Data buku '.$buku->title.' tidak dapat ditambahkan');
                }
                else Session::flash('danger','Data buku tidak ditemukan');

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

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('books',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('books',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                // Inti function
                $item = $unit->books()->where([
                    'id' => $request->id,
                    'semester_id' => $semester->id,
                ])->has('buku')->first();

                $used_count = $item ? $item->buku()->whereHas('khatam.rapor',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id
                    ])->whereHas('khatamKurdeka.type',function($q)use($semester){
                        $q->where('name','Buku');
                    });
                })->count() : 0;
                if($item && $used_count < 1){
                    $name = $item->buku->title;
                    $item->delete();

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

namespace App\Http\Controllers\Penilaian\Khataman;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\Kurdeka\Buku;
use App\Models\Penilaian\Kurdeka\UnitBuku;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;

class DeskripsiBukuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'deskripsi-buku';
        $this->modul = $modul;
        $this->active = 'Deskripsi Buku';
        $this->route = $this->subsystem.'.penilaian.khataman.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $unit = null, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;
        
        $semesterList = $bookList = $isReadOnly = $data = $used = null;
        $isReadOnly = true;        

        $unitList = Unit::select('id','name')->sekolah();

        if(in_array($role,['kepsek','wakasek','guru'])){
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
                $semesterActive = Semester::where('is_active',1)->has('tahunAjaran');
                if(in_array($role,['guru'])){
                    $semesterActive = $semesterActive->whereHas('khatamTypes',function($q){
                        $q->whereHas('type',function($q){
                            $q->where('name','Buku');
                        })->whereHas('level.classes.jadwal',function($q){
                            $q->where([
                                'teacher_id' => auth()->user()->pegawai->id
                            ]);
                        });
                    });
                }
                $semesterActive = $semesterActive->first();
        
                $semesterList = Semester::where(function($q)use($role,$semesterActive){
                    $q->where(function($q)use($role){
                        $q->whereHas('books',function($q){
                            $q->where('unit_id',auth()->user()->pegawai->unit_id);
                        })->when(in_array($role,['guru']),function($q){
                            return $q->where('is_active','!=',1);
                        });
                    })->when($semesterActive,function($q)use($role,$semesterActive){
                        return $q->orWhere(function($q)use($role,$semesterActive){
                            $q->when(in_array($role,['guru']),function($q){
                                return $q->whereHas('khatamTypes',function($q){
                                    $q->whereHas('type',function($q){
                                        $q->where('name','Buku');
                                    })->whereHas('level.classes.jadwal',function($q){
                                        $q->where([
                                            'teacher_id' => auth()->user()->pegawai->id
                                        ]);
                                    });
                                })->aktif();
                            },function($q){
                                $q->whereHas('tahunAjaran',function($q)use($semesterActive){
                                    $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                                });
                            });
                        });
                    });
                })->get();

                if($tahun){
                    $tahun = str_replace("-","/",$tahun);
                    $tahun = TahunAjaran::where(function($q)use($semesterActive){
                        $q->whereHas('semester',function($q)use($semesterActive){
                            $q->whereHas('books',function($q){
                                $q->where('unit_id',auth()->user()->pegawai->unit_id);
                            });
                        })->when($semesterActive,function($q)use($semesterActive){
                            $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                        });
                    })->where('academic_year',$tahun)->first();
                }

                if($tahun){
                    $semester = Semester::where(function($q)use($role,$semesterActive){
                        $q->where(function($q)use($role){
                            $q->whereHas('books',function($q){
                                $q->where('unit_id',auth()->user()->pegawai->unit_id);
                            })->when(in_array($role,['guru']),function($q){
                                return $q->where('is_active','!=',1);
                            });
                        })->when($semesterActive,function($q)use($role,$semesterActive){
                            return $q->orWhere(function($q)use($role,$semesterActive){
                                $q->when(in_array($role,['guru']),function($q){
                                    return $q->whereHas('khatamTypes',function($q){
                                        $q->whereHas('type',function($q){
                                            $q->where('name','Buku');
                                        })->whereHas('level.classes.jadwal',function($q){
                                            $q->where([
                                                'teacher_id' => auth()->user()->pegawai->id
                                            ]);
                                        });
                                    })->aktif();
                                },function($q){
                                    $q->whereHas('tahunAjaran',function($q)use($semesterActive){
                                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                                    });
                                });
                            });
                        });
                    })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
                    if($semester){
                        if($semesterActive && ($tahun->academic_year_start >= $semesterActive->tahunAjaran->academic_year_start)){
                            $isReadOnly = false;
                            $bookList = Buku::select('id','title','total_pages')->whereDoesntHave('units',function($q)use($semester){
                                $q->where('semester_id', $semester->id);
                            })->orderBy('title')->get();
                        }

                        $data = $unit->books()->where([
                            'semester_id' => $semester->id
                        ])->has('buku')->get()->sortBy('buku.title');

                        foreach($data as $d){
                            $count = $d->buku()->whereHas('khatam.rapor',function($q)use($semester){
                                $q->where([
                                    'semester_id' => $semester->id
                                ])->whereHas('khatamKurdeka.type',function($q)use($semester){
                                    $q->where('name','Buku');
                                });
                            })->count();
                            if($count > 0) $used[$d->id] = 1;
                            else $used[$d->id] = 0;
                        }
                    }
                }
                else{
                    $semester = null;
                }
            }
            else{
                if(in_array($role,['kepsek','guru'])){
                    $unit = $request->user()->pegawai->unit;
                    return redirect()->route($this->route.'.index',['unit' => $unit->name]);
                }
                else return redirect()->route($this->route.'.index');
            }
        }
        else{
            if(in_array($role,['kepsek','guru'])){
                $unit = $request->user()->pegawai->unit;
                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','unitList','unit','tahun','semesterList','semester','bookList','data','used','isReadOnly'));
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

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('books',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('books',function($q){
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
                    'title.required' => 'Mohon tuliskan kompetensi IKLaS',
                    'title.max' => 'Panjang kompetensi IKLaS maksimal 150 karakter'
                ];

                $this->validate($request, [
                    'title' => 'required|max:150'
                ], $messages);

                $isRelate = false;

                $count = Buku::where([
                    'title' => $request->title
                ]);
                
                if($count->count() < 1){
                    $buku = new Buku();
                    $buku->title = $request->title;
                    $buku->save();
                    $buku->fresh();

                    $isRelate = true;
                }
                else{
                    $buku = $count->first();

                    $relationCount = $unit->books()->where([
                        'semester_id' => $semester->id,
                        'book_id' => $buku->id
                    ])->count();

                    if($relationCount < 1) $isRelate = true;
                    else Session::flash('danger','Data buku '.$request->title.' tidak dapat ditambahkan');
                }

                if($buku && $isRelate){
                    $bukuUnit = new UnitBuku();
                    $bukuUnit->semester_id = $semester->id;
                    $bukuUnit->unit_id = $unit->id;
                    $bukuUnit->book_id = $buku->id;
                    $bukuUnit->employee_id = auth()->user()->pegawai->id;
                    $bukuUnit->save();

                    Session::flash('success','Data buku '.$buku->title.' berhasil ditambahkan');
                }

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

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('books',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('books',function($q){
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
                    'book.required' => 'Mohon pilih salah satu judul buku'
                ];

                $this->validate($request, [
                    'book' => 'required'
                ], $messages);

                $buku = Buku::where([
                    'id' => $request->book
                ])->first();
                
                if($buku){
                    $relationCount = $unit->books()->where([
                        'semester_id' => $semester->id,
                        'book_id' => $buku->id
                    ])->count();

                    if($relationCount < 1){
                        $bukuUnit = new UnitBuku();
                        $bukuUnit->semester_id = $semester->id;
                        $bukuUnit->unit_id = $unit->id;
                        $bukuUnit->book_id = $buku->id;
                        $bukuUnit->employee_id = auth()->user()->pegawai->id;
                        $bukuUnit->save();

                        Session::flash('success','Data buku '.$buku->title.' berhasil ditambahkan');
                    }
                    else Session::flash('danger','Data buku '.$buku->title.' tidak dapat ditambahkan');
                }
                else Session::flash('danger','Data buku tidak ditemukan');

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

        if(in_array($role,['kepsek','wakasek','guru']))
            $unit = $request->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            $semesterActive = Semester::where('is_active',1)->has('tahunAjaran')->first();

            $tahun = str_replace("-","/",$tahun);
            $tahun = TahunAjaran::where(function($q)use($semesterActive){
                $q->whereHas('semester',function($q)use($semesterActive){
                    $q->whereHas('books',function($q){
                        $q->where('unit_id',auth()->user()->pegawai->unit_id);
                    });
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhere('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                });
            })->where('academic_year',$tahun)->first();
            if(!$tahun) return redirect()->route($this->route.'.index');

            $semester = Semester::where(function($q)use($semesterActive){
                $q->whereHas('books',function($q){
                    $q->where('unit_id',auth()->user()->pegawai->unit_id);
                })->when($semesterActive,function($q)use($semesterActive){
                    $q->orWhereHas('tahunAjaran',function($q)use($semesterActive){
                        $q->where('academic_year_start','>=',$semesterActive->tahunAjaran->academic_year_start);
                    });
                });
            })->where('semester_id',$tahun->academic_year.'-'.$semester)->first();
            if($semester){
                // Inti function
                $item = $unit->books()->where([
                    'id' => $request->id,
                    'semester_id' => $semester->id,
                ])->has('buku')->first();

                $used_count = $item ? $item->buku()->whereHas('khatam.rapor',function($q)use($semester){
                    $q->where([
                        'semester_id' => $semester->id
                    ])->whereHas('khatamKurdeka.type',function($q)use($semester){
                        $q->where('name','Buku');
                    });
                })->count() : 0;
                if($item && $used_count < 1){
                    $name = $item->buku->title;
                    $item->delete();

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
