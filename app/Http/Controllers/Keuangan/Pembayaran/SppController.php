<?php

namespace App\Http\Controllers\Keuangan\Pembayaran;

use App\Http\Controllers\Controller;
use App\Http\Resources\Kbm\SiswaDatatableCollection;
use App\Http\Resources\Kbm\SiswaListCollection;
use App\Http\Resources\Keuangan\Spp\LaporanSppMasukanCollection;
use App\Http\Resources\Keuangan\Spp\LaporanSppSiswaCollection;
use App\Http\Resources\Keuangan\Spp\SppSiswaCollection;
use App\Http\Resources\Siswa\SiswaCollection;
use App\Http\Services\Keuangan\SppDeductionService;
use Illuminate\Http\Request;

use NumberHelper;
use Mail;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Storage;

use App\Models\Level;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsPlan;
use App\Models\Pembayaran\BmsTermin;
use App\Models\Pembayaran\BmsTransaction;
use App\Models\Pembayaran\ExchangeTransaction;
use App\Models\Pembayaran\ExchangeTransactionTarget;
use App\Models\Pembayaran\Spp;
use App\Models\Pembayaran\SppBill;
use App\Models\Pembayaran\SppDeduction;
use App\Models\Pembayaran\SppDeductionYear;
use App\Models\Pembayaran\SppPlan;
use App\Models\Pembayaran\SppTransaction;
use App\Models\Pembayaran\SppYearTotal;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\Siswa;
use App\Models\Unit;
// use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use stdClass;

