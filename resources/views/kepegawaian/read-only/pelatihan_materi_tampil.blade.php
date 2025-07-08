@extends('template.main.master')

@section('title')
Kehadiran Pelatihan
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
        <li class="breadcrumb-item"><a href="{{ route('pelatihan.materi.index') }}">Materi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kehadiran</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div id="training-info" class="card">
      <div class="card-body p-3">
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
                  {{ $pelatihan->speaker ? $pelatihan->speaker : '-' }}
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
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $pelatihan->date ? $pelatihan->dateFullId : '-'}}
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
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $pelatihan->place ? $pelatihan->place : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($pelatihan->education_acc_status_id == 1)
<div class="row mb-4">
  @php
  if(count($peserta) > 0){
    $presence = $peserta->where('presence_status_id',1)->count();
    $absence = $peserta->where('presence_status_id',2)->count();
    $percentage = number_format((float)($presence/($presence+$absence))*100, 0, ',', '');
  }
  else{
    $presence = 0;
    $absence = 0;
    $percentage = 0;
  }
  @endphp
  <div class="col-md-4 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-uppercase mb-1">Persentase Kehadiran</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $percentage }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-percentage fa-2x text-warning"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-uppercase mb-1">Peserta Hadir</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $presence }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calendar-check fa-2x text-success"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-4">
    <div class="card h-100">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-uppercase mb-1">Peserta Absen</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $absence }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calendar-times fa-2x text-danger"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">Kehadiran Pelatihan</h6>
      </div>
      <div class="card-body p-3">
        @if(count($peserta) > 0)
        <div class="table-responsive">
          <table id="dataTable" class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th style="width: 15px">#</th>
                <th>Nama</th>
                <th>Unit</th>
                <th>Jabatan</th>
                <th>Kehadiran</th>
              </tr>
            </thead>
            <tbody>
              @php $no = 1; @endphp
              @foreach($peserta as $p)
              <tr id="tr-{{ $p->id }}">
                <td>{{ $no++ }}</td>
                <td>
                  <a href="{{ route('pegawai.detail', ['id' => $p->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                    <div class="avatar-small d-inline-block">
                      <img src="{{ asset($p->pegawai->showPhoto) }}" alt="user-{{ $p->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                    </div>
                    {{ $p->pegawai->name }}
                </td>
                <td>
                  {{ $p->pegawai->units()->count() > 0 ? implode(', ',$p->pegawai->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('name')->intersect($units)->toArray()) : '-' }}
                </td>
                <td>
                  {{ $p->pegawai->units()->count() > 0 ? implode(', ',$p->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->intersect($jabatans)->toArray()) : '' }}
                </td>
                <td>
                  <span class="badge badge-{{ $p->status->status == 'hadir' ? 'success' : 'danger' }} font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('j M Y H.i.s', strtotime($p->education_acc_time)) }}">{{ ucwords($p->status->status) }}</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center mx-3 mt-4 mb-5">
          <h3>Mohon Maaf,</h3>
          <h6 class="font-weight-light mb-3">Data kehadiran peserta pelatihan belum tersedia</h6>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endif
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