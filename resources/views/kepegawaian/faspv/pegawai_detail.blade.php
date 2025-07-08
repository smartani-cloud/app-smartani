@extends('template.main.master')

@section('title')
Detail Pegawai
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Detail Pegawai</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pegawai.index') }}">Pegawai</a></li>
    <li class="breadcrumb-item active" aria-current="page">Detail</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex flex-row mb-2">
          <div class="photo-profile-circle d-inline-block"><img src="{{ asset($pegawai->showPhoto) }}" alt="user-{{ $pegawai->id }}" class="avatar-img rounded-circle"></div>
          <div class="pl-3">
            <h3 class="font-weight-medium mt-md-3 mb-0">{{ $pegawai->name }}</h3>
            <span>{{ $pegawai->nip }}</span>
            <h5 class="mt-2 mb-0">
              @if($pegawai->gender_id == '1')
              <span class="badge badge-info font-weight-normal">{{ ucwords($pegawai->jenisKelamin->name) }}</span>
              @elseif($pegawai->gender_id == '2')
              <span class="badge badge-brand-green font-weight-normal">{{ ucwords($pegawai->jenisKelamin->name) }}</span>
              @endif
              @if($pegawai->statusBaru && $pegawai->statusBaru->status == 'aktif' && !$pegawai->statusPhk)
              <span class="badge badge-primary font-weight-normal">Baru</span>
              @elseif($pegawai->statusPhk && $pegawai->statusPhk->status == 'aktif')
              <span class="badge badge-warning font-weight-normal">PHK</span>
              @endif
            </h5>
          </div>
        </div>
      </div>
      @if($pegawai->status->status == 'nonaktif' && $pegawai->disjoin_date)
      <div class="card-body bg-gray-200 p-4">
        <div class="d-flex align-items-center">
          <span class="mdi mdi-24px mdi-information text-info mr-2"></span>Pegawai ini telah dinonaktifkan per tanggal {{ date('j F Y', strtotime($pegawai->disjoin_date)) }}
        </div>
      </div>
      @endif
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
                {{ $pegawai->nickname }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                {{ $pegawai->statusPegawai->kategori->name == 'Mitra' ? 'NIMY' : 'NIPY' }}
              </div>
              <div class="col-lg-6 col-md-5 col-12">
                {{ $pegawai->nip }}
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
                {{ $pegawai->nik }}
              </div>
            </div>
          </div>
        </div>
        @if($pegawai->npwp)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                NPWP
              </div>
              <div class="col-lg-6 col-md-5 col-12">
                {{ $pegawai->npwp }}
              </div>
            </div>
          </div>
        </div>
        @endif
		    @if($pegawai->nuptk)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                NUPTK
              </div>
              <div class="col-lg-6 col-md-5 col-12">
                {{ $pegawai->nuptk }}
              </div>
            </div>
          </div>
        </div>
        @endif
		    @if($pegawai->nrg)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                NRG
              </div>
              <div class="col-lg-6 col-md-5 col-12">
                {{ $pegawai->nrg }}
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
                {{ ucwords($pegawai->jenisKelamin->name) }}
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
                {{ $pegawai->birth_place.', '.$pegawai->birthDateId }}
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
                {{ $pegawai->age }}
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
                {{ ucwords($pegawai->statusPernikahan->status) }}
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
                {{ $pegawai->address . ', RT ' . sprintf('%03d',$pegawai->rt) . ' RW ' . sprintf('%03d',$pegawai->rw) . ', ' . $pegawai->alamat->name.', '.$pegawai->alamat->kecamatanName().', '.$pegawai->alamat->kabupatenName().', '.$pegawai->alamat->provinsiName() }}
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
                {{ $pegawai->email }}
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
                {{ $pegawai->phone_number }}
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
                {{ $pegawai->pendidikanTerakhir->name }}
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
                {{ $pegawai->latarBidangStudi->name }}
              </div>
            </div>
          </div>
        </div>
        @if($pegawai->universitas)
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Universitas
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $pegawai->universitas->name }}
              </div>
            </div>
          </div>
        </div>
        @endif
        <hr>
        <div class="row mb-3">
          <div class="col-12">
            <h6 class="font-weight-bold text-brand-green">Kepegawaian</h6>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Penempatan
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                @if($pegawai->units()->count() > 0)

                @php
                $unitCount = $pegawai->units()->count();
                @endphp

                @foreach($pegawai->units()->orderBy('unit_id')->get() as $u)

                @php
                $penempatan = null;
                @endphp

                @if($u->jabatans()->count() > 0)

                @php
                $i = $u->jabatans()->count();
                @endphp

                @foreach($u->jabatans as $j)
                @php
                if($i == 1){
                  $penempatan .= $j->name;
                }
                else{
                  $penempatan .= $j->name.', ';
                }
                $i--;
                @endphp
                @endforeach

                @endif

                @php
                $unit = $penempatan ? $u->unit->name.' - '.$penempatan : $u->unit->name.' - Belum ditentukan';
                @endphp

                @if($unitCount == 1)
                {{ $unit }}
                @else
                {{ $unit }}<br>
                @endif

                @php
                $unitCount--;
                @endphp

                @endforeach

                @else
                Belum ditentukan
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Status
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $pegawai->statusPegawai->status }}
              </div>
            </div>
          </div>
        </div>
        @if($pegawai->employee_status_id == 1 && ($pegawai->tetap && $pegawai->tetap->promotion_date))
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Tanggal Diangkat PT
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ date('j F Y', strtotime($pegawai->tetap->promotion_date)) }}
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Bergabung Sejak
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ date('j F Y', strtotime($pegawai->join_date)) }}
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row mb-3">
              <div class="col-lg-3 col-md-4 col-12">
                Masa Kerja
              </div>
              <div class="col-lg-9 col-md-8 col-12">
                {{ $pegawai->yearsOfService }}
              </div>
            </div>
          </div>
        </div>
        @if(($pegawai->statusBaru && $pegawai->statusBaru->status == 'aktif') || ($pegawai->statusPhk && $pegawai->statusPhk->status == 'aktif'))
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="row">
              <div class="offset-lg-3 col-lg-9 offset-md-4 col-md-8 col-12 text-left">
                @if($pegawai->statusBaru && $pegawai->statusBaru->status == 'aktif')
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#join-confirm" onclick="joinModal('{{ addslashes(htmlspecialchars($pegawai->name)) }}', '{{ route('pegawai.validasi', ['id' => $pegawai->id]) }}')">Konfirmasi</button>
                @endif
                @if($pegawai->statusPhk && $pegawai->statusPhk->status == 'aktif')
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#disjoin-confirm" onclick="disjoinModal('{{ addslashes(htmlspecialchars($pegawai->name)) }}', '{{ route('pegawai.validasi', ['id' => $pegawai->id]) }}')">Konfirmasi</button>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
<!--Row-->

@include('template.modal.pegawai_baru_validasi')

@include('template.modal.pegawai_phk_validasi')

@endsection

@section('footjs')
<!-- Page level custom scripts -->
@include('template.footjs.modal.get_join_validate')
@endsection