<?php

namespace Modules\FarmManagement\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use Modules\FarmManagement\Models\HarvestQuality;

class HarvestQualityController extends Controller
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
        $this->active = 'Kualitas Panen';
        $this->route = 'harvest-quality';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = HarvestQuality::all();

        $used = null;
        foreach($data as $d){
            if($d->harvestDistributions()->count() > 0 || $d->harvestSummaries()->count() > 0) $used[$d->id] = 1;
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
        ];

        $request->validate([
            'code' => 'required',
            'name' => 'required'
        ], $messages);

        $count = HarvestQuality::where(['code' => $request->code,'name' => $request->name])->count();

        if($count < 1){
            $item = new HarvestQuality();
            $item->code = $request->code;
            $item->name = $request->name;
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
        $data = $request->id ? HarvestQuality::find($request->id) : null;

        if($data){
            $module = $this->module;
            $active = $this->active;
            $route = $this->route;

            return view('template.modal.code-reference-item-edit', compact('data','module','active','route'));
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
            'editName.required' => 'Mohon tuliskan nama '.strtolower($this->active)
        ];

        $request->validate([
            'editCode' => 'required',
            'editName' => 'required'
        ], $messages);

        $item = HarvestQuality::find($request->id);
        $category = HarvestQuality::where(['code' => $request->editCode,'name' => $request->editName])->where('id','!=',$request->id);

        if($item && $category->count() < 1){
            $name = $item->name;
            $item->code = $request->editCode;
            $item->name = $request->editName;
            $item->save();
            
            Session::flash('success','Data '.$name.' berhasil diubah');
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
        $item = HarvestQuality::find($id);
        $used_count = $item ? $item->harvestDistributions()->count() || $item->harvestSummaries()->count() : 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->delete();

            Session::flash('success','Data '.strtolower($this->active).' '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
