@extends('template.main.master')

@section('title')
Daftar Kelas
@endsection


@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Daftar Kelas</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Kelas</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            @if(Session::has('sukses'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ Session::get('sukses') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Kelas</h6>
                @if( in_array((auth()->user()->role_id), array(1,2)))
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/kependidikan/kbm/kelas/daftar-kelas/tambah">Tambah <i class="fas fa-plus"></i></a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Kelas</th>
                                <th>Nama Kelas</th>
                                <th>Wali Kelas</th>
                                @if( in_array((auth()->user()->role_id), array(1,2)))
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $kelases as $index => $kelas)
                            <tr>
                                <td>{{$index+1}}</td>
                                <td>{{ $kelas->level->level }} {{$kelas->major_id==null?'':$kelas->jurusan->major_name}}</td>
                                <td>{{ $kelas->namakelases->class_name }}</td>
                                <td>{{ $kelas->walikelas ? $kelas->walikelas->name : '-' }}</td>
                                @if( in_array((auth()->user()->role_id), array(1,2)))
                                <td><a href="/kependidikan/kbm/kelas/daftar-kelas/ubah/{{ $kelas->id }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;
                                
                                @if($kelas->siswa()->count()>0)
                                <button href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{ $kelas->id }}" disabled><i class="fas fa-trash"></i></button>
                                @else
                                <button href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{ $kelas->id }}"><i class="fas fa-trash"></i></button>
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
@foreach(  $kelases as $kelas)
<!-- Modal Hapus -->
<div class="modal fade" id="HapusModal{{ $kelas->id }}" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/kependidikan/kbm/kelas/daftar-kelas/hapus/{{ $kelas->id }}"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Peringatan Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Menghapus 
                {{ $kelas->level->level }}
                {{ $kelas->namakelases->class_name }}?
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