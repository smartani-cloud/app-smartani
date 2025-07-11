@extends('keuangan.parent.ppa_detail')

@section('cards')
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
@endsection

@section('buttons')
@if((($isYear && $tahun == date('Y')) || (!$isYear && $tahun->is_finance_year == 1)) && Auth::user()->unit_id != 5 && $isAnggotaPa && $apbyAktif && $apbyAktif->is_active == 1)
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
</div>
@endif
@endsection