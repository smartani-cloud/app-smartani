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
    @if($jenisAktif)
    <li class="breadcrumb-item"><a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ !$isYear ? $tahun->academic_year : $tahun }}</li>
    @endif
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
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @if($years && count($years) > 0)
                      @foreach($years as $y)
                        <option value="{{ $y }}" {{ $isYear && $tahun == $y ? 'selected' : ''}}>{{ $y }}</option>
                      @endforeach
                      @elseif($isYear)
                      @if($tahun != date('Y'))
                      <option value="" disabled="disabled" selected>Pilih</option>
                      @endif
                      <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                      @if((!$academicYears && !$isYear) ||($academicYears && count($academicYears) > 0))
                      @foreach($tahunPelajaran as $t)
                      <option value="{{ $t->academicYearLink }}" {{ !$isYear && $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                      @endif
                    </select>
                    <a href="{{ route('ppb.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('ppb.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
                    </div>
                  </div>
                </div>
              </div>
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
                <h6 class="m-0 font-weight-bold text-brand-green">PPB</h6>
            </div>
            @if($bbk && count($bbk) > 0)
            @php
            $i = 1;
            @endphp
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th style="white-space: nowrap">Nomor PPB</th>
                            <th>Status</th>
                            <th>Jumlah</th>
                            <th style="width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bbk->sortByDesc('id')->all() as $b)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $b->date ? $b->date : '-' }}</td>
                            <td>{{ $b->number ? $b->number : '-' }}</td>
                            <td>
                                @if($b->detail()->count() < 1)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada daftar PPA yang dimasukkan untuk PPB ini"></i>
                                @elseif(!$b->director_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan {{ Auth::user()->pegawai->position_id == 17 ? 'Anda' : 'Director' }}"></i>
                                @elseif($b->director_acc_status_id == 1 && !$b->president_acc_status_id)
                                @if($isKso)
                                <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($b->accDirektur) ? 'Anda' : $b->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($b->director_acc_time)) }}"></i>
                                @else
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan {{ Auth::user()->pegawai->position_id == 16 ? 'Anda' : 'Ketua Yayasan' }}"></i>
                                @endif
                                @elseif(!$isKso && $b->president_acc_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-info mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($b->accKetua) ? 'Anda' : $b->accKetua->name }}<br>{{ date('d M Y H.i.s', strtotime($b->president_acc_time)) }}"></i>
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if(($isKso && $b->director_acc_status_id != 1 || !$isKso && $b->president_acc_status_id != 1))
                                {{ number_format($b->detail()->sum('ppa_value'), 0, ',', '.') }}
                                @else
                                {{ $b->totalValueWithSeparator }}
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('ppb.show', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'nomor' => $b->firstNumber]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data PPB yang ditemukan</h6>
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