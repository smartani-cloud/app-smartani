<<<<<<< HEAD
@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item"><a href="{{ route('bms.index')}}">Biaya Masuk Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              @if(!isset($jenis) || $jenis != 'berkala')
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route.'.index', ['jenis' => 'tunai']) }}">Tunai</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route.'.index', ['jenis' => 'berkala']) }}">Berkala</a>
              </li>
              @else
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route.'.index', ['jenis' => 'tunai']) }}">Tunai</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route.'.index', ['jenis' => 'berkala']) }}">Berkala</a>
              </li>
              @endif
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <form action="{{ route($route.'.index',['jenis' => $jenis]) }}" method="get">
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="unit_id" class="form-control-label">Unit</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    @if($units && $units->count() > 1)
                    <select name="unit_id" class="select2 form-control auto_width" id="unit_id" style="width:100%;">
                        @foreach($units as $index => $unit)
                        <option value="{{ $unit->id }}" {{ $unit->id == $unit_id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="text" class="form-control" value="{{ Auth::user()->pegawai->unit->name }}" disabled>
                    <input name="unit_id" type="hidden" value="{{ Auth::user()->pegawai->unit_id }}">
                    @endif
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
                    <label for="year" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select name="year" class="select2 form-control auto_width" id="year" style="width:100%;">
                      @foreach($academicYears as $index => $year_list)
                      <option value="{{ $year_list->id }}" {{ $year_list->id == $year ? 'selected' : '' }}>{{$year_list->academic_year}}</option>
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
                      <button type="button" id="filter_submit" class="btn btn-sm btn-brand-green-dark">Saring</button>
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6>
      </div>
      <div class="card-body">
        <div class="table-load p-4" style="display: none;">
            <div class="row">
              <div class="col-12">
                <div class="text-center my-5">
                  <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
                  <h5 class="font-weight-light mb-3">Memuat...</h5>
                </div>
              </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>Kategori</th>
                        <th>Total Nominal BMS {{ ucwords($jenis) }}</th>
                        <th>Total Potongan BMS  {{ ucwords($jenis) }}</th>
                        <th>Tanggungan BMS {{ ucwords($jenis).($jenis == 'tunai' ? '  Bersih' : '') }}</th>
                        <th>Tanggungan BMS {{ ucwords($jenis) }} yang Sudah Dibayarkan</th>
                        <th>Sisa Tanggungan BMS {{ ucwords($jenis) }} yang Harus Dibayarkan</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @foreach($lists as $list)
                    <tr>
                        <td class="text-nowrap">{{ $list['name'] }}</td>
                        <td>{{ number_format($list['total'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['deduction'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['nominal'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['paid'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['remain'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Row-->
@endsection

@section('footjs')
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.global.select2-default')

<!-- Page level custom scripts -->
<script>
$(document).ready(function()
{
    $('#filter_submit').click(function(){
        getData();
    });
    //getData();
})
</script>
<script>
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
function getData(){
    var year = $('#year').val();
    var unit_id = $('#unit_id').val();
    // var level_id = $('#level').val();

    console.log (
        year,
        unit_id,
        // level_id,
    );
    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            year : year,
            unit_id : unit_id,
            // level_id : level_id,
        },
        beforeSend  : function() {
            $('#filter_submit').prop('disabled',true);
            $('.table-responsive').hide();
            $('.table-load').show();
            $('#tbody').empty();
        },
        complete    : function() {
        }, 
        success: function async(response){
            console.log(response);
            response.map((item, index) => {
                let row = '<tr>+'+
                    '<td class="text-nowrap">'+item.name+'</td>'+
                    '<td>'+numberWithCommas(item.total)+'</td>'+
                    '<td>'+numberWithCommas(item.deduction)+'</td>'+
                    '<td>'+numberWithCommas(item.nominal)+'</td>'+
                    '<td>'+numberWithCommas(item.paid)+'</td>'+
                    '<td>'+numberWithCommas(item.remain)+'</td>'+
                '</tr>';
                $('#tbody').append(row);
            });
            $('.table-load').hide();
            $('.table-responsive').show();
            $('#filter_submit').prop('disabled',false);
        },
        error: function(xhr, textStatus, errorThrown){
            alert(xhr.responseText);
        },
    });
}
</script>
=======
@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item"><a href="{{ route('bms.index')}}">Biaya Masuk Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              @if(!isset($jenis) || $jenis != 'berkala')
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route.'.index', ['jenis' => 'tunai']) }}">Tunai</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route.'.index', ['jenis' => 'berkala']) }}">Berkala</a>
              </li>
              @else
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route.'.index', ['jenis' => 'tunai']) }}">Tunai</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route.'.index', ['jenis' => 'berkala']) }}">Berkala</a>
              </li>
              @endif
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <form action="{{ route($route.'.index',['jenis' => $jenis]) }}" method="get">
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="unit_id" class="form-control-label">Unit</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    @if($units && $units->count() > 1)
                    <select name="unit_id" class="select2 form-control auto_width" id="unit_id" style="width:100%;">
                        @foreach($units as $index => $unit)
                        <option value="{{ $unit->id }}" {{ $unit->id == $unit_id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="text" class="form-control" value="{{ Auth::user()->pegawai->unit->name }}" disabled>
                    <input name="unit_id" type="hidden" value="{{ Auth::user()->pegawai->unit_id }}">
                    @endif
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
                    <label for="year" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select name="year" class="select2 form-control auto_width" id="year" style="width:100%;">
                      @foreach($academicYears as $index => $year_list)
                      <option value="{{ $year_list->id }}" {{ $year_list->id == $year ? 'selected' : '' }}>{{$year_list->academic_year}}</option>
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
                      <button type="button" id="filter_submit" class="btn btn-sm btn-brand-green-dark">Saring</button>
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6>
      </div>
      <div class="card-body">
        <div class="table-load p-4" style="display: none;">
            <div class="row">
              <div class="col-12">
                <div class="text-center my-5">
                  <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
                  <h5 class="font-weight-light mb-3">Memuat...</h5>
                </div>
              </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>Kategori</th>
                        <th>Total Nominal BMS {{ ucwords($jenis) }}</th>
                        <th>Total Potongan BMS  {{ ucwords($jenis) }}</th>
                        <th>Tanggungan BMS {{ ucwords($jenis).($jenis == 'tunai' ? '  Bersih' : '') }}</th>
                        <th>Tanggungan BMS {{ ucwords($jenis) }} yang Sudah Dibayarkan</th>
                        <th>Sisa Tanggungan BMS {{ ucwords($jenis) }} yang Harus Dibayarkan</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @foreach($lists as $list)
                    <tr>
                        <td class="text-nowrap">{{ $list['name'] }}</td>
                        <td>{{ number_format($list['total'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['deduction'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['nominal'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['paid'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['remain'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Row-->
@endsection

@section('footjs')
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.global.select2-default')

<!-- Page level custom scripts -->
<script>
$(document).ready(function()
{
    $('#filter_submit').click(function(){
        getData();
    });
    //getData();
})
</script>
<script>
function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
function getData(){
    var year = $('#year').val();
    var unit_id = $('#unit_id').val();
    // var level_id = $('#level').val();

    console.log (
        year,
        unit_id,
        // level_id,
    );
    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            year : year,
            unit_id : unit_id,
            // level_id : level_id,
        },
        beforeSend  : function() {
            $('#filter_submit').prop('disabled',true);
            $('.table-responsive').hide();
            $('.table-load').show();
            $('#tbody').empty();
        },
        complete    : function() {
        }, 
        success: function async(response){
            console.log(response);
            response.map((item, index) => {
                let row = '<tr>+'+
                    '<td class="text-nowrap">'+item.name+'</td>'+
                    '<td>'+numberWithCommas(item.total)+'</td>'+
                    '<td>'+numberWithCommas(item.deduction)+'</td>'+
                    '<td>'+numberWithCommas(item.nominal)+'</td>'+
                    '<td>'+numberWithCommas(item.paid)+'</td>'+
                    '<td>'+numberWithCommas(item.remain)+'</td>'+
                '</tr>';
                $('#tbody').append(row);
            });
            $('.table-load').hide();
            $('.table-responsive').show();
            $('#filter_submit').prop('disabled',false);
        },
        error: function(xhr, textStatus, errorThrown){
            alert(xhr.responseText);
        },
    });
}
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection