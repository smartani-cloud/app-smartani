<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Anggaran\JenisAnggaranAnggaran;
use App\Models\Anggaran\KategoriAkun;
use App\Models\Anggaran\KategoriAnggaran;
use App\Models\Apby\Apby;
use App\Models\Apby\ApbyDetail;
use App\Models\Apby\ApbyTransferLog;
use App\Models\Bbk\Bbk;
use App\Models\Lppa\Lppa;
use App\Models\Ppa\Ppa;
use App\Models\Rkat\Rkat;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use Illuminate\Http\Request;

use Auth;
use DB;
use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ApbyController extends Controller
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
            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am'])){
                if($request->user()->pegawai->unit_id == '5'){
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id);});
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
        
        $jenisAktif = $kategoriAanggaran = $kategori = $apby = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $yearAttr = $checkApby = $anggarans = $accounts = null;
        $yearsCount = $academicYearsCount = 0;
        $perubahan = $changeYear = $nextYear = false;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif) return redirect()->route('apby.index');

            $queryApby = Apby::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });

            if($queryApby->count() > 0){
                $years = clone $queryApby;
                $yearsCount = $years->whereNotNull('year')->aktif()->count();
                $years = $years->whereNotNull('year')->aktif()->orderBy('year')->pluck('year')->unique();

                $academicYears = clone $queryApby;
                $academicYearsCount = $academicYears->has('tahunPelajaran')->aktif()->count();
                $academicYears = $academicYears->has('tahunPelajaran')->with('tahunPelajaran:id,academic_year')->aktif()->get()->sortBy('tahunPelajaran.academic_year')->pluck('academic_year_id')->unique();

                $latest = clone $queryApby;
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
                if($tahun){
                    $tahun = str_replace("-","/",$tahun);
                    $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                }
                else{
                    // Default Value
                    $tahun = TahunAjaran::where('is_finance_year',1)->latest()->first();
                }
                if(!$tahun) return redirect()->route('apby.index');
            }
            else{
                // Default Value
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
                    return redirect()->route('apby.index');
                }
            }

            if($jenisAktif){
                $kategoriAnggaran = KategoriAnggaran::select('id','name')->whereHas('anggarans.jenisAnggaran',function($q)use($jenisAktif){
                    $q->where('budgeting_type_id',$jenisAktif->id);
                })->get();

                $kategori = KategoriAkun::all();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apby = clone $queryApby;
                $apby = $apby->whereHas('jenisAnggaranAnggaran.tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->aktif()->get();

                $checkApby = Apby::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                });
                $anggaransQuery = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
                $anggaranIds = clone $anggaransQuery;
                $anggaranIds = $anggaranIds->select('id')->get()->pluck('id')->unique();
                $checkPpa = Ppa::whereIn('budgeting_budgeting_type_id',$anggaranIds)->submitted();
                $checkBbk = Bbk::where('budgeting_type_id',$jenisAktif->id);

                if($isKso){
                    $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbkAcc = clone $checkBbk;
                    $checkBbkAcc = $checkBbkAcc->where('director_acc_status_id',1);
                    $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                        $query->where([
                            $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                            'director_acc_status_id' => 1
                        ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                    });
                }
                else{
                    $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbkAcc = clone $checkBbk;
                    $checkBbkAcc = $checkBbkAcc->where('president_acc_status_id',1);
                    $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                        $query->where([
                            $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                            'director_acc_status_id' => 1
                        ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                    });
                }
                $checkApby = $checkApby->aktif();
                $checkPpaAcc = clone $checkPpa;
                $checkPpaAcc = $checkPpaAcc->where('director_acc_status_id',1);
                $checkLppaAcc = clone $checkLppa;
                $checkLppaAcc = $checkLppaAcc->where('finance_acc_status_id',1);
                
                if($checkApby->count() > 0){
                    if(($latest->year && $isYear && $tahun == date('Y')) || (!$latest->year && !$isYear && $tahun->is_finance_year == 1))
                        $perubahan = true;
                }

                if(!$latest->year && !$isYear && $tahun->is_finance_year == 1){
                    $nextYearCheck = TahunAjaran::where('academic_year_start','>',$tahun->academic_year_start)->min('academic_year_start');
                    $nextYear = $nextYearCheck ? true : false;
                    if($checkApby->count() > 0 && $checkPpaAcc->count() >= $checkPpa->count() && $checkBbkAcc->count() >= $checkBbk->count() && $checkLppaAcc->count() >= $checkLppa->count() && (!$latest->year && !$isYear && $tahun->is_finance_year == 1)){
                    //if($checkApby->count() > 0){
                        $changeYear = true;
                    }
                }

                $sumExportable = false;
                $sumApby = clone $checkApby;
                $countSumApby = $sumApby->whereHas('detail.akun',function($q){
                    $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                })->count();
                if($countSumApby > 0) $sumExportable = true;

                // Sum Dataset
                $isShowTotal = true;
                if($isShowTotal){
                    $totalPenerimaan = $totalBelanja = $saldoOperasional = $saldoPembiayaan = $totalAkhir = 0;
                }
                
                foreach($kategori as $k){
                    $kategoriValue = 0;
                    $children = $k->children();
                    
                    if($children->count() > 0){
                        if($isShowTotal){
                            $firstChildren = clone $children;
                            $firstChildren = $firstChildren->first();
    
                            if($firstChildren->name != $k->name){
                                $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){
                                        $q->where('budgeting_type_id',$jenisAktif->id);
                                    })->latest()->aktif();
                                })->whereHas('akun.kategori.parent',function($q)use($k){$q->where('name',$k->name);});
                                if($apbyDetail->count() > 0){
                                    $kategoriValue = $apbyDetail->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');
    
                                    if($k->name == 'Pendapatan'){
                                        $totalPenerimaan = $kategoriValue;
                                        $saldoOperasional += $totalPenerimaan;
                                    }
                                    if($k->name == 'Belanja'){
                                        $totalBelanja = $kategoriValue;
                                        $saldoOperasional -= $totalBelanja;
                                    }
                                    if($k->name == 'Pembiayaan'){
                                        $saldoPembiayaan = $kategoriValue;
                                        $totalAkhir = $saldoOperasional+$saldoPembiayaan;
                                    }
                                }
                            }
                        }

                        $childrens = $children->get();

                        foreach($childrens as $c){
                            $subAnggarans = clone $anggaransQuery;
                            $subAnggarans = $subAnggarans->whereHas('apby',function($q)use($yearAttr,$tahun,$c,$isShowTotal){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->when(!$isShowTotal,function($q){
                                    return $q->whereHas('detail.akun',function($q){
                                        $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                    });
                                })->whereHas('detail.akun.kategori',function($q)use($c){
                                    $q->where('name',$c->name);
                                })->aktif();
                            });
                            if($subAnggarans->count() > 0){
                                foreach($subAnggarans->get() as $a){
                                    $latestActiveApby = $a->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->when(!$isShowTotal,function($q){
                                        return $q->whereHas('detail.akun',function($q){
                                            $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                        });
                                    })->whereHas('detail.akun.kategori',function($q)use($c){
                                        $q->where('name',$c->name);
                                    })->latest()->aktif()->first();

                                    if($latestActiveApby){
                                        $apbyDetail = $latestActiveApby->detail()->whereHas('akun',function($q){
                                            $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                        })->whereHas('akun.kategori',function($q)use($c){
                                            $q->where('name',$c->name);
                                        });
                                            
                                        if($apbyDetail->count() > 0){
                                            foreach($apbyDetail->with('akun:id,code,name,sort_order')->get()->sortBy('akun.sort_order')->all() as $d){
                                                $account = collect([
                                                    [
                                                        'id' => $d->akun->id,
                                                        'code' => $d->akun->code,
                                                        'name' => $d->akun->name,
                                                        'value' => $d->value,
                                                        'valueWithSeparator' => $d->valueWithSeparator 
                                                    ]
                                                ]);
                                                if($accounts){
                                                    $accounts = $accounts->concat($account);
                                                }
                                                else{
                                                    $accounts = $account;
                                                }
                                            }
                                        }
                                        
                                        if(in_array($c->name,['Penerimaan Pembiayaan','Pengeluaran Pembiayaan'])){
                                            $sumApbyDetails = $latestActiveApby->detail()->whereHas('akun.kategori',function($q)use($c){
                                                $q->where('name',$c->name);
                                            })->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');
                                            $account = collect([
                                                [
                                                    'id' => 'c-'.$c->id,
                                                    'code' => null,
                                                    'name' => 'JUMLAH '.strtoupper($c->name),
                                                    'value' => $sumApbyDetails,
                                                    'valueWithSeparator' => number_format($sumApbyDetails, 0, ',', '.')
                                                ]
                                            ]);
                                            if($accounts){
                                                $accounts = $accounts->concat($account);
                                            }
                                            else{
                                                $accounts = $account;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if($isShowTotal && in_array($k->name,['Pendapatan','Belanja','Pembiayaan'])){
                        $account = collect([
                            [
                                'id' => 'k-'.$k->id,
                                'code' => null,
                                'name' => ($k->name == "Pembiayaan" ? 'SALDO ' : 'TOTAL ').strtoupper($k->name),
                                'value' => $kategoriValue,
                                'valueWithSeparator' => number_format($kategoriValue, 0, ',', '.')
                            ]
                        ]);
                        if($accounts){
                            $accounts = $accounts->concat($account);
                        }
                        else{
                            $accounts = $account;
                        }

                        if($k->name == "Belanja"){
                            $account = collect([
                                [
                                    'id' => 's-'.$k->id,
                                    'code' => null,
                                    'name' => 'SALDO OPERASIONAL',
                                    'value' => $saldoOperasional,
                                    'valueWithSeparator' => number_format($saldoOperasional, 0, ',', '.')
                                ]
                            ]);
                            if($accounts){
                                $accounts = $accounts->concat($account);
                            }
                            else{
                                $accounts = $account;
                            }
                        }
                    }
                }
                
                if($isShowTotal){
                    $account = collect([
                        [
                            'id' => 'sum',
                            'code' => null,
                            'name' => 'TOTAL SALDO OPERASIONAL DAN PEMBIAYAAN',
                            'value' => $totalAkhir,
                            'valueWithSeparator' => number_format($totalAkhir, 0, ',', '.')
                        ]
                    ]);
                    if($accounts){
                        $accounts = $accounts->concat($account);
                    }
                    else{
                        $accounts = $account;
                    }
                }
                
                $anggarans = clone $anggaransQuery;
                $anggarans = $anggaransQuery->whereHas('apby',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('detail.akun',function($q){
                        $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                    })->aktif();
                })->get();

                if($anggaran){
                    $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$apby->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id'))->first();
                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                        $apbyAktif = !$isYear ? $anggaranAktif->apby()->where('academic_year_id',$tahun->id)->latest()->aktif()->first() : $anggaranAktif->apby()->where('year',$tahun)->latest()->aktif()->first();

                        //if($apbyAktif || (!$apbyAktif && (!$isYear && $tahun->is_finance_year == 1) || ($isYear && $tahun == date('Y')))){
                        if($apbyAktif){
                            // Inti controller

                            $totalAnggaran = 0;

                            // Counter
                            $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'))->aktif();
                            });

                            $totalPendapatan = clone $apbyDetail;
                            $totalPendapatan = $totalPendapatan->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Pendapatan');
                                });
                            })->sum('value');

                            $totalPembiayaan = clone $apbyDetail;
                            $totalPembiayaan = $totalPembiayaan->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Pembiayaan');
                                });
                            })->sum('value');

                            $totalBelanja = clone $apbyDetail;
                            $totalBelanja = $totalBelanja->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Belanja');
                                });
                            })->sum('value');

                            if($apbyAktif && $apbyAktif->detail()->count() > 0){
                                $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
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

                            if(in_array($role,['ketuayys','direktur','fam','faspv','am']))
                                $folder = $role;
                            else $folder = 'read-only';

                            if($isKso)
                                return view('keuangan.'.$folder.'.apb_kso_detail', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','apby','years','academicYears','anggaranAktif','apbyAktif','kategori','total'));
                            else
                                return view('keuangan.'.$folder.'.apby_detail', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','apby','years','academicYears','anggaranAktif','apbyAktif','kategori','total'));
                        }
                        else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                    else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
            }
        }

        // if($jenis && $isKso)
        //     return view('keuangan.read-only.apb_kso_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','isYear','checkApby','apby','years','academicYears','perubahan','changeYear','nextYear'));
        // else
            return view('keuangan.read-only.apby_index', compact('jenisAnggaran','jenisAktif','kategoriAnggaran','kategori','tahun','tahunPelajaran','yearAttr','isYear','checkApby','apby','years','academicYears','perubahan','changeYear','nextYear','sumExportable','anggaransQuery','anggarans','accounts'));
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
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function show(Apby $apby)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            })->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif();
                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id', 1) : $apbyAktif->where('president_acc_status_id', 1);
                $apbyAktif = $apbyAktif->first();

                if($apbyAktif){
                    $apbyAktifDetail = $apbyAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1)->whereHas('kategori.parent',function($q){$q->where('name','Belanja');
                        });
                    });
                    $apbyAktifDetailFilter = clone $apbyAktifDetail;
                    $apbyAktifDetailFilter = $apbyAktifDetailFilter->where('id',$request->id)->where('balance','>',0);
                    if($apbyAktifDetailFilter->count() > 0){
                        if($role == 'am'){
                            // Inti function
                            $details = clone $apbyAktifDetail;
                            $details = $details->get();

                            $detail = $apbyAktifDetailFilter->first();

                            return view('keuangan.'.$role.'.apby_ubah', compact('jenisAktif','tahun','isYear','anggaranAktif','yearAttr','details','detail'));
                        }
                    }
                    return "Ups! Unable to load data. There is no editable account.";
                }
                else return "Ups! Unable to load data. Inactive budgeting.";
            }
            else return "Error! Unable to load data. Invalid budgeting.";
        }
        return "Error! Unable to load data. Invalid budgeting type.";
    }

    /**
     * Transfer the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function transfer(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            })->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif();
                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id', 1) : $apbyAktif->where('president_acc_status_id', 1);
                $apbyAktif = $apbyAktif->first();

                if($apbyAktif){
                    $apbyAktifDetail = $apbyAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1)->whereHas('kategori.parent',function($q){$q->where('name','Belanja');
                        });
                    });
                    $apbyAktifDetailFilter = clone $apbyAktifDetail;
                    $apbyAktifDetailFilter = $apbyAktifDetailFilter->where('id',$request->id)->where('balance','>',0);
                    if($apbyAktifDetailFilter->count() > 0){
                        if($role == 'am'){
                            // Inti function
                            $messages = [
                                'account.required' => 'Mohon pilih salah satu akun anggaran tujuan',
                                'amount.required' => 'Mohon masukkan jumlah nominal transfer',
                            ];

                            $this->validate($request, [
                                'account' => 'required',
                                'amount' => 'required'
                            ], $messages);

                            $destination = clone $apbyAktifDetail;
                            $destination = $destination->where('id','!=',$request->id)->where('id',$request->account)->first();

                            $detail = $apbyAktifDetailFilter->first();

                            if($destination && $detail){
                                $requestValue = (int)str_replace('.','',$request->amount);
                                if($requestValue > 0 && $requestValue <= $detail->balance){
                                    $log = new ApbyTransferLog();
                                    $log->from_detail_id = $detail->id;
                                    $log->from_value = $detail->value;
                                    $log->from_balance = $detail->balance;
                                    $log->to_detail_id = $destination->id;
                                    $log->to_value = $destination->value;
                                    $log->to_balance = $destination->balance;
                                    $log->amount = $requestValue;
                                    $log->employee_id = $request->user()->pegawai->id;

                                    $apbyAktif->transferLogs()->save($log);

                                    $destination->value += $requestValue;
                                    $detail->value -= $requestValue;

                                    $destination->balance += $requestValue;
                                    $detail->balance -= $requestValue;

                                    $destination->save();
                                    $detail->save();

                                    $destination->fresh();
                                    $detail->fresh();

                                    $this->updateParentValues($apbyAktif,$destination);
                                    $this->updateParentValues($apbyAktif,$detail);

                                    Session::flash('success', 'Transfer nominal saldo sebesar '.$request->amount.' ke '.$destination->akun->codeName.' berhasil.');
                                }
                                else Session::flash('danger', 'Transfer nominal saldo akun anggaran gagal. Pastikan jumlah nominal tidak melebihi saldo akun asal');
                            }
                            else Session::flash('danger', 'Transfer nominal saldo akun anggaran gagal.');
                        }
                    }
                    else Session::flash('danger', 'Transfer nominal saldo akun anggaran tidak dapat dilakukan.');
                }
                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('apby.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            })->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->first();

                if($apbyAktif){
                    $apbyAktifDetail = $apbyAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1);
                    });
                    if($apbyAktifDetail->count() > 0){
                        if($role == 'ketuayys' && !$isKso){
                            // Inti function
                            $apbyAktifDetailClone = clone $apbyAktifDetail;

                            // Get last revision
                            if($apbyAktif->revision > 1){
                                $apbyLastRevision = $anggaranAktif->apby()->where([
                                    ['revision', '<', $apbyAktif->revision],
                                    ['is_active', '=', 0],
                                    [$yearAttr, '=', ($yearAttr == 'year' ? $tahun : $tahun->id)]
                                ])->latest()->first();
                            }

                            foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                $detail->value = $requestValue;
                                $detail->value_president = $requestValue;
                                if($apbyAktif->revision > 1 && $apbyLastRevision){
                                    $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($detail){
                                        $query->where('id',$detail->akun->id);
                                    })->first();
                                    $detail->used = $lastDetail ? $lastDetail->used : 0;
                                    $detail->balance = $lastDetail ? ($requestValue-$lastDetail->used) : $requestValue;
                                }
                                else{
                                    $detail->balance = $requestValue;
                                }
                                if($requestValue > 0){
                                    if(isset($detail->value_director)){
                                        if($detail->value_director != $requestValue){
                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                        $detail->president_acc_id = $request->user()->pegawai->id;
                                        $detail->president_acc_status_id = 1;
                                        $detail->president_acc_time = Date::now('Asia/Jakarta');
                                    }
                                }
                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($apbyAktif,$detail);
                            }

                            if(isset($request->validate) && $request->validate == 'validate'){
                                $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                                // Accept unaccepted value
                                $apbyAktifDetailUpdate->whereNull([
                                    'president_acc_id',
                                    'president_acc_status_id',
                                    'president_acc_time',
                                ])->update([
                                    'president_acc_id' => $request->user()->pegawai->id,
                                    'president_acc_status_id' => 1,
                                    'president_acc_time' => Date::now('Asia/Jakarta')
                                ]);

                                $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                                    $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                        $q->where('name','Belanja');
                                    });
                                });

                                // Accept APBY
                                $apbyAktif->update([
                                    'total_value' => $totalAnggaran->sum('value'),
                                    'total_used' => $totalAnggaran->sum('used'),
                                    'total_balance' => $totalAnggaran->sum('balance'),
                                    'president_acc_id' => $request->user()->pegawai->id,
                                    'president_acc_status_id' => 1,
                                    'president_acc_time' => Date::now('Asia/Jakarta')
                                ]);

                                // Sync with RKAT Detail Values
                                $this->syncRkat($anggaranAktif,$apbyAktif,$tahun);
                            }
                        }
                        elseif($role == 'direktur'){
                            // Inti function
                            if($isKso){
                                $apbyAktifDetailClone = clone $apbyAktifDetail;

                                // Get last revision
                                if($apbyAktif->revision > 1){
                                    $apbyLastRevision = $anggaranAktif->apby()->where([
                                        ['revision', '<', $apbyAktif->revision],
                                        ['is_active', '=', 0],
                                        [$yearAttr, '=', ($yearAttr == 'year' ? $tahun : $tahun->id)]
                                    ])->latest()->first();
                                }

                                foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                    $inputName = 'value-'.$detail->id;
                                    $requestValue = (int)str_replace('.','',$request->{$inputName});
                                    $detail->value = $requestValue;
                                    $detail->value_director = $requestValue;
                                    if($apbyAktif->revision > 1 && $apbyLastRevision){
                                        $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($detail){
                                            $query->where('id',$detail->akun->id);
                                        })->first();
                                        $detail->used = $lastDetail ? $lastDetail->used : 0;
                                        $detail->balance = $lastDetail ? ($requestValue-$lastDetail->used) : $requestValue;
                                    }
                                    else{
                                        $detail->balance = $requestValue;
                                    }
                                    if($requestValue > 0){
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

                                    $this->updateParentValues($apbyAktif,$detail);
                                }

                                if(isset($request->validate) && $request->validate == 'validate'){
                                    $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                                    // Accept unaccepted value
                                    $apbyAktifDetailUpdate->whereNull([
                                        'director_acc_id',
                                        'director_acc_status_id',
                                        'director_acc_time',
                                    ])->update([
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                                        $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                            $q->where('name','Belanja');
                                        });
                                    });

                                    // Accept APBY
                                    $apbyAktif->update([
                                        'total_value' => $totalAnggaran->sum('value'),
                                        'total_used' => $totalAnggaran->sum('used'),
                                        'total_balance' => $totalAnggaran->sum('balance'),
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    // Sync with RKAT Detail Values
                                    $this->syncRkat($anggaranAktif,$apbyAktif,$tahun);
                                }
                            }
                            else{
                                $apbyAktifDetailFilter = $apbyAktifDetail->where(function($query){
                                    $query->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                })->with('akun')->get()->sortByDesc('akun.level')->all();
                                foreach($apbyAktifDetailFilter as $detail){
                                    $inputName = 'value-'.$detail->id;
                                    $requestValue = (int)str_replace('.','',$request->{$inputName});
                                    $detail->value = $requestValue;
                                    $detail->value_director = $requestValue;
                                    if($requestValue > 0){
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

                                    $this->updateParentValues($apbyAktif,$detail);
                                }

                                if($apbyAktifDetail->whereNull('director_acc_status_id')->count() < 1){
                                    $apbyAktif->director_acc_id = $request->user()->pegawai->id;
                                    $apbyAktif->director_acc_status_id = 1;
                                    $apbyAktif->director_acc_time = Date::now('Asia/Jakarta');
                                    $apbyAktif->save();
                                }
                            }
                        }
                        elseif($role == 'am'){
                            // Inti function
                            $apbyAktifDetailFilter = $apbyAktifDetail->where(function($query){
                                $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();
                            foreach($apbyAktifDetailFilter as $detail){
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                $detail->value = $requestValue;
                                $detail->value_fam = $requestValue;
                                if($requestValue > 0){
                                    if(!$detail->employee_id){
                                        $detail->employee_id = $request->user()->pegawai->id;
                                        if($detail->value_rkat != $requestValue){
                                             $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                        // $detail->finance_acc_id = $request->user()->pegawai->id;
                                        // $detail->finance_acc_status_id = 1;
                                        // $detail->finance_acc_time = Date::now('Asia/Jakarta');
                                    }
                                    if(isset($detail->value_faspv)){
                                        if($detail->value_faspv != $requestValue){
                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                        // $detail->finance_acc_id = $request->user()->pegawai->id;
                                        // $detail->finance_acc_status_id = 1;
                                        // $detail->finance_acc_time = Date::now('Asia/Jakarta');
                                    }
                                }
                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($apbyAktif,$detail);
                            }

                            if($apbyAktifDetail->whereNull('finance_acc_status_id')->count() < 1){
                                $apbyAktif->finance_acc_id = $request->user()->pegawai->id;
                                $apbyAktif->finance_acc_status_id = 1;
                                $apbyAktif->finance_acc_time = Date::now('Asia/Jakarta');
                                $apbyAktif->save();
                            }
                        }
                        elseif(in_array($role,['fam','faspv'])){
                            // Inti function
                            // $apbyAktifDetailFilter = $apbyAktifDetail->where(function($query){
                            //     $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                            // })->with('akun')->get()->sortByDesc('akun.level')->all();
                            $apbyAktifDetailFilter = $apbyAktifDetail->where(function($query){
                                $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();
                            foreach($apbyAktifDetailFilter as $detail){
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                $detail->value = $requestValue;
                                $detail->value_faspv = $requestValue;
                                $detail->value_fam = $requestValue;
                                if($requestValue > 0){
                                    if(!$detail->employee_id){
                                        $detail->employee_id = $request->user()->pegawai->id;
                                        if($detail->value_rkat != $requestValue){
                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                    }
                                    $detail->finance_acc_id = $request->user()->pegawai->id;
                                    $detail->finance_acc_status_id = 1;
                                    $detail->finance_acc_time = Date::now('Asia/Jakarta');
                                }
                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($apbyAktif,$detail);
                            }
                        }
                    }
                }
                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('apby.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function destroy(Apby $apby)
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
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            })->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->first();

                if($apbyAktif){
                    $apbyAktifDetail = $apbyAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1);
                    });
                    if($apbyAktifDetail->count() > 0){
                        if($role == 'ketuayys' && !$isKso){
                            // Inti function
                            $apbyAktifDetailClone = clone $apbyAktifDetail;

                            // Get last revision
                            if($apbyAktif->revision > 1){
                                $apbyLastRevision = $anggaranAktif->apby()->where([
                                    ['revision', '<', $apbyAktif->revision],
                                    ['is_active', '=', 0],
                                    [$yearAttr, '=', ($yearAttr == 'year' ? $tahun : $tahun->id)]
                                ])->latest()->first();
                            }

                            // Calculate balances
                            foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                if($apbyAktif->revision > 1 && $apbyLastRevision){
                                    $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($detail){
                                        $query->where('id',$detail->akun->id);
                                    })->first();
                                    $detail->used = $lastDetail ? $lastDetail->used : 0;
                                    $detail->balance = $lastDetail ? ($detail->value-$lastDetail->used) : $detail->value;
                                }
                                else{
                                    $detail->balance = $detail->value;
                                }
                                $detail->value_president = $detail->value;
                                $detail->save();
                            }

                            $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                            // Accept unaccepted value
                            $apbyAktifDetailUpdate->whereNull([
                                'president_acc_id',
                                'president_acc_status_id',
                                'president_acc_time',
                            ])->update([
                                'president_acc_id' => $request->user()->pegawai->id,
                                'president_acc_status_id' => 1,
                                'president_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                                $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                    $q->where('name','Belanja');
                                });
                            });

                            // Accept APBY
                            $apbyAktif->update([
                                'total_value' => $totalAnggaran->sum('value'),
                                'total_used' => $totalAnggaran->sum('used'),
                                'total_balance' => $totalAnggaran->sum('balance'),
                                'president_acc_id' => $request->user()->pegawai->id,
                                'president_acc_status_id' => 1,
                                'president_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            // Sync with RKAT Detail Values
                            $this->syncRkat($anggaranAktif,$apbyAktif,$tahun);
                        }
                        elseif($role == 'direktur'){
                            // Inti function

                            $apbyAktifDetailClone = clone $apbyAktifDetail;
                            if($isKso){
                                // Get last revision
                                if($apbyAktif->revision > 1){
                                    $apbyLastRevision = $anggaranAktif->apby()->where([
                                        ['revision', '<', $apbyAktif->revision],
                                        ['is_active', '=', 0],
                                        [$yearAttr, '=', ($yearAttr == 'year' ? $tahun : $tahun->id)]
                                    ])->latest()->first();
                                }

                                // Calculate balances
                                foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                    if($apbyAktif->revision > 1 && $apbyLastRevision){
                                        $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($detail){
                                            $query->where('id',$detail->akun->id);
                                        })->first();
                                        $detail->used = $lastDetail ? $lastDetail->used : 0;
                                        $detail->balance = $lastDetail ? ($detail->value-$lastDetail->used) : $detail->value;
                                    }
                                    else{
                                        $detail->balance = $detail->value;
                                    }
                                    $detail->value_director = $detail->value;
                                    $detail->save();
                                }
                            }
                            else{
                                $apbyAktifDetailFilter = $apbyAktifDetailClone->where(function($query){
                                    $query->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                })->with('akun')->get()->sortByDesc('akun.level')->all();

                                foreach($apbyAktifDetailFilter as $detail){
                                    $detail->value_director = $detail->value;
                                    $detail->save();
                                }
                            }

                            $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                            // Accept unaccepted value
                            $apbyAktifDetailUpdate->whereNull([
                                'director_acc_id',
                                'director_acc_status_id',
                                'director_acc_time',
                            ])->update([
                                'director_acc_id' => $request->user()->pegawai->id,
                                'director_acc_status_id' => 1,
                                'director_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            if($isKso){
                                $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                                    $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                        $q->where('name','Belanja');
                                    });
                                });

                                // Accept APB-KSO
                                $apbyAktif->update([
                                    'total_value' => $totalAnggaran->sum('value'),
                                    'total_used' => $totalAnggaran->sum('used'),
                                    'total_balance' => $totalAnggaran->sum('balance'),
                                    'director_acc_id' => $request->user()->pegawai->id,
                                    'director_acc_status_id' => 1,
                                    'director_acc_time' => Date::now('Asia/Jakarta')
                                ]);

                                // Sync with RKAT Detail Values
                                $this->syncRkat($anggaranAktif,$apbyAktif,$tahun);
                            }
                            else{
                                // Accept APBY
                                $apbyAktif->update([
                                    'director_acc_id' => $request->user()->pegawai->id,
                                    'director_acc_status_id' => 1,
                                    'director_acc_time' => Date::now('Asia/Jakarta')
                                ]);
                            }
                        }
                        elseif($role == 'am'){
                            // Inti function
                            $apbyAktifDetailClone = clone $apbyAktifDetail;
                            $apbyAktifDetailFilter = $apbyAktifDetailClone->where(function($query){
                                $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();

                            foreach($apbyAktifDetailFilter as $detail){
                                $detail->value_fam = $detail->value;
                                $detail->save();
                            }

                            // Accept 0 value
                            $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                            $apbyAktifDetailUpdate->whereNull([
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
                            $apbyAktifDetail->whereNull([
                                'finance_acc_id',
                                'finance_acc_status_id',
                                'finance_acc_time',
                            ])->update([
                                'finance_acc_id' => $request->user()->pegawai->id,
                                'finance_acc_status_id' => 1,
                                'finance_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            // Accept APBY
                            $apbyAktif->update([
                                'finance_acc_id' => $request->user()->pegawai->id,
                                'finance_acc_status_id' => 1,
                                'finance_acc_time' => Date::now('Asia/Jakarta')
                            ]);
                        }
                    }
                }
                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('apby.index');
    }

    /**
     * Revise the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function revise(Request $request, $jenis, $tahun)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            // Inti Controller
            $checkRkat = Rkat::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $checkApby = Apby::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $anggaranIds = $jenisAktif->anggaran()->select('id')->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->get()->pluck('id')->unique();
            $checkPpa = Ppa::whereIn('budgeting_budgeting_type_id',$anggaranIds)->submitted();
            $checkBbk = Bbk::where('budgeting_type_id',$jenisAktif->id);

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            if($isKso){
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('director_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                });
            }
            else{
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('president_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                });
            }
            $checkRkat = $checkRkat->aktif();
            $checkApby = $checkApby->aktif();
            $checkPpaAcc = clone $checkPpa;
            $checkPpaAcc = $checkPpaAcc->where('director_acc_status_id',1);
            $checkLppaAcc = clone $checkLppa;
            $checkLppaAcc = $checkLppaAcc->where('finance_acc_status_id',1);

            if($checkApby->count() > 0 && (($isYear && $tahun == date('Y')) || (!$isYear && $tahun->is_finance_year == 1))){
            //if($checkApby->count() > 0 && $checkPpaAcc->count() >= $checkPpa->count() && $checkBbkAcc->count() >= $checkBbk->count() && $checkLppaAcc->count() >= $checkLppa->count()){
                $checkRkat->update(['is_active' => 0]);
                $checkApby->update(['is_active' => 0]);

                DB::table('tm_settings')->where('name','budgeting_account_lock_status')->update(['value' => 0]);

                Session::flash('success', $jenisAktif->name.' perubahan berhasil dilakukan');

                return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else{
                Session::flash('danger', $jenisAktif->name.' perubahan gagal dilakukan');

                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
        }

        return redirect()->route('apby.index');
    }

    /**
     * Lock the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lock(Request $request, $jenis, $tahun)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            // Inti Controller
            $checkRkat = Rkat::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $checkApby = Apby::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $anggaranIds = $jenisAktif->anggaran()->select('id')->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->get()->pluck('id')->unique();
            $checkPpa = Ppa::whereIn('budgeting_budgeting_type_id',$anggaranIds)->submitted();
            $checkBbk = Bbk::where('budgeting_type_id',$jenisAktif->id);

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            if($isKso){
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('director_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                });
            }
            else{
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('president_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                });
            }
            $checkRkat = $checkRkat->aktif();
            $checkApby = $checkApby->aktif();
            $checkPpaAcc = clone $checkPpa;
            $checkPpaAcc = $checkPpaAcc->where('director_acc_status_id',1);
            $checkLppaAcc = clone $checkLppa;
            $checkLppaAcc = $checkLppaAcc->where('finance_acc_status_id',1);

            if($checkApby->count() > 0 && $checkPpaAcc->count() >= $checkPpa->count() && $checkBbkAcc->count() >= $checkBbk->count() && $checkLppaAcc->count() >= $checkLppa->count() && (!$isYear && $tahun->is_finance_year == 1)){
            //if($checkApby->count() > 0 && (!$isYear && $tahun->is_finance_year == 1)){
                $nextYear = TahunAjaran::where('academic_year_start','>',$tahun->academic_year_start)->min('academic_year_start');
                if($nextYear){
                    $checkRkat->update(['is_final' => 1]);
                    $checkApby->update(['is_final' => 1]);

                    $tahun->update(['is_finance_year' => 0]);
                    TahunAjaran::where('academic_year_start',$nextYear)->update(['is_finance_year' => 1]);

                    Session::flash('success', 'Tutup tahun '.$jenisAktif->name.' berhasil dilakukan');

                    return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
            }
            else{
                Session::flash('danger', 'Tutup tahun '.$jenisAktif->name.' gagal dilakukan, pastikan tidak ada PPA, PPB, maupun RPPA yang belum selesai prosesnya');

                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
        }

        return redirect()->route('apby.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $jenis, $tahun, $status = null)
    {
        // Pre-defined formats
        $FORMAT_CURRENCY_IDR_SIMPLE = '"Rp"#,##0.00_-';
        $FORMAT_CURRENCY_IDR = 'Rp#,##0_-';
        $FORMAT_ACCOUNTING_IDR = '_("Rp"* #,##0.00_);_("Rp"* \(#,##0.00\);_("Rp"* "-"??_);_(@_)';

        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;

            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            $tahunTitle = !$isYear ? ("Tahun Pelajaran ".$tahun->academic_year) : ("Tahun ".$tahun);

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('Sekolah MUDA')
            ->setLastModifiedBy($request->user()->pegawai->name)
            ->setTitle("Data ".$jenisAktif->name." MUDA ".$tahunTitle)
            ->setSubject($jenisAktif->name." MUDA ".$tahunTitle)
            ->setDescription("Rekapitulasi Data ".$jenisAktif->name."  MUDA ".$tahunTitle)
            ->setKeywords("APB, ".$jenisAktif->name.", MUDA");

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'YAYASAN MUDA INCOMSO'.($isKso? ' - LETRIS KSO' : null))
            ->setCellValue('A2', strtoupper($jenisAktif->fullname))
            ->setCellValue('A4', strtoupper('No. Akun'))
            ->setCellValue('B4', strtoupper('Nama Akun'))
            ->setCellValue('C4', strtoupper('Jumlah'));

            $kolom = $first_kolom = 5;

            $kategori = KategoriAkun::all();

            $totalPenerimaan = $totalBelanja = $saldoOperasional = $saldoPembiayaan = $totalAkhir = 0;

            //Styles
            $borderVertical = [
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                    'vertical' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
            ];

            $totalTopBottomBold = [
                'font' => [
                    'bold' => true
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE
                    ]
                ]
            ];

            if(!isset($status) || $status != 'sum'){
                foreach($kategori as $k){
                    $kategoriValue = 0;
                    $children = $k->children();
                    
                    if($children->count() > 0){
                        $firstChildren = clone $children;
                        $firstChildren = $firstChildren->first();

                        if($firstChildren->name != $k->name){
                            $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){
                                    $q->where('budgeting_type_id',$jenisAktif->id);
                                })->latest()->aktif();
                            })->whereHas('akun.kategori.parent',function($q)use($k){$q->where('name',$k->name);});
                            if($apbyDetail->count() > 0){
                                if($k->name == "Belanja"){
                                    $spreadsheet->getActiveSheet()->setCellValue('A'.$kolom, '5');
                                }

                                $spreadsheet->getActiveSheet()->setCellValue('B'.$kolom, strtoupper($k->name));

                                $styleArray = [
                                    'font' => [
                                        'bold' => true
                                    ],
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                    ]
                                ];

                                $spreadsheet->getActiveSheet()->getStyle('B'.$kolom++)->applyFromArray($styleArray);

                                $kategoriValue = $apbyDetail->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');

                                if($k->name == 'Pendapatan'){
                                    $totalPenerimaan = $kategoriValue;
                                    $saldoOperasional += $totalPenerimaan;
                                }
                                if($k->name == 'Belanja'){
                                    $totalBelanja = $kategoriValue;
                                    $saldoOperasional -= $totalBelanja;
                                }
                                if($k->name == 'Pembiayaan'){
                                    $saldoPembiayaan = $kategoriValue;
                                    $totalAkhir = $saldoOperasional+$saldoPembiayaan;
                                }
                            }
                        }

                        $childrens = $children->get();

                        foreach($childrens as $c){
                            $GrandChildren = $c->children();

                            if($GrandChildren->count() > 0){
                                $firstGrandChildren = clone $GrandChildren;
                                $firstGrandChildren = $firstGrandChildren->first();

                                if($firstChildren->name != $c->name){
                                    $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){
                                            $q->where('budgeting_type_id',$jenisAktif->id);
                                        })->latest()->aktif();
                                    })->whereHas('akun.kategori',function($q)use($c){$q->where('name',$c->name);});
                                    if($apbyDetail->count() > 0){
                                        $spreadsheet->getActiveSheet()->setCellValue('B'.$kolom++, strtoupper($c->name));
                                    }
                                }
                            }
                            else{
                                $anggaran = $jenisAktif->anggaran();
                                if($anggaran->count() > 0){
                                    $anggaran = $anggaran->get();
                                    foreach($anggaran as $a){
                                        $apby = $a->apby()->whereHas('detail.akun.kategori',function($q)use($c){
                                            $q->where('name',$c->name);
                                        })->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->first();

                                        if($apby){
                                            $apbyDetail = $apby->detail()->whereHas('akun.kategori',function($q)use($c){$q->where('name',$c->name);});
                                            $firstApbyDetail = clone $apbyDetail;
                                            $firstApbyDetail = $firstApbyDetail->first();

                                            if($firstApbyDetail->akun->name != strtoupper($c->name) && ($c->name != 'Belanja Anggaran')){
                                                $spreadsheet->getActiveSheet()->setCellValue('B'.$kolom++, strtoupper($c->name));
                                            }

                                            $parentDetail = $parentRow = null;
                                            $apbyDetailClone = clone $apbyDetail;

                                            foreach($apbyDetail->with('akun')->get()->sortBy('akun.sort_order')->all() as $d){
                                                if((count(explode('.',$d->akun->code)) == 2) && $c->name == 'Belanja Anggaran'){
                                                    $parentDetail = $d;
                                                    $apbyDetailCount = clone $apbyDetailClone;
                                                    $parentRow = $kolom+($apbyDetailCount->whereHas('akun',function($q)use($d){$q->where('code','LIKE',$d->akun->code.'.%');})->count())+1;
                                                }

                                                $spreadsheet->getActiveSheet()
                                                ->setCellValueExplicit('A'.$kolom, $d->akun->code, DataType::TYPE_STRING)
                                                ->setCellValue('B'.$kolom, $d->akun->name)
                                                ->setCellValue('C'.$kolom++, $d->akun->is_fillable == 1 ? abs($d->value) : null);

                                                if($parentDetail && $parentRow && ($parentRow == $kolom) && ($c->name == 'Belanja Anggaran')){
                                                    $spreadsheet->getActiveSheet()
                                                    ->setCellValue('B'.$kolom, 'TOTAL '.strtoupper($parentDetail->akun->name))
                                                    ->setCellValue('C'.$kolom,$parentDetail->value);

                                                    $spreadsheet->getActiveSheet()->getStyle('B'.$kolom)->applyFromArray($totalTopBottomBold);

                                                    $kolom += 2;

                                                    $parentDetail = $parentRow = null;
                                                }
                                            }
                                            if($c->name != 'Belanja Anggaran'){
                                                $spreadsheet->getActiveSheet()
                                                ->setCellValue('B'.$kolom, 'JUMLAH '.strtoupper($c->name))
                                                ->setCellValue('C'.$kolom,$apbyDetail->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value'));

                                                $spreadsheet->getActiveSheet()->getStyle('B'.$kolom)->applyFromArray($totalTopBottomBold);

                                                $kolom += 2;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $spreadsheet->getActiveSheet()
                        ->setCellValue('B'.$kolom, ($k->name == "Pembiayaan" ? 'SALDO ' : 'TOTAL ').strtoupper($k->name))
                        ->setCellValue('C'.$kolom, $kategoriValue);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':B'.$kolom)->applyFromArray($totalTopBottomBold);

                        $kolom += 2;

                        if($k->name == "Belanja"){
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('B'.$kolom, 'SALDO OPERASIONAL')
                            ->setCellValue('C'.$kolom, $saldoOperasional);

                            $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':B'.$kolom)->applyFromArray($totalTopBottomBold);

                            $kolom += 2;
                        }
                    }
                }

                $maxRow = $kolom;

                $spreadsheet->getActiveSheet()
                ->setCellValue('B'.$kolom, 'TOTAL SALDO OPERASIONAL DAN PEMBIAYAAN')
                ->setCellValue('C'.$kolom, $totalAkhir);

                $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':B'.$kolom)->applyFromArray($totalTopBottomBold);
            }
            else{
                $accounts = null;
                
                $anggaransQuery = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
                
                // Sum Dataset
                $isShowTotal = true;
                
                foreach($kategori as $k){
                    $kategoriValue = 0;
                    $children = $k->children();
                    
                    if($children->count() > 0){
                        if($isShowTotal){
                            $firstChildren = clone $children;
                            $firstChildren = $firstChildren->first();
    
                            if($firstChildren->name != $k->name){
                                $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){
                                        $q->where('budgeting_type_id',$jenisAktif->id);
                                    })->latest()->aktif();
                                })->whereHas('akun.kategori.parent',function($q)use($k){$q->where('name',$k->name);});
                                if($apbyDetail->count() > 0){
                                    $kategoriValue = $apbyDetail->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');
    
                                    if($k->name == 'Pendapatan'){
                                        $totalPenerimaan = $kategoriValue;
                                        $saldoOperasional += $totalPenerimaan;
                                    }
                                    if($k->name == 'Belanja'){
                                        $totalBelanja = $kategoriValue;
                                        $saldoOperasional -= $totalBelanja;
                                    }
                                    if($k->name == 'Pembiayaan'){
                                        $saldoPembiayaan = $kategoriValue;
                                        $totalAkhir = $saldoOperasional+$saldoPembiayaan;
                                    }
                                }
                            }
                        }

                        $childrens = $children->get();

                        foreach($childrens as $c){
                            $subAnggarans = clone $anggaransQuery;
                            $subAnggarans = $subAnggarans->whereHas('apby',function($q)use($yearAttr,$tahun,$c,$isShowTotal){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->when(!$isShowTotal,function($q){
                                    return $q->whereHas('detail.akun',function($q){
                                        $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                    });
                                })->whereHas('detail.akun.kategori',function($q)use($c){
                                    $q->where('name',$c->name);
                                })->aktif();
                            });
                            if($subAnggarans->count() > 0){
                                foreach($subAnggarans->get() as $a){
                                    $latestActiveApby = $a->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->when(!$isShowTotal,function($q){
                                        return $q->whereHas('detail.akun',function($q){
                                            $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                        });
                                    })->whereHas('detail.akun.kategori',function($q)use($c){
                                        $q->where('name',$c->name);
                                    })->latest()->aktif()->first();

                                    if($latestActiveApby){
                                        $apbyDetail = $latestActiveApby->detail()->whereHas('akun',function($q){
                                            $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                        })->whereHas('akun.kategori',function($q)use($c){
                                            $q->where('name',$c->name);
                                        });
                                            
                                        if($apbyDetail->count() > 0){
                                            foreach($apbyDetail->with('akun:id,code,name,sort_order')->get()->sortBy('akun.sort_order')->all() as $d){
                                                $account = collect([
                                                    [
                                                        'id' => $d->akun->id,
                                                        'code' => $d->akun->code,
                                                        'name' => $d->akun->name,
                                                        'value' => $d->value,
                                                        'valueWithSeparator' => $d->valueWithSeparator 
                                                    ]
                                                ]);
                                                if($accounts){
                                                    $accounts = $accounts->concat($account);
                                                }
                                                else{
                                                    $accounts = $account;
                                                }
                                            }
                                        }
                                        
                                        if(in_array($c->name,['Penerimaan Pembiayaan','Pengeluaran Pembiayaan'])){
                                            $sumApbyDetails = $latestActiveApby->detail()->whereHas('akun.kategori',function($q)use($c){
                                                $q->where('name',$c->name);
                                            })->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');
                                            $account = collect([
                                                [
                                                    'id' => 'c-'.$c->id,
                                                    'code' => null,
                                                    'name' => 'JUMLAH '.strtoupper($c->name),
                                                    'value' => $sumApbyDetails,
                                                    'valueWithSeparator' => number_format($sumApbyDetails, 0, ',', '.')
                                                ]
                                            ]);
                                            if($accounts){
                                                $accounts = $accounts->concat($account);
                                            }
                                            else{
                                                $accounts = $account;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if($isShowTotal && in_array($k->name,['Pendapatan','Belanja','Pembiayaan'])){
                        $account = collect([
                            [
                                'id' => 'k-'.$k->id,
                                'code' => null,
                                'name' => ($k->name == "Pembiayaan" ? 'SALDO ' : 'TOTAL ').strtoupper($k->name),
                                'value' => $kategoriValue,
                                'valueWithSeparator' => number_format($kategoriValue, 0, ',', '.')
                            ]
                        ]);
                        if($accounts){
                            $accounts = $accounts->concat($account);
                        }
                        else{
                            $accounts = $account;
                        }

                        if($k->name == "Belanja"){
                            $account = collect([
                                [
                                    'id' => 's-'.$k->id,
                                    'code' => null,
                                    'name' => 'SALDO OPERASIONAL',
                                    'value' => $saldoOperasional,
                                    'valueWithSeparator' => number_format($saldoOperasional, 0, ',', '.')
                                ]
                            ]);
                            if($accounts){
                                $accounts = $accounts->concat($account);
                            }
                            else{
                                $accounts = $account;
                            }
                        }
                    }
                }
                
                if($isShowTotal){
                    $account = collect([
                        [
                            'id' => 'sum',
                            'code' => null,
                            'name' => 'TOTAL SALDO OPERASIONAL DAN PEMBIAYAAN',
                            'value' => $totalAkhir,
                            'valueWithSeparator' => number_format($totalAkhir, 0, ',', '.')
                        ]
                    ]);
                    if($accounts){
                        $accounts = $accounts->concat($account);
                    }
                    else{
                        $accounts = $account;
                    }
                }
                
                if($accounts && $accounts->count() > 0){
                    $i = 1;
                    foreach($accounts->groupBy('id') as $d){
                        $data = $d->first();
                        $value = null;
                        if(count($d) > 0){
                            $value = $d->sum('value');
                        }
                        $spreadsheet->getActiveSheet()
                            ->setCellValueExplicit('A'.$kolom, $data['code'], DataType::TYPE_STRING)
                            ->setCellValue('B'.$kolom, $data['name'])
                            ->setCellValue('C'.$kolom, abs($value ? $value : $data['value']));
                        if($data['code']){
                            $kolom++;
                        }
                        elseif($i < $accounts->groupBy('id')->count()){
                            if(in_array($data['name'],['JUMLAH PENERIMAAN PEMBIAYAAN','JUMLAH PENGELUARAN PEMBIAYAAN'])){
                                $spreadsheet->getActiveSheet()->getStyle('B'.$kolom)->applyFromArray($totalTopBottomBold);
                            }
                            else{
                                $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':B'.$kolom)->applyFromArray($totalTopBottomBold);
                            }
                            $kolom += 2;
                        }
                        $i++;
                    }
                }

                $maxRow = $kolom;
            }

            $kolom += 2;

            //TTD
            $ttdStartRow = $ttdEndRow = null;
            $jabatan = $isKso ? Jabatan::where('code','19')->first() : Jabatan::where('code','18')->first();
            $pejabat = $jabatan->pegawaiUnit()->whereHas('pegawai',function($q){
                $q->aktif();
            })->first()->pegawai;

            if($pejabat){
                $ttdStartRow = $kolom;
                $apby = $isKso ? Apby::where('director_acc_status_id', 1) : Apby::where('president_acc_status_id', 1);
                $apby = $apby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                    $query->where('budgeting_type_id',$jenisAktif->id);
                })->latest()->aktif();
                $apby = $isKso ? $apby->orderBy('director_acc_time','DESC') : $apby->orderBy('president_acc_time','DESC');
                $apby = $apby->first();

                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$kolom++, 'Tangerang Selatan, '.($apby ? ($isKso ? Date::parse($apby->director_acc_time)->format('j F Y') : Date::parse($apby->president_acc_time)->format('j F Y')) : Date::now('Asia/Jakarta')->format('j F Y')));
                $kolom++;
                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$kolom++, $jabatan->name == 'Ketua Yayasan' ? 'Ketua Pengurus' : 'Direktur')
                ->setCellValue('A'.$kolom++, 'YAYASAN MUDA INCOMSO');
                $kolom+=4;
                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$kolom++, ($apby ? ($isKso ? $apby->accDirektur->name : $apby->accKetua->name) : $pejabat->name));

                $ttdEndRow = $kolom-1;
            }

            $spreadsheet->getActiveSheet()->setTitle($jenisAktif->name.' '.(!$isYear ? $tahun->academicYearLink : $tahun));

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(65);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            $styleArray = [
                'font' => [
                    'size' => 16,
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ]
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:A2')->applyFromArray($styleArray);

            $styleArray = [
                'font' => [
                    'size' => 16,
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A4:C4')->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->getStyle('A5:C'.$maxRow)->applyFromArray($borderVertical);

            $styleArray = [
                'font' => [
                    'size' => 16
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A5:C'.$maxRow)->applyFromArray($styleArray);

            //Alignment

            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT
                ]
            ];

            $spreadsheet->getActiveSheet()->getStyle('A5:B'.$maxRow)->applyFromArray($styleArray);

            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT
                ],
                'numberFormat' => [
                    'formatCode' => $FORMAT_CURRENCY_IDR
                ]
            ];

            $spreadsheet->getActiveSheet()->getStyle('C5:C'.$maxRow)->applyFromArray($styleArray);

            $styleArray = [
                'font' => [
                    'size' => 16
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];

            if($ttdStartRow && $ttdEndRow) $spreadsheet->getActiveSheet()->getStyle('A'.$ttdStartRow.':A'.$ttdEndRow)->applyFromArray($styleArray);

            // $writer = new Xls($spreadsheet);

            // header('Content-Type: application/vnd.ms-excel');
            // header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ', '-', $jenisAktif->name)).'_'.(!$isYear ? $tahun->academicYearLink : $tahun).'.xls"');
            // header('Cache-Control: max-age=0');

            // $writer->save('php://output');
                        
            // ob_end_flush();

            $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

            $headers = [
                'Cache-Control' => 'max-age=0',
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="'.strtolower(str_replace(' ', '-', $jenisAktif->name)).'_'.(!$isYear ? $tahun->academicYearLink : $tahun).(isset($status) && $status == 'sum' ? '_'.$status : null).'.xlsx"',
            ];

            return response()->stream(function()use($writer){
                $writer->save('php://output');
            }, 200, $headers);
        }

        return redirect()->route('apby.index');
    }

    /**
     * Update the specified resources from storage.
     *
     * @param  \App\Models\Apby\Apby        $apbyAktif
     * @param  \App\Models\Apby\ApbyDetail  $detail
     */
    public function updateParentValues(Apby $apbyAktif, ApbyDetail $detail)
    {
        $childDetail = $detail;
        for($i = 0;$i < $detail->akun->parentsCount;$i++){
            $parent = $apbyAktif->detail()->whereHas('akun',function($query)use($childDetail){
                $query->where(['code' => $childDetail->akun->parentCode,'is_fillable' => 0]);
            })->first();
            if($parent){
                $childs = $apbyAktif->detail()->whereHas('akun',function($query)use($parent){
                    $query->where('code','LIKE',$parent->akun->code.'.%');
                })->with('akun')->get()->pluck('akun')->where('level',$parent->akun->level+1)->pluck('id');
                $childsValue = $apbyAktif->detail()->whereHas('akun',function($query)use($childs){
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
     * Sync the specified resources from storage.
     *
     * @param  \App\Models\Anggaran\JenisAnggaranAnggaran   $anggaranAktif
     * @param  \App\Models\Apby\Apby                        $apbyAktif
     */
    public function syncRkat($anggaranAktif, $apbyAktif, $tahun)
    {
        $yearAttr = strlen($tahun) == 4 ? 'year' : 'academic_year_id';

        $rkat = $anggaranAktif->rkat()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('revision',$apbyAktif->revision)->latest()->first();

        if($rkat && $rkat->detail()->count() > 0){
            foreach($apbyAktif->detail as $d){
                $detail = $rkat->detail()->where('account_id',$d->account_id)->first();
                if($detail){
                    if($detail->value != $d->value && $d->akun->is_fillable == 1){
                        $detail->edited_employee_id = Auth::user()->pegawai->id;
                        $detail->edited_status_id = 1;
                    }
                    $detail->value = $d->value;
                    $detail->save();
                }
            }
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Anggaran\JenisAnggaranAnggaran;
use App\Models\Anggaran\KategoriAkun;
use App\Models\Anggaran\KategoriAnggaran;
use App\Models\Apby\Apby;
use App\Models\Apby\ApbyDetail;
use App\Models\Apby\ApbyTransferLog;
use App\Models\Bbk\Bbk;
use App\Models\Lppa\Lppa;
use App\Models\Ppa\Ppa;
use App\Models\Rkat\Rkat;
use App\Models\Kbm\TahunAjaran;
use App\Models\Penempatan\Jabatan;
use Illuminate\Http\Request;

use Auth;
use DB;
use Session;
use Jenssegers\Date\Date;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ApbyController extends Controller
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
            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am'])){
                if($request->user()->pegawai->unit_id == '5'){
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id);});
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
        
        $jenisAktif = $kategoriAanggaran = $kategori = $apby = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $yearAttr = $checkApby = $anggarans = $accounts = null;
        $yearsCount = $academicYearsCount = 0;
        $perubahan = $changeYear = $nextYear = false;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif) return redirect()->route('apby.index');

            $queryApby = Apby::where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });

            if($queryApby->count() > 0){
                $years = clone $queryApby;
                $yearsCount = $years->whereNotNull('year')->aktif()->count();
                $years = $years->whereNotNull('year')->aktif()->orderBy('year')->pluck('year')->unique();

                $academicYears = clone $queryApby;
                $academicYearsCount = $academicYears->has('tahunPelajaran')->aktif()->count();
                $academicYears = $academicYears->has('tahunPelajaran')->with('tahunPelajaran:id,academic_year')->aktif()->get()->sortBy('tahunPelajaran.academic_year')->pluck('academic_year_id')->unique();

                $latest = clone $queryApby;
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
                if($tahun){
                    $tahun = str_replace("-","/",$tahun);
                    $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                }
                else{
                    // Default Value
                    $tahun = TahunAjaran::where('is_finance_year',1)->latest()->first();
                }
                if(!$tahun) return redirect()->route('apby.index');
            }
            else{
                // Default Value
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
                    return redirect()->route('apby.index');
                }
            }

            if($jenisAktif){
                $kategoriAnggaran = KategoriAnggaran::select('id','name')->whereHas('anggarans.jenisAnggaran',function($q)use($jenisAktif){
                    $q->where('budgeting_type_id',$jenisAktif->id);
                })->get();

                $kategori = KategoriAkun::all();

                $yearAttr = $isYear ? 'year' : 'academic_year_id';

                $apby = clone $queryApby;
                $apby = $apby->whereHas('jenisAnggaranAnggaran.tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->aktif()->get();

                $checkApby = Apby::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    });
                });
                $anggaransQuery = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
                $anggaranIds = clone $anggaransQuery;
                $anggaranIds = $anggaranIds->select('id')->get()->pluck('id')->unique();
                $checkPpa = Ppa::whereIn('budgeting_budgeting_type_id',$anggaranIds)->submitted();
                $checkBbk = Bbk::where('budgeting_type_id',$jenisAktif->id);

                if($isKso){
                    $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbkAcc = clone $checkBbk;
                    $checkBbkAcc = $checkBbkAcc->where('director_acc_status_id',1);
                    $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                        $query->where([
                            $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                            'director_acc_status_id' => 1
                        ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                    });
                }
                else{
                    $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    $checkBbkAcc = clone $checkBbk;
                    $checkBbkAcc = $checkBbkAcc->where('president_acc_status_id',1);
                    $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                        $query->where([
                            $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                            'director_acc_status_id' => 1
                        ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                    });
                }
                $checkApby = $checkApby->aktif();
                $checkPpaAcc = clone $checkPpa;
                $checkPpaAcc = $checkPpaAcc->where('director_acc_status_id',1);
                $checkLppaAcc = clone $checkLppa;
                $checkLppaAcc = $checkLppaAcc->where('finance_acc_status_id',1);
                
                if($checkApby->count() > 0){
                    if(($latest->year && $isYear && $tahun == date('Y')) || (!$latest->year && !$isYear && $tahun->is_finance_year == 1))
                        $perubahan = true;
                }

                if(!$latest->year && !$isYear && $tahun->is_finance_year == 1){
                    $nextYearCheck = TahunAjaran::where('academic_year_start','>',$tahun->academic_year_start)->min('academic_year_start');
                    $nextYear = $nextYearCheck ? true : false;
                    if($checkApby->count() > 0 && $checkPpaAcc->count() >= $checkPpa->count() && $checkBbkAcc->count() >= $checkBbk->count() && $checkLppaAcc->count() >= $checkLppa->count() && (!$latest->year && !$isYear && $tahun->is_finance_year == 1)){
                    //if($checkApby->count() > 0){
                        $changeYear = true;
                    }
                }

                $sumExportable = false;
                $sumApby = clone $checkApby;
                $countSumApby = $sumApby->whereHas('detail.akun',function($q){
                    $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                })->count();
                if($countSumApby > 0) $sumExportable = true;

                // Sum Dataset
                $isShowTotal = true;
                if($isShowTotal){
                    $totalPenerimaan = $totalBelanja = $saldoOperasional = $saldoPembiayaan = $totalAkhir = 0;
                }
                
                foreach($kategori as $k){
                    $kategoriValue = 0;
                    $children = $k->children();
                    
                    if($children->count() > 0){
                        if($isShowTotal){
                            $firstChildren = clone $children;
                            $firstChildren = $firstChildren->first();
    
                            if($firstChildren->name != $k->name){
                                $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){
                                        $q->where('budgeting_type_id',$jenisAktif->id);
                                    })->latest()->aktif();
                                })->whereHas('akun.kategori.parent',function($q)use($k){$q->where('name',$k->name);});
                                if($apbyDetail->count() > 0){
                                    $kategoriValue = $apbyDetail->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');
    
                                    if($k->name == 'Pendapatan'){
                                        $totalPenerimaan = $kategoriValue;
                                        $saldoOperasional += $totalPenerimaan;
                                    }
                                    if($k->name == 'Belanja'){
                                        $totalBelanja = $kategoriValue;
                                        $saldoOperasional -= $totalBelanja;
                                    }
                                    if($k->name == 'Pembiayaan'){
                                        $saldoPembiayaan = $kategoriValue;
                                        $totalAkhir = $saldoOperasional+$saldoPembiayaan;
                                    }
                                }
                            }
                        }

                        $childrens = $children->get();

                        foreach($childrens as $c){
                            $subAnggarans = clone $anggaransQuery;
                            $subAnggarans = $subAnggarans->whereHas('apby',function($q)use($yearAttr,$tahun,$c,$isShowTotal){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->when(!$isShowTotal,function($q){
                                    return $q->whereHas('detail.akun',function($q){
                                        $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                    });
                                })->whereHas('detail.akun.kategori',function($q)use($c){
                                    $q->where('name',$c->name);
                                })->aktif();
                            });
                            if($subAnggarans->count() > 0){
                                foreach($subAnggarans->get() as $a){
                                    $latestActiveApby = $a->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->when(!$isShowTotal,function($q){
                                        return $q->whereHas('detail.akun',function($q){
                                            $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                        });
                                    })->whereHas('detail.akun.kategori',function($q)use($c){
                                        $q->where('name',$c->name);
                                    })->latest()->aktif()->first();

                                    if($latestActiveApby){
                                        $apbyDetail = $latestActiveApby->detail()->whereHas('akun',function($q){
                                            $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                        })->whereHas('akun.kategori',function($q)use($c){
                                            $q->where('name',$c->name);
                                        });
                                            
                                        if($apbyDetail->count() > 0){
                                            foreach($apbyDetail->with('akun:id,code,name,sort_order')->get()->sortBy('akun.sort_order')->all() as $d){
                                                $account = collect([
                                                    [
                                                        'id' => $d->akun->id,
                                                        'code' => $d->akun->code,
                                                        'name' => $d->akun->name,
                                                        'value' => $d->value,
                                                        'valueWithSeparator' => $d->valueWithSeparator 
                                                    ]
                                                ]);
                                                if($accounts){
                                                    $accounts = $accounts->concat($account);
                                                }
                                                else{
                                                    $accounts = $account;
                                                }
                                            }
                                        }
                                        
                                        if(in_array($c->name,['Penerimaan Pembiayaan','Pengeluaran Pembiayaan'])){
                                            $sumApbyDetails = $latestActiveApby->detail()->whereHas('akun.kategori',function($q)use($c){
                                                $q->where('name',$c->name);
                                            })->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');
                                            $account = collect([
                                                [
                                                    'id' => 'c-'.$c->id,
                                                    'code' => null,
                                                    'name' => 'JUMLAH '.strtoupper($c->name),
                                                    'value' => $sumApbyDetails,
                                                    'valueWithSeparator' => number_format($sumApbyDetails, 0, ',', '.')
                                                ]
                                            ]);
                                            if($accounts){
                                                $accounts = $accounts->concat($account);
                                            }
                                            else{
                                                $accounts = $account;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if($isShowTotal && in_array($k->name,['Pendapatan','Belanja','Pembiayaan'])){
                        $account = collect([
                            [
                                'id' => 'k-'.$k->id,
                                'code' => null,
                                'name' => ($k->name == "Pembiayaan" ? 'SALDO ' : 'TOTAL ').strtoupper($k->name),
                                'value' => $kategoriValue,
                                'valueWithSeparator' => number_format($kategoriValue, 0, ',', '.')
                            ]
                        ]);
                        if($accounts){
                            $accounts = $accounts->concat($account);
                        }
                        else{
                            $accounts = $account;
                        }

                        if($k->name == "Belanja"){
                            $account = collect([
                                [
                                    'id' => 's-'.$k->id,
                                    'code' => null,
                                    'name' => 'SALDO OPERASIONAL',
                                    'value' => $saldoOperasional,
                                    'valueWithSeparator' => number_format($saldoOperasional, 0, ',', '.')
                                ]
                            ]);
                            if($accounts){
                                $accounts = $accounts->concat($account);
                            }
                            else{
                                $accounts = $account;
                            }
                        }
                    }
                }
                
                if($isShowTotal){
                    $account = collect([
                        [
                            'id' => 'sum',
                            'code' => null,
                            'name' => 'TOTAL SALDO OPERASIONAL DAN PEMBIAYAAN',
                            'value' => $totalAkhir,
                            'valueWithSeparator' => number_format($totalAkhir, 0, ',', '.')
                        ]
                    ]);
                    if($accounts){
                        $accounts = $accounts->concat($account);
                    }
                    else{
                        $accounts = $account;
                    }
                }
                
                $anggarans = clone $anggaransQuery;
                $anggarans = $anggaransQuery->whereHas('apby',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('detail.akun',function($q){
                        $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                    })->aktif();
                })->get();

                if($anggaran){
                    $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$apby->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id'))->first();
                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                        $apbyAktif = !$isYear ? $anggaranAktif->apby()->where('academic_year_id',$tahun->id)->latest()->aktif()->first() : $anggaranAktif->apby()->where('year',$tahun)->latest()->aktif()->first();

                        //if($apbyAktif || (!$apbyAktif && (!$isYear && $tahun->is_finance_year == 1) || ($isYear && $tahun == date('Y')))){
                        if($apbyAktif){
                            // Inti controller

                            $totalAnggaran = 0;

                            // Counter
                            $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'))->aktif();
                            });

                            $totalPendapatan = clone $apbyDetail;
                            $totalPendapatan = $totalPendapatan->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Pendapatan');
                                });
                            })->sum('value');

                            $totalPembiayaan = clone $apbyDetail;
                            $totalPembiayaan = $totalPembiayaan->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Pembiayaan');
                                });
                            })->sum('value');

                            $totalBelanja = clone $apbyDetail;
                            $totalBelanja = $totalBelanja->whereHas('akun',function($q){
                                $q->where('is_fillable',1)->whereHas('kategori.parent',function($q){
                                    $q->where('name','Belanja');
                                });
                            })->sum('value');

                            if($apbyAktif && $apbyAktif->detail()->count() > 0){
                                $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
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

                            if(in_array($role,['ketuayys','direktur','fam','faspv','am']))
                                $folder = $role;
                            else $folder = 'read-only';

                            if($isKso)
                                return view('keuangan.'.$folder.'.apb_kso_detail', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','apby','years','academicYears','anggaranAktif','apbyAktif','kategori','total'));
                            else
                                return view('keuangan.'.$folder.'.apby_detail', compact('jenisAnggaran','jenisAktif','tahun','tahunPelajaran','isYear','apby','years','academicYears','anggaranAktif','apbyAktif','kategori','total'));
                        }
                        else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                    else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
            }
        }

        // if($jenis && $isKso)
        //     return view('keuangan.read-only.apb_kso_index', compact('jenisAnggaran','jenisAktif','kategori','tahun','tahunPelajaran','isYear','checkApby','apby','years','academicYears','perubahan','changeYear','nextYear'));
        // else
            return view('keuangan.read-only.apby_index', compact('jenisAnggaran','jenisAktif','kategoriAnggaran','kategori','tahun','tahunPelajaran','yearAttr','isYear','checkApby','apby','years','academicYears','perubahan','changeYear','nextYear','sumExportable','anggaransQuery','anggarans','accounts'));
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
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function show(Apby $apby)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            })->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif();
                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id', 1) : $apbyAktif->where('president_acc_status_id', 1);
                $apbyAktif = $apbyAktif->first();

                if($apbyAktif){
                    $apbyAktifDetail = $apbyAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1)->whereHas('kategori.parent',function($q){$q->where('name','Belanja');
                        });
                    });
                    $apbyAktifDetailFilter = clone $apbyAktifDetail;
                    $apbyAktifDetailFilter = $apbyAktifDetailFilter->where('id',$request->id)->where('balance','>',0);
                    if($apbyAktifDetailFilter->count() > 0){
                        if($role == 'am'){
                            // Inti function
                            $details = clone $apbyAktifDetail;
                            $details = $details->get();

                            $detail = $apbyAktifDetailFilter->first();

                            return view('keuangan.'.$role.'.apby_ubah', compact('jenisAktif','tahun','isYear','anggaranAktif','yearAttr','details','detail'));
                        }
                    }
                    return "Ups! Unable to load data. There is no editable account.";
                }
                else return "Ups! Unable to load data. Inactive budgeting.";
            }
            else return "Error! Unable to load data. Invalid budgeting.";
        }
        return "Error! Unable to load data. Invalid budgeting type.";
    }

    /**
     * Transfer the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function transfer(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            })->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif();
                $apbyAktif = $isKso ? $apbyAktif->where('director_acc_status_id', 1) : $apbyAktif->where('president_acc_status_id', 1);
                $apbyAktif = $apbyAktif->first();

                if($apbyAktif){
                    $apbyAktifDetail = $apbyAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1)->whereHas('kategori.parent',function($q){$q->where('name','Belanja');
                        });
                    });
                    $apbyAktifDetailFilter = clone $apbyAktifDetail;
                    $apbyAktifDetailFilter = $apbyAktifDetailFilter->where('id',$request->id)->where('balance','>',0);
                    if($apbyAktifDetailFilter->count() > 0){
                        if($role == 'am'){
                            // Inti function
                            $messages = [
                                'account.required' => 'Mohon pilih salah satu akun anggaran tujuan',
                                'amount.required' => 'Mohon masukkan jumlah nominal transfer',
                            ];

                            $this->validate($request, [
                                'account' => 'required',
                                'amount' => 'required'
                            ], $messages);

                            $destination = clone $apbyAktifDetail;
                            $destination = $destination->where('id','!=',$request->id)->where('id',$request->account)->first();

                            $detail = $apbyAktifDetailFilter->first();

                            if($destination && $detail){
                                $requestValue = (int)str_replace('.','',$request->amount);
                                if($requestValue > 0 && $requestValue <= $detail->balance){
                                    $log = new ApbyTransferLog();
                                    $log->from_detail_id = $detail->id;
                                    $log->from_value = $detail->value;
                                    $log->from_balance = $detail->balance;
                                    $log->to_detail_id = $destination->id;
                                    $log->to_value = $destination->value;
                                    $log->to_balance = $destination->balance;
                                    $log->amount = $requestValue;
                                    $log->employee_id = $request->user()->pegawai->id;

                                    $apbyAktif->transferLogs()->save($log);

                                    $destination->value += $requestValue;
                                    $detail->value -= $requestValue;

                                    $destination->balance += $requestValue;
                                    $detail->balance -= $requestValue;

                                    $destination->save();
                                    $detail->save();

                                    $destination->fresh();
                                    $detail->fresh();

                                    $this->updateParentValues($apbyAktif,$destination);
                                    $this->updateParentValues($apbyAktif,$detail);

                                    Session::flash('success', 'Transfer nominal saldo sebesar '.$request->amount.' ke '.$destination->akun->codeName.' berhasil.');
                                }
                                else Session::flash('danger', 'Transfer nominal saldo akun anggaran gagal. Pastikan jumlah nominal tidak melebihi saldo akun asal');
                            }
                            else Session::flash('danger', 'Transfer nominal saldo akun anggaran gagal.');
                        }
                    }
                    else Session::flash('danger', 'Transfer nominal saldo akun anggaran tidak dapat dilakukan.');
                }
                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('apby.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $jenis, $tahun, $anggaran)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            })->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->first();

                if($apbyAktif){
                    $apbyAktifDetail = $apbyAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1);
                    });
                    if($apbyAktifDetail->count() > 0){
                        if($role == 'ketuayys' && !$isKso){
                            // Inti function
                            $apbyAktifDetailClone = clone $apbyAktifDetail;

                            // Get last revision
                            if($apbyAktif->revision > 1){
                                $apbyLastRevision = $anggaranAktif->apby()->where([
                                    ['revision', '<', $apbyAktif->revision],
                                    ['is_active', '=', 0],
                                    [$yearAttr, '=', ($yearAttr == 'year' ? $tahun : $tahun->id)]
                                ])->latest()->first();
                            }

                            foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                $detail->value = $requestValue;
                                $detail->value_president = $requestValue;
                                if($apbyAktif->revision > 1 && $apbyLastRevision){
                                    $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($detail){
                                        $query->where('id',$detail->akun->id);
                                    })->first();
                                    $detail->used = $lastDetail ? $lastDetail->used : 0;
                                    $detail->balance = $lastDetail ? ($requestValue-$lastDetail->used) : $requestValue;
                                }
                                else{
                                    $detail->balance = $requestValue;
                                }
                                if($requestValue > 0){
                                    if(isset($detail->value_director)){
                                        if($detail->value_director != $requestValue){
                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                        $detail->president_acc_id = $request->user()->pegawai->id;
                                        $detail->president_acc_status_id = 1;
                                        $detail->president_acc_time = Date::now('Asia/Jakarta');
                                    }
                                }
                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($apbyAktif,$detail);
                            }

                            if(isset($request->validate) && $request->validate == 'validate'){
                                $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                                // Accept unaccepted value
                                $apbyAktifDetailUpdate->whereNull([
                                    'president_acc_id',
                                    'president_acc_status_id',
                                    'president_acc_time',
                                ])->update([
                                    'president_acc_id' => $request->user()->pegawai->id,
                                    'president_acc_status_id' => 1,
                                    'president_acc_time' => Date::now('Asia/Jakarta')
                                ]);

                                $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                                    $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                        $q->where('name','Belanja');
                                    });
                                });

                                // Accept APBY
                                $apbyAktif->update([
                                    'total_value' => $totalAnggaran->sum('value'),
                                    'total_used' => $totalAnggaran->sum('used'),
                                    'total_balance' => $totalAnggaran->sum('balance'),
                                    'president_acc_id' => $request->user()->pegawai->id,
                                    'president_acc_status_id' => 1,
                                    'president_acc_time' => Date::now('Asia/Jakarta')
                                ]);

                                // Sync with RKAT Detail Values
                                $this->syncRkat($anggaranAktif,$apbyAktif,$tahun);
                            }
                        }
                        elseif($role == 'direktur'){
                            // Inti function
                            if($isKso){
                                $apbyAktifDetailClone = clone $apbyAktifDetail;

                                // Get last revision
                                if($apbyAktif->revision > 1){
                                    $apbyLastRevision = $anggaranAktif->apby()->where([
                                        ['revision', '<', $apbyAktif->revision],
                                        ['is_active', '=', 0],
                                        [$yearAttr, '=', ($yearAttr == 'year' ? $tahun : $tahun->id)]
                                    ])->latest()->first();
                                }

                                foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                    $inputName = 'value-'.$detail->id;
                                    $requestValue = (int)str_replace('.','',$request->{$inputName});
                                    $detail->value = $requestValue;
                                    $detail->value_director = $requestValue;
                                    if($apbyAktif->revision > 1 && $apbyLastRevision){
                                        $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($detail){
                                            $query->where('id',$detail->akun->id);
                                        })->first();
                                        $detail->used = $lastDetail ? $lastDetail->used : 0;
                                        $detail->balance = $lastDetail ? ($requestValue-$lastDetail->used) : $requestValue;
                                    }
                                    else{
                                        $detail->balance = $requestValue;
                                    }
                                    if($requestValue > 0){
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

                                    $this->updateParentValues($apbyAktif,$detail);
                                }

                                if(isset($request->validate) && $request->validate == 'validate'){
                                    $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                                    // Accept unaccepted value
                                    $apbyAktifDetailUpdate->whereNull([
                                        'director_acc_id',
                                        'director_acc_status_id',
                                        'director_acc_time',
                                    ])->update([
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                                        $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                            $q->where('name','Belanja');
                                        });
                                    });

                                    // Accept APBY
                                    $apbyAktif->update([
                                        'total_value' => $totalAnggaran->sum('value'),
                                        'total_used' => $totalAnggaran->sum('used'),
                                        'total_balance' => $totalAnggaran->sum('balance'),
                                        'director_acc_id' => $request->user()->pegawai->id,
                                        'director_acc_status_id' => 1,
                                        'director_acc_time' => Date::now('Asia/Jakarta')
                                    ]);

                                    // Sync with RKAT Detail Values
                                    $this->syncRkat($anggaranAktif,$apbyAktif,$tahun);
                                }
                            }
                            else{
                                $apbyAktifDetailFilter = $apbyAktifDetail->where(function($query){
                                    $query->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                })->with('akun')->get()->sortByDesc('akun.level')->all();
                                foreach($apbyAktifDetailFilter as $detail){
                                    $inputName = 'value-'.$detail->id;
                                    $requestValue = (int)str_replace('.','',$request->{$inputName});
                                    $detail->value = $requestValue;
                                    $detail->value_director = $requestValue;
                                    if($requestValue > 0){
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

                                    $this->updateParentValues($apbyAktif,$detail);
                                }

                                if($apbyAktifDetail->whereNull('director_acc_status_id')->count() < 1){
                                    $apbyAktif->director_acc_id = $request->user()->pegawai->id;
                                    $apbyAktif->director_acc_status_id = 1;
                                    $apbyAktif->director_acc_time = Date::now('Asia/Jakarta');
                                    $apbyAktif->save();
                                }
                            }
                        }
                        elseif($role == 'am'){
                            // Inti function
                            $apbyAktifDetailFilter = $apbyAktifDetail->where(function($query){
                                $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();
                            foreach($apbyAktifDetailFilter as $detail){
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                $detail->value = $requestValue;
                                $detail->value_fam = $requestValue;
                                if($requestValue > 0){
                                    if(!$detail->employee_id){
                                        $detail->employee_id = $request->user()->pegawai->id;
                                        if($detail->value_rkat != $requestValue){
                                             $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                        // $detail->finance_acc_id = $request->user()->pegawai->id;
                                        // $detail->finance_acc_status_id = 1;
                                        // $detail->finance_acc_time = Date::now('Asia/Jakarta');
                                    }
                                    if(isset($detail->value_faspv)){
                                        if($detail->value_faspv != $requestValue){
                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                        // $detail->finance_acc_id = $request->user()->pegawai->id;
                                        // $detail->finance_acc_status_id = 1;
                                        // $detail->finance_acc_time = Date::now('Asia/Jakarta');
                                    }
                                }
                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($apbyAktif,$detail);
                            }

                            if($apbyAktifDetail->whereNull('finance_acc_status_id')->count() < 1){
                                $apbyAktif->finance_acc_id = $request->user()->pegawai->id;
                                $apbyAktif->finance_acc_status_id = 1;
                                $apbyAktif->finance_acc_time = Date::now('Asia/Jakarta');
                                $apbyAktif->save();
                            }
                        }
                        elseif(in_array($role,['fam','faspv'])){
                            // Inti function
                            // $apbyAktifDetailFilter = $apbyAktifDetail->where(function($query){
                            //     $query->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                            // })->with('akun')->get()->sortByDesc('akun.level')->all();
                            $apbyAktifDetailFilter = $apbyAktifDetail->where(function($query){
                                $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();
                            foreach($apbyAktifDetailFilter as $detail){
                                $inputName = 'value-'.$detail->id;
                                $requestValue = (int)str_replace('.','',$request->{$inputName});
                                $detail->value = $requestValue;
                                $detail->value_faspv = $requestValue;
                                $detail->value_fam = $requestValue;
                                if($requestValue > 0){
                                    if(!$detail->employee_id){
                                        $detail->employee_id = $request->user()->pegawai->id;
                                        if($detail->value_rkat != $requestValue){
                                            $detail->edited_employee_id = $request->user()->pegawai->id;
                                            $detail->edited_status_id = 1;
                                        }
                                    }
                                    $detail->finance_acc_id = $request->user()->pegawai->id;
                                    $detail->finance_acc_status_id = 1;
                                    $detail->finance_acc_time = Date::now('Asia/Jakarta');
                                }
                                $detail->save();

                                $detail->fresh();

                                $this->updateParentValues($apbyAktif,$detail);
                            }
                        }
                    }
                }
                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('apby.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Apby\Apby  $apby
     * @return \Illuminate\Http\Response
     */
    public function destroy(Apby $apby)
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
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';
            $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereHas('jenisAnggaran',function($q)use($jenisAktif,$yearAttr,$tahun){
                $q->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            })->first();
            if($anggaranAktif){
                $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();

                $apbyAktif = $anggaranAktif->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->first();

                if($apbyAktif){
                    $apbyAktifDetail = $apbyAktif->detail()->whereHas('akun',function($query){
                        $query->where('is_fillable',1);
                    });
                    if($apbyAktifDetail->count() > 0){
                        if($role == 'ketuayys' && !$isKso){
                            // Inti function
                            $apbyAktifDetailClone = clone $apbyAktifDetail;

                            // Get last revision
                            if($apbyAktif->revision > 1){
                                $apbyLastRevision = $anggaranAktif->apby()->where([
                                    ['revision', '<', $apbyAktif->revision],
                                    ['is_active', '=', 0],
                                    [$yearAttr, '=', ($yearAttr == 'year' ? $tahun : $tahun->id)]
                                ])->latest()->first();
                            }

                            // Calculate balances
                            foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                if($apbyAktif->revision > 1 && $apbyLastRevision){
                                    $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($detail){
                                        $query->where('id',$detail->akun->id);
                                    })->first();
                                    $detail->used = $lastDetail ? $lastDetail->used : 0;
                                    $detail->balance = $lastDetail ? ($detail->value-$lastDetail->used) : $detail->value;
                                }
                                else{
                                    $detail->balance = $detail->value;
                                }
                                $detail->value_president = $detail->value;
                                $detail->save();
                            }

                            $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                            // Accept unaccepted value
                            $apbyAktifDetailUpdate->whereNull([
                                'president_acc_id',
                                'president_acc_status_id',
                                'president_acc_time',
                            ])->update([
                                'president_acc_id' => $request->user()->pegawai->id,
                                'president_acc_status_id' => 1,
                                'president_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                                $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                    $q->where('name','Belanja');
                                });
                            });

                            // Accept APBY
                            $apbyAktif->update([
                                'total_value' => $totalAnggaran->sum('value'),
                                'total_used' => $totalAnggaran->sum('used'),
                                'total_balance' => $totalAnggaran->sum('balance'),
                                'president_acc_id' => $request->user()->pegawai->id,
                                'president_acc_status_id' => 1,
                                'president_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            // Sync with RKAT Detail Values
                            $this->syncRkat($anggaranAktif,$apbyAktif,$tahun);
                        }
                        elseif($role == 'direktur'){
                            // Inti function

                            $apbyAktifDetailClone = clone $apbyAktifDetail;
                            if($isKso){
                                // Get last revision
                                if($apbyAktif->revision > 1){
                                    $apbyLastRevision = $anggaranAktif->apby()->where([
                                        ['revision', '<', $apbyAktif->revision],
                                        ['is_active', '=', 0],
                                        [$yearAttr, '=', ($yearAttr == 'year' ? $tahun : $tahun->id)]
                                    ])->latest()->first();
                                }

                                // Calculate balances
                                foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level')->all() as $detail){
                                    if($apbyAktif->revision > 1 && $apbyLastRevision){
                                        $lastDetail = $apbyLastRevision->detail()->whereHas('akun',function($query)use($detail){
                                            $query->where('id',$detail->akun->id);
                                        })->first();
                                        $detail->used = $lastDetail ? $lastDetail->used : 0;
                                        $detail->balance = $lastDetail ? ($detail->value-$lastDetail->used) : $detail->value;
                                    }
                                    else{
                                        $detail->balance = $detail->value;
                                    }
                                    $detail->value_director = $detail->value;
                                    $detail->save();
                                }
                            }
                            else{
                                $apbyAktifDetailFilter = $apbyAktifDetailClone->where(function($query){
                                    $query->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                })->with('akun')->get()->sortByDesc('akun.level')->all();

                                foreach($apbyAktifDetailFilter as $detail){
                                    $detail->value_director = $detail->value;
                                    $detail->save();
                                }
                            }

                            $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                            // Accept unaccepted value
                            $apbyAktifDetailUpdate->whereNull([
                                'director_acc_id',
                                'director_acc_status_id',
                                'director_acc_time',
                            ])->update([
                                'director_acc_id' => $request->user()->pegawai->id,
                                'director_acc_status_id' => 1,
                                'director_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            if($isKso){
                                $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                                    $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                                        $q->where('name','Belanja');
                                    });
                                });

                                // Accept APB-KSO
                                $apbyAktif->update([
                                    'total_value' => $totalAnggaran->sum('value'),
                                    'total_used' => $totalAnggaran->sum('used'),
                                    'total_balance' => $totalAnggaran->sum('balance'),
                                    'director_acc_id' => $request->user()->pegawai->id,
                                    'director_acc_status_id' => 1,
                                    'director_acc_time' => Date::now('Asia/Jakarta')
                                ]);

                                // Sync with RKAT Detail Values
                                $this->syncRkat($anggaranAktif,$apbyAktif,$tahun);
                            }
                            else{
                                // Accept APBY
                                $apbyAktif->update([
                                    'director_acc_id' => $request->user()->pegawai->id,
                                    'director_acc_status_id' => 1,
                                    'director_acc_time' => Date::now('Asia/Jakarta')
                                ]);
                            }
                        }
                        elseif($role == 'am'){
                            // Inti function
                            $apbyAktifDetailClone = clone $apbyAktifDetail;
                            $apbyAktifDetailFilter = $apbyAktifDetailClone->where(function($query){
                                $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                            })->with('akun')->get()->sortByDesc('akun.level')->all();

                            foreach($apbyAktifDetailFilter as $detail){
                                $detail->value_fam = $detail->value;
                                $detail->save();
                            }

                            // Accept 0 value
                            $apbyAktifDetailUpdate = clone $apbyAktifDetail;

                            $apbyAktifDetailUpdate->whereNull([
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
                            $apbyAktifDetail->whereNull([
                                'finance_acc_id',
                                'finance_acc_status_id',
                                'finance_acc_time',
                            ])->update([
                                'finance_acc_id' => $request->user()->pegawai->id,
                                'finance_acc_status_id' => 1,
                                'finance_acc_time' => Date::now('Asia/Jakarta')
                            ]);

                            // Accept APBY
                            $apbyAktif->update([
                                'finance_acc_id' => $request->user()->pegawai->id,
                                'finance_acc_status_id' => 1,
                                'finance_acc_time' => Date::now('Asia/Jakarta')
                            ]);
                        }
                    }
                }
                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]);
            }
            else return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
        }

        return redirect()->route('apby.index');
    }

    /**
     * Revise the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function revise(Request $request, $jenis, $tahun)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            // Inti Controller
            $checkRkat = Rkat::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $checkApby = Apby::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $anggaranIds = $jenisAktif->anggaran()->select('id')->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->get()->pluck('id')->unique();
            $checkPpa = Ppa::whereIn('budgeting_budgeting_type_id',$anggaranIds)->submitted();
            $checkBbk = Bbk::where('budgeting_type_id',$jenisAktif->id);

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            if($isKso){
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('director_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                });
            }
            else{
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('president_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                });
            }
            $checkRkat = $checkRkat->aktif();
            $checkApby = $checkApby->aktif();
            $checkPpaAcc = clone $checkPpa;
            $checkPpaAcc = $checkPpaAcc->where('director_acc_status_id',1);
            $checkLppaAcc = clone $checkLppa;
            $checkLppaAcc = $checkLppaAcc->where('finance_acc_status_id',1);

            if($checkApby->count() > 0 && (($isYear && $tahun == date('Y')) || (!$isYear && $tahun->is_finance_year == 1))){
            //if($checkApby->count() > 0 && $checkPpaAcc->count() >= $checkPpa->count() && $checkBbkAcc->count() >= $checkBbk->count() && $checkLppaAcc->count() >= $checkLppa->count()){
                $checkRkat->update(['is_active' => 0]);
                $checkApby->update(['is_active' => 0]);

                DB::table('tm_settings')->where('name','budgeting_account_lock_status')->update(['value' => 0]);

                Session::flash('success', $jenisAktif->name.' perubahan berhasil dilakukan');

                return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
            else{
                Session::flash('danger', $jenisAktif->name.' perubahan gagal dilakukan');

                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
        }

        return redirect()->route('apby.index');
    }

    /**
     * Lock the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lock(Request $request, $jenis, $tahun)
    {
        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;
            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }
            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            // Inti Controller
            $checkRkat = Rkat::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $checkApby = Apby::whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif,$yearAttr,$tahun){
                $query->where('budgeting_type_id',$jenisAktif->id)->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
            });
            $anggaranIds = $jenisAktif->anggaran()->select('id')->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
            })->get()->pluck('id')->unique();
            $checkPpa = Ppa::whereIn('budgeting_budgeting_type_id',$anggaranIds)->submitted();
            $checkBbk = Bbk::where('budgeting_type_id',$jenisAktif->id);

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            if($isKso){
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('director_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                });
            }
            else{
                $checkRkat = $checkRkat->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkApby = $checkApby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkPpa = $checkPpa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbk = $checkBbk->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                $checkBbkAcc = clone $checkBbk;
                $checkBbkAcc = $checkBbkAcc->where('president_acc_status_id',1);
                $checkLppa = Lppa::whereHas('ppa',function($query)use($jenisAktif,$yearAttr,$tahun){
                    $query->where([
                        $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),
                        'director_acc_status_id' => 1
                    ])->whereIn('budgeting_budgeting_type_id',$jenisAktif->anggaran()->pluck('id'));
                });
            }
            $checkRkat = $checkRkat->aktif();
            $checkApby = $checkApby->aktif();
            $checkPpaAcc = clone $checkPpa;
            $checkPpaAcc = $checkPpaAcc->where('director_acc_status_id',1);
            $checkLppaAcc = clone $checkLppa;
            $checkLppaAcc = $checkLppaAcc->where('finance_acc_status_id',1);

            if($checkApby->count() > 0 && $checkPpaAcc->count() >= $checkPpa->count() && $checkBbkAcc->count() >= $checkBbk->count() && $checkLppaAcc->count() >= $checkLppa->count() && (!$isYear && $tahun->is_finance_year == 1)){
            //if($checkApby->count() > 0 && (!$isYear && $tahun->is_finance_year == 1)){
                $nextYear = TahunAjaran::where('academic_year_start','>',$tahun->academic_year_start)->min('academic_year_start');
                if($nextYear){
                    $checkRkat->update(['is_final' => 1]);
                    $checkApby->update(['is_final' => 1]);

                    $tahun->update(['is_finance_year' => 0]);
                    TahunAjaran::where('academic_year_start',$nextYear)->update(['is_finance_year' => 1]);

                    Session::flash('success', 'Tutup tahun '.$jenisAktif->name.' berhasil dilakukan');

                    return redirect()->route('rkat.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
            }
            else{
                Session::flash('danger', 'Tutup tahun '.$jenisAktif->name.' gagal dilakukan, pastikan tidak ada PPA, PPB, maupun RPPA yang belum selesai prosesnya');

                return redirect()->route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
            }
        }

        return redirect()->route('apby.index');
    }

    /**
     * Export the resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, $jenis, $tahun, $status = null)
    {
        // Pre-defined formats
        $FORMAT_CURRENCY_IDR_SIMPLE = '"Rp"#,##0.00_-';
        $FORMAT_CURRENCY_IDR = 'Rp#,##0_-';
        $FORMAT_ACCOUNTING_IDR = '_("Rp"* #,##0.00_);_("Rp"* \(#,##0.00\);_("Rp"* "-"??_);_(@_)';

        $role = $request->user()->role->name;
        
        $jenisAktif = JenisAnggaran::where('link',$jenis)->first();

        if($jenisAktif){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $isYear = strlen($tahun) == 4 ? true : false;

            if(!$isYear){
                $tahun = str_replace("-","/",$tahun);
                $tahun = TahunAjaran::where('academic_year',$tahun)->first();
                if(!$tahun) return redirect()->route('apby.index', ['jenis' => $jenisAktif->link]);
            }

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            $tahunTitle = !$isYear ? ("Tahun Pelajaran ".$tahun->academic_year) : ("Tahun ".$tahun);

            $spreadsheet = new Spreadsheet();

            $spreadsheet->getProperties()->setCreator('Sekolah MUDA')
            ->setLastModifiedBy($request->user()->pegawai->name)
            ->setTitle("Data ".$jenisAktif->name." MUDA ".$tahunTitle)
            ->setSubject($jenisAktif->name." MUDA ".$tahunTitle)
            ->setDescription("Rekapitulasi Data ".$jenisAktif->name."  MUDA ".$tahunTitle)
            ->setKeywords("APB, ".$jenisAktif->name.", MUDA");

            $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'YAYASAN MUDA INCOMSO'.($isKso? ' - LETRIS KSO' : null))
            ->setCellValue('A2', strtoupper($jenisAktif->fullname))
            ->setCellValue('A4', strtoupper('No. Akun'))
            ->setCellValue('B4', strtoupper('Nama Akun'))
            ->setCellValue('C4', strtoupper('Jumlah'));

            $kolom = $first_kolom = 5;

            $kategori = KategoriAkun::all();

            $totalPenerimaan = $totalBelanja = $saldoOperasional = $saldoPembiayaan = $totalAkhir = 0;

            //Styles
            $borderVertical = [
                'borders' => [
                    'left' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                    'right' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                    'vertical' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
            ];

            $totalTopBottomBold = [
                'font' => [
                    'bold' => true
                ],
                'borders' => [
                    'top' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                    'bottom' => [
                        'borderStyle' => Border::BORDER_DOUBLE
                    ]
                ]
            ];

            if(!isset($status) || $status != 'sum'){
                foreach($kategori as $k){
                    $kategoriValue = 0;
                    $children = $k->children();
                    
                    if($children->count() > 0){
                        $firstChildren = clone $children;
                        $firstChildren = $firstChildren->first();

                        if($firstChildren->name != $k->name){
                            $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){
                                    $q->where('budgeting_type_id',$jenisAktif->id);
                                })->latest()->aktif();
                            })->whereHas('akun.kategori.parent',function($q)use($k){$q->where('name',$k->name);});
                            if($apbyDetail->count() > 0){
                                if($k->name == "Belanja"){
                                    $spreadsheet->getActiveSheet()->setCellValue('A'.$kolom, '5');
                                }

                                $spreadsheet->getActiveSheet()->setCellValue('B'.$kolom, strtoupper($k->name));

                                $styleArray = [
                                    'font' => [
                                        'bold' => true
                                    ],
                                    'alignment' => [
                                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                                    ]
                                ];

                                $spreadsheet->getActiveSheet()->getStyle('B'.$kolom++)->applyFromArray($styleArray);

                                $kategoriValue = $apbyDetail->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');

                                if($k->name == 'Pendapatan'){
                                    $totalPenerimaan = $kategoriValue;
                                    $saldoOperasional += $totalPenerimaan;
                                }
                                if($k->name == 'Belanja'){
                                    $totalBelanja = $kategoriValue;
                                    $saldoOperasional -= $totalBelanja;
                                }
                                if($k->name == 'Pembiayaan'){
                                    $saldoPembiayaan = $kategoriValue;
                                    $totalAkhir = $saldoOperasional+$saldoPembiayaan;
                                }
                            }
                        }

                        $childrens = $children->get();

                        foreach($childrens as $c){
                            $GrandChildren = $c->children();

                            if($GrandChildren->count() > 0){
                                $firstGrandChildren = clone $GrandChildren;
                                $firstGrandChildren = $firstGrandChildren->first();

                                if($firstChildren->name != $c->name){
                                    $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){
                                            $q->where('budgeting_type_id',$jenisAktif->id);
                                        })->latest()->aktif();
                                    })->whereHas('akun.kategori',function($q)use($c){$q->where('name',$c->name);});
                                    if($apbyDetail->count() > 0){
                                        $spreadsheet->getActiveSheet()->setCellValue('B'.$kolom++, strtoupper($c->name));
                                    }
                                }
                            }
                            else{
                                $anggaran = $jenisAktif->anggaran();
                                if($anggaran->count() > 0){
                                    $anggaran = $anggaran->get();
                                    foreach($anggaran as $a){
                                        $apby = $a->apby()->whereHas('detail.akun.kategori',function($q)use($c){
                                            $q->where('name',$c->name);
                                        })->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->latest()->aktif()->first();

                                        if($apby){
                                            $apbyDetail = $apby->detail()->whereHas('akun.kategori',function($q)use($c){$q->where('name',$c->name);});
                                            $firstApbyDetail = clone $apbyDetail;
                                            $firstApbyDetail = $firstApbyDetail->first();

                                            if($firstApbyDetail->akun->name != strtoupper($c->name) && ($c->name != 'Belanja Anggaran')){
                                                $spreadsheet->getActiveSheet()->setCellValue('B'.$kolom++, strtoupper($c->name));
                                            }

                                            $parentDetail = $parentRow = null;
                                            $apbyDetailClone = clone $apbyDetail;

                                            foreach($apbyDetail->with('akun')->get()->sortBy('akun.sort_order')->all() as $d){
                                                if((count(explode('.',$d->akun->code)) == 2) && $c->name == 'Belanja Anggaran'){
                                                    $parentDetail = $d;
                                                    $apbyDetailCount = clone $apbyDetailClone;
                                                    $parentRow = $kolom+($apbyDetailCount->whereHas('akun',function($q)use($d){$q->where('code','LIKE',$d->akun->code.'.%');})->count())+1;
                                                }

                                                $spreadsheet->getActiveSheet()
                                                ->setCellValueExplicit('A'.$kolom, $d->akun->code, DataType::TYPE_STRING)
                                                ->setCellValue('B'.$kolom, $d->akun->name)
                                                ->setCellValue('C'.$kolom++, $d->akun->is_fillable == 1 ? abs($d->value) : null);

                                                if($parentDetail && $parentRow && ($parentRow == $kolom) && ($c->name == 'Belanja Anggaran')){
                                                    $spreadsheet->getActiveSheet()
                                                    ->setCellValue('B'.$kolom, 'TOTAL '.strtoupper($parentDetail->akun->name))
                                                    ->setCellValue('C'.$kolom,$parentDetail->value);

                                                    $spreadsheet->getActiveSheet()->getStyle('B'.$kolom)->applyFromArray($totalTopBottomBold);

                                                    $kolom += 2;

                                                    $parentDetail = $parentRow = null;
                                                }
                                            }
                                            if($c->name != 'Belanja Anggaran'){
                                                $spreadsheet->getActiveSheet()
                                                ->setCellValue('B'.$kolom, 'JUMLAH '.strtoupper($c->name))
                                                ->setCellValue('C'.$kolom,$apbyDetail->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value'));

                                                $spreadsheet->getActiveSheet()->getStyle('B'.$kolom)->applyFromArray($totalTopBottomBold);

                                                $kolom += 2;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $spreadsheet->getActiveSheet()
                        ->setCellValue('B'.$kolom, ($k->name == "Pembiayaan" ? 'SALDO ' : 'TOTAL ').strtoupper($k->name))
                        ->setCellValue('C'.$kolom, $kategoriValue);

                        $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':B'.$kolom)->applyFromArray($totalTopBottomBold);

                        $kolom += 2;

                        if($k->name == "Belanja"){
                            $spreadsheet->getActiveSheet()
                            ->setCellValue('B'.$kolom, 'SALDO OPERASIONAL')
                            ->setCellValue('C'.$kolom, $saldoOperasional);

                            $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':B'.$kolom)->applyFromArray($totalTopBottomBold);

                            $kolom += 2;
                        }
                    }
                }

                $maxRow = $kolom;

                $spreadsheet->getActiveSheet()
                ->setCellValue('B'.$kolom, 'TOTAL SALDO OPERASIONAL DAN PEMBIAYAAN')
                ->setCellValue('C'.$kolom, $totalAkhir);

                $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':B'.$kolom)->applyFromArray($totalTopBottomBold);
            }
            else{
                $accounts = null;
                
                $anggaransQuery = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                });
                
                // Sum Dataset
                $isShowTotal = true;
                
                foreach($kategori as $k){
                    $kategoriValue = 0;
                    $children = $k->children();
                    
                    if($children->count() > 0){
                        if($isShowTotal){
                            $firstChildren = clone $children;
                            $firstChildren = $firstChildren->first();
    
                            if($firstChildren->name != $k->name){
                                $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($yearAttr,$tahun,$jenisAktif){
                                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){
                                        $q->where('budgeting_type_id',$jenisAktif->id);
                                    })->latest()->aktif();
                                })->whereHas('akun.kategori.parent',function($q)use($k){$q->where('name',$k->name);});
                                if($apbyDetail->count() > 0){
                                    $kategoriValue = $apbyDetail->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');
    
                                    if($k->name == 'Pendapatan'){
                                        $totalPenerimaan = $kategoriValue;
                                        $saldoOperasional += $totalPenerimaan;
                                    }
                                    if($k->name == 'Belanja'){
                                        $totalBelanja = $kategoriValue;
                                        $saldoOperasional -= $totalBelanja;
                                    }
                                    if($k->name == 'Pembiayaan'){
                                        $saldoPembiayaan = $kategoriValue;
                                        $totalAkhir = $saldoOperasional+$saldoPembiayaan;
                                    }
                                }
                            }
                        }

                        $childrens = $children->get();

                        foreach($childrens as $c){
                            $subAnggarans = clone $anggaransQuery;
                            $subAnggarans = $subAnggarans->whereHas('apby',function($q)use($yearAttr,$tahun,$c,$isShowTotal){
                                $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->when(!$isShowTotal,function($q){
                                    return $q->whereHas('detail.akun',function($q){
                                        $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                    });
                                })->whereHas('detail.akun.kategori',function($q)use($c){
                                    $q->where('name',$c->name);
                                })->aktif();
                            });
                            if($subAnggarans->count() > 0){
                                foreach($subAnggarans->get() as $a){
                                    $latestActiveApby = $a->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->when(!$isShowTotal,function($q){
                                        return $q->whereHas('detail.akun',function($q){
                                            $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                        });
                                    })->whereHas('detail.akun.kategori',function($q)use($c){
                                        $q->where('name',$c->name);
                                    })->latest()->aktif()->first();

                                    if($latestActiveApby){
                                        $apbyDetail = $latestActiveApby->detail()->whereHas('akun',function($q){
                                            $q->selectRaw("LENGTH(code)-LENGTH(REPLACE(code, '.', '')) as dots")->having('dots',1);
                                        })->whereHas('akun.kategori',function($q)use($c){
                                            $q->where('name',$c->name);
                                        });
                                            
                                        if($apbyDetail->count() > 0){
                                            foreach($apbyDetail->with('akun:id,code,name,sort_order')->get()->sortBy('akun.sort_order')->all() as $d){
                                                $account = collect([
                                                    [
                                                        'id' => $d->akun->id,
                                                        'code' => $d->akun->code,
                                                        'name' => $d->akun->name,
                                                        'value' => $d->value,
                                                        'valueWithSeparator' => $d->valueWithSeparator 
                                                    ]
                                                ]);
                                                if($accounts){
                                                    $accounts = $accounts->concat($account);
                                                }
                                                else{
                                                    $accounts = $account;
                                                }
                                            }
                                        }
                                        
                                        if(in_array($c->name,['Penerimaan Pembiayaan','Pengeluaran Pembiayaan'])){
                                            $sumApbyDetails = $latestActiveApby->detail()->whereHas('akun.kategori',function($q)use($c){
                                                $q->where('name',$c->name);
                                            })->whereHas('akun',function($q){$q->where('is_fillable',1);})->sum('value');
                                            $account = collect([
                                                [
                                                    'id' => 'c-'.$c->id,
                                                    'code' => null,
                                                    'name' => 'JUMLAH '.strtoupper($c->name),
                                                    'value' => $sumApbyDetails,
                                                    'valueWithSeparator' => number_format($sumApbyDetails, 0, ',', '.')
                                                ]
                                            ]);
                                            if($accounts){
                                                $accounts = $accounts->concat($account);
                                            }
                                            else{
                                                $accounts = $account;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if($isShowTotal && in_array($k->name,['Pendapatan','Belanja','Pembiayaan'])){
                        $account = collect([
                            [
                                'id' => 'k-'.$k->id,
                                'code' => null,
                                'name' => ($k->name == "Pembiayaan" ? 'SALDO ' : 'TOTAL ').strtoupper($k->name),
                                'value' => $kategoriValue,
                                'valueWithSeparator' => number_format($kategoriValue, 0, ',', '.')
                            ]
                        ]);
                        if($accounts){
                            $accounts = $accounts->concat($account);
                        }
                        else{
                            $accounts = $account;
                        }

                        if($k->name == "Belanja"){
                            $account = collect([
                                [
                                    'id' => 's-'.$k->id,
                                    'code' => null,
                                    'name' => 'SALDO OPERASIONAL',
                                    'value' => $saldoOperasional,
                                    'valueWithSeparator' => number_format($saldoOperasional, 0, ',', '.')
                                ]
                            ]);
                            if($accounts){
                                $accounts = $accounts->concat($account);
                            }
                            else{
                                $accounts = $account;
                            }
                        }
                    }
                }
                
                if($isShowTotal){
                    $account = collect([
                        [
                            'id' => 'sum',
                            'code' => null,
                            'name' => 'TOTAL SALDO OPERASIONAL DAN PEMBIAYAAN',
                            'value' => $totalAkhir,
                            'valueWithSeparator' => number_format($totalAkhir, 0, ',', '.')
                        ]
                    ]);
                    if($accounts){
                        $accounts = $accounts->concat($account);
                    }
                    else{
                        $accounts = $account;
                    }
                }
                
                if($accounts && $accounts->count() > 0){
                    $i = 1;
                    foreach($accounts->groupBy('id') as $d){
                        $data = $d->first();
                        $value = null;
                        if(count($d) > 0){
                            $value = $d->sum('value');
                        }
                        $spreadsheet->getActiveSheet()
                            ->setCellValueExplicit('A'.$kolom, $data['code'], DataType::TYPE_STRING)
                            ->setCellValue('B'.$kolom, $data['name'])
                            ->setCellValue('C'.$kolom, abs($value ? $value : $data['value']));
                        if($data['code']){
                            $kolom++;
                        }
                        elseif($i < $accounts->groupBy('id')->count()){
                            if(in_array($data['name'],['JUMLAH PENERIMAAN PEMBIAYAAN','JUMLAH PENGELUARAN PEMBIAYAAN'])){
                                $spreadsheet->getActiveSheet()->getStyle('B'.$kolom)->applyFromArray($totalTopBottomBold);
                            }
                            else{
                                $spreadsheet->getActiveSheet()->getStyle('A'.$kolom.':B'.$kolom)->applyFromArray($totalTopBottomBold);
                            }
                            $kolom += 2;
                        }
                        $i++;
                    }
                }

                $maxRow = $kolom;
            }

            $kolom += 2;

            //TTD
            $ttdStartRow = $ttdEndRow = null;
            $jabatan = $isKso ? Jabatan::where('code','19')->first() : Jabatan::where('code','18')->first();
            $pejabat = $jabatan->pegawaiUnit()->whereHas('pegawai',function($q){
                $q->aktif();
            })->first()->pegawai;

            if($pejabat){
                $ttdStartRow = $kolom;
                $apby = $isKso ? Apby::where('director_acc_status_id', 1) : Apby::where('president_acc_status_id', 1);
                $apby = $apby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                    $query->where('budgeting_type_id',$jenisAktif->id);
                })->latest()->aktif();
                $apby = $isKso ? $apby->orderBy('director_acc_time','DESC') : $apby->orderBy('president_acc_time','DESC');
                $apby = $apby->first();

                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$kolom++, 'Tangerang Selatan, '.($apby ? ($isKso ? Date::parse($apby->director_acc_time)->format('j F Y') : Date::parse($apby->president_acc_time)->format('j F Y')) : Date::now('Asia/Jakarta')->format('j F Y')));
                $kolom++;
                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$kolom++, $jabatan->name == 'Ketua Yayasan' ? 'Ketua Pengurus' : 'Direktur')
                ->setCellValue('A'.$kolom++, 'YAYASAN MUDA INCOMSO');
                $kolom+=4;
                $spreadsheet->getActiveSheet()
                ->setCellValue('A'.$kolom++, ($apby ? ($isKso ? $apby->accDirektur->name : $apby->accKetua->name) : $pejabat->name));

                $ttdEndRow = $kolom-1;
            }

            $spreadsheet->getActiveSheet()->setTitle($jenisAktif->name.' '.(!$isYear ? $tahun->academicYearLink : $tahun));

            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(65);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);

            $styleArray = [
                'font' => [
                    'size' => 16,
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ]
            ];

            $spreadsheet->getActiveSheet()->getStyle('A1:A2')->applyFromArray($styleArray);

            $styleArray = [
                'font' => [
                    'size' => 16,
                    'bold' => true
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A4:C4')->applyFromArray($styleArray);

            $spreadsheet->getActiveSheet()->getStyle('A5:C'.$maxRow)->applyFromArray($borderVertical);

            $styleArray = [
                'font' => [
                    'size' => 16
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'outline' => [
                        'borderStyle' => Border::BORDER_THIN
                    ],
                ],
            ];

            $spreadsheet->getActiveSheet()->getStyle('A5:C'.$maxRow)->applyFromArray($styleArray);

            //Alignment

            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT
                ]
            ];

            $spreadsheet->getActiveSheet()->getStyle('A5:B'.$maxRow)->applyFromArray($styleArray);

            $styleArray = [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT
                ],
                'numberFormat' => [
                    'formatCode' => $FORMAT_CURRENCY_IDR
                ]
            ];

            $spreadsheet->getActiveSheet()->getStyle('C5:C'.$maxRow)->applyFromArray($styleArray);

            $styleArray = [
                'font' => [
                    'size' => 16
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ];

            if($ttdStartRow && $ttdEndRow) $spreadsheet->getActiveSheet()->getStyle('A'.$ttdStartRow.':A'.$ttdEndRow)->applyFromArray($styleArray);

            // $writer = new Xls($spreadsheet);

            // header('Content-Type: application/vnd.ms-excel');
            // header('Content-Disposition: attachment;filename="'.strtolower(str_replace(' ', '-', $jenisAktif->name)).'_'.(!$isYear ? $tahun->academicYearLink : $tahun).'.xls"');
            // header('Cache-Control: max-age=0');

            // $writer->save('php://output');
                        
            // ob_end_flush();

            $writer = IOFactory::createWriter($spreadsheet,'Xlsx');

            $headers = [
                'Cache-Control' => 'max-age=0',
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment;filename="'.strtolower(str_replace(' ', '-', $jenisAktif->name)).'_'.(!$isYear ? $tahun->academicYearLink : $tahun).(isset($status) && $status == 'sum' ? '_'.$status : null).'.xlsx"',
            ];

            return response()->stream(function()use($writer){
                $writer->save('php://output');
            }, 200, $headers);
        }

        return redirect()->route('apby.index');
    }

    /**
     * Update the specified resources from storage.
     *
     * @param  \App\Models\Apby\Apby        $apbyAktif
     * @param  \App\Models\Apby\ApbyDetail  $detail
     */
    public function updateParentValues(Apby $apbyAktif, ApbyDetail $detail)
    {
        $childDetail = $detail;
        for($i = 0;$i < $detail->akun->parentsCount;$i++){
            $parent = $apbyAktif->detail()->whereHas('akun',function($query)use($childDetail){
                $query->where(['code' => $childDetail->akun->parentCode,'is_fillable' => 0]);
            })->first();
            if($parent){
                $childs = $apbyAktif->detail()->whereHas('akun',function($query)use($parent){
                    $query->where('code','LIKE',$parent->akun->code.'.%');
                })->with('akun')->get()->pluck('akun')->where('level',$parent->akun->level+1)->pluck('id');
                $childsValue = $apbyAktif->detail()->whereHas('akun',function($query)use($childs){
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
     * Sync the specified resources from storage.
     *
     * @param  \App\Models\Anggaran\JenisAnggaranAnggaran   $anggaranAktif
     * @param  \App\Models\Apby\Apby                        $apbyAktif
     */
    public function syncRkat($anggaranAktif, $apbyAktif, $tahun)
    {
        $yearAttr = strlen($tahun) == 4 ? 'year' : 'academic_year_id';

        $rkat = $anggaranAktif->rkat()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->where('revision',$apbyAktif->revision)->latest()->first();

        if($rkat && $rkat->detail()->count() > 0){
            foreach($apbyAktif->detail as $d){
                $detail = $rkat->detail()->where('account_id',$d->account_id)->first();
                if($detail){
                    if($detail->value != $d->value && $d->akun->is_fillable == 1){
                        $detail->edited_employee_id = Auth::user()->pegawai->id;
                        $detail->edited_status_id = 1;
                    }
                    $detail->value = $d->value;
                    $detail->save();
                }
            }
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
