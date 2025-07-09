@extends('template.main.master')

@section('title')
Kehadiran Pelatihan
@endsection

@section('headmeta')
<!-- Bootoast -->
<link href="{{ asset('vendor/bootoast/css/bootoast.min.css') }}" rel="stylesheet">
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
    <h1 class="h3 mb-0 text-gray-800">Pelatihan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pelatihan.saya.index') }}">Pelatihan</a></li>
        <li class="breadcrumb-item"><a href="{{ route('pelatihan.materi.index') }}">Materi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kehadiran</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div id="training-info" class="card">
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
        <div class="row mb-2">
          <div class="col-lg-8 col-md-10 col-12">
            <h3 class="mb-0 font-weight-bold">{{ $pelatihan->name }}</h3>
            @if($pelatihan->desc)
            <span class="mt-2">{{ $pelatihan->desc }}</span>
            @endif
          </div>
        </div>
        <hr>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  @if($pelatihan->active_status_id == 2)
                  {{ $pelatihan->number }}
                  @else
                  Belum ada<i class="fas fa-question-circle text-secondary ml-1" data-toggle="tooltip" data-original-title="Nomor pelatihan akan otomatis ditambahkan setelah pelatihan selesai"></i>
                  @endif
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
                  <label class="form-control-label">Tahun Pelajaran</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $pelatihan->semester->semester_id.' ('.ucwords($pelatihan->semester->semester).')' }}
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
                  <label class="form-control-label">Penyelenggara</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12 mb-2">
                  {{ $pelatihan->organizer }}
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
                  <label class="form-control-label">Narasumber</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12 mb-2">
                  {{ $pelatihan->narasumber ? $pelatihan->narasumber->name : ($pelatihan->speaker_name ? $pelatihan->speaker_name : '-') }}
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
                  <label class="form-control-label">Sasaran</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12 mb-2">
                  @php
                  $jabatans = $pelatihan->sasaran()->with('jabatan.jabatan')->get()->pluck('jabatan.jabatan')->unique()->pluck('name');
                  $units = $pelatihan->sasaran()->with('jabatan.unit')->get()->pluck('jabatan.unit')->unique()->pluck('name');
                  @endphp
                  @foreach($pelatihan->sasaran as $p)
                  <span class="badge badge-light font-weight-normal">{{ $p->jabatan->name }}</span>
                  @endforeach
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
                  <label class="form-control-label">Hari, Tanggal</label>
                </div>
                @if($pelatihan->date)
                <div id="date-col" class="col-lg-9 col-md-8 col-12" data-date="{{ $pelatihan->date }}">
                  {{ $pelatihan->dateFullId }}<button type="button" data-href="#edit-date-form" class="btn btn-sm btn-light btn-edit ml-2"><i class="fas fa-pen"></i></button>
                </div>
                @else
                <div id="date-col" class="col-lg-9 col-md-8 col-12 mb-2">
                  <button type="button" data-href="#edit-date-form" class="btn btn-sm btn-brand-green-dark btn-edit"><i class="fas fa-plus-circle mr-1"></i>Pilih tanggal</button>
                </div>
                @endif
                <div id="edit-date-form" class="col-md-6 col-12 mb-3" style="display: none">
                  <div class="input-group date">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    </div>
                    <input type="text" name="date" class="form-control" value="{{ $pelatihan->date ? date('d F Y', strtotime($pelatihan->date)) : '' }}" placeholder="Pilih tanggal" id="dateInput">
                  </div>
                  <div class="d-flex justify-content-end mt-2">
                    <button type="button" class="btn btn-sm btn-light btn-cancel mr-2" data-dismiss="#edit-date-form">Kembali</button>
                    <button id="save-date" type="button" class="btn btn-sm btn-brand-green-dark btn-submit">Simpan</button>
                  </div>
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
                  <label class="form-control-label">Tempat</label>
                </div>
                @if($pelatihan->place)
                <div id="place-col" class="col-lg-9 col-md-8 col-12">
                  {{ $pelatihan->place }}<button type="button" data-href="#edit-place-form" class="btn btn-sm btn-light btn-edit ml-2"><i class="fas fa-pen"></i></button>
                </div>
                @else
                <div id="place-col" class="col-lg-9 col-md-8 col-12 mb-2">
                  <button type="button" data-href="#edit-place-form" class="btn btn-sm btn-brand-green-dark btn-edit"><i class="fas fa-plus-circle mr-1"></i>Tambahkan tempat</button>
                </div>
                @endif
                <div id="edit-place-form" class="col-md-8 col-12 mb-3" style="display: none">
                  <input id="place" class="form-control" name="place" value="{{ $pelatihan->place }}" placeholder="Tuliskan tempat pelatihan" maxlength="255">
                  <div class="d-flex justify-content-end mt-2">
                    <button type="button" class="btn btn-sm btn-light btn-cancel mr-2" data-dismiss="#edit-place-form">Kembali</button>
                    <button id="save-place" type="button" class="btn btn-sm btn-brand-green-dark btn-submit">Simpan</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @if($pelatihan->education_acc_status_id == 1 && $pelatihan->active_status_id != 2)
        <div class="d-flex justify-content-end">
          <button type="button" class="btn btn-success btn-end" data-href="{{ route('pelatihan.materi.selesai',['id' => $pelatihan->id]) }}">Selesai</button>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div id="training-presence" class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Kehadiran Pelatihan</h6>
            </div>
            <div class="card-body p-3">
              @if($pelatihan->education_acc_status_id == 1 && count($peserta) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>Unit</th>
                      <th>Jabatan</th>
                      <th>Kehadiran</th>
                      @if($pelatihan->active_status_id != 2)
                      <th>Aksi</th>
                      @endif
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($peserta as $p)
                    <tr id="tr-{{ $p->id }}">
                      @if($pelatihan->active_status_id == 2)
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->pegawai->showPhoto) }}" alt="user-{{ $p->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->pegawai->name }}
                        </a>
                      </td>
                      <td>
                        {{ $p->units()->count() > 0 ? implode(', ',$p->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('name')->intersect($units)->toArray()) : '-' }}
                      </td>
                      <td>
                        {{ $p->units()->count() > 0 ? implode(', ',$p->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->intersect($jabatans)->toArray()) : '' }}
                      </td>
                      <td>
                        <span class="badge badge-{{ $p->status->status == 'hadir' ? 'success' : 'danger' }} font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('j M Y H.i.s', strtotime($p->education_acc_time)) }}">{{ ucwords($p->status->status) }}</span>
                      </td>
                      @else
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->name }}
                      </td>
                      <td>
                        {{ $p->units()->count() > 0 ? implode(', ',$p->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('name')->intersect($units)->toArray()) : '-' }}
                      </td>
                      <td>
                        {{ $p->units()->count() > 0 ? implode(', ',$p->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->intersect($jabatans)->toArray()) : '' }}
                      </td>
                      @php
                      $kehadiran = $p->pelatihan()->where('training_id',$pelatihan->id)->first();
                      @endphp
                      <td>
                        @if(!$kehadiran || ($kehadiran && !$kehadiran->presence_status_id))
                        -
                        @elseif($kehadiran && $kehadiran->status)
                        <span class="badge badge-{{ $kehadiran->status->status == 'hadir' ? 'success' : 'danger' }} font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('j M Y H.i.s', strtotime($kehadiran->education_acc_time)) }}">{{ ucwords($kehadiran->status->status) }}</span>
                        @endif
                      </td>
                      <td>
                        @if(!$kehadiran || ($kehadiran && !$kehadiran->presence_status_id))
                        <button type="button" class="btn btn-sm btn-success btn-check">
                          <i class="fas fa-check"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-times">
                          <i class="fas fa-times"></i>
                        </button>
                        @elseif($kehadiran && $kehadiran->status)
                        <button type="button" class="btn btn-sm btn-light btn-cancel">
                          <i class="fas fa-undo-alt"></i>
                        </button>
                        @endif
                      </td>
                      @endif
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- <div class="text-center mx-3 mt-4 mb-5">
                <span class="mdi mdi-alert-circle-outline mdi-48px text-warning"></span>
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Anda harus merevisi agenda materi pelatihan ini terlebih dahulu</h6>
              </div> -->
              @elseif(!$pelatihan->education_acc_status_id || ($pelatihan->education_acc_status_id == 2))
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Daftar peserta pelatihan belum dapat dimuat sampai ETL menyetujui pelatihan ini.</h6>
                <a href="{{ 'https://api.whatsapp.com/send?phone='.$etl->phoneNumberId.'&text=Assalamualaikum...' }}" class="btn btn-sm btn-success" target="_blank"><i class="fab fa-whatsapp mr-1"></i>Ingatkan ETL via WA</a>
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

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Materi Pelatihan</h5>
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

@if($pelatihan->education_acc_status_id == 1 && $pelatihan->active_status_id != 2)
@include('template.modal.pelatihan_selesai')
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootoast -->
<script src="{{ asset('vendor/bootoast/js/bootoast.min.js') }}"></script>
<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.kepegawaian.edit-training')
@include('template.footjs.kepegawaian.presence')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.modal.post_edit')
@if($pelatihan->education_acc_status_id == 1 && $pelatihan->active_status_id != 2)
@include('template.footjs.modal.post_training_end')
@endif
@endsection