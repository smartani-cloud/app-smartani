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
                                    @foreach($kelases as $kls)
                                    @if($kls->id == $kelas )
                                        <option value="{{ $kls->id }}" selected>{{$kls->level->level}} {{$kls->namakelases->class_name}}</option>
                                    @else
                                        <option value="{{ $kls->id }}">{{$kls->level->level}} {{$kls->namakelases->class_name}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Hari</label>
                            <div class="col-sm-3">
                                <select name="hari" class="select2 form-control select2-hidden-accessible auto_width" id="hari" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if( $hari=='Senin')
                                        <option value="Senin" selected>Senin</option>
                                        @else
                                        <option value="Senin">Senin</option>
                                        @endif
                                        @if( $hari=='Selasa')
                                        <option value="Selasa" selected>Selasa</option>
                                        @else
                                        <option value="Selasa">Selasa</option>
                                        @endif
                                        @if( $hari=='Rabu')
                                        <option value="Rabu" selected>Rabu</option>
                                        @else
                                        <option value="Rabu">Rabu</option>
                                        @endif
                                        @if( $hari=='Kamis')
                                        <option value="Kamis" selected>Kamis</option>
                                        @else
                                        <option value="Kamis">Kamis</option>
                                        @endif
                                        @if( $hari=="Jum'at")
                                        <option value="Jum'at" selected>Jum'at</option>
                                        @else
                                        <option value="Jum'at">Jum'at</option>
                                        @endif
                                        @if( $hari=="Sabtu")
                                        <option value="Sabtu" selected>Sabtu</option>
                                        @else
                                        <option value="Sabtu">Sabtu</option>
                                        @endif
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
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green-dark">Jadwal Pelajaran</h6>
                <div class="m-0 float-right">
                    @if(in_array((auth()->user()->role_id), array(1,2,3)))
                    <a href="javascript:void(0)" class="btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#TambahModal">Tambah<i class="fas fa-plus-circle ml-2"></i></a>
                    @endif
                    @if( in_array((auth()->user()->role_id), array(1,2)))
                    <a href="/kependidikan/kbm/pelajaran/jadwal-pelajaran/unduh" class="btn btn-success btn-sm">Ekspor Semua<i class="fas fa-file-export ml-2"></i></a>
                    @endif
                </div>
            </div>
            @if($jadwals && count($jadwals) > 0)
            <div class="card-body">
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 60px">Jam Ke</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Kegiatan/Mata Pelajaran</th>
                                <th>Guru</th>
                                @if( in_array((auth()->user()->role_id), array(1,2,3)))
                                <th style="width: 120px">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $jadwals as $index => $jadwal )
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>{{ $jadwal->jam ? Carbon\Carbon::parse($jadwal->jam->hour_start)->format('H:i') : '-' }}</td>
                                <td>{{ $jadwal->jam ? Carbon\Carbon::parse($jadwal->jam->hour_end)->format('H:i') : '-' }}</td>
                                @if((is_null($jadwal->subject_id)))
                                <td>{{$jadwal->jam->description}}</td>
                                @else
                                <td>{{$jadwal->mapel->subject_name}}</td>
                                @endif
                                @if((is_null($jadwal->teacher_id)))
                                <td>-</td>
                                @else
                                <td>{{$jadwal->guru->name}}</td>
                                @endif
                                @if( in_array((auth()->user()->role_id), array(1,2,3)))
                                <td>
                                    <button href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal{{$jadwal->id}}"><i class="fas fa-pen"></i></button>
                                    <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{ $jadwal->id }}"><i class="fas fa-trash"></i></a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <strong>Sukses!</strong> {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            @if(Session::has('danger'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <strong>Gagal!</strong> {{ Session::get('danger') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data jadwal pelajaran yang ditemukan</h6>
            </div>
            @endif
        </div>
    </div>
</div>

