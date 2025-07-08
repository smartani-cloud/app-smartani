@extends('template.main.master')

@section('title')
Cetak Sertifikat IKLaS
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('headmeta')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
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
@elseif ($message = Session::get('kurang'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Sukses!</strong> Data berhasil disimpan!
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Peringatan!</strong> {!!$message!!}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cetak Sertifikat IKLaS</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Seritikat</a></li>
        <li class="breadcrumb-item active" aria-current="page">IKLaS</li>
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
                                <input type="text" class="form-control" value="{{$semesteraktif->semester_id . ' (' .$semesteraktif->semester.')'}}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                <input type="text" name="rombel_id" class="form-control" id="rombel" style="width:100%;" value="{{$kelas->level->level.' '.$kelas->namakelases->class_name}}" tabindex="-1" aria-hidden="true" disabled>
                            </div>
                        </div>
                        <hr>
                        @if(collect($sertif)->filter(function($value, $key){return $value != null;})->count() > 0 && $semesteraktif->semester == 'Genap')
                        @if ($siswa)
                        @foreach ($siswa as $key => $siswas)
                        @if($sertif[$key] && $sertif[$key]->certificate_date != NULL)
                        @php
                        $tanggalsertif = $sertif[$key]->certificate_date;
                        @endphp
                        @else
                        @php
                        $tanggalsertif = NULL;
                        @endphp
                        @endif
                        @endforeach
                        @endif
                        <form action="{{route('sertifiklas.set_tanggal')}}" method="POST">
                            @csrf
                            <input type="hidden" name="class_id" value="{{$kelas->id}}">
                            <div class="form-group row">
                                <label for="rombel" class="col-sm-12 control-label font-weight-bold">Setting Tanggal Sertifikat</label>
                                <div class="col-sm-6">
                                    <input type="date" name="tanggal_sertif" <?php if (isset($tanggalsertif) && $tanggalsertif != NULL) echo 'value="' . $tanggalsertif . '"'; ?> class="form-control" required>
                                </div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-success h-100">Simpan</button>
                                </div>
                            </div>
                        </form>
                        @endif
                    </div>
                    <div class="col-md-12">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Cetak Sertifikat IKLaS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($siswa)
                                @foreach ($siswa as $key => $siswas)
                                <tr>
                                    <td>{{$siswas->identitas->student_name}}</td>
                                    <td class="text-center">
                                        @if($sertif[$key] && $semesteraktif->semester == 'Genap')
                                        <form action="{{route('sertifiklas.print')}}" target="_blank" method="POST">
                                            @csrf
                                            <input type="hidden" name="wali" value="1">
                                            <input type="hidden" name="id" value="{{$siswas->id}}">
                                            <input type="hidden" name="semester" value="{{$semesteraktif->id}}">
                                            <input type="hidden" name="level_id" value="{{$siswas->kelas->level_id}}">
                                            <input type="hidden" name="major_id" value="{{$siswas->kelas->major_id}}">
                                            <button type="submit" class="btn btn-brand-green btn-sm"><i class="fa fas fa-print"></i> Cetak Sertifikat</button>&nbsp;
                                        </form>
                                        @elseif($sertif[$key] && $semesteraktif->semester != 'Genap')
                                        Menunggu Semester Genap
                                        @else
                                        Nilai Belum Divalidasi
                                        @endif
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