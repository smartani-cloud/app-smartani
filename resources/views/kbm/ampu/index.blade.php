<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Kelas diampu
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Siswa Kelas Ku</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kelas Diampu</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                @if($kelas == null)
                    <h6 class="m-0 font-weight-bold text-brand-green">Anda Tidak Memiliki Kelas</h6>
                @else
                    <h6 class="m-0 font-weight-bold text-brand-green">Kelas {{$kelas->level->level}} {{$kelas->namakelases->class_name}} : {{$kelas->walikelas->name}}</h6>
                    <button class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#TambahModal">Tambah siswa <i class="fas fa-plus"></i></button>
                    @if( $kelas->status == 1 || $kelas->status == 4 )
                    <button class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#TambahModal">Tambah siswa <i class="fas fa-plus"></i></button>
                    @endif
                    <a class="m-0 float-right btn btn-success btn-sm" href="/kependidikan/kbm/kelas/kelas-diampu/cetak">Unduh <i class="fas fa-download"></i></a>
                @endif
            </div>

            @if($kelas !== null)
            @if($kelas->status == 4)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Pengajuan ditolak!</strong> Silahkan untuk mengajukan kembali
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if($kelas->status == 2)
                @if(Session::has('sukses'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('sukses') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @elseif(Session::has('gagal_tambah'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Penambahan Gagal!</strong> {{ Session::get('gagal_tambah') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @else
                <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                    <strong>Pengajuan sedang ditinjau!</strong> Silahkan untuk menunggu persetujuan
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
            @endif
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Tanggal Lahir</th>
                            @if( $kelas->status == 1 || $kelas->status == 4 )
                            <th>Aksi</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $urut = 1;
                    @endphp
                    @foreach( $siswas as $index => $siswa)
                        <tr>
                            <td>{{ $urut }}</td>
                            <td>{{ $siswa->identitas ? $siswa->identitas->student_name : '-' }}</td>
                            <td>{{ ucwords($siswa->identitas->jeniskelamin->name) }}</td>
                            <td>{{ $siswa->identitas->birth_place }}</td>
                            <td>{{ \Carbon\Carbon::parse($siswa->identitas->birth_date)->format('d-m-Y')}}</td>
                            @if( $kelas->status == 1 || $kelas->status == 4 )
                            <td><button href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash" data-toggle="modal" data-target="#HapusModal{{ $siswa->id }}"></i></button></td>
                            @endif
                            <td><button href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash" data-toggle="modal" data-target="#HapusModal{{ $siswa->id }}"></i></button></td>
                        </tr>
                        @php
                            $urut++;
                        @endphp
                    @endforeach
                    </tbody>
                </table>
                @if( $kelas->status == 1 || $kelas->status == 4 )
                <div class="text-center mt-4">
                    <form action="/kependidikan/kbm/kelas/kelas-diampu/ajukan/{{$kelas->id}}" method="POST">
                    @csrf
                        <button type="submit" class="btn btn-brand-green-dark">Ajukan</button>
                    </form>
                </div>
                @endif
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>


@if($kelas !== null)
<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/kependidikan/kbm/kelas/kelas-diampu/tambah/{{ $kelas->id }}"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Tambah Siswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <select name="siswa" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                        @foreach( $siswakosong as $siswa )
                            <option value="{{ $siswa->id }}">{{ $siswa->identitas ? $siswa->identitas->student_name : '-' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>
    </div>
  </div>
</div>
@endif


@if($kelas !== null)
@foreach(  $siswas as $siswa)
<!-- Modal Hapus -->
<div class="modal fade" id="HapusModal{{ $siswa->id }}" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/kependidikan/kbm/kelas/kelas-diampu/hapus/{{ $siswa->id }}"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Peringatan Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Menghapus {{ $siswa->identitas->student_name }} dari kelas anda?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
  </div>
</div>
@endforeach
@endif

<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
=======
@extends('template.main.master')

@section('title')
Kelas diampu
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Siswa Kelas Ku</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kelas Diampu</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                @if($kelas == null)
                    <h6 class="m-0 font-weight-bold text-brand-green">Anda Tidak Memiliki Kelas</h6>
                @else
                    <h6 class="m-0 font-weight-bold text-brand-green">Kelas {{$kelas->level->level}} {{$kelas->namakelases->class_name}} : {{$kelas->walikelas->name}}</h6>
                    <button class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#TambahModal">Tambah siswa <i class="fas fa-plus"></i></button>
                    @if( $kelas->status == 1 || $kelas->status == 4 )
                    <button class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#TambahModal">Tambah siswa <i class="fas fa-plus"></i></button>
                    @endif
                    <a class="m-0 float-right btn btn-success btn-sm" href="/kependidikan/kbm/kelas/kelas-diampu/cetak">Unduh <i class="fas fa-download"></i></a>
                @endif
            </div>

            @if($kelas !== null)
            @if($kelas->status == 4)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Pengajuan ditolak!</strong> Silahkan untuk mengajukan kembali
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if($kelas->status == 2)
                @if(Session::has('sukses'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('sukses') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @elseif(Session::has('gagal_tambah'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Penambahan Gagal!</strong> {{ Session::get('gagal_tambah') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @else
                <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                    <strong>Pengajuan sedang ditinjau!</strong> Silahkan untuk menunggu persetujuan
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
            @endif
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tempat Lahir</th>
                            <th>Tanggal Lahir</th>
                            @if( $kelas->status == 1 || $kelas->status == 4 )
                            <th>Aksi</th>
                            @endif
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php
                        $urut = 1;
                    @endphp
                    @foreach( $siswas as $index => $siswa)
                        <tr>
                            <td>{{ $urut }}</td>
                            <td>{{ $siswa->identitas ? $siswa->identitas->student_name : '-' }}</td>
                            <td>{{ ucwords($siswa->identitas->jeniskelamin->name) }}</td>
                            <td>{{ $siswa->identitas->birth_place }}</td>
                            <td>{{ \Carbon\Carbon::parse($siswa->identitas->birth_date)->format('d-m-Y')}}</td>
                            @if( $kelas->status == 1 || $kelas->status == 4 )
                            <td><button href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash" data-toggle="modal" data-target="#HapusModal{{ $siswa->id }}"></i></button></td>
                            @endif
                            <td><button href="#" class="btn btn-sm btn-danger"><i class="fas fa-trash" data-toggle="modal" data-target="#HapusModal{{ $siswa->id }}"></i></button></td>
                        </tr>
                        @php
                            $urut++;
                        @endphp
                    @endforeach
                    </tbody>
                </table>
                @if( $kelas->status == 1 || $kelas->status == 4 )
                <div class="text-center mt-4">
                    <form action="/kependidikan/kbm/kelas/kelas-diampu/ajukan/{{$kelas->id}}" method="POST">
                    @csrf
                        <button type="submit" class="btn btn-brand-green-dark">Ajukan</button>
                    </form>
                </div>
                @endif
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>


@if($kelas !== null)
<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/kependidikan/kbm/kelas/kelas-diampu/tambah/{{ $kelas->id }}"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Tambah Siswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <select name="siswa" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                        @foreach( $siswakosong as $siswa )
                            <option value="{{ $siswa->id }}">{{ $siswa->identitas ? $siswa->identitas->student_name : '-' }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>
    </div>
  </div>
</div>
@endif


@if($kelas !== null)
@foreach(  $siswas as $siswa)
<!-- Modal Hapus -->
<div class="modal fade" id="HapusModal{{ $siswa->id }}" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/kependidikan/kbm/kelas/kelas-diampu/hapus/{{ $siswa->id }}"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Peringatan Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Menghapus {{ $siswa->identitas->student_name }} dari kelas anda?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
  </div>
</div>
@endforeach
@endif

<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection