@extends('template.main.master')

@section('title')
RPPA
@endsection

@section('headmeta')
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
    @if($jenisAktif)
    <li class="breadcrumb-item"><a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ !$isYear ? $tahun->academic_year : $tahun }}</li>
    @endif
  </ol>
</div>
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
    @php
    if(!in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
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
                    <a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('lppa.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
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
                <h6 class="m-0 font-weight-bold text-brand-green">Anggaran Tersedia</h6>
            </div>
            <div class="card-body p-3">
                @if(count($jenisAktif->anggaran) > 0)
                <div class="row">
					<div class="col-12">
                        <div class="row ml-1">
					@php
                    $anggarans = $jenisAktif->anggaran()->whereHas('tahuns',function($q)use($yearAttr,$tahun){
                        $q->where($yearAttr,($yearAttr == 'year' ? $tahun : $tahun->id));
                    })->whereHas('ppa',function($q)use($yearAttr,$tahun){
						$q->where([$yearAttr=>($yearAttr == 'year' ? $tahun : $tahun->id),'director_acc_status_id'=>1])->has('lppa');
					});
					if(!in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
						if(Auth::user()->pegawai->unit_id == '5'){
							$anggarans = $anggarans->whereHas('anggaran',function($q){
								$q->where('position_id',Auth::user()->pegawai->jabatan->group()->first()->id);
							});
						}
						else{
							$anggarans = $anggarans->whereHas('anggaran',function($q){
								$q->where('unit_id',Auth::user()->pegawai->unit_id);
							});
						}
					}
					$anggarans = $anggarans->get();
					$kategoriAktifs = [];
					@endphp
					@if($kategori)
					@php
					foreach($kategori as $k){
						$anggaranKategori[$k->id] = $anggarans->whereIn('budgeting_id',$k->anggarans->pluck('id'));
						if($anggaranKategori[$k->id]->count() > 0){
							array_push($kategoriAktifs,$k->id);
						}
					}
					@endphp
					@foreach($kategori->whereIn('id',$kategoriAktifs)->take(1) as $k)
					<!--<a data-toggle="collapse" href="#collapse{{$k->name}}" role="button" aria-expanded="true" aria-controls="collapse{{$k->name}}" class="btn btn-brand-green btn-block btn-sm py-2 with-chevron">
					  <p class="d-flex align-items-center justify-content-between mb-0 px-3 py-2"><strong class="text-uppercase">{{$k->name}}</strong><i class="fa fa-angle-down"></i></p>
					</a>
					<div id="collapse{{$k->name}}" class="collapse mt-3 show">
						<div class="row ml-1"> -->
					@foreach($anggarans as $a)
                    <div class="col-md-6 col-12 mb-3">
                        <div class="row py-2 rounded border border-light mr-2">
                            <div class="col-8 d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="icon-circle bg-gray-500" data-toggle="tooltip" data-placement="bottom" data-original-title="{{ $a->anggaran->name }}">
                                        <i class="fas fa-money-check text-white"></i>
                                    </div>
                                </div>
                                <div class="d-none d-sm-block">
                                    <a class="font-weight-bold text-dark" href="{{ route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $a->anggaran->link])}}">{{ $a->anggaran->name }}</a>
                                </div>
                            </div>
                            <div class="col-4 d-flex justify-content-end align-items-center">
                                <a href="{{ route('lppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $a->anggaran->link])}}" class="btn btn-sm btn-outline-brand-green-dark">Pilih</a>
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
                    @if(!$anggarans || count($anggarans) < 1)
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
@endif
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@endsection