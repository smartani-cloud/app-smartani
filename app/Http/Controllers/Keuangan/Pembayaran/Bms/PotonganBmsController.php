<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Bms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Session;

use App\Models\Pembayaran\BmsDeduction;

class PotonganBmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Potongan BMS';
        $this->route = 'bms.potongan';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $data = BmsDeduction::orderBy('name')->get();

        $used = null;
        foreach($data as $d){
            $used[$d->id] = 0;
        }

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_arraY($role,['am'])) $editable = true;

        return view($this->template.$route.'-index', compact('data','used','active','route','editable'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Mohon tuliskan nama '.strtolower($this->active),
            'percentageOpt.required' => 'Mohon tentukan apakah potongan menggunakan persentase atau tidak',
            'percentage.required_if' => 'Mohon tuliskan persentase '.strtolower($this->active),
            'percentage.numeric' => 'Mohon tuliskan persentase dengan angka',
            'percentage.between' => 'Mohon tuliskan persentase dengan rentang 1 s.d. 100',
            'nominal.required_if' => 'Mohon tuliskan nominal '.strtolower($this->active),
        ];

        $this->validate($request, [
            'name' => 'required',
            'percentageOpt' => 'required',
            'percentage' => 'required_if:percentageOpt,yes|numeric|between:0,100',
            'nominal' => 'required_if:percentageOpt,no',
        ], $messages);

        $nominalValue = null;
        $count = BmsDeduction::where('name', $request->name);

        if($request->percentageOpt == 'yes')
            $count = $count->where('percentage',$request->percentage);
        else{
            $nominalValue = (int)str_replace('.','',$request->nominal);
            $count = $count->whereNull('percentage')->where('nominal',$nominalValue);
        }

        $count = $count->count();

        if($count < 1 && in_array($request->percentageOpt,['yes','no'])){
            $item = new BmsDeduction();
            $item->name = $request->name;
            if($request->percentageOpt == 'yes')
                $item->percentage = $request->percentage;
            else
                $item->nominal = $nominalValue;
            $item->save();

            Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
        }

        else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');

        return redirect()->route($this->route.'.index');
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
    public function edit(Request $request)
    {
        $data = $request->id ? BmsDeduction::find($request->id) : null;
        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'-edit', compact('data','active','route'));
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
        $item = BmsDeduction::find($request->id);

        if($item){
            $messages = [
                'editName.required' => 'Mohon tuliskan nama '.strtolower($this->active),
                'editPercentage.required' => 'Mohon tuliskan persentase '.strtolower($this->active),
                'editPercentage.numeric' => 'Mohon tuliskan persentase dengan angka',
                'editPercentage.between' => 'Mohon tuliskan persentase dengan rentang 1 s.d. 100',
                'editNominal.required' => 'Mohon tuliskan nominal '.strtolower($this->active),
            ];

            $this->validate($request, [
                'editName' => 'required'
            ], $messages);

            if($item->isPercentage){
                $validator = Validator::make($request->all(), [
                    'editPercentage' => 'required|numeric|between:0,100',
                ], $messages);
            }
            else{
                $validator = Validator::make($request->all(), [
                    'editNominal' => 'required',
                ], $messages);
            }

            if(isset($validator) && $validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $count = BmsDeduction::where('name', $request->editName)->where('id','!=',$request->id);
            if($item->isPercentage){
                $count = $count->where('percentage', $request->editPercentage);
            }
            else{
                $nominalValue = (int)str_replace('.','',$request->editNominal);
                $count = $count->whereNull('percentage')->where('nominal',$nominalValue);
            }

            $count = $count->count();

            if($item && $count < 1){
                $old = $item->name;
                $item->name = $request->editName;
                if($item->isPercentage){
                    $item->percentage = $request->editPercentage;
                    $item->nominal = null;
                }
                else{
                    $nominalValue = (int)str_replace('.','',$request->editNominal);
                    $item->percentage = null;
                    $item->nominal = $nominalValue;
                }
                $item->save();

                $item->fresh();
                
                Session::flash('success','Data '.$old.' berhasil diubah'.($old != $item->name ? ' menjadi '.$item->name : ''));
            }
            else Session::flash('danger','Perubahan data gagal disimpan');
        }
        else Session::flash('danger','Perubahan data gagal disimpan');

        return redirect()->route($this->route.'.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $item = BmsDeduction::find($request->id);
        $used_count = 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
