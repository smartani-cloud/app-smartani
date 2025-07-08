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

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="/kependidikan/kbm/pelajaran/waktu-pelajaran" method="POST">
                        @csrf
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Hari</label>
                                <div class="col-sm-4">
                                    <select name="hari" class="select2 form-control select2-hidden-accessible auto_width" id="hari" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Hari ==</option>
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
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Kelas</label>
                                <div class="col-sm-4">
                                    <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="hari" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Kelas ==</option>
                                        @foreach($levels as $level)
                                            @if($tingkat==$level->id)
                                                <option value="{{$level->id}}" selected>{{$level->level}}</option> 
                                            @else
                                                <option value="{{$level->id}}">{{$level->level}}</option> 
                                            @endif
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green-dark">Jam Pelajaran</h6>
                @if(in_array((auth()->user()->role_id), array(1,2,3)))
                <button type="button" class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#TambahModal">Tambah<i class="fas fa-plus-circle ml-2"></i></a>
                @endif
            </div>
            @if($jams && count($jams) > 0)
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
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Keterangan</th>
                                @if( in_array((auth()->user()->role_id), array(1,2)))
                                <th style="width: 120px">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach( $jams as $index => $jam )
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>{{Carbon\Carbon::parse($jam->hour_start)->format('H:i')}}</td>
                                <td>{{Carbon\Carbon::parse($jam->hour_end)->format('H:i')}}</td>
                                <td>{{$jam->description}}</td>
                                @if( in_array((auth()->user()->role_id), array(1,2)))
                                <td>
                                    <button href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal{{$jam->id}}"><i class="fas fa-pen"></i></button>
                                    @if($used && $used[$jam->id] < 1)
                                    <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{ $jam->id }}"><i class="fas fa-trash"></i></a>
                                    @else
                                    <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                                    @endif
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
                <h6 class="font-weight-light mb-3">Tidak ada data jam pelajaran yang ditemukan</h6>
            </div>
            @endif
        </div>
    </div>
</div>


@if( in_array((auth()->user()->role_id), array(1,2,3)))
@foreach( $jams as $index => $jam )
<!-- Modal Ubah -->
<div class="modal fade" id="UbahModal{{$jam->id}}" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/kependidikan/kbm/pelajaran/waktu-pelajaran/ubah/{{$jam->id}}"  method="POST">
        @method('PUT')
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Ubah Jam Pelajaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="academic_year_start" class="col-sm-6 control-label">Hari {{$hari}} Kelas {{$kelas->level}} Jam ke-</label>
                </div>
                <div class="form-group row">
                    <label for="Mulai" class="col-sm-4 control-label">Mulai</label>
                    <div class="col-sm-5">
                        <input type="time" name="mulai" class="form-control" value="{{$jam->hour_start}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Selesai" class="col-sm-4 control-label">Selesai</label>
                    <div class="col-sm-5">
                        <input type="time" name="selesai" class="form-control" value="{{$jam->hour_end}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Keterangan" class="col-sm-4 control-label">Keterangan</label>
                    <div class="col-sm-5">
                        <input type="text" name="Keterangan" class="form-control" value="{{$jam->description}}">
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
<div id="HapusModal{{$jam->id}}" class="modal fade">
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
                <form action="/kependidikan/kbm/pelajaran/waktu-pelajaran/hapus/{{$jam->id}}" " method="POST">
                    @csrf
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/kependidikan/kbm/pelajaran/waktu-pelajaran/tambah"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Tambah Jam Pelajaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="academic_year_start" class="col-sm-6 control-label">Hari {{$hari}} Kelas {{$kelas->level}}</label>
                </div>
                <div class="form-group row">
                    <label for="Mulai" class="col-sm-4 control-label">Mulai</label>
                    <div class="col-sm-5">
                        <input type="time" name="mulai" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Selesai" class="col-sm-4 control-label">Selesai</label>
                    <div class="col-sm-5">
                        <input type="time" name="selesai" class="form-control">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="Keterangan" class="col-sm-4 control-label">Keterangan</label>
                    <div class="col-sm-5">
                        <input type="text" name="Keterangan" class="form-control" value="-">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <input type="text" hidden value="{{$hari}}" name="hari">
            <input type="text" hidden value="{{$tingkat}}" name="level">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-brand-green-dark">Tambah</button>
            </div>
        </form>
    </div>
  </div>
</div>
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kbm.datatables')
@endsection