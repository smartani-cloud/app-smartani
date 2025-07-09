<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
    	$role = $request->user()->role->name;

    	/*if(in_array($role,['pembinayys','ketuayys','direktur','etl','etm','fam','faspv','am','aspv',
    		'kepsek','wakasek']))
            $folder = $role;
        else*/
            $folder = 'read-only';

        //return view('keuangan.'.$folder.'.dasbor_index', compact('pegawai'));
        return view('keuangan.'.$folder.'.dasbor_index');
    }
}