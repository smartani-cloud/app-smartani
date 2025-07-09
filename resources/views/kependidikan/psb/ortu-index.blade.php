<<<<<<< HEAD
@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@if($editable)
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
<style>
.select2-container .select2-results__option[aria-disabled=true]{
  background-color: #dddfeb!important;
}
</style>
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penerimaan Siswa Baru</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green-dark">{{ $active }}</h6>
        @if($count > 0 && in_array(auth()->user()->role->name,['cspv','aspv']))
        <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="{{ route($route.'.export') }}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
        @endif
      </div>
      @if($count > 0)
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
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <div class="table-responsive">
          <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>ID</th>
                <th>Nama Ayah</th>
                <th>Nama Ibu</th>
                <th>Nama Wali</th>
                <th>No. Seluler Ayah</th>
                <th>No. Seluler Ibu</th>
                <th>Username</th>
                <th>Nama Anak</th>
                <th>Civitas</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
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

@if($count > 0)
@if($editable)
<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah</h5>
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

<div class="modal fade" id="delete-check-confirm" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box">
          <i class="material-icons">&#xE5CD;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <form action="#" id="delete-check-link" method="post">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <div class="modal-body p-1">
          <div class="mb-3">
            Apakah Anda yakin ingin menghapus <span class="item font-weight-bold"></span> dari daftar <span class="title text-lowercase"></span>?
          </div>
          <div class="form-group form-check">
            <input type="checkbox" name="parent" class="form-check-input" id="parentCheck">
            <label class="form-check-label" for="parentCheck">Hapus juga data orang tua</label>
          </div>
        </div>

        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('template.modal.konfirmasi_reset_sandi')

@endif
@endif
@endsection

@section('footjs')
@if($count > 0)
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
<script>
$(document).ready(function(){
  var uri = window.location.href;
  $('#dataTable').DataTable({
    order: [[1, 'desc']],
    processing: true,
    serverSide: true,
    ajax: uri,
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'id', name: 'id'},
        {data: 'father_name', name: 'father_name'},
        {data: 'mother_name', name: 'mother_name'},
        {data: 'guardian_name', name: 'guardian_name'},
        {data: 'father_phone', name: 'father_phone'},
        {data: 'mother_phone', name: 'mother_phone'},
        {data: 'username', name: 'username'},
        {data: 'childrens', name: 'childrens'},
        {
          data: 'employee',
          name: 'employee',
          render: function (data, type) {
            if (type === 'display') {
              let icon = 'times';
              let color = 'danger';
              if(data == 'Y'){
                icon = 'check';
                color = 'success';
              }

              return '<i class="fa fa-lg fa-'+icon+'-circle text-'+color+'"></i>';
            }
            return data;
          },
          searchable: false
        },
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
  });
});
</script>
@if($editable)
@if(in_array(Auth::user()->role->name,['sek','aspv']))
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_reset_password')
@endif
@if(in_array(Auth::user()->role->name,['am','aspv']))
@include('template.footjs.modal.get_delete')
<script>
    function deleteCheckModal(title, item, delete_url)
    {
      $('#delete-check-confirm').modal('show', {backdrop: 'static', keyboard :false});
      $("#delete-check-confirm .title").text(title);
      $("#delete-check-confirm .item").text(item);
      $('#delete-check-link').attr("action" , delete_url);
      $('input[name="parent"]').prop('checked', false);
    }
</script>
@endif
@endif
@endif
=======
@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@if($editable)
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
<style>
.select2-container .select2-results__option[aria-disabled=true]{
  background-color: #dddfeb!important;
}
</style>
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penerimaan Siswa Baru</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green-dark">{{ $active }}</h6>
        @if($count > 0 && in_array(auth()->user()->role->name,['cspv','aspv']))
        <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="{{ route($route.'.export') }}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
        @endif
      </div>
      @if($count > 0)
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
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <div class="table-responsive">
          <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>ID</th>
                <th>Nama Ayah</th>
                <th>Nama Ibu</th>
                <th>Nama Wali</th>
                <th>No. Seluler Ayah</th>
                <th>No. Seluler Ibu</th>
                <th>Username</th>
                <th>Nama Anak</th>
                <th>Civitas</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
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

@if($count > 0)
@if($editable)
<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah</h5>
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

<div class="modal fade" id="delete-check-confirm" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box">
          <i class="material-icons">&#xE5CD;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <form action="#" id="delete-check-link" method="post">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
        <div class="modal-body p-1">
          <div class="mb-3">
            Apakah Anda yakin ingin menghapus <span class="item font-weight-bold"></span> dari daftar <span class="title text-lowercase"></span>?
          </div>
          <div class="form-group form-check">
            <input type="checkbox" name="parent" class="form-check-input" id="parentCheck">
            <label class="form-check-label" for="parentCheck">Hapus juga data orang tua</label>
          </div>
        </div>

        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
            <button type="submit" class="btn btn-danger">Ya, Hapus</button>
        </div>
      </form>
    </div>
  </div>
</div>

@include('template.modal.konfirmasi_reset_sandi')

@endif
@endif
@endsection

@section('footjs')
@if($count > 0)
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
<script>
$(document).ready(function(){
  var uri = window.location.href;
  $('#dataTable').DataTable({
    order: [[1, 'desc']],
    processing: true,
    serverSide: true,
    ajax: uri,
    columns: [
        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
        {data: 'id', name: 'id'},
        {data: 'father_name', name: 'father_name'},
        {data: 'mother_name', name: 'mother_name'},
        {data: 'guardian_name', name: 'guardian_name'},
        {data: 'father_phone', name: 'father_phone'},
        {data: 'mother_phone', name: 'mother_phone'},
        {data: 'username', name: 'username'},
        {data: 'childrens', name: 'childrens'},
        {
          data: 'employee',
          name: 'employee',
          render: function (data, type) {
            if (type === 'display') {
              let icon = 'times';
              let color = 'danger';
              if(data == 'Y'){
                icon = 'check';
                color = 'success';
              }

              return '<i class="fa fa-lg fa-'+icon+'-circle text-'+color+'"></i>';
            }
            return data;
          },
          searchable: false
        },
        {data: 'action', name: 'action', orderable: false, searchable: false},
    ]
  });
});
</script>
@if($editable)
@if(in_array(Auth::user()->role->name,['sek','aspv']))
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_reset_password')
@endif
@if(in_array(Auth::user()->role->name,['am','aspv']))
@include('template.footjs.modal.get_delete')
<script>
    function deleteCheckModal(title, item, delete_url)
    {
      $('#delete-check-confirm').modal('show', {backdrop: 'static', keyboard :false});
      $("#delete-check-confirm .title").text(title);
      $("#delete-check-confirm .item").text(item);
      $('#delete-check-link').attr("action" , delete_url);
      $('input[name="parent"]').prop('checked', false);
    }
</script>
@endif
@endif
@endif
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection