<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Generator;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\BmsTransactionCalonSiswa;
use Illuminate\Http\Request;

class BmsGeneratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function resetBmsCandidate()
    {
        $lists = BmsCalonSiswa::all();
        
        foreach($lists as $list){
            $list->register_paid = 0;
            $list->register_remain = $list->register_nominal;
            $list->bms_paid = 0;
            $list->bms_remain = $list->bms_nominal;
            $list->save();
        }
        return redirect()->back();
    }

    public function generateBmsCandidate()
    {
        ini_set('max_execution_time', 0);
        $trxs = BmsTransactionCalonSiswa::all();

        foreach($trxs as $trx){
            $bms = BmsCalonSiswa::where('candidate_student_id',$trx->candidate_student_id)->first();

            if($bms->register_remain > 0){
                if($bms->register_nominal > $bms->register_paid + $trx->nominal){
                    $bms->register_paid += $trx->nominal;
                    $bms->register_remain -= $trx->nominal;
                }else{
                    $bms->register_paid = $bms->register_nominal;
                    $bms->register_remain = 0;
                }
            }
            $bms->bms_paid += $trx->nominal;
            $bms->bms_remain -= $trx->nominal;
            $bms->save();
        }
        return redirect()->back();
    }

}
=======
<?php

namespace App\Http\Controllers\Generator;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\BmsTransactionCalonSiswa;
use Illuminate\Http\Request;

class BmsGeneratorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function resetBmsCandidate()
    {
        $lists = BmsCalonSiswa::all();
        
        foreach($lists as $list){
            $list->register_paid = 0;
            $list->register_remain = $list->register_nominal;
            $list->bms_paid = 0;
            $list->bms_remain = $list->bms_nominal;
            $list->save();
        }
        return redirect()->back();
    }

    public function generateBmsCandidate()
    {
        ini_set('max_execution_time', 0);
        $trxs = BmsTransactionCalonSiswa::all();

        foreach($trxs as $trx){
            $bms = BmsCalonSiswa::where('candidate_student_id',$trx->candidate_student_id)->first();

            if($bms->register_remain > 0){
                if($bms->register_nominal > $bms->register_paid + $trx->nominal){
                    $bms->register_paid += $trx->nominal;
                    $bms->register_remain -= $trx->nominal;
                }else{
                    $bms->register_paid = $bms->register_nominal;
                    $bms->register_remain = 0;
                }
            }
            $bms->bms_paid += $trx->nominal;
            $bms->bms_remain -= $trx->nominal;
            $bms->save();
        }
        return redirect()->back();
    }

}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
