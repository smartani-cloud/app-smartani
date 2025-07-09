<<<<<<< HEAD
@extends('proyek-penjualan.finance.report.project-show')

@section('card')
<!-- Content Row -->
<div class="row">
    <!-- Income (Monthly) Card -->
    <div class="col-xl-6 col-md-6 col-12 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Pemasukan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('income'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outcome (Monthly) Card -->
    <div class="col-xl-6 col-md-6 col-12 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Pengeluaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('outcome'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
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
        <h6 class="m-0 font-weight-bold text-primary">Komparasi Proyeksi</h6>
      </div>
      <div class="card-body">
        @if($values->get('product') && $values->get('product')->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 30%">Produk</th>
                <th style="width: 25%">Proyeksi</th>
                <th style="width: 10%">%</th>
                <th style="width: 25%">Faktual</th>
                <th style="width: 10%">%</th>
              </tr>
            </thead>
            <tbody>
              @foreach((object)$values->get('product') as $d)
              <tr>
                <td>{{ $d['name'] }}</td>
                <td>{{ number_format($d['value'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['percentage'], 1, ',', '.') }}</td>
                <td>{{ number_format($d['factual_value'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['factual_percentage'], 1, ',', '.') }}</td>
              </tr>
              @endforeach
              <tr>
                <td class="font-weight-bold">Total Penjualan</td>
                <td class="font-weight-bold">{{ number_format($total->get('product'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('product') && $total->get('product') > 0 ? number_format(($total->get('product')/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold">{{ number_format($factual->get('product'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $factual->get('product') && $factual->get('product') > 0 ? number_format(($factual->get('product')/$factual->get('product'))*100, 1, ',', '.') : 0 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        @endif
        @if($values->get('operational') && $values->get('operational')->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 30%">Operasional</th>
                <th style="width: 25%">Proyeksi</th>
                <th style="width: 10%">%</th>
                <th style="width: 25%">Faktual</th>
                <th style="width: 10%">%</th>
              </tr>
            </thead>
            <tbody>
              @foreach((object)$values->get('operational') as $d)
              <tr>
                <td>{{ $d['name'] }}</td>
                <td>{{ number_format($d['value'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['percentage'], 1, ',', '.') }}</td>
                <td>{{ number_format($d['factual_value'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['factual_percentage'], 1, ',', '.') }}</td>
              </tr>
              @endforeach
              <tr>
                <td class="font-weight-bold">Total Biaya Operasional</td>
                <td class="font-weight-bold">{{ number_format($total->get('operational'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('operational') && $total->get('operational') > 0 ? number_format(($total->get('operational')/$total->get('operational'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold">{{ number_format($factual->get('operational'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $factual->get('operational') && $factual->get('operational') > 0 ? number_format(($factual->get('operational')/$factual->get('operational'))*100, 1, ',', '.') : 0 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
<!--Row-->

=======
@extends('proyek-penjualan.finance.report.project-show')

@section('card')
<!-- Content Row -->
<div class="row">
    <!-- Income (Monthly) Card -->
    <div class="col-xl-6 col-md-6 col-12 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Pemasukan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('income'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-circle-down fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Outcome (Monthly) Card -->
    <div class="col-xl-6 col-md-6 col-12 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Pengeluaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($total->get('outcome'), 0, ',', '.') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-arrow-circle-up fa-2x text-gray-300"></i>
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
        <h6 class="m-0 font-weight-bold text-primary">Komparasi Proyeksi</h6>
      </div>
      <div class="card-body">
        @if($values->get('product') && $values->get('product')->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 30%">Produk</th>
                <th style="width: 25%">Proyeksi</th>
                <th style="width: 10%">%</th>
                <th style="width: 25%">Faktual</th>
                <th style="width: 10%">%</th>
              </tr>
            </thead>
            <tbody>
              @foreach((object)$values->get('product') as $d)
              <tr>
                <td>{{ $d['name'] }}</td>
                <td>{{ number_format($d['value'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['percentage'], 1, ',', '.') }}</td>
                <td>{{ number_format($d['factual_value'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['factual_percentage'], 1, ',', '.') }}</td>
              </tr>
              @endforeach
              <tr>
                <td class="font-weight-bold">Total Penjualan</td>
                <td class="font-weight-bold">{{ number_format($total->get('product'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('product') && $total->get('product') > 0 ? number_format(($total->get('product')/$total->get('product'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold">{{ number_format($factual->get('product'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $factual->get('product') && $factual->get('product') > 0 ? number_format(($factual->get('product')/$factual->get('product'))*100, 1, ',', '.') : 0 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        @endif
        @if($values->get('operational') && $values->get('operational')->count() > 0)
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 30%">Operasional</th>
                <th style="width: 25%">Proyeksi</th>
                <th style="width: 10%">%</th>
                <th style="width: 25%">Faktual</th>
                <th style="width: 10%">%</th>
              </tr>
            </thead>
            <tbody>
              @foreach((object)$values->get('operational') as $d)
              <tr>
                <td>{{ $d['name'] }}</td>
                <td>{{ number_format($d['value'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['percentage'], 1, ',', '.') }}</td>
                <td>{{ number_format($d['factual_value'], 0, ',', '.') }}</td>
                <td>{{ number_format($d['factual_percentage'], 1, ',', '.') }}</td>
              </tr>
              @endforeach
              <tr>
                <td class="font-weight-bold">Total Biaya Operasional</td>
                <td class="font-weight-bold">{{ number_format($total->get('operational'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $total->get('operational') && $total->get('operational') > 0 ? number_format(($total->get('operational')/$total->get('operational'))*100, 1, ',', '.') : 0 }}</td>
                <td class="font-weight-bold">{{ number_format($factual->get('operational'), 0, ',', '.') }}</td>
                <td class="font-weight-bold">{{ $factual->get('operational') && $factual->get('operational') > 0 ? number_format(($factual->get('operational')/$factual->get('operational'))*100, 1, ',', '.') : 0 }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
<!--Row-->

>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection