@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Waktu Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Waktu Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Hari</label>
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
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Jam Ke</th>
                                    <th>Jam Mulai</th>
                                    <th>Jam Selesai</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>07:15</td>
                                    <td>07:40</td>
                                    <td>Sholat Dhuha, Tilawah & Pembukaan Kelas</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>07:40</td>
                                    <td>08:15</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>08:15</td>
                                    <td>08:50</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>08:50</td>
                                    <td>09:25</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>09:25</td>
                                    <td>10:00</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>6</td>
                                    <td>10:00</td>
                                    <td>10:30</td>
                                    <td>Istirahat</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>7</td>
                                    <td>10:30</td>
                                    <td>11:05</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>8</td>
                                    <td>11:05</td>
                                    <td>11:40</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>9</td>
                                    <td>11:40</td>
                                    <td>12:40</td>
                                    <td>Istirahat</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>10</td>
                                    <td>12:40</td>
                                    <td>13:15</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>11</td>
                                    <td>13:15</td>
                                    <td>13:50</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>12</td>
                                    <td>13:50</td>
                                    <td>14:25</td>
                                    <td>-</td>
                                    <td><a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;<a href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a></td>
                                </tr>
                                <tr>
                                    <td>13</td>
                                    <td>14:25</td>
                                    <td>15:00</td>
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