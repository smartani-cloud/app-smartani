<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Session;

use App\Models\AccessRight\ModulOperation;
use App\Models\Rekrutmen\Universitas;

class UniversitasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subsystem = 'kepegawaian';
        $modul = 'universitas';
        $this->modul = $modul;
        $this->active = 'Universitas';
        $this->route = $this->subsystem.'.manajemen.rekrutmen.'.$this->modul;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /* Access Rights */
        $modul = $this->modul;
        $operations = ModulOperation::select('operation_id')->whereHas('modul',function($q)use($modul){$q->where('name',$modul);})->whereHas('rights',function($q){$q->where('role_id',auth::user()->role->id);})->with('operation:id,name')->get();
        /* End of Access Rights */

        if($operations->where('operation.name','read')->count() > 0){
            $data = Universitas::orderBy('name')->get();

            $active = $this->active;
            $route = $this->route;

            return view($this->route.'-index', compact('operations','active','route','data'));
        }
        else return redirect()->route($this->subsystem.'.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /* Access Rights */
        $modul = $this->modul;
        $operations = ModulOperation::select('operation_id')->whereHas('modul',function($q)use($modul){$q->where('name',$modul);})->whereHas('rights',function($q){$q->where('role_id',auth::user()->role->id);})->with('operation:id,name')->get();
        /* End of Access Rights */

        if($operations->where('operation.name','create')->count() > 0){
            $messages = [
                'name.required' => 'Mohon tuliskan nama universitas',
            ];

            $this->validate($request, [
                'name' => 'required',
            ], $messages);

            $count = Universitas::where('name', $request->name)->count();
            
            if($count < 1){
                $universitas = new Universitas();
                $universitas->name = $request->name;
                $universitas->save();

                Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
            }

            else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');
        }
        elseif($operations->where('operation.name','read')->count() > 0){
            return redirect()->route($this->route.'.index');
        }
        else{
            return redirect()->route($this->subsystem.'.index');
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
    public function edit(Request $request)
    {
        $universitas = $request->id ? Universitas::find($request->id) : null;

        return view('kepegawaian.etm.universitas_ubah', compact('universitas'));
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
            'name.required' => 'Mohon tuliskan nama program bidang studi',
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $universitas = Universitas::find($request->id);
        $count = Universitas::where('name',$request->name)->where('id','!=',$request->id)->count();

        if($universitas && $count < 1){
            $universitas->name = $request->name;
            $universitas->save();

           Session::flash('success','Data '.$universitas->name.' berhasil diubah');
        }

        else Session::flash('danger','Perubahan data gagal disimpan');

        return redirect()->route('universitas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $universitas = Universitas::find($id);
        $employee_count = $universitas->pegawai()->where('active_status_id',1)->count();
        if($universitas && $employee_count < 1){
            $name = $universitas->name;
            $universitas->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route('universitas.index');
    }
}
