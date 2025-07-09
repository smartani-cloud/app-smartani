@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Mata Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Mata Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Mata Pelajaran</h6>
                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/penilaian/tambahpredikatsikap">Tambah <i class="fas fa-plus"></i></a>
                        </div>
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelompok Mata Pelajaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Matematika</td>
                                    <td>Kelompok A</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Biologi</td>
                                    <td>Kelompok B</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Fisika</td>
                                    <td>Kelompok C</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
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
@endsection