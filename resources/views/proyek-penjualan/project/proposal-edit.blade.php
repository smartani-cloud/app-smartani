@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
Ubah {{ $active }}
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
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.show',['id' => $data->id]) }}">{{ $data->id }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body p-3">
        @if(Session::has('success-proposal'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success-proposal') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if(Session::has('danger-proposal'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ Session::get('danger-proposal') }}
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
          <button type="button" class="btn btn-sm btn-warning mr-2" data-toggle="modal" data-target="#edit-date"><i class="fas fa-pen mr-2"></i>Ubah</button>
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

@if($step == 1)
<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Penjualan</div>
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

@if($products && count($products) > 0)
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
                    <label for="select2Product" class="form-control-label">{{ $steps[$step-1] }}</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('product') is-invalid @enderror" name="product" id="select2Product" required="required">
                      @foreach($products as $p)
                      <option value="{{ $p->id }}" {{ old('product') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endforeach
                    </select>
                    @error('product')
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
@elseif($step == 2)
<div class="row">
  <div class="col-md-6 col-12 mb-4">
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
  <div class="col-md-6 col-12 mb-4">
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
</div>
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
@elseif($step == 3)
<div class="row">
  <div class="col-md-6 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Harga Bahan Baku</div>
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
            Harga Pokok Tersedia</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($max-$total, 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@elseif($step == 4)
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
                    <input type="text" id="nominal" class="form-control form-control-sm @error('nominal') is-invalid @enderror number-separator" name="nominal" value="{{ old('nominal') ? old('nominal') : '0' }}" maxlength="15" required="required">
                    @error('nominal')
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
@elseif($step == 5)
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
      @if((in_array($step,[1,3]) && $data->productSalesTypes()->count() > 0) || ($step == 2 && $data->cogs()->count() > 0) || ($step == 4 && $data->operationals()->count() > 0) || ($step == 5 && ($data->sofs()->count() > 0 || $direct)))
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
        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        @if($step == 1 && $data->productSalesTypes()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
                <th>%</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->productSalesTypes as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->productSalesType->name }}</td>
                <td>{{ $d->productSalesType->priceWithSeparator }}</td>
                <td>
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->quantityWithSeparator }}">
                </td>
                @php
                $value = $values && $values->where('id',$d->id)->count() > 0 ? $values->where('id',$d->id)->first()['value'] : 0;
                @endphp
                <td>{{ number_format($value, 0, ',', '.') }}</td>
                <td>{{ $total && $total > 0 ? number_format(($value/$total)*100, 1, ',', '.') : 0 }}</td>
                <td>
                  <!-- <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.edit',['id' => $data->id]) }}','{{ $d->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a> -->
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars($d->productSalesType->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @elseif($step == 2 && $data->cogs()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
                <th>MOQ</th>
                <th>Harga MOQ</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->cogs as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->materialSupplier->name }}
                  @if((($d->quantity_purpose-$d->materialSupplier->stock_quantity > 0) && $d->materialSupplier->moq <= 0) || ($d->materialSupplier->stock_quantity <= 0 && $d->materialSupplier->moq <= 0))
                  <i class="fas fa-exclamation-circle text-danger" data-toggle="tooltip" data-original-title="Bahan baku tidak dapat dipesan lagi"></i>
                  @endif
                </td>
                <td>{{ $d->materialSupplier->stockQuantityWithSeparator }}</td>
                <td>{{ $d->materialSupplier->priceWithSeparator }}</td>
                <td>
                  @if($d->materialSupplier->stock_quantity <= 0 && $d->materialSupplier->moq <= 0)
                  <input type="text" class="form-control form-control-sm number-separator{{ ($d->quantity_purpose-$d->materialSupplier->stock_quantity > 0) && $d->materialSupplier->moq <= 0 ? ' is-invalid' : null }}" value="{{ $d->quantityPurposeWithSeparator }}" max="{{ $d->materialSupplier->moq <= 0 ? $d->materialSupplier->stock_quantity : '-' }}" disabled="disabled">
                  @else
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator{{ ($d->quantity_purpose-$d->materialSupplier->stock_quantity > 0) && $d->materialSupplier->moq <= 0 ? ' is-invalid' : null }}" value="{{ $d->quantityPurposeWithSeparator }}" max="{{ $d->materialSupplier->moq <= 0 ? $d->materialSupplier->stock_quantity : '-' }}">
                  @endif
                </td>
                @php
                $value = $values && $values->where('id',$d->id)->count() > 0 ? $values->where('id',$d->id)->first()['value'] : 0;
                @endphp
                <td>{{ number_format($value, 0, ',', '.') }}</td>
                @php
                $times = ($d->quantity_purpose-$d->materialSupplier->stock_quantity > 0) && ($d->materialSupplier->moq > 0) ? ceil(($d->quantity_purpose-$d->materialSupplier->stock_quantity)/$d->materialSupplier->moq) : 0;
                @endphp
                <td>{{ number_format($times*$d->materialSupplier->moq, 0, ',', '.') }}</td>
                <td>{{ number_format($times*$d->materialSupplier->moqPrice, 0, ',', '.') }}</td>
                <td>
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars(str_replace(array("\r", "\n"), '', $d->materialSupplier->name))) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @elseif($step == 3 && $data->productSalesTypes()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Produk</th>
                <th>{{ $steps[$step-1] }}</th>
                <th>%</th>
                <th>Deskripsi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($products as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->name.' - '.$d->category->name.($d->sku_number ? ' - '.$d->sku_number : '') }}</td>
                @php
                $productCogs = $data->productCogs()->where('product_id',$d->id)->count() > 0 ? $data->productCogs()->where('product_id',$d->id)->first() : null;
                @endphp
                <td>
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $productCogs ? $productCogs->nominalWithSeparator : '0' }}" maxlength="12">
                </td>
                <td>{{ $productCogs && $total > 0 ? number_format(($productCogs->nominal/$total)*100, 1, ',', '.') : 0 }}</td>
                <td>
                  <input name="desc-{{ $d->id }}" type="text" class="form-control form-control-sm" value="{{ $productCogs ? $productCogs->desc : '' }}">
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @elseif($step == 4 && $data->operationals()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>{{ $steps[$step-1] }}</th>
                <th>Biaya</th>
                <th>%</th>
                <th>Deskripsi</th>
                <th style="width: 120px">Aksi</th>
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
                  <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->nominalWithSeparator }}">
                </td>
                <td>{{ $total && $total > 0 ? number_format(($d->nominal/$total)*100, 1, ',', '.') : 0 }}</td>
                <td>
                  <input name="desc-{{ $d->id }}" type="text" class="form-control form-control-sm" value="{{ $d->operational_desc }}">
                </td>
                <td>
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $steps[$step-1] }}', '{!! addslashes(htmlspecialchars($d->operational->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'step' => $step, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @elseif($step == 5 && ($data->sofs()->count() > 0 || $direct))
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
        @if($step != 5 || ($step == 5 && $data->sofs()->count() > 0))
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
            </div>
          </div>
        </div>
        @endif
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
        <form action="{{ $step ? route($route.'.update',['id' => $data->id,'step' => $step]) : route($route.'.update',['id' => $data->id]) }}" id="edit-date-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="selectYear" class="form-control-label">Tahun</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @php
                    $year_start = date('Y'); // current year
                    $year_end = date('Y', strtotime('+1 year')); 
                    $user_selected_year = $data->year ? $data->year : $year_start; //Check default year
                    @endphp
                    <select class="form-control @error('year') is-invalid @enderror" name="year" id="selectYear" required="required">
                    @for($i_year = $year_start; $i_year <= $year_end; $i_year++)
                    <option value="{{ $i_year }}" {{ old('year',$user_selected_year) == $i_year ? 'selected' : '' }}>{{ $i_year }}</option>
                    @endfor
                    </select>
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
                    <label for="selectMonth" class="form-control-label">Bulan</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @php
                      $selected_month = $data->month ? $data->month : date('m'); //Check default month

                      // Replace and add new months list
                      $months_name = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    @endphp
                    <select class="form-control @error('month') is-invalid @enderror" name="month" id="selectMonth" required="required">
                    @for($i_month = 1; $i_month <= 12; $i_month++)
                    <option value="{{ $i_month }}" {{ old('month',$selected_month) == $i_month ? 'selected' : '' }}>{{ $months_name[$i_month-1] }}</option>
                    @endfor
                    </select>
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

<!-- Number with Commas -->
<script src="{{ asset('js/number-with-commas.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.select2')
@include('template.footjs.global.tooltip')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@if($step == 2)
<script>
 $(function(){
    $("#update-form :input[name^=value]").on('keyup', function(e){
        if($(this).attr('max') != '-'){
            var max = parseInt($(this).attr('max'),10);
            var thisValue = $(this).val() ? parseInt($(this).val().replace(/\./g, ""),10) : 0;
            if(thisValue > max){
                if(!$(this).hasClass('is-invalid')) $(this).addClass('is-invalid');
            }
            else{
                if($(this).hasClass('is-invalid')) $(this).removeClass('is-invalid');
            }
        }
    });
});
</script>
@elseif($step == 3)
<script>
 $(function(){
    var max = {{ $max }};
    $('#update-form').on('submit', function(e){
      var value = 0;
      $("#update-form :input[name^=value]").each(function(){
        var input = $(this).val();
        input = input.replace(/\./g, '');
        var thisValueInput = parseInt(input,10);
        value += thisValueInput;
        console.log('+'+thisValueInput);
      });
      if(value <= max){
        return;
      }
      alert('Total harga pokok melebihi batas yang tersedia.\n' + value + ' > ' + max);
      e.preventDefault();
    });
});
</script>
@elseif($step == 5)
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
@endsection