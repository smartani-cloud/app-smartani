<<<<<<< HEAD
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
Ubah {{ $active }}
@endsection

@section('headmeta')
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
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
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.show',['id' => $data->id]) }}">{{ $data->id }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body p-3">
        @if(Session::has('success-date'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success-info') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if(Session::has('danger-info'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ Session::get('danger-info') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->date ? $data->name : '-' }}
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
                  <label class="form-control-label">Tanggal</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->date ? $data->dateId : '-' }}
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
                  <label class="form-control-label">Divisi</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->unit ? $data->unit->name : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @if($data->project)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Proyek</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  <a href="{{ route('proposal.show',['id' => $data->project]) }}" target="_blank" class="text-decoration-none text-info">{{ $data->project->name }}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Status</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->status ? $data->status->name : '-' }}
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
                  <label class="form-control-label">Dibuat</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ date('d M Y H.i', strtotime($data->created_at)) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          @if(in_array($data->type_id,[2,3]))
          <button type="button" class="btn btn-sm btn-warning mr-2" data-toggle="modal" data-target="#edit-date"><i class="fas fa-pen mr-2"></i>Ubah Tanggal</button>
          @endif
          @if($data->type_id == 1)
          <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-date"><i class="fas fa-pen mr-2"></i>Ubah Tanggal</button>
            <button type="button" class="btn btn-sm btn-warning dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              @if($isRelated)
              <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal" data-target="#edit-project">Ubah Proyek</a>
              @endif
            </div>
          </div>
          @endif
          <a href="{{ route($route.'.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Bukti Pengeluaran</h6>
        @if($data->files()->count() > 0)
        <button type="button" class="m-0 float-right btn btn-sm btn-primary" data-toggle="modal" data-target="#upload-modal"><i class="fas fa-sync-alt mr-2"></i>Ganti</a>
        @endif
      </div>
      <div class="card-body px-4 py-3">
        @if(Session::has('success-file'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success-file') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if(Session::has('danger-file'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ Session::get('danger-file') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @error('file')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ $message }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @enderror
        @if($data->files()->count() > 0)
        @php
        $file = $data->files()->latest()->first();
        @endphp
        <div class="d-flex">
          <div>
            <i class="far fa-file-archive text-success mr-2"></i><span>{{ $file->nameExtension }}</span>
          </div>
          <div class="ml-auto">
            <a href="{{ route($route.'.download',['id'=>$data->id]) }}" class="btn btn-sm btn-success"><i class="fas fa-download"></i></a>
          </div>
        </div>
        <div class="d-flex small text-muted">
          <span class="mr-2">{{ $file->updated_at->diffForHumans() }}</span>
          <span>{{ $file->formatSizeUnits }}</span>
        </div>
        @else
        <div class="text-center mx-3 my-3">
          <h4 class="text-center">Belum ada bukti,</h4>
          <h6 class="font-weight-light mb-3">Mohon unggah bukti-bukti pengeluaran dalam satu berkas berformat ZIP, RAR, TAR (maks. 5 MB)</h6>
          <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal" data-target="#upload-modal"><i class="fas fa-upload mr-2"></i>Unggah</button>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@if($data->type_id == 1)
<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow">
      <div class="card-body">
        <ul class="nav nav-pills">
          @php
          $thisStep = 1;
          @endphp
          @foreach($steps as $s)
          @php
          $navLink = route($route.'.edit',['id'=>$data->id,'step' => $thisStep]);
          @endphp
          <li class="nav-item">
            <a class="nav-link {{ $step == $thisStep ? 'active' : '' }}" href="{{ $step == $thisStep ? 'javascript:void(0)' : $navLink }}">{{ $s }}</a>
          </li>
          @php $thisStep++; @endphp
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>
@endif

@if(($data->unit_id == 1 || $data->type_id == 3) && $step == 1)
<div class="row">
  <div class="col-md-{{ $data->type_id == 3 ? '3' : '6' }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Harga Bahan Baku</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-{{ $data->type_id == 3 ? '3' : '6' }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Harga MOQ</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalMoq, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($data->type_id == 3)
  <div class="col-md-3 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Pembayaran</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPaid, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Tagihan</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRemain, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-minus fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@if($data->unit_id == 1)
@if($materials && count($materials) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store',['id'=>$data->id,'step' => $step]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Material" class="form-control-label">{{ $steps[$step-1] }}</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('material') is-invalid @enderror" name="material" id="select2Material" required="required">
                      @foreach($materials as $m)
                      <option value="{{ $m->id }}" {{ old('material') == $m->id ? 'selected' : '' }}>{{ $m->nameWithStock }}</option>
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
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Kuantitas</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="qty" class="form-control form-control-sm @error('qty') is-invalid @enderror number-separator" name="qty" value="{{ old('qty') ? old('qty') : '0' }}" maxlength="12" required="required">
                    @error('qty')
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
@endif
@endif
@elseif(($data->unit_id != 1 && $data->type_id == 1 && $step == 1) || (($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 2)) && $step == 2))
<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Biaya Operasional</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 1))
@if($operationals && count($operationals) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store',['id'=>$data->id,'step' => $step]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Operational" class="form-control-label">{{ $steps[$step-1] }}</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('operational') is-invalid @enderror" name="operational" id="select2Operational" required="required">
                      @foreach($operationals as $o)
                      <option value="{{ $o->id }}" {{ old('operational') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                      @endforeach
                    </select>
                    @error('operational')
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
                    <label for="normal-input" class="form-control-label">Biaya</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror number-separator" name="amount" value="{{ old('amount') ? old('amount') : '0' }}" maxlength="15" required="required">
                    @error('amount')
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
                    <label for="normal-input" class="form-control-label">Deskripsi</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" id="desc" class="form-control form-control-sm @error('desc') is-invalid @enderror" name="desc" value="{{ old('desc') }}" maxlength="100">
                    @error('desc')
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
@endif
@endif
@elseif($data->type_id == 1 && $step == 2 && ($data->sofs()->count() > 0 || $direct))
<div class="row">
  <div class="col-md-6 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Kebutuhan Dana</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($max, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Alokasi Maksimal</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($sofs && count($sofs) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store',['id'=>$data->id,'step' => $step]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2SoF" class="form-control-label">{{ $steps[$step-1] }}</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('sof') is-invalid @enderror" name="sof" id="select2SoF" required="required">
                      @foreach($sofs as $s)
                      <option value="{{ $s->id }}" {{ old('sof') == $s->id ? 'selected' : '' }}>{{ $s->nameWithBalance }}</option>
                      @endforeach
                    </select>
                    @error('sof')
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
                    <label for="normal-input" class="form-control-label">Alokasi</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="nominal" class="form-control form-control-sm @error('nominal') is-invalid @enderror number-separator" name="nominal" value="{{ old('nominal') ? old('nominal') : '0' }}" maxlength="15" required="required">
                    @error('nominal')
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
@endif
@endif

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ $steps[$step-1] }}</h6>
      </div>
      @if((($data->unit_id == 1 || $data->type_id == 3) && $step == 1 && $data->materials()->count() > 0) || ((($data->unit_id != 1 && $data->type_id == 1 && $step == 1) || (($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 2)) && $step == 2)) && $data->operationals()->count() > 0) || ($data->type_id == 1 && $step == 2 && ($data->sofs()->count() > 0 || $direct)))
      <div class="card-body">
        <form action="{{ route($route.'.update',['id'=>$data->id,'step' => $step]) }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
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
        @if(($data->unit_id == 1 || $data->type_id == 3) && $step == 1 && $data->materials()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                @if($data->unit_id == 1)
                <th>Stok</th>
                @endif
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
                <th>MOQ</th>
                <th>Harga MOQ</th>
                @if($data->type_id == 3)
                <th>Terbayar</th>
                <th>Sisa Tagihan</th>
                <th>Status</th>
                @endif
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->materials as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->materialSupplier->name }}</td>
                @if($data->unit_id == 1)
                <td>{{ $d->materialSupplier->stockQuantityWithSeparator }}</td>
                <td>{{ $d->materialSupplier->priceWithSeparator }}</td>
                @else
                <td>{{ $d->priceWithSeparator }}</td>
                @endif
                <td>
                  @if($data->unit_id == 1)
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->quantityProposeWithSeparator }}">
                  @else
                  {{ $d->quantityProposeWithSeparator }}
                  @endif
                </td>
                @if($data->unit_id == 1)
                @php
                $value = $values && $values->where('id',$d->id)->count() > 0 ? $values->where('id',$d->id)->first()['value'] : 0;
                @endphp
                <td>{{ number_format($value, 0, ',', '.') }}</td>
                @php
                $times = $d->quantity_propose-$d->materialSupplier->stock_quantity > 0 ? ceil(($d->quantity_propose-$d->materialSupplier->stock_quantity)/$d->materialSupplier->moq) : 0;
                @endphp
                <td>{{ number_format($times*$d->materialSupplier->moq, 0, ',', '.') }}</td>
                <td>{{ number_format($times*$d->materialSupplier->moqPrice, 0, ',', '.') }}</td>
                @else
                <td>{{ $d->amountWithSeparator }}</td>
                <td>{{ $d->quantityBuyWithSeparator }}</td>
                <td>{{ $d->amountMoqWithSeparator }}</td>
                @if($data->type_id == 3)
                <td>{{ $d->paidWithSeparator }}</td>
                <td>{{ $d->remainWithSeparator }}</td>
                <td>{!! $d->statusBadge !!}</td>
                @endif
                @endif
                <td>
                  @if($data->unit_id == 1)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars($d->materialSupplier->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                  @elseif($data->type_id == 3)
                  <a href="{{ route($route.'.bill', ['id' => $data->id,'material' => $d->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @elseif((($data->unit_id != 1 && $data->type_id == 1 && $step == 1) || (($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 2)) && $step == 2)) && $data->operationals()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                <th>Biaya</th>
                <th>%</th>
                <th>Deskripsi</th>
                @if($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 1))
                <th style="width: 120px">Aksi</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->operationals as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->operational->name }}</td>
                <td>
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->amountWithSeparator }}">
                </td>
                <td>{{ $total && $total > 0 ? number_format(($d->amount/$total)*100, 1, ',', '.') : 0 }}</td>
                <td>
                  <input name="desc-{{ $d->id }}" type="text" class="form-control form-control-sm" value="{{ $d->operational_desc }}">
                </td>
                @if($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 1))
                <td>
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars($d->operational->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @elseif($data->type_id == 1 && $step == 2 && ($data->sofs()->count() > 0 || $direct))
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                <th>Kas</th>
                <th>Alokasi</th>
                <th>Penggunaan</th>
                <th>%</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              $remain = $max;
              @endphp
              @foreach($data->sofs as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->sof->name }}</td>
                <td>{{ $d->sof->balance ? $d->sof->balance->balanceWithSeparator : '-' }}</td>
                <td>
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator allocation" value="{{ $d->nominalWithSeparator }}" max="{{ $d->sof->balance ? $d->sof->balance->balance : '0' }}">
                </td>
                @php
                $used = $d->nominal > $remain ? $remain : $d->nominal;
                $remain -= $used;
                @endphp
                <td>{{ number_format($used, 0, ',', '.') }}</td>
                <td>{{ $max && $max > 0 ? number_format(($used/$max)*100, 1, ',', '.') : 0 }}</td>
                <td>
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars($d->sof->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              @endforeach
              @if($direct)
              <tr>
                <td>{{ $no++ }}</td>
              <td>{{ $direct->name }}</td>
              <td>-</td>
                <td>
                  <input id="directAllocation" name="direct-{{ $direct->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ number_format(max($max-$total,0), 0, ',', '.') }}" disabled="disabled">
                </td>
                <td>{{ number_format($remain, 0, ',', '.') }}</td>
                <td>{{ $max && $max > 0 ? number_format(($remain/$max)*100, 1, ',', '.') : 0 }}</td>
                <td>&nbsp;</td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
        @endif
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              @if($data->unit_id == 1 || ($data->unit_id != 1 && ($data->type_id == 1 && ($step == 1 || ($step == 2 && $data->sofs()->count() > 0))) || ($data->type_id == 2)))
              <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
              @endif
              @if(Auth::user()->role_id == 3 && (($data->unit_id == 1 || ($data->unit_id != 1 && in_array($data->type_id,['2','3']))) && $data->status_id == 1) && ((($data->unit_id == 1 || ($data->type_id == 3 && $totalRemain <= 0)) && $data->materials()->count() > 0) || $data->operationals()->count() > 0) && $data->files()->count() > 0)
              <button type="button" class="btn btn-sm btn-success ml-1" data-toggle="modal" data-target="#finalize-confirm">Finalisasi</button>
              @endif
            </div>
          </div>
        </div>
        </form>
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
        <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($steps[$step-1]) }} yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
