@extends('template.main.master')

@section('title')
Atur {{ $active }}
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Atur {{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rekrutmen.index') }}">Rekrutmen</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur {{ $active }}</li>
  </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6> 
                @if($operations->where('operation.name','create')->count() > 0)
                <button type="button" class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#add-form">Tambah <i class="fas fa-plus-circle ml-1"></i></button>
                @endif
            </div>
            @if($operations->where('operation.name','create')->count() > 0 || $operations->where('operation.name','update')->count() > 0 || $operations->where('operation.name','delete')->count() > 0)
            @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
              <strong>Sukses!</strong> {{ Session::get('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            @if(Session::has('danger'))
            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
              <strong>Gagal!</strong> {{ Session::get('danger') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            @endif
            @if(count($data) > 0)
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Universitas</th>
                            <th>Jumlah Pegawai</th>
                            @if($operations->where('operation.name','update')->count() > 0 || $operations->where('operation.name','delete')->count() > 0)
                            <th style="width: 120px">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    @php $no = 1; @endphp
                      @foreach($data as $d)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $d->name }}</td>
                            @php
                            $employee_count = $d->pegawai()->where('active_status_id',1)->count();
                            @endphp
                            <td>{{ $employee_count }}</td>
                            @if($operations->where('operation.name','update')->count() > 0 || $operations->where('operation.name','delete')->count() > 0)
                            <td>
                                @if($operations->where('operation.name','update')->count() > 0)
                                <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.ubah') }}','{{ $d->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                                @endif
                                @if($operations->where('operation.name','delete')->count() > 0)
                                @if($employee_count < 1)
                                <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Universitas', '{{ addslashes(htmlspecialchars($d->name)) }}', '{{ route($route.'.hapus', ['id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                                @else
                                <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                                @endif
                                @endif
                            </td>
                            @endif
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data universitas yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

@if($operations->where('operation.name','create')->count() > 0)
<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Tambah Universitas</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.simpan') }}" id="latar-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Universitas</label>
                  </div>
                  <div class="col-12">
                    <textarea id="name" class="form-control" name="name" maxlength="255" rows="3" required="required"></textarea>
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
              <input id="save-academic-background" type="submit" class="btn btn-brand-green-dark" value="Tambah">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@if($operations->where('operation.name','update')->count() > 0)
<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Universitas</h5>
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
@endif

@if($operations->where('operation.name','delete')->count() > 0)
@include('template.modal.konfirmasi_hapus')
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@if($operations->where('operation.name','update')->count() > 0)
@include('template.footjs.modal.post_edit')
@endif
@if($operations->where('operation.name','delete')->count() > 0)
@include('template.footjs.modal.get_delete')
@endif
@endsection