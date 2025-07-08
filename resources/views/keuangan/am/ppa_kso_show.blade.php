@extends('template.main.master')

@section('title')
PPA
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
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
    <li class="breadcrumb-item"><a href="{{ route('ppa.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link])}}">{{ $anggaranAktif->anggaran->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $ppaAktif->firstNumber }}</li>
  </ol>
</div>

<div class="row">
    @foreach($jenisAnggaran as $j)
    @php
    if(!in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur','fam','faspv','am'])){
        if(Auth::user()->pegawai->unit_id == '5'){
            if($j->isKso){
                $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q){$q->where('position_id',Auth::user()->pegawai->jabatan->group()->first()->id);})->whereHas('apby',function($q){$q->where('director_acc_status_id',1);})->count();
            }
            else{
                $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q){$q->where('position_id',Auth::user()->pegawai->jabatan->group()->first()->id);})->whereHas('apby',function($q){$q->where('president_acc_status_id',1);})->count();
            }
        }
        else{
            if($j->isKso){
                $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q){$q->where('unit_id',Auth::user()->pegawai->unit_id);})->whereHas('apby',function($q){$q->where('director_acc_status_id',1);})->count();
            }
            else{
                $anggaranCount = $j->anggaran()->whereHas('anggaran',function($q){$q->where('unit_id',Auth::user()->pegawai->unit_id);})->whereHas('apby',function($q){$q->where('president_acc_status_id',1);})->count();
            }
            
        }
    }
    else{
        if($j->isKso){
            $anggaranCount = $j->anggaran()->whereHas('apby',function($q){$q->where('director_acc_status_id',1);})->count();
        }
        else{
            $anggaranCount = $j->anggaran()->whereHas('apby',function($q){$q->where('president_acc_status_id',1);})->count();
        }
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
                  <label class="form-control-label">Jenis</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $ppaAktif->type_id ? $ppaAktif->type->name : '-' }}
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
                  {{ $ppaAktif->dateId ? $ppaAktif->dateId : '-' }}
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
                  <label class="form-control-label">Nomor</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $ppaAktif->number ? $ppaAktif->number : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @if($ppaAktif->lppaRef)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor LPPA</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  <a href="{{ route('lppa.show',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->lppaRef->firstNumber]) }}" target="_blank" class="text-decoration-none text-info">{{ $ppaAktif->lppaRef->number }}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="d-flex justify-content-end">
          <a href="{{ route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}" class="btn btn-sm btn-light">Kembali</a>
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
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-money-check text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Anggaran</div>
                        <h6 class="mb-0">{{ $anggaranAktif->anggaran->name }}</h6>
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
                        <div class="icon-circle {{ $ppaAktif && $ppaAktif->detail()->count() > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Jumlah</div>
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
</div>

