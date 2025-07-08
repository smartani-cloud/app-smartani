@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cetak Rapor Penilaian Akhir Semester</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cetak Rapor PAS</li>
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
                                <input type="text" class="form-control" value="2020/2021 Ganjil (SMT 1)">
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
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Rombongan Belajar</label>
                            <div class="col-sm-9">
                                <select name="rombel_id" class="select2 form-control select2-hidden-accessible auto_width" id="rombel" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Rombongan Belajar ==</option>
                                    <option value="076bffb1-96e9-42fa-bbed-69a60d572f2d" selected>IPA 1</option>
                                    <option value="085bacd7-dfda-406f-bfb8-8cb63946c9cd">IPA 2</option>
                                    <option value="271267fb-169a-404a-8904-fbb37c6cc685">IPS 1</option>
                                    <option value="271267fb-169a-404a-8904-fbb37c6cc685">IPS 2</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Peserta Didik</th>
                                    <th colspan="3" class="text-center">Cetak Rapor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Anton</td>
                                    <td class="text-center"><button class="btn btn-sm btn-info"><i class="fa fa-print"></i> Cetak Cover</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Cetak Laporan PAS</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-secondary"><i class="fa fa-print"></i> Cetak Halaman Akhir</button></td>
                                </tr>
                                <tr>
                                    <td>Budi</td>
                                    <td class="text-center"><button class="btn btn-sm btn-info"><i class="fa fa-print"></i> Cetak Cover</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Cetak Laporan PAS</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-secondary"><i class="fa fa-print"></i> Cetak Halaman Akhir</button></td>
                                </tr>
                                <tr>
                                    <td>Chandra</td>
                                    <td class="text-center"><button class="btn btn-sm btn-info"><i class="fa fa-print"></i> Cetak Cover</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Cetak Laporan PAS</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-secondary"><i class="fa fa-print"></i> Cetak Halaman Akhir</button></td>
                                </tr>
                                <tr>
                                    <td>Dewi</td>
                                    <td class="text-center"><button class="btn btn-sm btn-info"><i class="fa fa-print"></i> Cetak Cover</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Cetak Laporan PAS</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-secondary"><i class="fa fa-print"></i> Cetak Halaman Akhir</button></td>
                                </tr>
                                <tr>
                                    <td>Erna</td>
                                    <td class="text-center"><button class="btn btn-sm btn-info"><i class="fa fa-print"></i> Cetak Cover</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-primary"><i class="fa fa-print"></i> Cetak Laporan PAS</button></td>
                                    <td class="text-center"><button class="btn btn-sm btn-secondary"><i class="fa fa-print"></i> Cetak Halaman Akhir</button></td>
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