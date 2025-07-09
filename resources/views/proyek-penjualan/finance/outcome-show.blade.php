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
<meta name="csrf-token" content="{{ Session::token() }}" />
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
        @if($data->unit_id != 1 && $data->type_id == 1 && $data->acc_status == 1)
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
        @endif
        @if($data->status_id == 5)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Difinalisasi</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ date('d M Y H.i', strtotime($data->updated_at)) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="d-flex justify-content-end">
          @if(Auth::user()->role_id == 3)
          @if((($data->unit_id == 1 || ($data->unit_id != 1 && $data->project)) && $data->status_id == 1) && (($data->unit_id == 1 && $data->materials()->count() > 0) || $data->operationals()->count() > 0) && $data->files()->count() > 0)
          <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#finalize-confirm"><i class="fas fa-check mr-2"></i>Finalisasi</button>
          @endif
          @elseif(Auth::user()->role_id == 2)
          @if($data->unit_id != 1 && $data->type_id == 1 && in_array($data->status_id,[1,3]))
          <div class="btn-group mr-2">
            <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#accept-confirm"><i class="fas fa-check mr-2"></i>Setujui</button>
            <button type="button" class="btn btn-sm btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#revise-confirm"><i class="fas fa-redo mr-2"></i>Revisi</a>
              <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#deny-confirm"><i class="fas fa-times-circle mr-2"></i>Tolak</a>
            </div>
          </div>
          @endif
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
      </div>
      <div class="card-body px-4 py-3">
        @if($data->files()->count() > 0)
        @php
        $file = $data->files()->latest()->first();
        @endphp
        <div class="d-flex">
          <div>
            <i class="far fa-file-pdf text-success mr-2"></i><span>{{ $file->nameExtension }}</span>
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
          <h4 class="text-center">Mohon maaf,</h4>
          <h6 class="font-weight-light mb-3">Belum ada bukti yang ditemukan</h6>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="{{ $data->unit_id == 1 || $data->type_id == 3 ? 'col-md-4 ' : null }}col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Pengeluaran</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('outcome'), 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($data->unit_id == 1)
  <div class="col-md-4 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Harga MOQ Bahan Baku</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('moq'), 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-lemon fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Biaya Operasional</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('operational'), 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-bolt fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @else
  @if($data->type_id == 3)
  <div class="col-md-4 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Pembayaran</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('paid'), 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Tagihan</div>
            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('remain'), 0, ',', '.') }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-minus fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  @endif
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ $active }}</h6>
      </div>
      @if((($data->unit_id == 1 || $data->type_id == 3) && $data->materials()->count() > 0) || $data->operationals()->count() > 0)
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
        @if(($data->unit_id == 1 || $data->type_id == 3) && $data->materials()->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th>Bahan Baku</th>
                @if($data->acc_status != 1 && $data->type_id != 3)
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
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($data->materials as $d)
              <tr>
                <td>{{ $d->materialSupplier->name }}</td>
                @if($data->acc_status == 1 || $data->type_id == 3)
                <td>{{ $d->priceWithSeparator }}</td>
                @else
                <td>{{ $d->materialSupplier->stockQuantityWithSeparator }}</td>
                <td>{{ $d->materialSupplier->priceWithSeparator }}</td>
                @endif
                <td>{{ $d->quantityProposeWithSeparator }}</td>
                @if($data->acc_status == 1 || $data->type_id == 3)
                <td>{{ $d->amountWithSeparator }}</td>
                <td>{{ $d->quantityBuyWithSeparator }}</td>
                <td>{{ $d->amountMoqWithSeparator }}</td>
                @else
                @php
                $value = $values['material'] && $values['material']->where('id',$d->id)->count() > 0 ? $values['material']->where('id',$d->id)->first()['value'] : 0;
                @endphp
                <td>{{ number_format($value, 0, ',', '.') }}</td>
                @php
                $times = $d->quantity_propose-$d->materialSupplier->stock_quantity > 0 ? ceil(($d->quantity_propose-$d->materialSupplier->stock_quantity)/$d->materialSupplier->moq) : 0;
                @endphp
                <td>{{ number_format($times*$d->materialSupplier->moq, 0, ',', '.') }}</td>
                <td>{{ number_format($times*$d->materialSupplier->moqPrice, 0, ',', '.') }}</td>
                @endif
                @if($data->type_id == 3)
                <td>{{ $d->paidWithSeparator }}</td>
                <td>{{ $d->remainWithSeparator }}</td>
                <td>{!! $d->statusBadge !!}</td>
                @endif
              </tr>                
              @endforeach
              <tr>
                <td class="font-weight-bold" colspan="{{ $data->acc_status == 1 || $data->type_id == 3 ? '3' : '4' }}">Total Bahan Baku</td>
                <td>{{ number_format($total->get('material'), 0, ',', '.') }}</td>
                <td>&nbsp;</td>
                <td>{{ number_format($total->get('moq'), 0, ',', '.') }}</td>
                @if($data->type_id == 3)
                <td>{{ number_format($total->get('paid'), 0, ',', '.') }}</td>
                <td>{{ number_format($total->get('remain'), 0, ',', '.') }}</td>
                <td>&nbsp;</td>
                @endif
              </tr>
            </tbody>
          </table>
        </div>
        @endif
        @if($data->operationals()->count() > 0)
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
                <td>{{ $d->amountWithSeparator }}</td>
                <td>{{ $total->get('outcome') && $total->get('outcome') > 0 ? number_format(($d->amount/$total->get('outcome'))*100, 1, ',', '.') : 0 }}</td>
                <td>{{ $d->operational_desc }}</td>
              </tr>
              @endforeach
              @endif
              <tr>
                <td class="font-weight-bold">Total Biaya Operasional</td>
                <td class="font-weight-bold">{{ number_format($total->get('operational'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('outcome') && $total->get('outcome') > 0 ? number_format(($total->get('operational')/$total->get('outcome'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold">&nbsp;</td>
              </tr>
            </tbody>
          </table>
        </div>
        @endif
      </div>
      @else
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Belum ada data {{ strtolower($active) }} yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
@if($data->type_id == 1 && ($data->sofs()->count() > 0 || $direct))
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
              @if($direct && $remain > 0 && $data->acc_status != 1)
              <tr>
                <td>{{ $direct->name }}</td>
                <td>-</td>
                <td>{{ number_format(max($max-$data->sofs()->sum('nominal'),0), 0, ',', '.') }}</td>
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

@if(Auth::user()->role_id == 3)
@if((($data->unit_id == 1 || ($data->unit_id != 1 && $data->project)) && $data->status_id == 1) && (($data->unit_id == 1 && $data->materials()->count() > 0) || $data->operationals()->count() > 0) && $data->files()->count() > 0)
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
@elseif(Auth::user()->role_id == 2)
@if($data->unit_id != 1 && $data->type_id == 1 && in_array($data->status_id,[1,3]))
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
        Apakah Anda yakin ingin menyetujui pengeluaran <span class="item font-weight-bold">{{ '#'.$data->name }}</span>?
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
        Apakah Anda yakin ingin meminta pengeluaran <span class="item font-weight-bold">{{ '#'.$data->name }}</span> agar direvisi?
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
        Apakah Anda yakin ingin menolak pengeluaran <span class="item font-weight-bold">{{ '#'.$data->name }}</span>?
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