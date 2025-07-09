<<<<<<< HEAD
@extends('template.main.master')

@section('title')
IKU {{ $category->name }}
@endsection

@section('headmeta')
<!-- Bootstrap Toggle -->
<link href="{{ asset('vendor/bootstrap4-toggle/css/bootstrap4-toggle.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
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
    <h1 class="h3 mb-0 text-gray-800">IKU {{ $category->name }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.index') }}">Indikator Kinerja Utama</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.'.$category->nameLc.'.index') }}">{{ $category->name }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]) }}">{{ $tahun->academic_year }}</a></li>
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
                        @if($t->is_active == 1 || $t->nilaiIku()->count() > 0)
                        <option value="{{ $t->academicYearLink }}" {{ $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                        @endif
                        @endforeach
                      </select>
                      <a href="{{ route('iku.'.$category->nameLc.'.index') }}" id="btn-select-year" class="btn btn-brand-purple ml-2 pt-2" data-href="{{ route('iku.'.$category->nameLc.'.index') }}">Pilih</a>
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

@if($unitAktif->name != 'TK')
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Chart</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Chart" name="chart" class="form-control" id="chartOpt">
                      @foreach($chartList as $l)
                      <option value="{{ $l['link'] }}" {{ $chart == $l['link'] ? 'selected' : '' }}>{{ $l['name'] }}</option>
                      @endforeach
                    </select>
                    <a href="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" id="btn-select-chart" class="btn btn-brand-purple ml-2 pt-2" data-href="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}">Pilih</a>
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

@if(isset($chart))
@if($chart != 'usp')
<div class="row mb-4">
  <div class="col-12"> 
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="semesterOpt" class="form-control-label">Semester</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="semesterOpt">
                    @foreach($tahun->semester()->select('semester_id','semester')->get() as $s)
                    <option value="{{ explode('-',$s->semester_id)[1] }}" {{ $semester->semester_id == $s->semester_id ? 'selected' : '' }}>{{ $s->semester }}</option>
                    @endforeach
                    </select>
                    <a href="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'chart' => $chart]) }}" id="btn-select-semester" class="btn btn-brand-purple ml-2 pt-2" data-href="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'chart' => $chart]) }}">Atur</a>
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
@endif

@if(isset($semester))
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="score" class="form-control-label">Nilai Tercapai</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <form action="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" method="get">
                        <input type="hidden" name="chart" value="{{ $chart }}">
                        <input type="hidden" name="semester" value="{{ explode('-',$semester->semester_id)[1] }}">
                        <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="mdi mdi-greater-than-or-equal"></i></span>
                            </div>
                            <input type="number" name="score" class="form-control" min="1" max="{{ $chart == 'iklas' ? '5' : '100' }}" value="{{ (isset($score) && ($score > 0)) ? $score : '1' }}" aria-label="Score" aria-describedby="greater-than" required>
                            @if($chart == 'iklas')
                            <div class="input-group-append">
                              <span class="input-group-text""><i class="fa fa-star"></i></span>
                            </div>
                            @endif
                            <button type="submit" class="btn btn-brand-purple ml-2 pt-2">Lihat</button>
                        </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

@if(isset($score) && isset($datasets))
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-purple">Grafik</h6>
            </div>
            <div class="card-body p-3">
                <div class="chartWrapper">
                    <div class="chartAreaWrapper">
                        <div class="chartAreaInnerWrapper">
                            <canvas id="percentBarChart"></canvas>
                        </div>
                    </div>
                    <canvas id="percentBarChartAxis" height="350" width="0"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endif
@endif
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-purple">IKU {{ $category->name }}</h6>
            </div>
            <div class="card-body p-3">
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
              @if($aspectUnits && count($aspectUnits) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek</th>
                      <th>Indikator Kinerja Utama</th>
                      <th>Objek</th>
                      <th>Alat Ukur</th>
                      <th>Target</th>
                      <th>Capaian</th>
                      <th>Berkas</th>
                      <th>Pranala</th>
                      <th>Catatan</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($aspectUnits as $a)
                    @php
                    $indicators = $a->indikator()->where('director_acc_status_id',1)->whereHas('nilai.iku',function($q)use($category,$tahun,$unitAktif){
                      $q->where([
                        'iku_category_id' => $category->id,
                        'academic_year_id' => $tahun->id,
                        'unit_id' => $unitAktif->id
                      ]);
                    });
                    @endphp
                    @if($indicators->count() > 0)
                    @foreach($indicators->get() as $i)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $i->aspek->aspek->name }}</td>
                      <td>{{ $i->name }}</td>
                      <td>{{ $i->object }}</td>
                      <td>{{ $i->mt }}</td>
                      <td>{{ $i->target }}</td>
                       @php
                        $nilaiIndikator = $nilai ? $nilai->detail()->where('indicator_id',$i->id)->first() : null;
                        @endphp
                      <td>
                        @if($nilaiIndikator && ($nilaiIndikator->is_achieved == 1))
                        <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Tercapai"></i>
                        @elseif($nilaiIndikator && ($nilaiIndikator->is_achieved != 1))
                        <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="Tidak Tercapai"></i>
                        @else
                        <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum diketahui"></i>
                        @endif
                      </td>
                      <td>
                        @if($nilaiIndikator && $nilaiIndikator->attachment)
                        <a href="{{ asset('upload/iku/'.$category->nameLc.'/'.$tahun->academicYearLink.'/'.$unitAktif->name.'/'.$nilaiIndikator->attachment) }}" class="btn btn-sm btn-success" target="_blank"><i class="fas fa-download mr-1"></i>Unduh</a>
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($nilaiIndikator && $nilaiIndikator->link)
                        <a href="{{ $nilaiIndikator->link }}" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-link mr-1"></i>Buka</a>
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($nilaiIndikator && $nilaiIndikator->note)
                        {{ $nilaiIndikator->note }}
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($nilai)
                        @if($nilaiIndikator && $nilaiIndikator->director_acc_status_id == 1)
                        <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($nilaiIndikator->accDirektur) ? 'Anda' : $nilaiIndikator->accDirektur->name }}<br>{{ date('j M Y H.i.s', strtotime($nilaiIndikator->director_acc_time)) }}"></i>
                        @else
                        <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan Anda"></i>
                        @endif
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if(!$nilaiIndikator || ($nilaiIndikator && $nilaiIndikator->director_acc_status_id != 1))
                        <a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#validate-confirm" onclick="validateModal('{{ addslashes(htmlspecialchars($i->name)) }}', '{{ route('iku.'.$category->nameLc.'.setujui', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name,'id' => $i->id]) }}')"><i class="fas fa-check"></i></a>
                        @else
                        <span class="badge badge-success font-weight-normal">Telah Disetujui</span>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data aspek IKU yang ditemukan</h6>
              </div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="validate-confirm" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
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
        Apakah Anda yakin ingin menyetujui capaian IKU <span class="name font-weight-bold"></span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <form action="#" id="validate-link" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.iku.change-chart-education')
