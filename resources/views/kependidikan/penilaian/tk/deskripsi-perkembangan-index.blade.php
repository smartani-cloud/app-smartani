@extends('template.main.master')

@section('title')
{{ $active }}
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
            @if($elementList && count($elementList) > 0)
            @if($editable)
            <div class="alert alert-light mx-3" role="alert">
              <i class="fa fa-info-circle text-info mr-2"></i>Gunakan tag <b>@nama</b>, <b>@capaian</b>, dan <b>@elemen</b> agar deskripsi menjadi lebih spesifik untuk masing-masing peserta didik
            </div>
            <form action="{{ route($route.'.update', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id]) }}" id="desc-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
              {{ csrf_field() }}
            @endif
              <div class="table-responsive">
                  <table class="table align-items-center table-flush">
                      <thead class="thead-light">
                          <tr>
                              <th style="width: 50px">#</th>
                              <th style="width: 25%">Elemen Capaian</th>
                              <th>Capaian Tertinggi</th>
                              <th>Capaian Terendah</th>
                          </tr>
                      </thead>
                      <tbody>
                        @php $no = 1; @endphp
                        @foreach($elementList as $element)
                          <tr>
                              <td>{{ $no++ }}</td>
                              <td>{{ $element->dev_aspect }}</td>
                              <td>
                                @if($editable)
                                <textarea id="maxDesc-{{$element->id}}" class="form-control @error('maxDesc['.$element->id.']') is-invalid @enderror" name="maxDesc[{{$element->id}}]" maxlength="150" rows="3" placeholder="Tuliskan deskripsi capaian pembelajaran tertinggi...">{{ old('maxDesc['.$element->id.']',(isset($data[$element->id]['max']) ? $data[$element->id]['max'] : null)) }}</textarea>
                                @else
                                {{ isset($data[$element->id]['max']) ? $data[$element->id]['max'] : '-' }}
                                @endif
                              </td>
                              <td>
                                @if($editable)
                                <textarea id="minDesc-{{$element->id}}" class="form-control @error('minDesc['.$element->id.']') is-invalid @enderror" name="minDesc[{{$element->id}}]" maxlength="150" rows="3" placeholder="Tuliskan deskripsi capaian pembelajaran terendah...">{{ old('minDesc['.$element->id.']',(isset($data[$element->id]['min']) ? $data[$element->id]['min'] : null)) }}</textarea>
                                @else
                                {{ isset($data[$element->id]['min']) ? $data[$element->id]['min'] : '-' }}
                                @endif
                              </td>
                          </tr>
                        @endforeach
                      </tbody>
                  </table>
              </div>
              <div class="card-footer">
                  @if($editable)
                  <div class="row">
                      <div class="col-12">
                          <div class="text-center">
                              <button id="btnSave" class="btn btn-brand-green-dark" type="submit">Simpan</button>
                          </div>
                      </div>
                  </div>
                  @endif
              </div>
            @if($editable)
            </form>
            @endif
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data elemen capaian pembelajaran yang ditemukan</h6>
            </div>
            <div class="card-footer"></div>
            @endif
        </div>
    </div>
</div>

@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.global.tooltip')
@if($semester && $tingkatList)
@include('template.footjs.kependidikan.change-level')
@endif
@include('template.footjs.keuangan.change-year')
@endsection
