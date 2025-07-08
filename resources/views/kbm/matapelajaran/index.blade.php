@extends('template.main.master')

@section('title')
Mata Pelajaran
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Mata Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Mata Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Mata Pelajaran</h6>
                @if( in_array((auth()->user()->role_id), array(1,2)))
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/kependidikan/kbm/pelajaran/mata-pelajaran/tambah">Tambah<i class="fas fa-plus-circle ml-2"></i></a>
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
                    <table id="dataTable" class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelompok Mata Pelajaran</th>
                                @if($unit !==1)
                                <th>KKM</th>
                                @endif
                                @if( in_array((auth()->user()->role_id), array(1,2,5)))
                                <th style="width: 120px">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                        @foreach( $mapellist as $index => $mapel)
                            <tr>
                                <td>{{ $index+1 }}</td>

                                @if( in_array((auth()->user()->role_id), array(5)))
                                <td>{{ $mapel->mataPelajaran->subject_name }} @if($unit !==1)({{ $mapel->mataPelajaran->subject_number }})@endif {{ $mapel->is_mulok==1?'(Mulok)':'' }}</td>
                                <td>{{ $mapel->mataPelajaran->kmps->group_subject_name }}</td>
                                <td>{{ $kkm[$index] }}</td>
                                @else
                                <td>{{ $mapel->subject_name }} @if($unit !==1)({{ $mapel->subject_number }})@endif {{ $mapel->is_mulok==1?'(Mulok)':'' }}</td>
                                <td>{{ $mapel->kmps->group_subject_name }}</td>
                                @if($unit !==1)
                                    <td>{{ $kkm[$index] }}</td>
                                @endif
                                @endif

                                @if( in_array((auth()->user()->role_id), array(1,2,5)))
                                    <td>
                                    @if( in_array((auth()->user()->role_id), array(5)))
                                        <a href="mata-pelajaran/ubah/{{ $mapel->mataPelajaran->id }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                                    @endif
                                    @if( in_array((auth()->user()->role_id), array(1,2)))
                                        <a href="mata-pelajaran/ubah/{{ $mapel->id }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                                        @if($used && $used[$mapel->id] < 1)
                                        <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{ $mapel->id }}"><i class="fas fa-trash"></i></a>
                                        @else
                                        <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                                        @endif
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
@foreach( $mapellist as $index => $mapel)
<!-- Modal Hapus -->
<div class="modal fade" id="HapusModal{{ $mapel->id }}" tabindex="-1" role="dialog" aria-labelledby="HapusModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="/kependidikan/kbm/pelajaran/mata-pelajaran/hapus/{{ $mapel->id }}"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Peringatan Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Menghapus {{ $mapel->subject_name }} ({{ $mapel->subject_number }}) dari kelompok mata pelajaran?
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