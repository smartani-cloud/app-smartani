@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Kelas</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pengajuan Kelas</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Pengajuan Kelas</h6>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>10</td>
                            <td>Diponegoro</td>
                            <td>Ihsan Fawzi</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;
                                <a href="#" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>&nbsp;
                                <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>10</td>
                            <td>Cut Nyak Dien</td>
                            <td>Indra Kusuma</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;
                                <a href="#" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>&nbsp;
                                <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>10</td>
                            <td>Ki Hajar Dewantara</td>
                            <td>Hari Wanto</td>
                            <td>
                                <a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;
                                <a href="#" class="btn btn-sm btn-success"><i class="fas fa-check"></i></a>&nbsp;
                                <a href="#" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></a>
                            </td>
                        </tr>
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