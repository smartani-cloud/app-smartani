@extends('template.main.master')

@section('title')
Refund
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
    <h1 class="h3 mb-0 text-gray-800">Refund</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/keuangan/pemindahan-transaksi')}}">Pemindahan Transaksi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Refund</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Refund</h6>
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
                                    <th>Transaksi Asal</th>
                                    <th>Nominal Awal</th>
                                    <th>Refund</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($datas as $data)
                                <tr>
                                    <td>{{$data->created_at}}</td>
                                    <td>{{$data->transactionOrigin->siswa->student_nis}}</td>
                                    <td>{{$data->transactionOrigin->siswa->identitas->student_name}}</td>
                                    <td>{{$data->origin==1?'BMS':'SPP'}}</td>
                                    <td>Rp {{number_format($data->nominal)}}</td>
                                    <td>Rp {{number_format($data->refund)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
@endsection