@extends('template.main.master')

@section('title')
Program Studi
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Atur Program Studi</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rekrutmen.index') }}">Rekrutmen</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur Program Studi</li>
  </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Program Studi</h6>
                <button type="button" class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#add-form">Tambah <i class="fas fa-plus-circle ml-1"></i></button>
            </div>
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
            @if(count($latar) > 0)
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Program Studi</th>
                            <th>Jumlah Pegawai</th>
                            <th style="width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php $no = 1; @endphp
                      @foreach($latar as $l)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $l->name }}</td>
                            @php
                            $employee_count = $l->pegawai()->where('active_status_id',1)->count();
                            @endphp
                            <td>{{ $employee_count }}</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('programstudi.ubah') }}','{{ $l->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                                @if($employee_count < 1)
                                <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Program Studi', '{{ addslashes(htmlspecialchars($l->name)) }}', '{{ route('programstudi.hapus', ['id' => $l->id]) }}')"><i class="fas fa-trash"></i></a>
                                @else
                                <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                                @endif
                            </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data program studi yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Tambah Program Studi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route('programstudi.simpan') }}" id="latar-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Program Studi</label>
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

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Program Studi</h5>
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

@include('template.modal.konfirmasi_hapus')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@endsection