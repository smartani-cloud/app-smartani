<?php

namespace Modules\FarmManagement\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use Modules\FarmManagement\Models\IrrigationSystem;

class IrrigationSystemController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = 'farmmanagement';
        $this->template = 'reference.';
        $this->active = 'Sistem Irigasi';
        $this->route = 'irrigation-system';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = IrrigationSystem::all();

        $used = null;
        foreach($data as $d){
            if($d->greenhouses()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-index', compact('data','used','module','active','route'));
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
            'code.required' => 'Mohon tuliskan kode '.strtolower($this->active),
            'name.required' => 'Mohon tuliskan nama '.strtolower($this->active),          
            'desc.string' => 'Pastikan deskripsi '.strtolower($this->active).' menggunakan karakter yang valid',
            'desc.max' => 'Deskripsi'.strtolower($this->active).' maksimum 255 karakter',
        ];

        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'desc' => ' nullable|string|max:255'
        ], $messages);

        $count = IrrigationSystem::where(['code' => $request->code])->count();

        if($count < 1){
            $item = new IrrigationSystem();
            $item->code = $request->code;
            $item->name = $request->name;
            $item->desc = $request->desc;
            $item->save();

            Session::flash('success','Data '.$item->name.' berhasil ditambahkan');
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
        $data = $request->id ? IrrigationSystem::find($request->id) : null;

        if($data){
            $module = $this->module;
            $active = $this->active;
            $route = $this->route;

            return view($this->module.'::'.$this->template.$route.'-edit', compact('data','module','active','route'));
        }
        else return "Ups, tidak dapat memuat data";
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
            'editCode.required' => 'Mohon tuliskan kode '.strtolower($this->active),
            'editName.required' => 'Mohon tuliskan nama '.strtolower($this->active),      
            'editDesc.string' => 'Pastikan deskripsi '.strtolower($this->active).' menggunakan karakter yang valid',
            'editDesc.max' => 'Deskripsi'.strtolower($this->active).' maksimum 255 karakter',
        ];

        $request->validate([
            'editCode' => 'required',
            'editName' => 'required',
            'editDesc' => 'nullable|string|max:255'
        ], $messages);

        $item = IrrigationSystem::find($request->id);
        $irrigation = IrrigationSystem::where(['code' => $request->editCode])->where('id','!=',$request->id);

        if($item && $irrigation->count() < 1){
            $old = $item->name;
            $item->code = $request->editCode;
            $item->name = $request->editName;
            $item->desc = $request->editDesc;
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
    public function destroy($id)
    {
        $item = IrrigationSystem::find($id);
        $used_count = $item ? $item->greenhouses()->count() : 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->delete();

            Session::flash('success','Data '.strtolower($this->active).' '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
