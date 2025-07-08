<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Anggaran\Anggaran;
use App\Models\Anggaran\JenisAnggaran;
use App\Models\Apby\Apby;
use App\Models\Apby\ApbyDetail;
use App\Models\Ppa\Ppa;
use App\Models\Ppa\PpaDetail;
use App\Models\Lppa\Lppa;
use App\Models\Lppa\LppaDetail;
use App\Models\Kbm\TahunAjaran;
use Illuminate\Http\Request;

use Session;
use Jenssegers\Date\Date;

class RealisasiController extends Controller
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
            if(!in_array($request->user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','fas','akunspv'])){
                if($request->user()->pegawai->unit_id == '5'){
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('position_id',$request->user()->pegawai->jabatan->group()->first()->id);});
                }
                else{
                    $anggaranCount = $anggaranCount->whereHas('anggaran',function($q)use($request){$q->where('unit_id',$request->user()->pegawai->unit_id);});
                }
            }
            if($j->isKso){
                $anggaranCount = $anggaranCount->whereHas('apby',function($q){$q->where('director_acc_status_id',1);})->count();
            }
            else{
                $anggaranCount = $anggaranCount->whereHas('apby',function($q){$q->where('president_acc_status_id',1);})->count();
            }
            if($jenisAnggaranCount){
                $jenisAnggaranCount = $jenisAnggaranCount->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
            else{
                $jenisAnggaranCount = collect()->push(['id' => $j->id, 'anggaranCount' => $anggaranCount]);
            }
        }
        
        $jenisAktif = $years = $academicYears = $latest = $tahunPelajaran = $isYear = $apby = $budgetings = $datasets = $total = null;
        $yearsCount = $academicYearsCount = 0;
        $isKso = false;

        if($jenis){
            $explodeJenis = explode('-',$jenis);
            $isKso = count($explodeJenis) > 1 && $explodeJenis[1] == 'kso'? true : false;
            $jenisAktif = JenisAnggaran::where('link',$jenis)->whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if(!$jenisAktif) return redirect()->route('realisasi.index');

            $queryApby = Apby::select('id','year','academic_year_id','budgeting_budgeting_type_id','total_value','total_used','total_balance')->where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });

            $queryPpa = Ppa::select('id','year','academic_year_id','budgeting_budgeting_type_id','total_value')->where(function($q){
                $q->where(function($q){
                    $q->whereNotNull('year');
                })->orWhere(function($q){
                    $q->has('tahunPelajaran');
                });
            })->whereHas('jenisAnggaranAnggaran',function($query)use($jenisAktif){
                $query->where('budgeting_type_id',$jenisAktif->id);
            });

            $queryApby = $isKso ? $queryApby->where('director_acc_status_id',1) : $queryApby->where('president_acc_status_id',1);

            $queryPpa = $isKso ? $queryPpa->whereHas('bbk.bbk',function($query){
                $query->where('director_acc_status_id',1);
            }) : $queryPpa->whereHas('bbk.bbk',function($query){
                $query->where('president_acc_status_id',1);
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
                if($request->tahun){
                    $tahun = str_replace("-","/",$request->tahun);
                    $tahun = $tahunPelajaran->where('academic_year',$tahun)->first();
                }
                else{
                    $tahun = $tahunPelajaran->sortByDesc('created_at')->first();
                }
                if(!$tahun) return redirect()->route('realisasi.index');
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
                    return redirect()->route('lppa.index');
                }
            }

            $yearAttr = $isYear ? 'year' : 'academic_year_id';

            if($jenisAktif){
                $apby = clone $queryApby;
                $apby = $apby->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->aktif()->latest()->get()->sortBy('jenisAnggaranAnggaran.number');

                $ppa = clone $queryPpa;
                $ppa = $ppa->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->with('jenisAnggaranAnggaran',function($q){$q->select('id','number','budgeting_type_id','budgeting_id')->with('anggaran:id,name');})->get();

                foreach($apby as $a){
                    $ppaTotalValue = $ppa->where('budgeting_budgeting_type_id',$a->jenisAnggaranAnggaran->id)->sum('total_value');
                    $ppbValue[$a->jenisAnggaranAnggaran->id]['used'] = number_format($ppaTotalValue, 0, ',', '.');
                    $ppbValue[$a->jenisAnggaranAnggaran->id]['balance'] = number_format($a->total_value-$ppaTotalValue, 0, ',', '.');

                    $rppas = Lppa::whereHas('ppa',function($query)use($a,$yearAttr,$tahun){
                        $query->where([
                            'budgeting_budgeting_type_id' => $a->jenisAnggaranAnggaran->id,
                            $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id)
                        ]);
                    })->has('detail')->with('detail:id,value')->get();
                    $rppaTotalValue = 0;
                    foreacH($rppas as $r){
                        $rppaTotalValue += $r->detail()->sum('value');
                    }
                    $rppaValue[$a->jenisAnggaranAnggaran->id]['used'] = number_format($rppaTotalValue, 0, ',', '.');
                    $rppaValue[$a->jenisAnggaranAnggaran->id]['balance'] = number_format($a->total_value-$rppaTotalValue, 0, ',', '.');
                }
                
                $budgetings = $apby ? $apby->pluck('jenisAnggaranAnggaran') : null;

                $totalAnggaran = 0;

                // Counter
                $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($apby){$q->whereIn('id',$apby->pluck('id'));});

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
                })->sum('used');

                $total = collect([
                    'pendapatanPembiayaan' => $totalPendapatan + $totalPembiayaan,
                    'belanja' => $totalBelanja,
                    'operasionalPembiayaan' => $totalPendapatan - $totalBelanja + $totalPembiayaan
                ]);
                
                if($budgetings && count($budgetings) > 0){
                    $num = 12;
                    $skippedNum = ['24','25','26','28','29','30','31','36','37','38','39','41','47'];
                    
                    $items = ['Rencana','Realisasi','Selisih'];
                    foreach($items as $i){
                        $dataArr = null;
                        foreach($budgetings as $b){
                            if(!$dataArr)
                                $dataArr = array();
                            if($i == 'Rencana')
                                $dataArr[] = $apby->where('budgeting_budgeting_type_id',$b->id)->pluck('total_value');
                            elseif($i == 'Realisasi')
                                $dataArr[] = $apby->where('budgeting_budgeting_type_id',$b->id)->pluck('total_used');
                            elseif($i == 'Selisih')
                                $dataArr[] = $apby->where('budgeting_budgeting_type_id',$b->id)->pluck('total_balance');
                        }
                        if(!$datasets){
                            $datasets = collect([
                                [   
                                    'label' => $i,
                                    'backgroundColor' => $this->getColor($num),
                                    'data' => $dataArr
                                ]
                            ]);
                        }
                        else{
                            $dataset = collect([
                                [   
                                    'label' => $i,
                                    'backgroundColor' => $this->getColor($num),
                                    'data' => $dataArr
                                ]
                            ]);
                            $datasets = $datasets->concat($dataset);
                        }
                        $num++;
                        while(in_array($num,$skippedNum)){
                            $num++;
                        }
                    }
                }

                if($anggaran){
                    $anggaranAktif = Anggaran::where('name','LIKE',str_replace('-',' ',$anggaran))->whereIn('id',$apby->pluck('jenisAnggaranAnggaran.anggaran')->pluck('id'))->first();
                    if($anggaranAktif){
                        $anggaranAktif = $anggaranAktif->jenisAnggaran()->where('budgeting_type_id',$jenisAktif->id)->first();
                        $apbyAktif = $anggaranAktif->apby()->whereIn('id',$apby->pluck('id')->unique())->first();

                        if($apbyAktif){
                            // Inti controller

                            $ppaDetail = PpaDetail::whereHas('ppa',function($q)use($anggaranAktif,$yearAttr,$tahun,$isKso){
                                $q->where([
                                    'budgeting_budgeting_type_id' => $anggaranAktif->id,
                                    $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id)
                                ])->whereHas('bbk.bbk',function($q)use($isKso){
                                    $q->when($isKso,function($q){
                                        $q->where('director_acc_status_id',1);
                                    },function($q){
                                        $q->where('president_acc_status_id',1);
                                    });
                                });
                            });

                            $apbyDetail = $apbyAktif->detail()->whereHas('akun.kategori.parent',function($q){$q->where('name','Belanja');});
                            $ppbValue = null;

                            if($apbyDetail->count() > 0){
                                foreach($apbyDetail->with('akun')->get()->sortBy('akun.sort_order')->all() as $d){
                                    $ppaTotalValue = clone $ppaDetail;
                                    $ppaTotalValue = $ppaTotalValue->when($d->akun->is_fillable < 1,function($q)use($d){
                                        $q->whereHas('akun',function($q)use($d){$q->where('code','LIKE',$d->akun->code.'%')->where('is_fillable',1);});
                                    },function($q)use($d){
                                        $q->where('account_id',$d->akun->id);
                                    })->sum('value');
                                    $ppbValue[$d->akun->id]['used'] = number_format($ppaTotalValue, 0, ',', '.');
                                    $ppbValue[$d->akun->id]['balance'] = number_format($d->value-$ppaTotalValue, 0, ',', '.');

                                    $rppaTotalValue = LppaDetail::whereHas('lppa',function($q)use($anggaranAktif,$yearAttr,$tahun){
                                        $q->whereHas('ppa',function($q)use($anggaranAktif,$yearAttr,$tahun){
                                            $q->where([
                                                'budgeting_budgeting_type_id' => $anggaranAktif->id,
                                                $yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id)
                                            ]);
                                        });
                                    })->when($d->akun->is_fillable < 1,function($q)use($d){
                                        $q->whereHas('ppaDetail.akun',function($q)use($d){$q->where('code','LIKE',$d->akun->code.'%')->where('is_fillable',1);});
                                    },function($q)use($d){
                                        $q->whereHas('ppaDetail',function($q)use($d){
                                            $q->where('account_id',$d->akun->id);
                                        });
                                    })->sum('value');
                                    $rppaValue[$d->akun->id]['used'] = number_format($rppaTotalValue, 0, ',', '.');
                                    $rppaValue[$d->akun->id]['balance'] = number_format($d->value-$rppaTotalValue, 0, ',', '.');
                                }
                            }

                            $totalAnggaran = 0;

                            // Counter
                            $apbyDetail = ApbyDetail::whereHas('apby',function($q)use($apby){$q->whereIn('id',$apby->pluck('id')->unique());});

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

                            return view('keuangan.read-only.realisasi_detail', compact('jenisAnggaran','jenisAnggaranCount','jenisAktif','tahun','tahunPelajaran','isYear','apby','ppbValue','rppaValue','years','academicYears','isKso','anggaranAktif','apbyAktif','total'));
                        }
                        else return redirect()->route('realisasi.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                    }
                    else return redirect()->route('realisasi.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]);
                }
            }
        }
        elseif(!in_array($role,['pembinayys','ketuayys','direktur','fam','faspv','fas','akunspv'])){
            return 'coba';
            $jenisAktif = JenisAnggaran::whereIn('id',$jenisAnggaranCount->collect()->where('anggaranCount','>',0)->pluck('id'))->first();
            if($jenisAktif){
                return redirect()->route('realisasi.index', ['jenis' => $jenisAktif->link]);
            }
            else{
                return redirect()->route('keuangan.index');
            }
        }

        return view('keuangan.read-only.realisasi_index', compact('jenisAnggaran','jenisAnggaranCount','jenisAktif','tahun','tahunPelajaran','isYear','apby','ppbValue','rppaValue','years','academicYears','isKso','budgetings','datasets','total'));
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
    public function edit(Apby $apby)
    {
        //
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
        //
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
     * Sync the resources in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function sync()
    {
        $apby = Apby::aktif()->get();
        
        foreach($apby as $a){
            $apbyAktif = $a;
            $apbyAktifClone = clone $apbyAktif;
            $apbyAktifDetail = $apbyAktifClone->detail()->whereHas('akun',function($query){
                $query->where('is_fillable',1);
                });
            $apbyAktifDetailClone = clone $apbyAktifDetail;
            if($apbyAktifDetail->count() > 0){
                foreach($apbyAktifDetailClone->with('akun')->get()->sortByDesc('akun.level') as $detail){
                    $usedPpa = $detail->akun->ppa()->whereHas('ppa',function($q)use($apbyAktif){
                        $q->where([
                            'year' => $apbyAktif->year,
                            'academic_year_id' => $apbyAktif->academic_year_id,
                            'budgeting_budgeting_type_id' => $apbyAktif->budgeting_budgeting_type_id,
                        ])->has('lppa');
                    })->sum('value');
                    $surplusLppa = $detail->akun->ppa()->whereHas('ppa',function($q)use($apbyAktif){
                        $q->where([
                            'year' => $apbyAktif->year,
                            'academic_year_id' => $apbyAktif->academic_year_id,
                            'budgeting_budgeting_type_id' => $apbyAktif->budgeting_budgeting_type_id,
                        ])->whereHas('lppa',function($q){
                            $q->where('finance_acc_status_id',1);
                        });
                    })->has('lppaDetail')->get();
                    $sumSurplus = 0;
                    foreach($surplusLppa as $s){
                        $selisih = $s->value - $s->lppaDetail->value;
                        if($selisih > 0) $sumSurplus += $selisih;
                    }
                    $used = $usedPpa - $sumSurplus;
                    // if($used > 0){
                    //     echo $detail->id.' - '.($apbyAktif->academic_year_id ? 'KSO' : 'No').' - '.($apbyAktif->jenisAnggaranAnggaran->anggaran->name).' - '.number_format($detail->used, 0, ',', '.').' - '.number_format($usedPpa, 0, ',', '.').' - '.number_format($used, 0, ',', '.').($detail->used == $used ? ' - Sama' : '').'<br>';
                    // }
                    $detail->used = $used;
                    $detail->balance = $detail->value-$used;
                    $detail->save();

                    $detail->fresh();
                    
                    //$this->updateParentValues($apbyAktif,$detail);

                    echo $detail->id.' - '.($apbyAktif->academic_year_id ? 'KSO' : 'No').' - '.($apbyAktif->jenisAnggaranAnggaran->anggaran->name).' - '.number_format($detail->used, 0, ',', '.').' - '.number_format($usedPpa, 0, ',', '.').' - '.number_format($used, 0, ',', '.').($detail->used == $used ? ' - Sama' : '').'<br>';
                }
            }
            
            $apbyAktifUpdate = clone $apbyAktif;
            
            $totalAnggaran = $apbyAktif->detail()->whereHas('akun',function($query){
                $query->where(['is_fillable' => 1, 'is_static' => 0])->whereHas('kategori.parent',function($q){
                    $q->where('name','Belanja');
                });
            });

            $apbyAktifUpdate->total_used = $totalAnggaran->sum('used');
            $apbyAktifUpdate->total_balance = $totalAnggaran->sum('balance');
            $apbyAktifUpdate->save();
        }
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
                
                $usedValue = $apbyAktif->detail()->whereHas('akun',function($query)use($childs){
                    $query->whereIn('id',$childs);
                })->sum('used');
                $parent->used = $usedValue;
                
                $balanceValue = $apbyAktif->detail()->whereHas('akun',function($query)use($childs){
                    $query->whereIn('id',$childs);
                })->sum('balance');
                $parent->balance = $balanceValue;
                
                $parent->save();

                $parent->fresh();

                $childDetail = $parent;
            }
        }
    }
    
    /**
     * Get RGB colors.
     */
    function getColor($num){
        $hash = md5('color' . $num); // modify 'color' to get a different palette
        return 'rgb('.
            hexdec(substr($hash, 0, 2)).','. // r
            hexdec(substr($hash, 2, 2)).','. // g
            hexdec(substr($hash, 4, 2)).')'; // b
    }
}
