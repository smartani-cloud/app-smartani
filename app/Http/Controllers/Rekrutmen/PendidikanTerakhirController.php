<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Rekrutmen\PendidikanTerakhir;

class PendidikanTerakhirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pendidikan = PendidikanTerakhir::orderBy('id')->get();

        return view('kepegawaian.etm.pendidikan_terakhir_index', compact('pendidikan'));
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
            'name.required' => 'Mohon tuliskan pendidikan terakhir',
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $count = PendidikanTerakhir::where('name', $request->name)->count();

        if($count < 1){
            $pendidikan = new PendidikanTerakhir();
            $pendidikan->name = $request->name;
            $pendidikan->desc = isset($request->desc) ? $request->desc : null;
            $pendidikan->save();

            Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
        }

        else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');

        return redirect()->route('pendidikanterakhir.index');
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
        $pendidikan = $request->id ? PendidikanTerakhir::find($request->id) : null;

        return view('kepegawaian.etm.pendidikan_terakhir_ubah', compact('pendidikan'));
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
            'name.required' => 'Mohon tuliskan pendidikan terakhir',
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $pendidikan = PendidikanTerakhir::find($request->id);
        $count = PendidikanTerakhir::where('name',$request->name)->where('id','!=',$request->id)->count();

        if($pendidikan && $count < 1){
            $pendidikan->name = $request->name;
            $pendidikan->desc = isset($request->desc) ? $request->desc : null;
            $pendidikan->save();
            
            Session::flash('success','Data '.$pendidikan->name.' berhasil diubah');
        }

        else Session::flash('danger','Perubahan data gagal disimpan');

        return redirect()->route('pendidikanterakhir.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pendidikan = PendidikanTerakhir::find($id);
        $employee_count = $pendidikan->pegawai()->where('active_status_id',1)->count();
        if($pendidikan && $employee_count < 1){
            $name = $pendidikan->name;
            $pendidikan->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route('pendidikanterakhir.index');
    }
}
