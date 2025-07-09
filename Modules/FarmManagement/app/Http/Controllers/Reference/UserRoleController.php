<?php

namespace App\Http\Controllers\Reference;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\UserRole;

class UserRoleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->template = 'proyek-penjualan.reference.';
        $this->active = 'User Role';
        $this->route = 'role';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = UserRole::all();
        $used = null;
        $active = $this->active;
        $route = $this->route;

        return view($this->template.$route.'-index', compact('data','active','route'));
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
            'name.required' => 'Mohon tuliskan '.strtolower($this->active),
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $count = UserRole::where('name', $request->name)->count();

        if($count < 1){
            $item = new UserRole();
            $item->name = $request->name;
            $item->save();

            Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
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
        $data = $request->id ? UserRole::find($request->id) : null;
        $active = $this->active;
        $route = $this->route;

        return view('template.modal.post-edit', compact('data','active','route'));
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
            'name.required' => 'Mohon tuliskan '.strtolower($this->active),
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $item = UserRole::find($request->id);
        $count = UserRole::where('name',$request->name)->where('id','!=',$request->id)->count();

        if($item && $count < 1){
            $old = $item->name;
            $item->name = $request->name;
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
    public function destroy($id)
    {
        $item = UserRole::find($id);
        //$employee_count = $item->pegawai()->where('active_status_id',1)->count();
        $used_count = 0;
        if($item && $used_count < 1){
            $name = $item->name;
            $item->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route($this->route.'.index');
    }
}