<!--Row-->

<div class="modal fade" id="edit-date" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ $step ? route($route.'.update',['id' => $data->id]) : route($route.'.update',['id' => $data->id]) }}" id="edit-date-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Tanggal</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group date end-today">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                      </div>
                      <input type="text" name="date" class="form-control" value="{{ $data->date ? date('d F Y', strtotime($data->date)) : '' }}" placeholder="Pilih tanggal" id="dateInput">
                    </div>
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
              <input id="save-data" type="submit" class="btn btn-primary" value="Simpan">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@if($isRelated)
<div class="modal fade" id="edit-project" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.update',['id' => $data->id]) }}" id="edit-project-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          @csrf          
          {{ method_field('PUT') }}
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
                        <option value="{{ $p->id }}" {{ ($data && $data->project_id ? old('project',$data->project_id) : old('project')) == $p->id ? 'selected' : '' }}>{{ $p->name.' (ID: '.$p->id.')' }}</option>
                        @endforeach
                      </select>
                    </div>
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
              <input type="submit" class="btn btn-primary" value="Simpan">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endif
<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Unggah Bukti</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ $step ? route($route.'.update',['id' => $data->id]) : route($route.'.update',['id' => $data->id]) }}" id="upload-file-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Bukti Pengeluaran</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="fa fa-paperclip"></i>
                        </span>
                      </div>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" name="file" accept=".zip,.rar,.tar">
                        <label class="custom-file-label" for="customFile">Pilih berkas (.zip/.rar/.tar)</label>
                      </div>
                    </div>
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
              <input id="save-data" type="submit" class="btn btn-primary" value="Unggah">
            </div>
          </div>
        </form>
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

