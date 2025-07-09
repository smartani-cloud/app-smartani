<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Nilai Hafalan
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nilai Hafalan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        @if($semester || $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.hafalan.index') }}">Nilai Hafalan</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">Nilai Hafalan</li>
        @endif
        @if($semester)
        @if($kelasList && $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</li>
        @endif
        @if($kelas)
        <li class="breadcrumb-item active" aria-current="page">{{ $kelas->level->level.' '.$kelas->namakelases->class_name }}</li>
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
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @foreach($semesterList as $s)
                      @if($s->is_active == 1 || ($s->is_active != 1 && $s->riwayatKelas()->count() > 0))
                      <option value="{{ $s->semesterLink }}" {{ $semester->id == $s->id ? 'selected' : '' }}>{{ $s->semester_id . ' (' .$s->semester.')' }}</option>
                      @endif
                      @endforeach
                    </select>
                    <a href="{{ route('penilaian.hafalan.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.hafalan.index') }}">Atur</a>
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
                    <label for="classOpt" class="form-control-label">Kelas</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    @if(Auth::user()->role->name == 'guru')
                    {{ $kelas->levelName }}
                    @else
                    <div class="input-group">
                        <select aria-label="Kelas" name="kelas" class="form-control" id="classOpt">
                          @foreach($kelasList->sortBy('levelName')->all() as $k)
                          <option value="{{ $k->id }}" {{ $kelas && $kelas->id == $k->id ? 'selected' : '' }}>{{ $k->levelName }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-class" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
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
@if($kelas)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="studentOpt" class="form-control-label">Siswa</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        @php
                        $raporCount = 0;
                        @endphp
                        @if($riwayatKelas && count($riwayatKelas) > 0)
                        <select aria-label="Siswa" name="siswa" class="form-control" id="studentOpt">
                          @foreach($riwayatKelas as $r)
                          @php
                          $s = $r->siswa()->select('id','student_id')->first();
                          @endphp
                          <option value="{{ $s->id }}" {{ $siswa && $siswa->id == $s->id ? 'selected' : '' }}>{{ $s->identitas->student_name }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}" id="btn-select-student" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}">Lihat</a>
                        @else
                        <input type="text" class="form-control" value="Tidak ada data siswa" disabled>
                        <button type="button" class="btn btn-secondary ml-2 pt-2" disabled="disabled">Lihat</button>
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
</div>
@endif

@if($semester && $kelas && $siswa)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Hafalan</h6>
                <div class="m-0 float-right">
                <button type="button" class="btn btn-brand-green-dark btn-sm" id="tambahhafalan">Tambah Surah <i class="fa fa-plus ml-1"></i></button>
                </div>
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
                <form action="{{ route('penilaian.hafalan.perbarui', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->id]) }}" id="tilawah-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th class="text-center" width="75%" colspan="3">Nama Surat</th>
                                    <th class="text-center" width="25%">Predikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $rapor = $siswa->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                $tahfidz = $rapor && $rapor->tahfidz ? $rapor->tahfidz->detail : null;
                                @endphp
                                @if($tahfidz && count($tahfidz) > 0)
                                @foreach($tahfidz as $t)
                                <input type="hidden" name="hafalan_id[]" value="{{ $t->id }}">
                                <tr>
                                    <td width="20%">
                                        <select class="form-control" name="jenis[]" required>
                                            <option value="surat" {{ $t->surat && $t->surat->surah ? 'selected' : '' }}>Surat</option>
                                            <option value="juz" {{ $t->juz && $t->juz->juz ? 'selected' : '' }}>Juz</option>
                                        </select>
                                    </td>
                                    <td width="30%">
                                        <select class="form-control" name="surat[]" {!! $t->surat && $t->surat->surah ? 'required' : "style='display: none'" !!}>
                                            @foreach($surat as $s)
                                            <option value="{{ $s->id }}" {!! $t->surah_id == $s->id ? 'selected' : '' !!}>{{ $s->surahNumberPrefix }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control" name="juz[]" {!! $t->juz && $t->juz->juz ? 'required' : "style='display: none'" !!}>
                                            @foreach($juz as $j)
                                            <option value="{{ $j->id }}" {!! $t->juz_id == $j->id ? 'selected' : '' !!}>{{ $j->juz }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <select class="form-control" name="status[]" required>
                                            @foreach($status as $s)
                                            <option value="{{ $s->id }}" {{ $t->status_id == $s->id ? 'selected' : '' }}>{{ $s->status }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <select class="form-control" name="predikat[]" required>
                                                    <option value="">== Pilih ==</option>
                                                    <option value="A" <?php if ($t->predicate == "A") echo 'selected'; ?>>A</option>
                                                    <option value="B" <?php if ($t->predicate == "B") echo 'selected'; ?>>B</option>
                                                    <option value="C" <?php if ($t->predicate == "C") echo 'selected'; ?>>C</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" id="hapushafalan" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr class="new-surah">
                                    <td width="20%">
                                        <select class="form-control" name="jenis[]" required>
                                            <option value="surat" selected="selected">Surat</option>
                                            <option value="juz">Juz</option>
                                        </select>
                                    </td>
                                    <td width="30%">
                                        <select class="form-control" name="surat[]" required>
                                            @foreach($surat as $s)
                                            <option value="{{ $s->id }}">{{ $s->surahNumberPrefix }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control" name="juz[]" style="display: none">
                                            @foreach($juz as $j)
                                            <option value="{{ $j->id }}">{{ $j->juz }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <select class="form-control" name="status[]" required>
                                            @foreach($status as $s)
                                            <option value="{{ $s->id }}" {{ $s->id == '4' ? 'selected' : '' }}>{{ $s->status }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <select class="form-control" name="predikat[]" required>
                                            <option value="">== Pilih ==</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                        </select>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        <hr>
                        <div class="row">
                            <div class="col-md-2 p-3 text-center">
                                <label>Deskripsi</label>
                            </div>
                            <div class="col-md-10 p-3">
                                <select class="form-control" name="deskripsi" required>
                                    <option value="">== Pilih ==</option>
                                    @if($rpd)
                                    @foreach($rpd as $r)
                                    <option value="{{ $r->id }}" <?php if($rapor && isset($rapor->tahfidz->rpd_id) && $rapor->tahfidz->rpd_id == $r->id) echo "selected"; ?>>{{ $r->description }}</option>
                                    @endforeach
                                    @else
                                    <option value="">Deskripsi Hafalan Belum Diisi</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-4">
                            <div class="input-group mt-4">
                                <input type="password" name="pwedit" class="form-control" placeholder="Password Verifikasi" required />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-toggle-visibility" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">&nbsp;</div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Password Visibility -->
<script src="{{ asset('js/password-visibility.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@if($semester && $kelasList)
@include('template.footjs.kependidikan.change-class')
@endif
@if($kelas && $riwayatKelas)
@include('template.footjs.kependidikan.change-student')
@endif
@if($siswa)
@include('template.footjs.kependidikan.change-surah')
@endif
@include('template.footjs.keuangan.change-year')
@endsection
=======
@extends('template.main.master')

@section('title')
Nilai Hafalan
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nilai Hafalan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        @if($semester || $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.hafalan.index') }}">Nilai Hafalan</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">Nilai Hafalan</li>
        @endif
        @if($semester)
        @if($kelasList && $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</li>
        @endif
        @if($kelas)
        <li class="breadcrumb-item active" aria-current="page">{{ $kelas->level->level.' '.$kelas->namakelases->class_name }}</li>
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
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @foreach($semesterList as $s)
                      @if($s->is_active == 1 || ($s->is_active != 1 && $s->riwayatKelas()->count() > 0))
                      <option value="{{ $s->semesterLink }}" {{ $semester->id == $s->id ? 'selected' : '' }}>{{ $s->semester_id . ' (' .$s->semester.')' }}</option>
                      @endif
                      @endforeach
                    </select>
                    <a href="{{ route('penilaian.hafalan.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.hafalan.index') }}">Atur</a>
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
                    <label for="classOpt" class="form-control-label">Kelas</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    @if(Auth::user()->role->name == 'guru')
                    {{ $kelas->levelName }}
                    @else
                    <div class="input-group">
                        <select aria-label="Kelas" name="kelas" class="form-control" id="classOpt">
                          @foreach($kelasList->sortBy('levelName')->all() as $k)
                          <option value="{{ $k->id }}" {{ $kelas && $kelas->id == $k->id ? 'selected' : '' }}>{{ $k->levelName }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-class" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
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
@if($kelas)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="studentOpt" class="form-control-label">Siswa</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                        @php
                        $raporCount = 0;
                        @endphp
                        @if($riwayatKelas && count($riwayatKelas) > 0)
                        <select aria-label="Siswa" name="siswa" class="form-control" id="studentOpt">
                          @foreach($riwayatKelas as $r)
                          @php
                          $s = $r->siswa()->select('id','student_id')->first();
                          @endphp
                          <option value="{{ $s->id }}" {{ $siswa && $siswa->id == $s->id ? 'selected' : '' }}>{{ $s->identitas->student_name }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}" id="btn-select-student" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.hafalan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}">Lihat</a>
                        @else
                        <input type="text" class="form-control" value="Tidak ada data siswa" disabled>
                        <button type="button" class="btn btn-secondary ml-2 pt-2" disabled="disabled">Lihat</button>
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
</div>
@endif

@if($semester && $kelas && $siswa)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Hafalan</h6>
                <div class="m-0 float-right">
                <button type="button" class="btn btn-brand-green-dark btn-sm" id="tambahhafalan">Tambah Surah <i class="fa fa-plus ml-1"></i></button>
                </div>
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
                <form action="{{ route('penilaian.hafalan.perbarui', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->id]) }}" id="tilawah-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th class="text-center" width="75%" colspan="3">Nama Surat</th>
                                    <th class="text-center" width="25%">Predikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $rapor = $siswa->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                $tahfidz = $rapor && $rapor->tahfidz ? $rapor->tahfidz->detail : null;
                                @endphp
                                @if($tahfidz && count($tahfidz) > 0)
                                @foreach($tahfidz as $t)
                                <input type="hidden" name="hafalan_id[]" value="{{ $t->id }}">
                                <tr>
                                    <td width="20%">
                                        <select class="form-control" name="jenis[]" required>
                                            <option value="surat" {{ $t->surat && $t->surat->surah ? 'selected' : '' }}>Surat</option>
                                            <option value="juz" {{ $t->juz && $t->juz->juz ? 'selected' : '' }}>Juz</option>
                                        </select>
                                    </td>
                                    <td width="30%">
                                        <select class="form-control" name="surat[]" {!! $t->surat && $t->surat->surah ? 'required' : "style='display: none'" !!}>
                                            @foreach($surat as $s)
                                            <option value="{{ $s->id }}" {!! $t->surah_id == $s->id ? 'selected' : '' !!}>{{ $s->surahNumberPrefix }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control" name="juz[]" {!! $t->juz && $t->juz->juz ? 'required' : "style='display: none'" !!}>
                                            @foreach($juz as $j)
                                            <option value="{{ $j->id }}" {!! $t->juz_id == $j->id ? 'selected' : '' !!}>{{ $j->juz }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <select class="form-control" name="status[]" required>
                                            @foreach($status as $s)
                                            <option value="{{ $s->id }}" {{ $t->status_id == $s->id ? 'selected' : '' }}>{{ $s->status }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <select class="form-control" name="predikat[]" required>
                                                    <option value="">== Pilih ==</option>
                                                    <option value="A" <?php if ($t->predicate == "A") echo 'selected'; ?>>A</option>
                                                    <option value="B" <?php if ($t->predicate == "B") echo 'selected'; ?>>B</option>
                                                    <option value="C" <?php if ($t->predicate == "C") echo 'selected'; ?>>C</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="button" id="hapushafalan" class="btn btn-sm btn-danger h-100"><i class="fa fa-times"></i></button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr class="new-surah">
                                    <td width="20%">
                                        <select class="form-control" name="jenis[]" required>
                                            <option value="surat" selected="selected">Surat</option>
                                            <option value="juz">Juz</option>
                                        </select>
                                    </td>
                                    <td width="30%">
                                        <select class="form-control" name="surat[]" required>
                                            @foreach($surat as $s)
                                            <option value="{{ $s->id }}">{{ $s->surahNumberPrefix }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control" name="juz[]" style="display: none">
                                            @foreach($juz as $j)
                                            <option value="{{ $j->id }}">{{ $j->juz }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <select class="form-control" name="status[]" required>
                                            @foreach($status as $s)
                                            <option value="{{ $s->id }}" {{ $s->id == '4' ? 'selected' : '' }}>{{ $s->status }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <select class="form-control" name="predikat[]" required>
                                            <option value="">== Pilih ==</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                        </select>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                        <hr>
                        <div class="row">
                            <div class="col-md-2 p-3 text-center">
                                <label>Deskripsi</label>
                            </div>
                            <div class="col-md-10 p-3">
                                <select class="form-control" name="deskripsi" required>
                                    <option value="">== Pilih ==</option>
                                    @if($rpd)
                                    @foreach($rpd as $r)
                                    <option value="{{ $r->id }}" <?php if($rapor && isset($rapor->tahfidz->rpd_id) && $rapor->tahfidz->rpd_id == $r->id) echo "selected"; ?>>{{ $r->description }}</option>
                                    @endforeach
                                    @else
                                    <option value="">Deskripsi Hafalan Belum Diisi</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">&nbsp;</div>
                        <div class="col-md-4">
                            <div class="input-group mt-4">
                                <input type="password" name="pwedit" class="form-control" placeholder="Password Verifikasi" required />
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary btn-toggle-visibility" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">&nbsp;</div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Password Visibility -->
<script src="{{ asset('js/password-visibility.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@if($semester && $kelasList)
@include('template.footjs.kependidikan.change-class')
@endif
@if($kelas && $riwayatKelas)
@include('template.footjs.kependidikan.change-student')
@endif
@if($siswa)
@include('template.footjs.kependidikan.change-surah')
@endif
@include('template.footjs.keuangan.change-year')
@endsection
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
