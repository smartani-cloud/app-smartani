@extends('template.login.master')

@section('title')
Pilih Sistem @endsection

@section('headmeta')
<!-- Main styles required by this view -->
<link href="{{ asset('css/util.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/login.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <div class="wrap-menu100 card mb-4">
        <div class="card-body px-5 pt-5 pb-4">
          <h3 class="mt-3 mb-2 font-weight-bold text-brand-green">Selamat datang, {{ Auth::user()->pegawai->nickname }}!</h3>
          <span>Sebelum mulai, pilih salah satu sistem yang Anda tuju</span>
            <div class="row mt-4">
              <div class="col-md-4 col-12 px-1">
                <a class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="{{ route('kepegawaian.index') }}">
                  <div class="card-body text-center">
                    <i class="zmdi zmdi-accounts-list-alt zmdi-hc-3x"></i>
                    <div class="row">
                      <div class="col-12">
                        <span class="text-element">Kepegawaian</span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-md-4 col-12 px-1">
                <a class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="{{ url('/kependidikan') }}">
                  <div class="card-body text-center">
                    <i class="zmdi zmdi-graduation-cap zmdi-hc-3x"></i>
                    <div class="row">
                      <div class="col-12">
                        <span class="text-element">Kependidikan</span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
              <div class="col-md-4 col-12 px-1">
                <a class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="{{ url('/keuangan') }}">
                  <div class="card-body text-center">
                    <i class="zmdi zmdi-balance-wallet zmdi-hc-3x"></i>
                    <div class="row">
                      <div class="col-12">
                        <span class="text-element">Keuangan</span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          <div class="row mb-2">
            <div class="col-12 px-1 text-right">
                <a href="{{ route('logout') }}" class="btn btn-sm bg-gray-200"><span class="text-gray-600 mx-2">Keluar</span></a>
            </div>
          </div>
        </div>
      </div>
<!--Row-->
@endsection