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
    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
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
    <div class="col-lg-4 col-12 mb-4">
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
    <div class="col-lg-4 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $ppaAktif && $ppaAktif->detail()->count() > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Jumlah Auliya</div>
                        <h6 id="summary" class="mb-0">
                            @if($ppaAktif && $ppaAktif->detail()->count() > 0)
                            @if($ppaAktif->finance_acc_status_id != 1)
                            {{ number_format($ppaAktif->detail()->sum('value'), 0, ',', '.') }}
                            @else
                            {{ $ppaAktif->totalValueWithSeparator }}
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
    <div class="col-lg-4 col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $ppaAktif && $ppaAktif->detail()->whereNotNull('value_letris')->count() > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Jumlah Letris</div>
                        <h6 id="summary" class="mb-0">
                            @if($ppaAktif && $ppaAktif->detail()->whereNotNull('value_letris')->count() > 0)
                            {{ number_format($ppaAktif->detail()->sum('value_letris'), 0, ',', '.') }}
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
        <form action="{{ route('ppb.perbarui',['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $ppaAktif->id]) }}" id="ppa-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" name="validate" value="">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $ppaAktif->jenisAnggaranAnggaran->anggaran->name }}</h6>
                @if($ppaAktif && $ppaAktif->finance_acc_status_id == 1 && $ppaAktif->bbk && $ppaAktif->bbk->bbk->director_acc_status_id != 1)
                <div class="m-0 float-right">
                @php $bypass = 1 @endphp
      					@if($ppaAktif && (($role == 'direktur' && $ppaAktif->director_acc_status_id != 1) || ($role == 'keulsi' && $ppaAktif->letris_acc_status_id != 1)) && $bypass != 1)
      					@if($ppaAktif->detail()->where('letris_acc_status_id',1)->count() >= $ppaAktif->detail()->count())
      					<button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#agreeAll">Sepakati <i class="fas fa-handshake ml-1"></i></button>
      					@else
      					<button type="button" class="btn btn-secondary btn-sm" disabled="disabled">Sepakati <i class="fas fa-handshake ml-1"></i></button>
      					@endif
      					@endif
      					@if($role == 'keulsi' &&  $ppaAktif->detail()->whereNull('value_director')->count() < 1)
      					<a href="{{ route('ppa.ekspor', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $ppaAktif->jenisAnggaranAnggaran->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-export ml-1"></i></a>
      					@endif
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
			@if($ppaAktif && ($ppaAktif->detail()->where('letris_acc_status_id',1)->count() >= $ppaAktif->detail()->count()))
			@if($ppaAktif->director_acc_status_id != 1 && $ppaAktif->letris_acc_status_id != 1)
			<div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
              <!-- Jangan lupa ekspor daftar PPA ini untuk kebutuhan arsip sebelum menekan tombol "Sepakati" -->
              <i class="fa fa-info-circle text-info mr-2"></i>Jangan lupa ekspor daftar PPA ini untuk kebutuhan arsip
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
              <i class="fa fa-check-circle mr-2"></i>PPA ini berhasil disepakati Auliya dan Letris
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
                            <th>Sisa Saldo</th>
                            <th style="min-width: 200px">Jumlah Auliya</th>
                            <th style="min-width: 200px">Jumlah Letris</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ppaAktif->detail as $p)
                        <tr id="p-{{ $p->id }}">
                            <td>{{ $i++ }}</td>
                            <td class="detail-account">{{ $p->akun->codeName }}</td>
                            <td class="detail-note">{{ $p->note }}</td>
                            @php
                            $apbyDetail = $apbyAktif ? $p->akun->apby()->whereHas('apby',function($q)use($apbyAktif){$q->where('id',$apbyAktif->id);})->where('account_id',$p->account_id)->first() : null;
                            @endphp
                            <td>{{ $apbyDetail ? $apbyDetail->balanceWithSeparator : '-' }}</td>
                            <td class="detail-value" style="min-width: 200px">
                                @if($p->letris_acc_status_id == 1 || $role == 'keulsi')
                                <input type="text" class="form-control form-control-sm" value="{{ $p->valueWithSeparator }}" disabled>
                                @else
                                <input name="value-{{ $p->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $p->valueWithSeparator }}">
                                @endif
                            </td>
                            <td class="letris-value" style="min-width: 200px">
                                @if(!$p->value_director || $p->letris_acc_status_id == 1 || $role == 'direktur')
                                <input type="text" class="form-control form-control-sm" value="{{ $p->value_letris ? $p->valueLetrisWithSeparator : $p->valueWithSeparator }}" disabled>
                                @else
                                <input name="value-{{ $p->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $p->value_letris ? $p->valueLetrisWithSeparator : $p->valueWithSeparator }}">
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if($ppaAktif && ($ppaAktif->detail()->where('letris_acc_status_id',1)->count() < $ppaAktif->detail()->count()) && $ppaAktif->detail()->whereNull('value_director')->count() < 1)
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data pengajuan yang ditemukan</h6>
            </div>
            <div class="card-footer"></div>
            @endif
        </form>
        </div>
    </div>
</div>
@endif
<!--Row-->

@if($ppaAktif && (($role == 'direktur' && $ppaAktif->director_acc_status_id != 1) || ($role == 'keulsi' && $ppaAktif->letris_acc_status_id != 1)) && ($ppaAktif->detail()->where('letris_acc_status_id',1)->count() >= $ppaAktif->detail()->count()))
<div class="modal fade" id="agreeAll" tabindex="-1" role="dialog" aria-labelledby="sepakatiModalLabel" aria-hidden="true" style="display: none;">
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
        Apakah Anda yakin ingin menyepakati semua alokasi dana yang ada pada PPA ini?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
		<form action="{{ route('ppb.sepakati',['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'nomor' => $bbkAktif->firstNumber, 'ppa' => $ppaAktif->id]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Sepakati</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('footjs')
<!-- Page level plugins -->

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@endsection