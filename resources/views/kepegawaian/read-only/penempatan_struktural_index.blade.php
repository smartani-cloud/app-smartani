@extends('template.main.master')

@section('title')
Penempatan Struktural
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Penempatan Struktural</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('penempatan.index') }}">Penempatan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Struktural</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <form action="{{ route('struktural.index') }}" method="get">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="academicYearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Tahun" name="tahunajaran" class="form-control" id="academicYearOpt" onchange="if(this.value){ this.form.submit(); }" required="required">
                      @foreach($tahun as $t)
                      @if($t->is_active == 1 || $t->penempatanPegawai()->count() > 0)
                      <option value="{{ $t->academic_year_start.'-'.$t->academic_year_end }}" {{ $aktif->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
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
              @if(Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(Session::has('danger'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ Session::get('danger') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @php
                $unit_aktif = 0;
              @endphp
              @if(count($unit) > 0)
              <div class="row ml-1">
                @foreach($unit as $u)
                @php
                  $unit_aktif = $u->penempatanPegawai->where('academic_year_id',$aktif->id)->count();
                @endphp
                @if(($aktif->is_active == 0 && $unit_aktif > 0) || $aktif->is_active == 1)
                <div class="col-md-6 col-12 mb-3">
                  <div class="row py-2 rounded border border-light mr-2">
                    <div class="col-8 d-flex align-items-center">
                      <div class="mr-3">
                        <div class="icon-circle bg-gray-500">
                          <i class="fas fa-school text-white"></i>
                        </div>
                      </div>
                      <div>
                        <a class="font-weight-bold text-dark" href="{{ route('struktural.show', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $u->name])}}">
                          {{ $u->name }}
                        </a>
                      </div>
                    </div>
                    <div class="col-4 d-flex justify-content-end align-items-center">
                      <a href="{{ route('struktural.show', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $u->name])}}" class="btn btn-sm btn-outline-brand-green-dark">Pilih</a>
                    </div>
                  </div>
                </div>
                @endif
                @endforeach
                @if($aktif->is_active == 0 && $unit_aktif == 0)
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