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
    <li class="breadcrumb-item"><a href="{{ route('ppa.index', ['jenis' => $jenisAktif->link,'tahun' => $tahun->academicYearLink])}}">{{ $tahun->academic_year }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $anggaranAktif->anggaran->name }}</li>
  </ol>
</div>

<div class="row">
    @foreach($jenisAnggaran as $j)
    @php
    if(!in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur','fam'])){
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
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Tahun</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @foreach($tahunPelajaran as $t)
                      @if($t->is_finance_year == 1 || ($t->is_finance_year != 1 && $t->whereHas('ppa',function($q)use($jenisAktif,$t){$q->where('academic_year_id',$t->id)->whereHas('jenisAnggaranAnggaran',function($q)use($jenisAktif){$q->where('budgeting_type_id',$jenisAktif->id);});})->count()))
                      <option value="{{ $t->academicYearLink }}" {{ $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endif
                      @endforeach
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
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
                @if($tahun->is_finance_year == 1 && $isAnggotaPa && $apbyAktif->is_active == 1)
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="{{ route('ppa.buat', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link])}}">Buat Pengajuan <i class="fas fa-plus-circle ml-1"></i></a>
                @endif
            </div>
                @if(count($ppa) > 0)
                @php
                $i = 1;
                @endphp
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th style="white-space: nowrap">Nomor</th>
                                <th>Status</th>
                                <th>Pengajuan</th>
                                <th>Disetujui</th>
                                <th>Realisasi</th>
                                <th style="width: 120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ppa->sortByDesc('id')->all() as $p)
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td>{{ $p->date ? $p->date : '-' }}</td>
                                <td>{{ $p->number ? $p->number : '-' }}</td>
                                <td>
                                    @if($p->detail()->count() < 1)
                                    <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada rincian akun anggaran yang dimasukkan untuk pengajuan ini"></i>
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
                                    @if($p->bbk && $p->bbk->bbk->director_acc_status_id == 1)
                                    @if($p->detail()->whereNull('value_director')->count() > 0)
                                    @if($p->lppa && $p->lppa->finance_acc_status_id == 1 && $p->lppa->detail()->count() > 0 && ($p->lppa->detail()->sum('value') != $p->detail()->sum('value')))
                                    {{ number_format($p->lppa->detail()->sum('value'), 0, ',', '.') }}
                                    @else
                                    {{ number_format($p->detail()->sum('value'), 0, ',', '.') }}
                                    @endif
                                    @else
                                    @if($p->lppa && $p->lppa->finance_acc_status_id == 1 && $p->lppa->detail()->count() > 0 && ($p->lppa->detail()->sum('value') != $p->detail()->sum('value_director')))
                                    {{ number_format($p->lppa->detail()->sum('value'), 0, ',', '.') }}
                                    @else
                                    {{ number_format($p->detail()->sum('value_director'), 0, ',', '.') }}
                                    @endif
                                    @endif
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('ppa.show', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $p->firstNumber]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
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

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@endsection