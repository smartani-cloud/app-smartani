@extends('template.main.master')

@section('title')
Atur Aspek IKU
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
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
    <h1 class="h3 mb-0 text-gray-800">Atur Aspek IKU</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.index') }}">Indikator Kinerja Utama</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.aspek.index') }}">Atur Aspek IKU</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $iku->name }}</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="categoryOpt" class="form-control-label">IKU</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="input-group">
                      <select aria-label="IKU" name="iku" class="form-control" id="categoryOpt" required="required">
                        @foreach($categories as $c)
                        <option value="{{ $c->nameLc }}" {{ $c->name == $iku->name ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                      </select>
                      <a href="{{ route('iku.aspek.index') }}" id="btn-select-category" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('iku.aspek.index') }}">Pilih</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Unit Tersedia</h6>
            </div>
            <div class="card-body p-3">
              <div class="row ml-1">
                @foreach($units as $u)
                <div class="col-md-6 col-12 mb-3">
                  <div class="row py-2 rounded border border-light mr-2">
                    <div class="col-8 d-flex align-items-center">
                      <div class="mr-3">
                        <div class="icon-circle bg-gray-500">
                          <i class="fas fa-school text-white"></i>
                        </div>
                      </div>
                      <div>
                        <a class="font-weight-bold text-dark" href="{{ route('iku.aspek.index', ['iku' => $iku->nameLc, 'unit' => $u->name]) }}">
                          {{ $u->name }}
                        </a>
                      </div>
                    </div>
                    <div class="col-4 d-flex justify-content-end align-items-center">
                      <a href="{{ route('iku.aspek.index', ['iku' => $iku->nameLc, 'unit' => $u->name]) }}" class="btn btn-sm btn-outline-brand-green-dark">Pilih</a>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->

@include('template.modal.konfirmasi_hapus')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.iku.change-category')
@endsection
