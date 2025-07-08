@extends('template.main.master')

@section('title')
Nama Kelas
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nama Kelas</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nama Kelas</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Nama Kelas</h6>
                @if( in_array((auth()->user()->role_id), array(1,2)))
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/kependidikan/kbm/kelas/nama-kelas/tambah">Tambah <i class="fas fa-plus"></i></a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            @if( in_array((auth()->user()->role_id), array(1,2)))
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    @foreach( $lists as $indexKey => $list)
                        <tr>
                            <td>{{ $indexKey+1 }}</td>
                            <td>{{ $list->class_name }}</td>
                            @if( in_array((auth()->user()->role_id), array(1,2)))
                            <td>
                                <button href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal{{ $list->id }}"><i class="fas fa-pen"></i></button>&nbsp;
                                
                                @if($list->kelases()->count()>0)
                                <button href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{ $list->id }}" disabled><i class="fas fa-trash"></i></button>
                                @else
                                <button href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{ $list->id }}"><i class="fas fa-trash"></i></button>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>


@if( in_array((auth()->user()->role_id), array(1,2)))
@foreach( $lists as $indexKey => $list)
<!-- Modal Ubah -->
<div class="modal fade" id="UbahModal{{ $list->id }}" tabindex="-1" role="dialog" aria-labelledby="UbahModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="nama-kelas/ubah/{{ $list->id }}"  method="POST">
        @method('PUT')
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Ubah Nama Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="academic_year_start" class="col-sm-3 control-label">Nama Kelas</label>
                    <div class="col-sm-8">
                        <input  name="nama_kelas" class="form-control" value="{{ $list->class_name }}">
                    </div>
                </div>
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

        <form action="nama-kelas/hapus/{{ $list->id }}"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Peringatan Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Menghapus {{ $list->class_name }} dari nama kelas?
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
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>

@include('template.footjs.kbm.datatables')
@endsection