<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Perjanjian Kerja
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
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="{{ route('spk.ekspor') }}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
            </div>
            <div class="card-body p-3">
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
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($spk as $s)
                    <tr>
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

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
=======
@extends('template.main.master')

@section('title')
Perjanjian Kerja
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
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="{{ route('spk.ekspor') }}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
            </div>
            <div class="card-body p-3">
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
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($spk as $s)
                    <tr>
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

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection