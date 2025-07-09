@extends('template.main.master')

@section('title')
Kelompok Mata Pelajaran
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelompok Mata Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kelompok Mata Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Kelompok Mata Pelajaran</h6>
                @if( in_array((auth()->user()->role_id), array(1,2)))
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="kelompok-mata-pelajaran/tambah">Tambah<i class="fas fa-plus-circle ml-2"></i></a>
                @endif
            </div>
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
                                <th style="width: 15px">#</th>
                                <th>Kelompok</th>
                                @if( in_array((auth()->user()->role_id), array(1,2)))
                                <th style="width: 120px">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach( $lists as $indexKey => $list)
                            <tr>
                                <td>{{ $indexKey+1 }}</td>
                                <td>{{ $list->group_subject_name }} {{ $list->major_id==null?'':$list->jurusan->major_name }}</td>
                                @if( in_array((auth()->user()->role_id), array(1,2)))
                                <td>
                                    <button href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal{{ $list->id }}"><i class="fas fa-pen"></i></button>
                                    @if($list->matapelajarans()->count() < 1)
                                    <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{ $list->id }}"><i class="fas fa-trash"></i></a>
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
        </div>
    </div>
</div>


@if( in_array((auth()->user()->role_id), array(1,2)))
@foreach( $lists as $indexKey => $list)
<!-- Modal Ubah -->
<div class="modal fade" id="UbahModal{{ $list->id }}" tabindex="-1" role="dialog" aria-labelledby="UbahModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="kelompok-mata-pelajaran/ubah/{{ $list->id }}"  method="POST">
        @method('PUT')
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Ubah Kelompok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="academic_year_start" class="col-sm-3 control-label">Nama Kelompok</label>
                    <div class="col-sm-8">
                        <input  name="kelompok" class="form-control" value="{{ $list->group_subject_name }}">
                    </div>
                </div>
                @if( auth()->user()->pegawai->unit_id == 4)
                <div class="form-group row">
                    <label for="kelompok" class="col-sm-3 control-label">Jurusan</label>
                    <div class="col-sm-8">
                        <select name="jurusan" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                        @if($list->major_id == null)
                            <option value="" selected>Tidak Ada</option>
                        @else
                            <option value="">Tidak Ada</option>
                        @endif
                        @foreach( $jurusans as $jurusan )
                        @if($jurusan->id == $list->major_id)
                            <option value="{{ $jurusan->id }}" selected>{{ $jurusan->major_name }}</option>
                        @else
                            <option value="{{ $jurusan->id }}">{{ $jurusan->major_name }}</option>
                        @endif
                        @endforeach
                        </select>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning">ubah</button>
            </div>
        </form>
    </div>
  </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="HapusModal{{ $list->id }}" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="kelompok-mata-pelajaran/hapus/{{ $list->id }}"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Peringatan Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Menghapus {{ $list->group_subject_name }} dari kelompok mata pelajaran?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus</button>
            </div>
        </form>
    </div>
  </div>
</div>
@endforeach
@endif

<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kbm.datatables')
@endsection