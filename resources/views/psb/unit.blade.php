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
          <h3 class="mt-3 mb-2 font-weight-bold text-brand-green">Selamat datang, Calon Siswa Baru!</h3>
          <span>Sebelum mulai, pilih salah satu unit yang Anda tuju</span>
            <div class="row mt-4">
              <form action="/psb/pendaftaran" method="POST">
              @csrf
                <input type="text" value="TK" name="unit_name" hidden>
                <button>
                  <div class="col-md-4 col-12 px-1">
                    <div class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="/psb/pendaftaran">
                      <div class="card-body text-center">
                        <i class="zmdi zmdi-graduation-cap zmdi-hc-3x"></i>
                        <div class="row">
                          <div class="col-12">
                            <span class="text-element">TK</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </button>
              </form>
              <form action="/psb/pendaftaran" method="POST">
              @csrf
                <input type="text" value="SD" name="unit_name" hidden>
                <button>
                  <div class="col-md-4 col-12 px-1">
                    <div class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="/psb/pendaftaran">
                      <div class="card-body text-center">
                        <i class="zmdi zmdi-graduation-cap zmdi-hc-3x"></i>
                        <div class="row">
                          <div class="col-12">
                            <span class="text-element">SD</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </button>
              </form>
              <form action="/psb/pendaftaran" method="POST">
              @csrf
                <input type="text" value="SMP" name="unit_name" hidden>
                <button>
                  <div class="col-md-4 col-12 px-1">
                    <div class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="/psb/pendaftaran">
                      <div class="card-body text-center">
                        <i class="zmdi zmdi-graduation-cap zmdi-hc-3x"></i>
                        <div class="row">
                          <div class="col-12">
                            <span class="text-element">SMP</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </button>
              </form>
              <form action="/psb/pendaftaran" method="POST">
              @csrf
                <input type="text" value="SMA" name="unit_name" hidden>
                <button> 
                  <div class="col-md-4 col-12 px-1">
                    <div class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="/psb/pendaftaran">
                      <div class="card-body text-center">
                        <i class="zmdi zmdi-graduation-cap zmdi-hc-3x"></i>
                        <div class="row">
                          <div class="col-12">
                            <span class="text-element">SMA</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </button>
              </form>
            </div>
          <div class="row mb-2">
            <div class="col-12 px-1 text-right">
                <a href="/psb" class="btn btn-sm bg-gray-200"><span class="text-gray-600 mx-2">Login</span></a>
            </div>
          </div>
        </div>
      </div>
<!--Row-->
@endsection