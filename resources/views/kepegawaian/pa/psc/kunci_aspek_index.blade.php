@extends('template.main.master')

@section('title')
Kunci Aspek
@endsection

@section('headmeta')
<!-- Bootstrap Toggle -->
<link href="{{ asset('vendor/bootstrap4-toggle/css/bootstrap4-toggle.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@php
$role = Auth::user()->role->name;
@endphp
@if(in_array($role,['admin','am','aspv','direktur','etl','etm','fam','faspv','kepsek','pembinayys','ketuayys','wakasek']))
@include('template.sidebar.kepegawaian.'.$role)
@else
@include('template.sidebar.kepegawaian.employee')
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Atur Kunci Aspek</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Atur Kunci Aspek</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">Aspek Evaluasi dan IKU</h6>
      </div>
      <div class="card-body p-3">
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
        <form action="{{ route('psc.aspek.kunci.perbarui') }}" id="aspect-setting-form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-5 col-md-8 col-sm-9 col-12">
                    <label for="normal-input" class="form-control-label">Kunci Aspek Evaluasi dan IKU</label>
                  </div>
                  <div class="col-lg-7 col-md-4 col-sm-3 col-12">
                    <input name="lock" class="lock-toggle" type="checkbox" data-toggle="toggle" data-on="Terbuka" data-off="Terkunci" data-onstyle="success" data-offstyle="danger" {{ isset($lock) && $lock->value == 1 ? 'checked' : null }} >
                    @error('lock')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="row">
                <div class="col-lg-7 offset-lg-5 col-md-4 offset-md-8 col-sn-3 offset-sm-9 col-12">
                  <div class="text-left">
                    <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                  </div>
                </div>
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

<!-- Bootstrap Toggle -->
<script src="{{ asset('vendor/bootstrap4-toggle/js/bootstrap4-toggle.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@endsection
