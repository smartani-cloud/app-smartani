@extends('template.main.master')

@section('title')
Sumbangan Pembinaan Pendidikan
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard Pembayaran</h1>
    <ol class="breadcrumb">
        {{-- <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li> --}}
        <li class="breadcrumb-item active" aria-current="page">Dashboard Pembayaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Dashboard Pembayaran</h6>
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
                                    <th>Pembayaran</th>
                                    <th>Tahun</th>
                                    <th>Rencana</th>
                                    <th>Realisasi</th>
                                    <th>Selisih</th>
                                    <th>Jumlah Siswa Lunas</th>
                                    <th>Jumlah Siswa Belum Lunas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>SPP</td>
                                    <td>2021</td>
                                    <td>1000000</td>
                                    <td>1000000</td>
                                    <td>0</td>
                                    <td>100</td>
                                    <td>0</td>
                                </tr>
                                <tr>
                                    <td>BMS</td>
                                    <td>2021</td>
                                    <td>1000000</td>
                                    <td>1000000</td>
                                    <td>0</td>
                                    <td>100</td>
                                    <td>0</td>
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
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
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

<script>
    $(document).ready(function()
    {

        $('#ubahKategori').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var name = button.data('name') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
            modal.find('p[id="name"]').text('Apakah Anda yakin akan mengubah kategori transaksi '+name+'?');
        })


    })
</script>
@endsection