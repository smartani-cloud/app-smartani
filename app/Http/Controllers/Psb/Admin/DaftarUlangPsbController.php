<?php

namespace App\Http\Controllers\Psb\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Psb\ListingDaftarUlang;
use App\Http\Services\Psb\RegisterCounterService;
use App\Models\Level;
use App\Models\Pembayaran\BmsCalonSiswa;
use App\Models\Pembayaran\BmsPlan;
use App\Models\Psb\RegisterCounter;
use App\Models\Siswa\CalonSiswa;
use Illuminate\Http\Request;

class DaftarUlangPsbController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $title = "Belum Lunas";
        $route = 'kependidikan.psb.belum-lunas';

        $bayar = 'sebagian';
        if(isset($request->bayar) && $request->bayar != 'sebagian'){
            if(in_array($request->bayar,['belum'])) $bayar = $request->bayar;
        }

        // $unit_id = auth()->user()->pegawai->unit_id;
        // $unit = 'Semua';
        // if($unit_id == 5){
        //     $levels = Level::all();
        //     $lists = BmsCalonSiswa::where('register_remain','>',0)->orderBy('unit_id','asc')->get();
        // }else{
        //     $lists = BmsCalonSiswa::where('unit_id',$unit_id)->where('register_remain','>',0)->orderBy('candidate_student_id','asc')->get();
        //     $levels = Level::where('unit_id',$unit_id);
        // }
        // $level = 'semua';



        $lists = ListingDaftarUlang::list($request->level, $request->year, 0,$bayar);

        return view('psb.admin.bayar', compact('lists','title','route','bayar','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $title = "Sudah Lunas";
        // $unit_id = auth()->user()->pegawai->unit_id;
        // $unit = 'Semua';
        // if($unit_id == 5){
        //     $levels = Level::all();
        //     $lists = BmsCalonSiswa::where('register_remain',0)->orderBy('unit_id','asc')->get();
        // }else{
        //     $lists = BmsCalonSiswa::where('unit_id',$unit_id)->where('register_remain',0)->orderBy('candidate_student_id','asc')->get();
        //     $levels = Level::where('unit_id',$unit_id);
        // }
        // $level = 'semua';

        $lists = ListingDaftarUlang::list($request->level, $request->year, 1);

        return view('psb.admin.bayar', compact('lists','title','request'));
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
        $calons = CalonSiswa::find($request->id);
        if(!$calons)return redirect()->back()->with('error', 'Konfirmasi siswa gagal');
        if($calons->status_id != 4)return redirect()->back()->with('error', 'Konfirmasi siswa gagal');
        $calons->year_spp = $request->year_spp;
        $calons->month_spp = $request->month_spp;
        $calons->status_id = 5;
        $calons->save();
        
        RegisterCounterService::addCounter($calons->id,'reapply');

        return redirect()->back()->with('success', 'Calon siswa berhasil dikonfirmasi');
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
        $bms_candidate = BmsCalonSiswa::where('candidate_student_id',$request->id)->first();
        $plan = BmsPlan::where('academic_year_id',$bms_candidate->siswa->academic_year_id)->first();
        $plan->total_plan = $plan->total_plan - $bms_candidate->register_nominal + str_replace('.','',$request->bms_daftar_ulang) ;
        $plan->remain = $plan->total_plan - $plan->total_get;
        $plan->save();
        $bms_candidate->register_nominal = str_replace('.','',$request->bms_daftar_ulang);
        $bms_candidate->register_remain = str_replace('.','',$request->bms_daftar_ulang) - $bms_candidate->register_paid;
        $bms_candidate->save();
        $bms_candidate->fresh();
        if($bms_candidate->register_remain <= 0){
            RegisterCounterService::addCounter($bms_candidate->candidate_student_id,'reapply');
        }
        return redirect()->back()->with('success','Ubah nominal daftar ulang berhasil');
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
