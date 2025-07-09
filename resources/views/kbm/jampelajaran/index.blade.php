<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Jam Pelajaran
@endsection


@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Jam Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Jam Pelajaran</li>
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
                        <form action="/kependidikan/kbm/pelajaran/waktu-pelajaran" method="POST">
                        @csrf
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Hari</label>
                                <div class="col-sm-4">
                                    <select name="hari" class="select2 form-control select2-hidden-accessible auto_width" id="hari" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Hari ==</option>
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jum'at">Jum'at</option>
                                        <option value="Sabtu">Sabtu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Kelas</label>
                                <div class="col-sm-4">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="hari" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Kelas ==</option>
                                        @foreach($levels as $level)
                                        <option value="{{$level->id}}">{{$level->level}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-brand-green-dark">Cari</button>
                            </div>
                            <div class="text-center mt-4">
                            </div>
                        </form>
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
@include('template.footjs.kbm.datatables')
=======
@extends('template.main.master')

@section('title')
Jam Pelajaran
@endsection


@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Jam Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Jam Pelajaran</li>
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
                        <form action="/kependidikan/kbm/pelajaran/waktu-pelajaran" method="POST">
                        @csrf
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Hari</label>
                                <div class="col-sm-4">
                                    <select name="hari" class="select2 form-control select2-hidden-accessible auto_width" id="hari" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Hari ==</option>
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jum'at">Jum'at</option>
                                        <option value="Sabtu">Sabtu</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Kelas</label>
                                <div class="col-sm-4">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="hari" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Kelas ==</option>
                                        @foreach($levels as $level)
                                        <option value="{{$level->id}}">{{$level->level}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-brand-green-dark">Cari</button>
                            </div>
                            <div class="text-center mt-4">
                            </div>
                        </form>
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
@include('template.footjs.kbm.datatables')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection