<?php

namespace App\Http\Controllers\Keuangan\Pembayaran;

use App\Http\Controllers\Controller;
use App\Http\Resources\Keuangan\Bms\LaporanBmsCollection;
use App\Http\Resources\Siswa\CalonSiswaListCollection;
use App\Models\Kbm\TahunAjaran;
use Illuminate\Http\Request;

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
use App\Models\Pembayaran\SppPlan;
use App\Models\Pembayaran\SppTransaction;
use App\Models\Rekrutmen\Pegawai;
use App\Models\Siswa\CalonSiswa;
use App\Models\Siswa\Siswa;
use App\Models\Unit;
use Illuminate\Support\Facades\Crypt;
use stdClass;

class BmsController extends Controller
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
        $unit = 'Semua';
        if($unit_id == 5){
            $levels = Level::all();
            $lists = BMS::orderBy('unit_id','asc')->get();
        }else{
            $lists = BMS::where('unit_id',$unit_id)->orderBy('student_id','asc')->get();
            $levels = Level::where('unit_id',$unit_id);
        }
        $level = 'semua';
        $tahun_aktif = TahunAjaran::where('is_active',1)->first();

        return view('keuangan.pembayaran.bms.index', compact('lists','levels','level','unit_id','unit','tahun_aktif'));
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
        return view('keuangan.pembayaran.bms.rencana', compact('levels','level','unit','unit_id'));
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
        return view('keuangan.pembayaran.bms.log', compact('levels','level'));
    }

    public function LaporanSiswa()
    {
        //
        $levels = Level::all();
        $level = 'semua';
        return view('keuangan.pembayaran.bms.laporan.siswa', compact('levels','level'));
    }

    public function calonList($unit = null)
    {
        $list = $unit ? CalonSiswa::select('id','reg_number','student_name')->whereHas('bms',function($q){
            $q->where('bms_remain','>',0);
        })->where('unit_id',$unit)->get() : [];
        $collection = new CalonSiswaListCollection($list);
        return response()->json($collection,200);
    }

    public function cetakTagihan($id)
    {
        try {
            $real_id = Crypt::decrypt($id);
        } catch (\Exception $e){
            return redirect()->back()->with('error','Terjadi Kesalahan');
        }
    
        $bms = BMS::find($real_id);
        $calon = Siswa::find($bms->student_id);
        if($bms->bms_type_id == 1){
            return view('keuangan.pembayaran.bms.surat',compact('calon', 'bms'));
        }else{
            return view('keuangan.pembayaran.bms.surat-berkala',compact('calon', 'bms'));
        }
    }
}
