@extends('template.main.master')

@section('title')
Detail Calon Pegawai
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Detail Calon Pegawai</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('calon.index') }}">Calon Pegawai</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex flex-row mb-2">
          <div class="photo-profile-circle d-inline-block"><img src="{{ asset($calon->showPhoto) }}" alt="user-{{ $calon->id }}" class="avatar-img rounded-circle"></div>
          <div class="pl-3">
            <h3 class="font-weight-medium mt-md-3 mb-0">{{ $calon->name }}</h3>
            <span>{{ $calon->nik }}</span>
            <h5 class="mt-2 mb-0">
              @if($calon->gender_id == '1')
              <span class="badge badge-info font-weight-normal">{{ ucwords($calon->jenisKelamin->name) }}</span>
              @elseif($calon->gender_id == '2')
              <span class="badge badge-brand-green font-weight-normal">{{ ucwords($calon->jenisKelamin->name) }}</span>
              @endif
              @if($calon->acceptance_status_id == '1')
              <span class="badge badge-success font-weight-normal">{{ ucwords($calon->rekomendasiPenerimaan->status) }}</span>
              @elseif($calon->acceptance_status_id == '2')
              <span class="badge badge-danger font-weight-normal">{{ ucwords($calon->rekomendasiPenerimaan->status) }}</span>
              @endif
            </h5>
          </div>
        </div>
      </div>
      <div class="card-body bg-gray-200 p-4">
        <div class="d-flex align-items-center">
          @if(!$calon->education_acc_id)
          <span class="mdi mdi-24px mdi-information text-info mr-2"></span>Calon pegawai ini masih menunggu persetujuan Education Team Leader
          @else
          <span class="mdi mdi-24px mdi-check-circle text-success mr-2"></span>Rekomendasi calon pegawai ini telah disetujui oleh {{ $calon->accEdukasi->name }}
          @endif
        </div>
      </div>
      <div class="card-body p-4">
        <div class="row mb-3">
          <div class="col-12">
            <h6 class="font-weight-bold text-brand-green">Info Umum</h6>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Nama Panggilan
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->nickname }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                NIK
              </div>
              <div class="col-lg-6 col-md-5 col-12">
                {{ $calon->nik }}
              </div>
            </div>
          </div>
        </div>
        @if($calon->npwp)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                NPWP
              </div>
              <div class="col-lg-6 col-md-5 col-12">
                {{ $calon->npwp }}
              </div>
            </div>
          </div>
        </div>
        @endif
        @if($calon->nuptk)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                NUPTK
              </div>
              <div class="col-lg-6 col-md-5 col-12">
                {{ $calon->nuptk }}
              </div>
            </div>
          </div>
        </div>
        @endif
        @if($calon->nrg)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                NRG
              </div>
              <div class="col-lg-6 col-md-5 col-12">
                {{ $calon->nrg }}
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Jenis Kelamin
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ ucwords($calon->jenisKelamin->name) }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Tempat, Tanggal Lahir
              </div>
              <div class="col-lg-7 col-md-6 col-12">
                {{ $calon->birth_place.', '.$calon->birthDateId }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Usia
              </div>
              <div class="col-lg-7 col-md-6 col-12">
                {{ $calon->age }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Status Pernikahan
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ ucwords($calon->statusPernikahan->status) }}
              </div>
            </div>
          </div>
        </div>
        <hr>
        <div class="row mb-3">
          <div class="col-12">
            <h6 class="font-weight-bold text-brand-green">Info Alamat dan Kontak</h6>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Alamat
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->address . ', RT ' . sprintf('%03d',$calon->rt) . ' RW ' . sprintf('%03d',$calon->rw) . ', ' . $calon->alamat->name.', '.$calon->alamat->kecamatanName().', '.$calon->alamat->kabupatenName().', '.$calon->alamat->provinsiName() }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Email
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->email }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Nomor Seluler
              </div>
              <div class="col-lg-6 col-md-8 col-12">
                {{ $calon->phone_number }}
              </div>
            </div>
          </div>
        </div>
        <hr>
        <div class="row mb-3">
          <div class="col-12">
            <h6 class="font-weight-bold text-brand-green">Pendidikan</h6>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Pendidikan Terakhir
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->pendidikanTerakhir->name }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Program Studi
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->latarBidangStudi->name }}
              </div>
            </div>
          </div>
        </div>
        @if($calon->universitas)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Universitas
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->universitas->name }}
              </div>
            </div>
          </div>
        </div>
        @endif
        <hr>
        <div class="row mb-3">
          <div class="col-12">
            <h6 class="font-weight-bold text-brand-green">Hasil Tes</h6>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Kompetensi
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->competency_test }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Psikotes
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->psychological_test }}
              </div>
            </div>
          </div>
        </div>
        <hr>
        <div class="row mb-3">
          <div class="col-12">
            <h6 class="font-weight-bold text-brand-green">Rekomendasi</h6>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Penerimaan
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                @if($calon->acceptance_status_id == '1')
                <i class="fa fa-check text-success mr-1"></i>{{ ucwords($calon->rekomendasiPenerimaan->status) }}
                @elseif($calon->acceptance_status_id == '2')
                <i class="fa fa-times text-danger mr-1"></i>{{ ucwords($calon->rekomendasiPenerimaan->status) }}
                @endif
              </div>
            </div>
          </div>
        </div>
        @if($calon->acceptance_status_id == '1')
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Unit Penempatan
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->units()->count() > 0 ? implode(', ',$calon->units->sortBy('id')->pluck('name')->toArray()) : '-' }}
              </div>
            </div>
          </div>
        </div>
        @if($calon->jabatans()->count() > 0)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Jabatan
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ implode(', ',$calon->jabatans()->with('jabatan')->get()->sortBy('jabatan.id')->pluck('name')->toArray()) }}
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Status
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->statusPegawai->status }}
              </div>
            </div>
          </div>
        </div>
        @if($calon->period_start && $calon->period_end)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Masa Kerja
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $calon->periodId }}
              </div>
            </div>
          </div>
        </div>
        @endif
        @endif
      </div>
    </div>
  </div>
</div>
<!--Row-->

@endsection
