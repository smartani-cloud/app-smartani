<<<<<<< HEAD
@extends('template.main.master')

@section('title')
RPPA
@endsection

@section('headmeta')
<!-- Bootstrap Toggle -->
<link href="{{ asset('vendor/bootstrap4-toggle/css/bootstrap4-toggle.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">RPPA</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lppa.index')}}">RPPA</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">{{ !$isYear ? $tahun->academic_year : $tahun }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link])}}">{{ $anggaranAktif->anggaran->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $lppaAktif->firstNumber }}</li>
  </ol>
</div>
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
    @php
    if(!in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv'])){
        if(Auth::user()->pegawai->unit_id == '5'){
            $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q){$q->where('position_id',Auth::user()->pegawai->jabatan->group()->first()->id);})->whereHas('ppa',function($q){$q->where('director_acc_status_id',1)->has('lppa');})->count();
        }
        else{
            $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q){$q->where('unit_id',Auth::user()->pegawai->unit_id);})->whereHas('ppa',function($q){$q->where('director_acc_status_id',1)->has('lppa');})->count();
        }
    }
    else{
        $anggaranCount = $j->anggaran()->whereHas('ppa',function($q){$q->where('director_acc_status_id',1)->has('lppa');})->count();
    }
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
                        <a href="{{ route('lppa.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
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
        @php
        $hasSelisih = $lppaAktif && $lppaAktif->ppa->bbk->ppa_value && $lppaAktif->detail()->whereNotNull('employee_id')->count() > 0 ? true: false;
        if($hasSelisih){
            $selisihLebih = 0;
            $selisihKurang = 0;
            foreach($lppaAktif->detail as $l){
                $selisih = ($l->ppaDetail->value)-$l->value;
                if($selisih > 0) $selisihLebih += $selisih;
                elseif($selisih < 0) $selisihKurang += $selisih;
            }
        }
        @endphp
        @if($lppaAktif->finance_acc_status_id == 1 && ($hasSelisih && $selisihLebih > 0) && $isAnggotaPa)
        <div class="alert alert-secondary alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle text-yellow mr-2"></i><strong>Perhatian!</strong> Total selisih yang lebih harap segera ditransfer ke rekening Bank Syariah Indonesia <strong>448 448 4321</strong>
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor RPPA</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $lppaAktif && $lppaAktif->number ? $lppaAktif->number : '-' }}
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
                  @if($lppaAktif && $lppaAktif->ppa->number)
                  @if(Auth::user()->role->name == 'fas')
                  {{ $lppaAktif->ppa->number }}
                  @else
                  <a href="{{ route('ppa.show',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->ppa->firstNumber]) }}" target="_blank" class="text-decoration-none text-info">{{ $lppaAktif->ppa->number }}</a>
                  @endif
                  @else
                  -
                  @endif
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
                  <label class="form-control-label">Pencairan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $lppaAktif && $lppaAktif->ppa->bbk->bbk->president_acc_time ? $lppaAktif->ppa->bbk->bbk->presidentAccDateId : $lppaAktif->ppa->bbk->bbk->directorAccDateId }}
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
                  <label class="form-control-label">Pelaporan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $lppaAktif && $lppaAktif->dateId ? $lppaAktif->dateId : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}" class="btn btn-sm btn-light">Kembali</a>
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
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Pengguna Anggaran</div>
                        <h6 class="mb-0">{{ $anggaranAktif->anggaran->accJabatan->name }}</h6>
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
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-money-bill-wave text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Pencairan</div>
                        <h6 class="mb-0">{{ $lppaAktif && $lppaAktif->ppa->bbk->ppa_value ? $lppaAktif->ppa->bbk->ppaValueWithSeparator : '0' }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    @php
                    $hasUsed = $lppaAktif && $lppaAktif->detail()->whereNotNull('employee_id')->count() > 0 ? true : false;
                    @endphp
                    <div class="mr-3">
                        <div class="icon-circle {{ $hasUsed ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-dolly text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Realisasi</div>
                        <h6 class="mb-0">
                            @if($lppaAktif && $lppaAktif->detail()->count() > 0)
                            {{ number_format($lppaAktif->detail()->sum('value'), 0, ',', '.') }}
                            @else
                            0
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $hasSelisih ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Selisih</div>
                        <h6 class="mb-0">
                            @if($hasSelisih)
                            @if($lppaAktif->finance_acc_status_id != 1)
                            @php
                            $selisih = $lppaAktif->ppa->bbk->ppa_value-($lppaAktif->detail()->sum('value'));
                            @endphp
                            {{ ($selisih > 0 ? '+' : null).number_format($selisih, 0, ',', '.') }}
                            @else
                            @php
                            $selisih = $lppaAktif->difference_total_value;
                            @endphp
                            {{ ($selisih > 0 ? '+' : null).$lppaAktif->differenceTotalValueWithSeparator }}
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
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $hasSelisih ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-plus text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Selisih Lebih</div>
                        <h6 class="mb-0">
                            @if($hasSelisih)
                            {{ ($selisihLebih > 0 ? '+' : null).number_format($selisihLebih, 0, ',', '.') }}
                            @else
                            0
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $hasSelisih ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-minus text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Selisih Kurang</div>
                        <h6 class="mb-0">
                            @if($hasSelisih)
                            {{ number_format($selisihKurang, 0, ',', '.') }}
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
        @if($lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count()))
        <form action="{{ route('lppa.perbarui',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->firstNumber]) }}" id="lppa-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" name="validate" value="">
        @endif
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
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
            @if($lppaAktif->finance_acc_status_id == 1 && ($hasSelisih && $selisihKurang < 0))
            <div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
                <i class="fa fa-info-circle text-info mr-2"></i>Selisih yang kurang diajukan dengan PPA nomor <strong><a href="{{ route('ppa.show',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->ppaKurang->firstNumber]) }}" target="_blank" class="text-info">{{ $lppaAktif->ppaKurang->number }}</a></strong>
            </div>
            @endif
            @if($lppaAktif->detail()->count() > 0)
            @php
            $i = 1;
            @endphp
            <div class="table-responsive">
                <table id="lppaDetail" class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Keterangan</th>
                            <th>Pencairan</th>
                            <th>Realisasi</th>
                            <th>Selisih</th>
                            <th>Bukti Transaksi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lppaAktif->detail as $l)
                        <tr id="l-{{ $l->id }}">
                            <td>{{ $i++ }}</td>
                            <td>{{ $l->ppaDetail->note }}</td>
                            <td>
                                {{ $l->ppaDetail->valueWithSeparator }}
                            </td>
                            <td>
                                @if($lppaAktif->finance_acc_status_id == 1)
                                {{ $l->valueWithSeparator }}
                                @elseif(($lppaAktif->detail()->where('acc_status_id',1)->count() >= ($lppaAktif->detail()->count())) || $l->ppaDetail->value == 0)
                                <input type="text" class="form-control form-control-sm" value="{{ $l->valueWithSeparator }}" disabled>
                                @else
                                <input name="value-{{ $l->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $l->valueWithSeparator }}">
                                @endif
                            </td>
                            <td>
                                @if($l->employee_id)
                                @php
                                $selisih = ($l->ppaDetail->value)-$l->value;
                                @endphp
                                {{ ($selisih > 0 ? '+' : null).number_format($selisih, 0, ',', '.') }}
                                @else
                                0
                                @endif
                            </td>
                            <td>
                                @if($lppaAktif->finance_acc_status_id == 1)
                                @if($l->receipt_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="{{ ucwords($l->buktiStatus->status) }}"></i>
                                @elseif($l->receipt_status_id == 2)
                                <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="{{ ucwords($l->buktiStatus->status) }}"></i>
                                @else
                                <i class="fa fa-lg fa-question-circle text-warning" data-toggle="tooltip" data-original-title="Tidak diketahui"></i>
                                @endif
                                @elseif(($lppaAktif->detail()->where('acc_status_id',1)->count() >= ($lppaAktif->detail()->count())) || $l->ppaDetail->value == 0)
                                <input class="receipt-toggle" type="checkbox" data-toggle="toggle" data-on="Ada" data-off="Tidak" data-size="small" data-onstyle="success" data-offstyle="danger" {{ $l->receipt_status_id == 1 ? 'checked' : null }} disabled>
                                @else
                                <input name="receipt-{{ $l->id }}" class="receipt-toggle" type="checkbox" data-toggle="toggle" data-on="Ada" data-off="Tidak" data-size="small" data-onstyle="success" data-offstyle="danger" {{ $l->receipt_status_id == 1 ? 'checked' : null }} >
                                @endif
                            </td>
                            <td>
                                @if(!$l->employee_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada nominal realisasi yang dimasukkan"></i>
                                @elseif(!$l->acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == $anggaranAktif->anggaran->acc_position_id ? 'Anda' : $anggaranAktif->anggaran->accJabatan->name }}"></i>
                                @elseif(($lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count())) && $l->acc_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-secondary mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($l->accPegawai) ? 'Anda' : $l->accPegawai->name }}<br>{{ date('d M Y H.i.s', strtotime($l->acc_time)) }}"></i>
                                @elseif(($lppaAktif->detail()->where('acc_status_id',1)->count() >= ($lppaAktif->detail()->count())) && !$lppaAktif->finance_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan {{ Auth::user()->pegawai->position_id == 57 ? 'Anda' : 'Supervisor Akuntansi' }}"></i>
                                @elseif($lppaAktif->finance_acc_status_id)
                                <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($lppaAktif->accKeuangan) ? 'Anda' : $lppaAktif->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($lppaAktif->finance_acc_time)) }}"></i>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if($lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count()))
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            @if($lppaAktif->detail()->whereHas('ppaDetail',function($q){$q->where('value','>',0);})->count())
                            <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                            @endif
                            <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#saveAccept">Simpan & Laporkan</button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data laporan yang ditemukan</h6>
            </div>
            <div class="card-footer"></div>
            @endif
        @if($lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count()))
        </form>
        @endif
        </div>
    </div>
