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
    <h1 class="h3 mb-0 text-gray-800">Daftar Transaksi Sumbangan Pembinaan Pendidikan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sumbangan Pembinaan Pendidikan</li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Transaksi</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Daftar Transaksi</h6>
                        </div>
                        @if(Session::has('sukses'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> {{ Session::get('sukses') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>NIPD</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>20-01-2021</td>
                                    <td>SMA33001</td>
                                    <td>M Ihsan Fawzi</td>
                                    <td>Rp 2.000.000</td>
                                </tr>
                                <tr>
                                    <td>20-01-2021</td>
                                    <td>SMA33002</td>
                                    <td>Hari Wanto</td>
                                    <td>Rp 3.500.000</td>
                                </tr>
                                <tr>
                                    <td>20-01-2021</td>
                                    <td>SMA33003</td>
                                    <td>Indra Kusuma</td>
                                    <td>Rp 2.500.000</td>
                                </tr>
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
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
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
    <h1 class="h3 mb-0 text-gray-800">Daftar Transaksi Sumbangan Pembinaan Pendidikan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Sumbangan Pembinaan Pendidikan</li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Transaksi</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Daftar Transaksi</h6>
                        </div>
                        @if(Session::has('sukses'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> {{ Session::get('sukses') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>NIPD</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>20-01-2021</td>
                                    <td>SMA33001</td>
                                    <td>M Ihsan Fawzi</td>
                                    <td>Rp 2.000.000</td>
                                </tr>
                                <tr>
                                    <td>20-01-2021</td>
                                    <td>SMA33002</td>
                                    <td>Hari Wanto</td>
                                    <td>Rp 3.500.000</td>
                                </tr>
                                <tr>
                                    <td>20-01-2021</td>
                                    <td>SMA33003</td>
                                    <td>Indra Kusuma</td>
                                    <td>Rp 2.500.000</td>
                                </tr>
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
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection