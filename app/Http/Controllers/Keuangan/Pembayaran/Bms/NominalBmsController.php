<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Bms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Session;

use App\Models\Pembayaran\BmsNominal;
use App\Models\Pembayaran\TipeBms;
use App\Models\Unit;

class NominalBmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Nominal BMS';
        $this->route = 'bms.nominal';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        
        $data = BmsNominal::with('tipe:id,bms_type');        
        // if($request->user()->pegawai->unit->name != 'Manajemen'){
        //     $data = $data->where('unit_id',$request->user()->pegawai->unit_id);
        // }
        $data = $data->get()->sortBy('tipe.id');

        $used = null;
        foreach($data as $d){
            if($data->where('unit_id',$d->unit_id)->where('bms_type_id',$d->bms_type_id)->count() < 2) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }
        $unit = Unit::sekolah();
        // if($request->user()->pegawai->unit->name != 'Manajemen'){
        //     $unit = $unit->where('id',$request->user()->pegawai->unit_id);
        // }
        $unit = $unit->get();
        $type = TipeBms::withCount('bmsNominal')->get();

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_array($role,['am'])) $editable = true;

        return view($this->template.$route.'-index', compact('data','used','active','route','unit','type','editable'));
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
            'type.required' => 'Mohon pilih salah satu jenis BMS',
            'unit.required' => 'Mohon pilih salah satu unit',
            'nominal.required' => 'Mohon masukkan nominal BMS',
        ];

        $this->validate($request, [
            'type' => 'required',
            'unit' => 'required',
            'nominal' => 'required'
        ], $messages);

        $bmsNominal = BmsNominal::where('bms_type_id',$request->type);
        if($request->user()->pegawai->unit->name == 'Manajemen')
            $bmsNominal = $bmsNominal->where('unit_id',$request->unit);
        else
            $bmsNominal = $bmsNominal->where('unit_id',$request->user()->pegawai->unit_id);

        $checkUnit = false;
        $type = TipeBms::find($request->type);
        $unit = Unit::sekolah()->where('id',$request->unit)->first();
        if($unit && $type) $checkUnit = true;

        if($bmsNominal->count() < 1 && $checkUnit){            
            $nominalValue = (int)str_replace('.','',$request->nominal);
            $item = new BmsNominal();
            $item->unit_id = $request->unit;
            $item->bms_type_id = $request->type;
            $item->bms_nominal = $nominalValue;
            $item->save();

            $item->fresh();

            Session::flash('success','Data '.$item->name.' berhasil ditambahkan');
        }
        elseif(!$checkUnit){
            Session::flash('danger','Data gagal ditambah. Mohon pastikan jenis BMS dan unit valid.');
        }
        else{
            $bmsNominal = $bmsNominal->first();
            Session::flash('danger','Data '.$bmsNominal->name.' sudah pernah ditambahkan');
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
        $items = BmsNominal::count();
        $data = $request->id ? ($request->user()->pegawai->unit->name != 'Manajemen' ? BmsNominal::where(['id' => $request->id,'unit_id' => $request->user()->pegawai->unit_id])->first() : BmsNominal::find($request->id)) : null;

        $data = $request->id ? BmsNominal::find($request->id) : null;
        $active = $this->active;
        $route = $this->route;
        $unit = Unit::sekolah()->get();
        $type = TipeBms::withCount('bmsNominal')->get();

        $editable = false;
        //if($type && $unit && ($items > (count($type)*count($unit)))) $editable = false;

        return view($this->template.$route.'-edit', compact('data','active','route','unit','type','editable'));
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
        $messages = [
            'editType.required' => 'Mohon pilih salah satu jenis BMS',
            'editUnit.required' => 'Mohon pilih salah satu unit',
            'editNominal.required' => 'Mohon masukkan nominal BMS',
        ];

        $this->validate($request, [
            'editNominal' => 'required'
        ], $messages);

        $items = BmsNominal::count();
        $unit = Unit::sekolah()->count();
        $type = TipeBms::count();

        $editable = false;
        //if($items > ($type*$unit)) $editable = false;

        if($editable){
            $validator = Validator::make($request->all(), [
                'editType' => 'required',
                'editUnit' => 'required',
            ], $messages);

            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $item = $request->id ? ($request->user()->pegawai->unit->name != 'Manajemen' ? BmsNominal::where(['id' => $request->id,'unit_id' => $request->user()->pegawai->unit_id])->first() : BmsNominal::find($request->id)) : null;

        if($editable){
            $bmsNominal = BmsNominal::where('bms_type_id',$request->editType)->where('id','!=',$request->id);
            if($request->user()->pegawai->unit->name == 'Manajemen')
                $bmsNominal = $bmsNominal->where('unit_id',$request->editUnit);
            else
                $bmsNominal = $bmsNominal->where('unit_id',$request->user()->pegawai->unit_id);
        }

        if($item && (!$editable || ($editable && $bmsNominal->count() < 1))){
            $old = $item->name;
            $nominalValue = (int)str_replace('.','',$request->editNominal);
            if($editable){
                $item->unit_id = $request->editUnit;
                $item->bms_type_id = $request->editType;
            }
            $item->bms_nominal = $nominalValue;
            $item->save();
            
            Session::flash('success','Data '.$old.' berhasil diubah'.($old != $item->name ? ' menjadi '.$item->name : ''));
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
        $item = BmsNominal::where('id', $request->id);
        if($request->user()->pegawai->unit->name != 'Manajemen')
            $item = $item->where('unit_id',$request->user()->pegawai->unit_id);
        $item = $item->first();
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
=======
<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Bms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Session;

use App\Models\Pembayaran\BmsNominal;
use App\Models\Pembayaran\TipeBms;
use App\Models\Unit;

class NominalBmsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Nominal BMS';
        $this->route = 'bms.nominal';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;
        
        $data = BmsNominal::with('tipe:id,bms_type');        
        // if($request->user()->pegawai->unit->name != 'Manajemen'){
        //     $data = $data->where('unit_id',$request->user()->pegawai->unit_id);
        // }
        $data = $data->get()->sortBy('tipe.id');

        $used = null;
        foreach($data as $d){
            if($data->where('unit_id',$d->unit_id)->where('bms_type_id',$d->bms_type_id)->count() < 2) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }
        $unit = Unit::sekolah();
        // if($request->user()->pegawai->unit->name != 'Manajemen'){
        //     $unit = $unit->where('id',$request->user()->pegawai->unit_id);
        // }
        $unit = $unit->get();
        $type = TipeBms::withCount('bmsNominal')->get();

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_array($role,['am'])) $editable = true;

        return view($this->template.$route.'-index', compact('data','used','active','route','unit','type','editable'));
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
            'type.required' => 'Mohon pilih salah satu jenis BMS',
            'unit.required' => 'Mohon pilih salah satu unit',
            'nominal.required' => 'Mohon masukkan nominal BMS',
        ];

        $this->validate($request, [
            'type' => 'required',
            'unit' => 'required',
            'nominal' => 'required'
        ], $messages);

        $bmsNominal = BmsNominal::where('bms_type_id',$request->type);
        if($request->user()->pegawai->unit->name == 'Manajemen')
            $bmsNominal = $bmsNominal->where('unit_id',$request->unit);
        else
            $bmsNominal = $bmsNominal->where('unit_id',$request->user()->pegawai->unit_id);

        $checkUnit = false;
        $type = TipeBms::find($request->type);
        $unit = Unit::sekolah()->where('id',$request->unit)->first();
        if($unit && $type) $checkUnit = true;

        if($bmsNominal->count() < 1 && $checkUnit){            
            $nominalValue = (int)str_replace('.','',$request->nominal);
            $item = new BmsNominal();
            $item->unit_id = $request->unit;
            $item->bms_type_id = $request->type;
            $item->bms_nominal = $nominalValue;
            $item->save();

            $item->fresh();

            Session::flash('success','Data '.$item->name.' berhasil ditambahkan');
        }
        elseif(!$checkUnit){
            Session::flash('danger','Data gagal ditambah. Mohon pastikan jenis BMS dan unit valid.');
        }
        else{
            $bmsNominal = $bmsNominal->first();
            Session::flash('danger','Data '.$bmsNominal->name.' sudah pernah ditambahkan');
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
        $items = BmsNominal::count();
        $data = $request->id ? ($request->user()->pegawai->unit->name != 'Manajemen' ? BmsNominal::where(['id' => $request->id,'unit_id' => $request->user()->pegawai->unit_id])->first() : BmsNominal::find($request->id)) : null;

        $data = $request->id ? BmsNominal::find($request->id) : null;
        $active = $this->active;
        $route = $this->route;
        $unit = Unit::sekolah()->get();
        $type = TipeBms::withCount('bmsNominal')->get();

        $editable = false;
        //if($type && $unit && ($items > (count($type)*count($unit)))) $editable = false;

        return view($this->template.$route.'-edit', compact('data','active','route','unit','type','editable'));
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
        $messages = [
            'editType.required' => 'Mohon pilih salah satu jenis BMS',
            'editUnit.required' => 'Mohon pilih salah satu unit',
            'editNominal.required' => 'Mohon masukkan nominal BMS',
        ];

        $this->validate($request, [
            'editNominal' => 'required'
        ], $messages);

        $items = BmsNominal::count();
        $unit = Unit::sekolah()->count();
        $type = TipeBms::count();

        $editable = false;
        //if($items > ($type*$unit)) $editable = false;

        if($editable){
            $validator = Validator::make($request->all(), [
                'editType' => 'required',
                'editUnit' => 'required',
            ], $messages);

            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $item = $request->id ? ($request->user()->pegawai->unit->name != 'Manajemen' ? BmsNominal::where(['id' => $request->id,'unit_id' => $request->user()->pegawai->unit_id])->first() : BmsNominal::find($request->id)) : null;

        if($editable){
            $bmsNominal = BmsNominal::where('bms_type_id',$request->editType)->where('id','!=',$request->id);
            if($request->user()->pegawai->unit->name == 'Manajemen')
                $bmsNominal = $bmsNominal->where('unit_id',$request->editUnit);
            else
                $bmsNominal = $bmsNominal->where('unit_id',$request->user()->pegawai->unit_id);
        }

        if($item && (!$editable || ($editable && $bmsNominal->count() < 1))){
            $old = $item->name;
            $nominalValue = (int)str_replace('.','',$request->editNominal);
            if($editable){
                $item->unit_id = $request->editUnit;
                $item->bms_type_id = $request->editType;
            }
            $item->bms_nominal = $nominalValue;
            $item->save();
            
            Session::flash('success','Data '.$old.' berhasil diubah'.($old != $item->name ? ' menjadi '.$item->name : ''));
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
        $item = BmsNominal::where('id', $request->id);
        if($request->user()->pegawai->unit->name != 'Manajemen')
            $item = $item->where('unit_id',$request->user()->pegawai->unit_id);
        $item = $item->first();
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
