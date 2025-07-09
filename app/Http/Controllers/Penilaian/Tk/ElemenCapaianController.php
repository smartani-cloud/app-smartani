<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Penilaian\Tk;

use App\Http\Controllers\Controller;
use App\Models\Penilaian\Iklas\KompetensiIklas;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ElemenCapaianController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'elemen-capaian';
        $this->modul = $modul;
        $this->active = 'Elemen Capaian Pembelajaran';
        $this->route = $this->subsystem.'.penilaian.tk.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = auth()->user()->role->name;
        
        $data = AspekPerkembangan::select('id','dev_aspect')->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');})->aktif()->orderBy('dev_aspect')->get();                

        $used = null;
        foreach($data as $d){
            if($d->objectives()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','data','used'));
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
    public function store(Request $request)
    {
        $role = auth()->user()->role->name;

        $messages = [
            'name.required' => 'Mohon tuliskan elemen capaian pembelajaran',
            'name.max' => 'Panjang elemen capaian pembelajaran maksimal 100 karakter'
        ];

        $this->validate($request, [
            'name' => 'required|max:100'
        ], $messages);

        $count = AspekPerkembangan::where([
            'dev_aspect' => $request->name
        ])->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');});
        
        if($count->count() < 1){
            $aspek = new AspekPerkembangan();
            $aspek->dev_aspect = $request->name;
            $aspek->is_deleted = 0;
            $aspek->save();

            Session::flash('success','Data elemen capaian pembelajaran '.$request->name.' berhasil ditambahkan');
        }
        else Session::flash('danger','Data elemen capaian pembelajaran '.$request->name.' sudah pernah ditambahkan');

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
        $role = auth()->user()->role->name;

        $data = $request->id ? AspekPerkembangan::where([
            'id' => $request->id,
        ])->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');})->first() : null;
        $active = $this->active;
        $route = $this->route;

        return view($route.'-edit', compact('active','route','data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $role = auth()->user()->role->name;

        $messages = [
            'editName.required' => 'Mohon tuliskan elemen capaian pembelajaran',
            'editName.max' => 'Panjang elemen capaian pembelajaran maksimal 100 karakter'
        ];

        $this->validate($request, [
            'editName' => 'required|max:100'
        ], $messages);

        $item = AspekPerkembangan::where([
            'id' => $request->id
        ])->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');})->first();

        $count = AspekPerkembangan::where([
            'dev_aspect' => $request->editName
        ])->where('id','!=',$request->id)->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');})->count();

        if($item && $count < 1){
            $old = $item->name;
            $item->dev_aspect = $request->editName;
            $item->save();
            
            Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->name);
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
    public function destroy(Request $request, $id)
    {
        $role = auth()->user()->role->name;

        $item = AspekPerkembangan::where([
            'id' => $request->id
        ])->first();

        $used_count = $item ? $item->objectives()->count() : 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->predicates()->where([
                'rpd_type_id' => 15
            ])->delete();
            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
=======
<?php

namespace App\Http\Controllers\Penilaian\Tk;

use App\Http\Controllers\Controller;
use App\Models\Penilaian\Iklas\KompetensiIklas;
use App\Models\Penilaian\AspekPerkembangan;
use App\Models\Unit;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ElemenCapaianController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'elemen-capaian';
        $this->modul = $modul;
        $this->active = 'Elemen Capaian Pembelajaran';
        $this->route = $this->subsystem.'.penilaian.tk.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = auth()->user()->role->name;
        
        $data = AspekPerkembangan::select('id','dev_aspect')->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');})->aktif()->orderBy('dev_aspect')->get();                

        $used = null;
        foreach($data as $d){
            if($d->objectives()->count() > 0) $used[$d->id] = 1;
            else $used[$d->id] = 0;
        }

        $active = $this->active;
        $route = $this->route;

        return view($this->route.'-index', compact('active','route','data','used'));
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
    public function store(Request $request)
    {
        $role = auth()->user()->role->name;

        $messages = [
            'name.required' => 'Mohon tuliskan elemen capaian pembelajaran',
            'name.max' => 'Panjang elemen capaian pembelajaran maksimal 100 karakter'
        ];

        $this->validate($request, [
            'name' => 'required|max:100'
        ], $messages);

        $count = AspekPerkembangan::where([
            'dev_aspect' => $request->name
        ])->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');});
        
        if($count->count() < 1){
            $aspek = new AspekPerkembangan();
            $aspek->dev_aspect = $request->name;
            $aspek->is_deleted = 0;
            $aspek->save();

            Session::flash('success','Data elemen capaian pembelajaran '.$request->name.' berhasil ditambahkan');
        }
        else Session::flash('danger','Data elemen capaian pembelajaran '.$request->name.' sudah pernah ditambahkan');

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
        $role = auth()->user()->role->name;

        $data = $request->id ? AspekPerkembangan::where([
            'id' => $request->id,
        ])->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');})->first() : null;
        $active = $this->active;
        $route = $this->route;

        return view($route.'-edit', compact('active','route','data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $role = auth()->user()->role->name;

        $messages = [
            'editName.required' => 'Mohon tuliskan elemen capaian pembelajaran',
            'editName.max' => 'Panjang elemen capaian pembelajaran maksimal 100 karakter'
        ];

        $this->validate($request, [
            'editName' => 'required|max:100'
        ], $messages);

        $item = AspekPerkembangan::where([
            'id' => $request->id
        ])->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');})->first();

        $count = AspekPerkembangan::where([
            'dev_aspect' => $request->editName
        ])->where('id','!=',$request->id)->whereHas('kurikulum',function($q){$q->where('name','Kurdeka');})->count();

        if($item && $count < 1){
            $old = $item->name;
            $item->dev_aspect = $request->editName;
            $item->save();
            
            Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$item->name);
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
    public function destroy(Request $request, $id)
    {
        $role = auth()->user()->role->name;

        $item = AspekPerkembangan::where([
            'id' => $request->id
        ])->first();

        $used_count = $item ? $item->objectives()->count() : 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->predicates()->where([
                'rpd_type_id' => 15
            ])->delete();
            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
