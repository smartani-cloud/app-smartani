<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Calon Pegawai
@endsection

@section('headmeta')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
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
                <h6 class="m-0 font-weight-bold text-brand-purple">Calon Pegawai Lulus Seleksi</h6>
            </div>
            <div class="card-body p-3">
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
                        <a href="{{ route('calon.detail', ['id' => $c->id]) }}" class="btn btn-sm btn-brand-purple-dark" target="_blank"><i class="fas fa-eye"></i></a>
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
=======
@extends('template.main.master')

@section('title')
Calon Pegawai
@endsection

@section('headmeta')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
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
                <h6 class="m-0 font-weight-bold text-brand-purple">Calon Pegawai Lulus Seleksi</h6>
            </div>
            <div class="card-body p-3">
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
                        <a href="{{ route('calon.detail', ['id' => $c->id]) }}" class="btn btn-sm btn-brand-purple-dark" target="_blank"><i class="fas fa-eye"></i></a>
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection