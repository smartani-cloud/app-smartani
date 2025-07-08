<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\AnggaranAkun;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Anggaran\KategoriAkun;
use App\Models\Anggaran\KategoriAnggaran;
use App\Models\Apby\Apby;
use App\Models\Apby\ApbyDetail;
use App\Models\Rkat\Rkat;
use App\Models\Rkat\RkatDetail;
use App\Models\Kbm\TahunAjaran;
use App\Models\Setting;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;

class RkatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $jenis = null, $tahun = null, $anggaran = null)
    {
        // Override Budget Category
        if(!$jenis) $jenis = 'apby';
        
        $role = $request->user()->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAnggaranCount = null;
        foreach($jenisAnggaran as $j){
            $anggaranCount = $j->anggaran();
            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv'])){
                if($request->user()->pegawai->unit_id == '5'){
                    // if(in_array($role,['etl','ctl'])){
                    //     $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('acc_position_id',$request->user()->pegawai->jabatan->first()->id);});
                    // }
                    // else{
                        $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id);});
                    // }
                }
                else{
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('unit_id',$request->user()->pegawai->unit_id);});
                }
            }
            $anggaranCount = $anggaranCount->count();
            if($jenisAnggaranCount){
                $jenisAnggaranCount = $jenisAnggaranCount->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
            else{
                $jenisAnggaranCount = collect()->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
        }
        
        $jenisAktif = $kategori = $rkat = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $yearAttr = null;
        $yearsCount = $academicYearsCount = 0;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif) return redirect()->route('rkat.index');

            $queryRkat = Rkat::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });
            
            // if(in_array($role,['etl','ctl'])){
            //     $queryRkat = $queryRkat->whereHas('jenisAnggaranAnggaran.anggaran',function($q)use($request){
            //         $q->where('acc_position_id',$request->user()->pegawai->position_id);
            //     });
            // }

            if($queryRkat->count() > 0){
                $years = clone $queryRkat;
                $yearsCount = $years->whereNotNull('year')->aktif()->count();
                $years = $years->whereNotNull('year')->aktif()->orderBy('year')->pluck('year')->unique();

                $academicYears = clone $queryRkat;
                $academicYearsCount = $academicYears->has('tahunPelajaran')->aktif()->count();
                $academicYears = $academicYears->has('tahunPelajaran')->with('tahunPelajaran:id,academic_year')->aktif()->get()->sortBy('tahunPelajaran.academic_year')->pluck('academic_year_id')->unique();

                $latest = clone $queryRkat;
                $latest = $latest->latest()->first();

                if($tahun) $isYear = strlen($tahun) == 4 ? true : false;
                else $isYear = $latest->year ? true : false;
            }
            else{
                $isYear = $jenisAktif->is_academic_year == 1 ? false : true;
            }

            $tahunPelajaran = TahunAjaran::where('is_finance_year',1)->latest()->take(1)->get();

            if($academicYearsCount > 0){
                $tahunPelajaran = TahunAjaran::where(function($q)use($academicYears){
                    $q->where(function($q){
                        $q->where('is_finance_year',1);
                    })->orWhere(function($q)use($academicYears){
                        $q->whereIn('id',$academicYears);
                    });
                })->orderBy('created_at')->get();
            }

            if(!$isYear){
                if($request->tahun){
                    $tahun = str_replace("-","/",$request->tahun);
                    $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                }
                else{
                    $tahun = TahunAjaran::where('is_finance_year',1)->latest()->first();
                }
                if(!$tahun) return redirect()->route('rkat.index');
            }
            else{
                if(!$tahun){
                    if($yearsCount > 0 && $jenisAktif->is_academic_year == 1){
                        $tahun = $years->last();
                    }
                    else{
                        $tahun = Date::now('Asia/Jakarta')->format('Y');
                    }
                }
                else{
                    if($yearsCount > 0 && $jenisAktif->is_academic_year == 1){
                        if(!in_array($tahun,$years->toArray())) $tahun = null;
                    }
                    else{
                        if($tahun != Date::now('Asia/Jakarta')->format('Y')) $tahun = null;
                    }
                }
                if(!$tahun){
                    return redirect()->route('rkat.index');
                }
            }

            if($jenisAktif){
                $kategori = KategoriAnggaran::select('id','name')->whereHas('anggarans.jenisAnggaran',function($q)use($jenisAktif){
                    $q->where('budgeting_type_id',$jenisAktif->id);
                })->get();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $rkat = clone $queryRkat;
                $rkat = $rkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran.tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->get();

                if($anggaran){
                    $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                        $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                            $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                        });
                    });

                    if($request->user()->pegawai->unit_id == '5'){
                        $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->first();
                    }
                    else{
                        $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->where('unit_id',$request->user()->pegawai->unit_id)->first();
                    }
                    // if(in_array($role,['etl','ctl'])){
                    //     $anggaranAktif = $anggaranAktif->where('acc_position_id',$request->user()->pegawai->position_id);
                    //     $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->where('acc_position_id',$request->user()->pegawai->position_id)->first();
                    // }
                    // elseif(in_array($request->user()->pegawai->position_id,[20,25,34,37,48,50,53,57])){
                    if(in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']) || (in_array($request->user()->role->name,['fam','faspv','am','akunspv']) && !$checkAnggaran)){
                        $anggaranAktif = $anggaranAktif->whereIn('id',$rkat->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'));
                    }
                    $anggaranAktif = $anggaranAktif->first();

                    if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']) && !$checkAnggaran){
                        $anggaranAktif = null;
                    }

                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                        $rkatAktif = $anggaranAktif->rkat()->where($yearAttr, ($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->latest()->first();

                        if($rkatAktif || (!$rkatAktif && (!$isYear && $tahun->is_finance_year == 1) || ($isYear && $tahun == date('Y')))){
                            // Inti controller

                            $kategori = KategoriAkun::all();

                            $totalAnggaran = 0;

                            // Counter
                            $rkatDetail = RkatDetail::whereHas('rkat',function($q)use($yearAttr,$tahun,$jenisAktif){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id')->unique())->aktif();
                            });

                            $totalPendapatan = clone $rkatDetail;
                            $totalPendapatan = $totalPendapatan->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Pendapatan');
                                });
                            })->sum('value');

                            $totalPembiayaan = clone $rkatDetail;
                            $totalPembiayaan = $totalPembiayaan->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Pembiayaan');
                                });
                            })->sum('value');

                            $totalBelanja = clone $rkatDetail;
                            $totalBelanja = $totalBelanja->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Belanja');
                                });
                            })->sum('value');

                            if($rkatAktif && $rkatAktif->detail()->count() > 0){
                                $totalAnggaran = $rkatAktif->detail()->whereHas('akun',function($query){
                                    $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                        $q->where('name','Belanja');
                                    });
                                })->sum('value');
                            }

                            $total = collect([
                                'pendapatanPembiayaan' => $totalPendapatan + $totalPembiayaan,
                                'belanja' => $totalBelanja,
                                'anggaran' => $totalAnggaran,
                                'operasionalPembiayaan' => $totalPendapatan - $totalBelanja + $totalPembiayaan
                            ]);

                            $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;
                            $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);

                            $lock = Setting::where('name','budgeting_account_lock_status')->first();
                                
                            if(in_array($role,['direktur','fam','faspv','am']))
                                $folder = $role;
                            elseif($isPa || (in_array($request->user()->pegawai->position_id,[57]) && $isAnggotaPa))
                                $folder = 'pa';
                            else
                                $folder = 'read-only';

                            return view('keuangan.'.$folder.'.rkat_detail', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','rkat','years','academicYears','anggaranAktif','rkatAktif','kategori','total','lock','isAnggotaPa'));
                        }
                        else return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                    else return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
                if(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv'])){
                    $anggaranAktif = $checkAnggaran = null;
                    if($request->user()->pegawai->unit_id == '5'){
                        $anggaranAktif = Anggaran::where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->whereIn('id',$rkat->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
                        $checkAnggaran = Anggaran::where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->first();
                    }
                    else{
                        $anggaranAktif = Anggaran::where('unit_id',$request->user()->pegawai->unit_id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->whereIn('id',$rkat->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'))->first();
                        $checkAnggaran = Anggaran::where('unit_id',$request->user()->pegawai->unit_id)->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                            $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            });
                        })->first();
                    }
                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            })->first();
                        $rkatAktif = !$isYear ? $anggaranAktif->rkat()->where('academic_year_id',$tahun->id)->first() : $anggaranAktif->rkat()->where('year',$tahun)->first();

                        if(!$rkatAktif){
                            $tahun = !$isYear ? TahunAjaran::where('is_finance_year',1)->latest()->first() : Date::now('Asia/Jakarta')->format('Y');
                        }
                        return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                    elseif($checkAnggaran){
                        $anggaranAktif = $checkAnggaran->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                            })->first();
                        $tahun = !$isYear ? TahunAjaran::where('is_finance_year',1)->latest()->first() : Date::now('Asia/Jakarta')->format('Y');
                        return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
                    }
                }
            }

            else return redirect()->route('rkat.index');
        }
        elseif(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv'])){
            $jenisAktif = JenisAnggaran::whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if($jenisAktif){
                return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link]);
            }
            elseif($anggaranCount < 1){
                return redirect()->route('keuangan.index');
            }
        }

        // if($jenis && $isKso)
        //     return view('keuangan.read-only.rkat_kso_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','isYear','rkat','years','academicYears'));
        // else
            return view('keuangan.read-only.rkat_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','yearAttr','isYear','rkat','years','academicYears'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;

        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $rkat = Rkat::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            if($request->user()->pegawai->unit_id == '5'){
                $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                    $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                })->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->first();
            }
            else{
                $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                    $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                })->where('unit_id',$request->user()->pegawai->unit_id)->first();
            }
            // if(in_array($role,['etl','ctl'])){
            //     $anggaranAktif = $anggaranAktif->where('acc_position_id',$request->user()->pegawai->position_id);
            //     $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->where('acc_position_id',$request->user()->pegawai->position_id)->first();
            // }
            // elseif(in_array($request->user()->pegawai->position_id,[20,25,34,37,48,50,53,57])){
            if(in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']) || (in_array($request->user()->role->name,['fam','faspv','am','akunspv']) && !$checkAnggaran)){
                $anggaranAktif = $anggaranAktif->whereIn('id',$rkat->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'));
            }
            $anggaranAktif = $anggaranAktif->first();

            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']) && !$checkAnggaran){
                $anggaranAktif = null;
            }

            if($anggaranAktif){
                $lock = Setting::where('name','budgeting_account_lock_status')->first();
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                $rkatAktif = !$isYear ? $anggaranAktif->rkat()->where('academic_year_id', $tahun->id)->aktif()->latest()->first() : $anggaranAktif->rkat()->where('year', $tahun)->aktif()->latest()->first();
                $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);

                if(($lock && $lock->value == 1) && (($rkatAktif && $rkatAktif->detail()->count() < 1) || (!$rkatAktif && ((!$isYear && $tahun->is_finance_year == 1) || ($isYear && $tahun == date('Y')))))) {
                    if(($role == 'am' || ($role != 'am' && $request->user()->pegawai->position_id == $anggaranAktif->anggaran->acc_position_id) || ($role == 'faspv' && $isAnggotaPa)) && $anggaranAktif->akun()->count() > 0){
                        // Inti function
                        if(!$rkatAktif){
                            $rkat = new Rkat();
                            if(!$isYear)
                                $rkat->academic_year_id = $tahun->id;
                            else
                                $rkat->year = $tahun;
                            $rkat->budgeting_budgeting_type_id = $anggaranAktif->id;
                            $rkat->employee_id = $request->user()->pegawai->id;

                            //Get last revision
                            $latestRkat = Rkat::where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->orderBy('revision','desc')->first();

                            if($latestRkat) $rkat->revision = $latestRkat->is_active == 1 ? $latestRkat->revision : ($latestRkat->revision + 1);

                            $rkat->save();

                            $rkat->fresh();
                        }
                        else $rkat = $rkatAktif;

                        foreach($anggaranAktif->akun()->where('is_exclusive',0)->orderBy('sort_order')->get() as $a){
                            $rkat->detail()->save(RkatDetail::create([
                                'rkat_id' => $rkat->id,
                                'account_id' => $a->id
                            ]));
                        }
                    }
                }
                elseif(!$lock || ($lock && $lock->value != 1)){
                    Session::flash('danger','Tidak dapat membuat RKAB baru untuk anggaran '.$anggaranAktif->anggaran->name);
                }
                return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('rkat.index');
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
     * @param  \App\Models\Rkat\Rkat  $rkat
     * @return \Illuminate\Http\Response
     */
    public function show(Rkat $rkat)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rkat\Rkat  $rkat
     * @return \Illuminate\Http\Response
     */
    public function edit(Rkat $rkat)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rkat\Rkat  $rkat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $rkat = Rkat::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            if($request->user()->pegawai->unit_id == '5'){
                $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                    $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                })->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->first();
            }
            else{
                $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                    $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                })->where('unit_id',$request->user()->pegawai->unit_id)->first();
            }
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            if(in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']) || (in_array($request->user()->role->name,['fam','faspv','am','akunspv']) && !$checkAnggaran)){
                $anggaranAktif = $anggaranAktif->whereIn('id',$rkat->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'));
            }
            $anggaranAktif = $anggaranAktif->first();

            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']) && !$checkAnggaran){
                $anggaranAktif = null;
            }

            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                $rkatAktif = !$isYear ? $anggaranAktif->rkat()->where('academic_year_id', $tahun->id)->aktif()->latest()->first() : $anggaranAktif->rkat()->where('year', $tahun)->aktif()->latest()->first();
                
                if($rkatAktif){
                    $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;
                    $isAnggotaPa = $this->checkRole($anggaranAktif->anggaran,$role);

                    $rkatAktifDetail = $rkatAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1);
                    });

                    if($rkatAktifDetail->count() > 0){
                        if($role == 'direktur'){
                            // Inti function
                            if($rkatAktif->finance_acc_status_id == 1){
                                $rkatAktifDetailClone = clone $rkatAktifDetail;
                                foreach($rkatAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                    $isBelanja = $detail->akun->kategori->parent->name == 'Belanja' ? true : false;
                                    $inputName = 'value-'.$detail->id;
                                    $requestValue = (int)str_replace('.','',$request->{$inputName});
                                    $detail->value = $requestValue;
                                    $detail->value_director = $requestValue;
                                    if(($isBelanja && $requestValue > 0) || (!$isBelanja && $requestValue != 0)){
                                        if(isset($detail->value_fam)){
                                            if($detail->value_fam != $requestValue){
                                                $detail->edited_employee_id = $request->user()->pegawai->id;
                                                $detail->edited_status_id = 1;
                                            }
                                            $detail->director_acc_id = $request->user()->pegawai->id;
                                            $detail->director_acc_status_id = 1;
                                            $detail->director_acc_time = Date::now('Asia/Jakarta');
                                        }
                                    }
                                    $detail->save();

                                    $detail->fresh();

                                    $this->updateParentValues($rkatAktif,$detail);
                                }

                                if(isset($request->validate) && $request->validate == 'validate'){
                                    // Accept unaccepted value
                                    $rkatAktifDetail->whereNull([
                                        'director_acc_id',
                                        'director_acc_status_id',
                                        'director_acc_time',
                                    ])->update([
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    // Accept RKAT
                                    $rkatAktif->update([
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    // Generate APBY
                                    $this->generateApby($anggaranAktif,$rkatAktif,$tahun);
                                }
                            }
                        }
                        elseif(in_array($role,['am','fam'])){
                            // Inti function
                            $rkatAktifDetailClone = clone $rkatAktifDetail;
                            $rkatAktifDetailFilter = $rkatAktifDetailClone->where(function($query){
                                $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();
                            foreach($rkatAktifDetailFilter as $detail){
                                $isBelanja = $detail->akun->kategori->parent->name == 'Belanja' ? true : false;
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});

                                if(($isBelanja && $requestValue > 0) || (!$isBelanja && $requestValue != 0)){
                                    if($isAnggotaPa && !$detail->employee_id){
                                        $detail->employee_id = $request->user()->pegawai->id;
                                        $detail->finance_acc_id = $request->user()->pegawai->id;
                                        $detail->finance_acc_status_id = 1;
                                        $detail->finance_acc_time = Date::now('Asia/Jakarta');
                                    }
                                    if(isset($detail->value_pa)){
                                        if($detail->value_pa != $requestValue && $detail->employee_id != $request->user()->pegawai->id){
                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                        $detail->finance_acc_id = $request->user()->pegawai->id;
                                        $detail->finance_acc_status_id = 1;
                                        $detail->finance_acc_time = Date::now('Asia/Jakarta');
                                    }
                                }

                                $detail->value = $requestValue;
                                if($isAnggotaPa) $detail->value_pa = $requestValue;
                                $detail->value_fam = $requestValue;

                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($rkatAktif,$detail);
                            }

                            if($rkatAktifDetail->whereNull('finance_acc_status_id')->count() < 1){
                                $rkatAktif->finance_acc_id = $request->user()->pegawai->id;
                                $rkatAktif->finance_acc_status_id = 1;
                                $rkatAktif->finance_acc_time = Date::now('Asia/Jakarta');
                                $rkatAktif->save();
                            }
                        }
                        elseif($isAnggotaPa && in_array($role,['faspv'])){
                            // Inti function
                            $rkatAktifDetailFilter = $rkatAktifDetail->where(function($query){
                                $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();
                            foreach($rkatAktifDetailFilter as $detail){
                                $isBelanja = $detail->akun->kategori->parent->name == 'Belanja' ? true : false;
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                $detail->value = $requestValue;
                                $detail->value_pa = $requestValue;

                                if((($isBelanja && $requestValue > 0) || (!$isBelanja && $requestValue != 0)) && !$detail->employee_id){
                                    $detail->employee_id = $request->user()->pegawai->id;
                                }
                                
                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($rkatAktif,$detail);
                            }
                        }
                        elseif(in_array($role,['kepsek','etl','etm','ctl','ftm','sdmm','ctm','ekispv','layspv','aspv'])){
                            // Inti function
                            $rkatAktifDetailFilter = $rkatAktifDetail->where(function($query){
                                $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();
                            foreach($rkatAktifDetailFilter as $detail){
                                $isBelanja = $detail->akun->kategori->parent->name == 'Belanja' ? true : false;
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                $detail->value = $requestValue;
                                $detail->value_pa = $requestValue;

                                if((($isBelanja && $requestValue > 0) || (!$isBelanja && $requestValue != 0)) && !$detail->employee_id){
                                    $detail->employee_id = $request->user()->pegawai->id;
                                }
                                
                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($rkatAktif,$detail);
                            }
                        }
                    }
                }
                return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('rkat.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rkat\Rkat  $rkat
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rkat $rkat)
    {
        //
    }

    /**
     * Accept all resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function acceptAll(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = $this->hasBudgetingType($jenis, $request->user());

        if($jenisAktif){
            $rkat = Rkat::select('id','budgeting_budgeting_type_id')->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            })->with('jenisAnggaranAnggaran',function($q){$q->select('id','budgeting_id')->with('anggaran:id');})->get();
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            if($request->user()->pegawai->unit_id == '5'){
                $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                    $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                })->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id)->first();
            }
            else{
                $checkAnggaran = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                    $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                })->where('unit_id',$request->user()->pegawai->unit_id)->first();
            }
            if(in_array($request->user()->role->name,['pembinayys','ketuayys','direktur']) || (in_array($request->user()->role->name,['fam','faspv','am','akunspv']) && !$checkAnggaran)){
                $anggaranAktif = $anggaranAktif->whereIn('id',$rkat->pluck('jenisAnggaranAnggaran.anggaran')->unique()->pluck('id'));
            }
            $anggaranAktif = $anggaranAktif->first();

            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']) && !$checkAnggaran){
                $anggaranAktif = null;
            }

            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                $rkatAktif = !$isYear ? $anggaranAktif->rkat()->where('academic_year_id', $tahun->id)->aktif()->latest()->first() : $anggaranAktif->rkat()->where('year', $tahun)->aktif()->latest()->first();

                if($rkatAktif){
                    $isPa = $anggaranAktif->anggaran->acc_position_id == $request->user()->pegawai->position_id ? true : false;

                    $rkatAktifDetail = $rkatAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1);
                    });
                    if($rkatAktifDetail->count() > 0){
                        if($role == 'direktur'){
                            // Inti function
                            $rkatAktifDetailClone = clone $rkatAktifDetail;
                            foreach($rkatAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                $detail->value_director = $detail->value;
                                $detail->save();
                            }

                            // Accept unaccepted value
                            $rkatAktifDetail->whereNull([
                                'director_acc_id',
                                'director_acc_status_id',
                                'director_acc_time',
                            ])->update([
                                'director_acc_id' => $request->user()->pegawai->id,
                                'director_acc_status_id' => 1,
                                'director_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            // Accept RKAT
                            $rkatAktif->update([
                                'director_acc_id' => $request->user()->pegawai->id,
                                'director_acc_status_id' => 1,
                                'director_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            // Generate APBY
                            $this->generateApby($anggaranAktif,$rkatAktif,$tahun);
                        }
                        elseif($role == 'am'){
                            // Inti function
                            $rkatAktifDetailClone = clone $rkatAktifDetail;
                            $rkatAktifDetailFilter = $rkatAktifDetailClone->where(function($query){
                                $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();

                            foreach($rkatAktifDetailFilter as $detail){
                                if($isPa) $detail->value_pa = $detail->value;
                                $detail->value_fam = $detail->value;
                                $detail->save();
                            }

                            $rkatAktifDetailUpdate = clone $rkatAktifDetail;

                            // Accept 0 value
                            $rkatAktifDetailUpdate->whereNull([
                                'employee_id',
                                'finance_acc_id',
                                'finance_acc_status_id',
                                'finance_acc_time',
                            ])->update([
                                'employee_id' => $request->user()->pegawai->id,
                                'finance_acc_id' => $request->user()->pegawai->id,
                                'finance_acc_status_id' => 1,
                                'finance_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            // Accept unaccepted value
                            $rkatAktifDetail->whereNull([
                                'finance_acc_id',
                                'finance_acc_status_id',
                                'finance_acc_time',
                            ])->update([
                                'finance_acc_id' => $request->user()->pegawai->id,
                                'finance_acc_status_id' => 1,
                                'finance_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            // Accept RKAT
                            $rkatAktif->update([
                                'finance_acc_id' => $request->user()->pegawai->id,
                                'finance_acc_status_id' => 1,
                                'finance_acc_time' => Date::now('Asia/Jakarta')
                            ]);
                        }
                    }
                }
                return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);                    
            }
            else return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('rkat.index');
    }

    /**
     * Update the specified resources from storage.
     *
     * @param  \App\Models\Rkat\Rkat        $rkatAktif
     * @param  \App\Models\Rkat\RkatDetail  $detail
     */
    public function updateParentValues(Rkat $rkatAktif, RkatDetail $detail)
    {
        $childDetail = $detail;
        for($i = 0;$i < $detail->akun->parentsCount;$i++){
            $parent = $rkatAktif->detail()->whereHas('akun',function($query)use($childDetail){
                $query->where(['code' => $childDetail->akun->parentCode,'is_fillable' => 0,'account_category_id' => $childDetail->akun->kategori->id]);
            })->first();
            if($parent){
                $childs = $rkatAktif->detail()->whereHas('akun',function($query)use($parent){
                    $query->where('code','LIKE',$parent->akun->code.'.%')->where('account_category_id',$parent->akun->kategori->id);
                })->with('akun')->get()->pluck('akun')->where('level',$parent->akun->level+1)->pluck('id')->unique();
                $childsValue = $rkatAktif->detail()->whereHas('akun',function($query)use($childs){
                    $query->whereIn('id',$childs);
                })->sum('value');
                $parent->value = $childsValue;
                $parent->save();

                $parent->fresh();

                $childDetail = $parent;
            }
        }
    }

    /**
     * Generate the specified resources from storage.
     *
     * @param  \App\Models\Anggaran\JenisAnggaranAnggaran   $anggaranAktif
     * @param  \App\Models\Rkat\Rkat                        $rkatAktif
     */
    public function generateApby($anggaranAktif, $rkatAktif, $tahun)
    {
        $isYear = strlen($tahun) == 4 ? true : false;

        $yearAttr = $isYear ? 'year' : 'academic_year_id';

        $apby = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('revision',$rkatAktif->revision);

        if($apby->count() < 1){
            $apby = new Apby();
            if(!$isYear)
                $apby->academic_year_id = $rkatAktif->academic_year_id;
            else
                $apby->year = $rkatAktif->year;
            $apby->budgeting_budgeting_type_id = $rkatAktif->budgeting_budgeting_type_id;

            // Bypass validation section
            $apby->finance_acc_id = $rkatAktif->finance_acc_id;
            $apby->finance_acc_status_id = $rkatAktif->finance_acc_status_id;
            $apby->finance_acc_time = $rkatAktif->finance_acc_time;
            $apby->director_acc_id = $rkatAktif->director_acc_id;
            $apby->director_acc_status_id = $rkatAktif->director_acc_status_id;
            $apby->director_acc_time = $rkatAktif->director_acc_time;
            // -- End of bypass validation section

            $apby->revision = $rkatAktif->revision;
            $apby->save();

            $apby->fresh();
        }
        else $apby = $apby->latest()->first();

        // Get last revision
        $apbyLastRevision = null;
        if($apby->revision > 1 && $anggaranAktif->jenis->isKso){
            $apbyLastRevision = $anggaranAktif->apby()->where([
                'revision' => ($apby->revision-1),
                'is_active' => 0,
                $yearAttr => ($yearAttr == 'year' ? $rkatAktif->year : $rkatAktif->academic_year_id)
            ])->latest()->first();
        }

        if($apby->detail()->count() < 1){
            foreach($rkatAktif->detail as $d){

                // $apby->detail()->save(ApbyDetail::create([
                //     'apby_id' => $apby->id,
                //     'account_id' => $d->account_id,
                //     'value' => $d->value,
                //     'value_rkat' => $d->value,
                // ]));

                $lastDetail = null;

                if($apby->revision > 1 && $apbyLastRevision){
                    $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($d){
                        $query->where('id',$d->akun->id);
                    })->first();
                }

                $apby->detail()->save(ApbyDetail::create([
                    'apby_id' => $apby->id,
                    'account_id' => $d->account_id,
                    'value' => $d->value,
                    'value_rkat' => $d->value,
                    'value_faspv' => $d->value_faspv,
                    'value_fam' => $d->value_fam,
                    'value_director' => $d->value_director,
                    'used' => $lastDetail ? $lastDetail->used : 0,
                    'balance' => $lastDetail ? ($d->value-$lastDetail->used) : ($anggaranAktif->jenis->isKso ? $d->value : 0),
                    'employee_id' => $d->employee_id,
                    'edited_employee_id' => $d->edited_employee_id,
                    'edited_status_id' => $d->edited_status_id,
                    'finance_acc_id' => $d->finance_acc_id,
                    'finance_acc_status_id' => $d->finance_acc_status_id,
                    'finance_acc_time' => $d->finance_acc_time,
                    'director_acc_id' => $d->director_acc_id,
                    'director_acc_status_id' => $d->director_acc_status_id,
                    'director_acc_time' => $d->director_acc_time
                ]));
            }

            if($anggaranAktif->jenis->isKso){
                $totalAnggaran = $apby->detail()->whereHas('akun',function($query){
                    $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                        $q->where('name','Belanja');
                    });
                });

                // Accept APBY
                $apby->update([
                    'total_value' => $totalAnggaran->sum('value'),
                    'total_used' => $totalAnggaran->sum('used'),
                    'total_balance' => $totalAnggaran->sum('balance')
                ]);
            }
        }
    }
    
    /**
     * Check whether specified resource from storage has access.
     *
     * @return \Illuminate\Http\Response
     */
    public function hasBudgetingType($jenis, $user)
    {
        $role = $user->role->name;
        
        $jenisAnggaran = JenisAnggaran::all();
        $jenisAnggaranCount = null;
        foreach($jenisAnggaran as $j){
            $anggaranCount = $j->anggaran();
            if(!in_array($user->role->name,['pembinayys','ketuayys','direktur','fam','am','akunspv'])){
                if($user->pegawai->unit_id == '5'){
                    // if(in_array($role,['etl','ctl'])){
                    //     $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($user){$q->where('acc_position_id',$user->pegawai->jabatan->first()->id);});
                    // }
                    // else{
                        $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($user){$q->where('position_id',$user->pegawai->jabatan->group()->first()->id);});
                    // }
                }
                else{
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($user){$q->where('unit_id',$user->pegawai->unit_id);});
                }
            }
            $anggaranCount = $anggaranCount->count();
            if($jenisAnggaranCount){
                $jenisAnggaranCount = $jenisAnggaranCount->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
            else{
                $jenisAnggaranCount = collect()->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
        }
        $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
        
        return $jenisAktif;
    }

    /**
     * Check the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkRole($anggaran,$role){
        // Sesuai penempatan
        // $rolesCollection = collect([
        //     ['unit' => 1, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
        //     ['unit' => 2, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
        //     ['unit' => 3, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
        //     ['unit' => 4, 'position' => null, 'roles' => ['kepsek','wakasek','keu']],
        //     ['unit' => 5, 'position' => 18, 'roles' => ['etl','etm']],
        //     ['unit' => 5, 'position' => 23, 'roles' => ['ctl','ctm']],
        //     ['unit' => 5, 'position' => 28, 'roles' => ['am','fam','faspv','fas']],
        //     ['unit' => 5, 'position' => 32, 'roles' => ['am','aspv']],
        //     ['unit' => 5, 'position' => 36, 'roles' => ['am','ftm','ftspv']],
        //     ['unit' => 5, 'position' => 48, 'roles' => ['etl','ekim','ekipv']],
        //     ['unit' => 5, 'position' => 50, 'roles' => ['etl','sdmm','sdmspv']],
        //     ['unit' => 5, 'position' => 53, 'roles' => ['ctl','layspv']],
        //     ['unit' => 5, 'position' => 57, 'roles' => ['am','akunspv']]
        // ]);

        // Temp
        $rolesCollection = collect([
            ['name' => 'TKIT', 'unit' => 1, 'position' => null, 'roles' => ['kepsek']],
            ['name' => 'SDIT', 'unit' => 2, 'position' => null, 'roles' => ['kepsek']],
            ['name' => 'SMPIT', 'unit' => 3, 'position' => null, 'roles' => ['kepsek']],
            ['name' => 'SMAIT', 'unit' => 4, 'position' => null, 'roles' => ['kepsek']],
            ['name' => 'Education Team', 'unit' => 5, 'position' => 18, 'roles' => ['etl','etm']],
            ['name' => 'Customer Team', 'unit' => 5, 'position' => 23, 'roles' => ['ctl','ctm']],
            ['name' => 'Finance and Accounting', 'unit' => 5, 'position' => 28, 'roles' => ['am','fam','faspv','fas']],
            ['name' => 'Administration, Legal, and IT', 'unit' => 5, 'position' => 32, 'roles' => ['am','aspv']],
            ['name' => 'Facilities', 'unit' => 5, 'position' => 36, 'roles' => ['am','ftm','ftspv','fts']],
            ['name' => 'Divisi Edukasi', 'unit' => 5, 'position' => 18, 'roles' => ['etl']],
            ['name' => 'Divisi Layanan', 'unit' => 5, 'position' => 23, 'roles' => ['ctl']],
            ['name' => 'Divisi Umum', 'unit' => 5, 'position' => 32, 'roles' => ['am','faspv','akunspv']]
        ]);

        if($anggaran->unit_id == 5){
            $roles = $rolesCollection->where('name',$anggaran->name)->where('unit',$anggaran->unit_id)->where('position',$anggaran->position_id)->first();
        }
        else{
            $roles = $rolesCollection->where('unit',$anggaran->unit_id)->first();
        }

        if($roles && in_array($role,$roles['roles'])) return true;
        else return false;
    }
}
