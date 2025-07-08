<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wilayah;

class WilayahController extends Controller
{
    public function getKabupaten($provinsi)
    {
        $kabupatens = Wilayah::whereRaw('LENGTH(code) = 5')->where('code','LIKE',$provinsi.'.%')->orderBy('name', 'ASC')->pluck("name","code");
        return json_encode($kabupatens);
    }

    public function getKecamatan($kabupaten)
    {
        $kecamatan = Wilayah::whereRaw('LENGTH(code) = 8')->where('code','LIKE',$kabupaten.'.%')->orderBy('name', 'ASC')->pluck("name","code");
        return json_encode($kecamatan);
    }

    public function getDesa($kecamatan)
    {
        $desa = Wilayah::whereRaw('LENGTH(code) = 13')->where('code','LIKE',$kecamatan.'.%')->orderBy('name', 'ASC')->pluck("name","code");
        return json_encode($desa);
    }

    public function searchDesa(Request $request)
    {
        $desa = Wilayah::whereRaw('LENGTH(code) = 13')->where('name','LIKE','%'.$request->desa.'%')->orderBy('name', 'ASC')->get();
        return view('cari_wilayah', compact('desa'));
    }
}
