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
                <span class="login100-form-subtitle p-b-12">Make Ur Digital Assistant <i>for School</i></span>
				<div class="text-center">
					<span class="mdi mdi-email-newsletter mdi-48px text-brand-purple-dark"></span>
				</div>
				  <div class="text-center m-b-20">
					<h5 class="text-secondary font-weight-bold">Terima kasih, periksa email Anda dan ikuti instruksi yang ada untuk reset kata sandi.</h5>
				  </div>
				  <div class="text-center m-b-30">
					<span class="text-secondary">Email tidak diterima? Periksa folder spam Anda atau <a href="{{ route('forget.password.get') }}" class="font-weight-bold text-brand-purple-dark">Kirim Ulang</a>.</span>
				  </div>
				  <div class="text-center m-t-25">
					<a href="{{ route('psb.index',['view' => 'login']) }}" class="text-secondary font-weight-bold">Kembali ke Menu Login</a>
				  </div>
            </form>
        </div>
<!--Row-->
@endsection