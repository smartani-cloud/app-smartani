<?php

namespace App\Http\Controllers\Kbm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Models\Kbm\NamaKelas;

class NamaKelasController extends Controller
{
    public function index()
    {
        //cek unit id
        $unit = auth()->user()->pegawai->unit_id;

        if($unit == 5){
            $lists = NamaKelas::all();
        }else{
            $lists = NamaKelas::where('unit_id',$unit)->get();
        }
        return view('kbm.namakelas.index',compact('lists'));
    }

    public function create()
    {
        //
        return view('kbm.namakelas.tambah');
    }

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'nama_kelas' => 'required',
        ]);
        
        //cek unit id
        $unit = Auth::user()->pegawai->unit_id;

        // create to table
        NamaKelas::create([
            'unit_id' => $unit,
    		'class_name' => $request->nama_kelas,
        ]);

        // return with create success notification
        return redirect('/kependidikan/kbm/kelas/nama-kelas')->with('sukses','Tambah Nama Kelas Berhasil');
    }

    public function update(Request $request, $id)
    {
        // Validate
        $request->validate([
            'nama_kelas' => 'required',
        ]);

        // update kelompok mata pelajaran
    	$namakelas = NamaKelas::find($id);
        $namakelas->class_name = $request->nama_kelas;
        $namakelas->save();

        // update success notification
        return redirect('/kependidikan/kbm/kelas/nama-kelas')->with('sukses','Ubah Nama Kelas Berhasil');
    }

    public function destroy($id)
    {
        // destroy
    	$namakelas = NamaKelas::find($id);
    	$namakelas->delete();

        // return with destroy success notification
        return redirect('/kependidikan/kbm/kelas/nama-kelas')->with('sukses','Hapus Nama Kelas Berhasil');
    }
}
