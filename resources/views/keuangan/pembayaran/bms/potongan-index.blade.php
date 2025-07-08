@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Atur {{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item"><a href="{{ route('bms.index')}}">Biaya Masuk Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>

@if($editable)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0" style="padding-left:20px">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <form action="{{ route($route.'.store') }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Nama</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" id="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required="required"/>
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
                    <label for="percentageOpt" class="form-control-label">Gunakan Persentase?</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="percentageOpt1" name="percentageOpt" class="custom-control-input" value="yes" required="required" checked>
                      <label class="custom-control-label" for="percentageOpt1">Ya</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="percentageOpt2" name="percentageOpt" class="custom-control-input" value="no" required="required">
                      <label class="custom-control-label" for="percentageOpt2">Tidak</label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="percentageRow">
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="form-group">
                  <div class="row mb-3">
                    <div class="col-lg-3 col-md-4 col-12">
                      <label for="normal-input" class="form-control-label">Potongan</label>
                    </div>
                    <div class="col-lg-3 col-md-6 col-6">
                      <div class="input-group">
                        <input type="number" class="input-sm form-control @error('percentage') is-invalid @enderror" name="percentage" value="{{ old('percentage',0) }}" min="0" max="100" step="0.01" required="required"/>
                        <div class="input-group-append">
                        <span class="input-group-text">%</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="nominalRow" style="display: none;">
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="form-group">
                  <div class="row mb-3">
                    <div class="col-lg-3 col-md-4 col-12">
                      <label for="normal-input" class="form-control-label">Potongan</label>
                    </div>
                    <div class="col-lg-6 col-md-8 col-12">
                      <input type="text" id="nominal" class="form-control @error('nominal') is-invalid @enderror number-separator" name="nominal" value="{{ old('nominal',0) }}" maxlength="15">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      <input type="submit" class="btn btn-brand-green-dark" value="Tambah">
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green-dark">{{ $active }}</h6>
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
                <th>Nama</th>
                <th>Potongan</th>
                @if($editable)
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
                <td>{{ $d->percentage ? $d->percentageWithSymbol : $d->nominalWithSeparator }}</td>
                @if($editable)
                <td>
                  <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.edit') }}','{{ $d->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                  @if($used && $used[$d->id] < 1)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{!! addslashes(htmlspecialchars($d->name)) !!}', '{{ route($route.'.destroy', ['id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
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

@endif
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

@if($editable)
<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

@endif
<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@if($editable)
<script>
    $(document).ready(function () {
      $('input[name="percentageOpt"]').on('change',function(){
        var percentageOpt = $(this).val();
        if(percentageOpt == 'yes'){
          $('input[name="percentage"]').prop("required", true);
          $('input[name="nominal"]').prop("required", false);
          $('#percentageRow').show();
          $('#nominalRow').hide();
        }else{
          $('input[name="percentage"]').prop("required", false);
          $('input[name="nominal"]').prop("required", true);
          $('#percentageRow').hide();
          $('#nominalRow').show();
        }
      });
    });
</script>
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@endif
@endsection