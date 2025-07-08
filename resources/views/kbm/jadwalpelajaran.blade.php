@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Jadwal Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Jadwal Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-9">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Kelas ==</option>
                                    <option value="10">10 Diponegoro</option>
                                    <option value="11">11 Cut Nyak Dien</option>
                                    <option value="12" selected>12 Sultan Hasanuddin</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Hari</label>
                            <div class="col-sm-9">
                                <select name="hari" class="select2 form-control select2-hidden-accessible auto_width" id="hari" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Hari ==</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu" selected>Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Jadwal Pelajaran</h6>
                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/kependidikan/kbm/jadwal-pelajaran">Ekspor Semua <i class="fas fa-file-export"></i></a>
                        </div>
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Jam Ke</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Sholat Dhuha, Tilawah & Pembukaan Kelas</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Matematika</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Fisika</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Bahasa Inggris</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Ekonomi</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>Istirahat</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>Kimia</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>Bahasa Jerman</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>Istirahat</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td>-</td>
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