@if( in_array((auth()->user()->role_id), array(1,2,3)))
<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <form action="/kependidikan/kbm/pelajaran/jadwal-pelajaran/{{$kelas}}/{{$hari}}/tambah"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Tambah Jadwal Pelajaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="academic_year_start" class="col-sm-9 control-label">Hari {{$hari}} Kelas {{ $kelasnya->level->level }} {{ $kelasnya->namakelases->class_name }}</label>
                </div>
                <div class="form-group row">
                    <label for="Mulai" class="col-sm-2 control-label">Jam</label>
                    <div class="col-sm-10">
                        <select name="jam" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                        @foreach($jams as $index => $jam)
                            <option value="{{ $jam->id }}">{{ Carbon\Carbon::parse($jam->hour_start)->format('H:i') }}-{{ Carbon\Carbon::parse($jam->hour_end)->format('H:i') }} {{ $jam->description }}</option>
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Selesai" class="col-sm-2 control-label">Pelajaran</label>
                    <div class="col-sm-10">
                        <select name="mapel" class="select2 form-control select2-hidden-accessible auto_width" id="mapel_dipilih" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value=""></option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->id }}">{{$mapel->subject_name}} ({{$mapel->subject_number}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Keterangan" class="col-sm-2 control-label">Guru</label>
                    <div class="col-sm-10">
                        <select name="guru" class="select2 form-control select2-hidden-accessible auto_width" id="guru" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="" id="guru"></option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" id="guru">{{$guru->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-brand-green-dark">Tambah</button>
            </div>
        </form>
    </div>
  </div>
</div>
@endif


@if( in_array((auth()->user()->role_id), array(1,2,3)))
@foreach( $jadwals as $jadwal )
<!-- Modal Ubah -->
<div class="modal fade" id="UbahModal{{$jadwal->id}}" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <form action="/kependidikan/kbm/pelajaran/jadwal-pelajaran/{{$kelas}}/{{$hari}}/ubah/{{$jadwal->id}}"  method="POST">
        @method('PUT')
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Ubah Jadwal Pelajaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="academic_year_start" class="col-sm-9 control-label">Hari {{$hari}} Kelas {{ $kelasnya->level->level }} {{ $kelasnya->namakelases->class_name }}</label>
                </div>
                <div class="form-group row">
                    <label for="Mulai" class="col-sm-2 control-label">Jam</label>
                    <div class="col-sm-10">
                        <select name="jam" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                        @foreach($jams as $index => $jam)
                        @if($jam->id == $jadwal->schedule_id)
                            <option value="{{ $jam->id }}" selected>{{ Carbon\Carbon::parse($jam->hour_start)->format('H:i') }}-{{ Carbon\Carbon::parse($jam->hour_end)->format('H:i') }} {{ $jam->description }}</option>
                        @else
                            <option value="{{ $jam->id }}">{{ Carbon\Carbon::parse($jam->hour_start)->format('H:i') }}-{{ Carbon\Carbon::parse($jam->hour_end)->format('H:i') }} {{ $jam->description }}</option>
                        @endif
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Selesai" class="col-sm-2 control-label">Pelajaran</label>
                    <div class="col-sm-10">
                        <select name="mapel" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value=""></option>
                            @foreach($mapels as $mapel)
                            @if( $mapel->id == $jadwal->subject_id )
                                <option value="{{ $mapel->id }}" selected>{{$mapel->subject_name}} ({{$mapel->subject_number}})</option>
                            @else
                                <option value="{{ $mapel->id }}">{{$mapel->subject_name}} ({{$mapel->subject_number}})</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Keterangan" class="col-sm-2 control-label">Guru</label>
                    <div class="col-sm-10">
                        <select name="guru" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value=""></option>
                            @foreach($gurus as $guru)
                            @if( $guru->id == $jadwal->teacher_id )
                                <option value="{{ $guru->id }}" selected>{{$guru->name}}</option>
                            @else
                                <option value="{{ $guru->id }}">{{$guru->name}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning">Ubah</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!-- Modal Hapus -->
<div id="HapusModal{{$jadwal->id}}" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xE5CD;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan menghapus data yang dipilih?.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="/kependidikan/kbm/pelajaran/jadwal-pelajaran/{{$kelas}}/{{$hari}}/hapus/{{$jadwal->id}}" method="POST">
                    @csrf
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('js/jadwal.js')}}"></script>
@include('template.footjs.kbm.datatables')
@endsection