@extends('template.main.master')

@section('title')
Evaluasi Civitas Auliya
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Evaluasi Civitas Auliya</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('calon.index') }}">Rekrutmen</a></li>
        <li class="breadcrumb-item active" aria-current="page">Evaluasi Civitas Auliya</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              <li class="nav-item">
                <a class="nav-link active" href="{{ route('evaluasi.index', ['status' => 'aktif']) }}">Aktif</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route('evaluasi.index', ['status' => 'selesai']) }}">Selesai</a>
              </li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Evaluasi Pegawai Tidak Tetap</h6>
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
              @if(count($eval) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIPY</th>
                      <th>Masa Kerja</th>
                      <th>Masa Awal SPK</th>
                      <th>Masa Akhir SPK</th>
                      <th>PSC Terakhir</th>
                      <th>PSC Sementara</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($eval as $e)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $e->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($e->pegawai->showPhoto) }}" alt="user-{{ $e->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $e->pegawai->name }}
                        </a>
                      </td>
                      <td>{{ $e->pegawai->nip }}</td>
                      <td>{{ $e->pegawai->yearsOfService }}</td>
                      @php
                        $spk = $e->pegawai->spk()->latest()->first();
                      @endphp
                      <td>{{ $spk && $spk->period_start ? date('Y-m-d', strtotime($spk->period_start)) : '-' }}</td>
                      <td>{{ $spk && $spk->period_end ? date('Y-m-d', strtotime($spk->period_end)) : '-' }}</td>
                      <td>-</td>
                      <td>{{ $e->pscSementara ? $e->pscSementara->name : '-' }}</td>
                      <td>
                        @if($e->education_acc_status_id == 2 && (strtotime($e->education_acc_time) >= strtotime($e->updated_at)))
                          <i class="fa fa-lg fa-exclamation-circle text-warning" data-toggle="tooltip" data-original-title="Perlu Direvisi"></i>
                        @elseif($e->education_acc_status_id == 1)
                          <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($e->accEdukasi) ? 'Anda' : $e->accEdukasi->name }}<br>{{ date('j M Y H.i.s', strtotime($e->education_acc_time)) }}"></i>
                        @else
                          @if(!$e->supervision_result || !$e->interview_result)
                          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Hasil Supervisi dan Interview"></i>
                          @elseif($e->supervision_result && $e->interview_result && !$e->recommend_status_id)
                          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Rekomendasi dari ETM"></i>
                          @elseif($e->supervision_result && $e->interview_result && $e->recommend_status_id)
                          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan ETL"></i>
                          @endif
                        @endif
                      </td>
                      <td>
                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('evaluasi.ubah') }}','{{ $e->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data evaluasi Civitas Auliya yang ditemukan</h6>
              </div>
              @endif
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Atur Hasil Evaluasi</h5>
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

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.modal.post_edit')
@endsection