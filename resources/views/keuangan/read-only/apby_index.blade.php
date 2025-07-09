@extends('template.main.master')

@section('title')
APBY
@endsection

@section('headmeta')
<style>
.nav-pills .nav-link:not(.active){
    color: #a7248c!important;
}
</style>
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">APBY</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('apby.index')}}">APBY</a></li>
    @if($jenisAktif)
    <li class="breadcrumb-item"><a href="{{ route('apby.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
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
                        <a href="{{ route('apby.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
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
                    <a href="{{ route('apby.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('apby.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
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
            <ul class="nav nav-pills p-3" id="pills-tab" role="tablist">
              <li class="nav-item" role="presentation">
                <a href="javascript:void(0)" class="nav-link active" id="pills-home-tab" data-toggle="pill" data-target="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">APBY</a>
              </li>
              <li class="nav-item" role="presentation">
                <a href="javascript:void(0)" class="nav-link" id="pills-detail-tab" data-toggle="pill" data-target="#pills-detail" role="tab" aria-controls="pills-detail" aria-selected="false">Rincian APBY</a>
              </li>
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Akun Anggaran</h6>
                @if(in_array(Auth::user()->role->name,['ketuayys','direktur','faspv','am']) && count($apby) > 0)
                <div class="m-0 float-right">
                    @if($sumExportable)
                    <a class="btn btn-brand-green-dark btn-sm" href="{{ route('apby.ekspor', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'status' => 'sum'])}}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                    @else
                    <button type="button" class="btn btn-secondary btn-sm" disabled>Ekspor <i class="fas fa-file-export ml-1"></i></a>
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
            @if($anggaransQuery->count() > 0)
                @if($anggarans && count($anggarans) > 0)
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th style="white-space: nowrap">No Akun</th>
                                <th>Nama Akun</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($accounts && $accounts->count() > 0)
                            @foreach($accounts->groupBy('id') as $d)
                            @php
                            $data = $d->first();
                            $value = null;
                            if(count($d) > 0){
                                $value = $d->sum('value');
                            }
                            @endphp
                            <tr>
                                <td>{{ $data['code'] }}</td>
                                <td>{{ $data['name'] }}</td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="{{ $value ? number_format($value, 0, ',', '.') : $data['valueWithSeparator'] }}" disabled>
                                </td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center mx-3 my-5">
                    <h3 class="text-center">Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data akun anggaran yang ditemukan</h6>
                </div>
                @endif
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data anggaran yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
            </div>
            <div class="tab-pane fade" id="pills-detail" role="tabpanel" aria-labelledby="pills-detail-tab">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Anggaran Tersedia</h6>
                @if(in_array(Auth::user()->role->name,['ketuayys','direktur','faspv','am']) && count($apby) > 0)
                <div class="m-0 float-right">
                    @if(in_array(Auth::user()->role->name,['am','faspv']))
                    @if($perubahan)
                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#perubahanModal">APB Perubahan <i class="fas fa-pen-alt ml-1"></i></button>
                    @else
                    <button type="button" class="btn btn-secondary btn-sm" disabled>APB Perubahan <i class="fas fa-pen-alt ml-1"></i></button>
                    @endif
                    @endif
                    @if($checkApby && $checkApby->count() > 0)
                    <a class="btn btn-brand-green-dark btn-sm" href="{{ route('apby.ekspor', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                    @else
                    <button type="button" class="btn btn-secondary btn-sm" disabled>Ekspor <i class="fas fa-file-export ml-1"></i></a>
                    @endif
                </div>
                @endif
            </div>
            <div class="card-body p-3">
                @if(count($apby) > 0 && in_array(Auth::user()->role->name,['am','faspv']) && !$perubahan)
                <div class="alert alert-light alert-dismissible fade show" role="alert">
                  <i class="fa fa-info-circle text-info mr-2"></i>Untuk dapat melakukan APB Perubahan, pastikan tidak ada PPA, PPB, maupun RPPA yang belum selesai prosesnya
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
                @php
                $anggaranAktif = 0;
                @endphp
                @if(count($jenisAktif->anggaran) > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="row ml-1">
                    @php
                    $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    })->get();
                    $anggaranAktifs = $kategoriAktifs = [];
                    foreach($anggarans as $a){
                        $apbyCount[$a->id] = !$isYear ? $a->apby()->where('academic_year_id',$tahun->id)->aktif()->count() : $a->apby()->where('year',$tahun)->aktif()->count();
                        $anggaranAktif += $apbyCount[$a->id] > 0 ? 1 : 0;
                        if(($isYear && (($tahun != date('Y') && $apbyCount[$a->id] > 0) || $tahun == date('Y'))) || (!$isYear && (($tahun->is_finance_year != 1 && $apbyCount[$a->id] > 0) || $tahun->is_finance_year == 1))){
                            array_push($anggaranAktifs,$a->id);
                        }
                    }
                    @endphp
                    @if($kategoriAnggaran)
                    @php
                    foreach($kategoriAnggaran as $k){
                        $anggaranKategori[$k->id] = $anggarans->whereIn('budgeting_id',$k->anggarans->pluck('id'));
                        if($anggaranKategori[$k->id]->count() > 0){
                            array_push($kategoriAktifs,$k->id);
                        }
                    }
                    @endphp
                    @foreach($kategoriAnggaran->take(1) as $k)
                    <!-- <a data-toggle="collapse" href="#collapse{{$k->name}}" role="button" aria-expanded="true" aria-controls="collapse{{$k->name}}" class="btn btn-brand-green btn-block btn-sm py-2 with-chevron">
                      <p class="d-flex align-items-center justify-content-between mb-0 px-3 py-2"><strong class="text-uppercase">{{$k->name}}</strong><i class="fa fa-angle-down"></i></p>
                    </a>
                    <div id="collapse{{$k->name}}" class="collapse mt-3 show">
                        <div class="row ml-1"> -->
                    @foreach($anggarans->whereIn('id',$anggaranAktifs) as $a)
                    <div class="col-md-6 col-12 mb-3">
                        <div class="row py-2 rounded border border-light mr-2">
                            <div class="col-8 d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="icon-circle bg-gray-500" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $a->anggaran->name }}">
                                        <i class="fas fa-money-check text-white"></i>
                                    </div>
                                </div>
                                <div class="d-none d-sm-block">
                                  @if($apbyCount[$a->id] > 0)
                                    <a class="font-weight-bold text-dark" href="{{ route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $a->anggaran->link])}}">{{ $a->anggaran->name }}</a>
                                  @else
                                    <span class="font-weight-bold text-dark" >{{ $a->anggaran->name }}</span>
                                  @endif
                                </div>
                            </div>
                            <div class="col-4 d-flex justify-content-end align-items-center">
                              @if($apbyCount[$a->id] > 0)
                                <a href="{{ route('apby.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $a->anggaran->link])}}" class="btn btn-sm btn-outline-brand-green-dark">Pilih</a>
                              @else
                                <button type="button" class="btn btn-sm btn-light" disabled="">Pilih</button>
                              @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                        <!-- </div>
                    </div> -->
                    @endforeach
                    @endif
                        </div>
                    </div>
                    @if((($isYear && $tahun != date('Y')) || (!$isYear && $tahun->is_finance_year != 1)) && $anggaranAktif == 0)
                    <div class="col-12 pl-0 pr-3">
                        <div class="text-center mx-3 mt-4 mb-5">
                            <h3>Mohon Maaf,</h3>
                            <h6 class="font-weight-light mb-3">Tidak ada data anggaran yang ditemukan</h6>
                        </div>
                    </div>
                    @endif
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
    </div>
</div>

@if(count($apby) > 0 && in_array(Auth::user()->role->name,['am','faspv']) && $perubahan)
<div class="modal fade" id="perubahanModal" tabindex="-1" role="dialog" aria-labelledby="perubahanModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-warning">
          <i class="material-icons text-warning">&#xE3C9;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin mengubah seluruh {{ $jenisAktif->name }} yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('apby.perubahan', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-warning">Ya, Ubah</button>
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
@endsection