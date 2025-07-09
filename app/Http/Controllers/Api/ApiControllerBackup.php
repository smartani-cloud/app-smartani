<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\BsiService;
use App\Models\Kbm\TahunAjaran;
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\BmsPlan;
use App\Models\Pembayaran\BmsTermin;
use App\Models\Pembayaran\BmsTransaction;
use App\Models\Pembayaran\BmsTransactionCalonSiswa;
use App\Models\Pembayaran\Spp;
use App\Models\Pembayaran\SppBill;
use App\Models\Pembayaran\SppPlan;
use App\Models\Pembayaran\SppTransaction;
use App\Models\Pembayaran\VirtualAccountCalonSiswa;
use App\Models\Pembayaran\VirtualAccountSiswa;
use App\Models\Psb\RegisterCounter;
use App\Models\Setting;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\OrangTua;
use App\Models\Siswa\Siswa;
use App\Models\TestCallback;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ApiControllerBackup extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = TestCallback::all();

        return response()->json([$data],200);
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
        $data = $request->json()->all();
        $validator = Validator::make($data, [
            'code' => 'required',
            'message' => 'required',
            'type' => 'required',
            'id' => 'required',
            'number' => 'required',
            'amount' => 'required',
            'remainingAmount' => 'required',
            'virtualAccount' => 'required',
            // 'va' => 'required',
            'date' => 'required',
            'bankCode' => 'required',
            'bankName' => 'required',
            'ref' => 'required',
            'channel' => 'required',
            // 'email' => 'required',
            // 'transactionId' => 'required',
        ]);
        
        if ($validator->fails()) {
            //TODO Handle your data
            $response = response()->json([
                'message' => 'invalid parameter',
                'status' => 'error'
            ],400);
        } else {

            $is_bms = VirtualAccountSiswa::where('bms_va',$request->virtualAccount)->first();

            if($is_bms){

                $active_academic_year = TahunAjaran::where('is_active',1)->first();

                $bms_trx = BmsTransaction::create([
                    'unit_id' => $is_bms->unit_id,
                    'student_id' => $is_bms->student_id,
                    'month' => date('m'),
                    'year' => date('Y'),
                    'nominal' => $request->amount,
                    'academic_year_id' => $active_academic_year->id,
                    'trx_id' => $request->id,
                    'date' => date('d'),
                ]);

                $bms = BMS::where('student_id',$is_bms->student_id)->first();

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

                $nominal = $request->amount;
                $bms_termins = BmsTermin::where('bms_id',$bms->id)->where('is_student',1)->where('remain','>','0')->orderBy('academic_year_id')->get();
                $tersedia = array();
                $masuk = array();
                $baru = array();
                $dikurangi = array();
                $keluar = array();
                foreach($bms_termins as $termin){
                    $plan = BmsPlan::where('unit_id',$is_bms->unit_id)->where('academic_year_id',$termin->academic_year_id)->first();

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
                $response = response()->json([
                    'message' => 'success', 
                    'status' => 'ok',
                ],200);
            }else{
                $is_bms_candidate = VirtualAccountCalonSiswa::where('bms_va',$request->virtualAccount)->first();
                // dd($request->virtualAccount,$is_bms_candidate);
                if($is_bms_candidate){
    
                    $active_academic_year = TahunAjaran::where('is_active',1)->first();
    
                    $bms_trx = BmsTransactionCalonSiswa::create([
                        'unit_id' => $is_bms_candidate->unit_id,
                        'candidate_student_id' => $is_bms_candidate->candidate_student_id,
                        'month' => date('m'),
                        'year' => date('Y'),
                        'nominal' => $request->amount,
                        'academic_year_id' => $active_academic_year->id,
                        'trx_id' => $request->id,
                        'date' => date('d'),
                    ]);
    
                    $bms = BmsCalonSiswa::where('candidate_student_id',$is_bms_candidate->candidate_student_id)->first();
    
                    if($bms->register_nominal > $bms->register_paid){

                        $calons = CalonSiswa::find($is_bms_candidate->candidate_student_id);

                        // 
                        $plan = BmsPlan::where('unit_id',$is_bms_candidate->unit_id)->where('academic_year_id',$calons->academic_year_id)->first();
                        $sisa_register = $bms->register_nominal - $bms->register_paid;
                        if($sisa_register > $bms_trx->nominal){
                            $plan->total_get += $bms_trx->nominal;
                            $plan->remain -= $bms_trx->nominal;
                        }else{
                            $plan->total_get += $sisa_register;
                            $plan->remain -= $sisa_register;
                        }
                        $plan->save();

                        $bms->register_paid = $bms->register_paid + $bms_trx->nominal;
                        $bms->register_remain = $bms->register_remain - $bms_trx->nominal;
                        $bms->bms_paid = $bms->bms_paid + $bms_trx->nominal;
                        $bms->bms_remain = $bms->bms_remain - $bms_trx->nominal;
                        $bms->save();

                        $for_termin = 0;
    
                        if($bms->register_paid > $bms->register_nominal){

                            $for_termin = $bms->register_paid - $bms->register_nominal;

                            $bms->register_paid = $bms->register_nominal;
                            $bms->register_remain = 0;
                            $bms->save();
                        }

                        if($bms->register_remain == 0){

                            $counter = RegisterCounter::where('unit_id',$calons->unit_id)->where('academic_year_id',$calons->academic_year_id)->first();

                            if($calons->origin_school == 'SIT Auliya'){
                                $counter->reapply_intern = $counter->reapply_intern + 1;
                            }else{
                                $counter->reapply_extern = $counter->reapply_extern + 1;
                            }
                            $counter->save();
                        }
                    }else{
                        $bms->bms_paid = $bms->bms_paid + $bms_trx->nominal;
                        $bms->bms_remain = $bms->bms_remain - $bms_trx->nominal;
                        $bms->save();
                        
                        $for_termin = $request->amount;
                    }

                    $nominal = $for_termin;
                    $bms_termins = BmsTermin::where('bms_id',$bms->id)->where('is_student',0)->where('remain','>','0')->orderBy('academic_year_id')->get();
                    $tersedia = array();
                    $masuk = array();
                    $keluar = array();
                    foreach($bms_termins as $termin){
                        $plan = BmsPlan::where('unit_id',$is_bms_candidate->unit_id)->where('academic_year_id',$termin->academic_year_id)->first();
                        array_push($tersedia, $nominal);
                        if($nominal > 0){
                            array_push($masuk, $nominal);
                            
                            if($termin->remain > 0 && $nominal >= $termin->remain){
        
                                $plan->remain = $plan->remain - $termin->remain;
                                $plan->total_get = $plan->total_get + $termin->remain;
                                $plan->student_remain -= 1;
                                $plan->percent = ($plan->student_remain / $plan->total_student)*100;
                                $plan->save();
        
                                $nominal = $nominal - $termin->remain;
                                
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
    
                    $response = response()->json([
                        'message' => 'success', 
                        'status' => 'ok',
                    ],200);
                }else{

                    $is_spp = VirtualAccountSiswa::where('spp_va',$request->virtualAccount)->first();

                    if($is_spp){

                        $nominal = $request->amount;
                        $active_academic_year = TahunAjaran::where('is_active',1)->first();

                        $spp_trx = SppTransaction::create([
                            'unit_id' => $is_spp->unit_id,
                            'student_id' => $is_spp->student_id,
                            'month' => date('m'),
                            'year' => date('Y'),
                            'nominal' => $request->amount,
                            'academic_year_id' => $active_academic_year->id,
                            'trx_id' => $request->id,
                            'date' => date('d'),
                        ]);

                        $spp = Spp::where('student_id',$is_spp->student_id)->first();

                        if($spp->remain == 0){

                            $spp->saldo = $spp->saldo + $nominal;
                            $spp->save();

                        }else{

                            $spp_bills = SppBill::where('student_id',$is_spp->student_id)->where('status',0)->orderBy('created_at','asc')->get();

                            $transfered = $request->amount;

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

                        $response = response()->json([
                            'message' => 'success', 
                            'status' => 'ok',
                        ],200);

                    }else{
                        $response = response()->json([
                            'message' => 'invalid parameter',
                            'status' => 'error'
                        ],400);
                    }

                }
            }

        
        }
        return $response;
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
    public function auth()
    {
        $token = BsiService::getToken("1");
        return response()->json(['data' => $token]);
    }

    public function registerInvoice()
    {
        $token = BsiService::registerInvoice(
            "Ihsan Fawzi",
            "ihsanfawzi@incomso.com",
            "Tangerang",
            "88081234568",
            "Test",
            "1"
        );
        return response()->json(['data' => $token]);
    }

    public function inquiry()
    {
        $data = BsiService::InquiryVa(5,1);
        return response()->json(["data" => $data]);
    }

    public function encryptOrtu()
    {
        $parents = OrangTua::all();
        foreach($parents as $parent){
            if(strlen($parent->father_phone) <= 16){
                $parent->father_phone = Crypt::encryptString($parent->father_phone);
                $parent->mother_phone = Crypt::encryptString($parent->mother_phone);
                $parent->father_nik = Crypt::encryptString($parent->father_nik);
                $parent->mother_nik = Crypt::encryptString($parent->mother_nik);
                $parent->save();

            }
        }

        return response()->json(["message" => "encrypted"]);
    }
}