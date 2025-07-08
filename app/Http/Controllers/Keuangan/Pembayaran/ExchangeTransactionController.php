<?php

namespace App\Http\Controllers\Keuangan\Pembayaran;

use App\Http\Controllers\Controller;
// use App\Http\Services\Psb\CalonSiswa\SppSiswaService;
use App\Http\Services\Psb\Siswa\BmsReverseService;
use App\Http\Services\Psb\CalonSiswa\BmsCalonReverseService;
use App\Http\Services\Psb\Siswa\BmsSiswaService;
use App\Http\Services\Psb\Siswa\SppReverseService;
use App\Http\Services\Psb\Siswa\SppSiswaService;
use App\Models\Pembayaran\BmsTransaction;
use App\Models\Pembayaran\BmsTransactionCalonSiswa;
use App\Models\Pembayaran\ExchangeTransaction;
use App\Models\Pembayaran\ExchangeTransactionTarget;
use App\Models\Pembayaran\SppTransaction;
use Illuminate\Http\Request;

// Unused
use App\Models\Pembayaran\BMS;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\Spp;

class ExchangeTransactionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Pemindahan Transaksi';
        $this->route = 'exchange';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$status = null)
    {
        //
        $status = 'menunggu';
        if(isset($request->status) && $request->status != 'menunggu'){
            if(in_array($request->status,['sukses','refund','ditolak'])) $status = $request->status;
        }

        $datas = ExchangeTransaction::where(function($q){
            $q->where(function($q){
                $q->has('bmsTransactionOrigin')->where('origin',1);
            })->orWhere(function($q){
                $q->has('sppTransactionOrigin')->where('origin',2);
            })->orWhere(function($q){
                $q->has('bmsCandidateTransactionOrigin')->where('origin',3);
            });
        });
        if($status == 'sukses'){
            $datas = $datas->where('status',1)->where('refund',0);
        }
        elseif($status == 'refund'){
            $datas = $datas->where('status',1)->where('refund','!=',0);
        }
        elseif($status == 'ditolak'){
            $datas = $datas->where('status',2);
        }
        else{
            $datas = $datas->where('status',0);   
        }
        $datas = $datas->get();
        $active = $this->active;
        $route = $this->route;
        $editable = $status == 'menunggu' && in_array($request->user()->pegawai->position_id,[57]) ? true : false;

        return view($this->template.$route,compact('active','route','datas','editable','status'));
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
        $isCandidateOrigin = isset($request->is_student) && $request->is_student == 0 ? true : false;
        $isCandidateTarget = isset($request->category_split) && $request->category_split == 'calon' ? true : false;

        $refundValue = (int)str_replace('.','',$request->refund);
        $nominalSiswaValue = (int)str_replace('.','',$request->nominal_siswa);
        $nominalSplitValue = (int)str_replace('.','',$request->nominal_split);
        
        $exchange = ExchangeTransaction::find($request->id);
        // dd($exchange);
        $exchange->refund = $refundValue;
        $exchange->save();

        $targets = ExchangeTransactionTarget::where('exchange_transaction_id', $exchange->id)->delete();
        // $targets->delete();

        ExchangeTransactionTarget::create([
            'exchange_transaction_id' => $exchange->id,
            'student_id' => $isCandidateOrigin ? $exchange->transactionOrigin->candidate_student_id : $exchange->transactionOrigin->student_id,
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

        return redirect()->back()->with('success', 'Ubah data berhasil');

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
    public function update(Request $request)
    {
        //
        // dd('dont');
        $id = $request->id;

        $exchange = ExchangeTransaction::find($id);
        // dd($exchange->transactionOrigin, $exchange);
        if($exchange->origin == 1){
            $reverse = BmsReverseService::transaction($exchange->transaction_id);
        }elseif($exchange->origin == 2){
            $reverse = SppReverseService::reverse($exchange->transaction_id);
        }else{
            $reverse = BmsCalonReverseService::transaction($exchange->transaction_id);
        }

        foreach($exchange->transactionTarget as $move){
            // BMS
            if($move->transaction_type == 1){
                $bms = $move->student->bms;
                $register_paid = $move->nominal;
                if($bms->register_nominal > $bms->register_paid){
                    $register_paid = $bms->register_paid + $move->nominal;
                    if($register_paid > $bms->register_nominal){
                        $register_paid -= $bms->register_nominal;
                    }
                    else{
                        $register_paid = 0;
                    }
                }

                if(isset($move->is_student) && $move->is_student == 0){
                    $bms_trx = BmsSiswaService::saveToTransaction($move->student->unit_id, $move->student->id, $move->nominal, $exchange->transactionOrigin->trx_id, true);

                    $bms_termin_total = BmsSiswaService::saveToBms($bms_trx, $bms, true);

                    if($register_paid > 0){
                        BmsSiswaService::saveToTermin($move->student->unit_id,$bms,$register_paid, true);
                    }
                }
                else{
                    $bms_trx = BmsSiswaService::saveToTransaction($move->student->unit_id, $move->student->id, $move->nominal, $exchange->transactionOrigin->trx_id);

                    $bms_termin_total = BmsSiswaService::saveToBms($bms_trx, $bms);

                    if($register_paid > 0){
                        BmsSiswaService::saveToTermin($move->student->unit_id,$bms,$register_paid);
                    }
                }
            }else{
                // dd($move);
                $spp_trx = SppSiswaService::saveToTransactions($move->student, $move->nominal, $exchange->transactionOrigin->trx_id);

                $spp = SppSiswaService::saveToSpp($move->student, $move->nominal);
            }
        }

        $exchange->status = 1;
        $exchange->save();

        return redirect()->back()->with('success', 'Perubahan berhasil disetujui');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
        $id = $request->id;
        $exchange = ExchangeTransaction::find($id);
        $exchange->status = 2;
        $exchange->save();

        if($exchange->origin == 1){
            $trx = BmsTransaction::find($exchange->transaction_id);
        }elseif($exchange->origin == 2){
            $trx = SppTransaction::find($exchange->transaction_id);
        }else{
            $trx = BmsTransactionCalonSiswa::find($exchange->transaction_id);
        }
        $trx->exchange_que = 0;
        $trx->save();

        return redirect()->back()->with('success', 'Perubahan berhasil ditolak');
    }
}
