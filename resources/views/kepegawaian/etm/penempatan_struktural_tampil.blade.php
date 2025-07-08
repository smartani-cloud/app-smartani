@extends('template.main.master')

@section('title')
Penempatan Struktural
@endsection

@section('headmeta')
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
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
                <h6 class="m-0 font-weight-bold text-brand-green">Penempatan Struktural</h6>
                @if(($penempatan && count($penempatan->arsip) < 1) || !$penempatan)
                <button type="button" class="float-right m-0 btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#add-form">Tambah <i class="fas fa-plus-circle ml-1"></i></button>
                @endif
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
                      <th>Masa Awal</th>
                      <th>Masa Akhir</th>
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
                      <td>{{ $p->jabatan->name }}</td>
                      <td>{{ $p->period_start }}</td>
                      <td>{{ $p->period_end }}</td>
                      <td>
                        @if($p->acc_status_id == 2 && (strtotime($p->acc_time) >= strtotime($p->updated_at)))
                          <i class="fa fa-lg fa-exclamation-circle text-warning" data-toggle="tooltip" data-original-title="Perlu Direvisi"></i>
                        @elseif($p->acc_status_id == 1)
                          <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($p->accPegawai) ? 'Anda' : $p->accPegawai->name }}<br>{{ date('d M Y H.i.s', strtotime($p->acc_time)) }}"></i>
                        @else
                          @if(!$p->placement_date)
                          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Administration Supervisor mengisi tanggal penetapan"></i>
                          @else
                          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan {{ $p->accJabatan->name }}"></i>
                          @endif
                        @endif
                      </td>
                      <td>
                        @if($p->acc_status_id == 1)
						@if(Auth::user()->role->name == 'etl')
						<a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Penempatan Pegawai', '{{ addslashes(htmlspecialchars($p->pegawai->name)) }}', '{{ route('struktural.hapus', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name, 'id' => $p->id]) }}')"><i class="fas fa-trash"></i></a>
						@else
                        <span class="badge badge-success font-weight-normal">Telah Disetujui</span>
						@endif
                        @else
                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('struktural.ubah', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}','{{ $p->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                        <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Penempatan Pegawai', '{{ addslashes(htmlspecialchars($p->pegawai->name)) }}', '{{ route('struktural.hapus', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name, 'id' => $p->id]) }}')"><i class="fas fa-trash"></i></a>
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

<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Tambah Penempatan Struktural</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route('struktural.simpan', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}" id="penempatan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row mb-2">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="select2Employee" class="form-control-label">Pegawai <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-12">
                    <select class="select2 form-control" name="employee" id="select2Employee" required="required">
                      @foreach($pegawai as $p)
                      @php
                      $check_employee = 0;
                      @endphp
                      <!-- if($penempatan && count($penempatan->show) > 0){
                        $check_employee = $penempatan->show->where('employee_id',$p->id)->count();
                      } -->
                      @if($check_employee < 1)
                      <option value="{{ $p->nip }}" {{ old('employee') == $p->nip ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endif
                      @endforeach
                    </select>
                    @error('employee')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="selectPosition" class="form-control-label">Penempatan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-12">
                    <select class="form-control" name="position" id="selectPosition" required="required">
                      <option value="" {{ old('position') ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
                      @foreach($jabatan as $j )
                      <option value="{{ $j->id }}" {{ old('position') == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                      @endforeach
                    </select>
                    @error('position')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Masa Penempatan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-12">
                    <div class="input-daterange input-group">
                      <input type="text" class="input-sm form-control" name="period_start" placeholder="Awal" value="{{ old('period_start') }}" required="required"/>
                      <div class="input-group-prepend">
                        <span class="input-group-text">-</span>
                      </div>
                      <input type="text" class="input-sm form-control" name="period_end" placeholder="Akhir" value="{{ old('period_end') }}" required="required"/>
                    </div>
                  </div>
                  <div class="col-12 mt-3 mb-0">
                    @if($errors->any())
                    <div class="alert alert-danger">
                      <ul class="mb-0" style="padding-inline-start:25px">
                        @if($errors->first('period_start'))
                        <li>{{ $errors->first('period_start') }}</li>
                        @elseif($errors->first('period_end'))
                        <li>{{ $errors->first('period_end') }}</li>
                        @endif
                      </ul>
                    </div>
                    @endif
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
              <input type="submit" class="btn btn-brand-green-dark" value="Tambah">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Penempatan Struktural</h5>
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

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.kepegawaian.select2')
@include('template.footjs.modal.get_delete')
@include('template.footjs.modal.post_edit')
@endsection