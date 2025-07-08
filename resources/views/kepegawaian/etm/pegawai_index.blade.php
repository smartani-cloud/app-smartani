@extends('template.main.master')

@section('title')
Pegawai
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
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Pegawai</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pegawai</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              <li class="nav-item">
                <a class="nav-link active" href="{{ route('pegawai.index', ['status' => 'aktif']) }}">Aktif</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route('pegawai.index', ['status' => 'nonaktif']) }}">Nonaktif</a>
              </li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('pegawai.index') }}" id="filter-form" method="get">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Filter</h6>
            </div>
            <div class="card-body p-3">
              <div class="row">
                <div class="col-lg-10 col-md-12">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-2 col-md-3 col-12">
                        <label for="position" class="form-control-label">Jabatan</label>
                      </div>
                      <div class="col-lg-10 col-md-9 col-12">
                        <select class="select2-multiple form-control" name="jabatan[]" multiple="multiple" id="position">
                          @foreach($jabatan as $j)
                          <option value="{{ $j->id }}" {{ $filterJabatan && count($filterJabatan) > 0 ? ($filterJabatan->contains($j->id) ? 'selected' : '') : '' }}>{{ $j->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-10 col-md-12">
                  <div class="row">
                    <div class="col-lg-10 offset-lg-2 col-md-12">
                      <div class="text-left">
                        <button class="btn btn-sm btn-brand-green-dark" type="submit">Terapkan</button>
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-primary">Pegawai Aktif</h6>
                <!-- <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="#" data-toggle="modal" data-target="#import-modal">Impor <i class="fas fa-file-import ml-1"></i></a> -->
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
              @if(count($pegawai) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIPY</th>
                      <th>Tempat Lahir</th>
                      <th>Tanggal Lahir</th>
                      <th>Unit</th>
                      <th>Jabatan</th>
                      <th>Masa Kerja</th>
                      <th>Status Pegawai</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($pegawai as $p)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->name }}
                        </a>
                        @if($p->statusBaru && $p->statusBaru->status == 'aktif')
                        <span class="badge badge-primary font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('d M Y', strtotime($p->join_date)) }}">Baru</span>
                        @endif
                        @if($p->statusPhk && $p->statusPhk->status == 'aktif')
                        <span class="badge badge-warning font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('d M Y', strtotime($p->disjoin_date)) }}">PHK</span>
                        @endif
                      </td>
                      <td>{{ $p->nip }}</td>
                      <td>{{ $p->birth_place }}</td>
                      <td>{{ date('Y-m-d',strtotime($p->birth_date)) }}</td>
                      <td>{{ $p->units()->count() > 0 ? implode(', ',$p->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('show_name')->toArray()) : '-' }}</td>
                      <td>{{ $p->units()->count() > 0 ? implode(', ',$p->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->toArray()) : '' }}</td>
                      <td>{{ $p->yearsOfService }}</td>
                      <td>{{ $p->statusPegawai->show_name }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->id]) }}" class="btn btn-sm btn-brand-green-dark" target="_blank"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('pegawai.ubah', ['id' => $p->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                        @if(!$p->phk)
                        @if($p->employee_status_id == 1)
                        <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('phk.tambah') }}', '{{ $p->id }}')"><i class="fas fa-times"></i></a>
                        @elseif(in_array($p->employee_status_id,[3,4]))
                        <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-times"></i></button>
                        @endif
                        @endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3 >Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data pegawai aktif yang ditemukan</h6>
              </div>
              @endif
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

<!-- <div class="modal fade" id="import-modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-purple-dark border-0">
        <h5 class="modal-title text-white">Impor Data Pegawai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <form action="{{ route('pegawai.impor') }}" id="import-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <div class="row">
                <div class="col-12">
                  <label for="normal-input" class="form-control-label">Berkas</label>
                </div>
                <div class="col-12">
                  <input type="file" name="excel" class="file d-none" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="fa fa-paperclip"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control @error('excel') is-invalid @enderror" disabled placeholder="Pilih dokumen (.xls/.xlsx)" id="file">
                    <div class="input-group-append">
                      <button type="button" class="browse btn btn-brand-purple-dark">Pilih</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-3">
          <div class="col-6 text-left">
            <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
          </div>
          <div class="col-6 text-right">
            <input type="submit" class="btn btn-brand-purple-dark" value="Impor">
          </div>
        </div>
      </form>
      </div>
    </div>
  </div>
</div> -->

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger border-0">
        <h5 class="modal-title text-white">Ajukan PHK Pegawai</h5>
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
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<!-- Input Filename -->
<script src="{{ asset('js/input-filename.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.kepegawaian.import')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@endsection