@extends('template.main.master')

@section('title')
APBY
@endsection

@section('headmeta')
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
    <li class="breadcrumb-item"><a href="{{ route('apby.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('apby.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">{{ !$isYear ? $tahun->academic_year : $tahun }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $anggaranAktif->anggaran->name }}</li>
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
                      @if((!$academicYears && !$isYear) ||($academicYears && count($academicYears) > 0))
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

<div class="row">
    <div class="{{ $apbyAktif && $apbyAktif->detail()->count() > 0 && $apbyAktif->president_acc_status_id == 1? 'col-lg-4 ' : 'col-lg-6 ' }}col-12 mb-4">
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
    <div class="{{ $apbyAktif && $apbyAktif->detail()->count() > 0 && $apbyAktif->president_acc_status_id == 1? 'col-lg-4 ' : 'col-lg-6 ' }}col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $apbyAktif && $apbyAktif->detail()->count() > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Anggaran</div>
                        <h6 id="summary" class="mb-0">
                            @if($apbyAktif && $apbyAktif->detail()->count() > 0)
                            @if($apbyAktif->president_acc_status_id != 1)
                            {{ number_format($total->get('anggaran'), 0, ',', '.') }}
                            @else
                            {{ $apbyAktif->totalValueWithSeparator }}
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
    @if($apbyAktif && $apbyAktif->detail()->count() > 0 && $apbyAktif->president_acc_status_id == 1)
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-wallet text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Sisa Saldo</div>
                        <h6 class="mb-0">
                            @if($apbyAktif->president_acc_status_id != 1)
                            {{ number_format($total->get('anggaran'), 0, ',', '.') }}
                            @else
                            {{ $apbyAktif->totalBalanceWithSeparator }}
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $total && $total->get('pendapatanPembiayaan') > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-coins text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Pendapatan & Pembiayaan</div>
                        <h6 id="summary" class="mb-0">
                            {{ number_format($total->get('pendapatanPembiayaan'), 0, ',', '.') }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $total && $total->get('belanja') > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Belanja</div>
                        <h6 id="summary" class="mb-0">
                            {{ number_format($total->get('belanja'), 0, ',', '.') }}
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle  {{ $total && $total->get('operasionalPembiayaan') > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-suitcase text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Saldo Operasional & Pembiayaan</div>
                        <h6 id="summary" class="mb-0">
                            {{ number_format($total->get('operasionalPembiayaan'), 0, ',', '.') }}
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
        <form action="{{ route('apby.perbarui',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}" id="apby-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <input type="hidden" name="validate" value="">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
                <div class="m-0 float-right">
                @if($apbyAktif && $apbyAktif->director_acc_status_id == 1 && ($apbyAktif->detail()->whereHas('akun',function($q){$q->where(['is_fillable' => 1, 'is_static' => 0]);})->whereNull('president_acc_status_id')->count() > 0 || ($apbyAktif->president_acc_status_id != 1 && $apbyAktif->detail()->whereHas('akun',function($q){$q->where(['is_fillable' => 1, 'is_static' => 0]);})->whereNull('president_acc_status_id')->count() < 1)))
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#acceptAll">Setujui Semua <i class="fas fa-check ml-1"></i></button>
                @endif
                </div>
            </div>
            @if($apbyAktif && $apbyAktif->detail()->count() > 0)
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th style="white-space: nowrap">No Akun</th>
                            <th>Nama Akun</th>
                            <th>Status</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $parent = null;
                        $categoryCount = $apbyAktif->detail()->has('akun.kategori')->with('akun.kategori')->get()->pluck('akun.kategori')->unique()->count();
                        @endphp
                        @foreach($kategori as $k)
                        @php
                        $i = 1;
                        $apbyDetail = $apbyAktif->detail()->whereHas('akun.kategori',function($q)use($k){$q->where('name',$k->name);});
                        @endphp
                        @if($apbyDetail->count() > 0)
                        @foreach($apbyDetail->with('akun')->get()->sortBy('akun.sort_order')->all() as $d)
                        @if($i == 1 && $categoryCount > 1 && $k->parent && ((($k->parent->name != $parent) && $parent != 'Pembiayaan') || $parent == 'Pembiayaan'))
                        @php $parent = $k->parent->name @endphp
                        <tr>
                            <td>&nbsp;</td>
                            <td colspan="3" class="font-weight-bold">{{ strtoupper($parent != 'Pembiayaan' ? $parent : $k->name) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>{{ $d->akun->code }}</td>
                            <td>{{ $d->akun->name }}</td>
                            <td>
                                @if($d->akun->is_fillable > 0)
                                @if(!$d->employee_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Nominal anggaran akun ini berasal dari RKAT"></i>
                                @elseif(!$d->finance_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == 33 ? 'Anda' : 'Kepala Divisi Umum' }}"></i>
                                @elseif(!$apbyAktif->finance_acc_status_id && $d->finance_acc_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-warning mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($d->accKeuangan) ? 'Anda' : $d->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($d->finance_acc_time)) }}"></i>
                                @elseif($apbyAktif->finance_acc_status_id == 1 && !$d->director_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan  {{ Auth::user()->pegawai->position_id == 17 ? 'Anda' : 'Director' }}"></i>
                                @elseif(!$apbyAktif->director_acc_status_id && $d->director_acc_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($d->accDirektur) ? 'Anda' : $d->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($d->director_acc_time)) }}"></i>
                                @elseif($apbyAktif->director_acc_status_id == 1 && !$d->president_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan {{ Auth::user()->pegawai->position_id == 16 ? 'Anda' : 'Ketua Yayasan' }}"></i>
                                @elseif($d->president_acc_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-info mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($d->accKetua) ? 'Anda' : $d->accKetua->name }}<br>{{ date('d M Y H.i.s', strtotime($d->president_acc_time)) }}"></i>
                                @else
                                -
                                @endif
                                @else
                                -
                                @endif
                            </td>
                            <td>
                                @if($apbyAktif->director_acc_status_id != 1 || $apbyAktif->president_acc_status_id == 1 || ($apbyAktif->president_acc_status_id != 1 && $d->akun->is_fillable < 1))
                                <input type="text" class="form-control form-control-sm" value="{{ $d->valueWithSeparator }}" disabled>
                                @else
                                <input name="value-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->valueWithSeparator }}" {{ $d->akun->is_fillable < 1 ? 'disabled' : '' }}>
                                @endif
                            </td>
                        </tr>
                        @php $i++ @endphp
                        @endforeach
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                @if($apbyAktif->director_acc_status_id == 1 && $apbyAktif->president_acc_status_id != 1)
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                            <button class="btn btn-info" type="button" data-toggle="modal" data-target="#saveAccept">Simpan & Setujui</button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data akun anggaran yang ditemukan</h6>
            </div>
            <div class="card-footer"></div>
            @endif
        </form>
        </div>
    </div>
</div>
@endif

<!--Row-->

@if($apbyAktif && $apbyAktif->director_acc_status_id == 1 && ($apbyAktif->detail()->whereHas('akun',function($q){$q->where(['is_fillable' => 1, 'is_static' => 0]);})->whereNull('president_acc_status_id')->count() > 0 || ($apbyAktif->president_acc_status_id != 1 && $apbyAktif->detail()->whereHas('akun',function($q){$q->where(['is_fillable' => 1, 'is_static' => 0]);})->whereNull('president_acc_status_id')->count() < 1)))
<div class="modal fade" id="acceptAll" tabindex="-1" role="dialog" aria-labelledby="setujuiModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-info">
          <i class="material-icons text-info">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menyetujui semua alokasi dana yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('apby.validasi.semua',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-info">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif
@if($apbyAktif && $apbyAktif->director_acc_status_id == 1 && $apbyAktif->president_acc_status_id != 1)
<div class="modal fade" id="saveAccept" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-info">
          <i class="material-icons text-info">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menyimpan dan menyetujui semua alokasi dana yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <button type="submit" id="saveAcceptBtn" class="btn btn-info" data-form="apby-form">Ya, Simpan & Setujui</button>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@include('template.footjs.modal.post_save_accept')
@endsection