@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        @if($unit || $semester || $tingkat)
        <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
        @endif
		@if($unit)
		@if($semesterList && $semester)
		<li class="breadcrumb-item"><a href="{{ route($route.'.index', ['unit' => $unit->name]) }}">{{ $unit->name }}</a></li>
		@else
		<li class="breadcrumb-item active" aria-current="page">{{ $unit->name }}</li>
		@endif
        @if($semester)
        <li class="breadcrumb-item active" aria-current="page">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</li>
        @endif
		@endif
    </ol>
</div>

@if($unitList && count($unitList) > 0)
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
                  <div class="col-lg-8 col-md-8 col-12">
                    @if(in_array(Auth::user()->role->name,['kepsek','wakasek','guru']))
                    <input type="text" class="form-control" value="{{ $unit->name }}" disabled>
                    @else
                    <div class="input-group">
                    <select aria-label="Unit" name="unit" class="form-control" id="unitOpt">
                      @foreach($unitList as $u)
                      <option value="{{ $u->name }}" {{ $unit && $unit->id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                      @endforeach
                    </select>
                    <a href="{{ route($route.'.index') }}" id="btn-select-unit" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index') }}">Pilih</a>
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
@else
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
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                      <select aria-label="Unit" name="unit" class="form-control" id="unitOpt" disabled="disabled">
                        <option value="">Belum ada unit</option>
                      </select>
                      <button class="btn btn-secondary ml-2 pt-2" disabled="disabled">Pilih</button>
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
@if($unit)
@if($semesterList && count($semesterList) > 0)
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
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @foreach($semesterList as $s)
                      <option value="{{ $s->semesterLink }}" {{ $semester && $semester->id == $s->id ? 'selected' : '' }}>{{ $s->semester_id . ' (' .$s->semester.')' }}</option>
                      @endforeach
                    </select>
                    <a href="{{ route($route.'.index', ['unit' => $unit->name]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index', ['unit' => $unit->name]) }}">Atur</a>
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
@else
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
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                      <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt" disabled="disabled">
                        <option value="">Belum ada tahun pelajaran</option>
                      </select>
                      <button class="btn btn-secondary ml-2 pt-2" disabled="disabled">Pilih</button>
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
@endif

@if($unit && $semester)
@if(!$isReadOnly)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">Tambah {{ $active }}</h6>
      </div>
      <div class="card-body pt-2 pb-3 px-4">
        <div class="row">
          <div class="col-lg-10 col-md-12">
            <div class="form-group">
              <div class="row mb-3">
                <div class="col-lg-3 col-md-4 col-12">
                  <label for="existingOpt" class="form-control-label">Pilihan</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="existOpt1" name="existOpt" class="custom-control-input" value="true" required="required" {{ $bookList && count($bookList) > 0 ? 'checked' : 'disabled' }}>
                    <label class="custom-control-label" for="existOpt1">Sudah Ada</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="existOpt2" name="existOpt" class="custom-control-input" value="false" required="required" {{ !$bookList || ($bookList && count($bookList) < 1) ? 'checked' : null }}>
                    <label class="custom-control-label" for="existOpt2">Baru</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="existRow" {!! $bookList && count($bookList) > 0 ? null : 'style="display: none;"' !!}>
          <form action="{{ route($route.'.relate',['unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="existing-competence-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Book" class="form-control-label">Judul Buku</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @if($bookList && count($bookList) > 0)
                    <select class="select2 form-control form-control-sm @error('book') is-invalid @enderror" name="book" id="select2Book" required="required">
                      @foreach($bookList as $book)
                      <option value="{{ $book->id }}">{{ $book->titleWithPages }}</option>
                      @endforeach
                    </select>
                    @else
                    <select aria-label="Book" name="book" class="form-control" id="select2Book" disabled="disabled">
                      <option value="">Belum ada buku</option>
                    </select>
                    @endif
                    @error('book')
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
                  @if($bookList && count($bookList) > 0)
                  <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Masukkan ke Daftar {{ $active }}">
                  @else
                  <button class="btn btn-sm btn-secondary" disabled="disabled">Masukkan ke Daftar {{ $active }}</button>
                  @endif
                </div>
              </div>
            </div>
          </div>
          </form>
        </div>
        <div id="newRow" {!! $bookList && count($bookList) > 0 ? 'style="display: none;"' : null !!}>
          <form action="{{ route($route.'.store',['unit' => $unit->name, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="new-competence-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="form-group">
                  <div class="row mb-3">
                    <div class="col-lg-3 col-md-4 col-12">
                      <label for="title" class="form-control-label">Judul Buku <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                      <input type="text" class="form-control form-control-sm @error('title') is-invalid @enderror" name="title" maxlength="150" required="required"/>
                      @error('title')
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
                      <label for="pages" class="form-control-label">Jumlah Halaman</label>
                    </div>
                    <div class="col-lg-2 col-md-3 col-xs-6 col-12">
                      <input type="number" class="form-control form-control-sm @error('pages') is-invalid @enderror" name="pages" min="1"/>
                      @error('pages')
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

@endif
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6>
            </div>
            @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
              <strong>Sukses!</strong> {{ Session::get('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            @if(Session::has('danger'))
            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
              <strong>Gagal!</strong> {{ Session::get('danger') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            @if($data && count($data) > 0)
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Judul Buku</th>
                            <th>Jumlah Halaman</th>
                            @if(!$isReadOnly)
                            <th style="width: 120px">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                      @php $no = 1; @endphp
                      @foreach($data as $d)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $d->buku->title }}</td>
                            <td>{{ $d->buku->total_pages ? $d->buku->total_pages : '-' }}</td>
                            @if(!$isReadOnly)
                            <td>
                              @if($used && $used[$d->id] < 1)
                              <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{{ addslashes(htmlspecialchars($d->buku->title)) }}', '{{ route($route.'.destroy', ['unit' => $unit->id, 'tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                              @else
                              <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                              @endif
                            </td>
                            @endif
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($active) }} yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>

@if(!$isReadOnly)
@include('template.modal.konfirmasi_hapus')

@endif
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.tooltip')
@if(!$isReadOnly)
@include('template.footjs.global.select2')
@endif
@include('template.footjs.modal.get_delete')
@if(!in_array(Auth::user()->role->name,['kepsek','wakasek','guru']))
@include('template.footjs.kependidikan.change-unit')
@endif
@include('template.footjs.keuangan.change-year')
@if(!$isReadOnly)
@include('template.footjs.modal.get_delete')
<script>
    $(document).ready(function () {
      $('input[name="existOpt"]').on('change',function(){
        var existOpt = $(this).val();
        if(existOpt == 'true'){
          $('#existRow').show();
          $('#newRow').hide();
        }else{
          $('#existRow').hide();
          $('#newRow').show();
        }
      });
    });
</script>
@endif
@endsection
