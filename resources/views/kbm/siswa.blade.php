<<<<<<< HEAD
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Siswa</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="2020/2021 Ganjil (SMT 1)" readonly="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-9">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tingkat Kelas ==</option>
                                    <option value="10">Kelas 10</option>
                                    <option value="11">Kelas 11</option>
                                    <option value="12" selected>Kelas 12</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Siswa</h6>
                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/penilaian/tambahpredikatsikap">Tambah <i class="fas fa-plus"></i></a>
                        </div>
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>MAN30221</td>
                                    <td>332112312332</td>
                                    <td>Ihsan Fawzi</td>
                                    <td>19-09-1997</td>
                                    <td>Laki-laki</td>
                                    <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>MAN30222</td>
                                    <td>332112312543</td>
                                    <td>Indra Kusuma</td>
                                    <td>19-09-1997</td>
                                    <td>Laki-laki</td>
                                    <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>MAN30223</td>
                                    <td>332112312433</td>
                                    <td>Hari Wanto</td>
                                    <td>19-09-1997</td>
                                    <td>Laki-laki</td>
                                    <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
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

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Siswa</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Tahun Ajaran</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="2020/2021 Ganjil (SMT 1)" readonly="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-9">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tingkat Kelas ==</option>
                                    <option value="10">Kelas 10</option>
                                    <option value="11">Kelas 11</option>
                                    <option value="12" selected>Kelas 12</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Siswa</h6>
                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/penilaian/tambahpredikatsikap">Tambah <i class="fas fa-plus"></i></a>
                        </div>
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>MAN30221</td>
                                    <td>332112312332</td>
                                    <td>Ihsan Fawzi</td>
                                    <td>19-09-1997</td>
                                    <td>Laki-laki</td>
                                    <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>MAN30222</td>
                                    <td>332112312543</td>
                                    <td>Indra Kusuma</td>
                                    <td>19-09-1997</td>
                                    <td>Laki-laki</td>
                                    <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>MAN30223</td>
                                    <td>332112312433</td>
                                    <td>Hari Wanto</td>
                                    <td>19-09-1997</td>
                                    <td>Laki-laki</td>
                                    <td><a href="#" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
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