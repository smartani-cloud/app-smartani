<?php

namespace App\Http\Controllers\Rekrutmen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Session;

use App\Models\Rekrutmen\LatarBidangStudi;

class ProgramStudiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $latar = LatarBidangStudi::orderBy('name')->get();

        return view('kepegawaian.manajemen.rekrutmen.program_studi_index', compact('latar'));
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
            'name.required' => 'Mohon tuliskan nama program studi',
        ];

        $this->validate($request, [
            'name' => 'required',
        ], $messages);

        $count = LatarBidangStudi::where('name', $request->name)->count();
        
        if($count < 1){
            $latar = new LatarBidangStudi();
            $latar->name = $request->name;
            $latar->save();

            Session::flash('success','Data '.$request->name.' berhasil ditambahkan');
        }

        else Session::flash('danger','Data '.$request->name.' sudah pernah ditambahkan');

        return redirect()->route('programstudi.index');
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
        $latar = $request->id ? LatarBidangStudi::find($request->id) : null;

        return view('kepegawaian.etm.program_studi_ubah', compact('latar'));
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

        $latar = LatarBidangStudi::find($request->id);
        $count = LatarBidangStudi::where('name',$request->name)->where('id','!=',$request->id)->count();

        if($latar && $count < 1){
            $latar->name = $request->name;
            $latar->save();

           Session::flash('success','Data '.$latar->name.' berhasil diubah');
        }

        else Session::flash('danger','Perubahan data gagal disimpan');

        return redirect()->route('programstudi.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $latar = LatarBidangStudi::find($id);
        $employee_count = $latar->pegawai()->where('active_status_id',1)->count();
        if($latar && $employee_count < 1){
            $name = $latar->name;
            $latar->delete();

            Session::flash('success','Data '.$name.' berhasil dihapus');
        }
        else Session::flash('danger','Data gagal dihapus');

        return redirect()->route('programstudi.index');
    }
}
