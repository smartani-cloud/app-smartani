@extends('template.main.master')

@section('title')
Laporan PSC Saya
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
    <h1 class="h3 mb-0 text-gray-800">Laporan PSC Saya</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        @if(!isset($tahun))
        <li class="breadcrumb-item active" aria-current="page">Laporan PSC Saya</li>
        @else
        <li class="breadcrumb-item"><a href="{{ route('psc.laporan.saya.index') }}">Laporan PSC Saya</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $tahun->academic_year }}</li>
        @endif
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <form action="{{ route('psc.laporan.saya.index') }}" method="get">
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
                      <option value="{{ $t->academicYearLink }}" {{ $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                    </select>
                    <a href="{{ route('psc.laporan.saya.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('psc.laporan.saya.index') }}">Pilih</a>
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

@if($nilai && $nilai->detail()->count() > 0)
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
                  {{ $nilai->tahunPelajaran->academicYearLink }}
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
                  {{ $nilai->unit->name }}
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
                <h6 class="m-0 font-weight-bold text-brand-green">Laporan PSC Saya</h6>
            </div>
            <div class="card-body p-3">
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
            </div>
        </div>
    </div>
</div>
@else
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="text-center mx-3 my-5">
          <h3 class="text-center">Mohon Maaf,</h3>
          <h6 class="font-weight-light mb-3">Tidak ada data nilai PSC yang ditemukan</h6>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@endsection