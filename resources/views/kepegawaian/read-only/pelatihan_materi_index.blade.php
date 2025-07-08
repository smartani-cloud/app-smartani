@extends('template.main.master')

@section('title')
Materi Pelatihan
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Pelatihan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pelatihan.saya.index') }}">Pelatihan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Materi</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <form action="{{ route('pelatihan.materi.index') }}" method="get">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="academicYearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Tahun" name="tahunajaran" class="form-control" id="academicYearOpt" onchange="if(this.value){ this.form.submit(); }" required="required">
                      @foreach($tahun as $t)
                      @if($t->is_active == 1 || $t->pelatihan()->count() > 0)
                      <option value="{{ $t->academicYearLink }}" {{ $aktif->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endif
                      @endforeach
                    </select>
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
            <ul class="nav nav-pills p-3">
              <li class="nav-item">
                <a class="nav-link active" href="{{ route('pelatihan.materi.index', ['tahunajaran' => $aktif->academicYearLink, 'status' => 'aktif']) }}">Aktif</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route('pelatihan.materi.index', ['tahunajaran' => $aktif->academicYearLink, 'status' => 'selesai']) }}">Selesai</a>
              </li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Kurikulum Materi Pelatihan</h6>
            </div>
            <div class="card-body p-3">
              @if(count($pelatihan) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Materi</th>
                      <th>Deskripsi</th>
                      <th>Semester</th>
                      <th>Sifat</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($pelatihan as $p)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('pelatihan.materi.detail', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                          {{ $p->name }}
                        </a>
                      </td>
                      <td>{{ $p->desc }}</td>
                      <td>
                        @if($p->semester->semesterNumber == 1)
                        <span class="badge badge-pill badge-success">{{ $p->semester->semester }}</span>
                        @elseif($p->semester->semesterNumber == 2)
                        <span class="badge badge-pill badge-warning">{{ $p->semester->semester }}</span>
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($p->status->status == 'wajib')
                        <span class="badge badge-pill badge-primary">{{ ucwords($p->status->status) }}</span>
                        @elseif($p->status->status == 'pilihan')
                        <span class="badge badge-pill badge-secondary">{{ ucwords($p->status->status) }}</span>
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($p->education_acc_status_id == 1)
                        <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($p->accEdukasi) ? 'Anda' : $p->accEdukasi->name }}<br>{{ date('j M Y H.i.s', strtotime($p->education_acc_time)) }}"></i>
                        @else
                        <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan ETL"></i>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('pelatihan.materi.detail', ['id' => $p->id]) }}" class="btn btn-sm btn-brand-green-dark" target="_blank"><i class="fas fa-eye"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data kurikulum materi yang ditemukan</h6>
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

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.select2-multiple')
@endsection