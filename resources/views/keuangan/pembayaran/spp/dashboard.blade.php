@extends('template.main.master')

@section('title')
Sumbangan Pembinaan Pendidikan
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Sumbangan Pembinaan Pendidikan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sumbangan Pembinaan Pendidikan</li>
    </ol>
</div>

<div class="col-lg-12">
    <div class="card mb-4">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Pembayaran</h6>
      </div>
      <div class="card-body">
        <div class="col-md-8">
          @csrf
          <div class="form-group row">
              <label for="kelas" class="col-sm-2 control-label">Unit</label>
              <div class="col-sm-5">
                  <select name="unit_id" class="select2 form-control select2-hidden-accessible auto_width" id="unit_id" style="width:40%;" tabindex="-1" aria-hidden="true">
                      @foreach (getUnits() as $index => $units)
                          <option value="{{$units->id}}" {{$index==0?'selected':''}}>{{$units->name}}</option>
                      @endforeach
                  </select>
              </div>
          </div>
          <div class="form-group row">
              <label for="year" class="col-sm-2 control-label">Mulai</label>
              <div class="col-sm-5">
                  <select name="year_start" class="select2 form-control select2-hidden-accessible auto_width" id="year_start" style="width:40%;" tabindex="-1" aria-hidden="true">
                      @foreach (yearList() as $index => $year_list)
                      <option value="{{$year_list}}" {{$index==1?'selected':''}}>{{$year_list}}</option>
                      @endforeach
                  </select>
                  <select name="month_start" class="select2 form-control select2-hidden-accessible auto_width" id="month_start" style="width:40%;" tabindex="-1" aria-hidden="true">
                      @foreach (monthList() as $months)
                          <option value="{{$months->id}}">{{$months->name}}</option>
                      @endforeach
                  </select>
              </div>
          </div>
          <div class="form-group row">
              <label for="year" class="col-sm-2 control-label">Sampai</label>
              <div class="col-sm-5">
                  <select name="year_end" class="select2 form-control select2-hidden-accessible auto_width" id="year_end" style="width:40%;" tabindex="-1" aria-hidden="true">
                      @foreach (yearList() as $index => $year_list)
                      <option value="{{$year_list}}" {{$index==0?'selected':''}}>{{$year_list}}</option>
                      @endforeach
                  </select>
                  <select name="month_end" class="select2 form-control select2-hidden-accessible auto_width" id="month_end" style="width:40%;" tabindex="-1" aria-hidden="true">
                      @foreach (monthList() as $months)
                          <option value="{{$months->id}}">{{$months->name}}</option>
                      @endforeach
                  </select>
              </div>
          </div>
        </div>
        {{-- Styling for the area chart can be found in the
        <code>/js/demo/chart-area-demo.js</code> file. --}}
      </div>
    </div>
</div>

<div class="row">
  <div class="col-xl-4 col-md-6 mb-4">
      <div class="card h-100">
          <div class="card-body p-0">
              <div class="row align-items-center mx-0">
                  <div class="col-auto px-3 py-2 bg-brand-green">
                      <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                  </div>
                  <div class="col">
                      <div class="h6 mb-0 font-weight-bold text-gray-800">Rencana</div>
                      <p id="show_total">321312</p>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <div class="col-xl-4 col-md-6 mb-4">
      <div class="card h-100">
          <div class="card-body p-0">
              <div class="row align-items-center mx-0">
                  <div class="col-auto px-3 py-2 bg-brand-green">
                      <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                  </div>
                  <div class="col">
                      <div class="h6 mb-0 font-weight-bold text-gray-800">Realisasi</div>
                      <p id="show_get">321312</p>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <div class="col-xl-4 col-md-6 mb-4">
      <div class="card h-100">
          <div class="card-body p-0">
              <div class="row align-items-center mx-0">
                  <div class="col-auto px-3 py-2 bg-brand-green">
                      <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                  </div>
                  <div class="col">
                      <div class="h6 mb-0 font-weight-bold text-gray-800">Selisih</div>
                      <p id="show_selisih">Rp 321312</p>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-12 mb-4">
      <div class="card">
          <div class="card-body p-4">
              <div class="d-flex align-items-center">
                  <div class="mr-3">
                      <div class="icon-circle bg-brand-green">
                      <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                      </div>
                  </div>
                  <div>
                      <div class="small text-gray-500">Jumlah Siswa Lunas</div>
                      <h6 class="mb-0" id="show_total_student">321312</h6>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <div class="col-md-6 col-12 mb-4">
      <div class="card">
          <div class="card-body p-4">
              <div class="d-flex align-items-center">
                  <div class="mr-3">
                      <div class="icon-circle bg-brand-green">
                      <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                      </div>
                  </div>
                  <div>
                      <div class="small text-gray-500">Jumlah Siswa Belum Lunas</div>
                      <h6 class="mb-0" id="show_student_remain"></h6>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div>

<!-- Area Charts -->
<div class="col-lg-12">
  <div class="card mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Pembayaran</h6>
    </div>
    <div class="card-body">
      <div class="chart-area">
        <canvas id="myAreaChart"></canvas>
      </div>
      <hr>
      {{-- Styling for the area chart can be found in the
      <code>/js/demo/chart-area-demo.js</code> file. --}}
    </div>
  </div>
</div>
<div class="col-lg-12">
  <div class="card mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Pembayaran</h6>
    </div>
    <div class="card-body">
      <div class="chart-area1">
        <canvas id="myAreaChart1"></canvas>
      </div>
      <hr>
      {{-- Styling for the area chart can be found in the
      <code>/js/demo/chart-area-demo.js</code> file. --}}
    </div>
  </div>
</div>

<!--Row-->
@endsection

@section('footjs')
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/vfs_fonts.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/buttons.html5.min.js') }}"></script>
<!-- Page level plugins -->
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<!-- Page level custom scripts -->
<script src="{{ asset('js/chart/spp.js') }}"></script>
<script src="{{ asset('js/level.js') }}"></script>
<script>

$(document).ready(function()
{

  $('#unit_id').on('change',function(){
    reloadChart();
  });
  $('#year_start').on('change',function(){
    reloadChart();
  });
  $('#year_end').on('change',function(){
    reloadChart();
  });
  $('#month_start').on('change',function(){
    reloadChart();
  });
  $('#month_end').on('change',function(){
    reloadChart();
  });
  reloadChart();
});
</script>
@endsection