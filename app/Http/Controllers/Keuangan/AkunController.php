<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Akun;
use App\Models\Anggaran\JenisAnggaranAnggaran;
use App\Models\Anggaran\KategoriAkun;
use App\Models\Kbm\TahunAjaran;
use App\Models\Setting;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;

class AkunController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.anggaran.akun';
        $this->active = 'Akun Anggaran';
        $this->route = 'keuangan.akun';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $lock = Setting::where('name','budgeting_account_lock_status')->first();

        $countSortOrder = Akun::whereNotNull('sort_order')->count();

        $akun = $countSortOrder == Akun::count() ? Akun::orderBy('sort_order')->get() : Akun::all();
        $kategori = KategoriAkun::all();
        $jenisAnggaran = JenisAnggaranAnggaran::all();

        $tahunPelajaran = TahunAjaran::where('is_finance_year',1)->latest()->first();
        $tahun = Date::now('Asia/Jakarta')->format('Y');

        if(in_array($role,['fam','faspv','am','akunspv']))
            $folder = $role;
        else
            $folder = 'read-only';

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_array($role,['am']) && $lock && $lock->value != 1) $editable = true;

        return view($this->template.'-index', compact('active','route','editable','lock','akun','kategori','jenisAnggaran','tahunPelajaran','tahun'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
        $lock = Setting::where('name','budgeting_account_lock_status')->first();

        if($lock && $lock->value != 1){
            $messages = [
                'code.required' => 'Mohon tuliskan kode akun',
                'name.required' => 'Mohon tuliskan nama akun',
                'is_fillable.required' => 'Mohon tentukan apakah akun bisa diisi atau tidak',
                'account_category.required' => 'Mohon pilih salah satu kategori akun'
            ];

            $this->validate($request, [
                'code' => 'required',
                'name' => 'required',
                'is_fillable' => 'required',
                'account_category' => 'required'
            ], $messages);

            $count = Akun::where(['code' => $request->code, 'name' => $request->name])->whereNull('deleted_at')->count();
            
            if($count < 1){
                $kategori = KategoriAkun::where('id',$request->account_category)->has('parent')->first();

                $akun = new Akun();
                $akun->code = $request->code;
                $akun->name = strtoupper($request->name);
                $akun->is_fillable = $request->is_fillable;
                $akun->is_exclusive = $request->is_exclusive ? $request->is_exclusive : '0';
                $akun->is_autodebit = 0;
                
                if($kategori) $akun->account_category_id = $kategori->id;

                $last_sort_order = Akun::withTrashed()->count() > 0 ? Akun::withTrashed()->get()->max('sort_order') : 0;
                $akun->sort_order = $last_sort_order ? $last_sort_order+1 : 1;

                $akun->save();

                $akun->fresh();

                if(count($request->budgeting) > 0){
                    $akun->anggaran()->sync($request->budgeting);
                }
                elseif($request->acceptance_status != 1){
                    $akun->anggaran()->detach();
                }

                Session::flash('success','Data '.$akun->codeName.' berhasil ditambahkan');
            }

            else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');
        }
        else Session::flash('danger','Data tidak dapat ditambahkan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rkat\Rkat  $rkat
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $lock = Setting::where('name','budgeting_account_lock_status')->first();

        if($lock && $lock->value != 1){
            $role = $request->user()->role->name;

            $akun = $request->id ? Akun::where('id',$request->id)->whereNull('deleted_at')->first() : null;

            if($akun){
                $kategori = KategoriAkun::all();
                $budgeting = $akun->anggaran()->pluck('budgeting_budgeting_type_id');
                $jenisAnggaran = JenisAnggaranAnggaran::all();

                return view($this->template.'-ubah', compact('akun','kategori','budgeting','jenisAnggaran'));
            }
        }
        return "Ups, tidak dapat memuat data";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $lock = Setting::where('name','budgeting_account_lock_status')->first();

        if($lock && $lock->value != 1){
            $messages = [
                'code.required' => 'Mohon tuliskan kode akun',
                'name.required' => 'Mohon tuliskan nama akun',
                'is_fillable.required' => 'Mohon tentukan apakah akun bisa diisi atau tidak',
                'account_category.required' => 'Mohon pilih salah satu kategori akun'
            ];

            $this->validate($request, [
                'code' => 'required',
                'name' => 'required',
                'is_fillable' => 'required',
                'account_category' => 'required'
            ], $messages);

            $akun = Akun::where('id',$request->id)->whereNull('deleted_at')->first();
            $count = Akun::where(['code' => $request->code, 'name' => $request->name])->where('id','!=',$request->id)->whereNull('deleted_at')->count();

            if($akun && $count < 1){
                $kategori = KategoriAkun::where('id',$request->account_category)->has('parent')->first();

                $akun->code = $request->code;
                $akun->name = strtoupper($request->name);
                $akun->is_fillable = $request->is_fillable;
                $akun->is_exclusive = $request->is_exclusive ? $request->is_exclusive : '0';
                
                if($kategori) $akun->account_category_id = $kategori->id;

                $akun->save();

                $akun->fresh();

                if(count($request->budgeting) > 0){
                    $akun->anggaran()->sync($request->budgeting);
                }
                elseif($request->acceptance_status != 1){
                    $akun->anggaran()->detach();
                }

               Session::flash('success','Data '.$akun->codeName.' berhasil diubah');
            }
            else Session::flash('danger','Perubahan data gagal disimpan');
        }
        else Session::flash('danger','Perubahan data tidak dapat disimpan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $lock = Setting::where('name','budgeting_account_lock_status')->first();

        if($lock && $lock->value != 1){
            $akun = Akun::find($id);
            if($akun){
                $tahunPelajaran = TahunAjaran::where('is_finance_year',1)->latest()->first();
                $tahun = Date::now('Asia/Jakarta')->format('Y');

                $usedCount = $exUsedCount = null;
                if($akun->is_exclusive == 0){
                    $usedCount = $akun->ppa()->whereHas('ppa',function($q)use($tahunPelajaran,$tahun){
                        $q->where(function($q)use($tahunPelajaran,$tahun){
                            $q->where(function($q)use($tahunPelajaran){
                                $q->where('academic_year_id', $tahunPelajaran->id)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                    $q->where('name','LIKE','APB-KSO%');
                                })->where(function($q){
                                    $q->where(function($q){
                                        $q->doesntHave('lppa')->whereHas('bbk.bbk',function($q){
                                            $q->has('jenisAnggaran')->where(function($q){
                                                $q->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                                            });
                                        });
                                    })->orWhereHas('lppa',function($q){
                                        $q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    });
                                });
                            })->orWhere(function($q)use($tahun){
                                $q->where('year', $tahun)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                    $q->where('name','APBY');
                                })->where(function($q){
                                    $q->where(function($q){
                                        $q->doesntHave('lppa')->whereHas('bbk.bbk',function($q){
                                            $q->has('jenisAnggaran')->where(function($q){
                                                $q->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                            });
                                        });
                                    })->orWhereHas('lppa',function($q){
                                        $q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                    });
                                });
                            });
                        });
                    })->count();
                }
                elseif($akun->is_exclusive == 1){
                    $exUsedCount = $akun->ppa()->whereHas('ppa',function($q)use($tahunPelajaran,$tahun){
                        $q->where(function($q)use($tahunPelajaran,$tahun){
                            $q->where(function($q)use($tahunPelajaran){
                                $q->where('academic_year_id', $tahunPelajaran->id)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                    $q->where('name','LIKE','APB-KSO%');
                                })->whereHas('bbk.bbk',function($q){
                                    $q->has('jenisAnggaran')->where(function($q){
                                        $q->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                                    });
                                });
                            })->orWhere(function($q)use($tahun){
                                $q->where('year', $tahun)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                    $q->where('name','APBY');
                                })->whereHas('bbk.bbk',function($q){
                                    $q->has('jenisAnggaran')->where(function($q){
                                        $q->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                    });
                                });
                            });
                        });
                    })->count();
                }

                $name = $akun->codeName;

                if(($akun->is_exclusive != 1 && $usedCount < 1) || ($akun->is_exclusive == 1 && $exUsedCount < 1)){
                    $counter = 0;
                    if($akun->anggaran()->count() > 0) $counter++;
                    if($akun->rkat()->count() > 0) $counter++;
                    if($akun->apby()->count() > 0) $counter++;
                    if($akun->ppa()->count() > 0) $counter++;

                    
                    
                    if($counter > 0){
                        $akun->delete();
                    }
                    else{
                        $akun->forceDelete();
                    }

                    Session::flash('success','Data '.$name.' berhasil dihapus');
                }
                else Session::flash('danger','Data '.$name.' gagal dihapus. Mohon selesaikan semua proses pengajuan terlebih dahulu.');
            }
            else Session::flash('danger','Data gagal dihapus');
        }
        else Session::flash('danger','Data tidak dapat dihapus');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Sort the all resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        $lock = Setting::where('name','budgeting_account_lock_status')->first();

        if($lock && $lock->value != 1){
            $kategori = KategoriAkun::has('parent')->has('akun')->get();
            $sortOrder = 1;

            foreach($kategori as $k){
                $kodeAkun = $k->akun()->withTrashed()->pluck('code')->unique()->toArray();
                if($k->parent->name != 'Pembiayaan') natsort($kodeAkun);
                foreach($kodeAkun as $a){
                    $akun = $k->akun()->withTrashed()->where('code',$a);

                    if($akun->count() > 1){
                        $akuns = $akun->orderBy('created_at')->get();
                        foreach($akuns as $akun){
                            Akun::withTrashed()->find($akun->id)->update(['sort_order' => $sortOrder++]);
                        }
                    }
                    elseif($akun->count() == 1){
                        $akun->first()->update(['sort_order' => $sortOrder++]);
                    }
                }
            }

            if(($sortOrder-1) == Akun::withTrashed()->count()){
                Session::flash('success','Akun anggaran berhasil diurutkan');
            }

            else Session::flash('danger','Akun anggaran gagal diurutkan');
        }
        else Session::flash('danger','Akun anggaran tidak dapat diurutkan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Lock the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function lock(Request $request)
    {
        if(in_array($request->user()->role->name,['am'])){
            if(Akun::count() > 0){
                $lock = Setting::where('name','budgeting_account_lock_status')->first();

                if(!$lock){
                    $lock = new Setting();
                    $lock->name = 'budgeting_account_lock_status';
                    $lock->value = 1;
                    $lock->save();

                    $lock->fresh();
                }

                $lock->value = 1;
                $lock->save();

                Session::flash('success','Akun anggaran berhasil divalidasi');

                return redirect()->route($this->route.'.index');
            }

            else Session::flash('danger','Akun anggaran gagal divalidasi');
        }
        else return redirect()->route('keuangan.index');
    }
}
