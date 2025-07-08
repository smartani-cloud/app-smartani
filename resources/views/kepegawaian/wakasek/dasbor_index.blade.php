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
  @php
  if(isset($pegawai)){
    $total = $pegawai->count();
    $pt = $pegawai->where('employee_status_id',1)->count();
    $pttk = $pegawai->where('employee_status_id',3)->count();
    $ptth = $pegawai->where('employee_status_id',4)->count();
    $ptt = $pttk + $ptth;
  }
  else{
    $total = $pt = $ptt = $ptth = $pttk = 0;
  }
  @endphp
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Jumlah Pegawai</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-users fa-2x text-brand-green"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Pegawai Tetap</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pt }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-brand-green"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Pegawai Tidak Tetap Honorer</div>
            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $ptth }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-info"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold mb-1">Pegawai Tidak Tetap Kontrak</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pttk }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-user fa-2x text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@php
$unit = Auth::user()->pegawai->unit;
@endphp
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-brand-green">Unit Anda</h6>
      </div>
      <div class="card-body pt-1">
        <div class="d-flex align-items-center mb-3">
          <span class="fa-stack fa-2x">
            <i class="fas fa-square fa-stack-2x text-brand-green-dark"></i>
            <i class="fas fa-building fa-stack-1x fa-inverse"></i>
          </span>
          <h3 class="pt-1 mb-0">{{ $unit->desc }}</h3>
        </div>
        <div class="ml-2">
          <div class="row">
            @php
            $address = explode(";",$unit->address);
            $phone_unit = explode(";",$unit->phone_unit);
            $i = 0;
            @endphp
            @foreach($address as $a)
            <div class="col-md-6 col-12">
              <div class="d-flex align-items-start mb-2">
                <span class="mdi mdi-map-marker mdi-24px mr-2 text-danger"></span>
                <div class="row mt-2">
                  <div class="col-12">
                    {{ explode("-",$a)[0] }}
                  </div>
                  <div class="col-12">
                    {{ explode("-",$a)[1] }}
                  </div>
                  <div class="col-12">
                    {{ $unit->wilayah->name.', '.$unit->wilayah->kecamatanName().', '.$unit->wilayah->kabupatenName() }}
                  </div>
                  <div class="col-12">
                    {{ $unit->postal_code }}
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span class="mdi mdi-phone-classic mdi-24px mr-2 text-primary"></span>
                <div class="row mt-2">
                  @php
                  $phone = explode("-",$phone_unit[$i]);
                  $j = 1;
                  $max = count($phone);
                  @endphp
                  <div class="col-12">
                    @foreach($phone as $p)
                    {{ $p }}
                    @if($j != $max),@endif
                    @php $j++ @endphp
                    @endforeach
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-start mb-2">
                <span class="mdi mdi-email mdi-24px mr-2 text-warning"></span>
                <div class="row mt-2">
                  <div class="col-12">
                    {{ $unit->email }}
                  </div>
                </div>
              </div>
            </div>
            @php $i++ @endphp
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
  @php
  $senior = $pegawai->sortBy('join_date')->take(10)->all();
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
  $tua = $pegawai->where('birth_date','<=',Carbon::parse('-50 year'))->sortBy('birth_date')->all();
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
<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@endsection