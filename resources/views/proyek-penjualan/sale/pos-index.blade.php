<<<<<<< HEAD
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
        <h6 class="m-0 font-weight-bold text-primary">{{ $active }}</h6>
        @if(Auth::user()->role_id == 3)
        @if($isRelated)
        @if($projects && count($projects) > 0)
        <button type="button" class="m-0 float-right btn btn-primary btn-sm" data-toggle="modal" data-target="#add-form">Buat Baru <i class="fas fa-plus-circle ml-1"></i></button>
        @else
        <a href="javascript:void(0)" class="m-0 float-right btn btn-secondary btn-sm disabled">Buat Baru <i class="fas fa-plus-circle ml-1"></i></a>
        @endif
        @else
        <a class="m-0 float-right btn btn-primary btn-sm" href="{{ route($route.'.new') }}" onclick="event.preventDefault(); document.getElementById('new-form').submit();">Buat Baru <i class="fas fa-plus-circle ml-1"></i></a>
        @endif
        @endif
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
        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="table-responsive">
          <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Nomor</th>
                <th>Tanggal Penjualan</th>
                <th>Pembeli</th>
                <th>Total</th>
                <th>Pajak</th>
                <th>Total Bersih</th>
                <th>Status Pembayaran</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php $no = 1; @endphp
              @foreach($data as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->dateId }}</td>
                <td>{{ $d->buyer ? $d->buyer->name : '-' }}</td>
                <td>{{ $d->totalAmountWithSeparator }}</td>
                <td>{{ $d->taxWithSeparator }}</td>
                <td>{{ $d->netTotalAmountWithSeparator }}</td>
                <td>{!! $d->statusBadge !!}</td>
                <td>
                  @if($d->is_active == 0 || $d->paymentType)
                  <a href="{{ route($route.'.show', ['id' => $d->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-eye"></i></button>
                  @endif
                  @if(Auth::user()->role_id == 3)
                  @if($d->is_active == 1 || $inactiveEditable)
                  <a href="{{ route($route.'.edit', ['id' => $d->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-pen"></i></button>
                  @endif
                  @if($d->is_active == 1 && !$d->paymentType && !$d->sale)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{!! addslashes(htmlspecialchars("Penjualan #".$d->name)) !!}', '{{ route($route.'.destroy', ['id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                  @endif
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
      @if($errors->any())
      <div class="alert alert-danger m-3">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
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

@if(Auth::user()->role_id == 3)
@if($isRelated && $projects && count($projects) > 0)
<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Buat {{ $active }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.new') }}" id="add-project-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          @csrf
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="selectProject" class="form-control-label">Proyek</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-file"></i></span>
                      </div>
                      <select aria-label="Proyek" name="project" id="selectProject" title="Proyek" class="form-control @error('project') is-invalid @enderror" required="required">
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ ($data && $data->first() && $data->first()->project_id ? old('project',$data->first()->project_id) : old('project')) == $p->id ? 'selected' : '' }}>{{ $p->name.' (ID: '.$p->id.')' }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>          
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="select2Buyer" class="form-control-label">Pembeli</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                      </div>
                      <select name="buyer" id="select2Buyer" class="select2 form-control @error('buyer') is-invalid @enderror" required="required">
                      </select>
                    </div>
                    <small class="text-muted"><i class="fa fa-info-circle mr-2"></i>Pembeli belum ada pada daftar di atas? <a href="{{ route('buyer.index') }}" class="text-primary" target="_blank">Tambah di sini</a></small>
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
              <input type="submit" class="btn btn-primary" value="Buat">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@else
<form id="new-form" action="{{ route($route.'.new') }}" method="POST" class="d-none">@csrf</form>

@endif

@include('template.modal.delete-confirm')
@endif

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
@include('template.footjs.global.tooltip')
@if(Auth::user()->role_id == 3)
@include('template.footjs.modal.get_delete')
@endif
<script>
$(document).ready(function(){
    $('.select2').select2({
      placeholder: "Pilih salah satu",
      theme: 'bootstrap4',
      allowClear: false
    });
    $("#select2Buyer").select2({
      ajax: {
        url: '{!! route('buyer.get') !!}',
        type: "POST",
        dataType: 'JSON',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        delay: 250,
        data: function (params) {
          return {
            q: params.term, // search term
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      },
      placeholder: "Pilih salah satu",
      theme: 'bootstrap4',
      allowClear: false,
      dropdownParent: $('#add-form')
    });
    $('#select2Buyer').on('change',function(){
        if($(this).val() == ''){
          $(this).val(null).trigger('change');
          $('input[name="no_buyer"]').val(1).prop('required',true).prop('disabled',false);
        }else{
          $('input[name="no_buyer"]').val(0).prop('required',false).prop('disabled',true);
        }
      });
});
</script>
=======
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
        <h6 class="m-0 font-weight-bold text-primary">{{ $active }}</h6>
        @if(Auth::user()->role_id == 3)
        @if($isRelated)
        @if($projects && count($projects) > 0)
        <button type="button" class="m-0 float-right btn btn-primary btn-sm" data-toggle="modal" data-target="#add-form">Buat Baru <i class="fas fa-plus-circle ml-1"></i></button>
        @else
        <a href="javascript:void(0)" class="m-0 float-right btn btn-secondary btn-sm disabled">Buat Baru <i class="fas fa-plus-circle ml-1"></i></a>
        @endif
        @else
        <a class="m-0 float-right btn btn-primary btn-sm" href="{{ route($route.'.new') }}" onclick="event.preventDefault(); document.getElementById('new-form').submit();">Buat Baru <i class="fas fa-plus-circle ml-1"></i></a>
        @endif
        @endif
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
        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="table-responsive">
          <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Nomor</th>
                <th>Tanggal Penjualan</th>
                <th>Pembeli</th>
                <th>Total</th>
                <th>Pajak</th>
                <th>Total Bersih</th>
                <th>Status Pembayaran</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php $no = 1; @endphp
              @foreach($data as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->dateId }}</td>
                <td>{{ $d->buyer ? $d->buyer->name : '-' }}</td>
                <td>{{ $d->totalAmountWithSeparator }}</td>
                <td>{{ $d->taxWithSeparator }}</td>
                <td>{{ $d->netTotalAmountWithSeparator }}</td>
                <td>{!! $d->statusBadge !!}</td>
                <td>
                  @if($d->is_active == 0 || $d->paymentType)
                  <a href="{{ route($route.'.show', ['id' => $d->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-eye"></i></button>
                  @endif
                  @if(Auth::user()->role_id == 3)
                  @if($d->is_active == 1 || $inactiveEditable)
                  <a href="{{ route($route.'.edit', ['id' => $d->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-pen"></i></button>
                  @endif
                  @if($d->is_active == 1 && !$d->paymentType && !$d->sale)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{!! addslashes(htmlspecialchars("Penjualan #".$d->name)) !!}', '{{ route($route.'.destroy', ['id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                  @endif
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
      @if($errors->any())
      <div class="alert alert-danger m-3">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
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

@if(Auth::user()->role_id == 3)
@if($isRelated && $projects && count($projects) > 0)
<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Buat {{ $active }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.new') }}" id="add-project-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          @csrf
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="selectProject" class="form-control-label">Proyek</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-file"></i></span>
                      </div>
                      <select aria-label="Proyek" name="project" id="selectProject" title="Proyek" class="form-control @error('project') is-invalid @enderror" required="required">
                        @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ ($data && $data->first() && $data->first()->project_id ? old('project',$data->first()->project_id) : old('project')) == $p->id ? 'selected' : '' }}>{{ $p->name.' (ID: '.$p->id.')' }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>          
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="select2Buyer" class="form-control-label">Pembeli</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                      </div>
                      <select name="buyer" id="select2Buyer" class="select2 form-control @error('buyer') is-invalid @enderror" required="required">
                      </select>
                    </div>
                    <small class="text-muted"><i class="fa fa-info-circle mr-2"></i>Pembeli belum ada pada daftar di atas? <a href="{{ route('buyer.index') }}" class="text-primary" target="_blank">Tambah di sini</a></small>
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
              <input type="submit" class="btn btn-primary" value="Buat">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@else
<form id="new-form" action="{{ route($route.'.new') }}" method="POST" class="d-none">@csrf</form>

@endif

@include('template.modal.delete-confirm')
@endif

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
@include('template.footjs.global.tooltip')
@if(Auth::user()->role_id == 3)
@include('template.footjs.modal.get_delete')
@endif
<script>
$(document).ready(function(){
    $('.select2').select2({
      placeholder: "Pilih salah satu",
      theme: 'bootstrap4',
      allowClear: false
    });
    $("#select2Buyer").select2({
      ajax: {
        url: '{!! route('buyer.get') !!}',
        type: "POST",
        dataType: 'JSON',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        delay: 250,
        data: function (params) {
          return {
            q: params.term, // search term
          };
        },
        processResults: function (response) {
          return {
            results: response
          };
        },
        cache: true
      },
      placeholder: "Pilih salah satu",
      theme: 'bootstrap4',
      allowClear: false,
      dropdownParent: $('#add-form')
    });
    $('#select2Buyer').on('change',function(){
        if($(this).val() == ''){
          $(this).val(null).trigger('change');
          $('input[name="no_buyer"]').val(1).prop('required',true).prop('disabled',false);
        }else{
          $('input[name="no_buyer"]').val(0).prop('required',false).prop('disabled',true);
        }
      });
});
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection