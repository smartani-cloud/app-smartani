@extends('template.main.master')

@section('title')
Saldo Akun Anggaran
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Saldo Akun Anggaran</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('saldo.index')}}">Saldo Akun Anggaran</a></li>
    @if($jenisAktif)
    <li class="breadcrumb-item"><a href="{{ route('saldo.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $isKso ? $tahun->academic_year : $tahun }}</li>
    @endif
  </ol>
</div>

@if($jenisAnggaranCount && $jenisAnggaranCount->where('anggaranCount','>',0)->count() > 1)
<div class="row">
    @foreach($jenisAnggaran as $j)
    @if($jenisAktif == $j)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-brand-green">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled="disabled">Pilih</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    @php
    $anggaranCount = $jenisAnggaranCount->where('id',$j->id)->pluck('anggaranCount')->values()->first();
    @endphp
    @if($anggaranCount > 0)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-brand-green">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('saldo.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-secondary">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary disabled"role="button" aria-disabled="true">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
    @endforeach
</div>
@endif

@if($jenisAktif)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">{{ $isKso ? 'Tahun Pelajaran' : 'Tahun' }}</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                    @if($isKso)
                      @foreach($tahunPelajaran as $t)
                      <option value="{{ $t->academicYearLink }}" {{ $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                    @else
                      @if(count($apby) > 0)
                      @php
                      $years = $apby->sortBy('year')->pluck('year')->unique()->all();
                      @endphp
                      @foreach($years as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : ''}}>{{ $y }}</option>
                      @endforeach
                      @else
                      @if($tahun != date('Y'))
                      <option value="" disabled="disabled" selected>Pilih</option>
                      @endif
                      <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                    @endif
                    </select>
                    <a href="{{ route('saldo.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('saldo.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
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
                <h6 class="m-0 font-weight-bold text-brand-green">Detail Anggaran</h6>
            </div>
            <div class="card-body p-3">
                @if($apby && count($apby) > 0)
                <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Anggaran</th>
                      <th>Sisa Saldo</th>
                      <th style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($apby as $a)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $a->jenisAnggaranAnggaran->anggaran->name }}</td>
                      <td>{{ $a->totalBalanceWithSeparator }}</td>
                      <td>
                        <a href="{{ route('saldo.index', ['jenis' => $jenisAktif->link, 'tahun' => $isKso ? $tahun->academicYearLink : $tahun, 'anggaran' => $a->jenisAnggaranAnggaran->anggaran->link])}}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-eye"></i></a>
                      </td>
                    </tr>
                    @endforeach
                </table>    
                </div>
                @else
                <div class="text-center mx-3 my-5">
                    <h3>Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data anggaran yang ditemukan</h6>
                </div>
                @endif
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
</div>
@endif

<!--Row-->

@endsection

@section('footjs')
<!-- Page level custom scripts -->
@include('template.footjs.keuangan.change-year')
@endsection