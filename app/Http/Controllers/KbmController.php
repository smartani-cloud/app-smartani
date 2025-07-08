<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KbmController extends Controller
{
    //
    public function siswa()
    {
        return view('kbm.siswa');
    }

    //
    public function namakelas()
    {
        return view('kbm.namakelas');
    }

    //
    public function kelas()
    {
        return view('kbm.kelas');
    }

    //
    public function pengajuankelas()
    {
        return view('kbm.pengajuankelas');
    }

    //
    public function kelasdiampu()
    {
        return view('kbm.ampukelas');
    }

    //
    public function matapelajaran()
    {
        return view('kbm.matapelajaran');
    }

    //
    public function kelompokmatapelajaran()
    {
        return view('kbm.kelompokmatapelajaran');
    }

    //
    public function waktupelajaran()
    {
        return view('kbm.waktujadwal');
    }

    //
    public function jadwalpelajaran()
    {
        return view('kbm.jadwalpelajaran');
    }
    
    //
    public function tahunajaran()
    {
        return view('kbm.tahunajaran');
    }
}
