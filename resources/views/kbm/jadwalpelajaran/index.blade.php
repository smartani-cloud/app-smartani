@extends('template.main.master')

@section('title')
Jadwal Pelajaran
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Jadwal Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Jadwal Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                    <form action="/kependidikan/kbm/pelajaran/jadwal-pelajaran" method="POST">
                    @csrf
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Kelas</label>
                            <div class="col-sm-3">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Kelas ==</option>
                                    @foreach($kelases as $kelas)
                                    <option value="{{ $kelas->id }}">{{$kelas->level->level}} {{$kelas->namakelases->class_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Hari</label>
                            <div class="col-sm-3">
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
                            <button type="submit" class="btn btn-brand-green-dark">Cari</button>
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
@endsection