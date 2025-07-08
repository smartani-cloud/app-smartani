@extends('template.main.master')

@section('title')
Arsip SKHB
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
    <h1 class="h3 mb-0 text-gray-800">Arsip SKHB</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Arsip SKHB</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                <input type="text" name="rombel_id" class="form-control" id="rombel" style="width:100%;" value="{{$kelas->level->level.' '.$kelas->namakelases->class_name}}" tabindex="-1" aria-hidden="true" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table align-items-center table-flush">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th width="50%">Nama Siswa</th>
                                    <th width="50%" class="text-center">SKHB&nbsp;<strong style="color:red;">*</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($siswas)
                                @foreach ($siswas as $key => $siswa)
                                <form action="/kependidikan/skhb/arsip/store" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="siswa" value="{{$siswa->id}}">
                                    <tr>
                                        <td>{{$siswa->identitas->student_name}}</td>
                                        <td>
                                            @if( $siswa->arsip()->Skhb()->count() > 0 )

                                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/arsip/SKHB_{{$siswa->student_nis}}_{{$siswa->identitas->student_name}}.pdf">Unduh <i class="fas fa-download"></i></a>
                                            @else
                                            <div class="form-group row">
                                                <input name="file" type="file" class="form-control col-sm-8" accept="application/pdf">
                                                <button type="submit" class="m-0 float-right btn btn-brand-green-dark">Simpan</button>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                </form>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                        <!-- <p><strong style="color:red;">*)</strong> Hanya untuk rapor PAS</p> -->
                        <div class="text-center mt-4">
                        </div>
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