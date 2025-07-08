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
        <h6 class="m-0 font-weight-bold text-brand-green">Tambah Aspek Evaluasi dan IKU</h6>
      </div>
      <div class="card-body pt-2 pb-3 px-4">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="form-group">
              <div class="row mb-3">
                <div class="col-lg-3 col-md-4 col-12">
                  <label for="select2Account" class="form-control-label">Pilihan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="acceptanceOpt1" name="acceptance_status" class="custom-control-input" value="old" required="required" checked>
                    <label class="custom-control-label" for="acceptanceOpt1">Sudah Ada</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="acceptanceOpt2" name="acceptance_status" class="custom-control-input" value="new" required="required">
                    <label class="custom-control-label" for="acceptanceOpt2">Baru</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="oldRow">
          <form action="{{ route('psc.aspek.posisi.relasikan', ['position' => $target->target_position_id]) }}" id="existing-aspek-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Indicator" class="form-control-label">Aspek Evaluasi dan IKU</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('indicator') is-invalid @enderror" name="indicator" id="select2Indicator" required="required">
                      @foreach($indicators->where('level',1)->skip(2)->all() as $i)
                      @php
                      $item = (object) $i;
                      $firstChild = $item->childs()->first();
                      @endphp
                      @if($firstChild->childs()->count() > 0)
                      <option class="bg-gray-300" disabled="disabled">{{ $i->name }}</option>
                      @endif
                      @foreach($firstChild->childs()->get() as $c)
                      @if($c->target()->where('position_id',$target->target_position_id)->count() < 1)
                      @php
                      $graders = $c->penilai()->count() > 0 ?  ' - '.implode(', ',$c->penilai()->select('name')->pluck('name')->toArray()) : '';
                      @endphp
                      <option value="{{ $c->id }}">{{ $c->name.$graders }}</option>
                      @endif
                      @endforeach
                      @endforeach
                    </select>
                    @error('indicator')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
              <div class="row">
                <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                  <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Masukkan ke Daftar Aspek Evaluasi dan IKU">
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
        <div id="newRow" style="display: none;">
          <form action="{{ route('psc.aspek.posisi.simpan', ['position' => $target->target_position_id]) }}" id="aspek-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="form-group">
                  <div class="row mb-3">
                    <div class="col-lg-3 col-md-4 col-12">
                      <label for="parent" class="form-control-label">IKU Induk <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                      <select class="form-control form-control-sm @error('parent') is-invalid @enderror" name="parent" required="required">
                        @foreach($indicators->where('level',1)->skip(2)->all() as $i)
                        <option value="{{ $i['id'] }}">{{ $i['name'] }}</option>
                        @endforeach
                      </select>
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
                      <label for="name" class="form-control-label">Indikator Kinerja Utama <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                      <input type="text" class="form-control form-control-sm" name="name" maxlength="255" required="required"/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="from-group">
                  <div class="row mb-3">
                    <div class="col-lg-3 col-md-4 col-12">
                      <label for="percentage" class="form-control-label">Bobot <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-3 col-md-4 col-12">
                      <div class="input-group input-group-sm">
                        <input type="number" class="form-control" name="percentage" value="0" min="0" max="100" step="1" required="required"/>
                        <div class="input-group-append">
                          <span class="input-group-text">%</span>
                        </div>
                      </div>
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
                      <label for="grader" class="form-control-label">Penilai <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                      <select class="select2-multiple form-control form-control-sm @error('grader') is-invalid @enderror" name="grader[]" multiple="multiple" required="required">
                        @foreach($penempatan as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mt-1">
              <div class="col-lg-10 col-md-12">
                <div class="row">
                  <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                    <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Tambah">
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
          <form action="{{ route('psc.aspek.posisi.perbarui.semua', ['position' => $target->target_position_id]) }}" id="aspek-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Aspek Evaluasi dan IKU</h6>
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
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek Evaluasi dan Indikator Kinerja Utama</th>
                      <th style="min-width: 80px">Bobot</th>
                      <th>Penilai</th>
                      <th style="width: 120px">Aksi</th>
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
                          <input type="number" name="value-{{ $thisPercentage->id }}" class="input-sm form-control" value="{{ $thisPercentage->percentage }}" min="0" max="100" step="1" required="required"/>
                          @else
                          <input type="text" class="input-sm form-control" value="{{ $i->percentage }}" disabled="disabled"/>
                          @endif
                          <div class="input-group-append">
                            <span class="input-group-text">%</span>
                          </div>
                        </div>
                      </td>
                      <td>
                        @if($item->penilai()->count() > 0)
                        {{ implode(', ',$item->penilai()->select('name')->pluck('name')->toArray()) }}
                        @else
                        -
                        @endif
                      </td>
                      <td>
                        @if($thisPercentage)
                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('psc.aspek.posisi.ubah',['position' => $target->target_position_id]) }}','{{ $thisPercentage->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Aspek Evaluasi dan IKU', '{{ addslashes(htmlspecialchars($i->name)) }}', '{{ route('psc.aspek.posisi.hapus', ['position' => $target->target_position_id,'id' => $thisPercentage->id]) }}')"><i class="fas fa-times"></i></a>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endif
                  </tbody>
                </table>
              </div>
            </div>

            <div class="card-footer">
              @php
              $countIndicators = $indicators->filter(function($i)use($target){
                return $i->target()->where('position_id',$target->target_position_id)->count() > 0;
              })->count();
              @endphp
              @if($countIndicators > 0)
              <div class="row">
                <div class="col-12">
                  <div class="text-center">
                    <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </form>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h6 class="modal-title text-white">Ubah Aspek Evaluasi dan IKU</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>
      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
              <h5 class="font-weight-light mb-3">Memuat...</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" style="display: none;">
      </div>
    </div>
  </div>
</div>

@include('template.modal.konfirmasi_hapus')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<script>
		$(document).ready(function () {
			$('input[name="acceptance_status"]').on('change',function(){
				var acceptance_status = $(this).val();
				if(acceptance_status == 'old'){
					$('#oldRow').show();
					$('#newRow').hide();
				}else{
					$('#oldRow').hide();
					$('#newRow').show();
				}
			});
		});
</script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.select2')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.kepegawaian.psc.change-position')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@endsection
