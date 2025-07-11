<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $profile = $request->user()->profiles()->where('profilable_type',$request->user()->role->group->profilable_type)->first();

        return view('akun.profil_index', compact('profile'));
    }
}
