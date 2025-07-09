<<<<<<< HEAD
@extends('template.main.master')

@section('title')
PPB
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
    <li class="breadcrumb-item"><a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">{{ !$isYear ? $tahun->academic_year : $tahun }}</a></li>
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
          <a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]) }}" class="btn btn-sm btn-light">Kembali</a>
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
                @if($isKso || (!$isKso && $bbkAktif->president_acc_status_id == 1))
                <div class="m-0 float-right">
                <a href="{{ route('ppb.ekspor',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                </div>
                @endif
                @endif
            </div>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bbkAktif->detail as $b)
                            <tr>
                                <td>
                                    <a href="{{ route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $b->ppa->jenisAnggaranAnggaran->anggaran->link, 'nomor' => $b->ppa->firstNumber]) }}" class="text-info detail-link" target="_blank">
                                    {{ $b->ppa->number }}
                                    </a>
                                </td>
                                <td>{{ number_format($b->ppa->detail()->sum('value_fam'), 0, ',', '.') }}</td>
                                <td>{{ $b->ppaValueWithSeparator }}</td>
                                <td>{{ $b->ppa->jenisAnggaranAnggaran->anggaran->unit->account_number }}</td>
                                <td>
                                    @if(!$bbkAktif->director_acc_status_id)
                                    @if($b->ppa->detail()->whereNull('value_director')->count() > 0)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu diperiksa oleh {{ Auth::user()->pegawai->position_id == 17 ? 'Anda' : 'Director' }}"></i>
                                    @else
                                    <i class="fa fa-lg fa-eye text-success" data-toggle="tooltip" data-original-title="Sudah diperiksa oleh {{ Auth::user()->pegawai->position_id == 17 ? 'Anda' : 'Director' }}"></i>
                                    @endif
                                    @elseif($bbkAktif->director_acc_status_id == 1 && !$bbkAktif->president_acc_status_id)
                                    @if($isKso)
                                    <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($bbkAktif->accDirektur) ? 'Anda' : $bbkAktif->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($bbkAktif->director_acc_time)) }}"></i>
                                    @else
                                    @if($b->ppa->detail()->whereNull('value_president')->count() > 0)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu diperiksa oleh {{ Auth::user()->pegawai->position_id == 16 ? 'Anda' : 'Ketua Yayasan' }}"></i>
                                    @else
                                    <i class="fa fa-lg fa-eye text-info" data-toggle="tooltip" data-original-title="Sudah diperiksa oleh {{ Auth::user()->pegawai->position_id == 16 ? 'Anda' : 'Ketua Yayasan' }}"></i>
                                    @endif
                                    @endif
                                    @elseif(!$isKso && $bbkAktif->president_acc_status_id == 1)
                                    @if($b->ppa->totalValue != $b->ppa->detail()->sum('value_fam'))
                                    <i class="fa fa-lg fa-check-circle text-info mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui dengan perubahan oleh {{ Auth::user()->pegawai->is($bbkAktif->accKetua) ? 'Anda' : $bbkAktif->accKetua->name }}<br>{{ date('d M Y H.i.s', strtotime($bbkAktif->president_acc_time)) }}<br>Awal: {{ number_format($b->ppa->detail()->sum('value_director'), 0, ',', '.') }}"></i>
                                    @else
                                    <i class="fa fa-lg fa-check-circle text-info mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($bbkAktif->accKetua) ? 'Anda' : $bbkAktif->accKetua->name }}<br>{{ date('d M Y H.i.s', strtotime($bbkAktif->president_acc_time)) }}"></i>
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
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
=======
@extends('template.main.master')

@section('title')
PPB
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
    <li class="breadcrumb-item"><a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">{{ !$isYear ? $tahun->academic_year : $tahun }}</a></li>
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
          <a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun]) }}" class="btn btn-sm btn-light">Kembali</a>
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
                @if($isKso || (!$isKso && $bbkAktif->president_acc_status_id == 1))
                <div class="m-0 float-right">
                <a href="{{ route('ppb.ekspor',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $bbkAktif->firstNumber]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                </div>
                @endif
                @endif
            </div>
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bbkAktif->detail as $b)
                            <tr>
                                <td>
                                    <a href="{{ route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $b->ppa->jenisAnggaranAnggaran->anggaran->link, 'nomor' => $b->ppa->firstNumber]) }}" class="text-info detail-link" target="_blank">
                                    {{ $b->ppa->number }}
                                    </a>
                                </td>
                                <td>{{ number_format($b->ppa->detail()->sum('value_fam'), 0, ',', '.') }}</td>
                                <td>{{ $b->ppaValueWithSeparator }}</td>
                                <td>{{ $b->ppa->jenisAnggaranAnggaran->anggaran->unit->account_number }}</td>
                                <td>
                                    @if(!$bbkAktif->director_acc_status_id)
                                    @if($b->ppa->detail()->whereNull('value_director')->count() > 0)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu diperiksa oleh {{ Auth::user()->pegawai->position_id == 17 ? 'Anda' : 'Director' }}"></i>
                                    @else
                                    <i class="fa fa-lg fa-eye text-success" data-toggle="tooltip" data-original-title="Sudah diperiksa oleh {{ Auth::user()->pegawai->position_id == 17 ? 'Anda' : 'Director' }}"></i>
                                    @endif
                                    @elseif($bbkAktif->director_acc_status_id == 1 && !$bbkAktif->president_acc_status_id)
                                    @if($isKso)
                                    <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($bbkAktif->accDirektur) ? 'Anda' : $bbkAktif->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($bbkAktif->director_acc_time)) }}"></i>
                                    @else
                                    @if($b->ppa->detail()->whereNull('value_president')->count() > 0)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu diperiksa oleh {{ Auth::user()->pegawai->position_id == 16 ? 'Anda' : 'Ketua Yayasan' }}"></i>
                                    @else
                                    <i class="fa fa-lg fa-eye text-info" data-toggle="tooltip" data-original-title="Sudah diperiksa oleh {{ Auth::user()->pegawai->position_id == 16 ? 'Anda' : 'Ketua Yayasan' }}"></i>
                                    @endif
                                    @endif
                                    @elseif(!$isKso && $bbkAktif->president_acc_status_id == 1)
                                    @if($b->ppa->totalValue != $b->ppa->detail()->sum('value_fam'))
                                    <i class="fa fa-lg fa-check-circle text-info mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui dengan perubahan oleh {{ Auth::user()->pegawai->is($bbkAktif->accKetua) ? 'Anda' : $bbkAktif->accKetua->name }}<br>{{ date('d M Y H.i.s', strtotime($bbkAktif->president_acc_time)) }}<br>Awal: {{ number_format($b->ppa->detail()->sum('value_director'), 0, ',', '.') }}"></i>
                                    @else
                                    <i class="fa fa-lg fa-check-circle text-info mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($bbkAktif->accKetua) ? 'Anda' : $bbkAktif->accKetua->name }}<br>{{ date('d M Y H.i.s', strtotime($bbkAktif->president_acc_time)) }}"></i>
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection