<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Tambah Kelompok Mata Pelajaran
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Kelompok Mata Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran">Kelompok Mata Pelajaran</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <form action="tambah"  method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="kelompok" class="col-sm-4 control-label">Kelompok Mata Pelajaran</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="kelompok" placeholder="Kelompok C (Peminatan) IPA/IPS">
                            </div>
                        </div>
                        @if( auth()->user()->pegawai->unit_id == 4)
                        <div class="form-group row">
                            <label for="kelompok" class="col-sm-4 control-label">Jurusan</label>
                            <div class="col-sm-6">
                                <select name="jurusan" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">Tidak Ada</option>
                                @foreach( $jurusans as $jurusan )
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->major_name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
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
=======
@extends('template.main.master')

@section('title')
Tambah Kelompok Mata Pelajaran
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Kelompok Mata Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran">Kelompok Mata Pelajaran</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <form action="tambah"  method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="kelompok" class="col-sm-4 control-label">Kelompok Mata Pelajaran</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="kelompok" placeholder="Kelompok C (Peminatan) IPA/IPS">
                            </div>
                        </div>
                        @if( auth()->user()->pegawai->unit_id == 4)
                        <div class="form-group row">
                            <label for="kelompok" class="col-sm-4 control-label">Jurusan</label>
                            <div class="col-sm-6">
                                <select name="jurusan" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">Tidak Ada</option>
                                @foreach( $jurusans as $jurusan )
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->major_name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
