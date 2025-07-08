@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
<style>
.select2-container .select2-results__option[aria-disabled=true]{
  background-color: #dddfeb!important;
}
</style>
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store') }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Material" class="form-control-label">Bahan Baku</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control @error('material') is-invalid @enderror" name="material" id="select2Material" required="required">
                      @foreach($material as $m)
                      <option value="{{ $m->id }}" {{ old('material') == $m->id ? 'selected' : '' }} data-unit="{{ $m->unit_id }}">{{ Auth::user()->role_id == 3 ? $m->name : $m->nameUnit }}</option>
                      @endforeach
                    </select>
                    @error('material')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Supplier" class="form-control-label">Pemasok</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @php
                    $selectedUnit = $material && count($material) > 0 ? $material->first()->unit_id : null;
                    @endphp
                    <select class="select2 form-control @error('supplier') is-invalid @enderror" name="supplier" id="select2Supplier" required="required">
                      @foreach($supplier as $s)
                      <option value="{{ $s->id }}" {{ old('supplier') == $s->id ? 'selected' : '' }} data-unit="{{ $s->unit_id }}" {{ $selectedUnit && $s->unit_id != $selectedUnit ? 'disabled="disabled"' : null }}>{{ Auth::user()->role_id == 3 ? $s->name : $s->nameUnit }}</option>
                      @endforeach
                    </select>
                    @error('supplier')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">MOQ<i class="fa fa-lg fa-question-circle text-light ml-2" data-toggle="tooltip" data-original-title="Minimum Order Quantity"></i></label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="moq" class="form-control @error('moq') is-invalid @enderror number-separator" name="moq" value="{{ old('moq') ? old('moq') : '0' }}" maxlength="12" required="required">
                    @error('moq')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Harga</label>
                  </div>
                  <div class="col-lg-6 col-md-8 col-12">
                    <input type="text" id="price" class="form-control @error('price') is-invalid @enderror number-separator" name="price" value="{{ old('price') ? old('price') : '0' }}" maxlength="15" required="required">
                    @error('price')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      <input type="submit" class="btn btn-sm btn-primary" value="Tambah">
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ $active }}</h6>
      </div>
      @if(count($data) > 0)
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
                <th style="width: 50px">#</th>
                <th>Bahan Baku</th>
                <th>Pemasok</th>
                <th>MOQ</th>
                <th>Harga</th>
                <th>Stok</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php $no = 1; @endphp
              @foreach($data as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ Auth::user()->role_id == 3 ? $d->material->name : $d->material->nameUnit }}</td>
                <td>{{ Auth::user()->role_id == 3 ? $d->supplier->name : $d->supplier->nameUnit }}</td>
                <td>{{ $d->moqWithSeparator }}</td>
                <td>{{ $d->priceWithSeparator }}</td>
                <td>{{ $d->stockQuantityWithSeparator }}</td>
                <td>
                  @if($d->priceLogs()->count() > 0)
                  <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#log-modal" onclick="viewModal('log-modal','{{ route($route.'.log') }}','{{ $d->id }}')" data-toggle="modal"><i class="fas fa-history"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-history"></i></button>
                  @endif
                  <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.edit') }}','{{ $d->id }}')" data-toggle="modal"><i class="fas fa-pen"></i></a>
                  @if($used && $used[$d->id] < 1)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{!! addslashes(htmlspecialchars($d->name)) !!}', '{{ route($route.'.destroy', ['id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                  @endif
                </td>
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

<div class="modal fade" id="log-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Riwayat Perubahan Harga</h5>
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

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
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

@include('template.modal.delete-confirm')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables-thousands-dot')
@include('template.footjs.global.select2')
@include('template.footjs.global.tooltip')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.post_view')
@include('template.footjs.modal.get_delete')

@if(Auth::user()->role_id != 3)
<script type="text/javascript">
$(document).ready(function(){
    $('select[name="material"]').change(function() {
      var unitSelected = $(this).children("option:selected").attr('data-unit');
      $('select[name="supplier"] option').each(function(){
        $(this).removeAttr('disabled').removeClass('bg-gray-300');
      });
      if(unitSelected){
        console.log(unitSelected);
        $('select[name="supplier"] option[data-unit!='+unitSelected+']').each(function(){
          $(this).attr('disabled','disabled').addClass('bg-gray-300');
        });
      }
    });
});
</script>
@endif
@endsection