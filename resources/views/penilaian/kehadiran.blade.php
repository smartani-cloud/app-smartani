@extends('template.main.master')

@section('title')
Kehadiran
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
    <h1 class="h3 mb-0 text-gray-800">Kehadiran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kehadiran</li>
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
                        <form action="{{ route('kehadiran.simpan') }}" method="POST">
                            @csrf
                            <table class="table align-items-center table-flush">
                                <thead class="bg-brand-green text-white">
                                    <tr>
                                        <th width="40%">Nama Siswa</th>
                                        <th width="15%" class="text-center">Hari Efektif&nbsp;<strong style="color:red;">*</strong></th>
                                        <th width="15%" class="text-center">Sakit</th>
                                        <th width="15%" class="text-center">Izin</th>
                                        <th width="15%" class="text-center">Alpha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($siswa)
                                    @foreach ($siswa as $key => $siswa)
                                    <input type="hidden" name="siswa[]" value="{{$siswa->id}}">
                                    <tr>
                                        <td>{{$siswa->identitas->student_name}}</td>
                                        @if ($kehadiran != FALSE)
                                        <td>
                                            <input name="hariefektif[]" type="number" value="{{$kehadiran[$key] && $kehadiran[$key]->effective_day ? $kehadiran[$key]->effective_day : ''}}" class="form-control">
                                        </td>
                                        <td>
                                            <input name="sakit[]" type="number" value="{{$kehadiran[$key] ? $kehadiran[$key]->sick : ''}}" class="form-control" required>
                                        </td>
                                        <td>
                                            <input name="izin[]" type="number" value="{{$kehadiran[$key] ? $kehadiran[$key]->leave : ''}}" class="form-control" required>
                                        </td>
                                        <td>
                                            <input name="alpha[]" type="number" value="{{$kehadiran[$key] ? $kehadiran[$key]->absent : ''}}" class="form-control" required>
                                        </td>
                                        @else
                                        <td>
                                            <input name="hariefektif[]" type="number" class="form-control">
                                        </td>
                                        <td>
                                            <input name="sakit[]" type="number" class="form-control" required>
                                        </td>
                                        <td>
                                            <input name="izin[]" type="number" class="form-control" required>
                                        </td>
                                        <td>
                                            <input name="alpha[]" type="number" class="form-control" required>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center">Data Kosong</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                            <p><strong style="color:red;">*)</strong> Hanya untuk rapor PAS</p>
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