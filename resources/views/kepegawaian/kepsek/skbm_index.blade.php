<<<<<<< HEAD
@extends('template.main.master')

@section('title')
SKBM
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">SKBM</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">SKBM</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Surat Keterangan Belajar Mengajar</h6>
            </div>
            <div class="card-body p-3">
              @if(Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(Session::has('danger'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ Session::get('danger') }}
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
                      <th>Tahun Pelajaran</th>
                      <th>Unit</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $no = 1;
                    $thisYearSkbm = $skbm->where('academic_year_id',$aktif->id)->all();
                    @endphp
                    @if(count($thisYearSkbm) == 0)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $aktif->academic_year }}</td>
                      <td>{{ Auth::user()->pegawai->unit->name }}</td>
                      <td>
                        <span class="badge badge-brand-green font-weight-normal" data-toggle="tooltip" data-original-title="Baru">Baru</span>
                      </td>
                      <td>
                        <a href="{{ route('skbm.tampil', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => Auth::user()->pegawai->unit->name]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                      </td>
                    </tr>
                    @endif
                    @if(count($skbm) > 0)
                    @foreach($skbm as $s)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $s->tahunAjaran->academic_year }}</td>
                      <td>{{ $s->unit->name }}</td>
                      <td>
                        @if($s->status && $s->status->status == 'aktif')
                        <span class="badge badge-primary font-weight-normal" data-toggle="tooltip" data-original-title="Aktif">Aktif</span>
                        @elseif($s->status && $s->status->status == 'arsip')
                        <span class="badge badge-dark font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('d M Y', strtotime($s->updated_at)) }}">Arsip</span>
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('skbm.tampil', ['tahunpelajaran' => $s->tahunAjaran->academicYearLink, 'unit' => $s->unit->name]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('skbm.ekspor', ['tahunpelajaran' => $s->tahunAjaran->academicYearLink, 'unit' => $s->unit->name]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-file-export"></i></a>
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
=======
@extends('template.main.master')

@section('title')
SKBM
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">SKBM</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">SKBM</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Surat Keterangan Belajar Mengajar</h6>
            </div>
            <div class="card-body p-3">
              @if(Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(Session::has('danger'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ Session::get('danger') }}
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
                      <th>Tahun Pelajaran</th>
                      <th>Unit</th>
                      <th>Status</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $no = 1;
                    $thisYearSkbm = $skbm->where('academic_year_id',$aktif->id)->all();
                    @endphp
                    @if(count($thisYearSkbm) == 0)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $aktif->academic_year }}</td>
                      <td>{{ Auth::user()->pegawai->unit->name }}</td>
                      <td>
                        <span class="badge badge-brand-green font-weight-normal" data-toggle="tooltip" data-original-title="Baru">Baru</span>
                      </td>
                      <td>
                        <a href="{{ route('skbm.tampil', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => Auth::user()->pegawai->unit->name]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                      </td>
                    </tr>
                    @endif
                    @if(count($skbm) > 0)
                    @foreach($skbm as $s)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $s->tahunAjaran->academic_year }}</td>
                      <td>{{ $s->unit->name }}</td>
                      <td>
                        @if($s->status && $s->status->status == 'aktif')
                        <span class="badge badge-primary font-weight-normal" data-toggle="tooltip" data-original-title="Aktif">Aktif</span>
                        @elseif($s->status && $s->status->status == 'arsip')
                        <span class="badge badge-dark font-weight-normal" data-toggle="tooltip" data-original-title="{{ date('d M Y', strtotime($s->updated_at)) }}">Arsip</span>
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        <a href="{{ route('skbm.tampil', ['tahunpelajaran' => $s->tahunAjaran->academicYearLink, 'unit' => $s->unit->name]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('skbm.ekspor', ['tahunpelajaran' => $s->tahunAjaran->academicYearLink, 'unit' => $s->unit->name]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-file-export"></i></a>
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection