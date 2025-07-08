<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Bms;

use App\Http\Controllers\Controller;
use App\Http\Resources\Keuangan\Bms\BmsVaCollection;
use Illuminate\Http\Request;

use App\Models\Level;
use App\Models\Pembayaran\VirtualAccountSiswa;

class VaBmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Virtual Account BMS';
        $this->route = 'bms.va';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $levels = Level::all();
        $level = 'semua';

        $unit_id = auth()->user()->pegawai->unit_id;
        $unit = 'Semua';
        if($unit_id == 5){
            $levels = Level::all();
            $lists = VirtualAccountSiswa::orderBy('created_at','desc')->get();
        }else{
            $levels = Level::where('unit_id',$unit_id);
            $lists = VirtualAccountSiswa::where('unit_id',$unit_id)->orderBy('created_at','desc')->get();
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','lists','levels','level'));
    }

    /**
     * Get a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function vaGet(Request $request)
    {
        $unit_id = $request->unit_id;
        $level_id = $request->level_id;

        $datas = VirtualAccountSiswa::when($level_id, function($q, $level_id){
                return $q->whereHas('siswa', function($q) use ($level_id){
                    $q->where('level_id',$level_id);
                });
            })
            ->whereHas('siswa', function($q){
                $q->where('is_lulus',0);
            });
        if($request->user()->pegawai->unit_id == 5){
            $datas = $datas->where('unit_id',$unit_id);
        }else{
            $datas = $datas->where('unit_id',$request->user()->pegawai->unit_id);
        }
        $datas = $datas->get();

        $data = new BmsVaCollection($datas);

        return response()->json([$data]);
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
