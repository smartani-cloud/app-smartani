<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pembayaran\Spp;
use App\Models\Unit;

class DasborSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Dashboard SPP';
        $this->route = 'spp.dasbor';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->pegawai->unit_id == 5){
            $units = Unit::select('id','name')->sekolah()->get();
        }else{
            $units = Unit::select('id','name')->where('id',$request->user()->pegawai->unit_id)->get();
        }

        $lists = null;

        foreach($units as $u){
            $sppBill = $u->sppBill()->where([
                'month' => date('m'),
                'year' => date('Y'),
            ]);
            $sppCount = $sppBill->count();
            
            $lastSppBill = $u->sppBill()->where(function($q){
                $q->where('year','<',date('Y', strtotime("first day of previous month")))->orWhere(function($q){
                    $q->where('year',date('Y', strtotime("first day of previous month")))->where('month','<=',date('m', strtotime("first day of previous month")));
                });
            });

            $spps = Spp::select('id','saldo')->whereHas('bills',function($q)use($u){
                $q->where([
                    'unit_id' => $u->id,
                    'month' => date('m', strtotime("first day of previous month")),
                    'year' => date('Y', strtotime("first day of previous month")),
                ]);
            });

            if($sppCount > 0){
                $summary = collect([
                    [
                        'name' => $u->name,
                        'last' => $lastSppBill->sum('spp_nominal')-$lastSppBill->sum('deduction_nominal')-$lastSppBill->sum('spp_paid'),
                        'deposit' => $spps->sum('saldo'),
                        'nominal' => $sppBill->sum('spp_nominal'),
                        'deduction' => $sppBill->sum('deduction_nominal'),
                        'bill' => $sppBill->sum('spp_nominal')-$sppBill->sum('deduction_nominal'),
                        'paid' => $sppBill->sum('spp_paid'),
                        'percentage' => number_format((float)(($sppBill->sum('spp_paid')/($sppBill->sum('spp_nominal')-$sppBill->sum('deduction_nominal'))*100)), 0, ',', '')
                    ]
                ]);
            }
            else{
                $summary = collect([
                    [
                        'name' => $u->name,
                        'last' => $lastSppBill->sum('spp_nominal')-$lastSppBill->sum('deduction_nominal')-$lastSppBill->sum('spp_paid'),
                        'deposit' => $spps->sum('saldo'),
                        'nominal' => 0,
                        'deduction' => 0,
                        'bill' => 0,
                        'paid' => 0,
                        'percentage' => 0
                    ]
                ]);
            }

            if($lists){
                $lists = $lists->concat($summary);
            }
            else{
                $lists = $summary;
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'-index', compact('active','route','lists'));
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
