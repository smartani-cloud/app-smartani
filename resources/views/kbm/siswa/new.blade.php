@extends('template.main.master')

@section('title')
Daftar Siswa
@endsection

@section('headmeta')
  <link href="{{ asset('public/buttons.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

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
                @if(Session::has('sukses'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('sukses') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                <div class="row">
                    <div class="col-md-8">
                    <form action="/kependidikan/kbm/siswa" method="POST">
                    @csrf
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
                            <h6 class="m-0 font-weight-bold text-brand-green">Siswa</h6>
                            @if( in_array((auth()->user()->role_id), array(1,5)))
                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/kependidikan/kbm/siswa/tambah">Tambah <i class="fas fa-plus"></i></a>
                            @endif
                        </div>
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th id="hide"   >No Pendaftaran</th>
                                    <th id="hide"   >Program</th>
                                    <th id="hide"   >Tanggal Daftar</th>
                                    <th id="hide"   >Tahun Ajaran</th>
                                    <th id="hide"   >Tingkat Kelas</th>
                                    <th>NIPD</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    <th id="hide"   >Nama Panggilan</th>
                                    <th id="hide"   >Tempat Lahir</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jenis Kelamin</th>
                                    <th id="hide"   >Agama</th>
                                    <th id="hide"   >Anak Ke</th>
                                    <th id="hide"   >Status Anak</th>
                                    <th id="hide"   >Alamat</th>
                                    <th id="hide"   >No</th>
                                    <th id="hide"   >RT</th>
                                    <th id="hide"   >RW</th>
                                    <th id="hide"   >Wilayah</th>
                                    <th id="hide"   >Nama Ayah</th>
                                    <th id="hide"   >NIK Ayah</th>
                                    <th id="hide"   >HP Ayah</th>
                                    <th id="hide"   >HP Email Ayah</th>
                                    <th id="hide"   >Pekerjaan Ayah</th>
                                    <th id="hide"   >Jabatan Ayah</th>
                                    <th id="hide"   >Telp Kantor Ayah</th>
                                    <th id="hide"   >Alamat Kantor Ayah</th>
                                    <th id="hide"   >Gaji Ayah</th>
                                    <th id="hide"   >Nama Ibu</th>
                                    <th id="hide"   >NIK Ibu</th>
                                    <th id="hide"   >HP Ibu</th>
                                    <th id="hide"   >HP Email Ibu</th>
                                    <th id="hide"   >Pekerjaan Ibu</th>
                                    <th id="hide"   >Jabatan Ibu</th>
                                    <th id="hide"   >Telp Kantor Ibu</th>
                                    <th id="hide"   >Alamat Kantor Ibu</th>
                                    <th id="hide"   >Gaji Ibu</th>
                                    <th id="hide"   >NIP (Orang tua yang bekerja di Auliya)</th>
                                    <th id="hide"   >Alamat Orang Tua</th>
                                    <th id="hide"   >HP Alternatif</th>
                                    <th id="hide"   >Nama Wali</th>
                                    <th id="hide"   >NIK Wali</th>
                                    <th id="hide"   >HP Wali</th>
                                    <th id="hide"   >HP Email Wali</th>
                                    <th id="hide"   >Pekerjaan Wali</th>
                                    <th id="hide"   >Jabatan Ibu</th>
                                    <th id="hide"   >Telp Kantor Wali</th>
                                    <th id="hide"   >Alamat Kantor Wali</th>
                                    <th id="hide"   >Gaji Wali</th>
                                    <th id="hide"   >Alamat Wali</th>
                                    <th id="hide"   >Asal Sekolah</th>
                                    <th id="hide"   >Saudara Kandung</th>
                                    <th id="hide"   >Nama Saudara</th>
                                    <th id="hide"   >Info Dari</th>
                                    <th id="hide"   >Nama</th>
                                    <th id="hide"   >Posisi</th>
                                    <th id="hide"   >class_id</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table_siswa">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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
                <p>Apakah Anda yakin akan menghapus data ?.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="/kependidikan/kbm/siswa/hapus/" method="POST">
                    @csrf
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
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
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>
<!-- <script src="{{asset('js/siswa/SemuaSiswa.js')}}"></script>
<script src="{{asset('js/siswa/toDatatable.js')}}"></script> -->
<script src="{{asset('js/siswa/SiswaDatatable.js')}}"></script> 

<!-- Page level custom scripts -->
<!-- @include('template.footjs.kbm.cetakdatatables') -->
@include('template.footjs.kbm.hideelement')
@endsection