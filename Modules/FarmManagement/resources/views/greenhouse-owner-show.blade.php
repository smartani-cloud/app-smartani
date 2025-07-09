@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('title')
Detail {{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- GLightbox -->
<link href="{{ asset('vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $data->nik }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex flex-row mb-2">
          <div class="photo-profile-circle d-inline-block"><img src="{{ asset($data->showPhoto) }}" alt="user-{{ $data->id }}" class="avatar-img rounded-circle"></div>
          <div class="pl-3">
            <h3 class="font-weight-medium mt-md-3 mb-0">{{ $data->name }}</h3>
            <span>{{ $data->nik }}</span>            
            <h5 class="mt-2 mb-0">
              @if($data->gender_id == '1')
              <span class="badge badge-info font-weight-normal">{{ ucwords($data->gender->name) }}</span>
              @elseif($data->gender_id == '2')
              <span class="badge badge-brand-green font-weight-normal">{{ ucwords($data->gender->name) }}</span>
              @endif
            </h5>
          </div>
        </div>
      </div>
      <ul class="nav nav-tabs profile-tab" role="tablist">
        <li class="nav-item ml-3"> 
          <a class="nav-link active" data-toggle="tab" href="#account-details" role="tab" aria-controls="account-details" aria-selected="true">
            <span class="d-block d-md-none">
              <i class="mdi mdi-card-account-details mdi-16px"></i>
            </span>
            <span class="d-none d-md-block">Profil</span>
          </a> 
        </li>
        <li class="nav-item"> 
          <a class="nav-link" data-toggle="tab" href="#greenhouse-details" role="tab" aria-controls="greenhouse-details" aria-selected="false">
            <span class="d-block d-md-none">
              <i class="mdi mdi-clipboard-arrow-up mdi-16px"></i>
            </span>
            <span class="d-none d-md-block">Greenhouse</span>
          </a> 
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="account-details" role="tabpanel" aria-labelledby="account-details">
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
                    {{ $data->nickname }}
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
                    {{ $data->nik }}
                  </div>
                </div>
              </div>
            </div>
            @if($data->npwp)
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    NPWP
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    {{ $data->npwp }}
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
                    {{ ucwords($data->gender->name) }}
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
                    {{ $data->birth_place.', '.$data->birthDateId }}
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
                    {{ $data->age }}
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
                    {{ $data->address . ', RT ' . sprintf('%03d',$data->rt) . ' RW ' . sprintf('%03d',$data->rw) . ', ' . $data->region->name.', '.$data->region->subdistrictName.', '.$data->region->cityName.', '.$data->region->provinceName }}
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
                    {{ $data->email }}
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
                    {{ $data->phone_number }}
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
                    @if($data->activeStatus->code == 'active')
                    <i class="fa fa-lg fa-check-circle text-success mr-1"></i>{{ $data->activeStatus->name }}
                    @else
                    <i class="fa fa-lg fa-times-circle text-danger mr-1"></i>{{ $data->activeStatus->name }}
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="greenhouse-details" role="tabpanel" aria-labelledby="greenhouse-details">
          <div class="card-body p-4">
            @if($data->units()->count() > 0)
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Daftar Greenhouse</h6>
              </div>
            </div>
            @endif
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Greenhouse
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @php $i = 1 @endphp
                    @foreach($data->units as $p)
                    {{ $i++.'. '.$p->name }}
                    @endforeach
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
<!--Row-->

@endsection
