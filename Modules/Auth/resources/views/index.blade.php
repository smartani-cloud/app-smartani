@extends('auth::components.layouts.master')

@section('title')
Masuk @endsection

@section('headmeta')
<!-- Main styles required by this view -->
<link href="{{ asset('modules/Auth/css/util.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('modules/Auth/css/login.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
        <div class="wrap-login100 p-l-55 p-r-55 p-t-30 p-b-30">
            <form class="login100-form validate-form" action="{{ url('/login') }}" method="post">
                @csrf
                <div class="login100-logo m-b-5">
                    <img src="{{ asset('img/logo/logomark.png') }}">
                </div>
                <span class="login100-form-title p-b-27"><img class="img-logotype" src="{{ asset('img/logo/logotype.png') }}"></span>

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
                <div class="wrap-input100 validate-input m-b-20" data-validate="Masukkan username">
                    <input class="input100" type="text" name="username" placeholder="Username">
                    <span class="focus-input100"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-25" data-validate = "Masukkan sandi">
                    <input class="input100" type="password" name="password" placeholder="Sandi">
                    <span class="focus-input100"></span>
                </div>

                <div class="container-login100-form-btn">
                    <button class="login100-form-btn">
                        Masuk
                    </button>
                </div>
                <!--<div class="text-center m-t-25">
                    <a href="{{ route('forget.password.get') }}" class="text-brand-purple-dark font-weight-bold">Lupa sandi?</a>
                </div>
                <div class="p-t-5 p-b-10">-->
            </form>
        </div>
<!--Row-->
@endsection
