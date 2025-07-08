@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
  </ol>
</div>

<!-- Content Row -->
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-purple-dark">{{ $active }}</h6>
        <a class="m-0 float-right btn btn-brand-purple-dark btn-sm" href="{{ route($route.'.create') }}">Tambah <i class="fas fa-plus-circle ml-1"></i></a>
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
                <th style="width: 50px">ID</th>
                <th>Greenhouse</th>
                <th>Tanaman</th>
                <th>Tanggal Semai</th>
                <th>Total Lubang Tanam</th>
                <th>Modal (Rp)</th>
                {{-- <th style="width: 120px">Aksi</th> --}}
              </tr>
            </thead>
            <tbody>
              @foreach($data as $d)
              <tr>
                <td>{{ $d->id_planting_cycle }}</td>
                <td>{{ $d->unit->name }}</td>
                <td>{{ $d->plant->name }}</td>
                <td>{{ $d->seeding_date ? date('Y-m-d',strtotime($d->seeding_date)) : '-' }}</td>
                <td>{{ number_format($d->total_seed_holes, 0, ',', '.')}}</td>
                <td>{{ $d->capital_cost ? number_format($d->capital_cost, 0, ',', '.') : '-' }}</td>
                  
                {{-- <td>
                  <a href="{{ route($route.'.show',['id' => $d->id_planting_cycle]) }}" class="btn btn-sm btn-brand-purple-dark"><i class="fas fa-eye"></i></a>
                  <a href="{{ route($route.'.edit',['id' => $d->id_planting_cycle]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                  @if($used && $used[$d->id] < 1)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{!! addslashes(htmlspecialchars($d->name)) !!}', '{{ route($route.'.destroy',['id' => $d->id_planting_cycle]) }}')"><i class="fas fa-trash"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                  @endif
                </td> --}}
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

@include('template.modal.delete-confirm')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.select2-multiple')
@include('template.footjs.global.tooltip')
@include('template.footjs.modal.get_delete')
@endsection