@if($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 1))
@include('template.modal.delete-confirm')
@endif

@if(Auth::user()->role_id == 3 && (($data->unit_id == 1 || ($data->unit_id != 1 && in_array($data->type_id,['2','3']))) && $data->status_id == 1) && ((($data->unit_id == 1 || ($data->type_id == 3 && $totalRemain <= 0)) && $data->materials()->count() > 0) || $data->operationals()->count() > 0) && $data->files()->count() > 0)
<div class="modal fade" id="finalize-confirm" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-success">
          <i class="material-icons text-success">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin memfinalisasi pengeluaran <span class="item font-weight-bold">{{ '#'.$data->name }}</span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route($route.'.finalize', ['id' => $data->id]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Finalisasi</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

@if($data->unit_id != 1 && $data->type_id == 1 && $step == 2)
<!-- Number with Commas -->
<script src="{{ asset('js/number-with-commas.js') }}"></script>

@endif
<!-- Page level custom scripts -->
@include('template.footjs.global.custom-file-input')
@include('template.footjs.global.datatables')
@include('template.footjs.global.datepicker')
@include('template.footjs.global.select2')
@include('template.footjs.global.tooltip')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@if($data->unit_id != 1 && $data->type_id == 1 && $step == 2)
<script>
 $(function(){
    var need = {{ $max }};
    $('.allocation').on('keyup', function(e){
      var thisValue = $(this).val() ? parseInt($(this).val().replace(/\./g, ""),10) : 0;
      var max = parseInt($(this).attr('max'),10);
      if(thisValue > max){
        $(this).val(numberWithCommas(max));
        thisValue = max;
      }
      var total = 0;
      $('input[name^="value"]').each(function () {
         var value = parseInt($(this).val().replace(/\./g, ""),10);
         if(value > 0) total += value;
      });
      var direct = need-total;
      $('#directAllocation').val(numberWithCommas(Math.max(direct,0)));
    });
});
</script>
@endif
=======
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
Ubah {{ $active }}
@endsection

@section('headmeta')
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
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
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.show',['id' => $data->id]) }}">{{ $data->id }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body p-3">
        @if(Session::has('success-date'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success-info') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if(Session::has('danger-info'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ Session::get('danger-info') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->date ? $data->name : '-' }}
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
                  <label class="form-control-label">Tanggal</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->date ? $data->dateId : '-' }}
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
                  <label class="form-control-label">Divisi</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->unit ? $data->unit->name : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @if($data->project)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Proyek</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  <a href="{{ route('proposal.show',['id' => $data->project]) }}" target="_blank" class="text-decoration-none text-info">{{ $data->project->name }}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Status</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->status ? $data->status->name : '-' }}
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
                  <label class="form-control-label">Dibuat</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ date('d M Y H.i', strtotime($data->created_at)) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          @if(in_array($data->type_id,[2,3]))
          <button type="button" class="btn btn-sm btn-warning mr-2" data-toggle="modal" data-target="#edit-date"><i class="fas fa-pen mr-2"></i>Ubah Tanggal</button>
          @endif
          @if($data->type_id == 1)
          <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-date"><i class="fas fa-pen mr-2"></i>Ubah Tanggal</button>
            <button type="button" class="btn btn-sm btn-warning dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              @if($isRelated)
              <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal" data-target="#edit-project">Ubah Proyek</a>
              @endif
            </div>
          </div>
          @endif
          <a href="{{ route($route.'.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Bukti Pengeluaran</h6>
        @if($data->files()->count() > 0)
        <button type="button" class="m-0 float-right btn btn-sm btn-primary" data-toggle="modal" data-target="#upload-modal"><i class="fas fa-sync-alt mr-2"></i>Ganti</a>
        @endif
      </div>
      <div class="card-body px-4 py-3">
        @if(Session::has('success-file'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success-file') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if(Session::has('danger-file'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ Session::get('danger-file') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @error('file')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ $message }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @enderror
        @if($data->files()->count() > 0)
        @php
        $file = $data->files()->latest()->first();
        @endphp
        <div class="d-flex">
          <div>
            <i class="far fa-file-archive text-success mr-2"></i><span>{{ $file->nameExtension }}</span>
          </div>
          <div class="ml-auto">
            <a href="{{ route($route.'.download',['id'=>$data->id]) }}" class="btn btn-sm btn-success"><i class="fas fa-download"></i></a>
          </div>
        </div>
        <div class="d-flex small text-muted">
          <span class="mr-2">{{ $file->updated_at->diffForHumans() }}</span>
          <span>{{ $file->formatSizeUnits }}</span>
        </div>
        @else
        <div class="text-center mx-3 my-3">
          <h4 class="text-center">Belum ada bukti,</h4>
          <h6 class="font-weight-light mb-3">Mohon unggah bukti-bukti pengeluaran dalam satu berkas berformat ZIP, RAR, TAR (maks. 5 MB)</h6>
          <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal" data-target="#upload-modal"><i class="fas fa-upload mr-2"></i>Unggah</button>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

@if($data->type_id == 1)
<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow">
      <div class="card-body">
        <ul class="nav nav-pills">
          @php
          $thisStep = 1;
          @endphp
          @foreach($steps as $s)
          @php
          $navLink = route($route.'.edit',['id'=>$data->id,'step' => $thisStep]);
          @endphp
          <li class="nav-item">
            <a class="nav-link {{ $step == $thisStep ? 'active' : '' }}" href="{{ $step == $thisStep ? 'javascript:void(0)' : $navLink }}">{{ $s }}</a>
          </li>
          @php $thisStep++; @endphp
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>
@endif

@if(($data->unit_id == 1 || $data->type_id == 3) && $step == 1)
<div class="row">
  <div class="col-md-{{ $data->type_id == 3 ? '3' : '6' }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Harga Bahan Baku</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-{{ $data->type_id == 3 ? '3' : '6' }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Harga MOQ</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalMoq, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($data->type_id == 3)
  <div class="col-md-3 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Pembayaran</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalPaid, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Tagihan</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRemain, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-minus fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
</div>
@if($data->unit_id == 1)
@if($materials && count($materials) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store',['id'=>$data->id,'step' => $step]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Material" class="form-control-label">{{ $steps[$step-1] }}</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('material') is-invalid @enderror" name="material" id="select2Material" required="required">
                      @foreach($materials as $m)
                      <option value="{{ $m->id }}" {{ old('material') == $m->id ? 'selected' : '' }}>{{ $m->nameWithStock }}</option>
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
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Kuantitas</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="qty" class="form-control form-control-sm @error('qty') is-invalid @enderror number-separator" name="qty" value="{{ old('qty') ? old('qty') : '0' }}" maxlength="12" required="required">
                    @error('qty')
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
@endif
@endif
@elseif(($data->unit_id != 1 && $data->type_id == 1 && $step == 1) || (($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 2)) && $step == 2))
<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Biaya Operasional</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 1))
@if($operationals && count($operationals) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store',['id'=>$data->id,'step' => $step]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Operational" class="form-control-label">{{ $steps[$step-1] }}</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('operational') is-invalid @enderror" name="operational" id="select2Operational" required="required">
                      @foreach($operationals as $o)
                      <option value="{{ $o->id }}" {{ old('operational') == $o->id ? 'selected' : '' }}>{{ $o->name }}</option>
                      @endforeach
                    </select>
                    @error('operational')
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
                    <label for="normal-input" class="form-control-label">Biaya</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="amount" class="form-control form-control-sm @error('amount') is-invalid @enderror number-separator" name="amount" value="{{ old('amount') ? old('amount') : '0' }}" maxlength="15" required="required">
                    @error('amount')
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
                    <label for="normal-input" class="form-control-label">Deskripsi</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" id="desc" class="form-control form-control-sm @error('desc') is-invalid @enderror" name="desc" value="{{ old('desc') }}" maxlength="100">
                    @error('desc')
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
@endif
@endif
@elseif($data->type_id == 1 && $step == 2 && ($data->sofs()->count() > 0 || $direct))
<div class="row">
  <div class="col-md-6 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Kebutuhan Dana</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($max, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Alokasi Maksimal</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($sofs && count($sofs) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store',['id'=>$data->id,'step' => $step]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2SoF" class="form-control-label">{{ $steps[$step-1] }}</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('sof') is-invalid @enderror" name="sof" id="select2SoF" required="required">
                      @foreach($sofs as $s)
                      <option value="{{ $s->id }}" {{ old('sof') == $s->id ? 'selected' : '' }}>{{ $s->nameWithBalance }}</option>
                      @endforeach
                    </select>
                    @error('sof')
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
                    <label for="normal-input" class="form-control-label">Alokasi</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="nominal" class="form-control form-control-sm @error('nominal') is-invalid @enderror number-separator" name="nominal" value="{{ old('nominal') ? old('nominal') : '0' }}" maxlength="15" required="required">
                    @error('nominal')
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
@endif
@endif

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ $steps[$step-1] }}</h6>
      </div>
      @if((($data->unit_id == 1 || $data->type_id == 3) && $step == 1 && $data->materials()->count() > 0) || ((($data->unit_id != 1 && $data->type_id == 1 && $step == 1) || (($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 2)) && $step == 2)) && $data->operationals()->count() > 0) || ($data->type_id == 1 && $step == 2 && ($data->sofs()->count() > 0 || $direct)))
      <div class="card-body">
        <form action="{{ route($route.'.update',['id'=>$data->id,'step' => $step]) }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
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
        @if(($data->unit_id == 1 || $data->type_id == 3) && $step == 1 && $data->materials()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                @if($data->unit_id == 1)
                <th>Stok</th>
                @endif
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
                <th>MOQ</th>
                <th>Harga MOQ</th>
                @if($data->type_id == 3)
                <th>Terbayar</th>
                <th>Sisa Tagihan</th>
                <th>Status</th>
                @endif
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->materials as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->materialSupplier->name }}</td>
                @if($data->unit_id == 1)
                <td>{{ $d->materialSupplier->stockQuantityWithSeparator }}</td>
                <td>{{ $d->materialSupplier->priceWithSeparator }}</td>
                @else
                <td>{{ $d->priceWithSeparator }}</td>
                @endif
                <td>
                  @if($data->unit_id == 1)
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->quantityProposeWithSeparator }}">
                  @else
                  {{ $d->quantityProposeWithSeparator }}
                  @endif
                </td>
                @if($data->unit_id == 1)
                @php
                $value = $values && $values->where('id',$d->id)->count() > 0 ? $values->where('id',$d->id)->first()['value'] : 0;
                @endphp
                <td>{{ number_format($value, 0, ',', '.') }}</td>
                @php
                $times = $d->quantity_propose-$d->materialSupplier->stock_quantity > 0 ? ceil(($d->quantity_propose-$d->materialSupplier->stock_quantity)/$d->materialSupplier->moq) : 0;
                @endphp
                <td>{{ number_format($times*$d->materialSupplier->moq, 0, ',', '.') }}</td>
                <td>{{ number_format($times*$d->materialSupplier->moqPrice, 0, ',', '.') }}</td>
                @else
                <td>{{ $d->amountWithSeparator }}</td>
                <td>{{ $d->quantityBuyWithSeparator }}</td>
                <td>{{ $d->amountMoqWithSeparator }}</td>
                @if($data->type_id == 3)
                <td>{{ $d->paidWithSeparator }}</td>
                <td>{{ $d->remainWithSeparator }}</td>
                <td>{!! $d->statusBadge !!}</td>
                @endif
                @endif
                <td>
                  @if($data->unit_id == 1)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars($d->materialSupplier->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                  @elseif($data->type_id == 3)
                  <a href="{{ route($route.'.bill', ['id' => $data->id,'material' => $d->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @elseif((($data->unit_id != 1 && $data->type_id == 1 && $step == 1) || (($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 2)) && $step == 2)) && $data->operationals()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                <th>Biaya</th>
                <th>%</th>
                <th>Deskripsi</th>
                @if($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 1))
                <th style="width: 120px">Aksi</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->operationals as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->operational->name }}</td>
                <td>
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->amountWithSeparator }}">
                </td>
                <td>{{ $total && $total > 0 ? number_format(($d->amount/$total)*100, 1, ',', '.') : 0 }}</td>
                <td>
                  <input name="desc-{{ $d->id }}" type="text" class="form-control form-control-sm" value="{{ $d->operational_desc }}">
                </td>
                @if($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 1))
                <td>
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars($d->operational->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @elseif($data->type_id == 1 && $step == 2 && ($data->sofs()->count() > 0 || $direct))
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                <th>Kas</th>
                <th>Alokasi</th>
                <th>Penggunaan</th>
                <th>%</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              $remain = $max;
              @endphp
              @foreach($data->sofs as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->sof->name }}</td>
                <td>{{ $d->sof->balance ? $d->sof->balance->balanceWithSeparator : '-' }}</td>
                <td>
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator allocation" value="{{ $d->nominalWithSeparator }}" max="{{ $d->sof->balance ? $d->sof->balance->balance : '0' }}">
                </td>
                @php
                $used = $d->nominal > $remain ? $remain : $d->nominal;
                $remain -= $used;
                @endphp
                <td>{{ number_format($used, 0, ',', '.') }}</td>
                <td>{{ $max && $max > 0 ? number_format(($used/$max)*100, 1, ',', '.') : 0 }}</td>
                <td>
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars($d->sof->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              @endforeach
              @if($direct)
              <tr>
                <td>{{ $no++ }}</td>
              <td>{{ $direct->name }}</td>
              <td>-</td>
                <td>
                  <input id="directAllocation" name="direct-{{ $direct->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ number_format(max($max-$total,0), 0, ',', '.') }}" disabled="disabled">
                </td>
                <td>{{ number_format($remain, 0, ',', '.') }}</td>
                <td>{{ $max && $max > 0 ? number_format(($remain/$max)*100, 1, ',', '.') : 0 }}</td>
                <td>&nbsp;</td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
        @endif
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              @if($data->unit_id == 1 || ($data->unit_id != 1 && ($data->type_id == 1 && ($step == 1 || ($step == 2 && $data->sofs()->count() > 0))) || ($data->type_id == 2)))
              <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
              @endif
              @if(Auth::user()->role_id == 3 && (($data->unit_id == 1 || ($data->unit_id != 1 && in_array($data->type_id,['2','3']))) && $data->status_id == 1) && ((($data->unit_id == 1 || ($data->type_id == 3 && $totalRemain <= 0)) && $data->materials()->count() > 0) || $data->operationals()->count() > 0) && $data->files()->count() > 0)
              <button type="button" class="btn btn-sm btn-success ml-1" data-toggle="modal" data-target="#finalize-confirm">Finalisasi</button>
              @endif
            </div>
          </div>
        </div>
        </form>
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
        <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($steps[$step-1]) }} yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
