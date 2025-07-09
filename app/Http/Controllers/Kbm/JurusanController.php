<<<<<<< HEAD
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Kbm\TahunAjaran;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'jurusan';
        $this->modul = $modul;
        $this->active = 'Jurusan';
        $this->route = $this->subsystem.'.kbm.kelas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $academicYearActive = TahunAjaran::aktif()->latest()->first();

        $datas = Jurusan::orderBy('id')->get();

        $used = $classCount = null;
        foreach($datas as $d){
            if($d->kelas()->count() > 0){
                $used[$d->id] = 1;
                $classCount[$d->id] = $academicYearActive ? $d->kelas()->where('academic_year_id',$academicYearActive->id)->count() : 0;
            }
            else{
                $used[$d->id] = $classCount[$d->id] = 0;
            }
        }

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_array($role,['kepsek']) && $request->user()->pegawai->unit->name == 'SMA') $editable = true;

        return view($this->route.'-index', compact('datas','used','active','route','editable','classCount'));
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
            'name.required' => 'Mohon tuliskan nama jurusan',
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $count = Jurusan::where('major_name', $request->name)->count();

        if($count < 1){
            $pendidikan = new Jurusan();
            $pendidikan->major_name = $request->name;
            $pendidikan->save();

            Session::flash('success','Data '.$request->major_name.' berhasil ditambahkan');
        }

        else Session::flash('danger','Data '.$request->major_name.' sudah pernah ditambahkan');

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
        $role = $request->user()->role->name;

        $data = $request->id ? Jurusan::find($request->id) : null;

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_array($role,['kepsek']) && $request->user()->pegawai->unit->name == 'SMA') $editable = true;

        if($editable) return view($this->route.'-edit', compact('data','active','route'));
        else return 'Ups, tidak dapat memuat data';
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
            'editName.required' => 'Mohon tuliskan nama jurusan',
        ];

        $this->validate($request, [
            'editName' => 'required',
        ], $messages);

        $data = Jurusan::find($request->id);
        $count = Jurusan::where('major_name',$request->editName)->where('id','!=',$request->id)->count();

        if($data && $count < 1){
            $old = $data->major_name;
            $data->major_name = $request->editName;
            $data->save();
            
            Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$data->major_name);
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
        $data = Jurusan::find($id);
        $classCount = $data->kelas()->count();
        if($data && $classCount < 1){
            $name = $data->major_name;
            $data->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
=======
<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Kbm\TahunAjaran;
use App\Models\Jurusan;

class JurusanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kependidikan';
        $modul = 'jurusan';
        $this->modul = $modul;
        $this->active = 'Jurusan';
        $this->route = $this->subsystem.'.kbm.kelas.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $role = $request->user()->role->name;

        $academicYearActive = TahunAjaran::aktif()->latest()->first();

        $datas = Jurusan::orderBy('id')->get();

        $used = $classCount = null;
        foreach($datas as $d){
            if($d->kelas()->count() > 0){
                $used[$d->id] = 1;
                $classCount[$d->id] = $academicYearActive ? $d->kelas()->where('academic_year_id',$academicYearActive->id)->count() : 0;
            }
            else{
                $used[$d->id] = $classCount[$d->id] = 0;
            }
        }

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_array($role,['kepsek']) && $request->user()->pegawai->unit->name == 'SMA') $editable = true;

        return view($this->route.'-index', compact('datas','used','active','route','editable','classCount'));
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
            'name.required' => 'Mohon tuliskan nama jurusan',
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $count = Jurusan::where('major_name', $request->name)->count();

        if($count < 1){
            $pendidikan = new Jurusan();
            $pendidikan->major_name = $request->name;
            $pendidikan->save();

            Session::flash('success','Data '.$request->major_name.' berhasil ditambahkan');
        }

        else Session::flash('danger','Data '.$request->major_name.' sudah pernah ditambahkan');

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
        $role = $request->user()->role->name;

        $data = $request->id ? Jurusan::find($request->id) : null;

        $active = $this->active;
        $route = $this->route;

        $editable = false;
        if(in_array($role,['kepsek']) && $request->user()->pegawai->unit->name == 'SMA') $editable = true;

        if($editable) return view($this->route.'-edit', compact('data','active','route'));
        else return 'Ups, tidak dapat memuat data';
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
            'editName.required' => 'Mohon tuliskan nama jurusan',
        ];

        $this->validate($request, [
            'editName' => 'required',
        ], $messages);

        $data = Jurusan::find($request->id);
        $count = Jurusan::where('major_name',$request->editName)->where('id','!=',$request->id)->count();

        if($data && $count < 1){
            $old = $data->major_name;
            $data->major_name = $request->editName;
            $data->save();
            
            Session::flash('success','Data '.$old.' berhasil diubah menjadi '.$data->major_name);
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
        $data = Jurusan::find($id);
        $classCount = $data->kelas()->count();
        if($data && $classCount < 1){
            $name = $data->major_name;
            $data->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
