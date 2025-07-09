<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Session;

use App\Models\Pembayaran\SppNominal;
use App\Models\Unit;

class NominalSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Nominal SPP';
        $this->route = 'spp.nominal';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $data = SppNominal::with('unit:id,name');        
        // if($request->user()->pegawai->unit->name != 'Manajemen'){
        //     $data = $data->where('unit_id',$request->user()->pegawai->unit_id);
        // }
        $data = $data->get()->sortBy('name.id');

        $used = null;
        foreach($data as $d){
            if($data->where('unit_id',$d->unit_id)->count() < 2) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }
        $unit = Unit::sekolah()->doesntHave('sppNominal');
        // if($request->user()->pegawai->unit->name != 'Manajemen'){
        //     $unit = $unit->where('id',$request->user()->pegawai->unit_id);
        // }
        $unit = $unit->get();

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_arraY($role,['am'])) $editable = true;

        return view($this->template.$route.'-index', compact('data','used','active','route','unit','editable'));
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
            'unit.required' => 'Mohon pilih salah satu unit',
            'nominal.required' => 'Mohon masukkan nominal SPP',
        ];

        $this->validate($request, [
            'unit' => 'required',
            'nominal' => 'required'
        ], $messages);

        if($request->user()->pegawai->unit->name == 'Manajemen')
            $SppNominal = SppNominal::where('unit_id',$request->unit);
        else
            $SppNominal = SppNominal::where('unit_id',$request->user()->pegawai->unit_id);

        $unit = Unit::sekolah()->doesntHave('sppNominal')->where('id',$request->unit)->first();
        $checkUnit = $unit ? true : false;

        if($SppNominal->count() < 1 && $checkUnit){
            $nominalValue = (int)str_replace('.','',$request->nominal);
            $item = new SppNominal();
            $item->unit_id = $request->unit;
            $item->spp_nominal = $nominalValue;
            $item->save();

            $item->fresh();

            Session::flash('success','Data '.$item->name.' berhasil ditambahkan');
        }
        elseif(!$checkUnit){
            Session::flash('danger','Data gagal ditambah. Mohon pastikan unit valid.');
        }
        else{
            $SppNominal = $SppNominal->first();
            Session::flash('danger','Data '.$SppNominal->name.' sudah pernah ditambahkan');
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
        $data = $request->id ? ($request->user()->pegawai->unit->name != 'Manajemen' ? SppNominal::where(['id' => $request->id,'unit_id' => $request->user()->pegawai->unit_id])->first() : SppNominal::find($request->id)) : null;
        
        $active = $this->active;
        $route = $this->route;
        $unit = Unit::sekolah()->where(function($q){
            $q->doesntHave('sppNominal');
        })->orWhere('id',$data->unit_id)->get();

        $editable = true;
        if($unit && count($unit) < 2) $editable = false;

        return view($this->template.$route.'-edit', compact('data','active','route','unit','editable'));
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
            'editUnit.required' => 'Mohon pilih salah satu unit',
            'editNominal.required' => 'Mohon masukkan nominal SPP',
        ];

        $this->validate($request, [
            'editNominal' => 'required'
        ], $messages);

        $unit = Unit::sekolah()->doesntHave('sppNominal')->count();

        $editable = true;
        if($unit < 1) $editable = false;

        if($editable){
            $validator = Validator::make($request->all(), [
                'editUnit' => 'required',
            ], $messages);

            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $item = $request->id ? ($request->user()->pegawai->unit->name != 'Manajemen' ? SppNominal::where(['id' => $request->id,'unit_id' => $request->user()->pegawai->unit_id])->first() : SppNominal::find($request->id)) : null;

        if($editable){
            if($request->user()->pegawai->unit->name == 'Manajemen')
                $SppNominal = SppNominal::where('unit_id',$request->editUnit);
            else
                $SppNominal = SppNominal::where('unit_id',$request->user()->pegawai->unit_id);
        }

        if($item && (!$editable || ($editable && $SppNominal->count() < 1))){
            $old = $item->name;
            $nominalValue = (int)str_replace('.','',$request->editNominal);
            if($editable){
                $item->unit_id = $request->editUnit;
            }
            $item->spp_nominal = $nominalValue;
            $item->save();

            $item->fresh();
            
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
        $item = SppNominal::where('id', $request->id);
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

namespace App\Http\Controllers\Keuangan\Pembayaran\Spp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Session;

use App\Models\Pembayaran\SppNominal;
use App\Models\Unit;

class NominalSppController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'keuangan.pembayaran.';
        $this->active = 'Nominal SPP';
        $this->route = 'spp.nominal';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $data = SppNominal::with('unit:id,name');        
        // if($request->user()->pegawai->unit->name != 'Manajemen'){
        //     $data = $data->where('unit_id',$request->user()->pegawai->unit_id);
        // }
        $data = $data->get()->sortBy('name.id');

        $used = null;
        foreach($data as $d){
            if($data->where('unit_id',$d->unit_id)->count() < 2) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }
        $unit = Unit::sekolah()->doesntHave('sppNominal');
        // if($request->user()->pegawai->unit->name != 'Manajemen'){
        //     $unit = $unit->where('id',$request->user()->pegawai->unit_id);
        // }
        $unit = $unit->get();

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_arraY($role,['am'])) $editable = true;

        return view($this->template.$route.'-index', compact('data','used','active','route','unit','editable'));
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
            'unit.required' => 'Mohon pilih salah satu unit',
            'nominal.required' => 'Mohon masukkan nominal SPP',
        ];

        $this->validate($request, [
            'unit' => 'required',
            'nominal' => 'required'
        ], $messages);

        if($request->user()->pegawai->unit->name == 'Manajemen')
            $SppNominal = SppNominal::where('unit_id',$request->unit);
        else
            $SppNominal = SppNominal::where('unit_id',$request->user()->pegawai->unit_id);

        $unit = Unit::sekolah()->doesntHave('sppNominal')->where('id',$request->unit)->first();
        $checkUnit = $unit ? true : false;

        if($SppNominal->count() < 1 && $checkUnit){
            $nominalValue = (int)str_replace('.','',$request->nominal);
            $item = new SppNominal();
            $item->unit_id = $request->unit;
            $item->spp_nominal = $nominalValue;
            $item->save();

            $item->fresh();

            Session::flash('success','Data '.$item->name.' berhasil ditambahkan');
        }
        elseif(!$checkUnit){
            Session::flash('danger','Data gagal ditambah. Mohon pastikan unit valid.');
        }
        else{
            $SppNominal = $SppNominal->first();
            Session::flash('danger','Data '.$SppNominal->name.' sudah pernah ditambahkan');
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
        $data = $request->id ? ($request->user()->pegawai->unit->name != 'Manajemen' ? SppNominal::where(['id' => $request->id,'unit_id' => $request->user()->pegawai->unit_id])->first() : SppNominal::find($request->id)) : null;
        
        $active = $this->active;
        $route = $this->route;
        $unit = Unit::sekolah()->where(function($q){
            $q->doesntHave('sppNominal');
        })->orWhere('id',$data->unit_id)->get();

        $editable = true;
        if($unit && count($unit) < 2) $editable = false;

        return view($this->template.$route.'-edit', compact('data','active','route','unit','editable'));
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
            'editUnit.required' => 'Mohon pilih salah satu unit',
            'editNominal.required' => 'Mohon masukkan nominal SPP',
        ];

        $this->validate($request, [
            'editNominal' => 'required'
        ], $messages);

        $unit = Unit::sekolah()->doesntHave('sppNominal')->count();

        $editable = true;
        if($unit < 1) $editable = false;

        if($editable){
            $validator = Validator::make($request->all(), [
                'editUnit' => 'required',
            ], $messages);

            if($validator->fails()){
                return redirect()->back()->withErrors($validator)->withInput();
            }
        }

        $item = $request->id ? ($request->user()->pegawai->unit->name != 'Manajemen' ? SppNominal::where(['id' => $request->id,'unit_id' => $request->user()->pegawai->unit_id])->first() : SppNominal::find($request->id)) : null;

        if($editable){
            if($request->user()->pegawai->unit->name == 'Manajemen')
                $SppNominal = SppNominal::where('unit_id',$request->editUnit);
            else
                $SppNominal = SppNominal::where('unit_id',$request->user()->pegawai->unit_id);
        }

        if($item && (!$editable || ($editable && $SppNominal->count() < 1))){
            $old = $item->name;
            $nominalValue = (int)str_replace('.','',$request->editNominal);
            if($editable){
                $item->unit_id = $request->editUnit;
            }
            $item->spp_nominal = $nominalValue;
            $item->save();

            $item->fresh();
            
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
        $item = SppNominal::where('id', $request->id);
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
