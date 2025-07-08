@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
Detail {{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
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
        @if(in_array($data->status_id,[1,3]) && !$isAcceptable)
        <div class="alert alert-warning" role="alert">
          <strong>Belum dapat disetujui!</strong> Pastikan divisi {{ $data->unit ? (Auth::user()->role_id == 3 && $data->unit_id == Auth::user()->unit_id ? 'Anda' : $data->unit->name) : 'terkait' }} sudah menerima bahan baku setelah proposal sebelumnya disetujui
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
        @if($data->acc_status == 1)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Disetujui</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ date('d M Y H.i', strtotime($data->acc_time)) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @if($data->status_id == 5)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Diterima</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ date('d M Y H.i', strtotime($data->updated_at)) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        @endif
        <div class="d-flex justify-content-end">
          @if(Auth::user()->role_id == 3)
          @if($data->status_id == 2)
          <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#confirm-confirm"><i class="fas fa-check mr-2"></i>Terima</button>
          @endif
          @elseif(Auth::user()->role_id == 2)
          @if(in_array($data->status_id,[1,3]))
          <div class="btn-group mr-2">
            @if($isAcceptable)
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#accept-confirm"><i class="fas fa-check mr-2"></i>Setujui</button>
            @else
            <button type="button" class="btn btn-sm btn-secondary" disabled><i class="fas fa-check mr-2"></i>Setujui</button>
            @endif
            <button type="button" class="btn btn-sm btn-{{ $isAcceptable ? 'success' : 'secondary' }} dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#revise-confirm"><i class="fas fa-redo mr-2"></i>Revisi</a>
              <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#deny-confirm"><i class="fas fa-times-circle mr-2"></i>Tolak</a>
            </div>
          </div>
          @endif
          @endif
          @if($data->status_id == 5)
          <a href="{{ route('report.project.show',['id' => $data->id]) }}" class="btn btn-sm btn-primary mr-2"><i class="fas fa-eye mr-2"></i>Lihat Laporan</a>
          @endif
          <a href="{{ route($route.'.index') }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Proposal Anggaran</h6>
      </div>
      @if($products && count($products) > 0)
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
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th>Produk</th>
                <th>Jumlah</th>
              </tr>
            </thead>
            <tbody>
              @foreach($products as $d)
              <tr>
                <td>{{ $d->name.' - '.$d->category->name.($d->sku_number ? ' - '.$d->sku_number : '') }}</td>
                <td>{{ number_format($data->productSalesTypes()->whereHas('productSalesType',function($q)use($d){
                  $q->where('product_id',$d->id);
                })->sum('quantity'), 0, ',', '.') }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if($data->cogs()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th>Bahan Baku</th>
                @if($data->acc_status != 1)
                <th>Stok</th>
                @endif
                <th>Harga</th>
                <th>Kuantitas</th>
                <th>Subtotal</th>
                <th>MOQ</th>
                <th>Harga MOQ</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->cogs as $d)
              @if($data->acc_status == 1)
              <tr>
                <td>{{ $d->materialSupplier->name }}</td>
                <td>{{ $d->priceWithSeparator }}</td>
                <td>{{ $d->quantityPurposeWithSeparator }}</td>
                <td>{{ $d->nominalWithSeparator }}</td>
                <td>{{ $d->quantityBuyWithSeparator }}</td>
                <td>{{ $d->nominalMoqWithSeparator }}</td>
              </tr>
              @else
              <tr>
                <td>{{ $d->materialSupplier->name }}
                  @if((($d->quantity_purpose-$d->materialSupplier->stock_quantity > 0) && $d->materialSupplier->moq <= 0) || ($d->materialSupplier->stock_quantity <= 0 && $d->materialSupplier->moq <= 0))
                  <i class="fas fa-exclamation-circle text-danger" data-toggle="tooltip" data-original-title="Bahan baku tidak dapat dipesan lagi"></i>
                  @endif
                </td>
                <td>{{ $d->materialSupplier->stockQuantityWithSeparator }}</td>
                <td>{{ $d->materialSupplier->priceWithSeparator }}</td>
                @php
                $propose = ($d->quantity_purpose-$d->materialSupplier->stock_quantity > 0) && $d->materialSupplier->moq <= 0 ? $d->materialSupplier->stockQuantityWithSeparator.' (Asli: '.$d->quantityPurposeWithSeparator.')' : $d->quantityPurposeWithSeparator;
                @endphp
                <td>{{ $propose }}</td>
                @php
                $value = $values['cogs'] && $values['cogs']->where('id',$d->id)->count() > 0 ? $values['cogs']->where('id',$d->id)->first()['value'] : 0;
                @endphp
                <td>{{ number_format($value, 0, ',', '.') }}</td>
                @php
                $times = ($d->quantity_purpose-$d->materialSupplier->stock_quantity > 0) && ($d->materialSupplier->moq > 0) ? ceil(($d->quantity_purpose-$d->materialSupplier->stock_quantity)/$d->materialSupplier->moq) : 0;
                @endphp
                <td>{{ number_format($times*$d->materialSupplier->moq, 0, ',', '.') }}</td>
                <td>{{ number_format($times*$d->materialSupplier->moqPrice, 0, ',', '.') }}</td>
              </tr>
              @endif
              @endforeach
              <tr>
                <td class="font-weight-bold" colspan="{{ $data->acc_status == 1 ? '3' : '4' }}">Total</td>
                <td>{{ number_format($total->get('cogs'), 0, ',', '.') }}</td>
                <td>&nbsp;</td>
                <td>{{ number_format($total->get('moq'), 0, ',', '.') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        @endif
      </div>
      @else
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Belum ada data yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>

@if($data->productSalesTypes()->count() > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Proyeksi Penjualan</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 30%">Produk</th>
                <th style="width: 20%">Penjualan</th>
                <th style="width: 10%">%</th>
                <th style="width: 40%" colspan="3">Deskripsi</th>
              </tr>
            </thead>
            <tbody>
              @foreach($data->productSalesTypes as $d)
              @if($data->acc_status == 1)
              <tr>
                <td>{{ $d->productSalesType->name }}</td>
                <td>{{ $d->nominalWithSeparator }}</td>
                <td>{{ $total->get('product') && $total->get('product') > 0 ? number_format(($d->nominal/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td>{{ $d->priceWithSeparator }}</td>
                <td>x</td>
                <td>{{ $d->quantityWithSeparator.' pcs' }}</td>
              </tr>
              @else
              <tr>
                <td>{{ $d->productSalesType->name }}</td>
                @php
                $productValue = (object)$values->get('product');
                $value = $productValue && $productValue->where('id',$d->id)->count() > 0 ? $productValue->where('id',$d->id)->first()['value'] : 0;
                @endphp
                <td>{{ number_format($value, 0, ',', '.') }}</td>
                <td>{{ $total->get('product') && $total->get('product') > 0 ? number_format(($value/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td>{{ $d->productSalesType->priceWithSeparator }}</td>
                <td>x</td>
                <td>{{ $d->quantityWithSeparator.' pcs' }}</td>
              </tr>
              @endif
              @endforeach
              <tr>
                <td class="font-weight-bold">Total Penjualan</td>
                <td class="font-weight-bold">{{ number_format($total->get('product'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('product') && $total->get('product') > 0 ? number_format(($total->get('product')/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold" colspan="3">Total penjualan bulanan gabungan</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 30%">Produk</th>
                <th style="width: 20%">HPP</th>
                <th style="width: 10%">%</th>
                <th style="width: 40%" colspan="3">Deskripsi</th>
              </tr>
            </thead>
            <tbody>
              @if($products && count($products) > 0)
              @foreach($products as $d)
              <tr>
                <td>{{ $d->name.' - '.$d->category->name.($d->sku_number ? ' - '.$d->sku_number : '') }}</td>
                @php
                $productCogs = $data->productCogs()->where('product_id',$d->id)->count() > 0 ? $data->productCogs()->where('product_id',$d->id)->first() : null;
                @endphp
                <td>{{ $productCogs ? $productCogs->nominalWithSeparator : '0' }}</td>
                <td>{{ $productCogs && $total->get('product') && $total->get('product') > 0 ? number_format(($productCogs->nominal/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td>{{ $productCogs ? $productCogs->desc : '' }}</td>
              </tr>
              @endforeach
              @endif
              <tr>
                <td class="font-weight-bold">Total HPP</td>
                <td class="font-weight-bold">{{ number_format($total->get('productCogs'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('product') && $total->get('product') > 0 ? number_format(($total->get('productCogs')/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold">Total Harga Pokok Penjualan (HPP)</td>
              </tr>
              <tr>
                <td class="font-weight-bold">Laba Kotor</td>
                @php
                $grossProfit = $total->get('product')-$total->get('productCogs');
                @endphp
                <td class="font-weight-bold">{{ number_format($grossProfit, 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('product') && $total->get('product') > 0 ? number_format(($grossProfit/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold">Total Penjualan - Total HPP</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 30%">Operasional</th>
                <th style="width: 20%">Biaya</th>
                <th style="width: 10%">%</th>
                <th style="width: 40%" colspan="3">Deskripsi</th>
              </tr>
            </thead>
            <tbody>
              @if($data->operationals()->count() > 0)
              @foreach($data->operationals as $d)
              <tr>
                <td>{{ $d->operational->name }}</td>
                <td>{{ $d->nominalWithSeparator }}</td>
                <td>{{ $total->get('product') && $total->get('product') > 0 ? number_format(($d->nominal/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td>{{ $d->operational_desc }}</td>
              </tr>
              @endforeach
              @endif
              <tr>
                <td class="font-weight-bold">Total Biaya Operasional</td>
                <td class="font-weight-bold">{{ number_format($total->get('operational'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('product') && $total->get('product') > 0 ? number_format(($total->get('operational')/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold">&nbsp;</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <tbody>
              <tr>
                <td class="font-weight-bold" style="width: 30%">Laba</td>
                @php
                $profit = $total->get('product')-$total->get('productCogs')-$total->get('operational');
                @endphp
                <td class="font-weight-bold" style="width: 20%">{{ number_format($profit, 0, ',', '.') }}</td>
                <td class="font-weight-bold" style="width: 10%">{{ $total->get('product') && $total->get('product') > 0 ? number_format(($profit/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold" style="width: 40%">Laba sebelum SAS</td>
              </tr>
              <tr>
                <td>SAS</td>
                @php
                $sas = 0.1*($total->get('product')-$total->get('productCogs')-$total->get('operational')); // 10%
                @endphp
                <td>{{ number_format($sas, 0, ',', '.') }}</td>
                <td>{{ $total->get('product') && $total->get('product') > 0 ? number_format(($sas/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td>10% dari Laba</td>
              </tr>
              <tr>
                <td>Laba Bersih</td>
                @php
                $netProfit = $profit-$sas;
                @endphp
                <td>{{ number_format($netProfit, 0, ',', '.') }}</td>
                <td>{{ $total->get('product') && $total->get('product') > 0 ? number_format(($netProfit/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td>Laba setelah SAS</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@if(($total->get('moq') > 0 || $total->get('operational') > 0) && ($data->sofs()->count() > 0 || ($data->acc_status != 1 && $direct)))
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Sumber Dana</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th>Sumber Dana</th>
                @if($data->acc_status == 1)
                <th>Jumlah</th>
                @else
                <th>Kas</th>
                <th>Alokasi</th>
                <th>Penggunaan</th>
                @endif
                <th>%</th>
              </tr>
            </thead>
            <tbody>
              @php
              $max = $remain = $total->get('moq')+$total->get('operational');
              @endphp
              @foreach($data->sofs as $d)
              <tr>
                <td>{{ $d->sof->name }}</td>
                @if($data->acc_status == 1)
                <td>{{ $d->nominalWithSeparator }}</td>
                <td>{{ $d->percentage }}</td>
                @else
                <td>{{ $d->sof->balance ? $d->sof->balance->balanceWithSeparator : '-' }}</td>
                <td>{{ $d->nominalWithSeparator }}</td>
                @php
                $used = $d->nominal > $remain ? $remain : $d->nominal;
                $remain -= $used;
                @endphp
                <td>{{ number_format($used, 0, ',', '.') }}</td>
                <td>{{ $max && $max > 0 ? number_format(($used/$max)*100, 1, ',', '.') : 0 }}</td>
                @endif
              </tr>
              @endforeach
              @if($direct && $remain > 0 && ($data->acc_status != 1 || $data->sofs()->count() <= 0))
              <tr>
                <td>{{ $direct->name }}</td>
                @if($data->acc_status != 1)
                <td>-</td>
                <td>{{ number_format(max($max-$data->sofs()->sum('nominal'),0), 0, ',', '.') }}</td>
                @endif
                <td>{{ number_format($remain, 0, ',', '.') }}</td>
                <td>{{ $max && $max > 0 ? number_format(($remain/$max)*100, 1, ',', '.') : 0 }}</td>
              </tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
<!--Row-->

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

@if(Auth::user()->role_id == 3)
@if($data->status_id == 2)
<div class="modal fade" id="confirm-confirm" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true" style="display: none;">
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
        Apakah Anda yakin ingin mengonfirmasi penerimaan bahan baku dari proposal <span class="item font-weight-bold">{{ $data->name }}</span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route($route.'.confirm', ['id' => $data->id]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Konfirmasi</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif
@elseif(Auth::user()->role_id == 2)
@if(in_array($data->status_id,[1,3]))
@if($isAcceptable)
<div class="modal fade" id="accept-confirm" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel" aria-hidden="true" style="display: none;">
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
        Apakah Anda yakin ingin menyetujui proposal <span class="item font-weight-bold">{{ $data->name }}</span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route($route.'.accept', ['id' => $data->id]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endif
<div class="modal fade" id="revise-confirm" tabindex="-1" role="dialog" aria-labelledby="reviseModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-warning">
          <i class="material-icons text-warning">&#xE627;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin meminta proposal <span class="item font-weight-bold">{{ $data->name }}</span> agar direvisi?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route($route.'.revise', ['id' => $data->id]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-warning">Ya, Revisi</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="deny-confirm" tabindex="-1" role="dialog" aria-labelledby="denyModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box">
          <i class="material-icons">&#xE5CD;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menolak proposal <span class="item font-weight-bold">{{ $data->name }}</span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route($route.'.deny', ['id' => $data->id]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-danger">Ya, Tolak</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.tooltip')
@endsection