@if(isset($chart) && $chart != 'usp')
@include('template.footjs.kepegawaian.iku.change-semester-education')
@endif
@include('template.footjs.keuangan.change-year')
@include('template.footjs.modal.get_iku_indicator_validate')

@if(isset($chart) && isset($semester) && isset($score) && isset($datasets))
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
var ctx = document.getElementById("percentBarChart").getContext('2d');
var labels_array = {!! isset($matapelajarans) ? $matapelajarans->pluck('subject_acronym') : (isset($refIklas) ? $refIklas->pluck('categoryNumberCategory') : null) !!};
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
          max: 100,
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
    },
    animation: {
        onComplete: function (animation) {
            var sourceCanvas = this.chart.canvas;
            // the -5 is so that we don't copy the edges of the line
            var copyWidth = this.scales['y-axis-0'].width - 10;
            // the +5 is so that the bottommost y axis label is not clipped off
            // we could factor this in using measureText if we wanted to be generic
            var copyHeight = this.scales['y-axis-0'].height + this.scales['y-axis-0'].top + 10;
            var targetCtx = document.getElementById("percentBarChartAxis").getContext("2d");
            targetCtx.canvas.width = copyWidth;
            targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth, copyHeight, 0, 0, copyWidth, copyHeight);
        }
    }
  }
});
</script>
@endif
=======
@extends('template.main.master')

@section('title')
IKU {{ $category->name }}
@endsection

