@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('title')
Daftar {{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Daftar {{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">Daftar {{ $active }}</li>
  </ol>
</div>

@if($categories && count($categories) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-purple-dark">Tambah</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store') }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="selectType" class="form-control-label">Jenis Tanaman</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <select class="select2 form-control form-control-sm @error('type') is-invalid @enderror" name="type" id="selectType" required="required">
                      @foreach($types as $t)
                      <option value="{{ $t->id }}" {{ old('type') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                      @endforeach
                    </select>
                    @error('type')
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
                    <label for="normal-input" class="form-control-label">Nama</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" id="name" class="form-control form-control-sm @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" maxlength="255" required="required">
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
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Nama Ilmiah</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input type="text" id="scientific_name" class="form-control form-control-sm @error('scientific_name') is-invalid @enderror" name="scientific_name" value="{{ old('scientific_name') }}" maxlength="255">
                    @error('scientific_name')
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
                    <label for="normal-input" class="form-control-label">Durasi Siklus Tanam</label>
                  </div>
                  <div class="col-lg-2 col-md-4 col-6">
                    <div class="input-group input-group-sm">
                      <input type="text" id="growth_cycle_days" class="form-control form-control-sm @error('growth_cycle_days') is-invalid @enderror number-separator" name="growth_cycle_days" value="{{ old('growth_cycle_days') }}" maxlength="10" required="required">
                      <div class="input-group-append">
                        <span class="input-group-text">hari</span>
                      </div>
                    </div>
                    @error('growth_cycle_days')
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
                    <label for="normal-input" class="form-control-label">Jumlah Panen per Lubang</label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-12">
                    <div class="input-daterange input-group input-group-sm" id="yield_per_hole_range">
                      <input type="text" id="yield_per_hole_min" class="form-control form-control-sm @error('yield_per_hole_min') is-invalid @enderror number-separator" name="yield_per_hole_min" placeholder="Min" value="{{ old('yield_per_hole_min') }}" maxlength="10" required="required"/>
                      <div class="input-group-prepend">
                        <span class="input-group-text">-</span>
                      </div>
                      <input type="text" id="yield_per_hole_max" class="form-control form-control-sm @error('yield_per_hole_max') is-invalid @enderror number-separator" name="yield_per_hole_max" placeholder="Maks" value="{{ old('yield_per_hole_max') }}" maxlength="10" required="required"/>
                    </div>
                    @if($errors->hasAny(['yield_per_hole_min', 'yield_per_hole_max']))
                    <span class="text-danger">{{ implode(', ', $errors->only(['yield_per_hole_min', 'yield_per_hole_max'])) }}</span>
                    @endif
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
                    <label for="normal-input" class="form-control-label">Berat per Buah (gram)</label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-12">
                    <div class="input-daterange input-group input-group-sm" id="fruit_weight_range">
                      <input type="text" id="fruit_weight_min_g" class="form-control form-control-sm @error('fruit_weight_min_g') is-invalid @enderror number-separator" name="fruit_weight_min_g" placeholder="Min" value="{{ old('fruit_weight_min_g') }}" maxlength="10" required="required"/>
                      <div class="input-group-prepend">
                        <span class="input-group-text">-</span>
                      </div>
                      <input type="text" id="fruit_weight_max_g" class="form-control form-control-sm @error('fruit_weight_max_g') is-invalid @enderror number-separator" name="fruit_weight_max_g" placeholder="Maks" value="{{ old('fruit_weight_max_g') }}" maxlength="10" required="required"/>
                    </div>
                    @if($errors->hasAny(['fruit_weight_min_g', 'fruit_weight_max_g']))
                    <span class="text-danger">{{ implode(', ', $errors->only(['fruit_weight_min_g', 'fruit_weight_max_g'])) }}</span>
                    @endif
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
                    <label for="normal-input" class="form-control-label">Frekuensi Penyiraman per Hari</label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-12">
                    <div class="input-daterange input-group input-group-sm" id="daily_watering_range">
                      <input type="text" id="daily_watering_min" class="form-control form-control-sm @error('daily_watering_min') is-invalid @enderror number-separator" name="daily_watering_min" placeholder="Min" value="{{ old('daily_watering_min') }}" maxlength="10" required="required"/>
                      <div class="input-group-prepend">
                        <span class="input-group-text">-</span>
                      </div>
                      <input type="text" id="daily_watering_max" class="form-control form-control-sm @error('daily_watering_max') is-invalid @enderror number-separator" name="daily_watering_max" placeholder="Maks" value="{{ old('daily_watering_max') }}" maxlength="10" required="required"/>
                    </div>
                    @if($errors->hasAny(['daily_watering_min', 'daily_watering_max']))
                    <span class="text-danger">{{ implode(', ', $errors->only(['daily_watering_min', 'daily_watering_max'])) }}</span>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      <input type="submit" class="btn btn-sm btn-brand-purple" value="Tambah">
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endif
<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-purple-dark">{{ $active }}</h6>
      </div>
      @if(count($data) > 0)
      <div class="card-body">
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
        @if($errors->has('editCategory') || $errors->has('editName'))
        <div class="alert alert-danger">
          <ul class="mb-0">
            @if($errors->has('editCategory'))
            <li>{{ $errors->first('editCategory') }}</li>
            @endif
            @if($errors->has('editName'))
            <li>{{ $errors->first('editName') }}</li>
            @endif
          </ul>
        </div>
        @endif
        <div class="table-responsive">
          <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Nama</th>
                <th>Nama Ilmiah</th>
                <th>Durasi Siklus Tanam (hari)</th>
                <th>Jumlah Panen per Lubang</th>
                <th>Berat per Buah (gram)</th>
                <th>Frekuensi Penyiraman per Hari</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php $no = 1; @endphp
              @foreach($data as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->type && $d->type->category ? $d->type->category->name : '-' }}</td>
                <td>{{ $d->type ? $d->type->name : '-' }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->scientific_name ?? '-' }}</td>
                <td>{{ $d->growth_cycle_days }}</td>
                <td>{{ $d->yield_per_hole_min }} - {{ $d->yield_per_hole_max }}</td>
                <td>{{ $d->fruit_weight_min_g }} - {{ $d->fruit_weight_max_g }}</td>
                <td>{{ $d->daily_watering_min }} - {{ $d->daily_watering_max }}</td>
                <td>
                  <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.edit') }}','{{ $d->id }}')"><i class="fas fa-pen"></i></a>
                  @if($used && $used[$d->id] < 1)
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{!! addslashes(htmlspecialchars($d->name)) !!}', '{{ route($route.'.destroy', ['id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                  @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @else
      @if(Session::has('success'))
      <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <strong>Sukses!</strong> {{ Session::get('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      @if(Session::has('danger'))
      <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <strong>Gagal!</strong> {{ Session::get('danger') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($active) }} yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
<!--Row-->

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-purple border-0">
        <h5 class="modal-title text-white">Ubah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-purple"></i>
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

@include('template.modal.delete-confirm')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.datepicker')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@endsection