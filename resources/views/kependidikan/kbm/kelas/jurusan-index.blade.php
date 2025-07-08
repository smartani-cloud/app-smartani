@extends('template.main.master')

@section('title')
Atur {{ $active }}
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Atur {{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('daftar-kelas.index') }}">Kelas</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur {{ $active }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6>
        @if($editable)
        <button type="button" class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#add-form">Tambah <i class="fas fa-plus-circle ml-1"></i></button>
        @endif
      </div>
      @if($editable)
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
     <div class="card-body">
     @if(count($datas) > 0)
     <div class="table-responsive">
      <table class="table align-items-center table-flush">
        <thead class="thead-light">
          <tr>
            <th style="width: 50px">#</th>
            <th>Jurusan</th>
            <th>Jumlah Kelas Aktif</th>
            @if($editable)
            <th style="width: 120px">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @php $no = 1; @endphp
          @foreach($datas as $d)
          <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $d->major_name }}</td>
            <td>{{ $classCount && $classCount[$d->id] ? $classCount[$d->id] : 0 }}</td>            
            @if($editable)
            <td>
              <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.edit') }}','{{ $d->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
              @if($used && $used[$d->id] < 1)
              <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{{ addslashes(htmlspecialchars($d->major_name)) }}', '{{ route($route.'.destroy', ['id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
              @else
              <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
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
      <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($active) }} yang ditemukan</h6>
    </div>
     @endif
     </div>
    <div class="card-footer"></div>
  </div>
</div>
</div>
<!--Row-->

@if($editable)
<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="addodalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Tambah {{ $active }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.store') }}" id="pendidikan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">{{ $active }}</label>
                  </div>
                  <div class="col-12">
                    <input id="name" class="form-control" name="name" maxlength="50" placeholder="mis. IPA, IPS" required="required">
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
              <input type="submit" class="btn btn-brand-green-dark" value="Tambah">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah {{ $active }}</h5>
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

@endif
@endsection

@section('footjs')
@if($editable)
<!-- Plugins and scripts required by this view-->
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@endif
@endsection