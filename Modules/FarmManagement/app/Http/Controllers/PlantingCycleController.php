<?php

namespace Modules\FarmManagement\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Carbon\Carbon;

use Modules\FarmManagement\Models\Greenhouse;
use Modules\FarmManagement\Models\GreenhouseOwner;
use Modules\FarmManagement\Models\IrrigationSystem;
use Modules\FarmManagement\Models\Plant;
use Modules\FarmManagement\Models\PlantingCycle;
use Modules\Core\Models\References\Region;
use Modules\Access\Models\Role;

use App\Models\JenisKelamin as Gender;
use App\Models\LoginUser;
use App\Models\Unit;

class PlantingCycleController extends Controller
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
        $this->active = 'Siklus Tanam';
        $this->route = 'planting-cycle';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = PlantingCycle::all();

        $used = null;
        foreach($data as $d){
            if($d->harvestSummaries()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-index', compact('data','used','module','active','route'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $greenhouses = Unit::select('id','name')->has('greenhouse')->get();
        $plants = Plant::all();

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-create', compact('module','active','route','greenhouses','plants'));
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
            'greenhouse.required' => 'Mohon pilih salah satu greenhouse',
            'greenhouse.exists' => 'Mohon pilih greenhouse yang valid',
            'plant.required' => 'Mohon pilih salah satu tanaman',
            'plant.exists' => 'Mohon pilih tanaman yang valid',
            'date.required' => 'Mohon pilih tanggal semai',
            'date.date' => 'Mohon pilih tanggal semai yang valid',
            'seed_hole.integer' => 'Lubang tanam harus berupa angka',
            'seed_hole.min' => 'Lubang tanam tidak boleh kurang dari 0',
            'irrigation_duration.required' => 'Mohon masukkan durasi penyiraman',
            'irrigation_duration.integer' => 'Pastikan durasi penyiraman hanya mengandung angka',
            'irrigation_duration.between' => 'Pastikan durasi penyiraman antara 0 sampai 3000',
            'capital_cost.integer' => 'Modal harus berupa angka',
            'capital_cost.min' => 'Modal tidak boleh kurang dari 0',
            'min_price.integer' => 'Modal harus berupa angka',
            'min_price.min' => 'Modal tidak boleh kurang dari 0',
            'max_price.integer' => 'Modal harus berupa angka',
            'max_price.min' => 'Modal tidak boleh kurang dari 0',
        ];

        $request->merge([
            'seed_hole' => str_replace(['.', ','], ['', '.'], $request->seed_hole),
            'capital_cost' => str_replace(['.', ','], ['', '.'], $request->capital_cost),
            'min_price' => str_replace(['.', ','], ['', '.'], $request->min_price),
            'max_price' => str_replace(['.', ','], ['', '.'], $request->max_price),
        ]);

        $request->validate([
            'greenhouse' => 'required|exists:App\Models\Unit,id',
            'plant' => 'required|exists:Modules\FarmManagement\Models\Plant,id',
            'date' => 'required|date',
            'seed_hole' => 'required|integer|min:0',
            'irrigation_duration' => 'required|integer|between:0,3000',
            'capital_cost' => 'nullable|integer|min:0',
            'min_price' => 'nullable|integer|min:0',
            'max_price' => 'nullable|integer|min:0',
        ], $messages);

        $greenhouse = Unit::select('id')->has('greenhouse')->where('id',$request->greenhouse)->first();
        if(!$greenhouse) return redirect()->back()->withInput();  

        $plant= Plant::select('id','yield_per_hole_min','yield_per_hole_max','fruit_weight_min_g','fruit_weight_max_g')->where('id', $request->plant)->first();
        if(!$plant) return redirect()->back()->withInput(); 
        
        $dateCode = date('Ym');
        $greenhouseId = str_pad($greenhouse->greenhouse->id ?? 0, 4, '0', STR_PAD_LEFT);
        $plantCode     = str_pad($plant->id ?? 0, 3, '0', STR_PAD_LEFT);
        // Hitung jumlah greenhouse yang sudah ada di lokasi itu tahun ini
        $batchPrefix = $dateCode.'-'.$greenhouseId.'-'.$plantCode;
        $lastCode = PlantingCycle::select('id_planting_cycle')
            ->where('id_planting_cycle', 'like', "{$batchPrefix}-%")
            ->orderByDesc('id_planting_cycle')
            ->value('id_planting_cycle');

        $nextBatch = 1;
        if($lastCode){
            $parts = explode('-', $lastCode);
            $lastBatch = (int) ($parts[3] ?? 0);
            $nextBatch = $lastBatch + 1;
        }
        $batchCode = str_pad($nextBatch, 5, '0', STR_PAD_LEFT);

        $item = new PlantingCycle();
        $item->unit_id = $greenhouse->id;
        $item->id_planting_cycle = $dateCode.'-'.$greenhouseId.'-'.$plantCode.'-'.$batchCode;
        $item->plant_id = $plant->id;
        $item->seeding_date = Carbon::parse($request->date);
        $item->total_seed_holes = $request->seed_hole;
        $item->irrigation_duration_seconds = $request->irrigation_duration;
        $item->capital_cost = $request->capital_cost ? $request->capital_cost : 0;
        $item->min_yield_kg = $request->seed_hole ? ($request->seed_hole * $plant->yield_per_hole_min * $plant->fruit_weight_min_g)/1000 : 0;
        $item->max_yield_kg = $request->seed_hole ? ($request->seed_hole * $plant->yield_per_hole_max * $plant->fruit_weight_max_g)/1000 : 0;
        $item->save();

        $item->refresh();

        $item->harvestProjection()->create([
            'min_price_per_ounce' => $request->min_price ? $request->min_price : 0,
            'max_price_per_ounce' => $request->max_price ? $request->max_price : 0
        ]);

        Session::flash('success','Data '. strtolower($this->active) .' berhasil ditambahkan');

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
        $data = PlantingCycle::where('id_planting_cycle', $id)->first();

        if($data){
            $active = $this->active;
            $route = $this->route;

            return view($this->module.'::'.$this->template.$route.'-show', compact('data','active','route'));
        }
        return redirect()->route($this->route.'.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = PlantingCycle::where('id_planting_cycle', $id)->first();

        if($data){
            $greenhouses = Unit::select('id','name')->has('greenhouse')->get();
            $plants = Plant::all();

            $module = $this->module;
            $active = $this->active;
            $route = $this->route;

            return view($this->module.'::'.$this->template.$route.'-edit', compact('data','active','route','greenhouses','plants'));
        }
        return redirect()->route($this->route.'.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $messages = [
            'greenhouse.required' => 'Mohon pilih salah satu greenhouse',
            'greenhouse.exists' => 'Mohon pilih greenhouse yang valid',
            'plant.required' => 'Mohon pilih salah satu tanaman',
            'plant.exists' => 'Mohon pilih tanaman yang valid',
            'date.required' => 'Mohon pilih tanggal semai',
            'date.date' => 'Mohon pilih tanggal semai yang valid',
            'seed_hole.integer' => 'Lubang tanam harus berupa angka',
            'seed_hole.min' => 'Lubang tanam tidak boleh kurang dari 0',
            'irrigation_duration.required' => 'Mohon masukkan durasi penyiraman',
            'irrigation_duration.integer' => 'Pastikan durasi penyiraman hanya mengandung angka',
            'irrigation_duration.between' => 'Pastikan durasi penyiraman antara 0 sampai 3000',
            'capital_cost.integer' => 'Modal harus berupa angka',
            'capital_cost.min' => 'Modal tidak boleh kurang dari 0',
            'min_price.integer' => 'Modal harus berupa angka',
            'min_price.min' => 'Modal tidak boleh kurang dari 0',
            'max_price.integer' => 'Modal harus berupa angka',
            'max_price.min' => 'Modal tidak boleh kurang dari 0',
        ];

        $request->merge([
            'seed_hole' => str_replace(['.', ','], ['', '.'], $request->seed_hole),
            'capital_cost' => str_replace(['.', ','], ['', '.'], $request->capital_cost),
            'min_price' => str_replace(['.', ','], ['', '.'], $request->min_price),
            'max_price' => str_replace(['.', ','], ['', '.'], $request->max_price),
        ]);

        $request->validate([
            'greenhouse' => 'required|exists:App\Models\Unit,id',
            'plant' => 'required|exists:Modules\FarmManagement\Models\Plant,id',
            'date' => 'required|date',
            'seed_hole' => 'required|integer|min:0',
            'irrigation_duration' => 'required|integer|between:0,3000',
            'capital_cost' => 'nullable|integer|min:0',
            'min_price' => 'nullable|integer|min:0',
            'max_price' => 'nullable|integer|min:0',
        ], $messages);

        $greenhouse = Unit::select('id')->has('greenhouse')->where('id',$request->greenhouse)->first();
        if(!$greenhouse) return redirect()->back()->withInput();

        $plant= Plant::select('id','yield_per_hole_min','yield_per_hole_max','fruit_weight_min_g','fruit_weight_max_g')->where('id', $request->plant)->first();
        if(!$plant) return redirect()->back()->withInput();

        $item = PlantingCycle::where('id_planting_cycle', $request->id)->first();

        if($item){
            $old = $item->id_planting_cycle;
            if($item->unit_id != $greenhouse->id || $item->plant_id != $plant->id){
                $dateCode = date('Ym');
                $greenhouseId = str_pad($greenhouse->greenhouse->id ?? 0, 4, '0', STR_PAD_LEFT);
                $plantCode     = str_pad($plant->id ?? 0, 3, '0', STR_PAD_LEFT);
                // Hitung jumlah greenhouse yang sudah ada di lokasi itu tahun ini
                $batchPrefix = $dateCode.'-'.$greenhouseId.'-'.$plantCode;
                $lastCode = PlantingCycle::select('id_planting_cycle')
                    ->where('id_planting_cycle', 'like', "{$batchPrefix}-%")
                    ->orderByDesc('id_planting_cycle')
                    ->value('id_planting_cycle');

                $nextBatch = 1;
                if($lastCode){
                    $parts = explode('-', $lastCode);
                    $lastBatch = (int) ($parts[3] ?? 0);
                    $nextBatch = $lastBatch + 1;
                }
                $batchCode = str_pad($nextBatch, 5, '0', STR_PAD_LEFT);

                $item->unit_id = $greenhouse->id;
                $item->id_planting_cycle = $dateCode.'-'.$greenhouseId.'-'.$plantCode.'-'.$batchCode;
                $item->plant_id = $plant->id;
            }
            
            $item->seeding_date = Carbon::parse($request->date);
            $item->total_seed_holes = $request->seed_hole;
            $item->irrigation_duration_seconds = $request->irrigation_duration;
            $item->capital_cost = $request->capital_cost ? $request->capital_cost : 0;
            $item->min_yield_kg = $request->seed_hole ? ($request->seed_hole * $plant->yield_per_hole_min * $plant->fruit_weight_min_g)/1000 : 0;
            $item->max_yield_kg = $request->seed_hole ? ($request->seed_hole * $plant->yield_per_hole_max * $plant->fruit_weight_max_g)/1000 : 0;
            $item->save();

            $harvestProjection = $item->harvestProjection;

            if(!$harvestProjection){
                $item->harvestProjection()->create([
                    'min_price_per_ounce' => $request->min_price ? $request->min_price : 0,
                    'max_price_per_ounce' => $request->max_price ? $request->max_price : 0
                ]);
            }
            else {
                $harvestProjection->update([
                    'min_price_per_ounce' => $request->min_price ? $request->min_price : 0,
                    'max_price_per_ounce' => $request->max_price ? $request->max_price : 0
                ]);
            }

            Session::flash('success','Data '.$old.' berhasil diubah'.($old != $item->id_planting_cycle ? ' menjadi '.$item->id_planting_cycle : null));
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
        $item = PlantingCycle::where('id_planting_cycle', $id)->first();

        $used_count = $item ? $item->harvestSummaries()->count()+$item->harvestDistributions()->count() : 0;
        if($item && $used_count < 1){
            $name = $item->id_planting_cycle;

            $item->harvestProjection()->delete();

            // Delete related irrigation and sensor records
            $item->irrigationRecords()->delete();
            $item->dailyIrrigation()->delete();
            $item->sensorReadings()->delete();

            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
