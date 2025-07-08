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
