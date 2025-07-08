@extends('template.main.master')

@section('title')
Chart
@endsection

@section('headmeta')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Chart</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Psb</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Chart</a></li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{route('kependidikan.psb..chart')}}" method="get">
                        @csrf
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Asal Calon</label>
                                <div class="col-sm-5">
                                    <select name="type" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                            <option value="1" {{1==$type?'selected':''}}>Internal</option>
                                            <option value="2" {{2==$type?'selected':''}}>Eksternal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Tahun Akademik</label>
                                <div class="col-sm-5">
                                    <select name="year" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @foreach ($year_list as $y)
                                            <option value="{{$y->id}}" {{$y->id==$year?'selected':''}}>{{$y->academic_year}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if(auth()->user()->pegawai->unit_id == 5)
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Unit</label>
                                <div class="col-sm-5">
                                    <select name="unit_id" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="1" {{$unit_id==1?'selected':''}}>TK</option>
                                        <option value="2" {{$unit_id==2?'selected':''}}>SD</option>
                                        <option value="3" {{$unit_id==3?'selected':''}}>SMP</option>
                                        <option value="4" {{$unit_id==4?'selected':''}}>SMA</option>
                                    </select>
                                </div>
                                @endif
                                <button class="btn btn-brand-green-dark btn-sm" type="submit">Saring</button>
                            </div>
                        </form>
                    </div>

                    {{-- Chart --}}

                    <div class="col-xl-8 col-lg-7">
                        <div class="card mb-4">
                          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Monthly Recap Report</h6>
                            <div class="dropdown no-arrow">
                              <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                              </a>
                              <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">Dropdown Header:</div>
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Something else here</a>
                              </div>
                            </div>
                          </div>
                          <div class="card-body">
                            <div class="chart-area">
                              <canvas id="myAreaChart"></canvas>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Row-->
@endsection

@section('footjs')
<script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
<script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('vendor/jquery-easing/jquery.easing.min.js')}}"></script>
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script>
// Area Chart Example
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ["Formulir", "Biaya Observasi", "Wawancara", "Diterima", "Lunas DU"],
    datasets: [{
      label: "Calon ",
      lineTension: 0.3,
      backgroundColor: "rgba(78, 115, 223, 0.5)",
      borderColor: "rgba(78, 115, 223, 1)",
      pointRadius: 3,
      pointBackgroundColor: "rgba(78, 115, 223, 1)",
      pointBorderColor: "rgba(78, 115, 223, 1)",
      pointHoverRadius: 3,
      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
      pointHitRadius: 10,
      pointBorderWidth: 2,
      data: @json($data),
    }],
  },
  options: {
    maintainAspectRatio: false,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: {
      xAxes: [{
        time: {
          unit: 'date'
        },
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 7
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          // Include a dollar sign in the ticks
          callback: function(value, index, values) {
            return (value);
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }],
    },
    legend: {
      display: false
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem, chart) {
          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': ' + tooltipItem.yLabel;
        }
      }
    }
  }
});
</script>
@endsection