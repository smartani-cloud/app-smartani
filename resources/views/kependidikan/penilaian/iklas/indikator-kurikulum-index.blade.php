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
        @if($semester || $tingkat)
        <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
        @endif
        @if($semester)
        @if($tingkatList && $tingkat)
        <li class="breadcrumb-item"><a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</li>
        @endif
        @if($tingkat)
        <li class="breadcrumb-item active" aria-current="page">{{ $tingkat->level }}</li>
        @endif
        @endif
    </ol>
</div>

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
                    <a href="{{ route($route.'.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index') }}">Atur</a>
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
@if($semester)
@if($tingkatList && count($tingkatList) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="levelOpt" class="form-control-label">Tingkat Kelas</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Level" name="level" class="form-control" id="levelOpt">
                          @foreach($tingkatList as $t)
                          <option value="{{ $t->id }}" {{ $tingkat && ($tingkat->id == $t->id) ? 'selected' : '' }}>{{ $t->level }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-level" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
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
                    <label for="levelOpt" class="form-control-label">Tingkat Kelas</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Level" name="level" class="form-control" id="levelOpt" disabled="disabled">
                          <option value="">Belum ada tingkat kelas</option>
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

@if($semester && $tingkat)
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
                    <input type="radio" id="existOpt1" name="existOpt" class="custom-control-input" value="true" required="required" checked>
                    <label class="custom-control-label" for="existOpt1">Sudah Ada</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="existOpt2" name="existOpt" class="custom-control-input" value="false" required="required">
                    <label class="custom-control-label" for="existOpt2">Baru</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="existRow">
          <form action="{{ route($route.'.relate',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]) }}" id="existing-indicator-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Competence" class="form-control-label">Kompetensi</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @if($competencies && count($competencies) > 0)
                    <select class="select2 form-control @error('competence') is-invalid @enderror" name="competence" id="select2Competence" required="required">
                      @foreach($competencies as $c)
                      <option value="{{ $c->id }}">{{ $c->competenceNumber.' '.$c->competence->name }}</option>
                      @endforeach
                    </select>
                    @else
                    <select aria-label="Competence" name="competence" class="form-control" id="select2Competence" disabled="disabled">
                      <option value="">Belum ada kategori</option>
                    </select>
                    @endif
                    @error('competence')
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
                    <label for="select2Indicator" class="form-control-label">Indikator</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @if($indicatorList && count($indicatorList) > 0)
                    <select class="select2 form-control form-control-sm @error('indicator') is-invalid @enderror" name="indicator" id="select2Indicator" required="required">
                      @foreach($indicatorList as $indicator)
                      <option value="{{ $indicator->id }}">{{ $indicator->indicator }}</option>
                      @endforeach
                    </select>
                    @else
                    <select aria-label="Indicator" name="indicator" class="form-control" id="select2Indicator" disabled="disabled">
                      <option value="">Belum ada indikator</option>
                    </select>
                    @endif
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
                  @if($indicatorList && count($indicatorList) > 0)
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
        <div id="newRow" style="display: none;">
          <form action="{{ route($route.'.store',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]) }}" id="new-indicator-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
            {{ csrf_field() }}
            <div class="row">
              <div class="col-lg-10 col-md-12">
                <div class="form-group">
                  <div class="row mb-3">
                    <div class="col-lg-3 col-md-4 col-12">
                      <label for="select2NewCompetence" class="form-control-label">Kompetensi</label>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                      @if($competencies && count($competencies) > 0)
                      <select class="select2 form-control @error('competence') is-invalid @enderror" name="competence" id="select2NewCompetence" required="required">
                        @foreach($competencies as $c)
                        <option value="{{ $c->id }}">{{ $c->competenceNumber.' '.$c->competence->name }}</option>
                        @endforeach
                      </select>
                      @else
                      <select aria-label="Competence" name="competence" class="form-control" id="select2NewCompetence" disabled="disabled">
                        <option value="">Belum ada kompetensi</option>
                      </select>
                      @endif
                      @error('competence')
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
                      <label for="name" class="form-control-label">Indikator</label>
                    </div>
                    <div class="col-lg-9 col-md-8 col-12">
                      <input type="text" class="form-control form-control-sm @error('name') is-invalid @enderror" name="name" maxlength="100" required="required"/>
                      @error('name')
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
            @if($competencies && count($competencies) > 0)
            <div class="table-responsive">
              <table class="table align-items-center" style="width:100%">
                <thead class="thead-light">
                  <tr>
                    <th>Kompetensi</th>
                    <th>Indikator</th>
                    @if(!$isReadOnly)
                    <th style="width: 120px">Aksi</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @php $catActive = null; @endphp
                  @foreach($competencies as $c)
                  @if($catActive != $c->category_id)
                  <tr>
                    <td colspan="{{ $isReadOnly ? 2 : 3 }}"><strong>{{ $c->category->number.'. '.$c->category->name }}</strong></td>
                  </tr>
                  @endif
                  @if($data[$c->id] && count($data[$c->id]) > 0)
                  @php $i = 1; @endphp
                  @foreach($data[$c->id] as $d)
                  <tr>
                    @if($i++ == 1)
                    <td {!! count($data[$c->id]) > 1 ? 'rowspan="'.count($data[$c->id]).'"' : null !!}">{{ $c->competenceNumber.' '.$c->competence->name }}</td>
                    @endif
                    <td>{{ $d->indicator->indicator }}</td>
                    <td>
                      @if(!$isReadOnly)
                      <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('{{ $active }}', '{{ addslashes(htmlspecialchars($d->indicator->indicator)) }}', '{{ route($route.'.destroy', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id, 'id' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                      @endif
                    </td>
                  </tr>
                  @endforeach
                  @else
                  <tr>
                    <td>{{ $c->competenceNumber.' '.$c->competence->name }}</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  @endif
                  @php if($catActive != $c->category_id) $catActive = $c->category_id @endphp
                  @endforeach
                </tbody>
              </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
              <h3 class="text-center">Mohon Maaf,</h3>
              <h6 class="font-weight-light mb-3">Tidak ada data kompetensi IKLaS yang ditemukan</h6>
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
@if($semester && $tingkatList)
@include('template.footjs.kependidikan.change-level')
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
