@extends('template.main.master')

@section('title')
Pratinjau Akun
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Pratinjau Akun</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('keuangan.pratinjau-akun.index')}}">Pratinjau Akun</a></li>
    <li class="breadcrumb-item"><a href="{{ route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">{{ !$isYear ? $tahun->academic_year : $tahun }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $anggaranAktif->anggaran->name }}</li>
  </ol>
</div>
{{--
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
                        <a href="{{ route('keuangan.pratinjau-akun.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>
--}}
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
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @if($years && count($years) > 0)
                      @foreach($years as $y)
                        <option value="{{ $y }}" {{ $isYear && $tahun == $y ? 'selected' : ''}}>{{ $y }}</option>
                      @endforeach
                      @if(!in_array(date('Y'),$years->toArray()) && $jenisAktif->is_academic_year == 0)
                      <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                      @elseif($isYear || (!$isYear && $jenisAktif->is_academic_year == 0))
                      @if($tahun != date('Y'))
                      <option value="" disabled="disabled" selected>Pilih</option>
                      @endif
                      <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                      @if(((!$academicYears || ($academicYears && count($academicYears) < 1)) && $jenisAktif->is_academic_year == 1) || ($academicYears && count($academicYears) > 0))
                      @foreach($tahunPelajaran as $t)
                      <option value="{{ $t->academicYearLink }}" {{ !$isYear && $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                      @endif
                    </select>
                    <a href="{{ route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('keuangan.pratinjau-akun.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
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

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Pengguna Anggaran</div>
                        <h6 class="mb-0">{{ $anggaranAktif->anggaran->accJabatan->name }}</h6>
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
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
            </div>
            @if($historyAktif && $historyAktif->jenisAnggaranAnggaran->akun()->count() > 0)
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th style="white-space: nowrap">No Akun</th>
                            <th>Nama Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $parent = null;
                        $categoryCount = $historyAktif->jenisAnggaranAnggaran->akun()->has('kategori')->with('kategori')->get()->pluck('kategori')->unique()->count();
                        @endphp
                        @foreach($kategori as $k)
                        @php
                        $i = 1;
                        $historyDetail = $historyAktif->jenisAnggaranAnggaran->akun()->wherehas('kategori',function($q)use($k){$q->where('name',$k->name);});
                        @endphp
                        @if($historyDetail->count() > 0)
                        @foreach($historyDetail->get()->sortBy('sort_order')->all() as $a)
                        @if($i == 1 && $categoryCount > 1 && $k->parent && ((($k->parent->name != $parent) && $parent != 'Pembiayaan') || $parent == 'Pembiayaan'))
                        @php $parent = $k->parent->name @endphp
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3" class="font-weight-bold">{{ strtoupper($parent != 'Pembiayaan' ? $parent : $k->name) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>{{ $a->code }}</td>
                            <td>{{ $a->name }}</td>
                        </tr>
                        @php $i++ @endphp
                        @endforeach
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data akun anggaran yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>
@endif

<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@endsection