@extends('template.main.master')

@section('title')
IKU Pegawai
@endsection

@section('headmeta')
<!-- Bootstrap Toggle -->
<link href="{{ asset('vendor/bootstrap4-toggle/css/bootstrap4-toggle.min.css') }}" rel="stylesheet">
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('sidebar')
@php
$role = Auth::user()->role->name;
@endphp
@if(in_array($role,['admin','am','aspv','direktur','etl','etm','fam','faspv','kepsek','pembinayys','ketuayys','wakasek']))
@include('template.sidebar.kepegawaian.'.$role)
@else
@include('template.sidebar.kepegawaian.employee')
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">IKU Pegawai</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.index') }}">Indikator Kinerja Utama</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.pegawai.index') }}">Pegawai</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.pegawai.index', ['tahun' => $tahun->academicYearLink]) }}">{{ $tahun->academic_year }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $unitAktif->name }}</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="input-group">
                      <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt" required="required">
                        @foreach($tahunPelajaran as $t)
                        @if($t->is_active == 1 || $t->nilaiPsc()->count() > 0)
                        <option value="{{ $t->academicYearLink }}" {{ $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                        @endif
                        @endforeach
                      </select>
                      <a href="{{ route('iku.pegawai.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('iku.pegawai.index') }}">Pilih</a>
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

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-school text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Unit</div>
                        <h6 class="mb-0">{{ $unitAktif->name }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-brand-green">Grafik</h6>
      </div>
      <div class="card-body">
        <div class="chart-bar">
          <canvas id="unitBarChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</div>

@if(isset($selectedScores) && count($selectedScores) > 0)
@foreach($selectedScores as $s)
@if($s != 'C')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                @if(in_array($s,['C+']))
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Pegawai yang Mendapat C+ / C</h6>
                @else
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Pegawai yang Mendapat {{ $s }}</h6>
                @endif
            </div>
            <div class="card-body p-3">
              @if(in_array($s,['C+']))
              @if($nilai && $nilai->whereIn('grade_name',['C+','C'])->count() > 0)
              <div class="table-responsive">
                <table id="dataTable-{{ $s }}" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIPY</th>
                      <th>Jabatan</th>
                      <th>Masa Kerja</th>
                      <th>Status Pegawai</th>
                      <th>Jumlah Nilai</th>
                      <th>Grade</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1 @endphp
                    @foreach($nilai->whereIn('grade_name',['C+','C'])->all() as $p)
                    <tr>
                       <td>{{ $no++ }}</td>
                       <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->pegawai->showPhoto) }}" alt="user-{{ $p->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->pegawai->name }}
                        </a>
                      </td>
                      <td>{{ $p->pegawai->nip }}</td>
                      <td>{{ $p->pegawai->units()->where('unit_id',$unitAktif->id)->count() > 0 ? implode(', ',$p->pegawai->units()->where('unit_id',$unitAktif->id)->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->toArray()) : '' }}</td>
                      <td>{{ $p->pegawai->yearsOfService }}</td>
                      <td>{{ $p->pegawai->statusPegawai->show_name }}</td>
                      <td>{{ $p->total_score }}</td>
                      <td>{{ $p->grade_name }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3><i class="fa fa-info-circle text-secondary"></i></h3>
                <h6 class="font-weight-light mb-3">Belum ada pegawai yang mendapatkan nilai {{ $s }}</h6>
              </div>
              @endif
              @else
              @if($nilai && $nilai->where('grade_name',$s)->count() > 0)
              <div class="table-responsive">
                <table id="dataTable-{{ $s }}" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIPY</th>
                      <th>Jabatan</th>
                      <th>Masa Kerja</th>
                      <th>Status Pegawai</th>
                      <th>Jumlah Nilai</th>
                      <th>Grade</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1 @endphp
                    @foreach($nilai->where('grade_name',$s)->all() as $p)
                    <tr>
                       <td>{{ $no++ }}</td>
                       <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->pegawai->showPhoto) }}" alt="user-{{ $p->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->pegawai->name }}
                        </a>
                      </td>
                      <td>{{ $p->pegawai->nip }}</td>
                      <td>{{ $p->pegawai->units()->where('unit_id',$unitAktif->id)->count() > 0 ? implode(', ',$p->pegawai->units()->where('unit_id',$unitAktif->id)->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->toArray()) : '' }}</td>
                      <td>{{ $p->pegawai->yearsOfService }}</td>
                      <td>{{ $p->pegawai->statusPegawai->show_name }}</td>
                      <td>{{ $p->total_score }}</td>
                      <td>{{ $p->grade_name }}</td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3><i class="fa fa-info-circle text-secondary"></i></h3>
                <h6 class="font-weight-light mb-3">Belum ada pegawai yang mendapatkan nilai {{ $s }}</h6>
              </div>
              @endif
              @endif
            </div>
        </div>
    </div>
</div>
@endif
@endforeach
@endif
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@if(isset($selectedScores) && count($selectedScores) > 0)
@foreach($selectedScores as $s)
<script>
  $(document).ready(function () {
    $('#dataTable-{{ $s }}').DataTable(); // ID From dataTable
  });
</script>
@endforeach
@endif
@include('template.footjs.keuangan.change-year')
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
var ctx = document.getElementById("unitBarChart").getContext('2d');
var labels_array = {!! isset($set) ? $set->gradeSorted : null !!};
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
          max: Math.max.apply(this, data_array) + 5,
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
@endsection