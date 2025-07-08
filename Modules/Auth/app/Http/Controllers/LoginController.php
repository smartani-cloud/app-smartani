<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Kbm\Semester;

use Auth;
use Session;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(Auth::check()) {
            if(auth()->user()->role->name == 'keulsi') return redirect()->route('keuangan.index');
            elseif(auth()->user()->role->name == 'ortu') return redirect()->route('psb.index');
            return view('auth::sso');
        } else return view('auth::index');
    }


    public function login(Request $request)
    {
		$credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
		
        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            $profile = $request->user()->profiles()->where('profilable_type',$request->user()->role->group->profilable_type)->first();

            // Check remaining period
            if($profile->profilable instanceof App\Models\Rekrutmen\Pegawai && $request->user()->pegawai && $request->user()->pegawai->remainingPeriod == 'Habis'){
                Session::flash('danger', 'Sisa masa kerja Anda sudah habis. Mohon hubungi Administrator.');
                Auth::logout();
                return redirect()->route('login');
            }
            $role = $request->user()->role->name;
            // Exclusion
            if($role == 'keulsi') return redirect()->route('keuangan.index');
            elseif($role == 'ortu') return redirect()->route('psb.index');

            $semester = Semester::where('is_active', 1)->first();
            Session::put('semester_aktif', $semester->id);

            return view('auth::sso');
        } else {
            Session::flash('danger', 'Nama pengguna atau kata sandi salah');
            return redirect()->route('login');
        }
    }

    public function logout()
    {
        Session::forget('semester_aktif');
        Session::flash('success', 'Anda telah keluar dari sistem');
        if(Auth::user() && Auth::user()->role->name == 'ortu'){
            Auth::logout();
            return redirect('/psb?view=login');
        }
        else{
            Auth::logout();
            return redirect()->route('login');
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
