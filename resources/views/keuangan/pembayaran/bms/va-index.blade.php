@extends('template.main.master')

@section('title')
{{ $active }} 
@endsection

@section('headmeta')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/jszip/datatables.min.css') }}" rel="stylesheet">
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
      <div class="card-body p-3">
        <div class="row">
          <div class="col-md-8">
            <form action="{{ route($route.'.get') }}" method="POST">
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
                        <th>NIPD</th>
                        <th>Nama Siswa</th>
                        <th>Unit</th>
                        <th>Kelas</th>
                        <th>Nomor VA BMS</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    {{-- @foreach ($lists as $list)
                        <tr>
                            <td>{{$list->siswa?$list->siswa->student_nis:'-'}}</td>
                            <td>{{$list->siswa?$list->siswa->identitas->student_name:'-'}}</td>
                            <td>{{$list->siswa?$list->siswa->unit->name:'-'}}</td>
                            <td>{{$list->siswa?$list->siswa->level?$list->siswa->level->level:'-':'-'}}</td>
                            <td>{{$list->bms_va}}</td>
                        </tr>
                    @endforeach --}}
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
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/jszip/datatables.min.js') }}"></script>

<!-- DataTables Button Function -->
<script src="{{ asset('js/functions/dataTables-button.js') }}"></script>

<!-- Unit-Levels -->
<script src="{{ asset('js/level.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.keuangan.change-filter-unit-level-va')
@endsection