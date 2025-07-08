<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Session;

use App\Models\Pembayaran\SppDeduction;
use App\Models\Rekrutmen\StatusPegawai;

class PotonganSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Potongan SPP';
        $this->route = 'spp.potongan';
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

        $data = SppDeduction::orderBy('name')->get();

        $used = null;
        foreach($data as $d){
            $used[$d->id] = 0;
        }

        $active = $this->active;
        $route = $this->route;

        $status = StatusPegawai::pegawaiAktif()->doesntHave('sppDeduction')->get();

        $editable = false;
        if(in_arraY($role,['am'])) $editable = true;

        return view($this->template.$route.'-index', compact('data','used','active','route','status','editable'));
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
            'newOpt.required' => 'Mohon pilih salah satu kategori',
            'status.required_if' => 'Mohon pilih salah satu kategori untuk civitas',
            'name.required_if' => 'Mohon tuliskan nama '.strtolower($this->active),
            'percentageOpt.required' => 'Mohon tentukan apakah potongan menggunakan persentase atau tidak',
            'percentage.required_if' => 'Mohon tuliskan persentase '.strtolower($this->active),
            'percentage.numeric' => 'Mohon tuliskan persentase dengan angka',
            'percentage.between' => 'Mohon tuliskan persentase dengan rentang 1 s.d. 100',
            'nominal.required_if' => 'Mohon tuliskan nominal '.strtolower($this->active),
        ];

        $this->validate($request, [
            'newOpt' => 'required',
            'status' => 'required_if:newOpt,old',
            'name' => 'required_if:newOpt,new',
            'percentageOpt' => 'required',
            'percentage' => 'required_if:percentageOpt,yes|numeric|between:0,100',
            'nominal' => 'required_if:percentageOpt,no',
        ], $messages);

        $status = $nominalValue = null;
        $count = SppDeduction::query();
        if($request->newOpt == 'old'){
            $status = StatusPegawai::pegawaiAktif()->doesntHave('sppDeduction')->where('id',$request->status)->first();
            $count = $count->where('employee_status_id',$request->status);
        }
        else
            $count = $count->where('name',$request->name);

        if($request->percentageOpt == 'yes')
            $count = $count->where('percentage',$request->percentage);
        else{
            $nominalValue = (int)str_replace('.','',$request->nominal);
            $count = $count->whereNull('percentage')->where('nominal',$nominalValue);
        }

        $count = $count->count();

        if($count < 1 && (($request->newOpt == 'old' && $status) || $request->newOpt == 'new') && in_array($request->percentageOpt,['yes','no'])){
            $item = new SppDeduction();
            if($request->newOpt == 'old'){
                $item->employee_status_id = $status->id;
                $item->name = 'Anak '.$status->status;
            }
            else
                $item->name = $request->name;
            if($request->percentageOpt == 'yes')
                $item->percentage = $request->percentage;
            else
                $item->nominal = $nominalValue;
            $item->save();

            $item->fresh();

            Session::flash('success','Data '.$item->name.' berhasil ditambahkan');
        }
        else{
            $name = $status ? $status->status : ($request->newOpt == 'new' ? $request->name : null);
            if($name)
                Session::flash('danger','Data '.$name.' sudah pernah ditambahkan');
            else
                Session::flash('danger','Data tidak dapat ditambahkan');
        }

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
        $data = $request->id ? SppDeduction::find($request->id) : null;
        $active = $this->active;
        $route = $this->route;

        $status = StatusPegawai::pegawaiAktif()->where(function($q){
            $q->doesntHave('sppDeduction');
        });
        if($data->statusPegawai){
            $status = $status->orWhere('id',$data->employee_status_id);
        }
        $status = $status->get();

        $editable = true;
        if($status && count($status) < 2) $editable = false;

        return view($this->template.$route.'-edit', compact('data','active','route','status','editable'));
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
        $item = SppDeduction::find($request->id);

        if($item){
            $messages = [
                'editStatus.required' => 'Mohon pilih kategori '.strtolower($this->active),
                'editName.required' => 'Mohon tuliskan nama '.strtolower($this->active),
                'editPercentage.required' => 'Mohon tuliskan persentase '.strtolower($this->active),
                'editPercentage.numeric' => 'Mohon tuliskan persentase dengan angka',
                'editPercentage.between' => 'Mohon tuliskan persentase dengan rentang 1 s.d. 100',
                'editNominal.required' => 'Mohon tuliskan nominal '.strtolower($this->active),
            ];

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

            $editable = true;
            if($item->category == 'civitas'){
                $statusCount = StatusPegawai::pegawaiAktif()->doesntHave('sppDeduction')->count();
                if($statusCount < 1) $editable = false;

                if($editable){
                    $validator = Validator::make($request->all(), [
                        'editStatus' => 'required',
                    ], $messages);
                }
            }
            else{
                $validator = Validator::make($request->all(), [
                    'editName' => 'required',
                ], $messages);
            }

            if(isset($validator) && $validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
            
            $status = null;
            $count = SppDeduction::where('id','!=',$request->id);
            if($item->category == 'civitas' && $editable){
                $status = StatusPegawai::pegawaiAktif()->where('id',$request->editStatus);
                if($item->employee_status_id != $request->editStatus){
                    $status = $status->doesntHave('sppDeduction');
                }
                $status = $status->first();
                $count = $count->where('employee_status_id',$request->editStatus);
            }
            else
                $count = $count->where('name',$request->editName);
            $count = $count->count();

            if(($item->category == 'civitas' && (!$editable || ($editable && $status && $count < 1))) || ($item->category == 'umum' && $count < 1)){
                $old = $item->name;
                if($item->category == 'civitas' && $editable){
                    $item->employee_status_id = $status->id;
                    $item->name = 'Anak '.$status->status;
                }
                else
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
        $item = SppDeduction::find($request->id);
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
