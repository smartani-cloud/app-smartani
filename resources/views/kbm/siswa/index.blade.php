@extends('template.main.master')

@section('title')
    @if(Request::path()!='kependidikan/kbm/siswa/alumni')
        Daftar Siswa Aktif
    @else
        Daftar Siswa Alumni
    @endif
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    @if(Request::path()!='kependidikan/kbm/siswa/alumni')
        <h1 class="h3 mb-0 text-gray-800">Siswa Aktif</h1>
    @else
        <h1 class="h3 mb-0 text-gray-800">Siswa Alumni</h1>
    @endif
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Siswa</li>
    </ol>
</div>

@if(Request::path()!='kependidikan/kbm/siswa/alumni')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row mb-0">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-5">
                                <select id="filterlevel" onchange="filterData()" name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
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
                            {{-- <button class="btn btn-brand-green-dark btn-sm" type="button">Saring</button> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                @if(Request::path()!='kependidikan/kbm/siswa/alumni')
                <h6 class="m-0 font-weight-bold text-brand-green">Siswa Aktif</h6>
                @else
                <h6 class="m-0 font-weight-bold text-brand-green">Siswa Alumni</h6>
                @endif
                @if(!in_array((auth()->user()->role->name), ['fam','faspv','lay']) || (in_array((auth()->user()->role_id), array(1,5))))
                <div class="float-right">
                @if(!in_array((auth()->user()->role->name), ['fam','faspv','lay']))
                <a id="download" class="m-0 btn btn-success btn-sm" href="{{\Request::url()}}/download">Ekspor<i class="fas fa-file-export ml-2"></i></a>
                @endif
                @if( in_array((auth()->user()->role_id), array(1,5)))
                <a class="m-0 btn btn-brand-green-dark btn-sm" href="/kependidikan/kbm/siswa/tambah">Tambah<i class="fas fa-plus ml-2"></i></a>
                @endif
                </div>
                @endif
            </div>
            <div class="card-body">
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @if(Session::has('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> {{ Session::get('danger') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                <div class="table-responsive">                        
                    <table id="dataTable" class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>NIPD</th>
                                <th>NISN</th>
                                <th>Nama</th>
                                <th>Tanggal Lahir</th>
                                <th>Jenis Kelamin</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if(in_array((auth()->user()->role->name), ['fam','faspv']))
<!-- Modal Konfirmasi -->
<div id="awalSppModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <h4 class="modal-title w-100">Ubah Awal Mula SPP</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('siswa.ubah-awal-spp')}}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="modal-body" id="form_penerimaan" style="display:block">
                    <div class="form-group">
                      <label for="year_spp" class="col-form-label">Nama</label>
                      <input type="text" name="name" class="form-control" value="-" disabled>
                    </div>
                    <div class="form-group">
                      <label for="year_spp" class="col-form-label">Tahun Mulai SPP</label>
                      <input type="number" name="year_spp" class="form-control" id="year_spp" value="{{date('Y')}}" min="2000" required>
                    </div>
                    <div class="form-group">
                        <label for="month_spp" class="col-form-label">Bulan Mulai SPP</label>
                        <select name="month_spp" class="select2 form-control select2-hidden-accessible auto_width" id="month_spp" style="width:100%;" tabindex="-1" aria-hidden="true" required>
                            <option value="1" selected>Januari</option>
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
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <input type="hidden" name="id" id="id" class="id"/>
                    <button type="submit" class="btn btn-success">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if( in_array((auth()->user()->role_id), array(1,2,18)))
<!-- Modal Hapus -->
<div id="HapusModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xE5CD;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p id="confirmationname">Apakah Anda yakin akan menghapus data Nama Siswa?.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="modalhapussiswa" action="/kependidikan/kbm/siswa/hapus/idsiswa" method="POST">
                    @csrf
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kbm.datatables')

<script type="text/javascript">
    $(document).ready(function(){
        filterData();
        var filter = $('#filterlevel').val();
        $('#awalSppModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var name_calon = button.data('name') // Extract info from data-* attributes
            var year = button.data('year') // Extract info from data-* attributes
            var month = button.data('month') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
            modal.find('input[name="name"]').val(name_calon)
            modal.find('input[name="year_spp"]').val(year)
            modal.find('select[name="month_spp"]').val(month)
        })
        $('#HapusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var nama = button.data('nama') // Extract info from data-* attributes
            var siswa = button.data('siswa') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('#confirmationname').text('Apakah Anda yakin akan menghapus data ' + nama + '?');
            modal.find('#modalhapussiswa').attr("action", "/kependidikan/kbm/siswa/hapus/"+siswa);
        });
    });

    function filterData(){
        var filter = $('#filterlevel').val();
        var uri = window.location.href+'/download?filter='+filter;
        $("#download").attr("href",uri);

        var uri = window.location.href+'/datatables?filter='+filter;
        $('#dataTable').DataTable().destroy();
        $('#dataTable').DataTable( {
            processing: true,
            serverSide: true,
            ajax: uri,
        } );
    }
</script>
@endsection