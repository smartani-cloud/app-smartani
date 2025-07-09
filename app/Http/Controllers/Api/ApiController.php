<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\Api\Payment\PaymentService;
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

class ApiController extends Controller
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
            return response()->json([
                'message' => 'invalid parameter',
                'status' => 'error'
            ],400);
        }

        [$va_type, $student_id] = PaymentService::checkVa($request->virtualAccount);

        if(!($va_type && $student_id)){
            return response()->json([
                'message' => 'data not found',
                'status' => 'error'
            ],404);
        }
        
        $payment = PaymentService::setPayment($va_type, $student_id, $request->amount, $request->id);

        if(!$payment){
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => 'error'
            ],500);
        }

        return response()->json([
            'message' => 'Success',
        ],200);
        // return $response;
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
=======
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\Api\Payment\PaymentService;
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

class ApiController extends Controller
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
            return response()->json([
                'message' => 'invalid parameter',
                'status' => 'error'
            ],400);
        }

        [$va_type, $student_id] = PaymentService::checkVa($request->virtualAccount);

        if(!($va_type && $student_id)){
            return response()->json([
                'message' => 'data not found',
                'status' => 'error'
            ],404);
        }
        
        $payment = PaymentService::setPayment($va_type, $student_id, $request->amount, $request->id);

        if(!$payment){
            return response()->json([
                'message' => 'Internal Server Error',
                'status' => 'error'
            ],500);
        }

        return response()->json([
            'message' => 'Success',
        ],200);
        // return $response;
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
}