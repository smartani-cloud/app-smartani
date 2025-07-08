@extends('template.main.master')

@section('title')
Kenaikan Kelas
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
@if ($message = Session::get('error'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Gagal!</strong> {{$message}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@elseif ($message = Session::get('sukses'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Sukses!</strong> {{$message}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kenaikan Kelas</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kenaikan Kelas</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Tahun Pelajaran</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{$semester->semester_id . ' (' .$semester->semester.')'}}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                <input type="text" name="rombel_id" class="form-control" id="rombel" style="width:100%;" value="{{$kelas->level->level.' '.$kelas->namakelases->class_name}}" tabindex="-1" aria-hidden="true" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form action="{{route('catatan.simpan')}}" method="POST">
                            @csrf
                            <input type="hidden" name="naik" value="1" />
                            <table class="table align-items-center table-flush">
                                <thead class="bg-brand-green text-white">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th class="text-center">Status Kenaikan Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($siswa)
                                    @foreach ($siswa as $key => $siswas)
                                    <input type="hidden" name="siswa_id[]" value="{{$siswas->id}}" />
                                    <tr>
                                        <td>{{$siswas->identitas->student_name}}</td>
                                        <td>
                                            <select class="form-control" name="kenaikan[]" required>
                                                <option value="">== Pilih ==</option>
                                                @if($siswas->level_id == 2 || $siswas->level_id == 8 || $siswas->level_id == 11 || $siswas->level_id == 14)
                                                <option value="lulus" <?php if ($pas && isset($pas[$key]) && $pas[$key]->conclusion == "lulus") echo "selected"; ?>>Lulus</option>
                                                @else
                                                <option value="naik" <?php if ($pas && isset($pas[$key]) && $pas[$key]->conclusion == "naik") echo "selected"; ?>>Naik Kelas</option>
                                                @endif
                                                <option value="tinggal" <?php if ($pas && isset($pas[$key]) && $pas[$key]->conclusion == "tinggal") echo "selected"; ?>>Tinggal Kelas</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="2" class="text-center">Data Kosong</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            @if($siswa)
                            @if($countrapor > 0)
                            @if($validasi > 0)
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                            </div>
                            @endif
                            @else
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                            </div>
                            @endif
                            @endif
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
@endsection