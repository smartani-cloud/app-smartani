<<<<<<< HEAD
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('title')
Tambah {{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<style>
.select2-container .select2-results__option[aria-disabled=true] {
  background-color: #dddfeb!important;
}
</style>
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-purple-dark">Tambah {{ $active }}</h6>
      </div>
      <div class="card-body">
        <form action="{{ route($route.'.store') }}" id="add-form" method="post" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="return validateDate('inputBirthDate','birthDateError');">
          {{ csrf_field() }}
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Info Umum</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputName" class="form-control-label">Nama Greenhouse <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input id="inputName" class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="mis. Greenhouse Baturraden" value="{{ old('name') }}" maxlength="255" required="required">
                    @error('name')
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
                    <label for="inputPhoto" class="form-control-label">Foto <span class="text-danger"></span></label>
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    <img src="{{ asset('img/avatar/default.png') }}" id="preview" class="img-thumbnail photo-preview">
                    <input type="file" name="photo" class="file d-none" accept="image/jpg,image/jpeg,image/png">
                    <div class="input-group mt-3">
                      <input type="text" class="form-control @error('photo') is-invalid @enderror" disabled placeholder="Unggah foto..." id="file">
                      <div class="input-group-append">
                        <button type="button" class="browse btn btn-brand-green-dark">Pilih</button>
                      </div>
                    </div>
                    <small id="photoHelp" class="form-text text-muted">Ekstensi .jpg, .jpeg, .png, .webp dan maksimum 1 MB</small>
                    @error('photo')
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
                    <label for="selectIrrigationSystem" class="form-control-label">Sistem Irigasi <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select class="select2 form-control @error('irrigation_system') is-invalid @enderror" name="irrigation_system" id="selectIrrigationSystem" required="required">
                      @foreach($irrigationSystems as $s)
                      <option value="{{ $s->code }}" {{ old('irrigation_system') == $s->code ? 'selected' : '' }}>{{ $s->name }}</option>
                      @endforeach
                    </select>
                    @error('irrigation_system')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Info Alamat</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="provinceOpt" class="form-control-label">Provinsi <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Province" name="province" class="form-control @error('province') is-invalid @enderror" id="provinceOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('province') ? '' : 'selected' }}>== Pilih Provinsi ==</option>
                      @foreach($provinces as $p)
                      <option value="{{ $p->code }}" {{ old('province') == $p->code ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endforeach
                    </select>
                    @error('province')
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
                    <label for="cityOpt" class="form-control-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="City" name="city" class="form-control @error('city') is-invalid @enderror" id="cityOpt" tabindex="-1" aria-hidden="true" required="required" disabled="disabled">
                      <option value="">== Pilih Kabupaten/Kota ==</option>
                    </select>
                    @error('city')
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
                    <label for="subdistrictOpt" class="form-control-label">Kecamatan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Subdistrict" name="subdistrict" class="form-control @error('subdistrict') is-invalid @enderror" id="subdistrictOpt" tabindex="-1" aria-hidden="true" required="required" disabled="disabled">
                      <option value="">== Pilih Kecamatan ==</option>
                    </select>
                    @error('subdistrict')
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
                    <label for="villageOpt" class="form-control-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Village" name="village" class="form-control @error('village') is-invalid @enderror" id="villageOpt" tabindex="-1" aria-hidden="true" required="required" disabled="disabled">
                      <option value="">== Pilih Desa/Kelurahan ==</option>
                    </select>
                    @error('village')
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
                    <label for="inputAddress" class="form-control-label">Alamat <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <textarea id="inputAddress" class="form-control @error('address') is-invalid @enderror" name="address" maxlength="255" rows="3" required="required">{{ old('address') }}</textarea>
                    @error('address')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div><div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputRt" class="form-control-label">RT <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-8">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputRt" type="text" name="rt" class="form-control @error('rt') is-invalid @enderror" value="{{ old('rt') }}" required="required">
                    </div>
                    @error('rt')
                    <span class="mt-1 text-danger d-block">{{ $message }}</span>
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
                    <label for="inputRw" class="form-control-label">RW <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-8">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputRw" type="text" name="rw" class="form-control @error('rw') is-invalid @enderror" value="{{ old('rw') }}" required="required">
                    </div>
                    @error('rw')
                    <span class="mt-1 text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Info Geografis</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Luas</label>
                  </div>
                  <div class="col-lg-2 col-md-4 col-6">
                    <div class="input-group">
                      <input type="text" id="area" class="form-control @error('area') is-invalid @enderror number-separator" name="area" value="{{ old('area') }}" placeholder="mis. 1.250" maxlength="10">
                      <div class="input-group-append">
                        <span class="input-group-text">m<sup>2</sup></span>
                      </div>
                    </div>
                    @error('area')
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
                    <label for="normal-input" class="form-control-label">Ketinggian</label>
                  </div>
                  <div class="col-lg-2 col-md-4 col-6">
                    <div class="input-group">
                      <input type="text" id="elevation" class="form-control @error('elevation') is-invalid @enderror number-separator" name="elevation" value="{{ old('elevation') }}" placeholder="mis. 350" maxlength="10">
                      <div class="input-group-append">
                        <span class="input-group-text">mdpl</span>
                      </div>
                    </div>
                    @error('elevation')
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
                    <label for="normal-input" class="form-control-label">Latitude</label>
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    <div class="input-group">
                      <input type="number" id="gps_lat" class="form-control @error('gps_lat') is-invalid @enderror" name="gps_lat" step="0.0000001" min="-90" max="90" value="{{ old('gps_lat') }}" placeholder="mis. -7.4935">
                      <div class="input-group-append">
                        <span class="input-group-text">&deg;</span>
                      </div>
                    </div>
                    @error('gps_lat')
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
                    <label for="normal-input" class="form-control-label">Longitude</label>
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    <div class="input-group">
                      <input type="number" id="gps_lng" class="form-control @error('gps_lng') is-invalid @enderror" name="gps_lng" step="0.0000001" min="-180" max="180" value="{{ old('gps_lng') }}" placeholder="mis. 109.3316">
                      <div class="input-group-append">
                        <span class="input-group-text">&deg;</span>
                      </div>
                    </div>
                    @error('gps_lng')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Kepemilikan</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Owner" class="form-control-label">Pemilik</label>
                  </div>
                  <div  class="col-lg-9 col-md-8 col-12">
                    <select class="select2-multiple form-control @error('owner') is-invalid @enderror" name="owner[]" multiple="multiple" id="select2Owner">
                      @foreach($owners as $o)
                      <option value="{{ $o->id }}" {{ old('owner') ? (in_array($o->id, old('owner')) ? 'selected' : '') : '' }}>{{ $o->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="text-right">
                <button class="btn btn-success" type="submit">Tambah</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>
<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Image Preview -->
<script src="{{ asset('js/image-preview.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.input-rt-rw')
@include('template.footjs.global.select-region')
@include('template.footjs.global.select2-multiple')
=======
@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('title')
Tambah {{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<style>
.select2-container .select2-results__option[aria-disabled=true] {
  background-color: #dddfeb!important;
}
</style>
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-purple-dark">Tambah {{ $active }}</h6>
      </div>
      <div class="card-body">
        <form action="{{ route($route.'.store') }}" id="add-form" method="post" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="return validateDate('inputBirthDate','birthDateError');">
          {{ csrf_field() }}
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Info Umum</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputName" class="form-control-label">Nama Greenhouse <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input id="inputName" class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="mis. Greenhouse Baturraden" value="{{ old('name') }}" maxlength="255" required="required">
                    @error('name')
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
                    <label for="inputPhoto" class="form-control-label">Foto <span class="text-danger"></span></label>
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    <img src="{{ asset('img/avatar/default.png') }}" id="preview" class="img-thumbnail photo-preview">
                    <input type="file" name="photo" class="file d-none" accept="image/jpg,image/jpeg,image/png">
                    <div class="input-group mt-3">
                      <input type="text" class="form-control @error('photo') is-invalid @enderror" disabled placeholder="Unggah foto..." id="file">
                      <div class="input-group-append">
                        <button type="button" class="browse btn btn-brand-green-dark">Pilih</button>
                      </div>
                    </div>
                    <small id="photoHelp" class="form-text text-muted">Ekstensi .jpg, .jpeg, .png, .webp dan maksimum 1 MB</small>
                    @error('photo')
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
                    <label for="selectIrrigationSystem" class="form-control-label">Sistem Irigasi <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select class="select2 form-control @error('irrigation_system') is-invalid @enderror" name="irrigation_system" id="selectIrrigationSystem" required="required">
                      @foreach($irrigationSystems as $s)
                      <option value="{{ $s->code }}" {{ old('irrigation_system') == $s->code ? 'selected' : '' }}>{{ $s->name }}</option>
                      @endforeach
                    </select>
                    @error('irrigation_system')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Info Alamat</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="provinceOpt" class="form-control-label">Provinsi <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Province" name="province" class="form-control @error('province') is-invalid @enderror" id="provinceOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('province') ? '' : 'selected' }}>== Pilih Provinsi ==</option>
                      @foreach($provinces as $p)
                      <option value="{{ $p->code }}" {{ old('province') == $p->code ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endforeach
                    </select>
                    @error('province')
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
                    <label for="cityOpt" class="form-control-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="City" name="city" class="form-control @error('city') is-invalid @enderror" id="cityOpt" tabindex="-1" aria-hidden="true" required="required" disabled="disabled">
                      <option value="">== Pilih Kabupaten/Kota ==</option>
                    </select>
                    @error('city')
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
                    <label for="subdistrictOpt" class="form-control-label">Kecamatan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Subdistrict" name="subdistrict" class="form-control @error('subdistrict') is-invalid @enderror" id="subdistrictOpt" tabindex="-1" aria-hidden="true" required="required" disabled="disabled">
                      <option value="">== Pilih Kecamatan ==</option>
                    </select>
                    @error('subdistrict')
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
                    <label for="villageOpt" class="form-control-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Village" name="village" class="form-control @error('village') is-invalid @enderror" id="villageOpt" tabindex="-1" aria-hidden="true" required="required" disabled="disabled">
                      <option value="">== Pilih Desa/Kelurahan ==</option>
                    </select>
                    @error('village')
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
                    <label for="inputAddress" class="form-control-label">Alamat <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <textarea id="inputAddress" class="form-control @error('address') is-invalid @enderror" name="address" maxlength="255" rows="3" required="required">{{ old('address') }}</textarea>
                    @error('address')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div><div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputRt" class="form-control-label">RT <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-8">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputRt" type="text" name="rt" class="form-control @error('rt') is-invalid @enderror" value="{{ old('rt') }}" required="required">
                    </div>
                    @error('rt')
                    <span class="mt-1 text-danger d-block">{{ $message }}</span>
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
                    <label for="inputRw" class="form-control-label">RW <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-8">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputRw" type="text" name="rw" class="form-control @error('rw') is-invalid @enderror" value="{{ old('rw') }}" required="required">
                    </div>
                    @error('rw')
                    <span class="mt-1 text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Info Geografis</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Luas</label>
                  </div>
                  <div class="col-lg-2 col-md-4 col-6">
                    <div class="input-group">
                      <input type="text" id="area" class="form-control @error('area') is-invalid @enderror number-separator" name="area" value="{{ old('area') }}" placeholder="mis. 1.250" maxlength="10">
                      <div class="input-group-append">
                        <span class="input-group-text">m<sup>2</sup></span>
                      </div>
                    </div>
                    @error('area')
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
                    <label for="normal-input" class="form-control-label">Ketinggian</label>
                  </div>
                  <div class="col-lg-2 col-md-4 col-6">
                    <div class="input-group">
                      <input type="text" id="elevation" class="form-control @error('elevation') is-invalid @enderror number-separator" name="elevation" value="{{ old('elevation') }}" placeholder="mis. 350" maxlength="10">
                      <div class="input-group-append">
                        <span class="input-group-text">mdpl</span>
                      </div>
                    </div>
                    @error('elevation')
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
                    <label for="normal-input" class="form-control-label">Latitude</label>
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    <div class="input-group">
                      <input type="number" id="gps_lat" class="form-control @error('gps_lat') is-invalid @enderror" name="gps_lat" step="0.0000001" min="-90" max="90" value="{{ old('gps_lat') }}" placeholder="mis. -7.4935">
                      <div class="input-group-append">
                        <span class="input-group-text">&deg;</span>
                      </div>
                    </div>
                    @error('gps_lat')
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
                    <label for="normal-input" class="form-control-label">Longitude</label>
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    <div class="input-group">
                      <input type="number" id="gps_lng" class="form-control @error('gps_lng') is-invalid @enderror" name="gps_lng" step="0.0000001" min="-180" max="180" value="{{ old('gps_lng') }}" placeholder="mis. 109.3316">
                      <div class="input-group-append">
                        <span class="input-group-text">&deg;</span>
                      </div>
                    </div>
                    @error('gps_lng')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Kepemilikan</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Owner" class="form-control-label">Pemilik</label>
                  </div>
                  <div  class="col-lg-9 col-md-8 col-12">
                    <select class="select2-multiple form-control @error('owner') is-invalid @enderror" name="owner[]" multiple="multiple" id="select2Owner">
                      @foreach($owners as $o)
                      <option value="{{ $o->id }}" {{ old('owner') ? (in_array($o->id, old('owner')) ? 'selected' : '') : '' }}>{{ $o->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="text-right">
                <button class="btn btn-success" type="submit">Tambah</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>
<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Image Preview -->
<script src="{{ asset('js/image-preview.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.input-rt-rw')
@include('template.footjs.global.select-region')
@include('template.footjs.global.select2-multiple')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection