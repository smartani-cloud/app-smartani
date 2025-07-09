@extends('template.main.master')

@section('title')
Realisasi
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Realisasi</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index')}}">Realisasi</a></li>
    @if($jenisAktif)
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ !$isYear ? $tahun->academic_year : $tahun }}</li>
    @endif
  </ol>
</div>
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
    @if($jenisAktif == $j)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-brand-green">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled="disabled">Pilih</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    @php
    $anggaranCount = $jenisAnggaranCount->where('id',$j->id)->pluck('anggaranCount')->values()->first();
    @endphp
    @if($anggaranCount > 0)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-brand-green">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('realisasi.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-secondary">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary disabled"role="button" aria-disabled="true">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
    @endforeach
</div>
--}}
@if($jenisAktif)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @if($years && count($years) > 0)
                      @foreach($years as $y)
                        <option value="{{ $y }}" {{ $isYear && $tahun == $y ? 'selected' : ''}}>{{ $y }}</option>
                      @endforeach
                      @elseif($isYear)
                      @if($tahun != date('Y'))
                      <option value="" disabled="disabled" selected>Pilih</option>
                      @endif
                      <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                      @if((!$academicYears && !$isYear) || ($academicYears && count($academicYears) > 0))
                      @foreach($tahunPelajaran as $t)
                      <option value="{{ $t->academicYearLink }}" {{ !$isYear && $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                      @endif
                    </select>
                    <a href="{{ route('realisasi.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('realisasi.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

@if(isset($datasets))
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-brand-green">Grafik</h6>
            </div>
            <div class="card-body p-3">
                <div class="chart-bar financeChartAreaInnerWrapper">
                  <canvas id="budgetingBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $total && $total->get('pendapatanPembiayaan') > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-coins text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Pendapatan & Pembiayaan</div>
                        <h6 id="summary" class="mb-0">
                            {{ number_format($total->get('pendapatanPembiayaan'), 0, ',', '.') }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $total && $total->get('belanja') > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Belanja</div>
                        <h6 id="summary" class="mb-0">
                            {{ number_format($total->get('belanja'), 0, ',', '.') }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $total && $total->get('operasionalPembiayaan') > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-wallet text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Sisa Saldo</div>
                        <h6 class="mb-0">
                            {{ number_format($total->get('operasionalPembiayaan'), 0, ',', '.') }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Detail Anggaran</h6>
            </div>
            <div class="card-body p-3">
                @if($apby && count($apby) > 0)
                <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Anggaran</th>
                      <th>Rencana</th>
                      <th>Realisasi</th>
                      {{--
                      <th>Realisasi PPB</th>
                      <th>Realisasi RPPA</th>
                      --}}
                      <th>Selisih</th>
                      {{--
                      <th>Selisih PPB</th>
                      <th>Selisih RPPA</th>
                      --}}
                      <th style="width: 100px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($apby as $a)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $a->jenisAnggaranAnggaran->anggaran->name }}</td>
                      <td>{{ $a->totalValueWithSeparator }}</td>
                      <td>{{ $a->totalUsedWithSeparator }}</td>
                      {{--
                      <td>{{ $ppbValue[$a->jenisAnggaranAnggaran->id]['used'] }}</td>
                      <td>{{ $rppaValue[$a->jenisAnggaranAnggaran->id]['used'] }}</td>
                      --}}
                      <td>{{ $a->totalBalanceWithSeparator }}</td>
                      {{--
                      <td>{{ $ppbValue[$a->jenisAnggaranAnggaran->id]['balance'] }}</td>
                      <td>{{ $rppaValue[$a->jenisAnggaranAnggaran->id]['balance'] }}</td>
                      --}}
                      <td>
                        <a href="{{ route('realisasi.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $a->jenisAnggaranAnggaran->anggaran->link])}}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-eye"></i></a>
                      </td>
                    </tr>
                    @endforeach
                </table>    
                </div>
                @else
                <div class="text-center mx-3 my-5">
                    <h3>Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data anggaran yang ditemukan</h6>
                </div>
                @endif
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
</div>
@endif

<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')

@if(isset($datasets))
<script>
// Set new default font family and font color to mimic Bootstrap's default styling
Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = '#858796';

function number_format(number, decimals, dec_point, thousands_sep) {
  // *     example: number_format(1234.56, 2, ',', ' ');
  // *     return: '1 234,56'
  number = (number + '').replace(',', '').replace(' ', '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function(n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

// Percent Bar Chart
var ctx = document.getElementById("budgetingBarChart").getContext('2d');
var labels_array = {!! isset($budgetings) ? $budgetings->pluck('anggaran')->pluck('name') : null !!};
var data_array = {!! json_encode($datasets->pluck('data')->flatten()->all()) !!};
var unitBarChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: labels_array,
    datasets: {!! json_encode($datasets) !!},
  },
  plugins: {
    beforeInit: function(chart, options) {
      chart.legend.afterFit = function() {
        this.height = this.height + 15;
      };
    }
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 0,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          autoSkip: false,
          maxTicksLimit: 6
        },
        maxBarThickness: 30,
      }],
      yAxes: [{
        ticks: {
          min: 0,
          padding: 10
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2],
          lineWidth: 3
        }
      }],
    },
    legend: {
      display: true
    },
    tooltips: {
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': ' + number_format(tooltipItem.yLabel);
        }
      }
    }
  }
});
</script>
@endif
@endsection