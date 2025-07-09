<<<<<<< HEAD
@extends('template.main.master')

@section('title')
PSC Pegawai
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@php
$role = Auth::user()->role->name;
@endphp
@if(in_array($role,['admin','am','aspv','direktur','etl','etm','fam','faspv','kepsek','pembinayys','ketuayys','wakasek']))
@include('template.sidebar.kepegawaian.'.$role)
@else
@include('template.sidebar.kepegawaian.employee')
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">PSC Pegawai</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index') }}">PSC Pegawai</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]) }}">{{ $tahun->academic_year }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}">{{ $unitAktif->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $pegawaiAktif->name }}</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nama</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $pegawaiAktif->name }}
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
                  <label class="form-control-label">Tahun Pelajaran</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $tahun->academicYearLink }}
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
                  <label class="form-control-label">Unit</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $unitAktif->name }}
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
                  <label class="form-control-label">Jabatan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $jabatan->name }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" class="btn btn-sm btn-light">Kembali</a>
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
                        <div class="icon-circle bg-{{ $nilai && $nilai->total_score ? 'brand-green' : 'secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Jumlah Nilai</div>
                        <h6 class="mb-0">{{ $nilai && $nilai->total_score ? $nilai->total_score : '-' }}</h6>
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
                        <div class="icon-circle bg-{{ $nilai && $nilai->grade_name ? 'brand-green' : 'secondary' }}">
                          <i class="fas fa-equals text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Grade</div>
                        <h6 class="mb-0">{{ $nilai && $nilai->grade_name ? $nilai->grade_name : '-' }}</h6>
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
                <h6 class="m-0 font-weight-bold text-brand-green">PSC Pegawai</h6>
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
              @if($nilai && $nilai->detail()->count() > 0)
              @if($nilai->acc_status_id == 1)
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="fa fa-check"></i></strong> Nilai ini telah disetujui oleh {{ Auth::user()->pegawai->is($nilai->accPegawai) ? 'Anda' : $nilai->accPegawai->name }} pada {{ date('j M Y H.i.s', strtotime($nilai->acc_time)) }}
              </div>
              @endif
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek dan Indikator Kinerja Utama</th>
                      <th style="min-width: 80px">Skor</th>
                      <th>Bobot</th>
                      <th>Nilai</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if($nilai->acc_status_id != 1)
                    @php
                    $level = null;
                    @endphp
                    @if($indicators->count() > 0)
                    @php
                    for($i=1;$i<=$indicators->max('level');$i++){
                      $no[$i] = 1;
                    }
                    @endphp
                    @foreach($indicators as $i)
                    @php
                    if(!$level) $level = $i['level'];
                    elseif($level == $i['level']) $no[$i['level']]++;
                    elseif($level != $i['level']){
                      if(($level > $i['level']) && ($i['level'] >= 1)){
                        $no[$level] = 1;
                        $no[$i['level']]++;
                      }
                      $level = $i['level'];
                    }
                    @endphp
                    <tr>
                      @php
                      $number = null;
                      for($j=$i['level'];$j>0;$j--){
                        if($j == $i['level']){
                          $number = $no[$j];
                        }
                        else{
                          $number = $no[$j].'.'.$number;
                        }
                      }
                      @endphp
                      <td>{{ $number }}</td>
                      @php $item = (object)$i @endphp
                      <td class="{{ $i->level == 1 ? 'font-weight-bold' : '' }}">{{ $i->name }}</td>
                      <td>
                        @php
                        $nilaiIndikator = $nilai->detail()->where('indicator_id',$i->id)->first();
                        @endphp
                        {{ $nilaiIndikator ? $nilaiIndikator->score : '0' }}
                      </td>
                      <td>
                        @php
                        $thisPercentage = null;
                        $isDetailPercentage = false;
                        @endphp
                        @if($item->target()->where('position_id',$jabatan->id)->count() > 0)
                        @php
                        $thisPercentage = $item->target()->select('id','percentage')->where('position_id',$jabatan->id)->first();
                        $isDetailPercentage = true;
                        @endphp
                        {{ $thisPercentage->percentage }}%
                        @else
                        {{ $i->percentage }}%
                        @endif
                      </td>
                      <td>
                        @php
                        $percentage = $isDetailPercentage ? ($thisPercentage->percentage/100) : ($i->percentage/100);
                        @endphp
                        {{ $nilaiIndikator ? ($nilaiIndikator->score * $percentage) : '0' }}
                      </td>
                    </tr>
                    @endforeach
                    @endif
                    @else
                    @php
                    $kodeIndikator = $nilai->detail()->pluck('code')->unique()->toArray();
                    natsort($kodeIndikator);
                    @endphp
                    @foreach($kodeIndikator as $k)
                    @php
                    $n = $nilai->detail()->where('code',$k)->first();
                    @endphp
                    <tr>
                      <td>{{ $n->code }}</td>
                      <td class="{{ $n->indikator->level == 1 ? 'font-weight-bold' : '' }}">{{ $n->indikator->name }}</td>
                      <td>{{ $n->score }}</td>
                      <td>{{ $n->percentage }}%</td>
                      <td>{{ $n->total_score }}</td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                @if($nilai && $indicators && ($nilai->acc_status_id != 1))
                @if($nilai->detail()->count() >= $indicators->count())
                <div class="row">
                  <div class="col-12">
                    <div class="text-center">
                      <button class="btn btn-success" type="button" data-toggle="modal" data-target="#saveAccept">Setujui</button>
                    </div>
                  </div>
                </div>
                @elseif($nilai->detail()->count() > 0)
                <div class="row">
                  <div class="col-12">
                    <div class="text-center">
                      <button class="btn btn-secondary" type="button" disabled="disabled">Setujui</button>
                    </div>
                  </div>
                </div>
                @endif
                @endif
              </div>
              @else
              <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                  <h6 class="font-weight-light mb-3">Tidak ada data nilai PSC pegawai yang ditemukan</h6>
              </div>
              <div class="card-footer"></div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

@if($nilai && $indicators && ($nilai->acc_status_id != 1) && ($nilai->detail()->count() >= $indicators->count()))
<div class="modal fade" id="saveAccept" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
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
        Apakah Anda yakin ingin menyetujui semua skor yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('psc.penilaian.validator.validasi',['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'pegawai' => $pegawaiAktif->nip]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" id="saveAcceptBtn" class="btn btn-success" data-form="psc-form">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.modal.post_save_accept')
=======
@extends('template.main.master')

@section('title')
PSC Pegawai
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@php
$role = Auth::user()->role->name;
@endphp
@if(in_array($role,['admin','am','aspv','direktur','etl','etm','fam','faspv','kepsek','pembinayys','ketuayys','wakasek']))
@include('template.sidebar.kepegawaian.'.$role)
@else
@include('template.sidebar.kepegawaian.employee')
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">PSC Pegawai</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index') }}">PSC Pegawai</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.index', ['tahun' => $tahun->academicYearLink]) }}">{{ $tahun->academic_year }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.penilaian.penilai.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}">{{ $unitAktif->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $pegawaiAktif->name }}</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nama</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $pegawaiAktif->name }}
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
                  <label class="form-control-label">Tahun Pelajaran</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $tahun->academicYearLink }}
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
                  <label class="form-control-label">Unit</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $unitAktif->name }}
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
                  <label class="form-control-label">Jabatan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $jabatan->name }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route('psc.penilaian.validator.index', ['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name]) }}" class="btn btn-sm btn-light">Kembali</a>
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
                        <div class="icon-circle bg-{{ $nilai && $nilai->total_score ? 'brand-green' : 'secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Jumlah Nilai</div>
                        <h6 class="mb-0">{{ $nilai && $nilai->total_score ? $nilai->total_score : '-' }}</h6>
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
                        <div class="icon-circle bg-{{ $nilai && $nilai->grade_name ? 'brand-green' : 'secondary' }}">
                          <i class="fas fa-equals text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Grade</div>
                        <h6 class="mb-0">{{ $nilai && $nilai->grade_name ? $nilai->grade_name : '-' }}</h6>
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
                <h6 class="m-0 font-weight-bold text-brand-green">PSC Pegawai</h6>
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
              @if($nilai && $nilai->detail()->count() > 0)
              @if($nilai->acc_status_id == 1)
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong><i class="fa fa-check"></i></strong> Nilai ini telah disetujui oleh {{ Auth::user()->pegawai->is($nilai->accPegawai) ? 'Anda' : $nilai->accPegawai->name }} pada {{ date('j M Y H.i.s', strtotime($nilai->acc_time)) }}
              </div>
              @endif
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek dan Indikator Kinerja Utama</th>
                      <th style="min-width: 80px">Skor</th>
                      <th>Bobot</th>
                      <th>Nilai</th>
                    </tr>
                  </thead>
                  <tbody>
                    @if($nilai->acc_status_id != 1)
                    @php
                    $level = null;
                    @endphp
                    @if($indicators->count() > 0)
                    @php
                    for($i=1;$i<=$indicators->max('level');$i++){
                      $no[$i] = 1;
                    }
                    @endphp
                    @foreach($indicators as $i)
                    @php
                    if(!$level) $level = $i['level'];
                    elseif($level == $i['level']) $no[$i['level']]++;
                    elseif($level != $i['level']){
                      if(($level > $i['level']) && ($i['level'] >= 1)){
                        $no[$level] = 1;
                        $no[$i['level']]++;
                      }
                      $level = $i['level'];
                    }
                    @endphp
                    <tr>
                      @php
                      $number = null;
                      for($j=$i['level'];$j>0;$j--){
                        if($j == $i['level']){
                          $number = $no[$j];
                        }
                        else{
                          $number = $no[$j].'.'.$number;
                        }
                      }
                      @endphp
                      <td>{{ $number }}</td>
                      @php $item = (object)$i @endphp
                      <td class="{{ $i->level == 1 ? 'font-weight-bold' : '' }}">{{ $i->name }}</td>
                      <td>
                        @php
                        $nilaiIndikator = $nilai->detail()->where('indicator_id',$i->id)->first();
                        @endphp
                        {{ $nilaiIndikator ? $nilaiIndikator->score : '0' }}
                      </td>
                      <td>
                        @php
                        $thisPercentage = null;
                        $isDetailPercentage = false;
                        @endphp
                        @if($item->target()->where('position_id',$jabatan->id)->count() > 0)
                        @php
                        $thisPercentage = $item->target()->select('id','percentage')->where('position_id',$jabatan->id)->first();
                        $isDetailPercentage = true;
                        @endphp
                        {{ $thisPercentage->percentage }}%
                        @else
                        {{ $i->percentage }}%
                        @endif
                      </td>
                      <td>
                        @php
                        $percentage = $isDetailPercentage ? ($thisPercentage->percentage/100) : ($i->percentage/100);
                        @endphp
                        {{ $nilaiIndikator ? ($nilaiIndikator->score * $percentage) : '0' }}
                      </td>
                    </tr>
                    @endforeach
                    @endif
                    @else
                    @php
                    $kodeIndikator = $nilai->detail()->pluck('code')->unique()->toArray();
                    natsort($kodeIndikator);
                    @endphp
                    @foreach($kodeIndikator as $k)
                    @php
                    $n = $nilai->detail()->where('code',$k)->first();
                    @endphp
                    <tr>
                      <td>{{ $n->code }}</td>
                      <td class="{{ $n->indikator->level == 1 ? 'font-weight-bold' : '' }}">{{ $n->indikator->name }}</td>
                      <td>{{ $n->score }}</td>
                      <td>{{ $n->percentage }}%</td>
                      <td>{{ $n->total_score }}</td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
              <div class="card-footer">
                @if($nilai && $indicators && ($nilai->acc_status_id != 1))
                @if($nilai->detail()->count() >= $indicators->count())
                <div class="row">
                  <div class="col-12">
                    <div class="text-center">
                      <button class="btn btn-success" type="button" data-toggle="modal" data-target="#saveAccept">Setujui</button>
                    </div>
                  </div>
                </div>
                @elseif($nilai->detail()->count() > 0)
                <div class="row">
                  <div class="col-12">
                    <div class="text-center">
                      <button class="btn btn-secondary" type="button" disabled="disabled">Setujui</button>
                    </div>
                  </div>
                </div>
                @endif
                @endif
              </div>
              @else
              <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                  <h6 class="font-weight-light mb-3">Tidak ada data nilai PSC pegawai yang ditemukan</h6>
              </div>
              <div class="card-footer"></div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

@if($nilai && $indicators && ($nilai->acc_status_id != 1) && ($nilai->detail()->count() >= $indicators->count()))
<div class="modal fade" id="saveAccept" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
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
        Apakah Anda yakin ingin menyetujui semua skor yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('psc.penilaian.validator.validasi',['tahun' => $tahun->academicYearLink, 'unit' => $unitAktif->name, 'pegawai' => $pegawaiAktif->nip]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" id="saveAcceptBtn" class="btn btn-success" data-form="psc-form">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.modal.post_save_accept')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection