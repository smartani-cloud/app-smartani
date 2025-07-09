<<<<<<< HEAD
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
{{ $active }}
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $data->id }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body p-3">
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
        @if($data->buyer)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Pembeli</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->buyer->name }}
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
          @if($data->buyer)
          <a href="{{ route($route.'.invoice',['id' => $data->id]) }}" class="btn btn-sm btn-success mr-2" target="_blank"><i class="fa fa-print mr-2"></i>Cetak Invoice</a>
          @else
          <a href="javascript:void(0)" class="btn btn-sm btn-secondary disabled mr-2"><i class="fa fa-print mr-2"></i>Cetak Invoice</a>
          @endif
          <a href="{{ route($route.'.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
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

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Produk</h6>
      </div>
      @if($data->details()->count() > 0)
      <div class="card-body">
        @if(!$data->paymentType || ($data->paymentType && $data->paymentType->name != "Berkala"))
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
                <td>{{ $d->productSalesType->product->name }}</td>
                <td>{{ $d->productSalesType->product->unit->name }}</td>
                <td>{{ $d->productSalesType->product->category->name }}</td>
                <td>{{ $d->productSalesType->salesType->name }}</td>
                <td>{{ $d->priceWithSeparator }}</td>
                <td>{{ $d->quantityWithSeparator }}</td>
                <td>{{ $d->subtotalWithSeparator }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @else
      @if(!$data->paymentType || ($data->paymentType && $data->paymentType->name != "Berkala"))
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
      @endif
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Belum ada data produk yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
<!--Row-->

@if($data->paymentType && $data->paymentType->name == "Berkala")
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran</h6>
      </div>
      @if($data->payments()->count() > 0)
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
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Tanggal</th>
                <th>Ketarangan</th>
                <th>Nominal</th>
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
                <td>{{ $d->note }}</td>
                <td>{{ $d->valueWithSeparator }}</td>
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
        <h6 class="font-weight-light mb-3">Belum ada data riwayat pembayaran yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>

@endif
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Rekapitulasi</h6>
      </div>
      <div class="card-body p-3">
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

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.global.tooltip')
=======
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
{{ $active }}
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $data->id }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body p-3">
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
        @if($data->buyer)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Pembeli</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->buyer->name }}
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
          @if($data->buyer)
          <a href="{{ route($route.'.invoice',['id' => $data->id]) }}" class="btn btn-sm btn-success mr-2" target="_blank"><i class="fa fa-print mr-2"></i>Cetak Invoice</a>
          @else
          <a href="javascript:void(0)" class="btn btn-sm btn-secondary disabled mr-2"><i class="fa fa-print mr-2"></i>Cetak Invoice</a>
          @endif
          <a href="{{ route($route.'.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
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

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Produk</h6>
      </div>
      @if($data->details()->count() > 0)
      <div class="card-body">
        @if(!$data->paymentType || ($data->paymentType && $data->paymentType->name != "Berkala"))
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
                <td>{{ $d->productSalesType->product->name }}</td>
                <td>{{ $d->productSalesType->product->unit->name }}</td>
                <td>{{ $d->productSalesType->product->category->name }}</td>
                <td>{{ $d->productSalesType->salesType->name }}</td>
                <td>{{ $d->priceWithSeparator }}</td>
                <td>{{ $d->quantityWithSeparator }}</td>
                <td>{{ $d->subtotalWithSeparator }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @else
      @if(!$data->paymentType || ($data->paymentType && $data->paymentType->name != "Berkala"))
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
      @endif
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Belum ada data produk yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
<!--Row-->

@if($data->paymentType && $data->paymentType->name == "Berkala")
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran</h6>
      </div>
      @if($data->payments()->count() > 0)
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
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Tanggal</th>
                <th>Ketarangan</th>
                <th>Nominal</th>
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
                <td>{{ $d->note }}</td>
                <td>{{ $d->valueWithSeparator }}</td>
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
        <h6 class="font-weight-light mb-3">Belum ada data riwayat pembayaran yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>

@endif
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Rekapitulasi</h6>
      </div>
      <div class="card-body p-3">
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

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.global.tooltip')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection