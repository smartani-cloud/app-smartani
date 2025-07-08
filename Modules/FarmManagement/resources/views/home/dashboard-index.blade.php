@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('title')
{{ $active }}
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
  </ol>
</div>

<!-- Content Row -->
<div class="row mb-4">
<div class="col-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-brand-green">Monitor Greenhouse</h6>
      </div>
      <div class="card-body pt-0">
        <div class="row pt-2">
            @foreach($sensors as $s)
          <div class="col border-right">
            <h2 class="font-weight-light">{{ $s->datas()->orderBy('recorded_at','desc')->first()->value }}</h2>
            <h6>{{ $s->name }}</h6>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
  </div>

<!-- Content Row -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body mx-4 my-2">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-sm text-brand-orange mb-1">
            Selamat Datang,</div>
            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ Auth::user()->pegawai ? Auth::user()->pegawai->name : Auth::user()->name }}</div>
          </div>
          <div class="col-auto">
            <img class="img-fluid px-3 px-sm-4 my-3" style="width: 25rem;" src="{{ asset('img/undraw_coding_re_iv62.svg') }}" alt="Welcome">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.global.tooltip')
@endsection