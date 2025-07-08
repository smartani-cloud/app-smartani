@extends('template.main.master')

@section('title')
PPA
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">PPA</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppa.index')}}">PPA</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppa.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ppa.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">{{ !$isYear ? $tahun->academic_year : $tahun }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $anggaranAktif->anggaran->name }}</li>
  </ol>
</div>
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
    @php
    $checkAccAttr = $j->isKso ? 'director_acc_status_id' : 'president_acc_status_id';
    if(!in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv'])){
        if(Auth::user()->pegawai->unit_id == '5'){
            $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q)use($checkAccAttr){$q->where('position_id',Auth::user()->pegawai->jabatan->group()->first()->id);})->whereHas('apby',function($q)use($checkAccAttr){$q->where($checkAccAttr,1);})->count();
        }
        else{
            $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q)use($checkAccAttr){$q->where('unit_id',Auth::user()->pegawai->unit_id);})->whereHas('apby',function($q)use($checkAccAttr){$q->where($checkAccAttr,1);})->count();
        }
    }
    else{
        $anggaranCount = $j->anggaran()->whereHas('apby',function($q)use($checkAccAttr){$q->where($checkAccAttr,1);})->count();
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
                        <a href="{{ route('ppa.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
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
                      @if(!in_array(date('Y'),$years->toArray()) && $jenisAktif->is_academic_year == 0)
                      <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                      @elseif($isYear || (!$isYear && $jenisAktif->is_academic_year == 0))
                      @if($tahun != date('Y'))
                      <option value="" disabled="disabled" selected>Pilih</option>
                      @endif
                      <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                      @if(((!$academicYears || ($academicYears && count($academicYears) < 1)) && $jenisAktif->is_academic_year == 1) || ($academicYears && count($academicYears) > 0))
                      @foreach($tahunPelajaran as $t)
                      <option value="{{ $t->academicYearLink }}" {{ !$isYear && $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                      @endif
                    </select>
                    <a href="{{ route('ppa.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('ppa.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
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

@yield('cards')

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
                @yield('buttons')
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
                @if(count($ppa) > 0)
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th style="white-space: nowrap">Nomor</th>
                                <th>Status</th>
                                <th>Diajukan</th>
                                <th>Diperiksa</th>
                                <th>Disetujui</th>
                                <th style="width: 120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 1;
                            @endphp
                            @foreach($ppa->sortByDesc('id')->all() as $p)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $p->date ? $p->date : '-' }}</td>
                                <td>{{ $p->number ? $p->number : '-' }}</td>
                                <td>
                                    @if($p->detail()->count() < 1)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada rincian akun anggaran yang dimasukkan untuk pengajuan ini"></i>
                                    @elseif($p->eksklusi)
                                    <i class="fa fa-lg fa-exclamation-circle text-warning" data-toggle="tooltip" data-html="true" data-original-title="Dieksklusi<br>{{ date('d M Y H.i.s', strtotime($p->eksklusi->created_at)) }}"></i>
                                    @elseif(!$p->pa_acc_status_id)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == $anggaranAktif->anggaran->acc_position_id ? 'Anda' : $anggaranAktif->anggaran->accJabatan->name }}"></i>
                                    @elseif($p->pa_acc_status_id == 1 && !$p->finance_acc_status_id)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == 33 ? 'Anda' : 'Kepala Divisi Umum' }}"></i>
                                    @elseif($p->finance_acc_status_id == 1 && !$p->director_acc_status_id)
                                    <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($p->accKeuangan) ? 'Anda' : $p->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($p->finance_acc_time)) }}"></i>
                                    @elseif($p->director_acc_status_id == 1)
                                    <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($p->accDirektur) ? 'Anda' : $p->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($p->director_acc_time)) }}"></i>
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @if($p->lppaRef && $p->detail()->whereNull('value_pa')->count() > 0)
                                    {{ number_format($p->detail()->sum('value'), 0, ',', '.') }}
                                    @else
                                    @if($p->pa_acc_status_id == 1)
                                    {{ number_format($p->detail()->sum('value_pa'), 0, ',', '.') }}
                                    @else
                                    {{ number_format($p->detail()->sum('value'), 0, ',', '.') }}
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    @if($p->finance_acc_status_id == 1)
                                    {{ number_format($p->detail()->sum('value_fam'), 0, ',', '.') }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @php
                                    $value = explode('_',$accAttr)[0];
                                    @endphp
                                    @if($p->bbk && $p->bbk->bbk->{$accAttr} == 1)
                                    @if($p->detail()->whereNull('value_'.$value)->count() > 0)
                                    @if($p->lppa && $p->lppa->finance_acc_status_id == 1 && $p->lppa->detail()->count() > 0 && ($p->lppa->detail()->sum('value') != $p->detail()->sum('value')))
                                    {{ number_format($p->lppa->detail()->sum('value'), 0, ',', '.') }}
                                    @else
                                    {{ number_format($p->detail()->sum('value'), 0, ',', '.') }}
                                    @endif
                                    @else
                                    @if($p->lppa && $p->lppa->finance_acc_status_id == 1 && $p->lppa->detail()->count() > 0 && ($p->lppa->detail()->sum('value') != $p->detail()->sum('value_'.$value)))
                                    {{ number_format($p->lppa->detail()->sum('value'), 0, ',', '.') }}
                                    @else
                                    {{ number_format($p->detail()->sum('value_'.$value), 0, ',', '.') }}
                                    @endif
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
									<a href="{{ route(($p->is_draft == 1 ? 'ppa.draft' : 'ppa.show'), ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $p->firstNumber]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
									@if(in_array(Auth::user()->role->name,['am']) && (($p->finance_acc_status_id == 1 && $p->total_value <= 0) || ($p->finance_acc_status_id != 1 && $p->detail()->sum('value') <= 0)) && $p->detail()->count() > 0 && !$p->eksklusi && !$p->lppa)
                                    <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#exclude-confirm" onclick="excludeModal('PPA', '{{ addslashes(htmlspecialchars('PPA No. '.$p->number)) }}', '{{ route('ppa.eksklusi', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $p->firstNumber, 'submitted' => $p->is_draft == 1 ? null : '1']) }}')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @endif
									@if($isPa && $p->is_draft == 1)
									<a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('PPA', '{{ addslashes(htmlspecialchars('PPA No. '.$p->number)) }}', '{{ route('ppa.destroy', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $p->firstNumber]) }}')">
                                        <i class="fas fa-trash"></i>
                                    </a>
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
                    <h6 class="font-weight-light mb-3">Tidak ada data pengajuan yang ditemukan</h6>
                </div>
                @endif
                <div class="card-footer"></div>
        </div>
    </div>
</div>
@endif
<!--Row-->

@if((($isYear && $tahun == date('Y')) || (!$isYear && $tahun->is_finance_year == 1)) && $isAnggotaPa && $apbyAktif && $apbyAktif->is_active == 1 && $creatable)
<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createModalLabel">Buat Pengajuan</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        Pilih jenis proposal yang ingin Anda buat?
        <div class="row">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="social-card">
                    <div class="box-part text-center">
                        <i class="far fa-lightbulb fa-2x text-brand-green"></i>
                        <div class="title mt-2">
                            <h4>Normal</h4>
                        </div>
                        <div class="text">
                            <span>Buat pengajuan PPA baru dari awal.</span>
                        </div>
                        <a class="btn btn-outline-brand-green-dark" href="{{ route('ppa.buat', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}">Buat <i class="fas fa-chevron-right ml-1"></i></a>
                        
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="social-card">
                    <div class="box-part text-center">
                        <i class="far fa-comment fa-2x text-brand-green"></i>
                        <div class="title mt-2">
                            <h4>Proposal</h4>
                        </div>
                        <div class="text">
                            <span>Buat pengajuan PPA dari proposal PPA.</span>
                        </div>
                        <a class="btn btn-outline-brand-green-dark" href="{{ route('ppa.buat', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'type' => 'proposal']) }}">Buat <i class="fas fa-chevron-right ml-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
      </div>
    </div>
  </div>
</div>
@endif
@if($isPa && $ppa->where('is_draft',1)->count() > 0)
@include('template.modal.konfirmasi_hapus')
@endif
@if(in_array(Auth::user()->role->name,['am']))
@include('template.modal.konfirmasi_eksklusi')
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@if($isPa && $ppa->where('is_draft',1)->count() > 0)
@include('template.footjs.modal.get_delete')
@endif
@if(in_array(Auth::user()->role->name,['am']))
@include('template.footjs.modal.get_exclude')
@endif
@endsection