<<<<<<< HEAD
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
    <li class="breadcrumb-item active" aria-current="page">{{ $data->greenhouse->id_greenhouse }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex flex-row mb-2">
          <div class="photo-profile-circle d-inline-block"><img src="{{ asset($data->greenhouse->showPhoto) }}" alt="{{ $data->greenhouse->id_greenhouse }}" class="avatar-img rounded-circle"></div>
          <div class="pl-3">
            <h3 class="font-weight-medium mt-md-3 mb-0">{{ $data->name }}</h3>
            <span>{{ $data->greenhouse->id_greenhouse }}</span>
            @if($data->greenhouse->irrigationSystem)
            <h5 class="mt-2 mb-0">
              <span class="badge badge-info font-weight-normal">{{ ucwords($data->greenhouse->irrigationSystem->name) }}</span>
            </h5>
            @endif
          </div>
        </div>
      </div>
      <ul class="nav nav-tabs profile-tab" role="tablist">
        <li class="nav-item ml-3"> 
          <a class="nav-link active" data-toggle="tab" href="#greenhouse-details" role="tab" aria-controls="greenhouse-details" aria-selected="true">
            <span class="d-block d-md-none">
              <i class="mdi mdi-card-account-details mdi-16px"></i>
            </span>
            <span class="d-none d-md-block">Profil</span>
          </a> 
        </li>
        <li class="nav-item"> 
          <a class="nav-link" data-toggle="tab" href="#planting-cycle-details" role="tab" aria-controls="planting-cycle-details" aria-selected="false">
            <span class="d-block d-md-none">
              <i class="mdi mdi-clipboard-arrow-up mdi-16px"></i>
            </span>
            <span class="d-none d-md-block">Siklus Tanam</span>
          </a> 
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="greenhouse-details" role="tabpanel" aria-labelledby="greenhouse-details">
          <div class="card-body p-4">
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Info Alamat</h6>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Alamat
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    {{ $data->greenhouse->address . ', RT ' . sprintf('%03d',$data->greenhouse->rt) . ' RW ' . sprintf('%03d',$data->greenhouse->rw) . ', ' . $data->region->name.', '.$data->region->subdistrictName.', '.$data->region->cityName.', '.$data->region->provinceName }}
                  </div>
                </div>
              </div>
            </div>
            @if($data->greenhouse->area || $data->greenhouse->elevation || $data->greenhouse->gps_coordinate)
            <hr>
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Info Geografis</h6>
              </div>
            </div>            
            @if($data->greenhouse->area)
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Luas
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    {{ round($data->greenhouse->area) }} m<sup>2</sup>
                  </div>
                </div>
              </div>
            </div>
            @endif
            @if($data->greenhouse->elevation)
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Ketinggian
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    {{ round($data->greenhouse->elevation) }} mdpl
                  </div>
                </div>
              </div>
            </div>
            @endif
            @if($data->greenhouse->gps_coordinate)
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Koordinat GPS
                  </div>
                <div class="col-lg-7 col-md-6 col-12">
                    {{ $data->greenhouse->gps_lat.', '.$data->greenhouse->gps_lng }}
                  </div>
                </div>
              </div>
            </div><div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Peta
                  </div>
                  <div class="col-xl-7 col-lg-9 col-md-8 col-12">
                    <iframe
                        width="100%"
                        height="450"
                        frameborder="0"
                        style="border:0"
                        allowfullscreen
                        src="https://www.google.com/maps?q={{ $data->greenhouse->gps_coordinate }}&z=15&output=embed">
                    </iframe>
                  </div>
                </div>
              </div>
            </div>
            @endif
            @endif
            @if($data->greenhouseOwners()->count() > 0)
            <hr>
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Kepemilikan</h6>
              </div>
            </div>  
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Pemilik
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @foreach($data->greenhouseOwners as $p)
                    <div class="mb-2">
                      <a href="{{ route('greenhouse-owner.show', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                        <div class="avatar-small d-inline-block">
                          <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                        </div>
                        {{ $p->name }}
                      </a>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
        <div class="tab-pane fade" id="planting-cycle-details" role="tabpanel" aria-labelledby="planting-cycle-details">
          <div class="card-body p-2 px-4 pb-4">
            @if($data->greenhouseOwners()->count() > 0)
            <hr>
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Daftar Siklus Tanam</h6>
              </div>
            </div>
            @endif
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Daftar Siklus Tanam
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
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
=======
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
    <li class="breadcrumb-item active" aria-current="page">{{ $data->greenhouse->id_greenhouse }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <div class="d-flex flex-row mb-2">
          <div class="photo-profile-circle d-inline-block"><img src="{{ asset($data->greenhouse->showPhoto) }}" alt="{{ $data->greenhouse->id_greenhouse }}" class="avatar-img rounded-circle"></div>
          <div class="pl-3">
            <h3 class="font-weight-medium mt-md-3 mb-0">{{ $data->name }}</h3>
            <span>{{ $data->greenhouse->id_greenhouse }}</span>
            @if($data->greenhouse->irrigationSystem)
            <h5 class="mt-2 mb-0">
              <span class="badge badge-info font-weight-normal">{{ ucwords($data->greenhouse->irrigationSystem->name) }}</span>
            </h5>
            @endif
          </div>
        </div>
      </div>
      <ul class="nav nav-tabs profile-tab" role="tablist">
        <li class="nav-item ml-3"> 
          <a class="nav-link active" data-toggle="tab" href="#greenhouse-details" role="tab" aria-controls="greenhouse-details" aria-selected="true">
            <span class="d-block d-md-none">
              <i class="mdi mdi-card-account-details mdi-16px"></i>
            </span>
            <span class="d-none d-md-block">Profil</span>
          </a> 
        </li>
        <li class="nav-item"> 
          <a class="nav-link" data-toggle="tab" href="#planting-cycle-details" role="tab" aria-controls="planting-cycle-details" aria-selected="false">
            <span class="d-block d-md-none">
              <i class="mdi mdi-clipboard-arrow-up mdi-16px"></i>
            </span>
            <span class="d-none d-md-block">Siklus Tanam</span>
          </a> 
        </li>
      </ul>
      <div class="tab-content">
        <div class="tab-pane fade show active" id="greenhouse-details" role="tabpanel" aria-labelledby="greenhouse-details">
          <div class="card-body p-4">
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Info Alamat</h6>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Alamat
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    {{ $data->greenhouse->address . ', RT ' . sprintf('%03d',$data->greenhouse->rt) . ' RW ' . sprintf('%03d',$data->greenhouse->rw) . ', ' . $data->region->name.', '.$data->region->subdistrictName.', '.$data->region->cityName.', '.$data->region->provinceName }}
                  </div>
                </div>
              </div>
            </div>
            @if($data->greenhouse->area || $data->greenhouse->elevation || $data->greenhouse->gps_coordinate)
            <hr>
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Info Geografis</h6>
              </div>
            </div>            
            @if($data->greenhouse->area)
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Luas
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    {{ round($data->greenhouse->area) }} m<sup>2</sup>
                  </div>
                </div>
              </div>
            </div>
            @endif
            @if($data->greenhouse->elevation)
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Ketinggian
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    {{ round($data->greenhouse->elevation) }} mdpl
                  </div>
                </div>
              </div>
            </div>
            @endif
            @if($data->greenhouse->gps_coordinate)
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Koordinat GPS
                  </div>
                <div class="col-lg-7 col-md-6 col-12">
                    {{ $data->greenhouse->gps_lat.', '.$data->greenhouse->gps_lng }}
                  </div>
                </div>
              </div>
            </div><div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Peta
                  </div>
                  <div class="col-xl-7 col-lg-9 col-md-8 col-12">
                    <iframe
                        width="100%"
                        height="450"
                        frameborder="0"
                        style="border:0"
                        allowfullscreen
                        src="https://www.google.com/maps?q={{ $data->greenhouse->gps_coordinate }}&z=15&output=embed">
                    </iframe>
                  </div>
                </div>
              </div>
            </div>
            @endif
            @endif
            @if($data->greenhouseOwners()->count() > 0)
            <hr>
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Kepemilikan</h6>
              </div>
            </div>  
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Pemilik
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @foreach($data->greenhouseOwners as $p)
                    <div class="mb-2">
                      <a href="{{ route('greenhouse-owner.show', ['id' => $p->id]) }}" class="text-info detail-link" target="_blank">
                        <div class="avatar-small d-inline-block">
                          <img src="{{ asset($p->showPhoto) }}" alt="user-{{ $p->id }}" class="avatar-img rounded-circle mr-1">
                        </div>
                        {{ $p->name }}
                      </a>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
            @endif
          </div>
        </div>
        <div class="tab-pane fade" id="planting-cycle-details" role="tabpanel" aria-labelledby="planting-cycle-details">
          <div class="card-body p-2 px-4 pb-4">
            @if($data->greenhouseOwners()->count() > 0)
            <hr>
            <div class="row mb-3">
              <div class="col-12">
                <h6 class="font-weight-bold text-brand-green">Daftar Siklus Tanam</h6>
              </div>
            </div>
            @endif
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    Daftar Siklus Tanam
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
