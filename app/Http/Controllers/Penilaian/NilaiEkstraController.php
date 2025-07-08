<?php

namespace App\Http\Controllers\Penilaian;

use Illuminate\Http\Request;
use App\Models\Penilaian\PredikatDeskripsi;
use App\Http\Controllers\Controller;

class NilaiEkstraController extends Controller
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

    public function deskripsiekstra()
    {

        $employee_id = auth()->user()->pegawai->id;
        $rpd = PredikatDeskripsi::where([['rpd_type_id', 2], ['employee_id', $employee_id]])->orderBy('created_at', 'ASC')->get();

        if ($rpd->isEmpty()) {
            $rpd = FALSE;
        }
        return view('penilaian.deskripsiekstra', compact('rpd'));
    }

    public function simpanDeskripsi(Request $request)
    {
        $request->validate([
            'deskripsi' => 'required',
        ]);

        //Jenis predikat deskripsi untuk ekstra
        $rpd_type_id = 2;

        //id pegawai
        $employee_id = auth()->user()->pegawai->id;

        $query = PredikatDeskripsi::create([
            'description' => $request->deskripsi,
            'employee_id' => $employee_id,
            'rpd_type_id' => $rpd_type_id
        ]);

        if ($query) {
            return redirect('/kependidikan/penilaian/deskripsiekstra')->with(['sukses' => 'Data berhasil ditambahkan']);
        } else {
            return redirect('/kependidikan/penilaian/deskripsiekstra')->with(['error' => 'Data gagal ditambahkan']);
        }
    }

    //Hapus Predikat Deskripsi
    public function hapusDeskripsi(Request $request)
    {

        $query = PredikatDeskripsi::where('id', $request->id)->first();

        if ($query->delete()) {
            return redirect('/kependidikan/penilaian/deskripsiekstra')->with(['sukses' => 'Data berhasil dihapus']);
        } else {
            return redirect('/kependidikan/penilaian/deskripsiekstra')->with(['error' => 'Data gagal dihapus']);
        }
    }

    //Ubah Predikat Deskripsi
    public function ubahDeskripsi(Request $request)
    {
        $query = PredikatDeskripsi::where('id', $request->id)->first();

        $query->description = $request->deskripsi;

        if ($query->update()) {
            return redirect('/kependidikan/penilaian/deskripsiekstra')->with(['sukses' => 'Data berhasil diubah']);
        } else {
            return redirect('/kependidikan/penilaian/deskripsiekstra')->with(['error' => 'Data gagal diubah']);
        }
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
