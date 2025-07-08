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
    <li class="breadcrumb-item"><a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link,'tahun' => $isKso ? $tahun->academicYearLink : $tahun])}}">{{ $isKso ? $tahun->academic_year : $tahun }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $bbkAktif->firstNumber }}</li>
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
                  <label class="form-control-label">Tanggal</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $bbkAktif->dateId ? $bbkAktif->dateId : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => $isKso ? $tahun->academicYearLink : $tahun]) }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $bbkAktif && $bbkAktif->detail()->count() > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                            <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Jumlah</div>
                        <h6 id="summary" class="mb-0">
                            @if($bbkAktif && $bbkAktif->detail()->count() > 0)
                            @if(($isKso && $bbkAktif->director_acc_status_id != 1) || (!$isKso && $bbkAktif->president_acc_status_id != 1))
                            {{ number_format($bbkAktif->detail()->sum('ppa_value'), 0, ',', '.') }}
                            @else
                            {{ $bbkAktif->totalValueWithSeparator }}
                            @endif
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
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar PPA</h6>
                @if($bbkAktif && $bbkAktif->director_acc_status_id == 1)
                <div class="m-0 float-right">
                <a href="{{ route('ppb.ekspor',['jenis' => $jenisAktif->link, 'tahun' => $isKso ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                </div>
                @endif
            </div>
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                  <strong>Sukses!</strong> {{ Session::get('success') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
                @if(Session::has('danger'))
                <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
                  <strong>Gagal!</strong> {{ Session::get('danger') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                   <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
                @if($bbkAktif && $bbkAktif->detail()->count() > 0)
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>No. PPA</th>
                                <th>Jumlah Perintah Bayar</th>
                                <th>Nomor Rekening Tujuan</th>
                                <th>Status</th>
                                @if($bbkAktif->director_acc_status_id != 1)
                                <th style="width: 120px">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bbkAktif->detail as $b)
                            <tr>
                                <td>
									@if($bbkAktif && $bbkAktif->director_acc_status_id == 1)
									<a href="{{ route('ppb.lihat', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $b->ppa->id]) }}" class="text-info detail-link">
									@else
                                    <a href="{{ route('ppb.ubah', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $b->ppa->id]) }}" class="text-info detail-link" target="_blank">
									@endif
                                    {{ $b->ppa->number }}
                                    </a>
                                </td>
                                <td>{{ $b->ppaValueWithSeparator }}</td>
                                <td>{{ $b->ppa->jenisAnggaranAnggaran->anggaran->unit->account_number }}</td>
                                <td>
                                    @if(!$b->ppa->director_acc_status_id && !$b->ppa->letris_acc_status_id)
                                    @if($b->ppa->detail()->whereNull('value_director')->count() > 0 && $b->ppa->detail()->whereNull('value_letris')->count() > 0)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Diperiksa Auliya"></i>
                                    @elseif($b->ppa->detail()->whereNull('value_letris')->count() > 0)
                                    <i class="fa fa-lg fa-eye text-success" data-toggle="tooltip" data-html="true" data-original-title="Sudah Diperiksa Auliya"></i>
                                    @elseif($b->ppa->detail()->sum('value_director') != $b->ppa->detail()->sum('value_letris'))
                                    @if($b->ppa->detail()->whereRaw('director_acc_time < updated_at')->count() > 0)
                                    <i class="fa fa-lg fa-clock text-danger" data-toggle="tooltip" data-original-title="Menunggu Diperiksa Ulang Auliya"></i>
                                    @else
                                    <i class="fa fa-lg fa-eye text-primary" data-toggle="tooltip" data-html="true" data-original-title="Sudah Diperiksa Ulang Auliya"></i>
                                    @endif
                                    @else
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Disepakati"></i>
                                    @endif
                                    @elseif($b->ppa->director_acc_status_id == 1 && !$b->ppa->letris_acc_status_id)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Disepakati Letris"></i>
                                    @elseif(!$b->ppa->director_acc_status_id && $b->ppa->letris_acc_status_id == 1)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Disepakati Auliya"></i>
                                    @elseif($b->ppa->director_acc_status_id == 1 && $b->ppa->letris_acc_status_id == 1)
                                    <i class="fa fa-lg fa-check-circle text-info" data-toggle="tooltip" data-original-title="Disepakati oleh Auliya dan Letris"></i>
                                    @else
                                    -
                                    @endif
                                </td>
                                @if($bbkAktif->director_acc_status_id != 1)
                                <td>
                                    <a href="{{ route('ppb.ubah', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $b->ppa->id]) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center mx-3 my-5">
                    <h3 class="text-center">Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data PPA yang ditemukan</h6>
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
@include('template.footjs.keuangan.change-year')
@endsection