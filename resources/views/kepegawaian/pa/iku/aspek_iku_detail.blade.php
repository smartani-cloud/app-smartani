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
                      <a href="{{ route('iku.aspek.index') }}" id="btn-select-category" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('iku.aspek.index') }}">Pilih</a>
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
        <h6 class="m-0 font-weight-bold text-brand-green">Tambah Aspek IKU {{ $iku->name }}</h6>
      </div>
      <div class="card-body pt-2 pb-3 px-4">
        <form action="{{ route('iku.aspek.simpan',['iku' =>$iku->nameLc, 'unit' => $unitAktif->name]) }}" id="aspek-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Aspect" class="form-control-label">Aspek</label>
                  </div>
                  <div class="col-lg-7 col-md-8 col-12">
                    @if(count($aspects) > 0)
                    <select class="select2 form-control form-control-sm @error('aspect') is-invalid @enderror" name="aspect" id="select2Aspect" required="required">
                      @foreach($aspects as $a)
                      <option value="{{ $a->id }}">{{ $a->aspek->name }}</option>
                      @endforeach
                    </select>
                    @error('aspect')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @else
                    <select class="form-control form-control-sm" required="required" disabled="disabled">
                      <option selected="selected">Belum ada aspek</option>
                    </select>
                    @endif
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
                    <label class="form-control-label">Indikator Kinerja Utama</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" name="name" class="form-control form-control-sm">
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
                    <label class="form-control-label">Objek</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <input type="text" name="object" class="form-control form-control-sm">
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
                    <label class="form-control-label">Alat Ukur</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <input type="text" name="mt" class="form-control form-control-sm">
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
                    <label class="form-control-label">Target</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" name="target" class="form-control form-control-sm">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      @if(count($aspects) > 0)
                      <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Tambah">
                      @else
                      <button type="button" class="btn btn-sm btn-secondary" disabled="disabled">Tambah</button>
                      @endif
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
                <h6 class="m-0 font-weight-bold text-brand-green">Aspek IKU {{ $iku->name }}</h6>
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
              @if(count($aspectUnits) > 0)
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
                    @if($i->director_acc_status_id != 1)
                    <tr id="i-{{ $i->id }}">
                      <td>{{ $no++ }}</td>
                      <td class="detail-aspect" data-aspect="{{ $i->aspek->id }}">{{ $i->aspek->aspek->name }}</td>
                      <td class="detail-name">{{ $i->name }}</td>
                      <td class="detail-object">{{ $i->object }}</td>
                      <td class="detail-mt">{{ $i->mt }}</td>
                      <td class="detail-target">{{ $i->target }}</td>
                    @else
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $i->aspek->aspek->name }}</td>
                      <td>{{ $i->name }}</td>
                      <td>{{ $i->object }}</td>
                      <td>{{ $i->mt }}</td>
                      <td>{{ $i->target }}</td>
                    @endif
                      <td>
                        @if($i->director_acc_status_id == 1)
                        <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($i->accDirektur) ? 'Anda' : $i->accDirektur->name }}<br>{{ date('j M Y H.i.s', strtotime($i->director_acc_time)) }}"></i>
                        @else
                        <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Persetujuan Direktur"></i>
                        @endif
                      </td>
                      <td>
                        @if($i->director_acc_status_id == 1)
                        <button type="button" class="btn btn-sm btn-secondary" disabled="disabled">
                          <i class="fa fa-pen"></i>
                        </button>
                        @else
                        <button type="button" class="btn btn-sm btn-warning btn-edit" data-toggle="modal" data-target="#edit-form">
                          <i class="fa fa-pen"></i>
                        </button>
                        @endif
                        <a href="javascript:void(0)" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Aspek IKU', '{{ addslashes(htmlspecialchars($i->name)) }}', '{{ route('iku.aspek.hapus', ['iku' => $iku->nameLc, 'unit' => $unitAktif->name,'id' => $i->id]) }}')"><i class="fas fa-times"></i></a>
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

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h6 class="modal-title text-white">Ubah Indikator Kinerja Utama</h6>
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
        <form action="{{ route('iku.aspek.ubah', ['iku' => $iku->nameLc, 'unit' => $unitAktif->name]) }}" id="editIndikatorForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input type="hidden" name="editId">
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2EditAspect" class="form-control-label">Aspek</label>
                  </div>
                  <div class="col-lg-7 col-md-8 col-12">
                    @if(count($aspects) > 0)
                    <select class="select2 form-control form-control-sm @error('aspect') is-invalid @enderror" name="editAspect" id="select2EditAspect" required="required">
                      @foreach($aspects as $a)
                      <option value="{{ $a->id }}">{{ $a->aspek->name }}</option>
                      @endforeach
                    </select>
                    @error('aspect')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @else
                    <select class="form-control form-control-sm" required="required" disabled="disabled">
                      <option selected="selected">Belum ada aspek</option>
                    </select>
                    @endif
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
                    <label class="form-control-label">Indikator Kinerja Utama</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" name="editName" class="form-control form-control-sm">
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
                    <label class="form-control-label">Objek</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <input type="text" name="editObject" class="form-control form-control-sm">
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
                    <label class="form-control-label">Alat Ukur</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <input type="text" name="editMt" class="form-control form-control-sm">
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
                    <label class="form-control-label">Target</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" name="editTarget" class="form-control form-control-sm">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Simpan">
                    </div>
                </div>
            </div>
          </div>
        </form>
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

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.select2')
@include('template.footjs.kepegawaian.iku.change-category')
@include('template.footjs.modal.get_delete')
@include('template.footjs.modal.iku_indicator_edit')
@endsection