</div>
@endif
<!--Row-->

<div class="modal fade" id="saveAccept" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-secondary">
          <i class="material-icons text-secondary">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menyimpan dan melaporkan semua nominal realisasi yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <button type="submit" id="saveAcceptBtn" class="btn btn-primary" data-form="lppa-form">Ya, Simpan & Laporkan</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Toggle -->
<script src="{{ asset('vendor/bootstrap4-toggle/js/bootstrap4-toggle.min.js') }}"></script>
<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@if($lppaAktif && $lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count()))
@include('template.footjs.modal.post_save_accept')
@endif
=======
@extends('template.main.master')

@section('title')
RPPA
@endsection

@section('headmeta')
<!-- Bootstrap Toggle -->
<link href="{{ asset('vendor/bootstrap4-toggle/css/bootstrap4-toggle.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">RPPA</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lppa.index')}}">RPPA</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">{{ !$isYear ? $tahun->academic_year : $tahun }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link])}}">{{ $anggaranAktif->anggaran->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $lppaAktif->firstNumber }}</li>
  </ol>
</div>
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
    @php
    if(!in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv'])){
        if(Auth::user()->pegawai->unit_id == '5'){
            $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q){$q->where('position_id',Auth::user()->pegawai->jabatan->group()->first()->id);})->whereHas('ppa',function($q){$q->where('director_acc_status_id',1)->has('lppa');})->count();
        }
        else{
            $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q){$q->where('unit_id',Auth::user()->pegawai->unit_id);})->whereHas('ppa',function($q){$q->where('director_acc_status_id',1)->has('lppa');})->count();
        }
    }
    else{
        $anggaranCount = $j->anggaran()->whereHas('ppa',function($q){$q->where('director_acc_status_id',1)->has('lppa');})->count();
    }
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
                        <a href="{{ route('lppa.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
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
        @php
        $hasSelisih = $lppaAktif && $lppaAktif->ppa->bbk->ppa_value && $lppaAktif->detail()->whereNotNull('employee_id')->count() > 0 ? true: false;
        if($hasSelisih){
            $selisihLebih = 0;
            $selisihKurang = 0;
            foreach($lppaAktif->detail as $l){
                $selisih = ($l->ppaDetail->value)-$l->value;
                if($selisih > 0) $selisihLebih += $selisih;
                elseif($selisih < 0) $selisihKurang += $selisih;
            }
        }
        @endphp
        @if($lppaAktif->finance_acc_status_id == 1 && ($hasSelisih && $selisihLebih > 0) && $isAnggotaPa)
        <div class="alert alert-secondary alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-circle text-yellow mr-2"></i><strong>Perhatian!</strong> Total selisih yang lebih harap segera ditransfer ke rekening Bank Syariah Indonesia <strong>448 448 4321</strong>
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor RPPA</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $lppaAktif && $lppaAktif->number ? $lppaAktif->number : '-' }}
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
                  @if($lppaAktif && $lppaAktif->ppa->number)
                  @if(Auth::user()->role->name == 'fas')
                  {{ $lppaAktif->ppa->number }}
                  @else
                  <a href="{{ route('ppa.show',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->ppa->firstNumber]) }}" target="_blank" class="text-decoration-none text-info">{{ $lppaAktif->ppa->number }}</a>
                  @endif
                  @else
                  -
                  @endif
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
                  <label class="form-control-label">Pencairan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $lppaAktif && $lppaAktif->ppa->bbk->bbk->president_acc_time ? $lppaAktif->ppa->bbk->bbk->presidentAccDateId : $lppaAktif->ppa->bbk->bbk->directorAccDateId }}
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
                  <label class="form-control-label">Pelaporan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $lppaAktif && $lppaAktif->dateId ? $lppaAktif->dateId : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}" class="btn btn-sm btn-light">Kembali</a>
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
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Pengguna Anggaran</div>
                        <h6 class="mb-0">{{ $anggaranAktif->anggaran->accJabatan->name }}</h6>
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
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-money-bill-wave text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Pencairan</div>
                        <h6 class="mb-0">{{ $lppaAktif && $lppaAktif->ppa->bbk->ppa_value ? $lppaAktif->ppa->bbk->ppaValueWithSeparator : '0' }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    @php
                    $hasUsed = $lppaAktif && $lppaAktif->detail()->whereNotNull('employee_id')->count() > 0 ? true : false;
                    @endphp
                    <div class="mr-3">
                        <div class="icon-circle {{ $hasUsed ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-dolly text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Realisasi</div>
                        <h6 class="mb-0">
                            @if($lppaAktif && $lppaAktif->detail()->count() > 0)
                            {{ number_format($lppaAktif->detail()->sum('value'), 0, ',', '.') }}
                            @else
                            0
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $hasSelisih ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Selisih</div>
                        <h6 class="mb-0">
                            @if($hasSelisih)
                            @if($lppaAktif->finance_acc_status_id != 1)
                            @php
                            $selisih = $lppaAktif->ppa->bbk->ppa_value-($lppaAktif->detail()->sum('value'));
                            @endphp
                            {{ ($selisih > 0 ? '+' : null).number_format($selisih, 0, ',', '.') }}
                            @else
                            @php
                            $selisih = $lppaAktif->difference_total_value;
                            @endphp
                            {{ ($selisih > 0 ? '+' : null).$lppaAktif->differenceTotalValueWithSeparator }}
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
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $hasSelisih ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-plus text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Selisih Lebih</div>
                        <h6 class="mb-0">
                            @if($hasSelisih)
                            {{ ($selisihLebih > 0 ? '+' : null).number_format($selisihLebih, 0, ',', '.') }}
                            @else
                            0
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $hasSelisih ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-minus text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Selisih Kurang</div>
                        <h6 class="mb-0">
                            @if($hasSelisih)
                            {{ number_format($selisihKurang, 0, ',', '.') }}
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
        @if($lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count()))
        <form action="{{ route('lppa.perbarui',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->firstNumber]) }}" id="lppa-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" name="validate" value="">
        @endif
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
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
            @if($lppaAktif->finance_acc_status_id == 1 && ($hasSelisih && $selisihKurang < 0))
            <div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
                <i class="fa fa-info-circle text-info mr-2"></i>Selisih yang kurang diajukan dengan PPA nomor <strong><a href="{{ route('ppa.show',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->ppaKurang->firstNumber]) }}" target="_blank" class="text-info">{{ $lppaAktif->ppaKurang->number }}</a></strong>
            </div>
            @endif
            @if($lppaAktif->detail()->count() > 0)
            @php
            $i = 1;
            @endphp
            <div class="table-responsive">
                <table id="lppaDetail" class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Keterangan</th>
                            <th>Pencairan</th>
                            <th>Realisasi</th>
                            <th>Selisih</th>
                            <th>Bukti Transaksi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lppaAktif->detail as $l)
                        <tr id="l-{{ $l->id }}">
                            <td>{{ $i++ }}</td>
                            <td>{{ $l->ppaDetail->note }}</td>
                            <td>
                                {{ $l->ppaDetail->valueWithSeparator }}
                            </td>
                            <td>
                                @if($lppaAktif->finance_acc_status_id == 1)
                                {{ $l->valueWithSeparator }}
                                @elseif(($lppaAktif->detail()->where('acc_status_id',1)->count() >= ($lppaAktif->detail()->count())) || $l->ppaDetail->value == 0)
                                <input type="text" class="form-control form-control-sm" value="{{ $l->valueWithSeparator }}" disabled>
                                @else
                                <input name="value-{{ $l->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $l->valueWithSeparator }}">
                                @endif
                            </td>
                            <td>
                                @if($l->employee_id)
                                @php
                                $selisih = ($l->ppaDetail->value)-$l->value;
                                @endphp
                                {{ ($selisih > 0 ? '+' : null).number_format($selisih, 0, ',', '.') }}
                                @else
                                0
                                @endif
                            </td>
                            <td>
                                @if($lppaAktif->finance_acc_status_id == 1)
                                @if($l->receipt_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="{{ ucwords($l->buktiStatus->status) }}"></i>
                                @elseif($l->receipt_status_id == 2)
                                <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="{{ ucwords($l->buktiStatus->status) }}"></i>
                                @else
                                <i class="fa fa-lg fa-question-circle text-warning" data-toggle="tooltip" data-original-title="Tidak diketahui"></i>
                                @endif
                                @elseif(($lppaAktif->detail()->where('acc_status_id',1)->count() >= ($lppaAktif->detail()->count())) || $l->ppaDetail->value == 0)
                                <input class="receipt-toggle" type="checkbox" data-toggle="toggle" data-on="Ada" data-off="Tidak" data-size="small" data-onstyle="success" data-offstyle="danger" {{ $l->receipt_status_id == 1 ? 'checked' : null }} disabled>
                                @else
                                <input name="receipt-{{ $l->id }}" class="receipt-toggle" type="checkbox" data-toggle="toggle" data-on="Ada" data-off="Tidak" data-size="small" data-onstyle="success" data-offstyle="danger" {{ $l->receipt_status_id == 1 ? 'checked' : null }} >
                                @endif
                            </td>
                            <td>
                                @if(!$l->employee_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada nominal realisasi yang dimasukkan"></i>
                                @elseif(!$l->acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == $anggaranAktif->anggaran->acc_position_id ? 'Anda' : $anggaranAktif->anggaran->accJabatan->name }}"></i>
                                @elseif(($lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count())) && $l->acc_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-secondary mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($l->accPegawai) ? 'Anda' : $l->accPegawai->name }}<br>{{ date('d M Y H.i.s', strtotime($l->acc_time)) }}"></i>
                                @elseif(($lppaAktif->detail()->where('acc_status_id',1)->count() >= ($lppaAktif->detail()->count())) && !$lppaAktif->finance_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan {{ Auth::user()->pegawai->position_id == 57 ? 'Anda' : 'Supervisor Akuntansi' }}"></i>
                                @elseif($lppaAktif->finance_acc_status_id)
                                <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($lppaAktif->accKeuangan) ? 'Anda' : $lppaAktif->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($lppaAktif->finance_acc_time)) }}"></i>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if($lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count()))
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            @if($lppaAktif->detail()->whereHas('ppaDetail',function($q){$q->where('value','>',0);})->count())
                            <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                            @endif
                            <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#saveAccept">Simpan & Laporkan</button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data laporan yang ditemukan</h6>
            </div>
            <div class="card-footer"></div>
            @endif
        @if($lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count()))
        </form>
        @endif
        </div>
    </div>
</div>
@endif
<!--Row-->

<div class="modal fade" id="saveAccept" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-secondary">
          <i class="material-icons text-secondary">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menyimpan dan melaporkan semua nominal realisasi yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <button type="submit" id="saveAcceptBtn" class="btn btn-primary" data-form="lppa-form">Ya, Simpan & Laporkan</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Toggle -->
<script src="{{ asset('vendor/bootstrap4-toggle/js/bootstrap4-toggle.min.js') }}"></script>
<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@if($lppaAktif && $lppaAktif->detail()->where('acc_status_id',1)->count() < ($lppaAktif->detail()->count()))
@include('template.footjs.modal.post_save_accept')
@endif
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection