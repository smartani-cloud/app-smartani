@extends('template.main.master')

@section('title')
Penempatan Struktural
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Penempatan Struktural</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('penempatan.index') }}">Penempatan</a></li>
        <li class="breadcrumb-item"><a href="{{ route('struktural.index') }}">Struktural</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $unit->name }}</li>
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
                <h6 class="m-0 font-weight-bold text-brand-purple">Penempatan Struktural</h6>
                <div class="float-right">
                  @if($penempatan && count($penempatan->detail) > 0)
                  <button type="button" class="m-0 btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-all-form">Atur Sekaligus <i class="fas fa-pen ml-1"></i></button>
                  @endif
                  <a class="m-0 btn btn-brand-green-dark btn-sm" href="{{ route('struktural.ekspor',['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                </div>
            </div>
            <div class="card-body p-3">
              @if(Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(Session::has('danger'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ Session::get('danger') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if($penempatan && count($penempatan->show) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIPY</th>
                      <th>TTL</th>
                      <th>Penempatan</th>
                      <th>Status</th>
                      <th style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($penempatan->show as $p)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->pegawai->showPhoto) }}" alt="user-{{ $p->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->pegawai->name }}
                        </a>
                      </td>
                      <td>{{ $p->pegawai->nip }}</td>
                      <td>{{ $p->pegawai->birth_place.', '.date('d-m-Y',strtotime($p->pegawai->birth_date)) }}</td>
                      <td>
                        {{ $p->jabatan->name}}
                      </td>
                      <td>
                        @if($p->acc_status_id == 2 && (strtotime($p->acc_time) >= strtotime($p->updated_at)))
                          <i class="fa fa-lg fa-exclamation-circle text-warning" data-toggle="tooltip" data-original-title="Perlu Direvisi"></i>
                        @elseif($p->acc_status_id == 1)
                          <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($p->accPegawai) ? 'Anda' : $p->accPegawai->name }}<br>{{ date('d M Y H.i.s', strtotime($p->acc_time)) }}"></i>
                        @else
                          @if(!$p->placement_date)
                          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Anda mengisi tanggal penetapan"></i>
                          @else
                          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan {{ $p->accJabatan->name }}"></i>
                          @endif
                        @endif
                      </td>
                      <td>
                        @if($p->acc_status_id == 1)
                        <span class="badge badge-success font-weight-normal">Telah Disetujui</span>
                        @else
                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('struktural.ubah', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}','{{ $p->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data penempatan pegawai struktural yang ditemukan</h6>
              </div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-purple border-0">
        <h5 class="modal-title text-white">Atur Penempatan Struktural</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
              <h5 class="font-weight-light mb-3">Memuat...</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" style="display: none;">
      </div>
    </div>
  </div>
</div>

@include('template.modal.penempatan_ubah_semua')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.modal.post_edit')
@endsection