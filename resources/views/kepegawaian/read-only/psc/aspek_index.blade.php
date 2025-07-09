<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Aspek Evaluasi dan IKU
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@php
$role = Auth::user()->role->name;
@endphp
@if(in_array($role,['admin','am','aspv','direktur','etl','etm','fam','faspv','kepsek','pembinayys','ketuayys','wakasek']))
@include('template.sidebar.kepegawaian.'.$role)
@else
@include('template.sidebar.kepegawaian.employee')
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Atur Aspek Evaluasi dan IKU</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Atur Aspek Evaluasi dan IKU</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="positionOpt" class="form-control-label">Jabatan</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="input-group">
                      <select aria-label="Position" name="position" class="form-control" id="positionOpt" required="required">
                        @foreach($targets as $t)
                        <option value="{{ $t->target->id }}" {{ $target && $t->target->id == $target->target_position_id ? 'selected' : '' }}>{{ $t->target->name }}</option>
                        @endforeach
                      </select>
                      <a href="{{ route('psc.aspek.index') }}" id="btn-select-position" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('psc.aspek.index') }}">Pilih</a>
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Aspek Evaluasi dan IKU</h6>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek Evaluasi dan Indikator Kinerja Utama</th>
                      <th style="min-width: 80px">Bobot</th>
                      <th>Penilai</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $level = null;
                    @endphp
                    @if($indicators->count() > 0)
                    @php
                    for($i=1;$i<=$indicators->max('level');$i++){
                      $no[$i] = 1;
                    }
                    @endphp
                    @foreach($indicators as $i)
                    @php
                    if(!$level) $level = $i['level'];
                    elseif($level == $i['level']) $no[$i['level']]++;
                    elseif($level != $i['level']){
                      if(($level > $i['level']) && ($i['level'] >= 1)){
                        $no[$level] = 1;
                        $no[$i['level']]++;
                      }
                      $level = $i['level'];
                    }
                    @endphp
                    <tr>
                      @php
                      $number = null;
                      for($j=$i['level'];$j>0;$j--){
                        if($j == $i['level']){
                          $number = $no[$j];
                        }
                        else{
                          $number = $no[$j].'.'.$number;
                        }
                      }
                      @endphp
                      <td>{{ $number }}</td>
                      @php $item = (object)$i @endphp
                      <td class="{{ $i->level == 1 ? 'font-weight-bold' : '' }}">{{ $i->name }}</td>
                      <td>
                        <div class="input-group">
                          @php $thisPercentage = null @endphp
                          @if($item->target()->where('position_id',$target->target_position_id)->count() > 0)
                          @php
                          $thisPercentage = $item->target()->select('id','percentage')->where('position_id',$target->target_position_id)->first();
                          @endphp
                          {{ $thisPercentage->percentage.' %' }}
                          @else
                          {{ $i->percentage.' %' }}
                          @endif
                        </div>
                      </td>
                      <td>
                        @if($item->penilai()->count() > 0)
                        {{ implode(', ',$item->penilai()->select('name')->pluck('name')->toArray()) }}
                        @else
                        -
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.select2')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.kepegawaian.psc.change-position')
@endsection
=======
@extends('template.main.master')

@section('title')
Aspek Evaluasi dan IKU
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@php
$role = Auth::user()->role->name;
@endphp
@if(in_array($role,['admin','am','aspv','direktur','etl','etm','fam','faspv','kepsek','pembinayys','ketuayys','wakasek']))
@include('template.sidebar.kepegawaian.'.$role)
@else
@include('template.sidebar.kepegawaian.employee')
@endif
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Atur Aspek Evaluasi dan IKU</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Atur Aspek Evaluasi dan IKU</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="positionOpt" class="form-control-label">Jabatan</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="input-group">
                      <select aria-label="Position" name="position" class="form-control" id="positionOpt" required="required">
                        @foreach($targets as $t)
                        <option value="{{ $t->target->id }}" {{ $target && $t->target->id == $target->target_position_id ? 'selected' : '' }}>{{ $t->target->name }}</option>
                        @endforeach
                      </select>
                      <a href="{{ route('psc.aspek.index') }}" id="btn-select-position" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('psc.aspek.index') }}">Pilih</a>
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

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Aspek Evaluasi dan IKU</h6>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek Evaluasi dan Indikator Kinerja Utama</th>
                      <th style="min-width: 80px">Bobot</th>
                      <th>Penilai</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $level = null;
                    @endphp
                    @if($indicators->count() > 0)
                    @php
                    for($i=1;$i<=$indicators->max('level');$i++){
                      $no[$i] = 1;
                    }
                    @endphp
                    @foreach($indicators as $i)
                    @php
                    if(!$level) $level = $i['level'];
                    elseif($level == $i['level']) $no[$i['level']]++;
                    elseif($level != $i['level']){
                      if(($level > $i['level']) && ($i['level'] >= 1)){
                        $no[$level] = 1;
                        $no[$i['level']]++;
                      }
                      $level = $i['level'];
                    }
                    @endphp
                    <tr>
                      @php
                      $number = null;
                      for($j=$i['level'];$j>0;$j--){
                        if($j == $i['level']){
                          $number = $no[$j];
                        }
                        else{
                          $number = $no[$j].'.'.$number;
                        }
                      }
                      @endphp
                      <td>{{ $number }}</td>
                      @php $item = (object)$i @endphp
                      <td class="{{ $i->level == 1 ? 'font-weight-bold' : '' }}">{{ $i->name }}</td>
                      <td>
                        <div class="input-group">
                          @php $thisPercentage = null @endphp
                          @if($item->target()->where('position_id',$target->target_position_id)->count() > 0)
                          @php
                          $thisPercentage = $item->target()->select('id','percentage')->where('position_id',$target->target_position_id)->first();
                          @endphp
                          {{ $thisPercentage->percentage.' %' }}
                          @else
                          {{ $i->percentage.' %' }}
                          @endif
                        </div>
                      </td>
                      <td>
                        @if($item->penilai()->count() > 0)
                        {{ implode(', ',$item->penilai()->select('name')->pluck('name')->toArray()) }}
                        @else
                        -
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.select2')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.kepegawaian.psc.change-position')
@endsection
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