@section('headmeta')
<!-- Bootstrap Toggle -->
<link href="{{ asset('vendor/bootstrap4-toggle/css/bootstrap4-toggle.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
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
    <h1 class="h3 mb-0 text-gray-800">IKU {{ $category->name }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.index') }}">Indikator Kinerja Utama</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.'.$category->nameLc.'.index') }}">{{ $category->name }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.'.$category->nameLc.'.index', ['tahun' => $tahun->academicYearLink]) }}">{{ $tahun->academic_year }}</a></li>
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
                        @if($t->is_active == 1 || $t->nilaiIku()->count() > 0)
                        <option value="{{ $t->academicYearLink }}" {{ $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                        @endif
                        @endforeach
                      </select>
                      <a href="{{ route('iku.'.$category->nameLc.'.index') }}" id="btn-select-year" class="btn btn-brand-purple ml-2 pt-2" data-href="{{ route('iku.'.$category->nameLc.'.index') }}">Pilih</a>
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

@if($unitAktif->name != 'TK')
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Chart</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Chart" name="chart" class="form-control" id="chartOpt">
                      @foreach($chartList as $l)
                      <option value="{{ $l['link'] }}" {{ $chart == $l['link'] ? 'selected' : '' }}>{{ $l['name'] }}</option>
                      @endforeach
                    </select>
                    <a href="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" id="btn-select-chart" class="btn btn-brand-purple ml-2 pt-2" data-href="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}">Pilih</a>
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

@if(isset($chart))
@if($chart != 'usp')
<div class="row mb-4">
  <div class="col-12"> 
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="semesterOpt" class="form-control-label">Semester</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="semesterOpt">
                    @foreach($tahun->semester()->select('semester_id','semester')->get() as $s)
                    <option value="{{ explode('-',$s->semester_id)[1] }}" {{ $semester->semester_id == $s->semester_id ? 'selected' : '' }}>{{ $s->semester }}</option>
                    @endforeach
                    </select>
                    <a href="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'chart' => $chart]) }}" id="btn-select-semester" class="btn btn-brand-purple ml-2 pt-2" data-href="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'chart' => $chart]) }}">Atur</a>
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
@endif

@if(isset($semester))
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="score" class="form-control-label">Nilai Tercapai</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <form action="{{ route('iku.edukasi.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" method="get">
                        <input type="hidden" name="chart" value="{{ $chart }}">
                        <input type="hidden" name="semester" value="{{ explode('-',$semester->semester_id)[1] }}">
                        <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="mdi mdi-greater-than-or-equal"></i></span>
                            </div>
                            <input type="number" name="score" class="form-control" min="1" max="{{ $chart == 'iklas' ? '5' : '100' }}" value="{{ (isset($score) && ($score > 0)) ? $score : '1' }}" aria-label="Score" aria-describedby="greater-than" required>
                            @if($chart == 'iklas')
                            <div class="input-group-append">
                              <span class="input-group-text""><i class="fa fa-star"></i></span>
                            </div>
                            @endif
                            <button type="submit" class="btn btn-brand-purple ml-2 pt-2">Lihat</button>
                        </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

@if(isset($score) && isset($datasets))
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-purple">Grafik</h6>
            </div>
            <div class="card-body p-3">
                <div class="chartWrapper">
                    <div class="chartAreaWrapper">
                        <div class="chartAreaInnerWrapper">
                            <canvas id="percentBarChart"></canvas>
                        </div>
                    </div>
                    <canvas id="percentBarChartAxis" height="350" width="0"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endif
@endif
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-purple">IKU {{ $category->name }}</h6>
            </div>
            <div class="card-body p-3">
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
              @if($aspectUnits && count($aspectUnits) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek</th>
                      <th>Indikator Kinerja Utama</th>
                      <th>Objek</th>
                      <th>Alat Ukur</th>
                      <th>Target</th>
                      <th>Capaian</th>
                      <th>Berkas</th>
                      <th>Pranala</th>
                      <th>Catatan</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($aspectUnits as $a)
                    @php
                    $indicators = $a->indikator()->where('director_acc_status_id',1)->whereHas('nilai.iku',function($q)use($category,$tahun,$unitAktif){
                      $q->where([
                        'iku_category_id' => $category->id,
                        'academic_year_id' => $tahun->id,
                        'unit_id' => $unitAktif->id
                      ]);
                    });
                    @endphp
                    @if($indicators->count() > 0)
                    @foreach($indicators->get() as $i)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $i->aspek->aspek->name }}</td>
                      <td>{{ $i->name }}</td>
                      <td>{{ $i->object }}</td>
                      <td>{{ $i->mt }}</td>
                      <td>{{ $i->target }}</td>
                       @php
                        $nilaiIndikator = $nilai ? $nilai->detail()->where('indicator_id',$i->id)->first() : null;
                        @endphp
                      <td>
                        @if($nilaiIndikator && ($nilaiIndikator->is_achieved == 1))
                        <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Tercapai"></i>
                        @elseif($nilaiIndikator && ($nilaiIndikator->is_achieved != 1))
                        <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="Tidak Tercapai"></i>
                        @else
                        <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum diketahui"></i>
                        @endif
                      </td>
                      <td>
                        @if($nilaiIndikator && $nilaiIndikator->attachment)
                        <a href="{{ asset('upload/iku/'.$category->nameLc.'/'.$tahun->academicYearLink.'/'.$unitAktif->name.'/'.$nilaiIndikator->attachment) }}" class="btn btn-sm btn-success" target="_blank"><i class="fas fa-download mr-1"></i>Unduh</a>
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($nilaiIndikator && $nilaiIndikator->link)
                        <a href="{{ $nilaiIndikator->link }}" class="btn btn-sm btn-primary" target="_blank"><i class="fas fa-link mr-1"></i>Buka</a>
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($nilaiIndikator && $nilaiIndikator->note)
                        {{ $nilaiIndikator->note }}
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($nilai)
                        @if($nilaiIndikator && $nilaiIndikator->director_acc_status_id == 1)
                        <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($nilaiIndikator->accDirektur) ? 'Anda' : $nilaiIndikator->accDirektur->name }}<br>{{ date('j M Y H.i.s', strtotime($nilaiIndikator->director_acc_time)) }}"></i>
                        @else
                        <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan Anda"></i>
                        @endif
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if(!$nilaiIndikator || ($nilaiIndikator && $nilaiIndikator->director_acc_status_id != 1))
                        <a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#validate-confirm" onclick="validateModal('{{ addslashes(htmlspecialchars($i->name)) }}', '{{ route('iku.'.$category->nameLc.'.setujui', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name,'id' => $i->id]) }}')"><i class="fas fa-check"></i></a>
                        @else
                        <span class="badge badge-success font-weight-normal">Telah Disetujui</span>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data aspek IKU yang ditemukan</h6>
              </div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="validate-confirm" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
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
        Apakah Anda yakin ingin menyetujui capaian IKU <span class="name font-weight-bold"></span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <form action="#" id="validate-link" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.iku.change-chart-education')
