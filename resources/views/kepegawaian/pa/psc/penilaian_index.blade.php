@extends('template.main.master')

@section('title')
PSC Pegawai
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
    <h1 class="h3 mb-0 text-gray-800">PSC Pegawai</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        @if(!isset($tahun))
        <li class="breadcrumb-item active" aria-current="page">PSC Pegawai</li>
        @else
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index') }}">PSC Pegawai</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $tahun->academic_year }}</li>
        @endif
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <form action="{{ route('psc.penilaian.index') }}" method="get">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                   <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt" required="required">
                      @foreach($tahunPelajaran as $t)
                      @if($t->is_active == 1 || $t->nilaiPsc()->count() > 0)
                      <option value="{{ $t->academicYearLink }}" {{ $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endif
                      @endforeach
                    </select>
                    <a href="{{ route('psc.penilaian.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('psc.penilaian.index') }}">Pilih</a>
                  </div>
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Unit Tersedia</h6>
            </div>
            <div class="card-body p-3">
              @if(count($allUnit) > 0)
              <div class="row ml-1">
                @foreach($allUnit as $u)
                <div class="col-md-6 col-12 mb-3">
                  <div class="row py-2 rounded border border-light mr-2">
                    <div class="col-8 d-flex align-items-center">
                      <div class="mr-3">
                        <div class="icon-circle bg-gray-500">
                          <i class="fas fa-school text-white"></i>
                        </div>
                      </div>
                      <div>
                        <a class="font-weight-bold text-dark" href="{{ route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink, 'unit' => $u->name])}}">
                          {{ $u->name }}
                        </a>
                      </div>
                    </div>
                    <div class="col-4 d-flex justify-content-end align-items-center">
                      <a href="{{ route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink, 'unit' => $u->name])}}" class="btn btn-sm btn-outline-brand-green-dark">Pilih</a>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data unit yang ditemukan</h6>
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

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@endsection