<!--Row-->

<div class="modal fade" id="edit-date" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ $step ? route($route.'.update',['id' => $data->id]) : route($route.'.update',['id' => $data->id]) }}" id="edit-date-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Tanggal</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group date end-today">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                      </div>
                      <input type="text" name="date" class="form-control" value="{{ $data->date ? date('d F Y', strtotime($data->date)) : '' }}" placeholder="Pilih tanggal" id="dateInput">
                    </div>
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
              <input id="save-data" type="submit" class="btn btn-primary" value="Simpan">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@if($isRelated)
<div class="modal fade" id="edit-project" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.update',['id' => $data->id]) }}" id="edit-project-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          @csrf          
          {{ method_field('PUT') }}
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
                        <option value="{{ $p->id }}" {{ ($data && $data->project_id ? old('project',$data->project_id) : old('project')) == $p->id ? 'selected' : '' }}>{{ $p->name.' (ID: '.$p->id.')' }}</option>
                        @endforeach
                      </select>
                    </div>
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
              <input type="submit" class="btn btn-primary" value="Simpan">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endif
<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Unggah Bukti</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ $step ? route($route.'.update',['id' => $data->id]) : route($route.'.update',['id' => $data->id]) }}" id="upload-file-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Bukti Pengeluaran</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="fa fa-paperclip"></i>
                        </span>
                      </div>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" name="file" accept=".zip,.rar,.tar">
                        <label class="custom-file-label" for="customFile">Pilih berkas (.zip/.rar/.tar)</label>
                      </div>
                    </div>
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
              <input id="save-data" type="submit" class="btn btn-primary" value="Unggah">
            </div>
          </div>
        </form>
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

