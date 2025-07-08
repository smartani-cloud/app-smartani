@extends('template.main.master')

@section('title')
Sumbangan Pembinaan Pendidikan
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">SPP Siswa Bulanan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">SPP Siswa Bulanan</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="" method="POST">
                        @csrf
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Tahun</label>
                                <div class="col-sm-5">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="semua">2021</option>
                                        <option value="semua">2020</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Bulan</label>
                                <div class="col-sm-5">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="0">Semua</option>
                                        <option value="1">Januari</option>
                                        <option value="2">Februari</option>
                                        <option value="3">Maret</option>
                                        <option value="4">April</option>
                                        <option value="5">Mei</option>
                                        <option value="6">Juni</option>
                                        <option value="7">Juli</option>
                                        <option value="8">Agustus</option>
                                        <option value="9">September</option>
                                        <option value="10">Oktober</option>
                                        <option value="11">November</option>
                                        <option value="12">Desember</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Unit</label>
                                <div class="col-sm-5">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="semua">TK</option>
                                        <option value="semua">SD</option>
                                        <option value="semua">SMP</option>
                                        <option value="semua">SMA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                                <div class="col-sm-5">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @foreach( $levels as $tingkat)
                                        @if( $level == $tingkat->id )
                                            <option value="{{$tingkat->id}}" selected>{{$tingkat->level}}</option>
                                        @else
                                        <option value="{{$tingkat->id}}">{{$tingkat->level}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-brand-green-dark btn-sm" type="submit">Saring</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Laporan SPP Siswa</h6>
                            <div class="float-right">
                            </div>
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
                                    <th>NIPD</th>
                                    <th>Nama</th>
                                    <th>Nominal</th>
                                    <th>Terbayar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SMA33001</td>
                                    <td>M Ihsan Fawzi</td>
                                    <td>Rp 2.000.000</td>
                                    <td>Rp 2.000.000</td>
                                    <td>Lunas</td>
                                </tr>
                                <tr>
                                    <td>SMA33002</td>
                                    <td>Hari Wanto</td>
                                    <td>Rp 3.500.000</td>
                                    <td>Rp 3.500.000</td>
                                    <td>Lunas</td>
                                </tr>
                                <tr>
                                    <td>SMA33003</td>
                                    <td>Indra Kusuma</td>
                                    <td>Rp 2.500.000</td>
                                    <td>Rp 1.500.000</td>
                                    <td>Belum Lunas</td>
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