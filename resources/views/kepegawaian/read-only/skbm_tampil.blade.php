@extends('template.main.master')

@section('title')
SKBM
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">SKBM</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('skbm.index') }}">SKBM</a></li>
        <li class="breadcrumb-item active" aria-current="page">Lihat</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row mb-2">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label for="academicYearOpt" class="form-control-label">Tahun Pelajaran</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $aktif->academic_year }}
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
                  <label for="academicYearOpt" class="form-control-label">Unit</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $unit->name }}
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
              <h6 class="m-0 font-weight-bold text-brand-green">Pembagian Tugas Mengajar Guru</h6>
            </div>
            <div class="card-body p-3">
              @if($skbm && count($skbm->show) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Struktur/Guru Mapel</th>
                      <th>Mata Pelajaran</th>
                      <th>Nama</th>
                      <th>Jumlah Siswa Per Rombel</th>
                      <th>Beban Jam Mengajar</th>
                      <th>Tanggal SK Mengajar</th>
                      <th>SK Mengajar</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($skbm->show as $s)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $s->jabatan->name }}</td>
                      <td>{{ $s->mataPelajaran ? $s->mataPelajaran->subject_name : '-' }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $s->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($s->pegawai->showPhoto) }}" alt="user-{{ $s->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $s->pegawai->name }}
                        </a>
                      </td>
                      <td>{{ $s->students ? $s->students : '-' }}</td>
                      <td>{{ $s->teaching_load ? $s->teaching_load : '-' }}</td>
                      <td>{{ $s->teaching_decree_date ? $s->teachingDecreeDateId : '-' }}</td>
                      <td>{{ $s->teaching_decree_number ? $s->teaching_decree_number : '-' }}</td>
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
            <div class="card-footer"></div>
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

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@endsection