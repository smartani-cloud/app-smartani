@extends('template.main.master')

@section('title')
Perjanjian Kerja
@endsection

@section('headmeta')
<!-- Bootoast -->
<link href="{{ asset('vendor/bootoast/css/bootoast.min.css') }}" rel="stylesheet">
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Perjanjian Kerja</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Perjanjian Kerja</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Perjanjian Kerja</h6>
                <div class="float-right">
                  @if($counter->value > 0)
                  <button type="button" class="m-0 btn btn-danger btn-sm" data-toggle="modal" data-target="#reset-number">Reset Nomor SPK <i class="fas fa-sync-alt  fa-flip-horizontal ml-1"></i></button>
                  @endif
                  <button type="button" class="m-0 btn btn-warning btn-sm" data-toggle="modal" data-target="#edit-all-form">Atur Sekaligus <i class="fas fa-pen ml-1"></i></button>
                  <a class="m-0 btn btn-brand-green-dark btn-sm" href="{{ route('spk.ekspor') }}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
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
              @if(count($spk) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIPY</th>
                      <th>Status Pegawai</th>
                      <th>Nomor</th>
                      <th>Masa Awal</th>
                      <th>Masa Akhir</th>
                      <th>Sisa Masa Kerja</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($spk as $s)
                    <tr id="tr-{{ $s->id }}">
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $s->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($s->pegawai->showPhoto) }}" alt="user-{{ $s->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $s->employee_name }}
                        </a>
                      </td>
                      <td>{{ $s->pegawai->nip }}</td>
                      <td>{{ $s->employee_status ? $s->employeeStatusAcronym : '-' }}</td>
                      <td>{{ $s->reference_number ? $s->reference_number : '-' }}</td>
                      <td>{{ $s->period_start ? date('Y-m-d', strtotime($s->period_start)) : '-' }}</td>
                      <td>{{ $s->period_end ? date('Y-m-d', strtotime($s->period_end)) : '-' }}</td>
                      <td>{{ $s->period_end ? $s->remainingPeriod : '-' }}</td>
                      <td>
                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('spk.ubah') }}','{{ $s->id }}')"><i class="fas fa-pen"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3 >Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data perjanjian kerja yang ditemukan</h6>
              </div>
              @endif
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Atur Perjanjian Kerja</h5>
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

@include('template.modal.spk_ubah_semua')

@if($counter->value > 0)
@include('template.modal.nomor_spk_reset')
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootoast -->
<script src="{{ asset('vendor/bootoast/js/bootoast.min.js') }}"></script>
<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-datepicker/locales/bootstrap-datepicker.id.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.modal.post_edit')
@endsection