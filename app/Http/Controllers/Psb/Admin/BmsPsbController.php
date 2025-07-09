<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Psb\CalonSiswa\Bms\EditBmsService;
use App\Http\Services\Psb\CalonSiswa\Bms\GenerateTransactionBmsService;
use App\Http\Services\Psb\CalonSiswa\Bms\ResetBmsService;
use App\Http\Services\Psb\CalonSiswa\BmsCalonService;
use App\Http\Services\Psb\CalonSiswa\BmsEditService;
use App\Http\Services\Psb\CalonSiswa\BmsResetService;
use Illuminate\Http\Request;

class BmsPsbController extends Controller
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
    public function update(Request $request)
    {

        ResetBmsService::resetTerminPlan($request);
        $new_bms = EditBmsService::editBmsCandidate($request);
        GenerateTransactionBmsService::generateFromBmsTransaction($new_bms->candidate_student_id);

        return redirect()->back()->with('success','Ubah bms calon siswa berhasil');

        //
        // dd($request);
        // $total_paid = BmsResetService::reset($request);
        // $bms_recreate = BmsEditService::edit($request);
        // // dd($bms_recreate, $total_paid);
        // $sisa_update_bms = BmsCalonService::saveToBmsCalon($total_paid,$bms_recreate);
        // $update_paid = BmsCalonService::saveToTerminCalon($bms_recreate->unit_id, $bms_recreate, $sisa_update_bms);
        // // dd($update_paid);
        // return redirect()->back()->with('success','Ubah bms calon siswa berhasil');
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
}
=======
<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Psb\CalonSiswa\Bms\EditBmsService;
use App\Http\Services\Psb\CalonSiswa\Bms\GenerateTransactionBmsService;
use App\Http\Services\Psb\CalonSiswa\Bms\ResetBmsService;
use App\Http\Services\Psb\CalonSiswa\BmsCalonService;
use App\Http\Services\Psb\CalonSiswa\BmsEditService;
use App\Http\Services\Psb\CalonSiswa\BmsResetService;
use Illuminate\Http\Request;

class BmsPsbController extends Controller
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
    public function update(Request $request)
    {

        ResetBmsService::resetTerminPlan($request);
        $new_bms = EditBmsService::editBmsCandidate($request);
        GenerateTransactionBmsService::generateFromBmsTransaction($new_bms->candidate_student_id);

        return redirect()->back()->with('success','Ubah bms calon siswa berhasil');

        //
        // dd($request);
        // $total_paid = BmsResetService::reset($request);
        // $bms_recreate = BmsEditService::edit($request);
        // // dd($bms_recreate, $total_paid);
        // $sisa_update_bms = BmsCalonService::saveToBmsCalon($total_paid,$bms_recreate);
        // $update_paid = BmsCalonService::saveToTerminCalon($bms_recreate->unit_id, $bms_recreate, $sisa_update_bms);
        // // dd($update_paid);
        // return redirect()->back()->with('success','Ubah bms calon siswa berhasil');
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
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
