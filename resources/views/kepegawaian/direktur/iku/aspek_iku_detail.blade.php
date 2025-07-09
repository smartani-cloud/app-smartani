@extends('template.main.master')

@section('title')
Atur Aspek IKU
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
    <h1 class="h3 mb-0 text-gray-800">Atur Aspek IKU</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.index') }}">Indikator Kinerja Utama</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.aspek.index') }}">Atur Aspek IKU</a></li>
        <li class="breadcrumb-item"><a href="{{ route('iku.aspek.index',['iku' => $iku->nameLc]) }}">{{ $iku->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $unitAktif->name }}</li>
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
                    <label for="categoryOpt" class="form-control-label">IKU</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="input-group">
                      <select aria-label="IKU" name="iku" class="form-control" id="categoryOpt" required="required">
                        @foreach($categories as $c)
                        <option value="{{ $c->nameLc }}" {{ $c->name == $iku->name ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                      </select>
                      <a href="{{ route('iku.aspek.index') }}" id="btn-select-category" class="btn btn-brand-purple ml-2 pt-2" data-href="{{ route('iku.aspek.index') }}">Pilih</a>
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

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-school text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Unit</div>
                        <h6 class="mb-0">{{ $unitAktif->name }}</h6>
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
                <h6 class="m-0 font-weight-bold text-brand-purple">Aspek IKU {{ $iku->name }}</h6>
                @php
                $indicatorCount = $aspectUnits->filter(function($value, $key){
                  return $value->indikator()->where(function($query){
                    $query->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                  })->count() > 0;
                })->count();
                @endphp
                @if($indicatorCount > 0)
                <!-- <div class="m-0 float-right">
                  <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#acceptAll">Setujui Semua <i class="fas fa-check ml-1"></i></button>
                </div> -->
                @endif
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
              @if($aspectUnits && count($aspectUnits) > 0)
              <div class="table-responsive">
                <table id="ikuIndicatorTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Aspek</th>
                      <th>Indikator Kinerja Utama</th>
                      <th>Objek</th>
                      <th>Alat Ukur</th>
                      <th>Target</th>
                      <th>Status</th>
                      <th style="width: 120px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($aspectUnits as $a)
                    @if($a->indikator()->count() > 0)
                    @foreach($a->indikator as $i)
                    <tr id="i-{{ $i->id }}">
                      <td>{{ $no++ }}</td>
                      <td class="detail-aspect" data-aspect="{{ $i->aspek->id }}">{{ $i->aspek->aspek->name }}</td>
                      <td class="detail-name">{{ $i->name }}</td>
                      <td class="detail-object">{{ $i->object }}</td>
                      <td class="detail-mt">{{ $i->mt }}</td>
                      <td class="detail-target">{{ $i->target }}</td>
                      <td>
                        @if($i->director_acc_status_id == 1)
                        <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($i->accDirektur) ? 'Anda' : $i->accDirektur->name }}<br>{{ date('j M Y H.i.s', strtotime($i->director_acc_time)) }}"></i>
                        @else
                        <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan {{ Auth::user()->pegawai->jabatan->name == 'Director' ? 'Anda' : 'Direktur' }}"></i>
                        @endif
                      </td>
                      <td>
                        @if(!$i->director_acc_status_id)
                        <a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#validate-confirm" onclick="validateModal('{{ addslashes(htmlspecialchars($i->name)) }}', '{{ route('iku.aspek.setujui', ['iku' => $iku->nameLc, 'unit' => $unitAktif->name,'id' => $i->id]) }}')"><i class="fas fa-check"></i></a>
                        @else
                        <span class="badge badge-success font-weight-normal">Telah Disetujui</span>
                        @endif
                      </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data IKU yang ditemukan</h6>
              </div>
              <div class="card-footer"></div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

@include('template.modal.iku_indikator_validasi')

@if($indicatorCount > 0)
<div class="modal fade" id="acceptAll" tabindex="-1" role="dialog" aria-labelledby="setujuiModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-success">
          <i class="material-icons text-success">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menyetujui semua IKU yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <form action="{{ route('iku.aspek.setujui.semua', ['iku' => $iku->nameLc, 'unit' => $unitAktif->name]) }}" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endif

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.iku.change-category')
@include('template.footjs.modal.get_iku_indicator_validate')
@endsection
