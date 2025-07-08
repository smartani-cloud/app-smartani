@extends('template.main.master')

@section('title')
Sumbangan Pembinaan Pendidikan
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{url('/vendor/select2/dist/css/select2.min.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Masukan SPP</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Laporan Masukan SPP</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="/keuangan/spp/laporan-masukan-spp" method="get">
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
                                <button id="filter_submit" class="btn btn-brand-green-dark btn-sm" type="button">Saring</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Laporan Masukan SPP</h6>
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
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>NIPD</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody">

                            </tbody>
                            <!-- <tfoot>
                                <tr>
                                    <th colspan="3">Total Diterima</th>
                                    <th>Rp 8.500.000</th>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ubahKategori -->
<div id="ubahKategori" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <form action="/keuangan/spp/change-transaction" method="POST">
                <div class="modal-header flex-column">
                    {{-- <div class="icon-box">
                        <i class="material-icons">&#xe5ca;</i>
                    </div> --}}
                    <h4 class="modal-title w-100">Ubah Transaksi</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                      <label for="nama_siswa" class="col-form-label">Siswa</label>
                      <input type="text" name="nama_siswa" class="form-control" id="nama_siswa" value="" disabled>
                    </div>

                    <div class="form-group">
                        <label for="jenis_pembayaran" class="col-form-label">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran" class="select2 form-control auto_width" id="jenis_pembayaran" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="1">BMS</option>
                            <option value="2" selected>SPP</option>
                        </select>
                    </div>

                    <input type="hidden" name="id" class="form-control" id="id" value="0" disabled>
                    <input type="hidden" name="total" class="form-control" id="total" value="0" disabled>
                    <div class="form-group">
                      <label for="nominal_siswa" class="col-form-label">Nominal</label>
                      <input type="text" name="nominal_siswa" class="form-control number-separator" id="nominal_siswa" value="0" required>
                    </div>

                    <div class="form-group">
                        <label for="split" class="col-form-label">Split dengan pembayaran lain?</label>
                        <select name="split" class="select2 form-control auto_width" id="split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="0" selected>Tidak</option>
                            <option value="1" >Ya</option>
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="unit_split" class="col-form-label">Unit Siswa</label>
                        <select name="unit_split" class="form-control auto_width" id="unit_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            @foreach (getUnits() as $index => $units)
                                <option value="{{$units->id}}" {{$index==0?'selected':''}}>{{$units->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="siswa_split" class="col-form-label">Pilih siswa</label>
                        <select name="siswa_split" class="select2-hidden-accessible form-control auto_width" id="siswa_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="jenis_pembayaran_split" class="col-form-label">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran_split" class="select2 form-control auto_width" id="jenis_pembayaran_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="1" selected>BMS</option>
                            <option value="2">SPP</option>
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                      <label for="nominal_split" class="col-form-label">Nominal</label>
                      <input type="text" name="nominal_split" class="form-control number-separator" id="nominal_split" value="0" required>
                    </div>

                    <div class="form-group">
                      <label for="refund" class="col-form-label">Refund</label>
                      <input type="text" readonly name="refund" class="form-control number-separator" id="refund" value="0" required>
                    </div>


                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        @csrf
                        <input type="text" name="student_id" id="student_id" class="id" hidden/>
                        <input type="text" name="id" id="id" class="id" hidden/>
                    <button type="submit" class="btn btn-success">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--Row-->
@endsection

@section('footjs')
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/vfs_fonts.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/buttons.html5.min.js') }}"></script>
<!-- Page level custom scripts -->
@include('template.footjs.kbm.datatables')

<script>
$(document).ready(function()
{
    const unitnya = $('#unit_split').val();
    getSiswaList(unitnya);

    $('#ubahKategori').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id = button.data('id') // Extract info from data-* attributes
        var name = button.data('name') // Extract info from data-* attributes
        var total = button.data('total') // Extract info from data-* attributes
        var student_id = button.data('student_id') // Extract info from data-* attributes
        var modal = $(this)
        modal.find('input[name="id"]').val(id)
        $('#nama_siswa').val(name);
        $('#total').val(total);
        $('#nominal_siswa').val(total);
        $('#nominal_split').val(0);
        $('#refund').val(0);
        $('#student_id').val(student_id);
        $('#split').val(0);
        $('.split-siswa').hide();
        hitungSemua();
        modal.find('p[id="name"]').text('Apakah Anda yakin akan mengubah kategori transaksi '+name+'?');
    })

    $('.select2-hidden-accessible').select2();

    $('#nominal_siswa').on('change', function() {
        hitungSemua();
    });
    $('#nominal_split').on('change', function() {
        hitungSemua();
    });
    $('#refund').on('change', function() {
        hitungSemua();
    });
    $('#split').on('change', function() {
        var value = this.value;
        $('#nominal_split').val(0);
        hitungSemua();
        if(value == 1){
            $('.split-siswa').show();
        }else{
            $('.split-siswa').hide();
        }
    });

    $('#filter_submit').click(function(){
        console.log('1123');
        getData();
    });
    getData();

    $('#unit_split').on('change', function() {
        getSiswaList(this.value);
    });

    $('#jenis_pembayaran').on('change', function() {
        getSiswaList($('#unit_split').val());
    });
    $('#jenis_pembayaran_split').on('change', function() {
        getSiswaList($('#unit_split').val());
    });
})

function hitungSemua(){
    var total = parseInt($('#total').val().replace(/\./g, ""));
    var nominal_siswa = parseInt($('#nominal_siswa').val().replace(/\./g, ""));
    var nominal_split = parseInt($('#nominal_split').val().replace(/\./g, ""));
    var refund = total - (nominal_siswa + nominal_split);

    $('#refund').val(refund);
    
    if(nominal_siswa > total){
        $('#nominal_siswa').val(total-nominal_split);
        $('#refund').val(0);
    }else if(refund < 0){
        $('#nominal_split').val(total-nominal_siswa);
        console.log(total-nominal_siswa);
        $('#refund').val(0);
    }
}

function getSiswaList(unit){
    const jenis_split = $('#jenis_pembayaran_split').val();
    const jenis = $('#jenis_pembayaran').val();
    const student_id = $('#student_id').val();
    console.log(jenis_split, jenis, student_id);
    jQuery.ajax({
        url: "{{url('/keuangan/spp/list-siswa')}}/"+unit,
        type : "GET",
        success:function(data)
        {

            $('.option-siswa').remove();
            data.map((item, index) => {
                if(item[0] == student_id && jenis == jenis_split){

                }else{
                    const valuenya = '<option class="option-siswa" value="'+item[0]+'" selected>'+item[1] + ' - ' + item[2]+'</option>';
                    $('#siswa_split').append(valuenya);
                }
            });
        }
    });
}

function getData(){

    var year = $('#year').val();
    var month = $('#month').val();
    var unit_id = $('#unit_id').val();
    // var level_id = $('#level').val();

    console.log (
        year,
        month,
        unit_id,
        // level_id,
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
            // level_id : level_id,
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
@endsection