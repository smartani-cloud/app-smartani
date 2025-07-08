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
    <li class="breadcrumb-item"><a href="{{ route('ppa.index', ['jenis' => $jenisAktif->link,'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link])}}">{{ $anggaranAktif->anggaran->name }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $ppaAktif->firstNumber }}</li>
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
                  @if(Auth::user()->role->name == 'fas')
                  {{ $ppaAktif->lppaRef->number }}
                  @else
                  <a href="{{ route('lppa.show',['jenis' => $jenisAktif->link, 'tahun' => isset($isKso) && $isKso ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->lppaRef->firstNumber]) }}" target="_blank" class="text-decoration-none text-info">{{ $ppaAktif->lppaRef->number }}</a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="d-flex justify-content-end">
          <a href="{{ route('ppa.index', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link]) }}" class="btn btn-sm btn-light">Kembali</a>
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

@if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->pa_acc_status_id != 1 && !$ppaAktif->lppa_id && ($akun && $akun->where('is_fillable',1)->count() > 0))
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">Tambah Pengajuan</h6>
      </div>
      <div class="card-body pt-2 pb-3 px-4">
        <form action="{{ route('ppa.tambah', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}" id="addPpaDetailForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
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
                      $apbyBalance = null;
                      if($a->is_exclusive != 1){
                        $apbyBalance = $a->apby()->whereHas('apby',function($q)use($tahun,$anggaranAktif){$q->where(['academic_year_id' => $tahun->id,'director_acc_status_id' => 1])->whereHas('jenisAnggaranAnggaran',function($q)use($anggaranAktif){$q->where('id',$anggaranAktif->id);})->aktif()->latest();})->where('account_id',$a->id)->first();
                      }
                      @endphp
                      @if($a->is_fillable == 1 && ($a->is_exclusive == 1 || ($a->is_exclusive != 1 && (!$checkBalance || ($checkBalance && ($apbyBalance && $apbyBalance->balance > 0))))))
                      <option value="{{ $a->id }}" {{ old('account', $ppaAktif->detail()->count() > 1 ? $ppaAktif->detail()->latest()->first()->account_id : null) == $a->id ? 'selected' : '' }}>{{ $a->codeName }}</option>
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
                    <label for="normal-input" class="form-control-label">Keterangan</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <textarea id="note" class="form-control form-control-sm @error('note') is-invalid @enderror" name="note" maxlength="255" rows="2" required="required"></textarea>
                    @error('note')
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
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Jumlah</label>
                  </div>
                  <div class="col-lg-6 col-md-8 col-12">
                    <input type="text" id="value" class="form-control form-control-sm @error('value') is-invalid @enderror number-separator" name="value" value="0" required="required">
                    @error('value')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Tambah">
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
        <form action="{{ route('ppa.perbarui',['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}" id="ppa-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
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
            @php
            $i = 1;
            @endphp
            <div class="table-responsive">
                <table id="ppaDetail" class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Akun Anggaran</th>
                            <th>Keterangan</th>
                            <th>Status</th>
                            <th style="min-width: 200px">Jumlah</th>
                            @if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->pa_acc_status_id != 1)
                            <th style="width: 160px">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ppaAktif->detail as $p)
                        <tr id="p-{{ $p->id }}">
                            <td>{{ $i++ }}</td>
                            <td class="detail-account">{{ $p->akun->codeName }}</td>
                            <td class="detail-note">{{ $p->note }}</td>
                            <td>
                                @if(!$p->pa_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == $anggaranAktif->anggaran->acc_position_id ? 'Anda' : $anggaranAktif->anggaran->accJabatan->name }}"></i>
                                @elseif(!$ppaAktif->pa_acc_status_id && $p->pa_acc_status_id == 1)
                                <i class="fa fa-lg fa-check-circle text-secondary mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($p->accPa) ? 'Anda' : $p->accPa->name }}<br>{{ date('d M Y H.i.s', strtotime($p->pa_acc_time)) }}"></i>
                                @elseif($ppaAktif->pa_acc_status_id == 1 && !$p->finance_acc_status_id)
                                <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == 33 ? 'Anda' : 'Kepala Divisi Umum' }}"></i>
                                @elseif($p->finance_acc_status_id == 1 && !$p->director_acc_status_id)
                                @if($p->employee_id == Auth::user()->pegawai->id && $p->edited_status_id == 1 && $p->editPegawai->is($p->accKeuangan))
                                <i class="fa fa-lg fa-check-circle text-warning mr-1" data-toggle="tooltip" data-html="true" data-original-title="Dsimpan dengan perubahan oleh {{ Auth::user()->pegawai->is($p->accKeuangan) ? 'Anda' : $p->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($p->finance_acc_time)) }}<br>Awal: {{ $p->valuePaWithSeparator }}"></i>
                                @else
                                <i class="fa fa-lg fa-check-circle text-warning mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($p->accKeuangan) ? 'Anda' : $p->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($p->finance_acc_time)) }}"></i>
                                @endif
                                @elseif($p->director_acc_status_id == 1)
                                @if($p->employee_id == Auth::user()->pegawai->id && $p->edited_status_id == 1 && $p->editPegawai->is($p->accDirektur))
                                <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui dengan perubahan oleh {{ Auth::user()->pegawai->is($p->accDirektur) ? 'Anda' : $p->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($p->director_acc_time)) }}<br>Awal: {{ $p->valuePaWithSeparator }}"></i>
                                @else
                                <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($p->accDirektur) ? 'Anda' : $p->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($p->director_acc_time)) }}"></i>
                                @endif
                                @else
                                -
                                @endif
                            </td>
                            <td class="detail-value" style="min-width: 200px">
                                @if($p->pa_acc_status_id == 1 || !$apbyAktif || ($apbyAktif && $apbyAktif->is_active == 0))
                                <input type="text" class="form-control form-control-sm" value="{{ $p->valueWithSeparator }}" disabled>
                                @else
                                <input name="value-{{ $p->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $p->valueWithSeparator }}">
                                @endif
                            </td>
                            @if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->pa_acc_status_id != 1)
                            <td>
                                @if($p->pa_acc_status_id != 1)
                                <button type="button" class="btn btn-sm btn-warning btn-edit" data-toggle="modal" data-target="#edit-form">
                                    <i class="fa fa-pen"></i>
                                </button>
                                <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Pengajuan', '{{ addslashes(htmlspecialchars($p->note)) }}', '{{ route('ppa.hapus', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'id' => $p->id]) }}')">
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
                @if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->pa_acc_status_id != 1 && $ppaAktif->detail()->where(function($q){$q->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');})->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="text-center">
                            <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
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

@if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->pa_acc_status_id != 1)
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
        <form action="{{ route('ppa.ubah', ['jenis' => $jenisAktif->link, 'tahun' => $tahun->academicYearLink, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}" id="editPpaDetailForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
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

@endsection

@section('footjs')
<!-- Page level plugins -->

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->pa_acc_status_id != 1)
@include('template.footjs.modal.get_delete')
@include('template.footjs.modal.ppa_edit')
@endif
@endsection