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
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('finance.index') }}">Keuangan</a></li>
    <li class="breadcrumb-item"><a href="{{ route('report.index') }}">Laporan Keuangan</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.index') }}" id="viewItemForm" method="get">
          @if(Auth::user()->unit->name == 'Management')
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="selectUnit" class="form-control-label">Divisi</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select class="form-control form-control-sm @error('unit') is-invalid @enderror" name="unit" id="selectUnit" required="required">
                      <option value="all" {{ old('unit',$unit) == 'all' ? 'selected' : '' }}>Semua</option>
                      @foreach($units as $key => $u)
                      <option value="{{ $key }}" {{ old('unit',$unit) == $key ? 'selected' : '' }}>{{ $u }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="selectYear" class="form-control-label">Tahun</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select class="form-control form-control-sm @error('year') is-invalid @enderror" name="year" id="selectYear" required="required">
                      @if(!$finances || ($finances && count($finances) < 1))
                      <option value="" selected="selected" disabled="disabled">Belum Ada</option>
                      @endif
                      @foreach($finances->pluck('year')->unique() as $y)
                      <option value="{{ $y }}" {{ old('year',$year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                      @endforeach
                    </select>
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
                    <label for="selectMonth" class="form-control-label">Bulan</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select class="form-control form-control-sm @error('month') is-invalid @enderror" name="month" id="selectMonth" required="required">
                      <option value="all" {{ old('month',$month) == 'all' ? 'selected' : '' }}>Semua</option>
                      @foreach($finances->where('year',$year) as $f)
                      <option value="{{ $f->month }}" {{ old('month',$month) == $f->month ? 'selected' : '' }}>{{ $f->monthName }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      <input type="submit" class="btn btn-sm btn-primary" value="Lihat">
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@if($year && $month && $data)
<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow">
      <div class="card-body">
        <ul class="nav nav-pills">
          @foreach($reports as $r)
          @php
          if(Auth::user()->unit->name == 'Management')
            $navLink = route($route.'.index',['unit' => $unit, 'year' => $year, 'month' => $month, 'report' => $r]);
          else
            $navLink = route($route.'.index',['year' => $year, 'month' => $month, 'report' => $r]);
          @endphp
          <li class="nav-item">
            <a class="nav-link {{ $report == $r ? 'active' : '' }}" href="{{ $report == $r ? 'javascript:void(0)' : $navLink }}">{{ ucwords($r) }}</a>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Content Row -->
<div class="row">
    @php
    $firstRowCol = 4;
    if($unit != 1) $firstRowCol = $report == 'faktual' ? 4 : 6;
    @endphp
    <!-- Income (Monthly) Card -->
    <div class="col-xl-{{ $firstRowCol }} col-md-{{ $firstRowCol }} col-12 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Pemasukan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financeUnit ? number_format($total->get('income'), 0, ',', '.') : '0' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outcome (Monthly) Card -->
    <div class="col-xl-{{ $firstRowCol }} col-md-{{ $firstRowCol }} col-12 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Pengeluaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financeUnit ? number_format($total->get('outcome'), 0, ',', '.') : '0' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($unit == 1)
    <!-- MUDA (Monthly) Card -->
    <div class="col-xl-{{ $firstRowCol }} col-md-{{ $firstRowCol }} col-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Kas MUDA</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('profit'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    @if($report == 'faktual')
    <!-- Tax (Monthly) Card -->
    <div class="col-xl-{{ $firstRowCol }} col-md-{{ $firstRowCol }} col-12 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pajak</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financeUnit ? number_format(Auth::user()->unit_id == 1 && $unit && $unit != 'all' ? ($financeDetails->where('unit_id',$unit)->where('type_id',3)->sum('amount') - $financeDetails->where('unit_id',$unit)->where('type_id',4)->sum('amount')) : ($financeDetails->where('type_id',3)->sum('amount') - $financeDetails->where('type_id',4)->sum('amount')), 0, ',', '.') : '0' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif
    <!-- Net Profit (Monthly) Card -->
    <div class="col-xl-3 col-md-3 col-12 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Laba Bersih</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('profit'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calculator fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MUDA (Monthly) Card -->
    <div class="col-xl-3 col-md-3 col-12 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            MUDA</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('sas'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-industry fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investor (Monthly) Card -->
    <div class="col-xl-3 col-md-3 col-12 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Investor</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('investor'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-syringe fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>    

    <!-- Profit Sharing (Monthly) Card -->
    <div class="col-xl-3 col-md-3 col-12 mb-4">
        <div class="card shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Bagi Hasil</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('profitSharing'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-handshake fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Mutasi</h6>
      </div>
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
        <div class="row mb-3">
          <div class="col-md-3 offset-md-9 col-12">
            <form action="{{ route($route.'.index') }}" method="get">
              @if(Auth::user()->unit->name == 'Management')
              <input type="hidden" name="unit" value="{{ $unit }}">
              @endif
              <input type="hidden" name="year" value="{{ $year }}">
              <input type="hidden" name="month" value="{{ $month }}">
              <input type="hidden" name="report" value="{{ $report }}">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fa fa-filter"></i>
                  </span>
                </div>
                <select aria-label="type" name="type" class="form-control rounded-right" id="typeOpt" onchange="if(this.value){ this.form.submit(); }" required="required">
                  <option value="all" {{ !$type || $type == 'all' ? 'selected' : '' }}>Semua</option>
                  <option value="in" {{ $type == 'in' ? 'selected' : '' }}>Pemasukan</option>
                  <option value="out" {{ $type == 'out' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
              </div>
            </form>
          </div>
        </div>
        @if($financeDetails && count($financeDetails) > 0)
        <div class="table-responsive">
          <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Nominal</th>
              </tr>
            </thead>
            <tbody>
              @php $no = 1; @endphp
              @foreach($financeDetails as $d)
              <tr>
                <td>{{ $d->date }}</td>
                <td>{{ $d->desc ? $d->desc : '' }}</td>
                <td class="{{ in_array($d->type_id,[1,3]) ? 'text-danger' : ( in_array($d->type_id,[2,4]) ? 'text-success' : '') }}">{{ (in_array($d->type_id,[1,3]) ? '-' : ( in_array($d->type_id,[2,4]) ? '+' : '')).$d->amountWithSeparator }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @else
        <div class="text-center mx-3 my-5">
          <h3 class="text-center">Mohon Maaf,</h3>
          <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($active) }} yang ditemukan</h6>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
<!--Row-->
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

@if(Auth::user()->unit->name == 'Management')
<!-- Report - Year -->
<script src="{{ asset('js/select-report-unit.js') }}"></script>

<!-- Report - Month -->
<script src="{{ asset('js/select-report-year.js') }}"></script>
@else
<!-- Report - Month -->
<script src="{{ asset('js/select-check-relation.js') }}"></script>
@endif

@if(Auth::user()->unit->name == 'Management')
<script type="text/javascript">
$(document).ready(function()
{
  selectDoubleCheckRelation('unit','year','month',false,'report',true);
});
</script>

@endif
<script type="text/javascript">
$(document).ready(function()
{
  selectCheckRelation('year','month',false,'report',true);
});
</script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.tooltip')
@endsection