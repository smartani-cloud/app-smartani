@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/jszip/datatables.min.css') }}" rel="stylesheet">
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
        <li class="breadcrumb-item"><a href="{{ route('spp.index')}}">Sumbangan Pembinaan Pendidikan</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>


<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
            <div class="col-md-8">
                <form action="{{ route($route.'.index') }}" method="post">
                    @csrf
                    <div class="form-group row">
                        <label for="year" class="col-sm-3 control-label">Tahun Pelajaran</label>
                        <div class="col-sm-5">
                            {{-- <select name="year" class="select2 form-control auto_width" id="year" style="width:100%;">
                                @foreach(yearList() as $index => $year_list)
                                <option value="{{$year_list}}" {{$index==0?'selected':''}}>{{$year_list}}</option>
                                @endforeach
                            </select> --}}
                            <select aria-label="Tahun" name="year" class="form-control" id="year">
                              @foreach($tahunPelajaran as $t)
                              <option value="{{ $t->academicYearLink }}" {{ !$isYear && $year->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                              @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="month" class="col-sm-3 control-label">Bulan</label>
                        <div class="col-sm-5">
                            <select name="month" class="select2 form-control auto_width" id="month" style="width:100%;">
                                <option value="">Semua</option>
                                @foreach(academicMonthList() as $months)
                                <option value="{{$months->id}}">{{$months->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="kelas" class="col-sm-3 control-label">Unit</label>
                        <div class="col-sm-5">
                            <select name="unit_id" class="select2 form-control auto_width" id="unit_id" style="width:100%;" {{ getUnits()->count() > 1 ? '' : 'readonly="readonly"'}}>
                                @foreach(getUnits() as $index => $units)
                                <option value="{{$units->id}}" {{$index==0?'selected':''}}>{{$units->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                        <div class="col-sm-5">
                            <select name="level" class="select2 form-control auto_width" id="level" style="width:100%;">
                                <option value="">Semua</option>
                            </select>
                        </div>
                        <button id="filter_submit" class="btn btn-brand-green-dark btn-sm" type="button">Saring</button>
                    </div>
                </form>
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
        <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6>
        <div class="float-right">
            <button class="m-0 btn btn-brand-green-dark btn-sm" data-toggle="modal" id="atur_sekaligus" style="display: none" data-target="#AturModal">Atur Sekaligus <i class="fas fa-cogs ml-1"></i></button>
        </div>
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
        <div class="table-load p-4">
            <div class="row">
              <div class="col-12">
                <div class="text-center my-5">
                  <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
                  <h5 class="font-weight-light mb-3">Memuat...</h5>
                </div>
              </div>
            </div>
        </div>
        <div class="table-responsive" style="display: none;">
            <table id="dataTable" class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>NIPD</th>
                        <th>Bulan</th>
                        <th>Nama Siswa</th>
                        <th>Nominal</th>
                        <th>Potongan</th>
                        <th>Terbayar</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    {{-- @foreach ($datas as $data)
                    <tr>
                        <td>{{$data->siswa->student_nis}}</td>
                        <td>{{$data->monthId}}</td>
                        <td>{{$data->siswa->identitas->student_name}}</td>
                        <td>{{number_format($data->spp_nominal)}}</td>
                        <td>{{number_format($data->deduction_nominal)}}</td>
                        <td>{{number_format($data->spp_paid)}}</td>
                        <td>{{$data->status=='0'?'Belum':'Lunas'}}</td>
                        <td>
                            <button class="m-0 btn btn-warning btn-sm" data-toggle="modal" data-target="#PotonganModal" data-id="{{$data->id}}" data-name="{{$data->siswa->student_name}}" data-potongan="{{$data->deduction_id}}"><i class="fas fa-cogs"></i></button>
                        </td>
                    </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Atur -->
<div class="modal fade" id="AturModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <form action="{{route($route.'.set')}}"  method="POST">
          @csrf
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Atur Sekaligus</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <div class="form-group row">
                      <label for="spp" class="col-sm-3 control-label">SPP</label>
                      <div class="col-sm-8">
                          <input type="text" name="spp" class="form-control number-separator">
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <input type="hidden" name="year" id="atur_year" value="">
                  <input type="hidden" name="month" id="atur_month" value="">
                  <input type="hidden" name="level" id="atur_level" value="">
                  <button type="submit" class="btn btn-brand-green-dark">Atur</button>
              </div>
          </form>
      </div>
    </div>
</div>

<!-- Modal Potongan -->
<div class="modal fade" id="PotonganModal" tabindex="-1" role="dialog" aria-labelledby="PotonganModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <form action="{{route($route.'.deduct')}}"  method="POST">
              @method('PUT')
              @csrf
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLongTitle">Ubah Potongan</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <div class="form-group row">
                      <label for="potongan" class="col-sm-3 control-label">Nama</label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control" id="name" value="" disabled>
                      </div>
                  </div>
                  <div class="form-group row">
                      <label for="nominal" class="col-sm-3 control-label">Nominal</label>
                      <div class="col-sm-8">
                          <input type="text" class="form-control number-separator" id="nominal" name="nominal" value="0" required="required">
                      </div>
                  </div>
                  <div class="form-group row">
                      <label for="potongan" class="col-sm-3 control-label">Potongan</label>
                      <div class="col-sm-8">
                          {{--<input type="text" id="potongan" name="potongan" class="form-control number-separator">--}}
                          <select id="potongan" class="form-control" name="potongan" {{ $deductions && count($deductions) < 1 ? 'disabled="disabled"' : null }}/>
                            <option value="">== Tidak Ada Potongan ==</option>
                            @foreach($deductions as $d)
                            <option value="{{ $d->id }}">{{ $d->isPercentage ? $d->nameWithPercentage : $d->nameWithNominal }}</option>
                            @endforeach
                          </select>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <input type="hidden" id="id" name="id" value="">
                  <button type="submit" class="btn btn-brand-green-dark"  {{ $deductions && count($deductions) < 1 ? 'disabled="disabled"' : null }}>Atur</button>
              </div>
          </form>
      </div>
    </div>
</div>

@if(Auth::user()->role->name == 'faspv' || in_array(Auth::user()->pegawai->position_id,[57]))
@include('template.modal.konfirmasi_hapus')
@endif

<!--Row-->
@endsection

@section('footjs')
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/jszip/datatables.min.js') }}"></script>

<!-- Easy Number Separator -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Unit-Levels -->
<script src="{{ asset('js/level.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.get-today-date')
@include('template.footjs.keuangan.datatables-thousands-dot-exportable')
<script>
$(document).ready(function()
{
    $('#PotonganModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id = button.data('id') // Extract info from data-* attributes
        var name = button.data('name') // Extract info from data-* attributes
        var nominal = button.data('nominal') // Extract info from data-* attributes
        var potongan = button.data('potongan') // Extract info from data-* attributes
        var modal = $(this)
        console.log(name)
        modal.find('input[id="id"]').val(id)
        modal.find('input[id="name"]').val(name)
        modal.find('input[id="nominal"]').val(nominal)
        modal.find('select[id="potongan"]').val(potongan)
    });

    $('#unit_id').on('change',function(){
        const unit_id = $(this).val();
        changeUnit(unit_id);
    });
    changeUnit($('#unit_id').val());

    $('#filter_submit').click(function(){
        checkActiveButton();
        getData();
    });
    checkActiveButton();
    getData();
});

function checkActiveButton(){
    var month = $('#month').val();    
    var level = $('#level').val();    
    var year = $('#year').val();    
    if(month && level){
        $('#atur_sekaligus').show();
    }else{
        $('#atur_sekaligus').hide();
    }
    $('#atur_year').val(year);
    $('#atur_month').val(month);
    $('#atur_level').val(level);
}

function getData(){
    var year = $('#year').val();
    var month = $('#month').val();
    var unit_id = $('#unit_id').val();
    var level_id = $('#level').val();

    console.log (
        year,
        month,
        unit_id,
        level_id,
    );

    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            year : year,
            month : month,
            unit_id : unit_id,
            level_id : level_id,
        },
        beforeSend  : function() {
            $('#filter_submit').prop('disabled',true);
            $('.table-responsive').hide();
            $('.table-load').show();
            $('#dataTable').DataTable().destroy();
            $('#tbody').empty();
        },
        complete    : function() {
        }, 
        success: function async(response){
            console.log(response);
            response[0].map((item, index) => {
                let row = '<tr>+'+
                        '<td>'+item[0]+'</td>'+
                        '<td>'+item[1]+'</td>'+
                        '<td>'+item[2]+'</td>'+
                        '<td>'+item[3]+'</td>'+
                        '<td>'+item[4]+'</td>'+
                        '<td>'+item[5]+'</td>'+
                        '<td>'+item[6]+'</td>'+
                        '<td>'+item[7]+'</td>'+
                    '</tr>';
                $('#tbody').append(row);
            });
            if(!$.fn.dataTable.isDataTable('#dataTable')){
                datatablesExportable([3,4,5],null,'Diekspor per '+getTodayDate());
            }
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
@if(Auth::user()->role->name == 'faspv' || in_array(Auth::user()->pegawai->position_id,[57]))
@include('template.footjs.modal.get_delete')
@endif
@endsection