@if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1) && $ppaAktif->detail()->count() < 5 && !$ppaAktif->lppa_id && ($akun && $akun->where('is_fillable',1)->count() > 0))
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">Tambah Pengajuan</h6>
      </div>
      <div class="card-body pt-2 pb-3 px-4">
        <form action="{{ route('ppa.tambah', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}" id="addPpaDetailForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Account" class="form-control-label">Akun Anggaran</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('account') is-invalid @enderror" name="account" id="select2Account" required="required">
                      @foreach($akun as $a)
                      @php
                      $accAttr = $isKso ? 'director_acc_status_id' : 'president_acc_status_id';
                      $apbyBalance = null;
                      if($a->is_exclusive != 1){
                        $apbyBalance = $a->apby()->whereHas('apby',function($q)use($yearAttr,$tahun,$anggaranAktif,$accAttr){$q->where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),$accAttr => 1])->whereHas('jenisAnggaranAnggaran',function($q)use($anggaranAktif){$q->where('id',$anggaranAktif->id);})->aktif()->latest();})->where('account_id',$a->id)->first();
                      }
                      $bypassCheck = true;
                      $isFinance = in_array(Auth::user()->role->name, ['fam','faspv']) ? true : false;
                      $showBalance = $apbyBalance && $isFinance ? true : false;
                      @endphp
                      @if($a->is_fillable == 1 && ($a->is_exclusive == 1 || ($a->is_exclusive != 1 && (!$checkBalance || ($checkBalance && ($bypassCheck || (!$bypassCheck && ($apbyBalance && $apbyBalance->balance > 0))))))))
                      <option value="{{ $a->id }}" {{ old('account', $ppaAktif->detail()->count() > 1 ? $ppaAktif->detail()->latest()->first()->account_id : null) == $a->id ? 'selected' : '' }}>{{ $a->codeName.($showBalance ? ' ('.number_format($apbyBalance->balance, 0, ',', '.').')' : '') }}</option>
                      @elseif($isFinance && $a->is_fillable == 1 && ($checkBalance && ($bypassCheck || (!$bypassCheck && ($apbyBalance && $apbyBalance->balance <= 0)))))
                      <option value="" class="bg-gray-300" disabled="disabled">{{ $a->codeName.($apbyBalance ? ' ('.number_format($apbyBalance->balance, 0, ',', '.').')' : '') }}</option>
                      @else
                      <option value="" class="bg-gray-300" disabled="disabled">{{ $a->codeName }}</option>
                      @endif
                      @endforeach
                    </select>
                    @error('account')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="{{ $ppaAktif->type_id == 2 ? 'select2Proposal' : 'normal-input' }}" class="form-control-label">Keterangan</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @if($ppaAktif->type_id == 2)
                    <select class="select2-multiple form-control form-control-sm @error('proposals') is-invalid @enderror" name="proposals[]" multiple="multiple" id="select2Proposal" required="required">
                      @foreach($proposals as $p)
                      <option value="{{ $p->id }}" {{ old('proposals') && in_array($p->id,old('proposals')) ? 'selected' : '' }} data-amount="{{ $p->amount }}">{{ $p->desc.' - '.$p->amountWithSeparator.' ['.$p->pegawai->nickname.', '.$p->jabatan->name.']' }}</option>
                      @endforeach
                    </select>
                    @error('proposals')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @else
                    <textarea id="note" class="form-control form-control-sm @error('note') is-invalid @enderror" name="note" maxlength="255" rows="2" required="required"></textarea>
                    @error('note')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          @if($ppaAktif->type_id == 2)
          <div class="row" style="display: none">
          @else
          <div class="row">
          @endif
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Jumlah</label>
                  </div>
                  <div class="col-lg-6 col-md-8 col-12">
                    @if($ppaAktif->type_id == 2)
                    <input type="text" id="value" class="form-control form-control-sm" name="total" value="0" disabled="disabled">
                    @else
                    <input type="text" id="value" class="form-control form-control-sm @error('value') is-invalid @enderror number-separator" name="value" value="0" required="required">
                    @error('value')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      @if($ppaAktif->type_id == 2 && $proposals && count($proposals) < 1)
                      <button type="button" class="btn btn-sm btn-secondary">Tambah</button>
                      @else
                      <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Tambah">
                      @endif
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
        <form action="{{ route('ppa.perbarui.semua',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}" id="ppa-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" name="validate" value="">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
                @if(in_array(Auth::user()->role->name, ['fam','faspv']))
                @if($ppaAktif && ((!$isKso && $ppaAktif->bbk && $ppaAktif->bbk->bbk->president_acc_status_id == 1) || ($isKso && $ppaAktif->pa_acc_status_id == 1)))
                <div class="m-0 float-right">
                @if($apbyAktif && $apbyAktif->is_active == 1)
                <a href="{{ route('ppa.ekspor', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                @else
                <button type="button" class="btn btn-secondary btn-sm" disabled="disabled">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                @endif
                </div>
                @endif
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
            @if($ppaAktif->detail()->count() > 0)
            <div class="table-responsive">
                <table id="ppaDetail" class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Akun Anggaran</th>
                            <th>Keterangan</th>
                            @if(in_array(Auth::user()->role->name, ['fam','faspv','am']) && ($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->director_acc_status_id != 1)
                            <th>Sisa Saldo</th>
                            @endif
                            <th>Status</th>
                            <th style="min-width: 200px">Jumlah</th>
                            @if(($apbyAktif && $apbyAktif->is_active == 1) && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
                            <th style="width: 160px">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $i = 1;
                        @endphp
                        @foreach($ppaAktif->detail as $p)
                        <tr id="p-{{ $p->id }}">
                            <td>{{ $i++ }}</td>
                            <td class="detail-account">{{ $p->akun->codeName }}</td>
                            <td class="detail-note">{{ $p->note }}</td>
                            @if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->director_acc_status_id != 1)
                            @php
                            $apbyDetail = $p->akun->apby()->whereHas('apby',function($q)use($tahun,$anggaranAktif){$q->where(['academic_year_id' => $tahun->id,'director_acc_status_id' => 1])->whereHas('jenisAnggaranAnggaran',function($q)use($anggaranAktif){$q->where('id',$anggaranAktif->id);})->aktif()->latest();})->where('account_id',$p->account_id)->first();
                            @endphp
                            <td>{{ $apbyDetail ? $apbyDetail->balanceWithSeparator : '-' }}</td>
                            @endif
                            <td>
                                @if(!$p->pa_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == $anggaranAktif->anggaran->acc_position_id ? 'Anda' : $anggaranAktif->anggaran->accJabatan->name }}"></i>
                                @elseif(!$ppaAktif->pa_acc_status_id && $p->pa_acc_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-secondary mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($p->accPa) ? 'Anda' : $p->accPa->name }}<br>{{ date('d M Y H.i.s', strtotime($p->pa_acc_time)) }}"></i>
                                @elseif($ppaAktif->pa_acc_status_id == 1 && !$p->finance_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == 33 ? 'Anda' : 'Kepala Divisi Umum' }}"></i>
                                @elseif($p->finance_acc_status_id == 1 && !$p->director_acc_status_id)
                                <i class="fa fa-lg fa-check-circle text-warning mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($p->accKeuangan) ? 'Anda' : $p->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($p->finance_acc_time)) }}"></i>
                                @elseif($p->director_acc_status_id == 1)
                                @if($p->value_fam && $p->value_fam > 0 && $p->value_fam != $p->value_director)
                                <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui dengan perubahan oleh {{ Auth::user()->pegawai->is($p->accDirektur) ? 'Anda' : $p->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($p->director_acc_time)) }}<br>Awal: {{ $p->valuePaWithSeparator }}"></i>
                                @else
                                <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($p->accDirektur) ? 'Anda' : $p->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($p->director_acc_time)) }}"></i>
                                @endif
                                @else
                                -
                                @endif
                            </td>
                            <td class="detail-value" style="min-width: 200px">
                                @if((!$isAnggotaPa && $ppaAktif->pa_acc_status_id != 1) || $ppaAktif->finance_acc_status_id == 1 || !$apbyAktif || ($apbyAktif && $apbyAktif->is_active == 0))
                                <input type="text" class="form-control form-control-sm" value="{{ $p->valueWithSeparator }}" disabled>
                                @else
                                <input name="value-{{ $p->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $p->valueWithSeparator }}">
                                @endif
                            </td>
                            @if(($apbyAktif && $apbyAktif->is_active == 1) && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
                            <td>
                                @if($p->finance_acc_status_id != 1)
                                @if($ppaAktif->type_id == 2)
                                <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('ppa.ubah.detail', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}','{{ $p->id }}')"><i class="fas fa-pen"></i></a>
                                @else
                                <button type="button" class="btn btn-sm btn-warning btn-edit" data-toggle="modal" data-target="#edit-form">
                                    <i class="fa fa-pen"></i>
                                </button>
                                @endif
                                <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Pengajuan', '{{ addslashes(htmlspecialchars($p->note)) }}', '{{ route('ppa.hapus', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'id' => $p->id]) }}')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if(($apbyAktif && $apbyAktif->is_active == 1) && ($isPa || (!$isPa && $ppaAktif->pa_acc_status_id == 1)) && $ppaAktif->finance_acc_status_id != 1)
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                            @if($ppaAktif->finance_acc_status_id != 1 || $ppaAktif->detail()->where(function($q){$q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');})->count() > 0)
                            <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#saveAccept">Simpan & Ajukan</button>
                            @endif
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

@if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h6 class="modal-title text-white">Ubah Pengajuan</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>
      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
              <h5 class="font-weight-light mb-3">Memuat...</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" style="display: none;">
        <form action="{{ route('ppa.perbarui.detail', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}" id="editPpaDetailForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" name="editId">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label class="form-control-label">Akun Anggaran</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" class="form-control form-control-sm" name="editAccount" disabled="disabled">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Keterangan</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <textarea id="editNote" class="form-control form-control-sm @error('editNote') is-invalid @enderror" name="editNote" maxlength="255" rows="2" required="required"></textarea>
                    @error('editNote')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Jumlah</label>
                  </div>
                  <div class="col-lg-6 col-md-8 col-12">
                    <input type="text" id="editValue" class="form-control form-control-sm @error('editValue') is-invalid @enderror number-separator" name="editValue" value="0" required="required">
                    @error('editValue')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-1">
            <div class="col-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Simpan">
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@include('template.modal.konfirmasi_hapus')

@endif

@if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif && ($isPa || (!$isPa && $ppaAktif->pa_acc_status_id == 1)) && ($ppaAktif->finance_acc_status_id != 1 || $ppaAktif->detail()->where(function($q){$q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');})->count() > 0))
<div class="modal fade" id="saveAccept" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-warning">
          <i class="material-icons text-warning">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menyimpan dan mengajukan semua alokasi dana yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn mr-1" data-dismiss="modal">Tidak</button>
        <button type="submit" id="saveAcceptBtn" class="btn btn-warning" data-form="ppa-form">Ya, Simpan & Ajukan</button>
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
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<!-- Number with Commas -->
<script src="{{ asset('js/number-with-commas.js') }}"></script>
<!-- Select Sum Amount -->
<script src="{{ asset('js/select-sum-amount.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
@include('template.footjs.modal.get_delete')
@if($ppaAktif->type_id == 2)
@include('template.footjs.modal.post_edit')
@else
@include('template.footjs.modal.ppa_edit')
@endif
@endif
@if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif && ($isPa || (!$isPa && $ppaAktif->pa_acc_status_id == 1)) && ($ppaAktif->finance_acc_status_id != 1 || $ppaAktif->detail()->where(function($q){$q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');})->count() > 0))
@include('template.footjs.modal.post_save_accept')
@endif
<script type="text/javascript">
$(document).ready(function(){
  selectSumAmount('#select2Proposal','data-amount','#value');
});
</script>
@endsection