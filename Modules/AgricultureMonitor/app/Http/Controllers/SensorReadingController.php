<?php

namespace Modules\AgricultureMonitor\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use Modules\AgricultureMonitor\Models\Sensor;
use Modules\AgricultureMonitor\Models\SensorReading;

class SensorReadingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = 'agriculturemonitor';
        $this->template = '';
        $this->active = 'Sensor';
        $this->route = 'sensor';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Sensor::all();

        $used = null;
        foreach($data as $d){
            if($d->feedback()->count() > 0) $used[$d->id] = 1;
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
        $data = $request->all();
        $count = 0;
        
        foreach($data as $key => $value){
            $sensor = Sensor::where(['name' => $key]);
            if($sensor->count() > 0){
                $sensor = $sensor->first();

                $log = SensorReading::create([
                    'planting_cycle_id' => 1,
                    'sensor_id' => $sensor->id,
                    'value' => $value,
                    'recorded_at' => now()
                ]);
                
                $count++;
            }
        }
        return response()->json([
                    'success' => true,
                    'message' => 'Data berhasil disimpan',
                    'data'    => $count,
                ]);
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
        $data = $request->id ? Sensor::find($request->id) : null;

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
            'editName.required' => 'Mohon tuliskan nama '.strtolower($this->active),
            'editUnit.required' => 'Mohon tuliskan besaran '.strtolower($this->active),
        ];

        $request->validate([
            'editName' => 'required',
            'editUnit' => 'required'
        ], $messages);

        $item = Sensor::find($request->id);
        $sensor = Sensor::where(['name' => $request->editName,'unit' => $request->editUnit])->where('id','!=',$request->id);

        if($item && $sensor->count() < 1){
            $name = $item->name;
            $item->name = $request->editName;
            $item->unit = $request->editUnit;
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
        $item = Sensor::find($id);
        $used_count = $item ? $item->feedback()->count() : 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->delete();

            Session::flash('success','Data '.strtolower($this->active).' '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
