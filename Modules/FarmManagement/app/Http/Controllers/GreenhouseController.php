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
use Modules\Core\Models\References\Region;
use Modules\Access\Models\Role;

use App\Models\JenisKelamin as Gender;
use App\Models\LoginUser;
use App\Models\Unit;

class GreenhouseController extends Controller
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
        $this->active = 'Greenhouse';
        $this->route = 'greenhouse-list';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Unit::select('id','name','region_id')->has('greenhouse')->get();

        $used = null;
        foreach($data as $d){
            if($d->greenhouseOwners()->count() > 0) $used[$d->id] = 1;
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
        $irrigationSystems = IrrigationSystem::select('code', 'name')->get();
        $provinces = Region::provinces()->get();
        $owners = GreenhouseOwner::select('id','name')->orderBy('name')->active()->get();

        $module = $this->module;
        $active = $this->active;
        $route = $this->route;

        return view($this->module.'::'.$this->template.$route.'-create', compact('module','active','route','irrigationSystems','provinces','owners'));
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
            'name.required' => 'Mohon tuliskan nama greenhouse',
            'photo.file' => 'Pastikan foto adalah berkas yang valid',
            'photo.max' => 'Ukuran foto yang boleh diunggah maksimum 1 MB',
            'photo.mimes' => 'Pastikan foto yang diunggah berekstensi .jpg, .jpeg, .png, atau .webp',
            'photo.dimensions' => 'Pastikan foto yang diunggah beresolusi minimal 200x200 px',
            'irrigation_system.required' => 'Mohon pilih salah satu sistem irigasi',
            'irrigation_system.exists' => 'Mohon pilih sistem irigasi yang valid',
            'province.required' => 'Mohon pilih salah satu provinsi',
            'city.required' => 'Mohon pilih salah satu kabupaten/kota',
            'subdistrict.required' => 'Mohon pilih salah satu kecamatan',
            'village.required' => 'Mohon pilih salah satu desa/kelurahan',
            'village.exists' => 'Mohon pilih desa/kelurahan yang valid',
            'address.required' => 'Mohon tuliskan alamat greenhouse',
            'rt.required' => 'Mohon masukkan RT',
            'rt.integer' => 'Pastikan RT hanya mengandung angka',
            'rt.between' => 'Pastikan RT antara 0 sampai 100',
            'rw.required' => 'Mohon masukkan RW',
            'rw.integer' => 'Pastikan RW hanya mengandung angka',
            'rw.between' => 'Pastikan RW antara 0 sampai 100',
            'area.numeric' => 'Luas harus berupa angka',
            'area.min' => 'Luas tidak boleh kurang dari 0',
            'elevation.numeric' => 'Ketinggian harus berupa angka',
            'elevation.min' => 'Ketinggian tidak boleh kurang dari 0',
            'gps_lat.numeric' => 'Latitude harus berupa angka',
            'gps_lat.between' => 'Latitude harus di antara -90 hingga 90 derajat',
            'gps_lng.numeric' => 'Longitude harus berupa angka',
            'gps_lng.between' => 'Longitude harus di antara -180 hingga 180 derajat',
        ];

        $request->merge([
            'area' => str_replace(['.', ','], ['', '.'], $request->area),
            'elevation' => str_replace(['.', ','], ['', '.'], $request->elevation),
        ]);

        $request->validate([
            'name' => 'required',
            'photo' => 'file|max:1024|mimes:jpg,jpeg,png,webp|dimensions:min_width=100,min_height=200',
            'irrigation_system' => 'required|exists:Modules\FarmManagement\Models\IrrigationSystem,code',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required|exists:Modules\Core\Models\References\Region,code',
            'address' => 'required',
            'rt' => 'required|integer|between:0,100',
            'rw' => 'required|integer|between:0,100',
            'area' => 'nullable|numeric|min:0',
            'elevation' => 'nullable|numeric|min:0',
            'gps_lat' => 'nullable|numeric|between:-90,90',
            'gps_lng' => 'nullable|numeric|between:-180,180',
        ], $messages);

        $village = Region::select('id','code')->whereRaw('LENGTH(code) = 13')->where('code',$request->village)->first();
        if(!$village) return redirect()->back()->withInput();

        $item = new Unit();
        $item->name = $request->name;
        $item->address = $request->address;
        $item->region_id = $village->id;
        $item->save();

        $item->refresh();
        
        $irrigationSystem = IrrigationSystem::select('id','code')->where('code',$request->irrigation_system)->first();

        $locationCode = str_pad($village->id, 6, '0', STR_PAD_LEFT);
        $year = date('Y');
        // Hitung jumlah greenhouse yang sudah ada di lokasi itu tahun ini
        $count = Greenhouse::where('id_greenhouse', 'like', "GH-{$locationCode}-{$year}-%")->count();
        $sequenceNumber = str_pad($count+1, 6, '0', STR_PAD_LEFT);
        
        $item->greenhouse()->create([
            'id_greenhouse' => 'GH-'.$locationCode.'-'.$year.'-'.$sequenceNumber,
            'irrigation_system_id' => $irrigationSystem->id,
            'photo' => null,
            'address' => $request->address,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'area' => $request->area ? $request->area : null,
            'elevation' => $request->elevation ? $request->elevation : null,
            'gps_lat' => $request->gps_lat ? $request->gps_lat : null,
            'gps_lng' => $request->gps_lng ? $request->gps_lng : null,
        ]);

        $greenhouse = $item->greenhouse;

        if($request->file('photo') && $request->file('photo')->isValid()) {
            // Pindah foto greenhouse ke folder public
            $file = $request->file('photo');
            $photo = $greenhouse->id . '_' . time() . '_photo.' . $file->getClientOriginalExtension();
            $file->storeAs('img/photo/greenhouse/', $photo, 'public');
        }

        $greenhouse->photo = isset($photo) ? $photo : null;
        $greenhouse->save();

        if($request->owner && count($request->owner) > 0) $item->greenhouseOwners()->attach($request->owner);

        Session::flash('success','Data '. $item->name .' berhasil ditambahkan');

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
        $data = Unit::select('id','name','region_id')->whereHas('greenhouse', function($query)use($id) {
            $query->where('id_greenhouse', $id);
        })->first();

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
        $data = Unit::whereHas('greenhouse', function($query)use($id) {
            $query->where('id_greenhouse', $id);
        })->first();

        if($data){
            $irrigationSystems = IrrigationSystem::select('code', 'name')->get();
            $provinces = Region::provinces()->get();
            $owners = GreenhouseOwner::select('id','name')->orderBy('name')->active()->get();

            $cities = $subdistricts = $villages = null;

            if($data->region){
                $cities = Region::citiesByCode($data->region->code)->orderBy('name')->get();
                $subdistricts = Region::subdistrictsByCode($data->region->code)->orderBy('name')->get();
                $villages = Region::villagesByCode($data->region->code)->orderBy('name')->get();
            }

            $module = $this->module;
            $active = $this->active;
            $route = $this->route;

            return view($this->module.'::'.$this->template.$route.'-edit', compact('data','active','route','irrigationSystems','provinces','cities','subdistricts','villages','owners'));
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
            'name.required' => 'Mohon tuliskan nama greenhouse',
            'photo.file' => 'Pastikan foto adalah berkas yang valid',
            'photo.max' => 'Ukuran foto yang boleh diunggah maksimum 1 MB',
            'photo.mimes' => 'Pastikan foto yang diunggah berekstensi .jpg, .jpeg, .png, atau .webp',
            'photo.dimensions' => 'Pastikan foto yang diunggah beresolusi minimal 200x200 px',
            'irrigation_system.required' => 'Mohon pilih salah satu sistem irigasi',
            'irrigation_system.exists' => 'Mohon pilih sistem irigasi yang valid',
            'province.required' => 'Mohon pilih salah satu provinsi',
            'city.required' => 'Mohon pilih salah satu kabupaten/kota',
            'subdistrict.required' => 'Mohon pilih salah satu kecamatan',
            'village.required' => 'Mohon pilih salah satu desa/kelurahan',
            'village.exists' => 'Mohon pilih desa/kelurahan yang valid',
            'address.required' => 'Mohon tuliskan alamat greenhouse',
            'rt.required' => 'Mohon masukkan RT',
            'rt.integer' => 'Pastikan RT hanya mengandung angka',
            'rt.between' => 'Pastikan RT antara 0 sampai 100',
            'rw.required' => 'Mohon masukkan RW',
            'rw.integer' => 'Pastikan RW hanya mengandung angka',
            'rw.between' => 'Pastikan RW antara 0 sampai 100',
            'area.numeric' => 'Luas harus berupa angka',
            'area.min' => 'Luas tidak boleh kurang dari 0',
            'elevation.numeric' => 'Ketinggian harus berupa angka',
            'elevation.min' => 'Ketinggian tidak boleh kurang dari 0',
            'gps_lat.numeric' => 'Latitude harus berupa angka',
            'gps_lat.between' => 'Latitude harus di antara -90 hingga 90 derajat',
            'gps_lng.numeric' => 'Longitude harus berupa angka',
            'gps_lng.between' => 'Longitude harus di antara -180 hingga 180 derajat',
        ];

        $request->merge([
            'area' => str_replace(['.', ','], ['', '.'], $request->area),
            'elevation' => str_replace(['.', ','], ['', '.'], $request->elevation),
        ]);

        $request->validate([
            'name' => 'required',
            'photo' => 'file|max:1024|mimes:jpg,jpeg,png,webp|dimensions:min_width=100,min_height=200',
            'irrigation_system' => 'required|exists:Modules\FarmManagement\Models\IrrigationSystem,code',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required|exists:Modules\Core\Models\References\Region,code',
            'address' => 'required',
            'rt' => 'required|integer|between:0,100',
            'rw' => 'required|integer|between:0,100',
            'area' => 'nullable|numeric|min:0',
            'elevation' => 'nullable|numeric|min:0',
            'gps_lat' => 'nullable|numeric|between:-90,90',
            'gps_lng' => 'nullable|numeric|between:-180,180',
        ], $messages);

        $village = Region::select('id','code')->whereRaw('LENGTH(code) = 13')->where('code',$request->village)->first();
        if(!$village) return redirect()->back()->withInput();

        $item = Unit::whereHas('greenhouse', function($query)use($request) {
            $query->where('id_greenhouse', $request->id);
        })->first();

        if($item){
            $greenhouse = $item->greenhouse;

            $old = $item->name;
            $item->name = $request->name;
            $item->address = $request->address;
            $item->region_id = $village->id;
            $item->save();            
            
            if($request->file('photo') && $request->file('photo')->isValid()) {
                // Hapus file foto greenhouse di folder public
                if($greenhouse->photoPath && Storage::disk('public')->exists('img/photo/greenhouse/'.$greenhouse->photo)) Storage::disk('public')->delete('img/photo/greenhouse/'.$greenhouse->photo);

                // Pindah foto greenhouse ke folder public
                $file = $request->file('photo');
                $photo = $greenhouse->id . '_' . time() . '_photo.' . $file->extension();
                $file->storeAs('img/photo/greenhouse/', $photo, 'public');
            }            
        
            $irrigationSystem = IrrigationSystem::select('id','code')->where('code',$request->irrigation_system)->first();

            $greenhouse->irrigation_system_id = $irrigationSystem->id;
            $greenhouse->photo = isset($photo) ? $photo : $greenhouse->photo;
            $greenhouse->address = $request->address;
            $greenhouse->rt = $request->rt;
            $greenhouse->rw = $request->rw;
            $greenhouse->area = $request->area ? $request->area : null;
            $greenhouse->elevation = $request->elevation ? $request->elevation : null;
            $greenhouse->gps_lat = $request->gps_lat ? $request->gps_lat : null;
            $greenhouse->gps_lng = $request->gps_lng ? $request->gps_lng : null;
            $greenhouse->save();

            $item->refresh();

            if($request->owner && count($request->owner) > 0) $item->greenhouseOwners()->sync($request->owner);
            else $item->greenhouseOwners()->detach();

            Session::flash('success','Data '.$old.' berhasil diubah'.($old != $item->name ? ' menjadi '.$item->name : null));
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
        $item = Unit::whereHas('greenhouse', function($query)use($id) {
            $query->where('id_greenhouse', $id);
        })->first();

        $used_count = 0;
        //$used_count = $item ? $item->greenhouseOwners()->count() : 0;
        if($item && $used_count < 1){
            $greenhouse = $item->greenhouse;
            // Hapus file foto greenhouse di folder public
            if($greenhouse->photoPath && Storage::disk('public')->exists('img/photo/greenhouse/'.$greenhouse->photo)) Storage::disk('public')->delete('img/photo/greenhouse/'.$greenhouse->photo);

            $name = $item->name;

            $greenhouse->delete();
            $item->greenhouseOwners()->detach();
            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
