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
        @if($riwayatKelas && $siswa)
        <li class="breadcrumb-item"><a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id])}}">{{ $kelas->level->level.' '.$kelas->namakelases->class_name }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $kelas->level->level.' '.$kelas->namakelases->class_name }}</li>
        @endif
        @if($siswa)
        <li class="breadcrumb-item active" aria-current="page">{{ $siswa->identitas->student_name }}</li>
        @endif
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
@if($siswa)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="student-input" class="form-control-label">Peserta Didik</label>
                  </div>
                  <div class="col-lg-8 col-md-8 col-12">
                    <div class="input-group">
                      <input type="text" class="form-control" value="{{ $siswa->identitas->student_name }}" disabled="disabled" />
                      <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id]) }}">Ubah</a>
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

@if($semester && $kelas)
@if($siswa)
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
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6>
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
                @if($objectives && count($objectives) > 0)
                <form action="{{ route($route.'.update', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $siswa->studentNisLink]) }}" id="formative-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="table-responsive">
                      <table class="table align-items-center table-sm" style="width:100%">
                        <thead class="bg-brand-green text-white">
                          <tr>
                            <th style="width: 50px">#</th>
                            <th class="text-center" width="60%">Tujuan Pembelajaran</th>
                            <th class="text-center">Perkembangan</th>
                          </tr>
                        </thead>
                        <tbody>
                          @php $catActive = null; @endphp
                          @foreach($objectives as $o)
                          @if($catActive != $o->element_id)
                          <tr class="bg-dark text-white">
                            <td colspan="3"><strong>{{ $o->element->dev_aspect }}</strong></td>
                          </tr>
                          @php $catActive = $o->element_id @endphp
                          @endif
                          <tr>
                            <td style="vertical-align: middle;">{{ $o->number }}</td>
                            <td style="vertical-align: middle;">{{ $o->objective->desc }}</td>
                            <td style="vertical-align: middle;">
                              <select class="form-control" name="predicate[{{ $o->sort_order }}]">
                                  @if(!isset($nilai[$siswa->id][$o->objective_id]))
                                  <option value="">== Pilih ==</option>
                                  @endif
                                  @for($p='A';$p<='D';$p++)
                                  <option value="{{ $p }}" {{ isset($nilai[$siswa->id][$o->objective_id]) && $nilai[$siswa->id][$o->objective_id] == $p ? 'selected' : null }}>{{ $p }}</option>
                                  @endfor
                              </select>
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
                <div class="text-center mx-3 my-5">
                  <h3 class="text-center">Mohon Maaf,</h3>
                  <h6 class="font-weight-light mb-3">Tidak ada data tujuan pembelajaran yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@else
@if(!$objectives || ($objectives && count($objectives) < 1))
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-left-info">
            <div class="card-body">
                <i class="fa fa-info-circle text-info mr-2"></i>Tujuan pembelajaran belum diatur. Penilaian dapat dilakukan setelah tujuan pembelajaran diatur. <a href="{{ route('kependidikan.penilaian.tk.tujuan-elemen.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $kelas->level_id]) }}" class="text-info font-weight-bold text-decoration-none">Atur sekarang</a>
            </div>
        </div>
    </div>
</div>
@endif
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
                @if($objectives && count($objectives) > 0 && $elements && $elements > 0 && (!$descs || ($descs && $descs < (2*$elements))))
                <div class="alert alert-light" role="alert">
                    <i class="fa fas fa-exclamation-triangle text-warning mr-2"></i><strong>Perhatian!</strong> Deskripsi elemen capaian pembelajaran belum diisi dengan lengkap. <a href="{{ route('kependidikan.penilaian.tk.deskripsi-perkembangan.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $kelas->level_id]) }}" class="text-info font-weight-bold text-decoration-none">Atur sekarang</a>
                </div>
                @endif
                @if($riwayatKelas && count($riwayatKelas) > 0)
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>Nama</th>
                                    <th>Status</th>
                                    <th style="width: 120px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($riwayatKelas as $r)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $r->identitas->student_name }}</td>
                                    <td>
                                      @if(!$objectives || ($objectives && count($objectives) < 1))
                                      <i class="fa fa-lg fa-exclamation-circle text-warning" data-toggle="tooltip" data-original-title="Belum dapat mengisi penilaian formatif kualitatif"></i>
                                      @else
                                      @if(!isset($count[$r->id]) || ($count[$r->id] && $count[$r->id] == 0))
                                      <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada perkembangan yang dimasukkan"></i>
                                      @elseif($count[$r->id] > 0 && $count[$r->id] < count($objectives))
                                      <i class="fa fa-lg fa-tasks text-info" data-toggle="tooltip" data-original-title="Sebagian nilai perkembangan sudah dimasukkan"></i>
                                      @else
                                      <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Seluruh nilai perkembangan sudah lengkap"></i>
                                      @endif
                                      @endif
                                    </td>
                                    <td>
                                      @if(!$objectives || ($objectives && count($objectives) < 1))
                                      <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-pen"></i></button>
                                      @else
                                      <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $r->studentNisLink]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
                                      @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
@include('template.footjs.keuangan.change-year')
@endsection
