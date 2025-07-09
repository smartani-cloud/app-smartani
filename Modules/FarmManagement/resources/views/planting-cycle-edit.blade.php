@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('title')
Ubah {{ $active }}
@endsection

@section('headmeta')
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
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
    <li class="breadcrumb-item"><a href="{{ route($route.'.show', ['id' => $data->id_planting_cycle]) }}">{{ $data->id_planting_cycle }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-purple-dark">Ubah {{ $active }}</h6>
      </div>
      <div class="card-body">
        <form action="{{ route($route.'.update') }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input id="id" type="hidden" name="id" required="required" value="{{ $data->id_planting_cycle }}">
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="selectGreenhouse" class="form-control-label">Greenhouse <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <select class="select2 form-control @error('greenhouse') is-invalid @enderror" name="greenhouse" id="selectGreenhouse" required="required">
                      @foreach($greenhouses as $g)
                      <option value="{{ $g->id }}" {{ old('greenhouse',$data->greenhouse_id ? $data->greenhouse_id : null) == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                      @endforeach
                    </select>
                    @error('greenhouse')
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
                    <label for="selectPlant" class="form-control-label">Tanaman <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <select class="select2 form-control @error('plant') is-invalid @enderror" name="plant" id="selectPlant" required="required">
                      @foreach($plants as $p)
                      <option value="{{ $p->id }}" {{ old('plant',$data->plant_id ? $data->plant_id : null) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endforeach
                    </select>
                    @error('plant')
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
                    <label for="inputSeedHole" class="form-control-label">Tanggal Semai <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-4 col-md-5 col-12">
                    <div class="input-group date">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                      </div>
                      <input type="text" name="date" class="form-control" value="{{ $data->seeding_date ? date('d F Y', strtotime($data->seeding_date)) : '' }}" placeholder="Pilih tanggal" id="dateInput">
                    </div>
                    @error('seed_hole')
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
                    <label for="inputSeedHole" class="form-control-label">Lubang Tanam <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-4 col-md-5 col-9">
                    <input type="text" id="inputSeedHole" class="form-control @error('seed_hole') is-invalid @enderror number-separator" name="seed_hole" value="{{ old('seed_hole',$data->totalSeedHolesWithSeparator) }}" placeholder="mis. 1.000" maxlength="12">
                    @error('seed_hole')
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
                    <label for="inputIrrigationDuration" class="form-control-label">Durasi Penyiraman (s)</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-9">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputIrrigationDuration" type="text" name="irrigation_duration" class="form-control @error('irrigation_duration') is-invalid @enderror" value="{{ old('irrigation_duration',$data->irrigation_duration_seconds ? $data->irrigation_duration_seconds : null) }}" placeholder="mis. 30" maxlength="12">
                    </div>
                    @error('irrigation_duration')
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
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Modal</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                      </div>
                      <input type="text" id="capital_cost" class="form-control @error('capital_cost') is-invalid @enderror number-separator" name="capital_cost" value="{{ old('capital_cost',$data->capitalCostWithSeparator) }}" placeholder="mis. 1.250.000" maxlength="12">
                    </div>
                    @error('capital_cost')
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
                    <label for="normal-input" class="form-control-label">Harga Jual Minimum</label>
                  </div>
                  <div class="col-lg-5 col-md-6 col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                      </div>
                      <input type="text" id="min_price" class="form-control @error('min_price') is-invalid @enderror number-separator" name="min_price" value="{{ old('min_price',$data->harvestProjection->minPricePerOunceWithSeparator) }}" placeholder="mis. 20.000" maxlength="10">
                      <div class="input-group-append">
                        <span class="input-group-text">/ ons</span>
                      </div>
                    </div>
                    @error('min_price')
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
                    <label for="normal-input" class="form-control-label">Harga Jual Maksimum</label>
                  </div>
                  <div class="col-lg-5 col-md-6 col-12">
                    <div class="input-group">
                      <div class="input-group-prepend">
                        <span class="input-group-text">Rp</span>
                      </div>
                      <input type="text" id="max_price" class="form-control @error('max_price') is-invalid @enderror number-separator" name="max_price" value="{{ old('max_price',$data->harvestProjection->maxPricePerOunceWithSeparator) }}" placeholder="mis. 40.000" maxlength="10">
                      <div class="input-group-append">
                        <span class="input-group-text">/ ons</span>
                      </div>
                    </div>
                    @error('max_price')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>          
          <div class="row">            
            <div class="col-lg-10 col-md-12">
              <div class="row">
                <div class="offset-lg-3 offset-md-4 d-xs-none"></div>
                <div class="col-lg-9 col-md-6 col-12">
                  <div class="text-left">
                    <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                  </div>
                </div>
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

<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>
<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datepicker')
@include('template.footjs.global.select2-multiple')
@include('template.footjs.monitoring.input-irrigation-duration')
@endsection