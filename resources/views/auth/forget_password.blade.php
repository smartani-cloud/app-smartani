@extends('template.login.master')

@section('title')
Lupa Sandi @endsection

@section('headmeta')
<!-- Main styles required by this view -->
<link href="{{ asset('css/util.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/login.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
        <div class="wrap-login100 p-l-55 p-r-55 p-t-30 p-b-30">
            <form class="login100-form" action="{{ route('forget.password.post') }}" method="post">
                @csrf
                <div class="login100-logo m-b-5">
                    <img src="{{ asset('img/logo/logomark.png') }}">
                </div>
                <span class="login100-form-title">MUDA</span>
                <span class="login100-form-subtitle p-b-27">Make Ur Digital Assistant <i>for School</i></span>
				  @if(Session::has('success'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  @endif
                  @if(Session::has('danger'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session::get('danger') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  @endif
				  @error('email')
				  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $message }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
				  @enderror
				  <div class="text-center m-b-20">
					<span class="text-secondary">Masukkan alamat email akun Anda yang terdaftar di SISTA</span>
				  </div>
                <div class="wrap-input100 m-b-25">
                    <input class="input100" type="email" name="email" placeholder="Alamat Email" value="{{ old('email') }}" required="required" autofocus="autofocus">
                    <span class="focus-input100"></span>
                </div>

                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn">
                        Reset Sandi
                    </button>
                </div>
				  <div class="text-center m-t-25">
					<a href="{{ route('psb.index',['view' => 'login']) }}" class="text-secondary font-weight-bold">Kembali ke Menu Login</a>
				  </div>
            </form>
        </div>
<!--Row-->
@endsection