@extends('template.main.master')

@section('title')
RPPA
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
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
				@if(in_array(Auth::user()->role->name,['fam','faspv','akunspv']))
				<div class="m-0 float-right">
                @if(in_array(Auth::user()->role->name,['faspv']) && $apbyAktif && $apbyAktif->is_active == 1 && !$isPa && $editable && $lppaAktif->finance_acc_status_id != 1)
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#acceptAll">Setujui Semua <i class="fas fa-check ml-1"></i></button>
                @endif
				@if($lppaAktif && $lppaAktif->finance_acc_status_id == 1)
                <a href="{{ route('lppa.ekspor', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->firstNumber]) }}" class="btn btn-brand-green-dark btn-sm">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                @endif
                </div>
				@endif
            </div>
            @if($lppaAktif->finance_acc_status_id == 1 &&  $selisihKurang < 0)
            <div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
                <i class="fa fa-info-circle text-info mr-2"></i>Selisih yang kurang diajukan dengan PPA nomor <strong><a href="{{ route('ppa.show',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->ppaKurang->firstNumber]) }}" target="_blank" class="text-info">{{ $lppaAktif->ppaKurang->number }}</a></strong>
            </div>
            @endif
            @if(in_array(Auth::user()->role->name,['faspv']))
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
                            <td>{{ $l->ppaDetail->valueWithSeparator }}</td>
                            <td>{{ $l->valueWithSeparator }}</td>
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
                                @if($l->receipt_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="{{ ucwords($l->buktiStatus->status) }}"></i>
                                @elseif($l->receipt_status_id == 2)
                                <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="{{ ucwords($l->buktiStatus->status) }}"></i>
                                @else
                                <i class="fa fa-lg fa-question-circle text-warning" data-toggle="tooltip" data-original-title="Tidak diketahui"></i>
                                @endif
                            </td>
                            <td>
                                @if(!$l->acc_status_id)
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
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data laporan yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>
@endif
<!--Row-->

@if(in_array(Auth::user()->role->name,['faspv']))
@if($apbyAktif && $apbyAktif->is_active == 1 && !$isPa && $editable && $lppaAktif && $lppaAktif->finance_acc_status_id != 1)
<div class="modal fade" id="acceptAll" tabindex="-1" role="dialog" aria-labelledby="setujuiModalLabel" aria-hidden="true" style="display: none;">
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
        Apakah Anda yakin ingin menyetujui semua data laporan yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('lppa.validasi',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $lppaAktif->firstNumber]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@endsection