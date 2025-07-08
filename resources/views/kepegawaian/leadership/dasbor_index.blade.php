@extends('template.main.master')

@section('title')
Beranda
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
    <h1 class="h3 mb-0 text-gray-800">Beranda</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Beranda</li>
    </ol>
</div>

<div class="row">
  <div class="col-xl-{{ !$viewYayasan ? 6 : 4 }} col-md-{{ !$viewYayasan ? 12 : 6 }} mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Jumlah Sivitas Akademika</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['total'] }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-users fa-2x text-brand-green-dark"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-{{ !$viewYayasan ? 6 : 4 }} col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Jumlah Mitra</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['mitra'] }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-hands-helping fa-2x text-secondary"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($viewYayasan)
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Jumlah Yayasan</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['yayasan'] }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-building fa-2x text-success"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Pegawai Tetap</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $count['pt'] }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-brand-green"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Pegawai Tidak Tetap Honorer</div>
            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $count['ptth'] }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x brand-green"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Pegawai Tidak Tetap Kontrak</div>
            <div class="h5 mb-0 font-weight-bold">{{ $count['pttk'] }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-lg-8">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-brand-green">Pegawai Tiap Unit</h6>
      </div>
      <div class="card-body">
        <div class="chart-bar">
          <canvas id="unitBarChart"></canvas>
        </div>
        <hr>
        Grafik batang perbandingan jumlah pegawai tiap unit
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-brand-green">Jenis Kelamin</h6>
      </div>
      <div class="card-body">
        <div class="chart-pie">
          <canvas id="genderPieChart"></canvas>
        </div>
        <hr>
        Perbandingan jumlah pegawai laki-laki dan perempuan adalah {{ isset($pegawai) && $count['total'] > 0 ? ceil(($count['laki']/$count['total'])*10).' : '.ceil(($count['perempuan']/$count['total'])*10) : '0 : 0' }}
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-brand-green">Guru Tiap Unit</h6>
      </div>
      <div class="card-body pt-0">
        <div class="row pt-2">
          <div class="col border-right">
            <h2 class="font-weight-light">{{ $count['guru_tk'] }}</h2>
            <h6>KB/TK</h6>
          </div>
          <div class="col border-right">
            <h2 class="font-weight-light">{{ $count['guru_sd'] }}</h2>
            <h6>SD</h6>
          </div>
          <div class="col border-right">
            <h2 class="font-weight-light">{{ $count['guru_smp'] }}</h2>
            <h6>SMP</h6>
          </div>
          <div class="col border-right">
            <h2 class="font-weight-light">{{ $count['guru_sma'] }}</h2>
            <h6>SMA</h6>
          </div>
          <div class="col">
            <h2 class="font-weight-light">{{ $count['guru_multi'] }}</h2>
            <h6>Multiunit</h6>
          </div>
        </div>
      </div>
    </div>
  </div>
  @php
  $senior = $pegawai->whereNotIn('id',$pejabat)->sortBy('join_date')->take(10)->all();
  @endphp
  <div class="col-md-6 col-12">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">10 Pegawai Terlama</h6>
      </div>
      <div class="card-body pt-1 pb-3 px-3">
        @foreach($senior as $p)
        <div class="row mb-3">
          <div class="col-9 d-flex align-items-center">
            <div class="card-avatar mr-3">
              <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle">
            </div>
            <div class="d-block">
              <h6 class="font-weight-medium mb-0 nowrap">{{ $p->name }}</h6><small class="text-muted no-wrap">{{ $p->jabatan ? $p->jabatan->name.(in_array($p->jabatan->kategori->id,[1,2]) ? ' '.$p->unit->name : '') : '-' }}</small></td>
            </div>
          </div>
          <div class="col-3 text-right align-middle pr-3">
            {{ $p->yearsOfService }}
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @php
  $tua = $pegawai->whereNotIn('id',$pejabat)->where('birth_date','<=',Carbon::parse('-50 year'))->sortBy('birth_date')->all();
  @endphp
  <div class="col-md-6 col-12">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">Pegawai Senja</h6>
      </div>
      <div class="card-body pt-0 pb-3 px-3">
        @if(count($tua) > 0)
        <div class="alert alert-light d-flex align-items-center" role="alert">
          <i class="fas fa-info-circle fa-lg mr-2 text-info"></i>
          <span>{{ count($tua) }} pegawai sudah memasuki usia senja</span>
        </div>
        @foreach($tua as $p)
        <div class="row mb-3">
          <div class="col-9 d-flex align-items-center">
            <div class="card-avatar mr-3">
              <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle">
            </div>
            <div class="d-block">
              <h6 class="font-weight-medium mb-0 nowrap">{{ $p->name }}</h6><small class="text-muted no-wrap">{{ $p->jabatan ? $p->jabatan->name : '-' }}</small></td>
            </div>
          </div>
          <div class="col-3 text-right align-middle pr-3">
            {{ $p->age }}
          </div>
        </div>
        @endforeach
        @else
        <div class="row">
          <div class="col-12 text-center pt-4 pb-5 px-5">
            <i class="mdi mdi-check-circle-outline mdi-48px text-brand-green d-block"></i>
            Belum ada pegawai yang memasuki usia senja
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.chart-bar-unit')
@include('template.footjs.kepegawaian.chart-pie-gender')
@endsection