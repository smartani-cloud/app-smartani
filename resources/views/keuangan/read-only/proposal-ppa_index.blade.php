@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
  </ol>
</div>

@if($years && count($years) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.index') }}" id="viewItemForm" method="get">
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="selectYear" class="form-control-label">Tahun{{ $isYear ? null : ' Pelajaran' }}</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select class="form-control @error('year') is-invalid @enderror" name="year" id="selectYear" onchange="if(this.value){ this.form.submit(); }" required="required">
                      @if(!$years || ($years && count($years) < 1))
                      <option value="" selected="selected" disabled="disabled">Belum Ada</option>
                      @endif
                      @if($isYear)
                      @foreach($years as $y)
                      <option value="{{ $y }}" {{ old('year',$year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                      @endforeach
                      @if(!in_array(date('Y'),$years->toArray()))
                      <option value="{{ date('Y') }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                      @else
                      @foreach($years as $y)
                      <option value="{{ $y->academicYearLink }}" {{ $year->id == $y->id ? 'selected' : '' }}>{{ $y->academic_year }}</option>
                      @endforeach
                      @endif
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <input type="hidden" name="status" value="{{ $status }}">
        </form>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              @if(!isset($status) || $status != 'diajukan')
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route.'.index', ['year' => $isYear ? $year : $year->academicYearLink, 'status' => 'menunggu']) }}">Menunggu</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route.'.index', ['year' => $isYear ? $year : $year->academicYearLink, 'status' => 'diajukan']) }}">Diajukan</a>
              </li>
              @else
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route.'.index', ['year' => $isYear ? $year : $year->academicYearLink, 'status' => 'menunggu']) }}">Menunggu</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route.'.index', ['year' => $isYear ? $year : $year->academicYearLink, 'status' => 'diajukan']) }}">Diajukan</a>
              </li>
              @endif
            </ul>
        </div>
    </div>
</div>

@if((!isset($status) || $status != 'diajukan') && (!isset($year) || (($isYear && $year == date('Y')) || (!$isYear && $year->is_finance_year == 1))))
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">Buat Proposal</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.create') }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Nama Proposal</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" id="desc" class="form-control form-control-sm @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" maxlength="100" required="required">
                    <small class="form-text text-muted">Maksimal 100 karakter.</small>
                    @error('title')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Deskripsi</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <textarea id="desc" class="form-control form-control-sm @error('desc') is-invalid @enderror" name="desc" maxlength="180" rows="2">{{ old('desc') }}</textarea>
                    <small class="form-text text-muted">Opsional. Maksimal 180 karakter.</small>
                    @error('desc')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Tambah">
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6>
      </div>
      @if($data && count($data) > 0)
      <div class="card-body">
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
        <div class="table-responsive">
          <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Tanggal</th>
                <th>Nama</th>
                <th>Pengajuan</th>
                <th>Unit</th>
                <th>Jabatan</th>
                <th>Tahap</th>
                <th>Tujuan</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php $no = 1; @endphp
              @foreach($data as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->date ? $d->date : '-'  }}</td>
                <td>{{ $d->title }}</td>
                <td>{{ $isDynamic ? $d->totalValueWithSeparator : $d->totalValueOriWithSeparator }}</td>
                <td>{{ $d->unit->name }}</td>
                <td>{{ $d->jabatan->name }}</td>
                <td>
                  @if(!$d->date)
                  <span class="badge badge-secondary">Draft</span>
                  @else
                  @if(!$d->ppa)
                  <span class="badge badge-info">Diajukan ke PA</span>
                  @else
                  <span class="badge badge-success">Proses PPA</span>
                  @endif
                  @endif
                </td>
                <td>{{ $d->anggaran ? $d->anggaran->accJabatan->name.' - '.$d->anggaran->name : '-' }}</td>
                <td>
                  <a href="{{ route($route.'.detail.show', ['id' => $d->id]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-eye"></i></a>
                  @if($d->pegawai->id == Auth::user()->pegawai->id || ($d->anggaran && ($d->anggaran->acc_position_id == Auth::user()->pegawai->position_id) || ($isAnggotaPa && in_array($d->anggaran->id,$anggarans->pluck('id')->toArray()))))
                  @if($used && $used[$d->id] < 1)
                  @if($d->pegawai->id == Auth::user()->pegawai->id)
                  @if((!isset($status) || $status != 'diajukan') && (!isset($year) || (($isYear && $year == date('Y')) || (!$isYear && $year->is_finance_year == 1))) && (!$d->anggaran || ($d->anggaran && $isPa)))
                  <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.edit') }}','{{ $d->id }}')"><i class="fas fa-pen"></i></a>
                  @endif
                  @endif
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{!! addslashes(htmlspecialchars($d->title)) !!}', '{{ route($route.'.destroy', ['id' => $d->id, 'year' => $isYear ? $year : $year->academicYearLink, 'status' => $status]) }}')"><i class="fas fa-trash"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                  @endif
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @else
      @if(Session::has('success'))
      <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <strong>Sukses!</strong> {{ Session::get('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      @if(Session::has('danger'))
      <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <strong>Gagal!</strong> {{ Session::get('danger') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($active) }} yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
<!--Row-->

@if($data && count($data) > 0)
<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah</h5>
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

@include('template.modal.konfirmasi_hapus')

@endif
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.tooltip')
@if($data && count($data) > 0)
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@endif
@endsection