@extends('proyek-penjualan.finance.report.project-show')

@section('card')
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.show',['id' => $data->id]) }}" id="viewItemForm" method="get">
          <input type="hidden" name="report" value="{{ $report }}">
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
                      @else
                      <option value="all" {{ old('year',$year) == 'all' ? 'selected' : '' }}>Semua</option>
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

<!-- Content Row -->
<div class="row">
    @php
    $firstRowCol = $report == 'faktual' ? 4 : 6;
    @endphp
    <!-- Income (Monthly) Card -->
    <div class="col-xl-{{ $firstRowCol }} col-md-{{ $firstRowCol }} col-12 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Pemasukan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financeTotal->count() > 0 ? number_format($total->get('income'), 0, ',', '.') : '0' }}</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financeTotal->count() > 0 ? number_format($total->get('outcome'), 0, ',', '.') : '0' }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($report == 'faktual')
    <!-- Tax (Monthly) Card -->
    <div class="col-xl-{{ $firstRowCol }} col-md-{{ $firstRowCol }} col-12 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pajak</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $financeTotal->count() > 0 ? number_format(($financeTotal->where('unit_id',$data->unit_id)->where('type_id',3)->sum('amount') - $financeTotal->where('unit_id',$data->unit_id)->where('type_id',3)->sum('amount')), 0, ',', '.') : '0' }}</div>
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
            <form action="{{ route($route.'.show',['id' => $data->id]) }}" method="get">
              <input type="hidden" name="report" value="{{ $report }}">
              <input type="hidden" name="year" value="{{ $year }}">
              <input type="hidden" name="month" value="{{ $month }}">
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

@endsection