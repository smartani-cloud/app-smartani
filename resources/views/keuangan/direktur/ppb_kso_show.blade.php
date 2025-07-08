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
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
    @php
    $anggaranCount = $j->anggaran()->whereHas('ppa',function($q){$q->where('finance_acc_status_id',1);})->count();
    @endphp
    @if($jenisAktif == $j)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-brand-green">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled="disabled">Pilih</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    @if($anggaranCount > 0)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-brand-green">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('ppb.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-secondary">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary disabled"role="button" aria-disabled="true">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
    @endforeach
</div>
--}}
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
                <!-- <div class="m-0 float-right">
                @if($bbkAktif && $bbkAktif->director_acc_status_id != 1)
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#acceptAll">Setujui Semua <i class="fas fa-check ml-1"></i></button>
                @endif
                </div> -->
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
                                <th>Pengajuan Awal</th>
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
                                    <a href="{{ route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => $isKso ? $tahun->academicYearLink : $tahun, 'anggaran' => $b->ppa->jenisAnggaranAnggaran->anggaran->link, 'nomor' => $b->ppa->firstNumber]) }}" class="text-info detail-link" target="_blank">
                                    {{ $b->ppa->number }}
                                    </a>
                                </td>
                                <td>{{ number_format($b->ppa->detail()->sum('value_fam'), 0, ',', '.') }}</td>
                                <td>{{ $b->ppaValueWithSeparator }}</td>
                                <td>{{ $b->ppa->jenisAnggaranAnggaran->anggaran->unit->account_number }}</td>
                                <td>
                                    @if(!$b->ppa->director_acc_status_id && !$b->ppa->letris_acc_status_id)
                                    @if($b->ppa->detail()->whereNull('value_director')->count() > 0 && $b->ppa->detail()->whereNull('value_letris')->count() > 0)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Diperiksa Auliya"></i>
                                    @elseif($b->ppa->detail()->whereNull('value_letris')->count() > 0)
                                    <i class="fa fa-lg fa-eye text-success" data-toggle="tooltip" data-original-title="Sudah Diperiksa Auliya"></i>
                                    @elseif($b->ppa->detail()->sum('value_director') != $b->ppa->detail()->sum('value_letris'))
                                    @if($b->ppa->detail()->whereRaw('director_acc_time < updated_at')->count() > 0)
                                    <i class="fa fa-lg fa-minus-circle text-danger" data-toggle="tooltip" data-original-title="Letris melakukan perubahan"></i>
                                    @else
                                    <i class="fa fa-lg fa-eye text-success" data-toggle="tooltip" data-original-title="Sudah Diperiksa Auliya"></i>
                                    @endif
                                    @else
                                    <i class="fa fa-lg fa-clock text-light" data-toggle="tooltip" data-original-title="Menunggu Disepakati"></i>
                                    @endif
                                    @elseif($b->ppa->director_acc_status_id == 1 && !$b->ppa->letris_acc_status_id)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Disepakati Letris"></i>
                                    @elseif(!$b->ppa->director_acc_status_id && $b->ppa->letris_acc_status_id == 1)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Disepakati Auliya"></i>
                                    @elseif($b->ppa->director_acc_status_id == 1 && $b->ppa->letris_acc_status_id == 1)
                                    <i class="fa fa-lg fa-check-circle text-info" data-toggle="tooltip" data-html="true" data-original-title="Disepakati oleh Auliya dan Letris"></i>
                                    @else
                                    -
                                    @endif
                                </td>
                                @if($bbkAktif->director_acc_status_id != 1)
                                <td>
                                    <a href="{{ route('ppb.ubah', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $b->ppa->id]) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    @if($bbkAktif->detail()->count() > 1)
                                    <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('PPB', '{{ addslashes(htmlspecialchars('PPA No. '.$b->ppa->number)) }}', '{{ route('ppb.tunda', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $b->ppa->id]) }}')">
                                        <i class="fas fa-history"></i>
                                    </a>
                                    @endif
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

@if($bbkAktif->director_acc_status_id != 1 && $bbkAktif->detail()->count() > 1)
@include('template.modal.konfirmasi_tunda')
@endif

@if($bbkAktif && $bbkAktif->director_acc_status_id != 1)
<!-- <div class="modal fade" id="acceptAll" tabindex="-1" role="dialog" aria-labelledby="setujuiModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-success">
          <i class="material-icons text-success">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menyetujui semua PPA yang ada pada PPB ini?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('ppb.validasi',['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div> -->
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@if($bbkAktif->director_acc_status_id != 1 && $bbkAktif->detail()->count() > 1)
@include('template.footjs.modal.get_delete')
@endif
@endsection