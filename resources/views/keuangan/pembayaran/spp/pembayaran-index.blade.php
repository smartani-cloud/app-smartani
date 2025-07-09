<<<<<<< HEAD
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
                <form action="{{ route($route.'.index') }}" method="get">
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
        @if(Session::has('success') || Session::has('sukses'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::has('success') ? Session::get('success') : Session::get('sukses') }}
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
                        <th>NIPD</th>
                        <th>Nama Siswa</th>
                        <th>SPP Terbayar</th>
                        @if(in_array(Auth::user()->role->name,['fam', 'keu']))
                        <th class='action-col'>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tbody">
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
            <form action="{{ route($route.'.change') }}" method="POST">
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
                        <select name="jenis_pembayaran" class="form-control auto_width" id="jenis_pembayaran" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="1">BMS</option>
                            <option value="2" selected>SPP</option>
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

<!--Row-->
@endsection

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
=======
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
                <form action="{{ route($route.'.index') }}" method="get">
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
        @if(Session::has('success') || Session::has('sukses'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::has('success') ? Session::get('success') : Session::get('sukses') }}
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
                        <th>NIPD</th>
                        <th>Nama Siswa</th>
                        <th>SPP Terbayar</th>
                        @if(in_array(Auth::user()->role->name,['fam', 'keu']))
                        <th class='action-col'>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody id="tbody">
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
            <form action="{{ route($route.'.change') }}" method="POST">
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
                        <select name="jenis_pembayaran" class="form-control auto_width" id="jenis_pembayaran" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="1">BMS</option>
                            <option value="2" selected>SPP</option>
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

<!--Row-->
@endsection

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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection