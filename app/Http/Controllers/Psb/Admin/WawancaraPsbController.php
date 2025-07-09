<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Psb\ListingCandidateStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Level;
use App\Models\Siswa\CalonSiswa;
use App\Models\Pembayaran\BmsDeduction;

class WawancaraPsbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $title = 'Wawancara';
        $status_id = 3;
        $link = 'wawancara';
        $unit = Auth::user()->pegawai->unit_id;
        $calons = ListingCandidateStudent::list($request->level, $request->year, $status_id);

        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('title','calons','status_id','link','request','deductions'));
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
}
=======
<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Psb\ListingCandidateStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Level;
use App\Models\Siswa\CalonSiswa;
use App\Models\Pembayaran\BmsDeduction;

class WawancaraPsbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $title = 'Wawancara';
        $status_id = 3;
        $link = 'wawancara';
        $unit = Auth::user()->pegawai->unit_id;
        $calons = ListingCandidateStudent::list($request->level, $request->year, $status_id);

        $deductions = BmsDeduction::orderBy('name')->get();

        return view('psb.admin.index',compact('title','calons','status_id','link','request','deductions'));
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
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
