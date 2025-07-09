@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@if(isset($siswa))
@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/jszip/datatables.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
@endsection
@endif

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

<div class="row">
    @php
    $siswaOpt = ['calon','siswa'];
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
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ ucwords($opt.($opt == 'calon' ? ' Siswa' : null)) }}</div>
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
<div class="row">
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $plan && $plan->total_plan > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-chart-line text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Rencana</div>
                        <h6 class="mb-0">Rp {{number_format($plan?$plan->total_plan:'0', 0, ',', '.')}}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $plan && $plan->total_get > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-money-bill-wave text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500"> Realisasi</div>
                        <h6 class="mb-0">
                            Rp {{number_format($plan?$plan->total_get:'0', 0, ',', '.')}}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $plan && $plan->total_plan-$plan->total_get > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Selisih</div>
                        <h6 class="mb-0">
                            Rp {{number_format($plan?$plan->total_plan-$plan->total_get:'0', 0, ',', '.')}}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row">
            <div class="col-md-8">
                <form action="{{ route($route.'.index', ['siswa' => $siswa]) }}" method="get">
                    <div class="form-group row">
                        <label for="unit_id" class="col-sm-3 control-label">Unit</label>
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
                        <label for="year" class="col-sm-3 control-label">Tahun Pelajaran</label>
                        <div class="col-sm-5">
                            {{--<select name="year" class="select2 form-control auto_width" id="year" style="width:100%;">
                                @foreach(yearList() as $index => $year_list)
                                <option value="{{$year_list}}" {{$index==0?'selected':''}}>{{$year_list}}</option>
                                @endforeach
                            </select>--}}
                            <select aria-label="Tahun" name="year" class="select2 form-control auto_width" id="year" style="width:100%;">
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
                        <th>Tgl Transaksi</th>
                        @if(!isset($siswa) || $siswa != 'calon')
                        <th>NIPD</th>
                        <th>Nama Siswa</th>
                        @else                        
                        <th>No. PSB</th>
                        <th>Nama Calon Siswa</th>
                        @endif
                        <th>BMS Terbayar</th>
                        @if(in_array(Auth::user()->role->name,['fam', 'keu']))
                        <th class='action-col'>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tbody">
                    {{-- @foreach ( $lists as $list )
                    <tr>
                        <td>{{$list->created_at}}</td>
                        <td>{{$list->siswa->student_nis}}</td>
                        <td>{{$list->siswa->identitas->student_name}}</td>
                        <td>Rp {{number_format($list->nominal)}}</td>
                        <td>
                            @if($list->exchange_que)
                            @if($list->exchange_que == 1)
                            Dalam Pengajuan Pemindahan Dana
                            @else($list->exchange_que == 2)
                            Pengajuan Pemindahan Dana Disetujui
                            @endif
                            @else
                            <a href="#" class="btn btn-sm btn-success" data-total="{{$list->nominal}}" data-toggle="modal" data-target="#ubahKategori" data-siswa="{{!isset($siswa) || $siswa != 'calon' ? 1 : 0}}" data-name="{{$list->siswa->identitas->student_name}}" data-id="{{$list->id}}"><i class="fa fa-random"></i></a>
                            @endif
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

@if(in_array(Auth::user()->role->name,['fam', 'keu']))
<!-- Modal ubahKategori -->
<div id="ubahKategori" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <form action="{{ route($route.'.change.transaction') }}" method="POST">
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
                        <select name="jenis_pembayaran" class="form-control auto_width" id="jenis_pembayaran" style="width:100%;" tabindex="-1" aria-hidden="true" {{ isset($siswa) && $siswa == 'calon' ? 'readonly' : null }}>
                            <option value="1" selected>BMS</option>
                            @if(!isset($siswa) || $siswa != 'calon')
                            <option value="2">SPP</option>
                            @endif
                        </select>
                    </div>

                    <input type="hidden" name="id" class="form-control" id="id" value="0" disabled>
                    <input type="hidden" name="is_student" id="is_student" value="1">
                    <input type="hidden" name="total" class="form-control" id="total" value="0" disabled>
                    <div class="form-group">
                      <label for="nominal_siswa" class="col-form-label">Nominal</label>
                      <input type="text" name="nominal_siswa" class="form-control number-separator" id="nominal_siswa" value="0" required>
                    </div>

                    <div class="form-group">
                        <label for="split" class="col-form-label">Split dengan pembayaran lain?</label>
                        <select name="split" class="form-control auto_width" id="split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="0" selected>Tidak</option>
                            <option value="1" >Ya</option>
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="split" class="col-form-label">Kategori</label>
                        <select name="category_split" class="form-control auto_width" id="category_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="calon" selected>Calon Siswa</option>
                            <option value="siswa" >Siswa</option>
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="unit_split" class="col-form-label">Unit Calon Siswa</label>
                        <select name="unit_split" class="form-control auto_width" id="unit_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            @foreach (getUnits() as $index => $units)
                                <option value="{{$units->id}}" {{$index==0?'selected':''}}>{{$units->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="siswa_split" class="col-form-label">Pilih Calon Siswa</label>
                        <select name="siswa_split" class="select2 form-control auto_width" id="siswa_split" style="width:100%;">
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="jenis_pembayaran_split" class="col-form-label">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran_split" class="form-control auto_width" id="jenis_pembayaran_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="1" selected>BMS</option>
                            <option value="2" class="bg-gray-300" disabled>SPP</option>
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
@endif
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

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Number with Commas -->
<script src="{{ asset('js/number-with-commas.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.global.get-today-date')
@include('template.footjs.global.select2-default')
@include('template.footjs.keuangan.datatables-thousands-dot-exportable')

<!-- Page level custom scripts -->
@if(in_array(Auth::user()->role->name,['fam', 'keu']))
@include('template.footjs.keuangan.change-transaction-category')
@endif
@include('template.footjs.keuangan.change-filter-unit-month-report')
@endsection
@endif