@if(isset($chart) && $chart != 'usp')
@include('template.footjs.kepegawaian.iku.change-semester-education')
@endif
@include('template.footjs.keuangan.change-year')
@include('template.footjs.modal.get_iku_indicator_validate')

@if(isset($chart) && isset($semester) && isset($score) && isset($datasets))
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
var ctx = document.getElementById("percentBarChart").getContext('2d');
var labels_array = {!! isset($matapelajarans) ? $matapelajarans->pluck('subject_acronym') : (isset($refIklas) ? $refIklas->pluck('categoryNumberCategory') : null) !!};
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
          max: 100,
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
    },
    animation: {
        onComplete: function (animation) {
            var sourceCanvas = this.chart.canvas;
            // the -5 is so that we don't copy the edges of the line
            var copyWidth = this.scales['y-axis-0'].width - 10;
            // the +5 is so that the bottommost y axis label is not clipped off
            // we could factor this in using measureText if we wanted to be generic
            var copyHeight = this.scales['y-axis-0'].height + this.scales['y-axis-0'].top + 10;
            var targetCtx = document.getElementById("percentBarChartAxis").getContext("2d");
            targetCtx.canvas.width = copyWidth;
            targetCtx.drawImage(sourceCanvas, 0, 0, copyWidth, copyHeight, 0, 0, copyWidth, copyHeight);
        }
    }
  }
});
</script>
@endif
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection