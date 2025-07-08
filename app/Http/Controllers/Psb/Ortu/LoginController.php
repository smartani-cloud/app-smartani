<?php

namespace App\Http\Controllers\Psb\Ortu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function index(Request $request)
    {
        if(Auth::check()) {
            if(auth()->user()->role_id == 36) return view('psb.ortu.index');
            return redirect()->route('login');
        }
        return view('login.psb.login',compact('request'));
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        if (Auth::attempt($credentials)) {
            if(auth()->user()->role_id == 36){
                return redirect('/psb/index');
            }else{
                Auth::logout();
                return redirect('/psb?view=login')->with('danger', 'Username atau password salah');
            }
        }else{
            return redirect('/psb?view=login')->with('danger', 'Username atau password salah');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/psb');
    }
}
