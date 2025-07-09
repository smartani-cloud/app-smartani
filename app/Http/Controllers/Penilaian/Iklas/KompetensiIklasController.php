<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Iklas;

use App\Http\Controllers\Controller;
use App\Models\Penilaian\Iklas\KompetensiIklas;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KompetensiIklasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'kompetensi';
        $this->modul = $modul;
        $this->active = 'Kompetensi IKLaS';
        $this->route = $this->subsystem.'.penilaian.iklas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $unit = null, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;
        
        $data = $used = null;

        $unitList = Unit::select('id','name')->sekolah();
        if($role == 'kepsek'){
            $myUnit = auth()->user()->pegawai->unit->name;
            if($unit && $unit != $myUnit){
                $unit = null;
            }
            else $unit = $myUnit;
            $unitList = $unitList->where('name',$myUnit);
        }
        $unitList = $unitList->get();

        if($unit){
            $unit = Unit::sekolah()->where('name',$unit)->first();
            
            if($unit){
                // Inti Function
                $data = KompetensiIklas::select('id','name')->orderBy('name');
                if($role == 'kepsek')
                    $data = $data->where('unit_id',auth()->user()->pegawai->unit_id);
                $data = $data->get();                

                $used = null;
                foreach($data as $d){
                    if($d->categories()->count() > 0) $used[$d->id] = 1;
                    else $used[$d->id] = 0;
                }
            }
            else{
                if($role == 'kepsek'){
                    $unit = auth()->user()->pegawai->unit;
                    return redirect()->route($this->route.'.index',['unit' => $unit->name]);
                }
                else return redirect()->route($this->route.'.index');
            }
        }
        else{
            if($role == 'kepsek'){
                $unit = auth()->user()->pegawai->unit;
                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','unitList','unit','data','used'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $messages = [
                'unit.required' => 'Mohon pilih salah satu unit',
                'name.required' => 'Mohon tuliskan kompetensi IKLaS',
                'name.max' => 'Panjang kompetensi IKLaS maksimal 50 karakter'
            ];

            $this->validate($request, [
                'name' => 'required|max:50'
            ], $messages);

            if($role != 'kepsek'){
                $validator = Validator::make($request->all(), [
                    'unit' => 'required',
                ], $messages);

                if($validator->fails()){
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }

            $count = KompetensiIklas::where([
                'unit_id' => $unit->id,
                'name' => $request->name
            ]);
            
            if($count->count() < 1){
                $competence = new KompetensiIklas();
                $competence->unit_id = $unit->id;
                $competence->name = $request->name;
                $competence->save();

                Session::flash('success','Data kompetensi '.$request->name.' berhasil ditambahkan');
            }
            else Session::flash('danger','Data kompetensi '.$request->name.' sudah pernah ditambahkan');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
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
    public function edit(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        $unitList = Unit::select('id','name')->sekolah();
        if($role == 'kepsek'){
            $unitList = $unitList->where('name',auth()->user()->pegawai->unit->name);
        }
        $unitList = $unitList->get();

        if(in_array($role,['kepsek','wakasek']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $data = $request->id ? KompetensiIklas::where([
                'id' => $request->id,
                'unit_id' => $unit->id
            ])->first() : null;
            $active = $this->active;
            $route = $this->route;

            $unitEditable = false;
            if($role != 'kepsek'){
                $unitEditable = $data ? ($data->categories()->count() > 0 ? false : true) : false;
            }

            return view($route.'-edit', compact('active','route','unitList','unit','data','unitEditable'));
        }
        else return 'Ups, unit tidak ditemukan';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $messages = [
                'editUnit.required' => 'Mohon pilih salah satu unit',
                'editName.required' => 'Mohon tuliskan kompetensi IKLaS',
                'editName.max' => 'Panjang kompetensi IKLaS maksimal 50 karakter'
            ];

            $this->validate($request, [
                'editName' => 'required|max:50'
            ], $messages);

            $item = KompetensiIklas::where([
                'id' => $request->id,
                'unit_id' => $unit->id
            ])->first();

            if($role != 'kepsek' && $item && $item->categories()->count() < 1){
                $validator = Validator::make($request->all(), [
                    'editUnit' => 'required',
                ], $messages);

                if($validator->fails()){
                    return redirect()->back()->with('danger','Perubahan data gagal disimpan');
                }
            }

            $count = KompetensiIklas::where([
                'unit_id' => $unit->id,
                'name' => $request->editName
            ])->where('id','!=',$request->id)->count();

            if($item && $count < 1){
                $old = $item->name;
                $item->name = $request->editName;
                if(isset($request->editUnit) && $role != 'kepsek' && $item->categories()->count() < 1)
                    $item->unit_id = $request->editUnit;
                $item->save();
                
                Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->name);
            }

            else Session::flash('danger','Perubahan data gagal disimpan');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $unit, $id)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $item = KompetensiIklas::where([
                'id' => $request->id,
                'unit_id' => $unit->id
            ])->first();

            $used_count = $item ? $item->categories()->count() : 0;
            if($item && $used_count < 1){
                $name = $item->name;
                $item->delete();

                Session::flash('success','Data '.$name.' berhasil dihapus');
            }
            else Session::flash('danger','Data gagal dihapus');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian\Iklas;

use App\Http\Controllers\Controller;
use App\Models\Penilaian\Iklas\KompetensiIklas;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KompetensiIklasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'kompetensi';
        $this->modul = $modul;
        $this->active = 'Kompetensi IKLaS';
        $this->route = $this->subsystem.'.penilaian.iklas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $unit = null, $tahun = null, $semester = null)
    {
        $role = auth()->user()->role->name;
        
        $data = $used = null;

        $unitList = Unit::select('id','name')->sekolah();
        if($role == 'kepsek'){
            $myUnit = auth()->user()->pegawai->unit->name;
            if($unit && $unit != $myUnit){
                $unit = null;
            }
            else $unit = $myUnit;
            $unitList = $unitList->where('name',$myUnit);
        }
        $unitList = $unitList->get();

        if($unit){
            $unit = Unit::sekolah()->where('name',$unit)->first();
            
            if($unit){
                // Inti Function
                $data = KompetensiIklas::select('id','name')->orderBy('name');
                if($role == 'kepsek')
                    $data = $data->where('unit_id',auth()->user()->pegawai->unit_id);
                $data = $data->get();                

                $used = null;
                foreach($data as $d){
                    if($d->categories()->count() > 0) $used[$d->id] = 1;
                    else $used[$d->id] = 0;
                }
            }
            else{
                if($role == 'kepsek'){
                    $unit = auth()->user()->pegawai->unit;
                    return redirect()->route($this->route.'.index',['unit' => $unit->name]);
                }
                else return redirect()->route($this->route.'.index');
            }
        }
        else{
            if($role == 'kepsek'){
                $unit = auth()->user()->pegawai->unit;
                return redirect()->route($this->route.'.index',['unit' => $unit->name]);
            }
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','unitList','unit','data','used'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $messages = [
                'unit.required' => 'Mohon pilih salah satu unit',
                'name.required' => 'Mohon tuliskan kompetensi IKLaS',
                'name.max' => 'Panjang kompetensi IKLaS maksimal 50 karakter'
            ];

            $this->validate($request, [
                'name' => 'required|max:50'
            ], $messages);

            if($role != 'kepsek'){
                $validator = Validator::make($request->all(), [
                    'unit' => 'required',
                ], $messages);

                if($validator->fails()){
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }

            $count = KompetensiIklas::where([
                'unit_id' => $unit->id,
                'name' => $request->name
            ]);
            
            if($count->count() < 1){
                $competence = new KompetensiIklas();
                $competence->unit_id = $unit->id;
                $competence->name = $request->name;
                $competence->save();

                Session::flash('success','Data kompetensi '.$request->name.' berhasil ditambahkan');
            }
            else Session::flash('danger','Data kompetensi '.$request->name.' sudah pernah ditambahkan');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
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
    public function edit(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        $unitList = Unit::select('id','name')->sekolah();
        if($role == 'kepsek'){
            $unitList = $unitList->where('name',auth()->user()->pegawai->unit->name);
        }
        $unitList = $unitList->get();

        if(in_array($role,['kepsek','wakasek']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $data = $request->id ? KompetensiIklas::where([
                'id' => $request->id,
                'unit_id' => $unit->id
            ])->first() : null;
            $active = $this->active;
            $route = $this->route;

            $unitEditable = false;
            if($role != 'kepsek'){
                $unitEditable = $data ? ($data->categories()->count() > 0 ? false : true) : false;
            }

            return view($route.'-edit', compact('active','route','unitList','unit','data','unitEditable'));
        }
        else return 'Ups, unit tidak ditemukan';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $unit)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $messages = [
                'editUnit.required' => 'Mohon pilih salah satu unit',
                'editName.required' => 'Mohon tuliskan kompetensi IKLaS',
                'editName.max' => 'Panjang kompetensi IKLaS maksimal 50 karakter'
            ];

            $this->validate($request, [
                'editName' => 'required|max:50'
            ], $messages);

            $item = KompetensiIklas::where([
                'id' => $request->id,
                'unit_id' => $unit->id
            ])->first();

            if($role != 'kepsek' && $item && $item->categories()->count() < 1){
                $validator = Validator::make($request->all(), [
                    'editUnit' => 'required',
                ], $messages);

                if($validator->fails()){
                    return redirect()->back()->with('danger','Perubahan data gagal disimpan');
                }
            }

            $count = KompetensiIklas::where([
                'unit_id' => $unit->id,
                'name' => $request->editName
            ])->where('id','!=',$request->id)->count();

            if($item && $count < 1){
                $old = $item->name;
                $item->name = $request->editName;
                if(isset($request->editUnit) && $role != 'kepsek' && $item->categories()->count() < 1)
                    $item->unit_id = $request->editUnit;
                $item->save();
                
                Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->name);
            }

            else Session::flash('danger','Perubahan data gagal disimpan');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $unit, $id)
    {
        $role = auth()->user()->role->name;

        if(in_array($role,['kepsek','wakasek']))
            $unit = auth()->user()->pegawai->unit;
        else
            $unit = Unit::select('id','name')->sekolah()->where('name',$unit)->first();

        if($unit){
            // Inti function
            $item = KompetensiIklas::where([
                'id' => $request->id,
                'unit_id' => $unit->id
            ])->first();

            $used_count = $item ? $item->categories()->count() : 0;
            if($item && $used_count < 1){
                $name = $item->name;
                $item->delete();

                Session::flash('success','Data '.$name.' berhasil dihapus');
            }
            else Session::flash('danger','Data gagal dihapus');

            return redirect()->route($this->route.'.index',['unit' => $unit->name]);
        }
        else{
            Session::flash('danger', 'Unit tidak ditemukan');

            return redirect()->route($this->route.'.index');
        }
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
