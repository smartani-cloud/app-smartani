@extends('template.main.master')

@section('title')
PPB
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">PPB</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppb.index')}}">PPB</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link,'tahun' => $tahun->academicYearLink])}}">{{ $tahun->academic_year }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppb.show', ['jenis' => $jenisAktif->link,'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber])}}">{{ $bbkAktif->firstNumber }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Lihat</li>
  </ol>
</div>

@if($jenisAktif)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor PPB</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $bbkAktif->number ? $bbkAktif->number : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor PPA</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $ppaAktif->number ? $ppaAktif->number : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber]) }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-money-check text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Anggaran</div>
                        <h6 class="mb-0">{{ $ppaAktif->jenisAnggaranAnggaran->anggaran->name }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $ppaAktif && $ppaAktif->detail()->count() > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Jumlah</div>
                        <h6 id="summary" class="mb-0">
                            @if($ppaAktif && $ppaAktif->detail()->count() > 0)
                            {{ $ppaAktif->totalValueWithSeparator }}
                            @else
                            0
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
          <input type="hidden" name="validate" value="">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $ppaAktif->jenisAnggaranAnggaran->anggaran->name }}</h6>
            </div>
			@if($ppaAktif && ($ppaAktif->detail()->where('letris_acc_status_id',1)->count() >= $ppaAktif->detail()->count()))
			@if($ppaAktif->director_acc_status_id != 1 && $ppaAktif->letris_acc_status_id != 1)
			<div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
              <i class="fa fa-info-circle text-info mr-2"></i>Jangan lupa ekspor daftar PPA ini untuk kebutuhan arsip sebelum menekan tombol "Sepakati"
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
			@elseif($ppaAktif->director_acc_status_id != 1 && $ppaAktif->letris_acc_status_id == 1)
			<div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
              <i class="fa fa-info-circle text-info mr-2"></i>Menunggu pihak Auliya menyepakati PPA ini
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
			@elseif($ppaAktif->director_acc_status_id == 1 && $ppaAktif->letris_acc_status_id != 1)
			<div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
              <i class="fa fa-check-circle text-success mr-2"></i>Pihak Auliya sudah menyepakati PPA ini pada {{ date('d M Y H.i', strtotime($ppaAktif->director_acc_time))}}.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
			@elseif($ppaAktif->director_acc_status_id == 1 && $ppaAktif->letris_acc_status_id == 1)
			<div class="alert alert-success fade show mx-3" role="alert">
              <i class="fa fa-check-circle mr-2"></i>PPA ini telah disepakati Auliya dan Letris
            </div>
			@endif
			@endif
            @if($ppaAktif->detail()->count() > 0)
            @php
            $i = 1;
            @endphp
            <div class="table-responsive">
                <table id="ppaDetail" class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Akun Anggaran</th>
                            <th>Keterangan</th>
                            <th style="min-width: 200px">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ppaAktif->detail as $p)
                        <tr id="p-{{ $p->id }}">
                            <td>{{ $i++ }}</td>
                            <td class="detail-account">{{ $p->akun->codeName }}</td>
                            <td class="detail-note">{{ $p->note }}</td>
                            <td class="detail-value" style="min-width: 200px">{{ $p->valueWithSeparator }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data pengajuan yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>
@endif
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@endsection