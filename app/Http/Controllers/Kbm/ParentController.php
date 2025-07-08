<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use App\Models\Siswa\OrangTua;
use App\Models\Siswa\Siswa;
use Illuminate\Http\Request;

class ParentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
        // dd($request->old, $request->new);
        $siswas = Siswa::where('parent_id',$request->old)->get();
        $check_new = OrangTua::find($request->new);
        if(!$check_new)dd('gada');
        foreach($siswas as $siswa){
            $siswa->parent_id = $request->new;
            $siswa->save();
        }
        $parent_old = OrangTua::find($request->old);
        $parent_old->delete();
        return redirect()->back()->with('success','hapus '.$request->old.' sukses, ubah ke'.$request->new.'');
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
        $parent = OrangTua::find($id);
        return response()->json([$id => $parent->father_name],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
