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
    <h1 class="h3 mb-0 text-gray-800">Sumbangan Pembinaan Pendidikan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sumbangan Pembinaan Pendidikan</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{route('spp.spp-siswa-filter')}}" method="POST">
                        @csrf
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
                            <h6 class="m-0 font-weight-bold text-brand-green">Sumbangan Pembinaan Pendidikan</h6>
                            <div class="float-right">
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
                        @error('email')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Gagal!</strong> {{ $message }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @enderror
                        @error('whatsapp')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Gagal!</strong> {{ $message }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @enderror
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>NIPD</th>
                                    <th>Nama</th>
                                    <th>Tanggungan SPP Per Bulan Lalu</th>
                                    <th>Deposit SPP Per Bulan Lalu</th>
                                    <th>Nominal SPP Bulan Ini</th>
                                    <th>Potongan SPP (di Awal Tapel)</th>
                                    <th>Total Tanggungan SPP Bulan Ini</th>
                                    <th>SPP Terbayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                {{-- @foreach ($lists as $list)
                                @if($list->spp)
                                <tr>
                                    <td>{{$list->student_nis}}</td>
                                    <td>{{$list->identitas->student_name}}</td>
                                    <td>Rp {{number_format($list->spp->total)}}</td>
                                    <td>Rp {{number_format($list->spp->deduction)}}</td>
                                    <td>Rp {{number_format($list->spp->paid)}}</td>
                                    <td>Rp {{number_format($list->spp->total-($list->spp->paid+$list->spp->deduction))}}</td>
                                    <td>Rp {{number_format($list->spp->saldo)}}</td>
                                </tr>
                                @endif
                                @endforeach --}}
                                {{-- @foreach ($datas as $list)
                                <tr>
                                    <td>{{$list->siswa->student_nis}}</td>
                                    <td>{{$list->siswa->identitas->student_name}}</td>
                                    <td>Rp {{number_format($list->total)}}</td>
                                    <td>Rp {{number_format($list->deduction)}}</td>
                                    <td>Rp {{number_format($list->paid)}}</td>
                                    <td>Rp {{number_format($list->total-($list->paid+$list->deduction))}}</td>
                                    <td>Rp {{number_format($list->saldo)}}</td>
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

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Kirim Email Pengingat Tagihan SPP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
              <h5 class="font-weight-light mb-3">Memuat...</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" style="display: none;">
      </div>
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
<!-- Page level custom scripts -->
<script src="{{ asset('js/level.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.custom-file-input')
@include('template.footjs.kbm.datatables')
@include('template.footjs.modal.post_edit')

<script>
$(document).ready(function()
{

    $('#unit_id').on('change',function(){
        const unit_id = $(this).val();
        changeUnit(unit_id);
    });
    changeUnit($('#unit_id').val());
    $('#filter_submit').click(function(){
        getData();
    });
    getData();
});

function getData(){

    var unit_id = $('#unit_id').val();
    var level_id = $('#level').val();


    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            unit_id : unit_id,
            level_id : level_id,
        },
        beforeSend  : function() {
        },
        complete    : function() {
        }, 
        success: function async(response){
            console.log(response);
            $('#dataTable').DataTable().destroy();
            $('#tbody').empty();
            response[0].map((item, index) => {
                let row = '<tr>+'+
                        '<td>'+item[0]+'</td>'+
                        '<td>'+item[1]+'</td>'+
                        '<td>'+item[2]+'</td>'+
                        '<td>'+item[3]+'</td>'+
                        '<td>'+item[4]+'</td>'+
                        '<td>'+item[5]+'</td>'+
                        '<td>'+item[6]+'</td>'+
                        '<td>'+item[7]+'</td>'
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
    <h1 class="h3 mb-0 text-gray-800">Sumbangan Pembinaan Pendidikan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sumbangan Pembinaan Pendidikan</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="{{route('spp.spp-siswa-filter')}}" method="POST">
                        @csrf
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
                            <h6 class="m-0 font-weight-bold text-brand-green">Sumbangan Pembinaan Pendidikan</h6>
                            <div class="float-right">
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
                        @error('email')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Gagal!</strong> {{ $message }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @enderror
                        @error('whatsapp')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Gagal!</strong> {{ $message }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @enderror
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>NIPD</th>
                                    <th>Nama</th>
                                    <th>Tanggungan SPP Per Bulan Lalu</th>
                                    <th>Deposit SPP Per Bulan Lalu</th>
                                    <th>Nominal SPP Bulan Ini</th>
                                    <th>Potongan SPP (di Awal Tapel)</th>
                                    <th>Total Tanggungan SPP Bulan Ini</th>
                                    <th>SPP Terbayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">
                                {{-- @foreach ($lists as $list)
                                @if($list->spp)
                                <tr>
                                    <td>{{$list->student_nis}}</td>
                                    <td>{{$list->identitas->student_name}}</td>
                                    <td>Rp {{number_format($list->spp->total)}}</td>
                                    <td>Rp {{number_format($list->spp->deduction)}}</td>
                                    <td>Rp {{number_format($list->spp->paid)}}</td>
                                    <td>Rp {{number_format($list->spp->total-($list->spp->paid+$list->spp->deduction))}}</td>
                                    <td>Rp {{number_format($list->spp->saldo)}}</td>
                                </tr>
                                @endif
                                @endforeach --}}
                                {{-- @foreach ($datas as $list)
                                <tr>
                                    <td>{{$list->siswa->student_nis}}</td>
                                    <td>{{$list->siswa->identitas->student_name}}</td>
                                    <td>Rp {{number_format($list->total)}}</td>
                                    <td>Rp {{number_format($list->deduction)}}</td>
                                    <td>Rp {{number_format($list->paid)}}</td>
                                    <td>Rp {{number_format($list->total-($list->paid+$list->deduction))}}</td>
                                    <td>Rp {{number_format($list->saldo)}}</td>
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

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Kirim Email Pengingat Tagihan SPP</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
              <h5 class="font-weight-light mb-3">Memuat...</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" style="display: none;">
      </div>
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
<!-- Page level custom scripts -->
<script src="{{ asset('js/level.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.custom-file-input')
@include('template.footjs.kbm.datatables')
@include('template.footjs.modal.post_edit')

<script>
$(document).ready(function()
{

    $('#unit_id').on('change',function(){
        const unit_id = $(this).val();
        changeUnit(unit_id);
    });
    changeUnit($('#unit_id').val());
    $('#filter_submit').click(function(){
        getData();
    });
    getData();
});

function getData(){

    var unit_id = $('#unit_id').val();
    var level_id = $('#level').val();


    $.ajax({
        url         : window.location.href,
        type        : 'POST',
        dataType    : 'JSON',
        headers     : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data        : {
            unit_id : unit_id,
            level_id : level_id,
        },
        beforeSend  : function() {
        },
        complete    : function() {
        }, 
        success: function async(response){
            console.log(response);
            $('#dataTable').DataTable().destroy();
            $('#tbody').empty();
            response[0].map((item, index) => {
                let row = '<tr>+'+
                        '<td>'+item[0]+'</td>'+
                        '<td>'+item[1]+'</td>'+
                        '<td>'+item[2]+'</td>'+
                        '<td>'+item[3]+'</td>'+
                        '<td>'+item[4]+'</td>'+
                        '<td>'+item[5]+'</td>'+
                        '<td>'+item[6]+'</td>'+
                        '<td>'+item[7]+'</td>'
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection