<<<<<<< HEAD
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
        @if($semester || $kelas)
        <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
        @endif
        @if($semester)
        @if($kelasList && $kelas)
        <li class="breadcrumb-item"><a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</li>
        @endif
        @if($kelas)
        <li class="breadcrumb-item active" aria-current="page">{{ $kelas->level->level.' '.$kelas->namakelases->class_name }}</li>
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
@if($kelasList && count($kelasList) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="classOpt" class="form-control-label">Kelas</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Kelas" name="kelas" class="form-control" id="classOpt">
                          @foreach($kelasList->sortBy('levelName')->all() as $k)
                          <option value="{{ $k->id }}" {{ $kelas && $kelas->id == $k->id ? 'selected' : '' }}>{{ $k->levelName }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-class" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
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
                    <label for="classOpt" class="form-control-label">Kelas</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Kelas" name="kelas" class="form-control" id="classOpt" disabled="disabled">
                          <option value="">Belum ada kelas</option>
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
@if($kelas)
@if($mataPelajaranList && count($mataPelajaranList) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="subjectOpt" class="form-control-label">Mata Pelajaran</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Subject" name="subject" class="form-control" id="subjectOpt">
                          @foreach($mataPelajaranList as $m)
                          <option value="{{ $m->id }}" {{ $mataPelajaran && ($mataPelajaran->id == $m->id) ? 'selected' : '' }}>{{ $m->subject_name }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}" id="btn-select-subject" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}">Pilih</a>
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
                    <label for="subjectOpt" class="form-control-label">Mata Pelajaran</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Subject" name="subject" class="form-control" disabled="disabled">
                          <option value="">Belum ada mata pelajaran</option>
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

@if($semester && $kelas && $mataPelajaran)
@if($tpDescs && count($tpDescs) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Tujuan Pembelajaran</h6>
                <div class="m-0 float-right">
                    <button type="button" class="btn btn-brand-green-dark btn-sm btn-collapse" data-toggle="collapse" data-target="#collapseTpDesc" aria-expanded="false" aria-controls="collapseTpDesc">Lihat <i class="fa fa-angle-down ml-1"></i></button>
                </div>
            </div>
            <div class="collapse" id="collapseTpDesc">
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                      @foreach($tpDescs as $t)
                      <li class="list-group-item"><span class="font-weight-bold mr-2">{{ $t->code }}</span>{{ $t->desc }}</li>
                      @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-left-brand-green">
            <div class="card-body">
                <i class="fa fa-exclamation-triangle text-warning mr-2"></i>Sebelum memasukan data nilai, mohon pastikan Anda <b>masih masuk dalam sistem</b> dan mohon untuk melakukan penyimpanan data nilai secara reguler.
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Peserta Didik</h6>
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
                @if(!$tpDescs || ($tpDescs && count($tpDescs) <= 0))
                <div class="alert alert-light" role="alert">
                    <i class="fa fa-exclamation-circle text-danger mr-2"></i><strong>Perhatian!</strong> Deskripsi TP sumatif belum diatur. <strong><a href="{{ route('kependidikan.penilaian.mapel.deskripsi-tps.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $kelas->level_id, 'mataPelajaran' => $mataPelajaran->id]) }}" target="_blank" class="text-info">Atur sekarang</a></strong>
                </div>
                @endif
                @if($riwayatKelas && count($riwayatKelas) > 0)
                <form action="{{ route($route.'.update', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]) }}" id="skill-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                {{ csrf_field() }}
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th>Nama</th>
                                    @if($tpDescs && count($tpDescs) > 0)
                                    @foreach($tpDescs as $t)
                                    <th class="text-center">{{ $t->code }}</th>
                                    @endforeach
                                    @endif
                                    <th class="text-center">NAS</th>
                                    <th class="text-center">NTSS</th>
                                    <th class="text-center">NASS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatKelas as $r)
                                <tr>
                                    <td><label style="width: 150px;">{{ $r->identitas->student_name }}</label></td>
                                    @if($tpDescs && count($tpDescs) > 0)
                                    @foreach($tpDescs as $t)
                                    <td>
                                        <input type="number" class="form-control mx-auto" min="0" max="100" name="s-{{$r->id}}-tp-{{$t->id}}" value="{{ isset($nilai[$r->id][$t->id]) ? $nilai[$r->id][$t->id] : 0 }}" style="width: 70px;">
                                    </td>
                                    @endforeach
                                    @endif
                                    <td>
                                        <input type="text" class="form-control mx-auto" name="s-{{$r->id}}-nas" value="{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nasWithSeparator : 0 }}" style="width: 70px;" disabled>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control mx-auto" name="s-{{$r->id}}-ntss" value="{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->ntssWithSeparator : 0 }}" style="width: 70px;">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control mx-auto" name="s-{{$r->id}}-nass" value="{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nassWithSeparator : 0 }}" style="width: 70px;">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data peserta didik yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@if($semester && $kelasList)
@include('template.footjs.kependidikan.change-class')
@endif
@if($kelas && $mataPelajaranList)
@include('template.footjs.kependidikan.change-subject')
@endif
@include('template.footjs.keuangan.change-year')
<script>
$('.btn-collapse').on('click', function(e) {
    if($(this).attr('aria-expanded') == 'true'){
        $(this).html('Lihat <i class="fa fa-angle-down ml-1"></i>');
    }
    else if($(this).attr('aria-expanded') == 'false'){ 
        $(this).html('Tutup <i class="fa fa-angle-up ml-1"></i>');
    }
});
</script>
@endsection
=======
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
        @if($semester || $kelas)
        <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
        @endif
        @if($semester)
        @if($kelasList && $kelas)
        <li class="breadcrumb-item"><a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</li>
        @endif
        @if($kelas)
        <li class="breadcrumb-item active" aria-current="page">{{ $kelas->level->level.' '.$kelas->namakelases->class_name }}</li>
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
@if($kelasList && count($kelasList) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="classOpt" class="form-control-label">Kelas</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Kelas" name="kelas" class="form-control" id="classOpt">
                          @foreach($kelasList->sortBy('levelName')->all() as $k)
                          <option value="{{ $k->id }}" {{ $kelas && $kelas->id == $k->id ? 'selected' : '' }}>{{ $k->levelName }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-class" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
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
                    <label for="classOpt" class="form-control-label">Kelas</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Kelas" name="kelas" class="form-control" id="classOpt" disabled="disabled">
                          <option value="">Belum ada kelas</option>
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
@if($kelas)
@if($mataPelajaranList && count($mataPelajaranList) > 0)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="subjectOpt" class="form-control-label">Mata Pelajaran</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Subject" name="subject" class="form-control" id="subjectOpt">
                          @foreach($mataPelajaranList as $m)
                          <option value="{{ $m->id }}" {{ $mataPelajaran && ($mataPelajaran->id == $m->id) ? 'selected' : '' }}>{{ $m->subject_name }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}" id="btn-select-subject" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}">Pilih</a>
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
                    <label for="subjectOpt" class="form-control-label">Mata Pelajaran</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        <select aria-label="Subject" name="subject" class="form-control" disabled="disabled">
                          <option value="">Belum ada mata pelajaran</option>
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

@if($semester && $kelas && $mataPelajaran)
@if($tpDescs && count($tpDescs) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Tujuan Pembelajaran</h6>
                <div class="m-0 float-right">
                    <button type="button" class="btn btn-brand-green-dark btn-sm btn-collapse" data-toggle="collapse" data-target="#collapseTpDesc" aria-expanded="false" aria-controls="collapseTpDesc">Lihat <i class="fa fa-angle-down ml-1"></i></button>
                </div>
            </div>
            <div class="collapse" id="collapseTpDesc">
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush">
                      @foreach($tpDescs as $t)
                      <li class="list-group-item"><span class="font-weight-bold mr-2">{{ $t->code }}</span>{{ $t->desc }}</li>
                      @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-left-brand-green">
            <div class="card-body">
                <i class="fa fa-exclamation-triangle text-warning mr-2"></i>Sebelum memasukan data nilai, mohon pastikan Anda <b>masih masuk dalam sistem</b> dan mohon untuk melakukan penyimpanan data nilai secara reguler.
            </div>
        </div>
    </div>
</div>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Peserta Didik</h6>
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
                @if(!$tpDescs || ($tpDescs && count($tpDescs) <= 0))
                <div class="alert alert-light" role="alert">
                    <i class="fa fa-exclamation-circle text-danger mr-2"></i><strong>Perhatian!</strong> Deskripsi TP sumatif belum diatur. <strong><a href="{{ route('kependidikan.penilaian.mapel.deskripsi-tps.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $kelas->level_id, 'mataPelajaran' => $mataPelajaran->id]) }}" target="_blank" class="text-info">Atur sekarang</a></strong>
                </div>
                @endif
                @if($riwayatKelas && count($riwayatKelas) > 0)
                <form action="{{ route($route.'.update', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]) }}" id="skill-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                {{ csrf_field() }}
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th>Nama</th>
                                    @if($tpDescs && count($tpDescs) > 0)
                                    @foreach($tpDescs as $t)
                                    <th class="text-center">{{ $t->code }}</th>
                                    @endforeach
                                    @endif
                                    <th class="text-center">NAS</th>
                                    <th class="text-center">NTSS</th>
                                    <th class="text-center">NASS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatKelas as $r)
                                <tr>
                                    <td><label style="width: 150px;">{{ $r->identitas->student_name }}</label></td>
                                    @if($tpDescs && count($tpDescs) > 0)
                                    @foreach($tpDescs as $t)
                                    <td>
                                        <input type="number" class="form-control mx-auto" min="0" max="100" name="s-{{$r->id}}-tp-{{$t->id}}" value="{{ isset($nilai[$r->id][$t->id]) ? $nilai[$r->id][$t->id] : 0 }}" style="width: 70px;">
                                    </td>
                                    @endforeach
                                    @endif
                                    <td>
                                        <input type="text" class="form-control mx-auto" name="s-{{$r->id}}-nas" value="{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nasWithSeparator : 0 }}" style="width: 70px;" disabled>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control mx-auto" name="s-{{$r->id}}-ntss" value="{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->ntssWithSeparator : 0 }}" style="width: 70px;">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control mx-auto" name="s-{{$r->id}}-nass" value="{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nassWithSeparator : 0 }}" style="width: 70px;">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data peserta didik yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@if($semester && $kelasList)
@include('template.footjs.kependidikan.change-class')
@endif
@if($kelas && $mataPelajaranList)
@include('template.footjs.kependidikan.change-subject')
@endif
@include('template.footjs.keuangan.change-year')
<script>
$('.btn-collapse').on('click', function(e) {
    if($(this).attr('aria-expanded') == 'true'){
        $(this).html('Lihat <i class="fa fa-angle-down ml-1"></i>');
    }
    else if($(this).attr('aria-expanded') == 'false'){ 
        $(this).html('Tutup <i class="fa fa-angle-up ml-1"></i>');
    }
});
</script>
@endsection
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
