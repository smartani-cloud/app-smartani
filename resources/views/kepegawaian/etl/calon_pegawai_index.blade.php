@extends('template.main.master')

@section('title')
Calon Pegawai
@endsection

@section('headmeta')
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<style>
.select2-container .select2-results__option[aria-disabled=true] {
  background-color: #dddfeb!important;
}
</style>
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Calon Pegawai</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">Calon Pegawai</li>
  </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Calon Pegawai Lulus Seleksi</h6>
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
              @if(count($calon) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIK</th>
                      <th>Tempat Lahir</th>
                      <th>Tanggal Lahir</th>
                      <th>Rekomendasi</th>
                      <th>Status</th>
                      <th style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($calon as $c)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('calon.detail', ['id' => $c->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($c->showPhoto) }}" alt="user-{{ $c->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $c->name }}
                        </a>
                      </td>
                      <td>{{ $c->nik }}</td>
                      <td>{{ $c->birth_place }}</td>
                      <td>{{ date('Y-m-d',strtotime($c->birth_date)) }}</td>
                      <td>
                        @if($c->rekomendasiPenerimaan->status == 'diterima')
                          <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-original-title="{{ ucwords($c->rekomendasiPenerimaan->status) }}"></i>
                        @elseif($c->rekomendasiPenerimaan->status == 'tidak diterima')
                          <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="{{ ucwords($c->rekomendasiPenerimaan->status) }}"></i>
                        @endif
                      </td>
                      <td>
                        @if($c->education_acc_status_id == 1)
                        <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($c->accEdukasi) ? 'Anda' : $c->accEdukasi->name }}<br>{{ date('j M Y H.i.s', strtotime($c->education_acc_time)) }}"></i>
                        @else
                        <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan Anda"></i>
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('calon.detail', ['id' => $c->id]) }}" class="btn btn-sm btn-brand-green-dark" target="_blank"><i class="fas fa-eye"></i></a>
                        @if(!$c->education_acc_status_id)
                        @php
                        $unitPenempatan = null;
                        $countUnits = $c->units()->count();
                        if($countUnits > 0){
                          $i = $countUnits;
                          foreach($c->units()->orderBy('unit_id')->pluck('name') as $u){
                            if($i > 2){
                              $unitPenempatan .= $u.', ';
                            }
                            elseif($i > 1){
                              if($countUnits > 2){
                                $unitPenempatan .= $u.', dan ';
                              }
                              elseif($countUnits == 2){
                                $unitPenempatan .= $u.' dan ';
                              }
                            }
                            else{
                              $unitPenempatan .= $u;
                            }
                            $i--;
                          }
                        }
                        else $unitPenempatan = '-';
                        @endphp
                        <a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#validate-confirm" onclick="validateModal('{{ addslashes(htmlspecialchars($c->name)) }}', '{{ ucwords($c->rekomendasiPenerimaan->status) }}', '{{ $unitPenempatan }}', '{{ $c->jabatans()->count() > 0 ? implode(', ',$c->jabatans()->with('jabatan')->get()->sortBy('jabatan.id')->pluck('name')->toArray()) : '-' }}', '{{ $c->statusPegawai ? $c->statusPegawai->status : '-' }}', '{{ $c->period_start && $c->period_end ? $c->periodId : '-' }}', '{{ route('calon.validasi', ['id' => $c->id]) }}')"><i class="fas fa-check"></i></a>
                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('calon.ubah.etl') }}','{{ $c->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
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
                <h6 class="font-weight-light mb-3">Tidak ada data calon pegawai yang ditemukan</h6>
              </div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

@include('template.modal.calon_pegawai_validasi')

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Calon Pegawai</h5>
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

<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-datepicker/locales/bootstrap-datepicker.id.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_candidate_employee_validate')
@endsection