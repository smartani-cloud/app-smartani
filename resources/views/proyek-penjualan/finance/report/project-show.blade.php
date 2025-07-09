<<<<<<< HEAD
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
Detail {{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('finance.index') }}">Keuangan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('report.index') }}">Laporan Keuangan</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $data->id }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body p-3">
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Divisi</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->unit ? $data->unit->name : '-' }}
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
                  <label class="form-control-label">Bulan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->month ? $data->monthId : '-' }}
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
                  <label class="form-control-label">Tahun</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->year ? $data->year : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('proposal.show',['id' => $data->id]) }}" class="btn btn-sm btn-primary mr-2"><i class="fas fa-eye mr-2"></i>Lihat Proposal</a>
          <a href="{{ route($route.'.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow">
      <div class="card-body">
        <ul class="nav nav-pills">
          @foreach($reports as $r)
          @php
          $navLink = route($route.'.show',['id' => $data->id, 'report' => $r]);
          @endphp
          <li class="nav-item">
            <a class="nav-link {{ $report == $r ? 'active' : '' }}" href="{{ $report == $r ? 'javascript:void(0)' : $navLink }}">{{ ucwords($r) }}</a>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>

@yield('card')
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.tooltip')
=======
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
Detail {{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('finance.index') }}">Keuangan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('report.index') }}">Laporan Keuangan</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $data->id }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body p-3">
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Divisi</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->unit ? $data->unit->name : '-' }}
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
                  <label class="form-control-label">Bulan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->month ? $data->monthId : '-' }}
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
                  <label class="form-control-label">Tahun</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->year ? $data->year : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('proposal.show',['id' => $data->id]) }}" class="btn btn-sm btn-primary mr-2"><i class="fas fa-eye mr-2"></i>Lihat Proposal</a>
          <a href="{{ route($route.'.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow">
      <div class="card-body">
        <ul class="nav nav-pills">
          @foreach($reports as $r)
          @php
          $navLink = route($route.'.show',['id' => $data->id, 'report' => $r]);
          @endphp
          <li class="nav-item">
            <a class="nav-link {{ $report == $r ? 'active' : '' }}" href="{{ $report == $r ? 'javascript:void(0)' : $navLink }}">{{ ucwords($r) }}</a>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>

@yield('card')
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.tooltip')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection