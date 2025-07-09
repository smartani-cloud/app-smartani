<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Laporan Prestasi Kerja
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
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
    <h1 class="h3 mb-0 text-gray-800">Laporan Prestasi Kerja</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index') }}">Laporan Prestasi Kerja</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]) }}">{{ $tahun->academic_year }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $unitAktif->name }}</li>
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
                          <i class="fas fa-school text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Unit</div>
                        <h6 class="mb-0">{{ $unitAktif->name }}</h6>
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
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Pegawai</h6>
                @if($nilai && count($nilai) > 0)
                <div class="m-0 float-right">
                  <a href="{{ route('psc.laporan.pegawai.ekspor', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                </div>
                @endif
            </div>
            <div class="card-body p-3">
              @if($nilai && count($nilai) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIPY</th>
                      <th>Jabatan</th>
                      <th>Jumlah Nilai</th>
                      <th>Grade</th>
                      <th style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($nilai as $n)
                    @php $p = $n->pegawai; @endphp
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        @if(in_array(Auth::user()->role->name,['admin','kepsek','wakasek','pembinayys','ketuayys','direktur','etl','etm','fam','faspv','am','aspv']))
                        <a href="{{ route('pegawai.detail', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->name }}
                        </a>
                        @else
                        <div class="avatar-small d-inline-block">
                          <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                        </div>
                        {{ $p->name }}
                        @endif
                      </td>
                      <td>{{ $p->nip }}</td>
                      <td>{{ $n->jabatan->name }}</td>
                      <td>{{ $n->total_score }}</td>
                      <td>{{ $n->grade_name }}</td>
                      <td>
                        <a href="{{ route('psc.laporan.pegawai.show', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'pegawai' => $p->nip]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('psc.laporan.pegawai.unduh', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'pegawai' => $p->nip]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-file-download" style="padding: 0 2.75px 0 2.75px"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data pegawai yang ditemukan</h6>
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

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.keuangan.change-year')
=======
@extends('template.main.master')

@section('title')
Laporan Prestasi Kerja
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
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
    <h1 class="h3 mb-0 text-gray-800">Laporan Prestasi Kerja</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index') }}">Laporan Prestasi Kerja</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]) }}">{{ $tahun->academic_year }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $unitAktif->name }}</li>
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
                          <i class="fas fa-school text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Unit</div>
                        <h6 class="mb-0">{{ $unitAktif->name }}</h6>
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
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Pegawai</h6>
                @if($nilai && count($nilai) > 0)
                <div class="m-0 float-right">
                  <a href="{{ route('psc.laporan.pegawai.ekspor', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                </div>
                @endif
            </div>
            <div class="card-body p-3">
              @if($nilai && count($nilai) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIPY</th>
                      <th>Jabatan</th>
                      <th>Jumlah Nilai</th>
                      <th>Grade</th>
                      <th style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($nilai as $n)
                    @php $p = $n->pegawai; @endphp
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        @if(in_array(Auth::user()->role->name,['admin','kepsek','wakasek','pembinayys','ketuayys','direktur','etl','etm','fam','faspv','am','aspv']))
                        <a href="{{ route('pegawai.detail', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->name }}
                        </a>
                        @else
                        <div class="avatar-small d-inline-block">
                          <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                        </div>
                        {{ $p->name }}
                        @endif
                      </td>
                      <td>{{ $p->nip }}</td>
                      <td>{{ $n->jabatan->name }}</td>
                      <td>{{ $n->total_score }}</td>
                      <td>{{ $n->grade_name }}</td>
                      <td>
                        <a href="{{ route('psc.laporan.pegawai.show', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'pegawai' => $p->nip]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('psc.laporan.pegawai.unduh', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'pegawai' => $p->nip]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-file-download" style="padding: 0 2.75px 0 2.75px"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data pegawai yang ditemukan</h6>
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

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.keuangan.change-year')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection