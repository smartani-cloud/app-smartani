@extends('template.main.master')

@section('title')
Pegawai
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
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
                <a class="nav-link active" href="{{ route('pegawai.index', ['status' => 'aktif']) }}">Aktif</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-purple" href="{{ route('pegawai.index', ['status' => 'nonaktif']) }}">Nonaktif</a>
              </li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <form action="{{ route('pegawai.index') }}" id="filter-form" method="get">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-purple">Filter</h6>
            </div>
            <div class="card-body p-3">
              <div class="row">
                <div class="col-lg-10 col-md-12">
                  <div class="form-group">
                    <div class="row">
                      <div class="col-lg-2 col-md-3 col-12">
                        <label for="position" class="form-control-label">Jabatan</label>
                      </div>
                      <div class="col-lg-10 col-md-9 col-12">
                        <select class="select2-multiple form-control" name="jabatan[]" multiple="multiple" id="position">
                          @foreach($jabatan as $j)
                          <option value="{{ $j->id }}" {{ $filterJabatan && count($filterJabatan) > 0 ? ($filterJabatan->contains($j->id) ? 'selected' : '') : '' }}>{{ $j->name }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-10 col-md-12">
                  <div class="row">
                    <div class="col-lg-10 offset-lg-2 col-md-12">
                      <div class="text-left">
                        <button class="btn btn-sm btn-brand-purple-dark" type="submit">Terapkan</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-purple">Pegawai Aktif</h6>
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="{{ route('pegawai.ekspor') }}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
            </div>
            <div class="card-body p-3">
              @if(count($pegawai) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Nama</th>
                      <th>{{ $pegawai->statusPegawai->kategori->name == 'Mitra' ? 'NIMY' : 'NIPY' }}</th>
                      <th>Tempat Lahir</th>
                      <th>Tanggal Lahir</th>
                      <th>Unit</th>
                      <th>Jabatan</th>
                      <th>Masa Kerja</th>
                      <th>Status Pegawai</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($pegawai as $p)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $p->name }}
                        </a>
                        @if($p->statusBaru && $p->statusBaru->status == 'aktif')
                        <span class="badge badge-primary font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('d M Y', strtotime($p->join_date)) }}">Baru</span>
                        @endif
                        @if($p->statusPhk && $p->statusPhk->status == 'aktif')
                        <span class="badge badge-warning font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('d M Y', strtotime($p->disjoin_date)) }}">PHK</span>
                        @endif
                      </td>
                      <td>{{ $p->nip }}</td>
                      <td>{{ $p->birth_place }}</td>
                      <td>{{ date('Y-m-d',strtotime($p->birth_date)) }}</td>
                      <td>{{ $p->units()->count() > 0 ? implode(', ',$p->units()->with('unit')->get()->pluck('unit')->sortBy('id')->pluck('show_name')->toArray()) : '-' }}</td>
                      <td>{{ $p->units()->count() > 0 ? implode(', ',$p->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->sortBy('id')->pluck('name')->unique()->toArray()) : '' }}</td>
                      <td>{{ $p->yearsOfService }}</td>
                      <td>{{ $p->statusPegawai->show_name }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $p->id]) }}" class="btn btn-sm btn-brand-purple-dark" target="_blank"><i class="fas fa-eye"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3 >Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data pegawai aktif yang ditemukan</h6>
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

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.select2-multiple')
@endsection