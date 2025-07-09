<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Tambah Daftar Kelas
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Daftar Kelas</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/nama-kelas">Daftar Kelas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <form action="/kependidikan/kbm/kelas/daftar-kelas/tambah"  method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas</label>
                            <div class="col-sm-6">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $levels as $level )
                                    <option value="{{ $level->id }}">{{ $level->level }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        @if( auth()->user()->pegawai->unit_id == 4)
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Jurusan</label>
                            <div class="col-sm-6">
                                <select name="jurusan" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $jurusans as $jurusan )
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->major_name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Nama Kelas</label>
                            <div class="col-sm-6">
                                <select name="nama_kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $namakelases as $namakelas )
                                    <option value="{{ $namakelas->id }}">{{ $namakelas->class_name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Wali Kelas</label>
                            <div class="col-sm-6">
                                <select name="wali_kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $gurus as $guru )
                                    <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                        </div>
                    </div>
                </div>
                </form>
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

@section('title')
Tambah Daftar Kelas
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Daftar Kelas</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/nama-kelas">Daftar Kelas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <form action="/kependidikan/kbm/kelas/daftar-kelas/tambah"  method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas</label>
                            <div class="col-sm-6">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $levels as $level )
                                    <option value="{{ $level->id }}">{{ $level->level }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        @if( auth()->user()->pegawai->unit_id == 4)
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Jurusan</label>
                            <div class="col-sm-6">
                                <select name="jurusan" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $jurusans as $jurusan )
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->major_name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Nama Kelas</label>
                            <div class="col-sm-6">
                                <select name="nama_kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $namakelases as $namakelas )
                                    <option value="{{ $namakelas->id }}">{{ $namakelas->class_name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Wali Kelas</label>
                            <div class="col-sm-6">
                                <select name="wali_kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $gurus as $guru )
                                    <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                        </div>
                    </div>
                </div>
                </form>
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