class SppController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $unit_id = auth()->user()->pegawai->unit_id;
        if($unit_id == 5){
            $levels = Level::all();
            $unit_id = 1;
        }else{
            $levels = Level::where('unit_id',$unit_id)->get();
        }
        $lists = Siswa::where('unit_id',$unit_id)->where('is_lulus',0)->get();
        $level = 'semua';

        $total = 0;

        $datas = Spp::where('unit_id',$unit_id)->whereHas('siswa', function($q) use ($unit_id){
            $q->where('unit_id',$unit_id)->where('is_lulus',0);
        })->get();

        return view('keuangan.pembayaran.spp.index', compact('levels','unit_id','datas','level'));
    }

    public function indexFilter(Request $request)
    {
        //
        if($request->level == 'semua') return redirect()->route('spp.spp-siswa');
        $level = $request->level;
        $unit_id = auth()->user()->pegawai->unit_id;
        if($unit_id == 5){
            $levels = Level::all();
            $unit_id = 1;
        }else{
            $levels = Level::where('unit_id',$unit_id)->get();
        }
        // $lists = Siswa::where('unit_id',$unit_id)->where('level_id',$level)->where('is_lulus',0)->get();

        $datas = Spp::where('unit_id',$unit_id)->whereHas('siswa', function($q) use ($unit_id, $level){
            $q->where('unit_id',$unit_id)->where('level_id',$level)->where('is_lulus',0);
        })->get();
        return view('keuangan.pembayaran.spp.index', compact('levels','level','unit_id','datas'));
    }

    public function indexGet(Request $request)
    {
        
        $unit_id = $request->unit_id;
        $level_id = $request->level_id;

        $datas = Spp::where('unit_id',$unit_id)
            ->when($level_id, function($q, $level_id){
                return $q->whereHas('siswa', function($q) use ($level_id){
                    $q->where('level_id',$level_id);
                });
            })
            ->whereHas('siswa', function($q){
                $q->where('is_lulus',0);
            })
            ->get();
        $data = new SppSiswaCollection($datas);

        return response()->json([$data]);
    }

    public function bulanan()
    {
        //
        $date_now = Carbon::now();

        dd($date_now);

        $unit_id = auth()->user()->pegawai->unit_id;
        $unit = 'Semua';
        if($unit_id == 5){
            $levels = Level::all();
            $lists = SppBill::orderBy('unit_id','asc')->get();
        }else{
            $lists = SppBill::where('unit_id',$unit_id)->orderBy('student_id','asc')->get();
            $levels = Level::where('unit_id',$unit_id);
        }
        $level = 'semua';
        return view('keuangan.pembayaran.spp.bulanan', compact('levels','level','unit_id','lists'));
    }

    public function rencana()
    {

        $unit_id = auth()->user()->pegawai->unit_id;
        $unit = 'Semua';
        if($unit_id == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit_id);
        }
        $level = 'semua';
        return view('keuangan.pembayaran.spp.rencana', compact('levels','level'));
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

    public function log()
    {
        //
        $levels = Level::all();
        $level = 'semua';
        return view('keuangan.pembayaran.spp.log', compact('levels','level'));
    }

    public function LaporanSiswa()
    {
        //
        $year = date("Y");
        $month = date("m");
        $unit_id = auth()->user()->pegawai->unit_id;
        $unit = 'Semua';
        if($unit_id == 5){
            $plan = SppPlan::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->first();
        }else{
            $plan = SppPlan::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->first();
        }
        $deductions = SppDeduction::orderBy('name')->get();

        return view('keuangan.pembayaran.spp.laporan.siswa', compact('plan','deductions'));
    }

    public function LaporanSiswaFilter(Request $request)
    {
        //
        $unit_id = auth()->user()->pegawai->unit_id;
        $unit = 'Semua';
        if($unit_id == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit_id)->get();
        }

        if($request->unit_id){
            $unit_id = $request->unit_id;
        }

        $year = $request->year;
        $month = $request->month;
        $level = $request->level;

        if($month != '0'){
            $plan = SppPlan::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->first();
        }else{
            $month_now = date('m');
            $plan = SppPlan::where('year',$year)->where('month',$month_now)->where('unit_id',$unit_id)->first();
        }

        if($month=='0' && $level=='semua'){
            $datas = SppBill::where('year',$year)->where('unit_id',$unit_id)->get();
        }else if($level=='semua'){
            $datas = SppBill::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->get();
        }else if($month=='semua'){
            $datas = SppBill::where('year',$year)->where('level_id',$level)->where('unit_id',$unit_id)->get();
        }else{
            $datas = SppBill::where('year',$year)->where('level_id',$level)->where('month',$month)->where('level_id',$level)->get();
        }
        // dd($unit_id);
        return view('keuangan.pembayaran.spp.laporan.siswa', compact('levels','level','year','month','datas','plan','unit_id'));
    }

    public function laporanSiswaGet(Request $request)
    {

        $year = $request->year;
        $month = $request->month;
        $unit_id = $request->unit_id;
        $level_id = $request->level_id;

        $datas = SppBill::where('unit_id', $unit_id)
        ->where('year', $year)
        ->when($level_id, function($q, $level_id){
            return $q->where('level_id', $level_id);
        })
        ->when($month, function($q, $month){
            return $q->where('month', $month);
        })
        ->get();

        // $datas = SppBill::whereHas('siswa.identitas.orangtua.pegawai',function($q){$q->where('name','Murtani');})
        // ->where('year', $year)
        // ->when($month, function($q, $month){
        //     return $q->where('month', $month);
        // })->with('siswa.identitas.orangtua')->
        
        // take(1)->get();

        $resource = new LaporanSppSiswaCollection($datas);

        return response()->json([$resource],200);
    }

    public function LaporanSiswaAtur(Request $request)
    {
        //
        $unit_id = auth()->user()->pegawai->unit_id;
        $unit = 'Semua';

        if($unit_id == 5){
            $levels = Level::all();
        }else{
            $levels = Level::where('unit_id',$unit_id)->get();
        }

        if($request->unit_id){
            $unit_id = $request->unit_id;
        }

        $year = $request->year;
        $month = $request->month;
        $level = $request->level;

        $plan = SppPlan::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->first();
        
        $students = Siswa::where('level_id',$level)->where('is_lulus',0)->where('year_spp','<=',$year)->get();
        $nominal = str_replace('.','',$request->spp);

        foreach($students as  $index => $student){

        $have_bill = SppBill::where('unit_id', $student->unit_id)->where('month',$month)->where('year',$year)->where('student_id',$student->id)->first();
        // if($index > 2)dd($have_bill);
        if(!$have_bill){
            $selected = false;
            if($student->year_spp < intval($year)){
                $selected = true;
                // if($student->id == 1879)dd($student->year_spp,$year);
            }elseif($student->month_spp <= intval($month)){
                // if($student->id == 1879)dd($student->month_spp,$month);
                $selected = true;
            }

            if($selected == true){
                $sppDeduction = null;
                $deduction = 0;
                $employeeParent = Siswa::where('id',$student->id)->has('identitas.orangtua.pegawai.statusPegawai')->first();
                if($employeeParent){
                    $sppDeduction = SppDeduction::whereNotNull('percentage')->where('employee_status_id',$employeeParent->identitas->orangtua->pegawai->employee_status_id)->latest()->first();
                    if($sppDeduction) $deduction = ($sppDeduction->percentage/100)*$nominal;
                }
            
                $spp_add_bill = SppBill::create([
                    'unit_id' => $student->unit_id,
                    'level_id' => $level,
                    'student_id'  => $student->id,
                    'month' => $month,
                    'year' => $year,
                    'spp_nominal' => $nominal,
                    'deduction_nominal' => $deduction,
                ]);

                $spp_student = Spp::where('student_id', $student->id)->first();
                if(!$spp_student){
                    $spp_student = Spp::create([
                        'unit_id' => $student->unit_id,
                        'student_id' => $student->id,
                        'saldo' => 0,
                        'total' => 0,
                        'deduction' => 0,
                        'remain' => 0,
                        'paid' => 0,
                    ]);
                }
                $spp_student->total += $nominal;
                $spp_student->deduction += $deduction;
                $plan = SppPlan::where('unit_id',$student->unit_id)->where('month',$month)->where('year',$year)->first();

                if(!$plan){
                    $plan = SppPlan::create([
                        'unit_id' => $student->unit_id,
                        'month' => $month,
                        'year' => $year,
                        'total_plan' => $nominal,
                        'total_student' => 1,
                        'student_remain' => 1,
                    ]);
                }else{
                    $plan->total_plan += $nominal;
                    $plan->total_student += 1;
                    $plan->student_remain += 1;
                    $plan->percent = ($plan->student_remain / $plan->total_student) * 100;
                }

                if($spp_student->saldo == 0){
                    $spp_student->remain = $spp_student->remain+$nominal-$deduction;

                    $plan->remain = $plan->remain + $nominal - $deduction;
                }else if($spp_student->saldo < ($nominal-$deduction)){

                    $plan->remain = $plan->remain + ($nominal - $deduction - $spp_student->saldo);
                    $plan->total_get = $plan->total_get + $spp_student->saldo;

                    $spp_add_bill->spp_paid = $spp_student->saldo;

                    $spp_student->remain = $nominal-$deduction-$spp_student->saldo;
                    $spp_student->paid = $spp_student->saldo;
                    $spp_student->saldo = 0;
                }else{
                    $spp_student->saldo = $spp_student->saldo-$nominal+$deduction;
                    $spp_student->paid += $nominal-$deduction;
                    $spp_add_bill->spp_paid = $nominal-$deduction;
                    $spp_add_bill->status = 1;

                    $plan->total_get = $plan->total_get + $nominal - $deduction;
                    $plan->remain = $plan->remain - ($spp_student->saldo-$nominal+$deduction);
                    $plan->student_remain -= 1;
                    $plan->percent = ($plan->student_remain / $plan->total_student) * 100;
                }
                $spp_add_bill->spp_id = $spp_student->id;
                if($sppDeduction)
                    $spp_add_bill->deduction_id = $sppDeduction->id;
                $spp_add_bill->save();
                $spp_student->save();
                $plan->save(); 
            
            }
            }
        }

        if($month=='semua' && $level=='semua'){
            $datas = SppBill::where('year',$year)->where('unit_id',$unit_id)->get();
        }else if($level=='semua'){
            $datas = SppBill::where('year',$year)->where('month',$month)->where('unit_id',$unit_id)->get();
        }else if($month=='semua'){
            $datas = SppBill::where('year',$year)->where('level_id',$level)->where('unit_id',$unit_id)->get();
        }else{
            $datas = SppBill::where('year',$year)->where('month',$month)->where('level_id',$level)->get();
        }

        return redirect()->route('spp.laporan-spp-siswa');
    }

    public function LaporanSiswaAturPotongan(Request $request){
        SppDeductionService::create($request);
        return redirect()->back()->with('sukses', 'Berhasil');
    }

    // public function LaporanSiswaAturPotongan(Request $request)
    // {
    //     $nominal_potongan = str_replace('.','',$request->potongan);
    //     $data = SppBill::find($request->id);
    //     $plan = SppPlan::where('unit_id',$data->unit_id)->where('month',$data->month)->where('year',$data->year)->first();
    //     $plan->total_plan = ($plan->total_plan + $data->deduction) - $nominal_potongan;
    //     $plan->remain = $plan->total_plan - $plan->total_get;
    //     $plan->percent = ($plan->total_get / $plan->total_plan) * 100;
    //     $plan->save();
        

    //     if($data->spp_nominal <= ($nominal_potongan + $data->spp_paid)){
            
    //         $plan->total_get = ($plan->total_get - $data->spp_paid) + ($data->spp_nominal - $nominal_potongan);
    //         $plan->remain = $plan->total_plan - $plan->total_get;
    //         $plan->percent = ($plan->total_get / $plan->total_plan) * 100;
    //         $plan->save();
            
    //         $sisa = ($nominal_potongan + $data->spp_paid) - $data->spp_nominal;
    //         $data->deduction_nominal = $nominal_potongan;
    //         $data->spp_paid = $data->spp_nominal - $nominal_potongan;
    //         $data->status = 1;
    //         $data->save();


    //         if($sisa > 0){

    //             $spp_bills = SppBill::where('student_id',$data->student_id)->where('status',0)->orderBy('month','asc')->orderBy('year','asc')->get();
    //             foreach($spp_bills as $spp){
    //                 if($sisa > 0){
    //                     if($spp->spp_nominal <= ($spp->spp_paid + $spp->deduction_nominal + $sisa)){
    //                         $sisa = ($spp->spp_paid + $spp->deduction_nominal + $sisa) - $spp->spp_nominal;
    //                         $spp->spp_paid = $spp->spp_nominal - $spp->deduction_nominal;
    //                         $spp->status = 1;
    //                         $spp->save();
    //                     }else{
    //                         $spp->spp_paid = $spp->spp_paid + $sisa;
    //                         $spp->save();
    //                     }
    //                 }
    //             }
    //         }

    //     }else {
    //         $data->deduction_nominal = $nominal_potongan;
    //         $data->status = 0;
    //         $data->save();
    //     }
    //     $spp = Spp::where('student_id',$data->student_id)->first();
    //     if($spp->remain < $nominal_potongan){
    //         $spp->deduction = $spp->deduction + $nominal_potongan;
    //         $sisa = $nominal_potongan - $spp->remain;
    //         $spp->remain = 0;
    //         $spp->paid = $spp->paid - $sisa;
    //         $spp->saldo = $spp->saldo + $sisa;
    //         $spp->save();
    //     }else{
    //         $spp->deduction = $spp->deduction + $nominal_potongan;
    //         $spp->remain = $spp->remain - $nominal_potongan;
    //         $spp->save();
    //     }

    //     $spp_deduction_year = SppDeductionYear::where('unit_id',$data->unit_id)->where('year',$data->year)->first();
    //     if($spp_deduction_year){
    //         $spp_deduction_year->total_deduction = $spp_deduction_year->total_deduction + $nominal_potongan;
    //         $spp_deduction_year->save();
    //     }else{
    //         $spp_deduction_year = SppDeductionYear::create([
    //             'unit_id' => $data->unit_id,
    //             'year' => $data->year,
    //             'total_deduction' => $nominal_potongan,
    //         ]);
    //     }

    //     return redirect()->route('spp.laporan-spp-siswa-filter');
    //     dd($request,$data,$spp);
    // }

    public function LaporanMasukan(Request $request)
    {
        //
        $levels = Level::all();
        $level = 'semua';
        if($request->unit_id){
            $unit_id = $request->unit_id;
        }else{
            $unit_id = auth()->user()->pegawai->unit_id;
        }

        if($request->tahun){
            $year = $request->tahun;
        }else{
            $year = date("Y");
        }

        if($unit_id == 5){
            $levels = Level::where('unit_id',1)->get();
            $unit_id = 1;
        }else{
            $levels = Level::where('unit_id',$unit_id)->get();
        }
        
        // $month = $request->bulan;
        if($request->bulan){
            $month = $request->bulan;
            $datas = SppTransaction::where('unit_id',$unit_id)->where('year',$year)->orderBy('created_at','desc')->where('month',$month)->get();
            // dd($datas);
            if($unit_id == 5){
                $units = Unit::all();
            }else{
                $units = Unit::find(auth()->user()->pegawai->unit_id);
            }
        }else{
            $month = null;
            if($unit_id == 5){
                $datas = SppTransaction::orderBy('created_at','desc')->where('year',$year)->get();
                $units = Unit::all();
            }else{
                $datas = SppTransaction::where('unit_id',$unit_id)->orderBy('created_at','desc')->where('year',$year)->get();
                $units = Unit::find(auth()->user()->pegawai->unit_id);
            }
            // dd($datas);
        }
        $year_now = date("Y");
        $years = array();
        $year_increment = 2019;
        while($year_increment <= $year_now){
            $year_obj = new stdClass();
            $year_obj->year=$year_increment;
            array_push($years,$year_obj);
            $year_increment+=1;
        }
        return view('keuangan.pembayaran.spp.laporan.masukan', compact('datas','levels','level','year','month','years','unit_id'));
    }

    public function LaporanMasukanFilter(Request $request)
    {
        //
        $levels = Level::all();
        $level = 'semua';
        $unit_id = $request->unit_id;
        if($unit_id == 5){
            $levels = Level::where('unit_id',1)->get();
            $unit_id = $request->unit_id;
        }else{
            $levels = Level::where('unit_id',$unit_id)->get();
        }

        $year = $request->year;
        $month = $request->month;

        $datas = SppTransaction::where('unit_id',$unit_id)->where('year',$year)->where('month',$month)->get();

        return view('keuangan.pembayaran.spp.laporan.masukan', compact('datas','levels','level','year','month','unit_id'));
    }

    public function laporanMasukanGet(Request $request)
    {
        $year = $request->year;
        $month = $request->month;
        $unit_id = $request->unit_id;
        // $level_id = $request->level_id;

        $datas = SppTransaction::where('unit_id', $unit_id)
        ->where('year', $year)
        // ->when($level_id, function($q, $level_id){
        //     return $q->where('level_id', $level_id);
        // })
        ->when($month, function($q, $month){
            return $q->where('month', $month);
        })
        ->whereIn('exchange_que',array(0,1))
        ->get();

        $resource = new LaporanSppMasukanCollection($datas);

        return response()->json([$resource],200);
    }

    public function sppToBms(Request $request)
    {
        
        $spp_trx = SppTransaction::find($request->id);
        if(!$spp_trx){
            return redirect()->back()->with('error','Pemindahan Gagal');
        }
        $nominal = $spp_trx->nominal;
        $student_id = $spp_trx->student_id;
        
        $spp = Spp::where('student_id',$student_id)->first();
        if($spp->saldo > 0){
            if($spp->saldo >= $nominal){
                $spp_nominal_return = 0;
                $spp->saldo -= $nominal;
            }else{
                $spp_nominal_return = $nominal - $spp->saldo;
                $spp->saldo = 0;
                $spp->paid = $spp->paid - $spp_nominal_return;
            }
        }else{
            $spp_nominal_return = $nominal;
        }
        $spp->save();

        $spp_bills = SppBill::where('student_id',$student_id)->where('spp_paid','>',0)->orderBy('month','desc')->orderBy('year','desc')->get();
        foreach($spp_bills as $bill){
            if($spp_nominal_return > 0){

                $spp_plan = SppPlan::where('unit_id',$bill->unit_id)->where('month',$bill->month)->where('year',$bill->year)->first();
                if($bill->status == 1){
                    $spp_plan->student_remain += 1;
                    $bill->status = 0;
                }

                if($spp_nominal_return > $bill->spp_paid){
                    
                    $spp_nominal_return -= $bill->spp_paid;

                    $spp_plan->total_get -= $bill->spp_paid;
                    
                    $bill->spp_paid = 0;

                }else{

                    $spp_plan->total_get -= $spp_nominal_return;
                    
                    $bill->spp_paid -= $spp_nominal_return;

                    $spp_nominal_return = 0;

                }
                $spp_plan->save();
                $bill->save();
                
            }
        }

        $bms_trx = BmsTransaction::create([
            'unit_id' => $spp_trx->unit_id,
            'student_id' => $spp_trx->student_id,
            'month' => $spp_trx->month,
            'year' => $spp_trx->year,
            'nominal' => $spp_trx->nominal,
            'academic_year_id' => $spp_trx->academic_year_id,
            'trx_id' => $spp_trx->trx_id,
            'date' => $spp_trx->date,
        ]);

        $bms_trx->created_at = $spp_trx->created_at;
        $bms_trx->save();

        $bms = BMS::where('student_id',$bms_trx->student_id)->first();

        if($bms->register_nominal > $bms->register_paid){
            $bms->register_paid = $bms->register_paid + $bms_trx->nominal;
            $bms->save();

            if($bms->register_paid > $bms->register_nominal){
                $bms->bms_paid = $bms->bms_paid + ($bms->register_paid - $bms->register_nominal);
                $bms->register_paid = $bms->register_nominal;
                $bms->bms_remain = $bms->bms_nominal - ($bms->bms_paid + $bms->deduction);
                $bms->save();
            }
        }else{
            $bms->bms_paid = $bms->bms_paid + $bms_trx->nominal;
            $bms->bms_remain = $bms->bms_remain - $bms_trx->nominal;
            $bms->save();
        }

        $bms_termins = BmsTermin::where('bms_id',$bms->id)->where('is_student',1)->where('remain','>','0')->orderBy('academic_year_id')->get();
        $tersedia = array();
        $masuk = array();
        $baru = array();
        $dikurangi = array();
        $keluar = array();
        foreach($bms_termins as $termin){
            $plan = BmsPlan::where('unit_id',$bms_trx->unit_id)->where('academic_year_id',$termin->academic_year_id)->first();

            array_push($tersedia, $nominal);

            if($nominal > 0){
                array_push($masuk, $nominal);
                if($termin->remain > 0 && $nominal >= $termin->remain){
                    
                    $plan->remain = $plan->remain - $termin->remain;
                    $plan->total_get = $plan->total_get + $termin->remain;
                    $plan->student_remain -= 1;
                    $plan->percent = ($plan->student_remain / $plan->total_student)*100;
                    $plan->save();
                    
                    $nominal -= $termin->remain;
                    // array_push($dikurangi, $nominal);
                    array_push($baru, $nominal);
                    $termin->remain = 0;
                    $termin->save();
                    

                }else{

                    $plan->remain = $plan->remain - $nominal;
                    $plan->total_get = $plan->total_get + $nominal;
                    $plan->save();

                    $termin->remain = $termin->remain - $nominal;
                    $termin->save();

                    $nominal = 0;
                }
            }
            array_push($keluar, $nominal);
        }
        $spp_trx->delete();

        return redirect()->back()->with('success','Pindah kategori berhasil');

    }

    public function siswaList($unit = null)
    {
        $list = $unit ? Siswa::where('unit_id',$unit)->whereIn('is_lulus',[0,1])->get() : [];
        $collection = new SiswaListCollection($list);
        return response()->json($collection,200);
    }

    /**
     * Print the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cetakTagihan($id)
    {
        try {
            $spp_id = Crypt::decrypt($id);
        } catch (\Exception $e){
            return redirect()->back()->with('error','Terjadi Kesalahan');
        }
        
        $spp_bill = SppBill::find($spp_id);
        $calon = Siswa::find($spp_bill->student_id);
        $bill_now = $spp_bill->spp_nominal - ($spp_bill->spp_paid + $spp_bill->deduction_nominal);
        $bill_before = $calon->spp->remain - $bill_now;

        // $pdf = PDF::loadView('keuangan.pembayaran.spp.surat',compact('calon', 'spp_bill', 'bill_before'));
        
        // $pdf->setPaper('A4', 'portrait');

        // return $pdf->stream($calon->identitas->student_name.'.pdf');
        
        return view('keuangan.pembayaran.spp.surat',compact('calon', 'spp_bill', 'bill_before'));
    }
}
