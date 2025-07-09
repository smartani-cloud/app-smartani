<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Nilai Tilawah
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nilai Tilawah</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        @if($semester || $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.tilawah.index') }}">Nilai Tilawah</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">Nilai Tilawah</li>
        @endif
        @if($semester)
        @if($kelasList && $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.tilawah.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
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
                    <a href="{{ route('penilaian.tilawah.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.tilawah.index') }}">Atur</a>
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
                        <a href="{{ route('penilaian.tilawah.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-class" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.tilawah.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
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

@if($semester && $kelas)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Siswa</h6>
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
                @php
                $raporCount = 0;
                @endphp
                @if($riwayatKelas && count($riwayatKelas) > 0)
                @if(count($tipeList) > 0)
                <form action="{{ route('penilaian.tilawah.perbarui', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}" id="tilawah-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th class="align-middle">Nama</th>
                                    @foreach($tipeList as $t)
                                    <th class="text-center align-middle">{{ $t->tilawah_type }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatKelas as $r)
                                @php
                                $s = $r->siswa()->select('id','student_id')->first();
                                $rapor = $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                if($rapor) $raporCount++;
                                $tilawah = $rapor ? $rapor->tilawah : null;
                                @endphp
                                <tr>
                                    <td><label style="width: 150px;">{{ $s->identitas->student_name }}</label></td>
                                    @foreach($tipeList as $t)
                                    <td>
                                        @php
                                        $detail = $tilawah ? $tilawah->detail()->select('predicate')->where('tilawah_type_id',$t->id)->whereNotNull('predicate')->first() : null;
                                        $scoreTilawah = $detail && (strlen($detail->predicate) > 0) ? $detail->predicate : null;
                                        @endphp
                                        <select name="s-{{$s->id}}-t-{{$t->id}}" class="form-control">
                                            <option value="">== Pilih ==</option>
                                            <option value="A" {{ $scoreTilawah == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ $scoreTilawah == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ $scoreTilawah == 'C' ? 'selected' : '' }}>C</option>
                                        </select>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($raporCount > 0)
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
                    @endif
                </form>
                @else
                <table class="table align-items-center table-sm" style="width:100%">
                    <thead class="bg-danger text-white">
                        <tr>
                            <td class="text-center">Kompetensi tilawah belum diatur!</td>
                        </tr>
                    </thead>
                </table>
                @endif
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data siswa yang ditemukan</h6>
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

@if($riwayatKelas && count($riwayatKelas) > 0)
<!-- Page level plugins -->

<!-- Password Visibility -->
<script src="{{ asset('js/password-visibility.js') }}"></script>

@endif
<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@if($semester && $kelasList)
@include('template.footjs.kependidikan.change-class')
@endif
@include('template.footjs.keuangan.change-year')
@endsection
=======
@extends('template.main.master')

@section('title')
Nilai Tilawah
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nilai Tilawah</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        @if($semester || $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.tilawah.index') }}">Nilai Tilawah</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">Nilai Tilawah</li>
        @endif
        @if($semester)
        @if($kelasList && $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.tilawah.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
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
                    <a href="{{ route('penilaian.tilawah.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.tilawah.index') }}">Atur</a>
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
                        <a href="{{ route('penilaian.tilawah.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-class" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.tilawah.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
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

@if($semester && $kelas)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Siswa</h6>
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
                @php
                $raporCount = 0;
                @endphp
                @if($riwayatKelas && count($riwayatKelas) > 0)
                @if(count($tipeList) > 0)
                <form action="{{ route('penilaian.tilawah.perbarui', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}" id="tilawah-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th class="align-middle">Nama</th>
                                    @foreach($tipeList as $t)
                                    <th class="text-center align-middle">{{ $t->tilawah_type }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatKelas as $r)
                                @php
                                $s = $r->siswa()->select('id','student_id')->first();
                                $rapor = $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                if($rapor) $raporCount++;
                                $tilawah = $rapor ? $rapor->tilawah : null;
                                @endphp
                                <tr>
                                    <td><label style="width: 150px;">{{ $s->identitas->student_name }}</label></td>
                                    @foreach($tipeList as $t)
                                    <td>
                                        @php
                                        $detail = $tilawah ? $tilawah->detail()->select('predicate')->where('tilawah_type_id',$t->id)->whereNotNull('predicate')->first() : null;
                                        $scoreTilawah = $detail && (strlen($detail->predicate) > 0) ? $detail->predicate : null;
                                        @endphp
                                        <select name="s-{{$s->id}}-t-{{$t->id}}" class="form-control">
                                            <option value="">== Pilih ==</option>
                                            <option value="A" {{ $scoreTilawah == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ $scoreTilawah == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ $scoreTilawah == 'C' ? 'selected' : '' }}>C</option>
                                        </select>
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($raporCount > 0)
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
                    @endif
                </form>
                @else
                <table class="table align-items-center table-sm" style="width:100%">
                    <thead class="bg-danger text-white">
                        <tr>
                            <td class="text-center">Kompetensi tilawah belum diatur!</td>
                        </tr>
                    </thead>
                </table>
                @endif
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data siswa yang ditemukan</h6>
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

@if($riwayatKelas && count($riwayatKelas) > 0)
<!-- Page level plugins -->

<!-- Password Visibility -->
<script src="{{ asset('js/password-visibility.js') }}"></script>

@endif
<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@if($semester && $kelasList)
@include('template.footjs.kependidikan.change-class')
@endif
@include('template.footjs.keuangan.change-year')
@endsection
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
