@extends('template.main.master')

@section('title')
Laporan Prestasi Kerja
@endsection

@section('headmeta')
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
    <h1 class="h3 mb-0 text-gray-800">Laporan Prestasi Kerja</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.laporan.pegawai.index') }}">Laporan Prestasi Kerja</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.laporan.pegawai.index', ['tahun' => $tahun->academicYearLink]) }}">{{ $tahun->academic_year }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.laporan.pegawai.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}">{{ $unitAktif->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $nilai->pegawai->name }}</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nama</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $nilai->pegawai->name }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Tahun Pelajaran</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $tahun->academic_year }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Unit</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $unitAktif->name }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Jabatan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $nilai->jabatan->name }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('psc.laporan.pegawai.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-{{ $nilai && $nilai->total_score ? 'brand-green' : 'secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Jumlah Nilai</div>
                        <h6 class="mb-0">{{ $nilai && $nilai->total_score ? $nilai->total_score : '-' }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-{{ $nilai && $nilai->grade_name ? 'brand-green' : 'secondary' }}">
                          <i class="fas fa-equals text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Grade</div>
                        <h6 class="mb-0">{{ $nilai && $nilai->grade_name ? $nilai->grade_name : '-' }}</h6>
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
                <h6 class="m-0 font-weight-bold text-brand-green">Laporan Prestasi Kerja</h6>
                @if($nilai && $nilai->acc_status_id == 1)
                <div class="m-0 float-right">
                  <a href="{{ route('psc.laporan.pegawai.unduh', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'pegawai' => $nilai->pegawai->nip]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-download ml-1"></i></a>
                </div>
                @endif
            </div>
            <div class="card-body p-3">
              @if($nilai && $nilai->detail()->count() > 0)
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="fa fa-check"></i></strong> Nilai ini telah disetujui oleh {{ Auth::user()->pegawai->is($nilai->accPegawai) ? 'Anda' : $nilai->accPegawai->name }} pada {{ date('j M Y H.i.s', strtotime($nilai->acc_time)) }}
              </div>
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek dan Indikator Kinerja Utama</th>
                      <th style="min-width: 80px">Skor</th>
                      <th>Bobot</th>
                      <th>Nilai</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $kodeIndikator = $nilai->detail()->pluck('code')->unique()->toArray();
                    natsort($kodeIndikator);
                    @endphp
                    @foreach($kodeIndikator as $k)
                    @php
                    $n = $nilai->detail()->where('code',$k)->first();
                    @endphp
                    <tr>
                      <td>{{ $n->code }}</td>
                      <td class="{{ $n->indikator->level == 1 ? 'font-weight-bold' : '' }}">{{ $n->indikator->name }}</td>
                      <td>{{ $n->score }}</td>
                      <td>{{ $n->percentage }}%</td>
                      <td>{{ $n->total_score }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                  <h6 class="font-weight-light mb-3">Tidak ada data nilai Laporan Prestasi Kerja yang ditemukan</h6>
              </div>
              @endif
              <div class="card-footer"></div>
            </div>
        </div>
    </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@endsection