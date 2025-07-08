@extends('template.main.master')

@section('title')
Biaya Masuk Sekolah
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Rencana Dan Realisasi</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Biaya Masuk Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Rencana Dan Realisasi</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                    <form action="/keuangan/bms/siswa" method="POST">
                    @csrf
                        @if($unit_id==5)
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Unit</label>
                            <div class="col-sm-5">
                                <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="semua">Semua</option>
                                    <option value="1" selected>TK</option>
                                    <option value="2" selected>SD</option>
                                    <option value="3" selected>SMP</option>
                                    <option value="4" selected>SMA</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-5">
                                <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="semua">Semua</option>
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
                            <h6 class="m-0 font-weight-bold text-brand-green">Rencana & Realisasi</h6>
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
                                    <th>Tahun</th>
                                    <th>Rencana</th>
                                    <th>Realisasi</th>
                                    <th>Selisih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>2020</td>
                                    <td>Rp 250.000.000</td>
                                    <td>Rp 200.000.000</td>
                                    <td>Rp 50.000.000</td>
                                </tr>
                                <tr>
                                    <td>2021</td>
                                    <td>Rp 300.000.000</td>
                                    <td>Rp 200.000.000</td>
                                    <td>Rp 100.000.000</td>
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