<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Bms;

use App\Http\Controllers\Controller;
use App\Http\Resources\Keuangan\Bms\LaporanBmsCollection;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsPlan;
use App\Models\Pembayaran\BmsTermin;
use App\Models\Pembayaran\BmsTransaction;
use App\Models\Pembayaran\BmsTransactionCalonSiswa;
use App\Models\Pembayaran\ExchangeTransaction;
use App\Models\Pembayaran\ExchangeTransactionTarget;
use App\Models\Pembayaran\Spp;
use App\Models\Pembayaran\SppBill;
use App\Models\Pembayaran\SppPlan;
use App\Models\Pembayaran\SppTransaction;
use App\Models\Unit;
use stdClass;

class PembayaranBmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Laporan Pembayaran BMS';
        $this->route = 'bms.pembayaran';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$siswa = null)
    {
        if(isset($siswa) && !in_array($siswa,['calon','siswa'])){
            $siswa = 'siswa';
        }

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->unit_id){
            $unit_id = $request->unit_id;
        }else{
            $unit_id = auth()->user()->pegawai->unit_id;
        }
        $unit = 'Semua';

        // $year = isset($request->tahun) ? $request->tahun : date("Y");

        // $bmsTransaction = $siswa == 'calon' ? BmsTransactionCalonSiswa::orderBy('created_at','desc') : BmsTransaction::orderBy('created_at','desc');
        // if($request->bulan){
        //     $month = $request->bulan;
        //     if($unit_id == 5){
        //         $lists = $bmsTransaction->where('month',$month)->where('year',$year)->get();
        //         $units = Unit::all();
        //     }else{
        //         $lists = $bmsTransaction->where('unit_id',$unit_id)->where('year',$year)->where('month',$month)->get();
        //         $units = Unit::find(auth()->user()->pegawai->unit_id);
        //     }
        // }else{
        //     $month = null;
        //     if($unit_id == 5){
        //         $lists = $bmsTransaction->where('year',$year)->get();
        //         $units = Unit::all();
        //     }else{
        //         $lists = $bmsTransaction->where('unit_id',$unit_id)->where('year',$year)->get();
        //         $units = Unit::find(auth()->user()->pegawai->unit_id);
        //     }
        // }
        // dd($lists[0]->unit_id);

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

        //$tahun_akademik_aktif = TahunAjaran::where('is_active',1)->first();
        if($unit_id == 5){
            $plan_list = BmsPlan::where('academic_year_id',$year->id)->get();
            $plan = new stdClass;
            $plan->total_plan = 0;
            $plan->total_get = 0;
            $plan->total_student = 0;
            $plan->student_remain = 0;
            $plan->remain = 0;
            $plan->percent = 0;
            foreach($plan_list as $pl){
                $plan->total_plan += $pl->total_plan;
                $plan->total_get += $pl->total_get;
                $plan->total_student += $pl->total_student;
                $plan->student_remain += $pl->student_remain;
                $plan->remain += $pl->remain;
                $plan->percent = ($plan->student_remain / $plan->total_student)*100;
            }
        }else{
            $plan = BmsPlan::where('academic_year_id',$year->id)->where('unit_id',$unit_id)->first();
        }
        // $year_now = date("Y");
        // $years = array();
        // $year_increment = 2019;
        // while($year_increment <= $year_now){
        //     $year_obj = new stdClass();
        //     $year_obj->year=$year_increment;
        //     array_push($years,$year_obj);
        //     $year_increment+=1;
        // }

        $active = $this->active.($siswa ? (' '.ucwords($siswa == 'calon' ? $siswa.' Siswa' : $siswa)) : null);
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','unit','years','year','academicYears','tahunPelajaran','isYear','plan','siswa'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGet(Request $request,$siswa = null)
    {
        if(isset($siswa) && !in_array($siswa,['calon','siswa'])){
            $siswa = 'siswa';
        }

        $year = $request->year;
        $month = $request->month;
        $unit_id = $request->unit_id;
        // $level_id = $request->level_id;

        $datas = null;

        $year = str_replace("-","/",$year);
        $year = TahunAjaran::select('id','academic_year_start','academic_year_end')->where('academic_year',$year)->first();
        
        if($year){
            $bmsTransaction = $siswa == 'calon' ? BmsTransactionCalonSiswa::query() : BmsTransaction::query();

            $datas = $bmsTransaction->when($siswa == 'siswa',function($q){
                return $q->whereIn('exchange_que',array(0,1));
            })->when($month, function($q)use($month,$year){
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

            $datas = $datas->latest()->get();
        }

        $resource = new LaporanBmsCollection($datas);

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
     * Change category the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bmsToSpp(Request $request)
    {
        // dd($request);
        $bms_trx = BmsTransaction::find($request->id);
        if(!$bms_trx){
            return redirect()->back()->with('danger','Pemindahan gagal');
        }
        $nominal = $bms_trx->nominal;
        $student_id = $bms_trx->student_id;
        
        $bms = BMS::where('student_id',$student_id)->first();
        if($bms->bms_remain == 0){
            if($bms->bms_paid + $bms->bms_deduction > $bms->bms_nominal){
                $saldo = $bms->bms_paid + $bms->bms_deduction - $bms->bms_nominal;
                $bms_nominal_return = $nominal - $saldo;
            }else{
                $bms_nominal_return = $nominal;
            }
        }else{
            $bms_nominal_return = $nominal;
        }

        $bms->bms_remain += $bms_nominal_return;
        $bms->bms_paid -= $nominal;
        $bms->save();

        $bms_termins = BmsTermin::where('bms_id',$bms->id)->orderBy('academic_year_id','desc')->get();
        foreach($bms_termins as $termin){
            if($bms_nominal_return > 0){

                $bms_plan = BmsPlan::where('unit_id',$bms->unit_id)->where('academic_year_id',$termin->academic_year_id)->first();
                if($termin->remain == 0) $bms_plan->student_remain += 1;

                $paid = $termin->nominal - $termin->remain;

                if($bms_nominal_return > $paid){
                    
                    $bms_plan->total_get -= $paid;
                    $bms_plan->remain += $paid;

                    $bms_nominal_return -= $paid;
                    $termin->remain += $paid;


                }else{

                    $bms_plan->total_get -= $bms_nominal_return;
                    $bms_plan->remain += $bms_nominal_return;

                    $termin->remain += $bms_nominal_return;
                    $bms_nominal_return = 0;

                }
                $termin->save();
                $bms_plan->save();

            }
        }

        $nominal = $nominal;

        $spp_trx = SppTransaction::create([
            'unit_id' => $bms_trx->unit_id,
            'student_id' => $bms_trx->student_id,
            'month' => $bms_trx->month,
            'year' => $bms_trx->year,
            'nominal' => $bms_trx->nominal,
            'academic_year_id' => $bms_trx->academic_year_id,
            'trx_id' => $bms_trx->trx_id,
            'date' => $bms_trx->date,
        ]);

        $spp_trx->created_at = $bms_trx->created_at;
        $spp_trx->save();

        $spp = Spp::where('student_id',$spp_trx->student_id)->first();

        if($spp->remain == 0){
            $spp->saldo = $spp->saldo + $nominal;
            $spp->save();

        }else{
            $spp_bills = SppBill::where('student_id',$spp_trx->student_id)->where('status',0)->orderBy('month','asc')->orderBy('year','asc')->get();

            $transfered = $nominal;

            foreach($spp_bills as $bill){

                if($nominal > 0){

                    $plan = SppPlan::where('unit_id',$bill->unit_id)->where('month',$bill->month)->where('year',$bill->year)->first();
                    
                    $remaining = $bill->spp_nominal - ($bill->deduction_nominal + $bill->spp_paid);
                    if($nominal >= $remaining){
                        $bill->spp_paid = $bill->spp_nominal - $bill->deduction_nominal;
                        $bill->status = 1;
                        $bill->save();
                        
                        $nominal = $nominal - $remaining;
                        
                        $plan->total_get = $plan->total_get + $remaining;
                        $plan->remain = $plan->remain - $remaining;
                        $plan->student_remain -= 1;
                        $plan->percent = ($plan->student_remain / $plan->total_student) * 100; 
                        $plan->save();
                    }else{
                        $bill->spp_paid = $bill->spp_paid + $nominal;
                        $bill->status = 0;
                        $bill->save();
                        
                        $plan->total_get = $plan->total_get + $nominal;
                        $plan->remain = $plan->remain - $nominal;
                        $plan->save();
                        
                        $nominal = 0;

                    }
                }
            }
            if($nominal == 0){
                $spp->remain = $spp->remain - $transfered;
                $spp->paid = $spp->paid + $transfered;
                $spp->save();
            }else{
                $spp->remain = 0;
                $spp->paid = $spp->paid + ($transfered - $nominal);
                $spp->saldo = $spp->saldo + $nominal;
                $spp->save();
            }
        }

        $bms_trx->delete();

        return redirect()->back()->with('success','Pemindahan berhasil');
    }

    /**
     * Change the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeTransaction(Request $request)
    {
        $isCandidateOrigin = isset($request->is_student) && $request->is_student == 0 ? true : false;
        $isCandidateTarget = isset($request->category_split) && $request->category_split == 'calon' ? true : false;

        $refundValue = (int)str_replace('.','',$request->refund);
        $nominalSiswaValue = (int)str_replace('.','',$request->nominal_siswa);
        $nominalSplitValue = (int)str_replace('.','',$request->nominal_split);

        $bms_trx = $isCandidateOrigin ? BmsTransactionCalonSiswa::find($request->id) : BmsTransaction::find($request->id);
        $exchange = ExchangeTransaction::create([
            'transaction_id' => $request->id,
            'origin' => $isCandidateOrigin ? 3 : 1,
            'nominal' => $bms_trx->nominal,
            'refund' => $refundValue,
        ]);
        ExchangeTransactionTarget::create([
            'exchange_transaction_id' => $exchange->id,
            'student_id' => $isCandidateOrigin ? $bms_trx->candidate_student_id : $bms_trx->student_id,
            'is_student' => $isCandidateOrigin ? 0 : 1,
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
        $bms_trx->exchange_que = 1;
        $bms_trx->save();
        return redirect()->back()->with('success', 'Berhasil mengajukan pemindahan transaksi');
    }
}
=======
<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Bms;

use App\Http\Controllers\Controller;
use App\Http\Resources\Keuangan\Bms\LaporanBmsCollection;
use Illuminate\Http\Request;

use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsPlan;
use App\Models\Pembayaran\BmsTermin;
use App\Models\Pembayaran\BmsTransaction;
use App\Models\Pembayaran\BmsTransactionCalonSiswa;
use App\Models\Pembayaran\ExchangeTransaction;
use App\Models\Pembayaran\ExchangeTransactionTarget;
use App\Models\Pembayaran\Spp;
use App\Models\Pembayaran\SppBill;
use App\Models\Pembayaran\SppPlan;
use App\Models\Pembayaran\SppTransaction;
use App\Models\Unit;
use stdClass;

class PembayaranBmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Laporan Pembayaran BMS';
        $this->route = 'bms.pembayaran';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$siswa = null)
    {
        if(isset($siswa) && !in_array($siswa,['calon','siswa'])){
            $siswa = 'siswa';
        }

        $unit_id = auth()->user()->pegawai->unit_id;
        if($request->unit_id){
            $unit_id = $request->unit_id;
        }else{
            $unit_id = auth()->user()->pegawai->unit_id;
        }
        $unit = 'Semua';

        // $year = isset($request->tahun) ? $request->tahun : date("Y");

        // $bmsTransaction = $siswa == 'calon' ? BmsTransactionCalonSiswa::orderBy('created_at','desc') : BmsTransaction::orderBy('created_at','desc');
        // if($request->bulan){
        //     $month = $request->bulan;
        //     if($unit_id == 5){
        //         $lists = $bmsTransaction->where('month',$month)->where('year',$year)->get();
        //         $units = Unit::all();
        //     }else{
        //         $lists = $bmsTransaction->where('unit_id',$unit_id)->where('year',$year)->where('month',$month)->get();
        //         $units = Unit::find(auth()->user()->pegawai->unit_id);
        //     }
        // }else{
        //     $month = null;
        //     if($unit_id == 5){
        //         $lists = $bmsTransaction->where('year',$year)->get();
        //         $units = Unit::all();
        //     }else{
        //         $lists = $bmsTransaction->where('unit_id',$unit_id)->where('year',$year)->get();
        //         $units = Unit::find(auth()->user()->pegawai->unit_id);
        //     }
        // }
        // dd($lists[0]->unit_id);

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

        //$tahun_akademik_aktif = TahunAjaran::where('is_active',1)->first();
        if($unit_id == 5){
            $plan_list = BmsPlan::where('academic_year_id',$year->id)->get();
            $plan = new stdClass;
            $plan->total_plan = 0;
            $plan->total_get = 0;
            $plan->total_student = 0;
            $plan->student_remain = 0;
            $plan->remain = 0;
            $plan->percent = 0;
            foreach($plan_list as $pl){
                $plan->total_plan += $pl->total_plan;
                $plan->total_get += $pl->total_get;
                $plan->total_student += $pl->total_student;
                $plan->student_remain += $pl->student_remain;
                $plan->remain += $pl->remain;
                $plan->percent = ($plan->student_remain / $plan->total_student)*100;
            }
        }else{
            $plan = BmsPlan::where('academic_year_id',$year->id)->where('unit_id',$unit_id)->first();
        }
        // $year_now = date("Y");
        // $years = array();
        // $year_increment = 2019;
        // while($year_increment <= $year_now){
        //     $year_obj = new stdClass();
        //     $year_obj->year=$year_increment;
        //     array_push($years,$year_obj);
        //     $year_increment+=1;
        // }

        $active = $this->active.($siswa ? (' '.ucwords($siswa == 'calon' ? $siswa.' Siswa' : $siswa)) : null);
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','unit','years','year','academicYears','tahunPelajaran','isYear','plan','siswa'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexGet(Request $request,$siswa = null)
    {
        if(isset($siswa) && !in_array($siswa,['calon','siswa'])){
            $siswa = 'siswa';
        }

        $year = $request->year;
        $month = $request->month;
        $unit_id = $request->unit_id;
        // $level_id = $request->level_id;

        $datas = null;

        $year = str_replace("-","/",$year);
        $year = TahunAjaran::select('id','academic_year_start','academic_year_end')->where('academic_year',$year)->first();
        
        if($year){
            $bmsTransaction = $siswa == 'calon' ? BmsTransactionCalonSiswa::query() : BmsTransaction::query();

            $datas = $bmsTransaction->when($siswa == 'siswa',function($q){
                return $q->whereIn('exchange_que',array(0,1));
            })->when($month, function($q)use($month,$year){
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

            $datas = $datas->latest()->get();
        }

        $resource = new LaporanBmsCollection($datas);

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
     * Change category the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bmsToSpp(Request $request)
    {
        // dd($request);
        $bms_trx = BmsTransaction::find($request->id);
        if(!$bms_trx){
            return redirect()->back()->with('danger','Pemindahan gagal');
        }
        $nominal = $bms_trx->nominal;
        $student_id = $bms_trx->student_id;
        
        $bms = BMS::where('student_id',$student_id)->first();
        if($bms->bms_remain == 0){
            if($bms->bms_paid + $bms->bms_deduction > $bms->bms_nominal){
                $saldo = $bms->bms_paid + $bms->bms_deduction - $bms->bms_nominal;
                $bms_nominal_return = $nominal - $saldo;
            }else{
                $bms_nominal_return = $nominal;
            }
        }else{
            $bms_nominal_return = $nominal;
        }

        $bms->bms_remain += $bms_nominal_return;
        $bms->bms_paid -= $nominal;
        $bms->save();

        $bms_termins = BmsTermin::where('bms_id',$bms->id)->orderBy('academic_year_id','desc')->get();
        foreach($bms_termins as $termin){
            if($bms_nominal_return > 0){

                $bms_plan = BmsPlan::where('unit_id',$bms->unit_id)->where('academic_year_id',$termin->academic_year_id)->first();
                if($termin->remain == 0) $bms_plan->student_remain += 1;

                $paid = $termin->nominal - $termin->remain;

                if($bms_nominal_return > $paid){
                    
                    $bms_plan->total_get -= $paid;
                    $bms_plan->remain += $paid;

                    $bms_nominal_return -= $paid;
                    $termin->remain += $paid;


                }else{

                    $bms_plan->total_get -= $bms_nominal_return;
                    $bms_plan->remain += $bms_nominal_return;

                    $termin->remain += $bms_nominal_return;
                    $bms_nominal_return = 0;

                }
                $termin->save();
                $bms_plan->save();

            }
        }

        $nominal = $nominal;

        $spp_trx = SppTransaction::create([
            'unit_id' => $bms_trx->unit_id,
            'student_id' => $bms_trx->student_id,
            'month' => $bms_trx->month,
            'year' => $bms_trx->year,
            'nominal' => $bms_trx->nominal,
            'academic_year_id' => $bms_trx->academic_year_id,
            'trx_id' => $bms_trx->trx_id,
            'date' => $bms_trx->date,
        ]);

        $spp_trx->created_at = $bms_trx->created_at;
        $spp_trx->save();

        $spp = Spp::where('student_id',$spp_trx->student_id)->first();

        if($spp->remain == 0){
            $spp->saldo = $spp->saldo + $nominal;
            $spp->save();

        }else{
            $spp_bills = SppBill::where('student_id',$spp_trx->student_id)->where('status',0)->orderBy('month','asc')->orderBy('year','asc')->get();

            $transfered = $nominal;

            foreach($spp_bills as $bill){

                if($nominal > 0){

                    $plan = SppPlan::where('unit_id',$bill->unit_id)->where('month',$bill->month)->where('year',$bill->year)->first();
                    
                    $remaining = $bill->spp_nominal - ($bill->deduction_nominal + $bill->spp_paid);
                    if($nominal >= $remaining){
                        $bill->spp_paid = $bill->spp_nominal - $bill->deduction_nominal;
                        $bill->status = 1;
                        $bill->save();
                        
                        $nominal = $nominal - $remaining;
                        
                        $plan->total_get = $plan->total_get + $remaining;
                        $plan->remain = $plan->remain - $remaining;
                        $plan->student_remain -= 1;
                        $plan->percent = ($plan->student_remain / $plan->total_student) * 100; 
                        $plan->save();
                    }else{
                        $bill->spp_paid = $bill->spp_paid + $nominal;
                        $bill->status = 0;
                        $bill->save();
                        
                        $plan->total_get = $plan->total_get + $nominal;
                        $plan->remain = $plan->remain - $nominal;
                        $plan->save();
                        
                        $nominal = 0;

                    }
                }
            }
            if($nominal == 0){
                $spp->remain = $spp->remain - $transfered;
                $spp->paid = $spp->paid + $transfered;
                $spp->save();
            }else{
                $spp->remain = 0;
                $spp->paid = $spp->paid + ($transfered - $nominal);
                $spp->saldo = $spp->saldo + $nominal;
                $spp->save();
            }
        }

        $bms_trx->delete();

        return redirect()->back()->with('success','Pemindahan berhasil');
    }

    /**
     * Change the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function changeTransaction(Request $request)
    {
        $isCandidateOrigin = isset($request->is_student) && $request->is_student == 0 ? true : false;
        $isCandidateTarget = isset($request->category_split) && $request->category_split == 'calon' ? true : false;

        $refundValue = (int)str_replace('.','',$request->refund);
        $nominalSiswaValue = (int)str_replace('.','',$request->nominal_siswa);
        $nominalSplitValue = (int)str_replace('.','',$request->nominal_split);

        $bms_trx = $isCandidateOrigin ? BmsTransactionCalonSiswa::find($request->id) : BmsTransaction::find($request->id);
        $exchange = ExchangeTransaction::create([
            'transaction_id' => $request->id,
            'origin' => $isCandidateOrigin ? 3 : 1,
            'nominal' => $bms_trx->nominal,
            'refund' => $refundValue,
        ]);
        ExchangeTransactionTarget::create([
            'exchange_transaction_id' => $exchange->id,
            'student_id' => $isCandidateOrigin ? $bms_trx->candidate_student_id : $bms_trx->student_id,
            'is_student' => $isCandidateOrigin ? 0 : 1,
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
        $bms_trx->exchange_que = 1;
        $bms_trx->save();
        return redirect()->back()->with('success', 'Berhasil mengajukan pemindahan transaksi');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
