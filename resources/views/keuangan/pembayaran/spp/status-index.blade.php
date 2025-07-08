@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/jszip/datatables.min.css') }}" rel="stylesheet">
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

<div class="row">
    @php
    $siswaOpt = ['siswa','alumni'];
    @endphp
    @foreach($siswaOpt as $opt)
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 {{ isset($siswa) && $siswa == $opt ? 'bg-brand-green' : 'bg-brand-green' }}">
                        <i class="mdi mdi-account-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ ucwords(($opt == 'alumni' ? 'Siswa ' : null).$opt) }}</div>
                    </div>
                    <div class="col-auto">
                        @if(!isset($siswa) || (isset($siswa) && $siswa != $opt))
                        <a href="{{ route($route.'.index', ['siswa' => $opt])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                        @else
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary disabled"role="button" aria-disabled="true">Pilih</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(isset($siswa))
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
          <div class="col-md-8">
            <form action="{{route($route.'.get')}}" method="POST">
            @csrf
            <div class="form-group row">
                <label for="kelas" class="col-sm-3 control-label">Unit</label>
                <div class="col-sm-5">
                    @if(getUnits()->count() > 1)
                    <select name="unit_id" class="select2 form-control auto_width" id="unit_id" style="width:100%;">
                        @foreach(getUnits() as $index => $units)
                        <option value="{{$units->id}}" {{$index==0?'selected':''}}>{{$units->name}}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="text" class="form-control" value="{{ Auth::user()->pegawai->unit->name }}" disabled>
                    <input name="unit_id" type="hidden" value="{{ Auth::user()->pegawai->unit_id }}">
                    @endif
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
                        <th>Nama Siswa</th>
                        <th>Tanggungan SPP Per Bulan Lalu</th>
                        <th>Deposit SPP Per Bulan Lalu</th>
                        <th>Nominal SPP Bulan Ini</th>
                        <th>Potongan SPP (di Awal Tapel)</th>
                        <th>Total Tanggungan SPP Bulan Ini</th>
                        <th>SPP Terbayar</th>
                        <th>Total Tanggungan yang Harus Dibayarkan</th>
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
@endif

<!--Row-->
@endsection

@if(isset($siswa))
@section('footjs')
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/jszip/datatables.min.js') }}"></script>

<!-- Page level custom scripts -->
<script src="{{ asset('js/level.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.custom-file-input')
@include('template.footjs.global.get-today-date')
@include('template.footjs.keuangan.datatables-thousands-dot-exportable')
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
                        '<td>'+item[8]+'</td>'+
                        '<td>'+item[9]+'</td>'
                    '</tr>';
                $('#tbody').append(row);
            });
            if(!$.fn.dataTable.isDataTable('#dataTable')){
                datatablesExportable([2,3,4,5,6,7,8],null,'Diekspor per '+getTodayDate());
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
@endsection
@endif