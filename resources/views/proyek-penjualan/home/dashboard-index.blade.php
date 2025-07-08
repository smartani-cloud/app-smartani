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
    <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
  </ol>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Income (Monthly) Card -->
    <div class="col-xl-4 col-md-4 col-12 mb-4">
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
    <div class="col-xl-4 col-md-4 col-12 mb-4">
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
    <!-- SAS (Monthly) Card -->
    <div class="col-xl-4 col-md-4 col-12 mb-4">
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
    <!-- Tax (Monthly) Card -->
    <div class="col-xl-4 col-md-4 col-12 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pajak</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financeUnit ? number_format(Auth::user()->unit_id == 1 && $unit && $unit != 'all' ? $financeDetails->where('unit_id',$unit)->where('type_id',3)->sum('amount'): $financeDetails->where('type_id',3)->sum('amount'), 0, ',', '.') : '0' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-percentage fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <!-- SAS (Monthly) Card -->
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
      <div class="card-body mx-4 my-2">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-sm text-primary mb-1">
            Selamat Datang,</div>
            <div class="h2 mb-0 font-weight-bold text-gray-800">{{ Auth::user()->name }}</div>
          </div>
          <div class="col-auto">
            <img class="img-fluid px-3 px-sm-4 my-3" style="width: 25rem;" src="img/undraw_city_life_gnpr.svg" alt="Welcome">
          </div>
        </div>
        <div class="text-center">
          
          
        </div>
      </div>
    </div>
  </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.global.tooltip')
@endsection