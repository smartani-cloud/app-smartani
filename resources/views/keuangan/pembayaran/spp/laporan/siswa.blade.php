<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Sumbangan Pembinaan Pendidikan
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan SPP Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan SPP Siswa</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{route('spp.laporan-spp-siswa-filter')}}" method="POST">
                        @csrf
                            <div class="form-group row">
                                <label for="year" class="col-sm-3 control-label">Tahun</label>
                                <div class="col-sm-5">
                                    <select name="year" class="select2 form-control select2-hidden-accessible auto_width" id="year" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @foreach (yearList() as $index => $year_list)
                                        <option value="{{$year_list}}" {{$index==0?'selected':''}}>{{$year_list}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Bulan</label>
                                <div class="col-sm-5">
                                    <select name="month" class="select2 form-control select2-hidden-accessible auto_width" id="month" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">Semua</option>
                                        @foreach (monthList() as $months)                                        
                                            <option value="{{$months->id}}">{{$months->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Unit</label>
                                <div class="col-sm-5">
                                    <select name="unit_id" class="select2 form-control select2-hidden-accessible auto_width" id="unit_id" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @foreach (getUnits() as $index => $units)
                                            <option value="{{$units->id}}" {{$index==0?'selected':''}}>{{$units->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                                <div class="col-sm-5">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="level" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">Semua</option>
                                    </select>
                                </div>
                                <button id="filter_submit" class="btn btn-brand-green-dark btn-sm" type="button">Saring</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Laporan SPP Siswa</h6>
                            <div class="float-right">
                                <button class="m-0 btn btn-brand-green-dark btn-sm" data-toggle="modal" id="atur_sekaligus" style="display: none" data-target="#AturModal">Atur Sekaligus <i class="fas fa-cogs"></i></button>
                            </div>
                        </div>
                        @if(Session::has('sukses'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> {{ Session::get('sukses') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>NIPD</th>
                                    <th>Nama</th>
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
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="AturModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
  
          <form action="{{route('spp.laporan-spp-siswa-filter-atur')}}"  method="POST">
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
                  <button type="submit" class="btn btn-primary">Atur</button>
              </div>
          </form>
      </div>
    </div>
  </div>


<!-- Modal Tambah -->
<div class="modal fade" id="PotonganModal" tabindex="-1" role="dialog" aria-labelledby="PotonganModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
  
          <form action="{{route('spp.laporan-spp-siswa-filter-atur')}}"  method="POST">
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
                      <label for="potongan" class="col-sm-3 control-label">Potongan</label>
                      <div class="col-sm-8">
                          {{--<input type="text" id="potongan" name="potongan" class="form-control number-separator">--}}
                          <select id="potongan" class="form-control" name="potongan" {{ $deductions && count($deductions) < 1 ? 'disabled="disabled"' : null }}/>
                            <option value="">== Belum Ada Potongan ==</option>
                            @foreach($deductions as $d)
                            <option value="{{ $d->id }}">{{ $d->nameWithPercentage }}</option>
                            @endforeach
                          </select>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <input type="hidden" id="id" name="id" value="">
                  <button type="submit" class="btn btn-primary"  {{ $deductions && count($deductions) < 1 ? 'disabled="disabled"' : null }}>Atur</button>
              </div>
          </form>
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

<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>
<!-- Page level custom scripts -->

<script src="{{ asset('js/level.js') }}"></script>

<script>
$(document).ready(function()
{
    $('#PotonganModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id = button.data('id') // Extract info from data-* attributes
        var name = button.data('name') // Extract info from data-* attributes
        var potongan = button.data('potongan') // Extract info from data-* attributes
        var modal = $(this)
        console.log(name)
        modal.find('input[id="id"]').val(id)
        modal.find('input[id="name"]').val(name)
        //modal.find('select[id="potongan"]').val(potongan)
    });

    $('#unit_id').on('change',function(){
        const unit_id = $(this).val();
        changeUnit(unit_id);
    });
    changeUnit($('#unit_id').val());

    $('#filter_submit').click(function(){
        console.log('1123');
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

    $('#dataTable').DataTable().destroy();
    $('#tbody').empty();
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
                    '</tr>';
                $('#tbody').append(row);
            });
            $('#dataTable').DataTable();
        },
        error: function(xhr, textStatus, errorThrown){
            alert(xhr.responseText);
        },
    });
}
</script>
{{-- @include('template.footjs.kbm.datatables') --}}
=======
@extends('template.main.master')

@section('title')
Sumbangan Pembinaan Pendidikan
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan SPP Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan SPP Siswa</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{route('spp.laporan-spp-siswa-filter')}}" method="POST">
                        @csrf
                            <div class="form-group row">
                                <label for="year" class="col-sm-3 control-label">Tahun</label>
                                <div class="col-sm-5">
                                    <select name="year" class="select2 form-control select2-hidden-accessible auto_width" id="year" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @foreach (yearList() as $index => $year_list)
                                        <option value="{{$year_list}}" {{$index==0?'selected':''}}>{{$year_list}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Bulan</label>
                                <div class="col-sm-5">
                                    <select name="month" class="select2 form-control select2-hidden-accessible auto_width" id="month" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">Semua</option>
                                        @foreach (monthList() as $months)                                        
                                            <option value="{{$months->id}}">{{$months->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Unit</label>
                                <div class="col-sm-5">
                                    <select name="unit_id" class="select2 form-control select2-hidden-accessible auto_width" id="unit_id" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @foreach (getUnits() as $index => $units)
                                            <option value="{{$units->id}}" {{$index==0?'selected':''}}>{{$units->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                                <div class="col-sm-5">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="level" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">Semua</option>
                                    </select>
                                </div>
                                <button id="filter_submit" class="btn btn-brand-green-dark btn-sm" type="button">Saring</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Laporan SPP Siswa</h6>
                            <div class="float-right">
                                <button class="m-0 btn btn-brand-green-dark btn-sm" data-toggle="modal" id="atur_sekaligus" style="display: none" data-target="#AturModal">Atur Sekaligus <i class="fas fa-cogs"></i></button>
                            </div>
                        </div>
                        @if(Session::has('sukses'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> {{ Session::get('sukses') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>NIPD</th>
                                    <th>Nama</th>
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
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="AturModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
  
          <form action="{{route('spp.laporan-spp-siswa-filter-atur')}}"  method="POST">
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
                  <button type="submit" class="btn btn-primary">Atur</button>
              </div>
          </form>
      </div>
    </div>
  </div>


<!-- Modal Tambah -->
<div class="modal fade" id="PotonganModal" tabindex="-1" role="dialog" aria-labelledby="PotonganModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
  
          <form action="{{route('spp.laporan-spp-siswa-filter-atur')}}"  method="POST">
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
                      <label for="potongan" class="col-sm-3 control-label">Potongan</label>
                      <div class="col-sm-8">
                          {{--<input type="text" id="potongan" name="potongan" class="form-control number-separator">--}}
                          <select id="potongan" class="form-control" name="potongan" {{ $deductions && count($deductions) < 1 ? 'disabled="disabled"' : null }}/>
                            <option value="">== Belum Ada Potongan ==</option>
                            @foreach($deductions as $d)
                            <option value="{{ $d->id }}">{{ $d->nameWithPercentage }}</option>
                            @endforeach
                          </select>
                      </div>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <input type="hidden" id="id" name="id" value="">
                  <button type="submit" class="btn btn-primary"  {{ $deductions && count($deductions) < 1 ? 'disabled="disabled"' : null }}>Atur</button>
              </div>
          </form>
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

<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>
<!-- Page level custom scripts -->

<script src="{{ asset('js/level.js') }}"></script>

<script>
$(document).ready(function()
{
    $('#PotonganModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id = button.data('id') // Extract info from data-* attributes
        var name = button.data('name') // Extract info from data-* attributes
        var potongan = button.data('potongan') // Extract info from data-* attributes
        var modal = $(this)
        console.log(name)
        modal.find('input[id="id"]').val(id)
        modal.find('input[id="name"]').val(name)
        //modal.find('select[id="potongan"]').val(potongan)
    });

    $('#unit_id').on('change',function(){
        const unit_id = $(this).val();
        changeUnit(unit_id);
    });
    changeUnit($('#unit_id').val());

    $('#filter_submit').click(function(){
        console.log('1123');
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

    $('#dataTable').DataTable().destroy();
    $('#tbody').empty();
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
                    '</tr>';
                $('#tbody').append(row);
            });
            $('#dataTable').DataTable();
        },
        error: function(xhr, textStatus, errorThrown){
            alert(xhr.responseText);
        },
    });
}
</script>
{{-- @include('template.footjs.kbm.datatables') --}}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection