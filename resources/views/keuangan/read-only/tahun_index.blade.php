<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Tahun Anggaran
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Tahun Anggaran</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tahun-anggaran.index')}}">Tahun Anggaran</a></li>
    @if($jenisAktif)
    <li class="breadcrumb-item"><a href="{{ route('tahun-anggaran.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ !$isYear ? $tahun->academic_year : $tahun }}</li>
    @endif
  </ol>
</div>
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
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
                        <a href="{{ route('tahun-anggaran.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                      @if((!$academicYears && !$isYear) || ($academicYears && count($academicYears) > 0))
                      @foreach($tahunPelajaran as $t)
                      <option value="{{ $t->academicYearLink }}" {{ !$isYear && $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                      @endif
                    </select>
                    <a href="{{ route('tahun-anggaran.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('tahun-anggaran.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
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
                <h6 class="m-0 font-weight-bold text-brand-green">Status Anggaran</h6>
                @if(in_array(Auth::user()->role->name,['ketuayys']) && count($apby) > 0)
                <div class="m-0 float-right">
                    @if(!$isYear)
                    @if(in_array(Auth::user()->role->name,['ketuayys']) && $changeYear && $nextYear)
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#tutupModal">Tutup Tahun <i class="fas fa-book ml-1"></i></button>
                    @else
                    <button type="button" class="btn btn-secondary btn-sm" disabled>Tutup Tahun <i class="fas fa-book ml-1"></i></button>
                    @endif
                    @endif
                </div>
                @endif
            </div>
            <div class="card-body p-3">
                @if(count($apby) > 0 && in_array(Auth::user()->role->name,['ketuayys']) && $changeYear && !$nextYear)
                <div class="alert alert-light alert-dismissible fade show" role="alert">
                  <i class="fa fa-info-circle text-info mr-2"></i>Untuk dapat melakukan tutup tahun {{ $jenisAktif->name }}, mohon pastikan ketersediaan tahun pelajaran
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
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
                @php
                $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->whereHas('apby',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->aktif();
                })->get();
                @endphp
                @if($anggarans && count($anggarans) > 0)
                <div class="row">
					<div class="col-12">
                        <div class="row ml-1">
					@foreach($anggarans as $a)
					@php
					$thisApby = $a->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->first();
					@endphp
                    <div class="col-md-6 col-12 mb-3">
                        <div class="row py-2 rounded border border-light mr-2">
                            <div class="col-8 d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="icon-circle bg-gray-500">
                                        <i class="fas fa-money-check text-white"></i>
                                    </div>
                                </div>
                                <div>
                                  <span class="font-weight-bold text-dark" >{{ $a->anggaran->name }}</span>
                                </div>
                            </div>
                            <div class="col-4 d-flex justify-content-end align-items-center">
                              @if($thisApby->is_final == 1)
                                <span class="badge badge-pill badge-success">Final</span>
                              @else
                                <span class="badge badge-pill badge-info">Aktif</span>
                              @endif
                            </div>
                        </div>
                    </div>
					@endforeach
						</div>
                    </div>
                </div>
                @else
                <div class="text-center mx-3 my-5">
                    <h3 class="text-center">Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data anggaran yang ditemukan</h6>
                </div>
                @endif
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
</div>

@if(count($apby) > 0 && in_array(Auth::user()->role->name,['ketuayys']) && $changeYear && $nextYear)
<div class="modal fade" id="tutupModal" tabindex="-1" role="dialog" aria-labelledby="tutupModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-warning">
          <i class="material-icons text-warning">&#xE865;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menutup tahun {{ $jenisAktif->name }}?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('tahun-anggaran.tutup', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}" method="post">
          {{ csrf_field() }}
          <button type="submit" class="btn btn-warning">Ya, Tutup</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

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
Tahun Anggaran
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Tahun Anggaran</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('tahun-anggaran.index')}}">Tahun Anggaran</a></li>
    @if($jenisAktif)
    <li class="breadcrumb-item"><a href="{{ route('tahun-anggaran.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ !$isYear ? $tahun->academic_year : $tahun }}</li>
    @endif
  </ol>
</div>
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
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
                        <a href="{{ route('tahun-anggaran.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                      @if((!$academicYears && !$isYear) || ($academicYears && count($academicYears) > 0))
                      @foreach($tahunPelajaran as $t)
                      <option value="{{ $t->academicYearLink }}" {{ !$isYear && $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                      @endif
                    </select>
                    <a href="{{ route('tahun-anggaran.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('tahun-anggaran.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
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
                <h6 class="m-0 font-weight-bold text-brand-green">Status Anggaran</h6>
                @if(in_array(Auth::user()->role->name,['ketuayys']) && count($apby) > 0)
                <div class="m-0 float-right">
                    @if(!$isYear)
                    @if(in_array(Auth::user()->role->name,['ketuayys']) && $changeYear && $nextYear)
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#tutupModal">Tutup Tahun <i class="fas fa-book ml-1"></i></button>
                    @else
                    <button type="button" class="btn btn-secondary btn-sm" disabled>Tutup Tahun <i class="fas fa-book ml-1"></i></button>
                    @endif
                    @endif
                </div>
                @endif
            </div>
            <div class="card-body p-3">
                @if(count($apby) > 0 && in_array(Auth::user()->role->name,['ketuayys']) && $changeYear && !$nextYear)
                <div class="alert alert-light alert-dismissible fade show" role="alert">
                  <i class="fa fa-info-circle text-info mr-2"></i>Untuk dapat melakukan tutup tahun {{ $jenisAktif->name }}, mohon pastikan ketersediaan tahun pelajaran
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
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
                @php
                $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                })->whereHas('apby',function($q)use($yearAttr,$tahun){
                    $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->aktif();
                })->get();
                @endphp
                @if($anggarans && count($anggarans) > 0)
                <div class="row">
					<div class="col-12">
                        <div class="row ml-1">
					@foreach($anggarans as $a)
					@php
					$thisApby = $a->apby()->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id))->aktif()->first();
					@endphp
                    <div class="col-md-6 col-12 mb-3">
                        <div class="row py-2 rounded border border-light mr-2">
                            <div class="col-8 d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="icon-circle bg-gray-500">
                                        <i class="fas fa-money-check text-white"></i>
                                    </div>
                                </div>
                                <div>
                                  <span class="font-weight-bold text-dark" >{{ $a->anggaran->name }}</span>
                                </div>
                            </div>
                            <div class="col-4 d-flex justify-content-end align-items-center">
                              @if($thisApby->is_final == 1)
                                <span class="badge badge-pill badge-success">Final</span>
                              @else
                                <span class="badge badge-pill badge-info">Aktif</span>
                              @endif
                            </div>
                        </div>
                    </div>
					@endforeach
						</div>
                    </div>
                </div>
                @else
                <div class="text-center mx-3 my-5">
                    <h3 class="text-center">Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data anggaran yang ditemukan</h6>
                </div>
                @endif
                <div class="card-footer"></div>
            </div>
        </div>
    </div>
</div>

@if(count($apby) > 0 && in_array(Auth::user()->role->name,['ketuayys']) && $changeYear && $nextYear)
<div class="modal fade" id="tutupModal" tabindex="-1" role="dialog" aria-labelledby="tutupModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-warning">
          <i class="material-icons text-warning">&#xE865;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menutup tahun {{ $jenisAktif->name }}?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('tahun-anggaran.tutup', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}" method="post">
          {{ csrf_field() }}
          <button type="submit" class="btn btn-warning">Ya, Tutup</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@endif
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection