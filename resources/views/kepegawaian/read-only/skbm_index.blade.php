@extends('template.main.master')

@section('title')
SKBM
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">SKBM</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">SKBM</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <form action="{{ route('skbm.index') }}" method="get">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="academicYearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Tahun" name="tahunpelajaran" class="form-control" id="academicYearOpt" onchange="if(this.value){ this.form.submit(); }" required="required">
                      @foreach($tahun as $t)
                      @if($t->is_active == 1 || $t->skbm()->count() > 0)
                      <option value="{{ $t->academicYearLink }}" {{ $aktif->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endif
                      @endforeach
                    </select>
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Unit Tersedia</h6>
            </div>
            <div class="card-body p-3">
              @php
                $total_unit_aktif = 0;
              @endphp
              @if(count($unit) > 0)
              <div class="row ml-1">
                @foreach($unit as $u)
                @php
                  $unit_aktif = $u->skbm->where('academic_year_id',$aktif->id)->count();
                  $total_unit_aktif += $unit_aktif;
                @endphp
                @if($unit_aktif > 0)
                <div class="col-md-6 col-12 mb-3">
                  <div class="row py-2 rounded border border-light mr-2">
                    <div class="col-8 d-flex align-items-center">
                      <div class="mr-3">
                        <div class="icon-circle bg-gray-500">
                          <i class="fas fa-school text-white"></i>
                        </div>
                      </div>
                      <div>
                        <a class="font-weight-bold text-dark" href="{{ route('skbm.tampil', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $u->name])}}">
                          {{ $u->name }}
                        </a>
                      </div>
                    </div>
                    <div class="col-4 d-flex justify-content-end align-items-center">
                      <a href="{{ route('skbm.tampil', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $u->name])}}" class="btn btn-sm btn-outline-brand-green-dark">Pilih</a>
                    </div>
                  </div>
                </div>
                @endif
                @endforeach
                @if($total_unit_aktif == 0)
                <div class="col-12 pl-0 pr-3">
                  <div class="text-center mx-3 mt-4 mb-5">
                    <h3>Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data unit yang ditemukan</h6>
                  </div>
                </div>
                @endif
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data unit yang ditemukan</h6>
              </div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@endsection