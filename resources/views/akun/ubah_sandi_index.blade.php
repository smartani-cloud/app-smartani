@extends('template.main.master')

@section('title')
Ubah Sandi
@endsection

@section('sidebar')
@php
$role = Auth::user()->role->name;
@endphp
@if(in_array($role,['admin','am','aspv','direktur','etl','etm','fam','faspv','kepsek','pembinayys','ketuayys','wakasek','keulsi']))
@include('template.sidebar.kepegawaian.'.$role)
@else
@include('template.sidebar.kepegawaian.employee')
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Ubah Sandi</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Akun</a></li>
    <li class="breadcrumb-item"><a href="{{ route('profil.index')}}">Profil Saya</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ubah Sandi</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex flex-row mb-2">
          <div class="photo-profile-circle d-inline-block"><img src="{{ asset($profile->profilable->showPhoto) }}" alt="user-{{ $profile->profilable->id }}" class="avatar-img rounded-circle"></div>
          <div class="pl-3">
            <h3 class="font-weight-medium mt-md-3 mb-0">{{ $profile->profilable->name }}</h3>
            @if($profile->profilable instanceof App\Models\Rekrutmen\Pegawai)
            <span>{{ $profile->profilable->nip }}</span>
            @elseif($profile->profilable instanceof Modules\FarmManagement\Models\GreenhouseOwner)
            <span>{{ $profile->profilable->nik }}</span>
            @endif
            <h5 class="mt-2 mb-0">
              @if($profile->profilable->gender_id == '1')
              <span class="badge badge-info font-weight-normal">
                @if($profile->profilable instanceof App\Models\Rekrutmen\Pegawai)
                {{ ucwords($profile->profilable->jenisKelamin->name) }}
                @elseif($profile->profilable instanceof Modules\FarmManagement\Models\GreenhouseOwner)
                {{ ucwords($profile->profilable->gender->name) }}
                @endif
              </span>
              @elseif($profile->profilable->gender_id == '2')
              <span class="badge badge-brand-green font-weight-normal">
                @if($profile->profilable instanceof App\Models\Rekrutmen\Pegawai)
                {{ ucwords($profile->profilable->jenisKelamin->name) }}
                @elseif($profile->profilable instanceof Modules\FarmManagement\Models\GreenhouseOwner)
                {{ ucwords($profile->profilable->gender->name) }}
                @endif
              </span>
              @endif
              @if($profile->profilable instanceof App\Models\Rekrutmen\Pegawai)
              @if($profile->profilable->statusBaru && $profile->profilable->statusBaru->status == 'aktif' && !$profile->profilable->statusPhk)
              <span class="badge badge-primary font-weight-normal">Baru</span>
              @elseif($profile->profilable->statusPhk && $profile->profilable->statusPhk->status == 'aktif')
              <span class="badge badge-warning font-weight-normal">PHK</span>
              @endif
              @endif
            </h5>
          </div>
        </div>
      </div>
      <ul class="nav nav-tabs profile-tab" role="tablist">
        <li class="nav-item ml-3"> 
          <a class="nav-link" href="{{ route('profil.index') }}">
            Profil Saya
          </a> 
        </li>
        <li class="nav-item"> 
          <a class="nav-link active" href="{{ route('ubahsandi.index') }}">
            Ubah Sandi
          </a> 
        </li>
      </ul>
      <div class="card-body px-4 pt-4 pb-4">
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if(Session::has('danger'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ Session::get('danger') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
           <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <form action="{{ route('ubahsandi.perbarui') }}" id="password-form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Sandi Lama</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="input-group">
                      <input class="form-control" type="password" name="old_pass" maxlength="255" required="">
                      <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-toggle-visibility" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                      </div>
                    </div>
                    @error('old_pass')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Sandi Baru</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="input-group">
                      <input class="form-control" type="password" name="new_pass" minlength="6" maxlength="255" required="">
                      <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-toggle-visibility" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                      </div>
                    </div>
                    <small class="form-text"><i class="fa fa-info-circle"></i> Minimal terdiri dari 6 karakter</small>
                    @error('new_pass')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Konfirmasi Sandi</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="input-group">
                      <input class="form-control" type="password" name="new_pass_confirmation" maxlength="255" required="">
                      <div class="input-group-append">
                        <button class="btn btn-outline-secondary btn-toggle-visibility" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                      </div>
                    </div>
                    @error('new_pass_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="text-right">
                <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Password Visibility -->
<script src="{{ asset('js/password-visibility.js') }}"></script>

@include('template.footjs.modal.post_edit')
@endsection