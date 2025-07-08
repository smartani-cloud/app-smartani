<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use App\Http\Resources\Keuangan\Spp\LaporanSppMasukanCollection;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\ExchangeTransaction;
use App\Models\Pembayaran\ExchangeTransactionTarget;
use App\Models\Pembayaran\SppTransaction;
use App\Models\Level;
use App\Models\Unit;
use stdClass;

class PembayaranSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Laporan Pembayaran SPP';
        $this->route = 'spp.pembayaran';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $levels = Level::all();
        $level = 'semua';
        // if($request->unit_id){
        //     $unit_id = $request->unit_id;
        // }else{
        //     $unit_id = auth()->user()->pegawai->unit_id;
        // }

        // if($request->tahun){
        //     $year = $request->tahun;
        // }else{
        //     $year = date("Y");
        // }

        // if($unit_id == 5){
        //     $levels = Level::where('unit_id',1)->get();
        //     $unit_id = 1;
        // }else{
        //     $levels = Level::where('unit_id',$unit_id)->get();
        // }
        
        // // $month = $request->bulan;
        // if($request->bulan){
        //     $month = $request->bulan;
        //     $datas = SppTransaction::where('unit_id',$unit_id)->where('year',$year)->orderBy('created_at','desc')->where('month',$month)->get();
        //     // dd($datas);
        //     if($unit_id == 5){
        //         $units = Unit::all();
        //     }else{
        //         $units = Unit::find(auth()->user()->pegawai->unit_id);
        //     }
        // }else{
        //     $month = null;
        //     if($unit_id == 5){
        //         $datas = SppTransaction::orderBy('created_at','desc')->where('year',$year)->get();
        //         $units = Unit::all();
        //     }else{
        //         $datas = SppTransaction::where('unit_id',$unit_id)->orderBy('created_at','desc')->where('year',$year)->get();
        //         $units = Unit::find(auth()->user()->pegawai->unit_id);
        //     }
        //     // dd($datas);
        // }
        // $year_now = date("Y");
        // $years = array();
        // $year_increment = 2019;
        // while($year_increment <= $year_now){
        //     $year_obj = new stdClass();
        //     $year_obj->year=$year_increment;
        //     array_push($years,$year_obj);
        //     $year_increment+=1;
        // }

        // Use Academic Year
        $year = $request->year;
        $years = $academicYears = null;
        $isYear = false;

        $queryData = SppTransaction::query();

        if($queryData->count() > 0){
            $years = clone $queryData;
            $yearsCount = $years->whereNotNull('year')->count();
            $years = $years->whereNotNull('year')->orderBy('year')->pluck('year')->unique();

            $academicYears = TahunAjaran::select('id','academic_year','academic_year_start','academic_year_end')->where('academic_year_start','>=',$years->min())->where('academic_year_end','<=',$years->max())->orderBy('academic_year')->get();
        }

        $tahunPelajaran = TahunAjaran::where('is_active',1)->latest()->take(1)->get();

        if($academicYears && $academicYears->count() > 0){
            $tahunPelajaran = TahunAjaran::where(function($q)use($academicYears){
                $q->where(function($q){
                    $q->where('is_active',1);
                })->orWhere(function($q)use($academicYears){
                    $q->whereIn('id',$academicYears);
                });
            })->orderBy('created_at')->get();
        }

        if(!$isYear){
            if($year){
                $year = str_replace("-","/",$year);
                $year = TahunAjaran::where('academic_year',$year)->first();
            }
            else{
                // Default Value
                $year = TahunAjaran::where('is_active',1)->latest()->first();
            }
            if(!$year) return redirect()->route($this->route.'.index');
        }
        else{
            // Default Value
            if(!$year){
                if($yearsCount > 0){
                    $year = $years->last();
                }
                else{
                    $year = Date::now('Asia/Jakarta')->format('Y');
                }
            }
            else{
                if($yearsCount > 0){
                    if(!in_array($year,$years->toArray())) $year = null;
                }
                else{
                    if($year != Date::now('Asia/Jakarta')->format('Y')) $year = null;
                }
            }
            if(!$year){
                return redirect()->route($this->route.'.index');
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','levels','level','year','years','academicYears','tahunPelajaran','isYear'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGet(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $unit_id = $request->unit_id;
        // $level_id = $request->level_id;

        $datas = null;

        $year = str_replace("-","/",$year);
        $year = TahunAjaran::select('id','academic_year_start','academic_year_end')->where('academic_year',$year)->first();
        
        if($year){
            $datas = SppTransaction::whereIn('exchange_que',array(0,1))->when($month, function($q)use($month,$year){
                return $q->where('month', $month)->when($month >= 7, function($q)use($year){
                    return $q->where('year',$year->academic_year_start);
                }, function($q)use($year){
                    return $q->where('year',$year->academic_year_end);
                });
            }, function($q)use($year){
                return $q->where(function($q)use($year){
                    $q->where(function($q)use($year){
                        $q->where('year',$year->academic_year_start)->where('month', '>=', 7);
                    })->orWhere(function($q)use($year){
                        $q->where('year',$year->academic_year_end)->where('month', '<=', 6);
                    });
                });
            });

            if($request->user()->pegawai->unit_id == 5){
                $datas = $datas->where('unit_id',$unit_id);
            }else{
                $datas = $datas->where('unit_id',$request->user()->pegawai->unit_id);
            }

            $datas = $datas->orderBy('month','DESC')->get();
        }

        $resource = new LaporanSppMasukanCollection($datas);

        return response()->json([$resource],200);
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    /**
     * Change the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeTransaction(Request $request)
    {
        $isCandidateTarget = isset($request->category_split) && $request->category_split == 'calon' ? true : false;

        $refundValue = (int)str_replace('.','',$request->refund);
        $nominalSiswaValue = (int)str_replace('.','',$request->nominal_siswa);
        $nominalSplitValue = (int)str_replace('.','',$request->nominal_split);
        
        $spp_trx = SppTransaction::find($request->id);
        $exchange = ExchangeTransaction::create([
            'transaction_id' => $request->id,
            'origin' => 2,
            'nominal' => $spp_trx->nominal,
            'refund' => $refundValue,
        ]);
        ExchangeTransactionTarget::create([
            'exchange_transaction_id' => $exchange->id,
            'student_id' => $spp_trx->student_id,
            'nominal' => $nominalSiswaValue,
            'transaction_type' => $request->jenis_pembayaran,
        ]);

        if($request->split == 1){
            ExchangeTransactionTarget::create([
                'exchange_transaction_id' => $exchange->id,
                'student_id' => $request->siswa_split,
                'is_student' => $isCandidateTarget ? 0 : 1,
                'nominal' => $nominalSplitValue,
                'transaction_type' => $request->jenis_pembayaran_split,
            ]);
        }
        $spp_trx->exchange_que = 1;
        $spp_trx->save();
        return redirect()->back()->with('success', 'Berhasil mengajukan pemindahan transaksi');
    }
}
