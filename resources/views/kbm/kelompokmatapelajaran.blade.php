@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelompok Mata Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kelompok Mata Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Kelompok Mata Pelajaran</h6>
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="kelompok-mata-pelajaran/tambah">Tambah <i class="fas fa-plus"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Kelompok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach( $lists as $indexKey => $list)
                        <tr>
                            <td>{{ $indexKey+1 }}</td>
                            <td>{{ $list->group_subject_name }}</td>
                            <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer"></div>
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