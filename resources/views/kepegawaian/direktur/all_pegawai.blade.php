@extends('template.main.master')

@section('title')
Pegawai
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.direktur')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Pegawai</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pegawai</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              <li class="nav-item">
                <a class="nav-link active" href="#">Aktif</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="#">Nonaktif</a>
              </li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Pegawai Aktif</h6>
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="#">Tambah <i class="fas fa-plus-circle ml-1"></i></a>
            </div>
            <div class="card-body p-3">
              @if(Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(count($pegawai) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>NIP</th>
                      <th>TTL</th>
                      <th>Unit</th>
                      <th>Masa Kerja</th>
                      <th style="width: 200px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($pegawai as $p)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('calon.detail', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                          <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="rounded-circle mr-1" width="30">{{ $p->name }}
                        </a>
                        @if($p->statusBaru->status == 'aktif')
                        <span class="badge badge-primary font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('d M Y', strtotime($p->join_date)) }}">Baru</span>
                        @endif
                      </td>
                      <td>{{ $p->nip }}</td>
                      <td>{{ $p->birth_place.', '.date('d-m-Y',strtotime($p->birth_date)) }}</td>
                      <td>{{ $p->unit->name }}</td>
                      <td>{{ $p->yearsOfService }}</td>
                      <td>
                        <a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#validate-confirm" onclick="validateModal('{{ $p->name }}', '{{ $p->statusPegawai->status }}', '{{ $p->unit->name }}', '{{ $p->statusPegawai->status }}', '{{ route('calon.validasi', ['id' => $p->id]) }}')"><i class="fas fa-check"></i></a>
                        <a href="{{ route('calon.ubah', ['id' => $p->id]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                        <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Calon Pegawai', '{{ $p->name }}', '{{ route('calon.hapus', ['id' => $p->id]) }}')"><i class="fas fa-trash"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3 >Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data calon pegawai yang ditemukan</h6>
              </div>
              @endif
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
@endsection