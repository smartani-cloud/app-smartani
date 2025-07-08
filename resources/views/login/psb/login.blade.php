@extends('template.login.master')

@section('title')
Masuk @endsection

@section('headmeta')
<!-- Main styles required by this view -->
<link href="{{ asset('css/util.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/login.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
        <div class="wrap-login100 p-l-55 p-r-55 p-t-30 p-b-30">
            <form class="login100-form validate-form" action="/psb" method="post">
                @csrf
                <div class="login100-logo m-b-5">
                   <img src="{{ asset('img/logo/logomark.png') }}">
                </div>
                <span class="login100-form-subtitle p-b-27">DIGIYOK<i> Sekolah</i></span>
                <span class="login100-form-subtitle p-b-27">LOGIN Pendaftaran Siswa Baru</span>

                <div id="buttonForm" class="row mt-4" style="justify-content: center {{ $request->view == "login" ? ';display: none' : ''}}">
                  <button onclick="document.location='/psb/pendaftaran'">
                    <div class="col-md-4 col-12 px-1">
                      <div class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="/psb/pendaftaran">
                        <div class="card-body text-center">
                          {{-- <i class="zmdi zmdi-graduation-cap zmdi-hc-3x"></i> --}}
                          <i class="fas fa-paste"></i>
                          <div class="row">
                            <div class="col-12">
                              <span class="text-element">Pendaftaran</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </button>
                  <button type="button" onclick="toLogin()">
                    <div class="col-md-4 col-12 px-1">
                      <div class="wrap-element100 card mb-4 stretched-link text-decoration-none" href="/psb/pendaftaran">
                        <div class="card-body text-center">
                          {{-- <i class="zmdi zmdi-graduation-cap zmdi-hc-3x"></i> --}}
                          <i class="fas fa-sign-in-alt"></i>
                          <div class="row">
                            <div class="col-12">
                              <span class="text-element">Masuk</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </button>
                </div>
                <div id="loginForm" style="{{ $request->view == "login" ? '' : 'display: none'}}">
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
                  <div class="wrap-input100 validate-input m-b-20" data-validate="Masukkan Username">
                      <input class="input100" type="text" name="username" placeholder="Username">
                      <span class="focus-input100"></span>
                  </div>
  
                  <div class="wrap-input100 validate-input m-b-25" data-validate = "Masukkan sandi">
                      <input class="input100" type="password" name="password" placeholder="Sandi">
                      <span class="focus-input100"></span>
                  </div>
  
                  {{-- <div class="row mb-1">
                    <div class="col-12 px-1 text-right">
                      <a href="/psb/pendaftaran" class="btn btn-sm bg-gray-200"><span class="text-gray-600 mx-2">Buat Akun</span></a>
                    </div>
                  </div> --}}
                  <div class="container-login100-form-btn m-t-2">
                    <button class="login100-form-btn">
                      Masuk
                    </button>
                  </div>
                  <div class="text-center m-t-25">
                    <a href="{{ route('forget.password.get') }}" class="text-brand-blue-dark font-weight-bold">Lupa sandi?</a>
                  </div>
                  <div class="p-t-20 p-b-10">
                    <hr>
                  </div>
                  <div class="text-center">
                    <div class="container-login100-form-btn">
                      <a href="{{ route('psb.index') }}" class="btn btn-outline-secondary">Halaman Awal</a>
                    </div>
                  </div>
              </div>
            </form>
          </div>
<!--Row-->
@endsection

@section('footjs')
<script>
    function toLogin() {
        var page1 = document.getElementById("buttonForm");
        var page2 = document.getElementById("loginForm");
        page1.style.display = "none";
        page2.style.display = "block";
    }
</script>
@endsection