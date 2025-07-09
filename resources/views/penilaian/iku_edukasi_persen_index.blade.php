@extends('template.main.master')

@section('title')
IKU Edukasi
@endsection

@section('headmeta')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">IKU Edukasi</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        @if($ledger || $unit || $semester)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.ikuEdukasi.persen') }}">IKU Edukasi</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">IKU Edukasi</li>
        @endif
		@if($ledger)
		@php
		$ledgerName = $ledgerList->where('link',$ledger)->first();
		$ledgerName = $ledgerName['name'];
		@endphp
        @if($unitList && $unit)
		<li class="breadcrumb-item"><a href="{{ route('penilaian.ikuEdukasi.persen', ['ledger' => $ledger])}}">{{ $ledgerName }}</a></li>
		@else
		<li class="breadcrumb-item active" aria-current="page">{{ $ledgerName }}</li>
		@endif
		@if($unit)
		@if($semesterList && $semester)
		<li class="breadcrumb-item"><a href="{{ route('penilaian.ikuEdukasi.persen', ['ledger' => $ledger, 'unit' => $unit->name])}}">{{ $unit->name }}</a></li>
		@else
		<li class="breadcrumb-item active" aria-current="page">{{ $unit->name }}</li>
		@endif
        @if($semester)
        <li class="breadcrumb-item active" aria-current="page">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</li>
        @endif
		@endif
		@endif
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Ledger</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Ledger" name="ledger" class="form-control" id="ledgerOpt">
                      @foreach($ledgerList as $l)
                      <option value="{{ $l['link'] }}" {{ $ledger == $l['link'] ? 'selected' : '' }}>{{ $l['name'] }}</option>
                      @endforeach
                    </select>
                    <a href="{{ route('penilaian.ikuEdukasi.persen') }}" id="btn-select-ledger" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.ikuEdukasi.persen') }}">Pilih</a>
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
@if($ledger)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="unitOpt" class="form-control-label">Unit</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    @if(in_array(Auth::user()->role->name,['kepsek','wakasek']))
                    <input type="text" class="form-control" value="{{ $unit->name }}" disabled>
                    @else
                    <div class="input-group">
                    <select aria-label="Unit" name="unit" class="form-control" id="unitOpt">
                      @foreach($unitList as $u)
                      <option value="{{ $u->name }}" {{ $unit && $unit->id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                      @endforeach
                    </select>
                    <a href="{{ route('penilaian.ikuEdukasi.persen', ['ledger' => $ledger]) }}" id="btn-select-unit" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.ikuEdukasi.persen', ['ledger' => $ledger]) }}">Pilih</a>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
@endif
@if($unit)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @foreach($semesterList as $s)
                      @if($s->is_active == 1 || ($s->is_active != 1 && $s->riwayatKelas()->count() > 0))
                      <option value="{{ $s->semesterLink }}" {{ $semester->id == $s->id ? 'selected' : '' }}>{{ $s->semester_id . ' (' .$s->semester.')' }}</option>
                      @endif
                      @endforeach
                    </select>
                    <a href="{{ route('penilaian.ikuEdukasi.persen', ['ledger' => $ledger, 'unit' => $unit->name]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.ikuEdukasi.persen', ['ledger' => $ledger, 'unit' => $unit->name]) }}">Atur</a>
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
@endif
@if($semester)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="score" class="form-control-label">Nilai Tercapai</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <form action="{{ route('penilaian.ikuEdukasi.persen', ['ledger' => $ledger, 'unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" method="get">
                        <div class="input-group">
                            <div class="input-group-prepend">
                              <span class="input-group-text"><i class="mdi mdi-greater-than-or-equal"></i></span>
                            </div>
                            <input type="number" name="score" class="form-control" min="1" max="{{ $ledger == 'iklas' ? '5' : '100' }}" value="{{ (isset($score) && ($score > 0)) ? $score : '1' }}" aria-label="Score" aria-describedby="greater-than" required>
                            @if($ledger == 'iklas')
                            <div class="input-group-append">
                              <span class="input-group-text""><i class="fa fa-star"></i></span>
                            </div>
                            @endif
                            <button type="submit" class="btn btn-brand-green ml-2 pt-2">Lihat</button>
                        </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>

@endif

@if(isset($score) && ($score > 0))
@yield('ledger')
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@if($unit && $semesterList)
@include('template.footjs.keuangan.change-year')
@endif
@if($ledger && $unitList)
@include('template.footjs.kependidikan.change-unit')
@endif
@include('template.footjs.kependidikan.change-ledger')
@yield('additional-scripts')
@endsection