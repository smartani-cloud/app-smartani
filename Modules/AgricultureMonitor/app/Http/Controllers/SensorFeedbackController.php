<?php

namespace Modules\AgricultureMonitor\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use Modules\FarmManagement\Models\Plant;

use Modules\AgricultureMonitor\Models\Sensor;
use Modules\AgricultureMonitor\Models\SensorFeedback;

class SensorFeedbackController extends Controller
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
        $this->active = 'Umpan Balik Sensor';
        $this->route = 'sensor-feedback';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = SensorFeedback::all();
        $plants = Plant::all();
        $sensors = Sensor::all();

        $used = null;

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-index', compact('data','used','module','active','route','plants','sensors'));
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
            'plant.required' => 'Mohon pilih salah satu tanaman',
            'plant.exists' => 'Mohon pilih tanaman yang valid',
            'sensor.required' => 'Mohon pilih salah satu sensor',
            'sensor.exists' => 'Mohon pilih sensor yang valid',
            'min.required' => 'Mohon isi nilai minimum sensor',
            'min.integer' => 'Nilai minimum sensor harus berupa angka',
            'max.required' => 'Mohon isi nilai maksimum sensor',
            'max.integer' => 'Nilai maksimum sensor harus berupa angka',
            'feedback.required' => 'Mohon tuliskan '.strtolower($this->active),
            'feedback.string' => $this->active.' harus berupa teks.',
            'feedback.max' => $this->active.' tidak boleh lebih dari 255 karakter.',
        ];

        // $request->merge([
        //     'min' => str_replace(['.', ','], ['', '.'], $request->min),
        //     'max' => str_replace(['.', ','], ['', '.'], $request->max),
        // ]);

        $request->validate([
            'plant' => 'required|exists:Modules\FarmManagement\Models\Plant,id',
            'sensor' => 'required|exists:Modules\AgricultureMonitor\Models\Sensor,id',
            'min' => 'required|numeric',
            'max' => 'required|numeric',
            'feedback' => 'required|string|max:255',
        ], $messages);

        $sensorFeedback = SensorFeedback::where([
            'plant_id' => $request->plant,
            'sensor_id' => $request->sensor,
            'min' => $request->min,
            'max' => $request->max
        ]);

        if($sensorFeedback->count() < 1){
            $item = new SensorFeedback();
            $item->plant_id = $request->plant;
            $item->sensor_id = $request->sensor;
            $item->value = 0; // Default value, can be updated later
            $item->min = $request->min;
            $item->max = $request->max;
            $item->feedback = $request->feedback;
            $item->save();

            $item->refresh();

            Session::flash('success','Data '.strtolower($this->active).' '.$item->sensor->name.' berhasil ditambahkan');
        }

        else{
            $sensorFeedback = $sensorFeedback->first();
            Session::flash('danger','Data  '.strtolower($this->active).' '.$sensorFeedback->sensor->name.' sudah pernah ditambahkan');
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
        $data = $request->id ? SensorFeedback::find($request->id) : null;

        if($data){
            $categories = PlantCategory::all();
            $types = Sensor::all();

            $module = $this->module;
            $active = $this->active;
            $route = $this->route;

            return view($this->module.'::'.$this->template.$route.'-edit', compact('data','module','active','route','categories','types'));
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
            'editType.required' => 'Mohon pilih salah satu jenis tanaman',
            'editName.required' => 'Mohon tuliskan nama '.strtolower($this->active),
            'editGrowthCycleDays.required' => 'Mohon isi lama siklus tanam',
            'editGrowthCycleDays.numeric' => 'Lama siklus tanam harus berupa angka',
            'editYieldPerHoleMin.required' => 'Mohon isi jumlah panen minimum per lubang tanam',
            'editYieldPerHoleMax.required' => 'Mohon isi jumlah panen maksimum per lubang tanam',
            'editFruitWeightMin.required' => 'Mohon isi berat buah minimum (gram)',
            'editFruitWeightMax.required' => 'Mohon isi berat buah maksimum (gram)',
            'editDailyWateringMin.required' => 'Mohon isi frekuensi penyiraman minimum per hari',
            'editDailyWateringMax.required' => 'Mohon isi frekuensi penyiraman maksimum per hari'
        ];

        $request->validate([
            'editType' => 'required',
            'editName' => 'required',
            'editScientificName' => 'nullable|string|max:255',
            'editGrowthCycleDays' => 'required|numeric|min:1',
            'editYieldPerHoleMin' => 'required|numeric|min:1',
            'editYieldPerHoleMax' => 'required|numeric|min:1',
            'editFruitWeightMin' => 'required|numeric|min:1',
            'editFruitWeightMax' => 'required|numeric|min:1',
            'editDailyWateringMin' => 'required|numeric|min:1',
            'editDailyWateringMax' => 'required|numeric|min:1',
        ], $messages);

        $item = SensorFeedback::find($request->id);
        $plant = SensorFeedback::where([
            'type_id' => $request->editType,
            'name' => $request->editName
        ])->where('id','!=',$request->id);

        if($item && $plant->count() < 1){
            $type = Sensor::where('id',$request->editType)->first();
            if($type){
                $name = $item->name;
                $item->type_id = $request->editType;
                $item->name = $request->editName;
                $item->scientific_name = $request->editScientificName;
                $item->growth_cycle_days = (int)str_replace('.','',$request->editGrowthCycleDays);
                $item->yield_per_hole_min = (int)str_replace('.','',$request->editYieldPerHoleMin);
                $item->yield_per_hole_max = (int)str_replace('.','',$request->editYieldPerHoleMax);
                $item->fruit_weight_min_g = (int)str_replace('.','',$request->editFruitWeightMin);
                $item->fruit_weight_max_g = (int)str_replace('.','',$request->editFruitWeightMax);
                $item->daily_watering_min = (int)str_replace('.','',$request->editDailyWateringMin);
                $item->daily_watering_max = (int)str_replace('.','',$request->editDailyWateringMax);
                $item->save();
                
                Session::flash('success','Data '.$name.' berhasil diubah');
            }

            else Session::flash('danger','Mohon pilih salah satu kategori tanaman');
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
        $item = SensorFeedback::find($id);
        if($item){
            $name = $item->sensor ? $item->sensor->name : null;
            $item->delete();

            Session::flash('success','Data '.strtolower($this->active).($name ? ' '.$name : '').' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
