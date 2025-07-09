<<<<<<< HEAD
@extends('keuangan.parent.ppa_detail')

@section('cards')
<div class="row">
    <div class="col-md-6 col-12 mb-4">
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
                          <i class="fas fa-wallet text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Sisa Saldo</div>
                        <h6 class="mb-0">{{ $apbyAktif->totalBalanceWithSeparator }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('buttons')
@if((($isYear && $tahun == date('Y')) || (!$isYear && $tahun->is_finance_year == 1)) && $isAnggotaPa && $apbyAktif && $apbyAktif->is_active == 1)
<div class="m-0 float-right btn-group">
    @if($creatable)
    <button type="button" class="btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#createModal">
      Buat Pengajuan <i class="fas fa-plus-circle ml-1"></i>
    </button>
    @else
    <button type="button" class="btn btn-secondary btn-sm" disabled>
      Buat Pengajuan <i class="fas fa-plus-circle ml-1"></i>
    </button>
    @endif
    {{--
    <a class="btn btn-brand-green-dark btn-sm" href="{{ route('ppa.buat', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}">Buat Pengajuan <i class="fas fa-plus-circle ml-1"></i></a>
    <button type="button" class="btn btn-brand-green-dark btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="{{ route('ppa.buat', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'type' => 'proposal']) }}">Buat dari Proposal</a>
    </div>
    --}}
</div>
@endif
=======
@extends('keuangan.parent.ppa_detail')

@section('cards')
<div class="row">
    <div class="col-md-6 col-12 mb-4">
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
                          <i class="fas fa-wallet text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Sisa Saldo</div>
                        <h6 class="mb-0">{{ $apbyAktif->totalBalanceWithSeparator }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('buttons')
@if((($isYear && $tahun == date('Y')) || (!$isYear && $tahun->is_finance_year == 1)) && $isAnggotaPa && $apbyAktif && $apbyAktif->is_active == 1)
<div class="m-0 float-right btn-group">
    @if($creatable)
    <button type="button" class="btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#createModal">
      Buat Pengajuan <i class="fas fa-plus-circle ml-1"></i>
    </button>
    @else
    <button type="button" class="btn btn-secondary btn-sm" disabled>
      Buat Pengajuan <i class="fas fa-plus-circle ml-1"></i>
    </button>
    @endif
    {{--
    <a class="btn btn-brand-green-dark btn-sm" href="{{ route('ppa.buat', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}">Buat Pengajuan <i class="fas fa-plus-circle ml-1"></i></a>
    <button type="button" class="btn btn-brand-green-dark btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="{{ route('ppa.buat', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'type' => 'proposal']) }}">Buat dari Proposal</a>
    </div>
    --}}
</div>
@endif
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection