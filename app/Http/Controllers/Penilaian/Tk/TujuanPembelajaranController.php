<?php

namespace App\Http\Controllers\Penilaian\Tk;

use App\Http\Controllers\Controller;
use App\Models\Kbm\Semester;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Penilaian\Iklas\KompetensiIklas;
use App\Models\Penilaian\Tk\Objective;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TujuanPembelajaranController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'tujuan-pembelajaran';
        $this->modul = $modul;
        $this->active = 'Tujuan Pembelajaran';
        $this->route = $this->subsystem.'.penilaian.tk.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $tingkat = null)
    {
        $role = auth()->user()->role->name;

        $semester = Semester::select('id','academic_year_id')->aktif()->first();
        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q)use($semester){
            $q->where([
                'semester_id' => $semester->id
            ])->whereHas('kurikulum',function($q){
                $q->where('name','Kurdeka');
            });
        })->first();

        $data = $used = null;

        $tingkatList = auth()->user()->pegawai->unit->levels()->whereHas('curricula',function($q)use($isWali,$semester){
            $q->when($isWali,function($q)use($semester){
                return $q->where('semester_id', $semester->id);
            })->where([
                'curriculum_id' => 2,
            ]);
        });
        if($isWali){
            $tingkatList = $tingkatList->whereHas('classes',function($q)use($semester){
                $q->where([
                    'academic_year_id' => $semester->academic_year_id,
                    'teacher_id' => auth()->user()->pegawai->id
                ]);
            });
        }
        $tingkatList = $tingkatList->get();
        if($tingkat){
            $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($isWali,$semester){
                $q->when($isWali,function($q)use($semester){
                    return $q->where('semester_id', $semester->id);
                })->where([
                    'curriculum_id' => 2,
                ]);
            });
            if($isWali){
                $tingkat = $tingkat->whereHas('classes',function($q)use($semester){
                    $q->where([
                        'academic_year_id' => $semester->academic_year_id,
                        'teacher_id' => auth()->user()->pegawai->id
                    ]);
                });
            }
            $tingkat = $tingkat->first();

            if($tingkat){
                $data = $tingkat->objectives()->select('id','desc')->get();

                foreach($data as $d){
                    if($d->elements()->count() > 0) $used[$d->id] = 1;
                    else $used[$d->id] = 0;
                }
            }
        }

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if($isWali) $editable = true;

        return view($this->route.'-index', compact('active','route','tingkatList','tingkat','data','used','editable'));
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
    public function store(Request $request, $tingkat)
    {
        $role = auth()->user()->role->name;

        $semester = Semester::select('id','academic_year_id')->aktif()->first();
        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q)use($semester){
            $q->where([
                'semester_id' => $semester->id
            ])->whereHas('kurikulum',function($q){
                $q->where('name','Kurdeka');
            });
        })->first();

        $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($isWali,$semester){
            $q->when($isWali,function($q)use($semester){
                return $q->where('semester_id', $semester->id);
            })->where([
                'curriculum_id' => 2,
            ]);
        });
        if($isWali){
            $tingkat = $tingkat->whereHas('classes',function($q)use($semester){
                $q->where([
                    'academic_year_id' => $semester->academic_year_id,
                    'teacher_id' => auth()->user()->pegawai->id
                ]);
            });
        }
        $tingkat = $tingkat->first();

        if($tingkat){
            // Inti function
            $messages = [
                'desc.required' => 'Mohon tuliskan tujuan pembelajaran',
                'desc.max' => 'Panjang tujuan pembelajaran maksimal 150 karakter'
            ];

            $this->validate($request, [
                'desc' => 'required|max:150'
            ], $messages);

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

                Session::flash('success','Data tujuan pembelajaran '.$request->desc.' berhasil ditambahkan');
            }
            else Session::flash('danger','Data tujuan pembelajaran '.$request->desc.' sudah pernah ditambahkan');

            return redirect()->route($this->route.'.index',['tingkat' => $tingkat->id]);
        }
        else{
            Session::flash('danger', 'Tingkat kelas tidak ditemukan');

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
    public function edit(Request $request, $tingkat)
    {
        $role = auth()->user()->role->name;

        $semester = Semester::select('id','academic_year_id')->aktif()->first();
        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q)use($semester){
            $q->where([
                'semester_id' => $semester->id
            ])->whereHas('kurikulum',function($q){
                $q->where('name','Kurdeka');
            });
        })->first();

        $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($isWali,$semester){
            $q->when($isWali,function($q)use($semester){
                return $q->where('semester_id', $semester->id);
            })->where([
                'curriculum_id' => 2,
            ]);
        });
        if($isWali){
            $tingkat = $tingkat->whereHas('classes',function($q)use($semester){
                $q->where([
                    'academic_year_id' => $semester->academic_year_id,
                    'teacher_id' => auth()->user()->pegawai->id
                ]);
            });
        }
        $tingkat = $tingkat->first();

        if($tingkat){
            // Inti function
            $data = $request->id ? Objective::where([
                'id' => $request->id,
                'level_id' => $tingkat->id
            ])->first() : null;

            if($data){
                $active = $this->active;
                $route = $this->route;

                return view($route.'-edit', compact('active','route','tingkat','data'));
            }
            else return 'Ups, tujuan pembelajaran tidak ditemukan';
        }
        else return 'Ups, tingkat kelas tidak ditemukan';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tingkat)
    {
        $role = auth()->user()->role->name;

        $semester = Semester::select('id','academic_year_id')->aktif()->first();
        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q)use($semester){
            $q->where([
                'semester_id' => $semester->id
            ])->whereHas('kurikulum',function($q){
                $q->where('name','Kurdeka');
            });
        })->first();

        $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($isWali,$semester){
            $q->when($isWali,function($q)use($semester){
                return $q->where('semester_id', $semester->id);
            })->where([
                'curriculum_id' => 2,
            ]);
        });
        if($isWali){
            $tingkat = $tingkat->whereHas('classes',function($q)use($semester){
                $q->where([
                    'academic_year_id' => $semester->academic_year_id,
                    'teacher_id' => auth()->user()->pegawai->id
                ]);
            });
        }
        $tingkat = $tingkat->first();

        if($tingkat){
            // Inti function
            $messages = [
                'editDesc.required' => 'Mohon tuliskan tujuan pembelajaran',
                'editDesc.max' => 'Panjang tujuan pembelajaran maksimal 150 karakter'
            ];

            $this->validate($request, [
                'editDesc' => 'required|max:150'
            ], $messages);

            $item = Objective::where([
                'id' => $request->id,
                'level_id' => $tingkat->id
            ])->first();

            $count = Objective::where([
                'level_id' => $tingkat->id,
                'desc' => $request->editDesc
            ])->where('id','!=',$request->id)->count();

            if($item && $count < 1){
                $old = $item->desc;
                $item->desc = $request->editDesc;
                $item->save();
                
                Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->desc);
            }

            else Session::flash('danger','Perubahan data gagal disimpan');

            return redirect()->route($this->route.'.index',['tingkat' => $tingkat->id]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $tingkat, $id)
    {
        $role = auth()->user()->role->name;

        $semester = Semester::select('id','academic_year_id')->aktif()->first();
        $isWali = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
            $q->aktif();
        })->whereHas('level.curricula',function($q)use($semester){
            $q->where([
                'semester_id' => $semester->id
            ])->whereHas('kurikulum',function($q){
                $q->where('name','Kurdeka');
            });
        })->first();

        $tingkat = auth()->user()->pegawai->unit->levels()->where(['id' => $tingkat])->whereHas('curricula',function($q)use($isWali,$semester){
            $q->when($isWali,function($q)use($semester){
                return $q->where('semester_id', $semester->id);
            })->where([
                'curriculum_id' => 2,
            ]);
        });
        if($isWali){
            $tingkat = $tingkat->whereHas('classes',function($q)use($semester){
                $q->where([
                    'academic_year_id' => $semester->academic_year_id,
                    'teacher_id' => auth()->user()->pegawai->id
                ]);
            });
        }
        $tingkat = $tingkat->first();

        if($tingkat){
            // Inti function
            $item = Objective::where([
                'id' => $request->id,
                'level_id' => $tingkat->id
            ])->first();

            $used_count = $item ? $item->elements()->count() : 0;
            if($item && $used_count < 1){
                $desc = $item->desc;
                $item->delete();

                Session::flash('success','Data '.$desc.' berhasil dihapus');
            }
            else Session::flash('danger','Data gagal dihapus');

            return redirect()->route($this->route.'.index',['tingkat' => $tingkat->id]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }
}
