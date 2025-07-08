@extends('template.login.master')

@section('title')
Reset Sandi @endsection

@section('headmeta')
<!-- Main styles required by this view -->
<link href="{{ asset('css/util.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/login.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
        <div class="wrap-login100 p-l-55 p-r-55 p-t-30 p-b-30">
            <form class="login100-form" action="{{ route('reset.password.post') }}" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="login100-logo m-b-5">
                    <img src="{{ asset('img/logo/logomark.png') }}">
                </div>
                <span class="login100-form-title">MUDA</span>
                <span class="login100-form-subtitle p-b-27">Make Ur Digital Assistant <i>for School</i></span>
                  @if(Session::has('danger'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session::get('danger') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  @endif
				  @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                  @endif
				  <div class="text-center m-b-20">
					<span class="text-secondary">Masukkan sandi baru Anda. Minimal terdiri dari 8 karakter.</span>
				  </div>
                  <div class="wrap-input100 m-b-20">
                    <input class="input100" type="email" name="email" placeholder="Alamat Email" value="{{ old('email') }}" required="required" autofocus="autofocus">
                    <span class="focus-input100"></span>
                  </div>
				
                  <div class="wrap-input100 m-b-20">
                      <input class="input100" type="password" id="password" name="password" minlength="8" maxlength="255" placeholder="Sandi Baru" required="required" autofocus="autofocus">
                      <span class="focus-input100"></span>
                  </div>
  
                  <div class="wrap-input100 m-b-25">
                      <input class="input100" type="password" id="password-confirm" name="password_confirmation" minlength="8" maxlength="255" placeholder="Konfirmasi Sandi" required="required">
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