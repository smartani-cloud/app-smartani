@extends('template.main.master')

@section('title')
Ubah Daftar Kelas
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Ubah Daftar Kelas</h1>
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
                <form action="/kependidikan/kbm/kelas/daftar-kelas/ubah/{{ $kelas->id }}"  method="POST">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas</label>
                            <div class="col-sm-6">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $levels as $level )
                                @if( $level->id == $kelas->level_id )
                                    <option value="{{ $level->id }}" selected>{{ $level->level }}</option>
                                @else
                                    <option value="{{ $level->id }}">{{ $level->level }}</option>
                                @endif
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
                                    <option value="{{ $jurusan->id }}" {{ $jurusan->id==$kelas->major_id?'selected':''}}>{{ $jurusan->major_name }}</option>
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
                                @if( $namakelas->id == $kelas->class_name_id )
                                    <option value="{{ $namakelas->id }}" selected>{{ $namakelas->class_name }}</option>
                                @else
                                    <option value="{{ $namakelas->id }}">{{ $namakelas->class_name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Wali Kelas</label>
                            <div class="col-sm-6">
                                <select name="wali_kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                @foreach( $gurus as $guru )
                                    @if( $guru->id == $kelas->teacher_id )
                                    <option value="{{ $guru->id }}" selected>{{ $guru->name }}</option>
                                    @else
                                    <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                    @endif
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
@endsection