@if($data->unit_id == 1 || ($data->unit_id != 1 && $data->type_id == 1))
@include('template.modal.delete-confirm')
@endif

@if(Auth::user()->role_id == 3 && (($data->unit_id == 1 || ($data->unit_id != 1 && in_array($data->type_id,['2','3']))) && $data->status_id == 1) && ((($data->unit_id == 1 || ($data->type_id == 3 && $totalRemain <= 0)) && $data->materials()->count() > 0) || $data->operationals()->count() > 0) && $data->files()->count() > 0)
<div class="modal fade" id="finalize-confirm" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-success">
          <i class="material-icons text-success">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin memfinalisasi pengeluaran <span class="item font-weight-bold">{{ '#'.$data->name }}</span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route($route.'.finalize', ['id' => $data->id]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Finalisasi</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

@if($data->unit_id != 1 && $data->type_id == 1 && $step == 2)
<!-- Number with Commas -->
<script src="{{ asset('js/number-with-commas.js') }}"></script>

@endif
<!-- Page level custom scripts -->
@include('template.footjs.global.custom-file-input')
@include('template.footjs.global.datatables')
@include('template.footjs.global.datepicker')
@include('template.footjs.global.select2')
@include('template.footjs.global.tooltip')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@if($data->unit_id != 1 && $data->type_id == 1 && $step == 2)
<script>
 $(function(){
    var need = {{ $max }};
    $('.allocation').on('keyup', function(e){
      var thisValue = $(this).val() ? parseInt($(this).val().replace(/\./g, ""),10) : 0;
      var max = parseInt($(this).attr('max'),10);
      if(thisValue > max){
        $(this).val(numberWithCommas(max));
        thisValue = max;
      }
      var total = 0;
      $('input[name^="value"]').each(function () {
         var value = parseInt($(this).val().replace(/\./g, ""),10);
         if(value > 0) total += value;
      });
      var direct = need-total;
      $('#directAllocation').val(numberWithCommas(Math.max(direct,0)));
    });
});
</script>
@endif
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection