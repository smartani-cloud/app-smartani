@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelas Diampu</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kelas Diampu</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Kelas 10 Diponegoro : Ihsan Fawzi, S.Kom</h6>
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/penilaian/tambahpredikatsikap">Tambah siswa <i class="fas fa-plus"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Tanggal Lahir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Ihsan Fawzi</td>
                            <td>Laki-laki</td>
                            <td>Tangerang Selatan</td>
                            <td>19-09-1997</td>
                            <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Indra Kusuma</td>
                            <td>Laki-laki</td>
                            <td>Banjarnegara</td>
                            <td>19-09-1997</td>
                            <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Haro Wanto</td>
                            <td>Laki-laki</td>
                            <td>Bogor</td>
                            <td>19-09-1997</td>
                            <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a></td>
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