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
        @if(Session::has('success-info'))
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
                  <label class="form-control-label">Pembeli</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->buyer ? $data->buyer->name : '-' }}
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
        @if($data->paymentType)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Metode Pembayaran</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->paymentType->name }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        @if($data->due_date && $data->paymentType && $data->paymentType->name == "Berkala")
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Batas Waktu</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->dueDateId }}
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
                  {{ $data->is_active == 1 ? 'Aktif' : 'Selesai' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @if($data->note)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Keterangan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->note }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="d-flex justify-content-end">
          @if(!$data->paymentType)
          <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-date"><i class="fas fa-pen mr-2"></i>Ubah Tanggal</button>
            <button type="button" class="btn btn-sm btn-warning dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal" data-target="#edit-note">Ubah Keterangan</a>
              <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal" data-target="#edit-buyer">Ubah Pembeli</a>
              @if($isRelated)
              <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal" data-target="#edit-project">Ubah Proyek</a>
              @endif
            </div>
          </div>
          @else
          <button type="button" class="btn btn-sm btn-warning mr-2" data-toggle="modal" data-target="#edit-note"><i class="fas fa-pen mr-2"></i>Ubah Keterangan</button>
          @endif
          <a href="{{ route($route.'.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

@if($data->paymentType && $data->paymentType->name == "Berkala")
<div class="row">
  @php
  $col = $data->sale && $data->balance > 0 ? '3' : '4';
  @endphp
  @if($data->sale && $data->balance > 0)
  <div class="col-md-{{ $col }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Saldo Pembeli</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data->balanceWithSeparator }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-wallet fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  <div class="col-md-{{ $col }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Penjualan</div>
            <div id="totalNominal" class="h5 mb-0 font-weight-bold text-gray-800">{{ $data->totalAmountWithSeparator }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-{{ $col }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Pembayaran</div>
            <div id="totalPaid" class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($data->payments()->sum('value'), 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-{{ $col }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Tagihan</div>
            <div id="totalRemain" class="h5 mb-0 font-weight-bold text-gray-800">{{ $data->billWithSeparator }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-minus fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@else
<div class="row">
  @php
  $col = $data->sale && $data->balance > 0 ? '6' : '12';
  @endphp
  @if($data->sale && $data->balance > 0)
  <div class="col-md-{{ $col }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Saldo Pembeli</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data->balanceWithSeparator }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-wallet fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  <div class="col-md-{{ $col }} col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Penjualan</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $data->totalAmountWithSeparator }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

@if($data->paymentType && $data->paymentType->name == "Berkala")
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Produk</h6>
      </div>
      @if($data->details()->count() > 0)
      <div class="card-body">
        <form action="{{ route($route.'.update',['id'=>$data->id]) }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <input type="hidden" name="_products" value="true">
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
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>SKU</th>
                <th>Produk</th>
                <th>Divisi</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->details as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->productSalesType->product->sku_number }}</td>
                @php
                $hasStocks = $d->productSalesType->product->stocks()->sum('taxed') > 0 ? true : false;
                @endphp
                <td>{{ $d->productSalesType->product->name }}
                  @if(!$hasStocks && ($data->total_amount-$data->payments()->sum('value') > 0))
                  <i class="fas fa-exclamation-circle text-danger" data-toggle="tooltip" data-original-title="Stok produk habis"></i>
                  @endif
                </td>
                <td>{{ $d->productSalesType->product->unit->name }}</td>
                <td>{{ $d->productSalesType->product->category->name }}</td>
                <td>{{ $d->productSalesType->salesType->name }}</td>
                <td>{{ $d->priceWithSeparator }}</td>
                <td>
                  <input name="value-{{ $d->id }}" type="number" class="form-control form-control-sm" value="{{ $d->quantity }}" min="0" max="{{ $data->total_amount-$data->payments()->sum('value') <= 0 ? $d->quantity : $d->productSalesType->product->stocks()->sum('taxed')+$d->quantity }}">
                </td>
                <td>{{ $d->subtotalWithSeparator }}</td>
                <td>
                  @if($data->details()->count() > 1 && $d->quantity == 0)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Produk', '{!! addslashes(htmlspecialchars($d->productSalesType->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
            </div>
          </div>
        </div>
        </form>
      </div>
      @else
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Belum ada data produk yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran</h6>
      </div>
      @if($data->payments()->count() > 0)
      <div class="card-body">
        @if(Session::has('success-payment'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success-payment') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if(Session::has('danger-payment'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ Session::get('danger-payment') }}
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
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Tanggal</th>
                <th>Ketarangan</th>
                <th>Nominal</th>
                @if($data->sales()->count() <= 0)
                <th style="width: 120px">Aksi</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->payments()->orderBy('date')->get() as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->dateId }}</td>
                <td>{{ $d->note ? $d->note : '-' }}</td>
                <td>{{ $d->valueWithSeparator }}</td>
                @if($data->sales()->count() <= 0)
                <td>
                  <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.payment.edit') }}','{{ $d->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Riwayat Pembayaran', '{!! addslashes(htmlspecialchars($d->name)) !!}', '{{ route($route.'.refund', ['id' => $data->id, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($data->total_amount-$data->payments()->sum('value') > 0)
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              <button class="btn btn-sm btn-primary px-3" type="button" data-toggle="modal" data-target="#pay-confirm">Bayar</button>
            </div>
          </div>
        </div>
        @endif
      </div>
      @else
      @if(Session::has('success-payment'))
      <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <strong>Sukses!</strong> {{ Session::get('success-payment') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      @if(Session::has('danger-payment'))
      <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <strong>Gagal!</strong> {{ Session::get('danger-payment') }}
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
        <h6 class="font-weight-light mb-3">Belum ada data riwayat pembayaran yang ditemukan</h6>
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              <button class="btn btn-sm btn-primary px-3" type="button" data-toggle="modal" data-target="#pay-confirm">Bayar</button>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@else
@if(!$data->paymentType)
@if($products && count($products) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store',['id'=>$data->id]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Product" class="form-control-label">Produk</label>
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
                    <input type="text" id="qty" class="form-control form-control-sm @error('qty') is-invalid @enderror number-separator" name="qty" value="{{ old('qty') ? old('qty') : '1' }}" maxlength="12" required="required">
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
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Produk</h6>
      </div>
      @if($data->details()->count() > 0)
      <div class="card-body">
        <form action="{{ route($route.'.update',['id'=>$data->id]) }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
        {{ method_field('PUT') }}
        <input type="hidden" name="_products" value="true">
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
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>SKU</th>
                <th>Produk</th>
                <th>Divisi</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->details as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->productSalesType->product->sku_number }}</td>
                @php
                $hasStocks = $d->productSalesType->product->stocks()->sum('taxed') > 0 ? true : false;
                @endphp
                <td>{{ $d->productSalesType->product->name }}
                  @if(($d->price != $d->productSalesType->price) && !$data->paymentType)
                  <i class="fas fa-exclamation-triangle text-warning" data-toggle="tooltip" data-original-title="Ada perubahan harga"></i>
                  @endif
                  @if(!$hasStocks && !$data->paymentType)
                  <i class="fas fa-exclamation-circle text-danger" data-toggle="tooltip" data-original-title="Stok produk habis"></i>
                  @endif
                </td>
                <td>{{ $d->productSalesType->product->unit->name }}</td>
                <td>{{ $d->productSalesType->product->category->name }}</td>
                <td>{{ $d->productSalesType->salesType->name }}</td>
                <td>{{ $d->priceWithSeparator }}
                  @if(($d->price != $d->productSalesType->price) && !$data->paymentType)
                  <a href="{{ route($route.'.sync.item',['id' => $data->id,'item'=>$d->id]) }}" class="text-decoration-none ml-1" onclick="event.preventDefault(); document.getElementById('sync-item-{{ $d->id }}').submit();"><i class="fas fa-sync-alt text-success"></i></a>
                  @endif
                </td>
                <td>
                  <input name="value-{{ $d->id }}" type="number" class="form-control form-control-sm {{ !$hasStocks && !$data->paymentType ? 'is-invalid' : '' }}" value="{{ $d->quantity }}" min="0" max="{{ $data->payment_type_id == 1 ? $d->quantity : $d->productSalesType->product->stocks()->sum('taxed') }}" {{ $hasStocks || $data->payment_type_id == 1 ? '' : 'disabled="disabled"' }}>
                </td>
                <td>{{ $d->subtotalWithSeparator }}</td>
                <td>
                  <!-- <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.edit',['id' => $data->id]) }}','{{ $d->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a> -->
                  @if(!$data->paymentType || ($data->paymentType && $data->details()->count() > 1 && $d->quantity == 0))
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Produk', '{!! addslashes(htmlspecialchars($d->productSalesType->name)) !!}', '{{ route($route.'.destroy', ['id' => $data->id, 'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
              @if(!$data->paymentType)
              @if($sellable)
              <button class="btn btn-sm btn-success px-3" type="button" data-toggle="modal" data-target="#sell-confirm">Jual</button>
              @else
              <button class="btn btn-sm btn-secondary px-3" type="button" disabled="disabled">Jual</button>
              @endif
              @endif
            </div>
          </div>
        </div>
        </form>
      </div>
      @foreach($data->details as $d)
      @if(($d->price != $d->productSalesType->price) && !$data->paymentType)
      <form id="sync-item-{{ $d->id }}" action="{{ route($route.'.sync.item',['id' => $data->id,'item'=>$d->id]) }}" method="POST" class="d-none">@csrf @method('PUT')</form>
      @endif
      @endforeach
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
        <h6 class="font-weight-light mb-3">Belum ada data produk yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
@endif
<!--Row-->

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Rekapitulasi</h6>
      </div>
      <div class="card-body p-3">
        @if(Session::has('success-tax'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success-tax') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if($tax && (floatval($data->tax_percentage) !== floatval($tax)))
        <div class="alert alert-warning" role="alert">
          <strong>Perhatian!</strong> Terjadi perubahan persentase pajak penjualan dari {{ floatval($data->tax_percentage) }}% ke {{ floatval($tax) }}%. <a href="{{ route($route.'.sync.tax',['id' => $data->id]) }}" class="text-info font-weight-bold text-decoration-none" onclick="event.preventDefault(); document.getElementById('sync-tax').submit();">Sinkronisasi</a>
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Total</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->totalAmountWithSeparator }}
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
                  <label class="form-control-label">Pajak</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->taxWithSeparator }}@if($data->tax_percentage)<small class="text-muted ml-1">({{$data->tax_percentage}}%)</small>@endif
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
                  <label class="form-control-label">Total Bersih</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->netTotalAmountWithSeparator }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<form id="sync-tax" action="{{ route($route.'.sync.tax',['id' => $data->id]) }}" method="POST" class="d-none">@csrf @method('PUT')</form>

<div class="modal fade" id="edit-note" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.update.note',['id' => $data->id]) }}" id="edit-note-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          @csrf
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="note" class="form-control-label">Ketarangan</label>
                  </div>
                  <div class="col-12">
                    <textarea id="note" class="form-control" name="note" maxlength="150" rows="3">{{ $data->note }}</textarea>
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

@if(!$data->paymentType)
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
        <form action="{{ route($route.'.update',['id' => $data->id]) }}" id="edit-date-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          @csrf
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

<div class="modal fade" id="edit-buyer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.update',['id' => $data->id]) }}" id="edit-buyer-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          @csrf
          {{ method_field('PUT') }}
          <input type="hidden" name="no_buyer" value="{{ $data->buyer ? 0 : 1 }}" {!! $data->buyer ? 'disabled="disabled"' : 'required="required"' !!}>
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
                      <select class="select2 form-control" name="buyer" id="select2Buyer" required="required">
                        @if($data->buyer)
                        <option value="{{ $data->buyer->id }}" selected="selected">{{ $data->buyer->name }}</option>
                        @endif
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
@endif
@if($data->paymentType && $data->paymentType->name == "Berkala")
<div class="modal fade" id="pay-confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Bayar Angsuran</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.pay',['id'=>$data->id]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="paymentDateInput" class="form-control-label">Tanggal<span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <div class="input-group date">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                      </div>
                      <input type="text" name="payment_date" class="form-control form-control-sm @error('payment_date') is-invalid @enderror" value="{{ date('d F Y') }}" placeholder="Pilih tanggal" id="paymentDateInput" required="required">
                    </div>
                    @error('payment_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="paymentNominal" class="form-control-label">Nominal<span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="paymentNominal" class="form-control form-control-sm @error('payment_nominal') is-invalid @enderror number-separator" name="payment_nominal" value="{{ old('payment_nominal') }}" maxlength="12" required="required">
                    <small class="form-text text-muted">Jika nominal melebihi tagihan, maka nominal yang akan tersimpan hanya senilai total tagihan yang ada</small>
                    @error('payment_nominal')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="paymentBalance" class="form-control-label">Sisa Tagihan</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="paymentBalance" class="form-control form-control-sm number-separator" name="payment_balance" value="{{ number_format($data->total_amount-$data->payments()->sum('value'), 0, ',', '.') }}" maxlength="12" disabled="disabled">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="paymentNote" class="form-control-label">Keterangan</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <textarea id="paymentNote" class="form-control form-control-sm @error('payment_note') is-invalid @enderror" name="payment_note" maxlength="50" rows="2"></textarea>
                    @error('payment_note')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
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
              <input type="submit" class="btn btn-primary" value="Bayar">
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

@endif
@if(!$data->paymentType)
@if($sellable)
<div class="modal fade" id="sell-confirm" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-success">
          <i class="material-icons text-success">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>

      <form id="sell-form" action="{{ route($route.'.sell', ['id' => $data->id]) }}" method="post">
        @csrf
        {{ method_field('PUT') }}
        <div class="modal-body p-1">
          Apakah Anda yakin ingin memproses penjualan <span class="item font-weight-bold">#{{ $data->name }}</span>?
          <div class="row mt-3">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="selectMethod" class="form-control-label">Metode Pembayaran</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-cash-register"></i></span>
                      </div>
                      <select aria-label="Metode" name="method" id="selectMethod" title="Metode" class="form-control @error('method') is-invalid @enderror" required="required">
                        @foreach($methods as $m)
                        <option value="{{ $m->id }}" {{ ($data && $data->payment_type_id ? old('method',$data->payment_type_id) : old('method')) == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
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
                    <label for="dueDateInput" class="form-control-label">Batas Waktu</label>
                  </div>
                  <div class="col-12">
                    <div class="input-group date">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                      </div>
                      <input type="text" name="due_date" class="form-control" value="{{ $data->due_date ? date('d F Y', strtotime($data->due_date)) : date('d F Y') }}" placeholder="Pilih tanggal" id="dueDateInput" required="required">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Tidak</button>
          <button type="submit" class="btn btn-success">Ya, Jual</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endif

@include('template.modal.delete-confirm')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

@if($data->paymentType && $data->paymentType->name == "Berkala")
<!-- Number with Commas -->
<script src="{{ asset('js/number-with-commas.js') }}"></script>

@endif
<!-- Page level custom scripts -->
@if(!$data->paymentType)
@include('template.footjs.global.datepicker-end-today')
@endif
@include('template.footjs.global.datepicker')
@include('template.footjs.global.tooltip')
@include('template.footjs.modal.get_delete')
@if($data->paymentType && $data->paymentType->name == "Berkala")
@include('template.footjs.modal.post_edit')
<script>
$(function(){
    var bill = parseInt($('#totalRemain').html().replace(/\./g, ""));
    $('#paymentNominal').on('keyup', function(e){
      var total = $(this).val() ? parseInt($(this).val().replace(/\./g, "")) : 0;
      var balance = bill-total;
      $('#paymentBalance').val(numberWithCommas(balance));
    });
    $('#edit-form').on("keyup",'#editPaymentNominal',function(e){
      e.preventDefault();
      var total = $(this).val() ? parseInt($(this).val().replace(/\./g, "")) : 0;
      var def = $(this).attr('data-default') ? $(this).attr('data-default') : 0;
      var balance = bill-(total-def);
      $('#editPaymentBalance').val(numberWithCommas(balance));
    });
});  
</script>
@endif
@if(!$data->paymentType)
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
            sid: '{!! $data->id !!}'
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
      dropdownParent: $('#edit-buyer')
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
<script>
$(function(){
    $('#sell-form').on('click', function(e){
      var invalid = 0;
      $("#update-form :input[name^=value]").each(function(){
        var input = parseInt($(this).val(),10);
        var max = parseInt($(this).attr('max'),10);
        if(input > max){
          invalid++;
        }
      });
      if(invalid == 0){
        return;
      }
      alert('Gagal!\nPastikan kuantitas produk tidak melebihi stok yang tersedia');
      e.preventDefault();
      $('#sell-confirm').modal('hide');
    });
});
</script>
@endif
@endsection