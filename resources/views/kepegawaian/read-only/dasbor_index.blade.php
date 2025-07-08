@extends('template.main.master')

@section('title')
Beranda
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
    <h1 class="h3 mb-0 text-gray-800">Beranda</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">Beranda</li>
    </ol>
</div>

@php
$unit = Auth::user()->pegawai->unit;
@endphp
@if($unit && $unit->name != 'Manajemen')
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
                  @php
                  $line = explode("-",$a);
                  @endphp
                  @if(count($line) > 0)
                  @foreach($line as $l)
                  <div class="col-12">
                    {{ $l }}
                  </div>
                  @endforeach
                  @else
                  {{ $a }}
                  @endif
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
</div>
@else
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
            <div class="col-md-6 col-12">
              <div class="d-flex align-items-start mb-2">
                <span class="mdi mdi-map-marker mdi-24px mr-2 text-danger"></span>
                <div class="row mt-2">
                  @php
                  $address = explode("-",$unit->address);
                  @endphp
                  @if(count($address) > 0)
                  @foreach($address as $a)
                  <div class="col-12">
                    {{ $a }}
                  </div>
                  @endforeach
                  @else
                  <div class="col-12">
                    {{ $unit->address }}
                  </div>
                  @endif
                  <div class="col-12">
                    {{ $unit->wilayah->name.', '.$unit->wilayah->kecamatanName().', '.$unit->wilayah->kabupatenName() }}
                  </div>
                  <div class="col-12">
                    {{ $unit->postal_code }}
                  </div>
                </div>
              </div>
              @if($unit->phone_unit)
              <div class="d-flex align-items-start mb-2">
                <span class="mdi mdi-phone-classic mdi-24px mr-2 text-primary"></span>
                <div class="row mt-2">
                  <div class="col-12">
                    {{ $unit->phone_unit }}
                  </div>
                </div>
              </div>
              @endif
              @if($unit->email)
              <div class="d-flex align-items-start mb-2">
                <span class="mdi mdi-email mdi-24px mr-2 text-warning"></span>
                <div class="row mt-2">
                  <div class="col-12">
                    {{ $unit->email }}
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@endsection