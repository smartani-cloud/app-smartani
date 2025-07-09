<?php

namespace Modules\FarmManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use Modules\FarmManagement\Models\Plant;
use Modules\FarmManagement\Models\PlantCategory;
use Modules\FarmManagement\Models\PlantType;

class PlantController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->module = 'farmmanagement';
        $this->template = '';
        $this->active = 'Tanaman';
        $this->route = 'plant-list';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Plant::all();
        $categories = PlantCategory::all();
        $types = PlantType::all();

        $used = null;
        foreach($data as $d){
            if($d->plantingCycles()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-index', compact('data','used','module','active','route','categories','types'));
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
            'type.required' => 'Mohon pilih salah satu jenis tanaman',
            'name.required' => 'Mohon tuliskan nama '.strtolower($this->active),
            'growth_cycle_days.required' => 'Mohon isi lama siklus tanam',
            'growth_cycle_days.numeric' => 'Lama siklus tanam harus berupa angka',
            'yield_per_hole_min.required' => 'Mohon isi jumlah panen minimum per lubang tanam',
            'yield_per_hole_max.required' => 'Mohon isi jumlah panen maksimum per lubang tanam',
            'fruit_weight_min_g.required' => 'Mohon isi berat buah minimum (gram)',
            'fruit_weight_max_g.required' => 'Mohon isi berat buah maksimum (gram)',
            'daily_watering_min.required' => 'Mohon isi frekuensi penyiraman minimum per hari',
            'daily_watering_max.required' => 'Mohon isi frekuensi penyiraman maksimum per hari'
        ];

        $request->validate([
            'type' => 'required',
            'name' => 'required|string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'growth_cycle_days' => 'required|numeric|min:1',
            'yield_per_hole_min' => 'required|numeric|min:1',
            'yield_per_hole_max' => 'required|numeric|min:1',
            'fruit_weight_min_g' => 'required|numeric|min:1',
            'fruit_weight_max_g' => 'required|numeric|min:1',
            'daily_watering_min' => 'required|numeric|min:1',
            'daily_watering_max' => 'required|numeric|min:1',
        ], $messages);

        $plant = Plant::where([
            'type_id' => $request->type,
            'name' => $request->name
        ]);

        if($plant->count() < 1){
            $type = PlantType::where('id',$request->type)->first();
            if($type){
                $item = new Plant();
                $item->type_id = $request->type;
                $item->name = $request->name;
                $item->scientific_name = $request->scientific_name;
                $item->growth_cycle_days = (int)str_replace('.','',$request->growth_cycle_days);
                $item->yield_per_hole_min = (int)str_replace('.','',$request->yield_per_hole_min);
                $item->yield_per_hole_max = (int)str_replace('.','',$request->yield_per_hole_max);
                $item->fruit_weight_min_g = (int)str_replace('.','',$request->fruit_weight_min_g);
                $item->fruit_weight_max_g = (int)str_replace('.','',$request->fruit_weight_max_g);
                $item->daily_watering_min = (int)str_replace('.','',$request->daily_watering_min);
                $item->daily_watering_max = (int)str_replace('.','',$request->daily_watering_max);
                $item->save();

                Session::flash('success','Data '.$item->name.' berhasil ditambahkan');
            }

            else Session::flash('danger','Mohon pilih salah satu kategori tanaman');
        }

        else{
            $plant = $plant->first();
            Session::flash('danger','Data '.$plant->name.' sudah pernah ditambahkan');
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
        $data = $request->id ? Plant::find($request->id) : null;

        if($data){
            $categories = PlantCategory::all();
            $types = PlantType::all();

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

        $item = Plant::find($request->id);
        $plant = Plant::where([
            'type_id' => $request->editType,
            'name' => $request->editName
        ])->where('id','!=',$request->id);

        if($item && $plant->count() < 1){
            $type = PlantType::where('id',$request->editType)->first();
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
        $item = Plant::find($id);
        $used_count = $item ? $item->plantingCycles()->count() : 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->growthPredictions()->delete();
            $item->delete();

            Session::flash('success','Data '.strtolower($this->active).' '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
