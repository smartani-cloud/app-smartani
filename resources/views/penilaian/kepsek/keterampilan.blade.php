@extends('template.main.master')

@section('title')
Nilai Keterampilan
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nilai Keterampilan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        @if($semester || $kelas)
        <li class="breadcrumb-item"><a href="{{ route('mapel.keterampilan.index') }}">Nilai Keterampilan</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">Nilai Keterampilan</li>
        @endif
        @if($semester)
        @if($kelasList && $kelas)
        <li class="breadcrumb-item"><a href="{{ route('mapel.keterampilan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
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
                    <a href="{{ route('mapel.keterampilan.index') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('mapel.keterampilan.index') }}">Atur</a>
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
                        <a href="{{ route('mapel.keterampilan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-class" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('mapel.keterampilan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
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
                        <a href="{{ route('mapel.keterampilan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}" id="btn-select-subject" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('mapel.keterampilan.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}">Pilih</a>
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
                @if($jumlahKd && $rpd == 4)
                <form action="{{ route('mapel.keterampilan.perbarui', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]) }}" id="skill-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                {{ csrf_field() }}
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th>Nama</th>
                                    @php
                                    $kd = $jumlahKd->kd;
                                    @endphp
                                    @if($kd > 0)
                                    @for($i = 1; $i <= $kd; $i++)
                                    <th class="text-center">NH{{$i}}</th>
                                    @endfor
                                    @endif
                                    <th class="text-center">Nilai Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riwayatKelas as $r)
                                @php
                                $s = $r->siswa()->select('id','student_id')->first();
                                $rapor = $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                if($rapor) $raporCount++;
                                $keterampilan = $rapor ? $rapor->keterampilan()->where('subject_id',$mataPelajaran->id)->first() : null;
                                @endphp
                                <tr>
                                    <td><label style="width: 150px;">{{ $s->identitas->student_name }}</label></td>
                                    @if($kd > 0)
                                    @for($i = 1; $i <= $kd; $i++)
                                    <td>
                                        @php
                                        $scoreSkill = $keterampilan && isset($keterampilan->nilaiketerampilandetail[$i-1]) ? $keterampilan->nilaiketerampilandetail[$i-1]->score : '';
                                        @endphp
                                        <input type="number" class="form-control mx-auto" min="0" max="100" name="s-{{$s->id}}-kd-{{$i}}" value="{{ $scoreSkill }}" style="width: 70px;">
                                    </td>
                                    @endfor
                                    @endif
                                    @php
                                    $na = $keterampilan && $keterampilan->mean ? number_format((float)$keterampilan->mean, 0, ',', '') : 0;
                                    @endphp
                                    <td>
                                        <input type="text" class="form-control mx-auto" name="s-{{$s->id}}-na" value="{{ $na }}" style="width: 70px;" disabled>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($raporCount > 0 && $kd > 0)
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
                @elseif(!$jumlahKd && $rpd < 4)
                <table class="table align-items-center table-sm" style="width:100%">
                    <thead class="bg-danger text-white">
                        <tr>
                            <td class="text-center">Jumlah NH dan Predikat Keterampilan Belum Diatur!</td>
                        </tr>
                    </thead>
                </table>
                @elseif(!$jumlahKd && $rpd == 4)
                <table class="table align-items-center table-sm" style="width:100%">
                    <thead class="bg-danger text-white">
                        <tr>
                            <td class="text-center">Jumlah NH Belum Diatur!</td>
                        </tr>
                    </thead>
                </table>
                @elseif($jumlahKd && $rpd < 4)
                <table class="table align-items-center table-sm" style="width:100%">
                    <thead class="bg-danger text-white">
                        <tr>
                            <td class="text-center">Predikat Keterampilan Belum Diatur!</td>
                        </tr>
                    </thead>
                </table>
                @else
                <hr>
                <table class="table align-items-center table-sm" style="width:100%">
                    <thead class="bg-danger text-white">
                        <tr>
                            <td class="text-center">Ups, terjadi error!</td>
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
@if($kelas && $mataPelajaranList)
@include('template.footjs.kependidikan.change-subject')
@endif
@include('template.footjs.keuangan.change-year')
@endsection
