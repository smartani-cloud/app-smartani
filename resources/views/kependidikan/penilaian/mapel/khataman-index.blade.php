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
                      <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'mataPelajaran' => $mataPelajaran->id, 'kelas' => $kelas->id]) }}" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'mataPelajaran' => $mataPelajaran->id, 'kelas' => $kelas->id]) }}">Ubah</a>
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
@if($siswa)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-left-brand-green">
            <div class="card-body">
                <i class="fa fa-exclamation-triangle text-warning mr-2"></i>Sebelum memasukan data capaian, mohon pastikan Anda <b>masih masuk dalam sistem</b> dan mohon untuk melakukan penyimpanan data capaian secara reguler.
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
                <form action="{{ route($route.'.update', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id, 'siswa' => $siswa->studentNisLink]) }}" id="quran-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="row mb-3">
                      <div class="col-12">
                        <div class="form-group mb-0">
                          <div class="row">
                            <div class="col-lg-2 col-md-3 col-12">
                              <label for="typeOpt" class="form-control-label">Jenis Capaian</label>
                            </div>
                            <div class="col-lg-4 col-md-6 col-12">
                              <div class="input-group">
                                <select aria-label="Type" name="type" class="form-control" id="typeOpt" required="required">
                                  @if($types && count($types) > 0)
                                  @foreach($types as $t)
                                  <option value="{{ $t->id }}" {{ isset($capaian['type']) && $capaian['type']->type_id == $t->id ? "selected" : null }}>{{ $t->name }}</option>
                                  @endforeach
                                  @else
                                  <option value="">Jenis capaian belum ditentukan</option>
                                  @endif
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th class="text-center" colspan="3">Capaian {{ $active }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($types && count($types) > 0)
                                @if($types->where('name','Al-Qur\'an')->count() > 0)
                                @if($capaian['quran'] && count($capaian['quran']) > 0)
                                @foreach($capaian['quran'] as $q)
                                <tr class="quran-row" {!! isset($capaian['type']) && $capaian['type']->type_id != 1 ? 'style="display: none"' : null !!}>
                                    <td width="20%">
                                        <select class="form-control" name="jenis[]" required>
                                            <option value="surat" {{ $q->jenis && $q->jenis->mem_type == 'Surat' ? 'selected' : '' }}>Surat</option>
                                            <option value="juz" {{ $q->jenis && $q->jenis->mem_type == 'Juz' ? 'selected' : '' }}>Juz</option>
                                        </select>
                                    </td>
                                    <td width="60%">
                                        <select class="form-control" name="surat[]" {!! $q->jenis && $q->jenis->mem_type == 'Surat' ? 'required' : "style='display: none'" !!}>
                                            @foreach($surat as $s)
                                            <option value="{{ $s->id }}" {!! $q->surah_id == $s->id ? 'selected' : '' !!}>{{ $s->surahNumberPrefix }}</option>
                                            @endforeach
                                        </select>
                                        <select class="form-control" name="juz[]" {!! $q->jenis && $q->jenis->mem_type == 'Juz' ? 'required' : "style='display: none'" !!}>
                                            @foreach($juz as $j)
                                            <option value="{{ $j->id }}" {!! $q->juz_id == $j->id ? 'selected' : '' !!}>{{ $j->juz }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td width="25%">
                                        <input type="text" class="form-control" name="ayat[]" maxlength="15" placeholder="Ayat" value="{{ $q->verse }}" {!! $q->jenis && $q->jenis->mem_type == 'Surat' ? 'required' : "style='display: none'" !!}/>
                                        <select class="form-control" name="status[]" {!! $q->jenis && $q->jenis->mem_type == 'Juz' ? 'required' : "style='display: none'" !!}>
                                            @foreach($status as $s)
                                            <option value="{{ $s->id }}" {{ $q->status_id == $s->id ? 'selected' : '' }}>{{ $s->status }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                @for($i=0;$i<2;$i++)
                                <tr class="quran-row" {!! isset($capaian['type']) && $capaian['type']->type_id != 1 ? ($types && $types->where('name','Al-Qur\'an')->count() > 0 && count($types) == 1 ? null : 'style="display: none"') : null !!}>
                                    <td width="20%">
                                        <select class="form-control" name="jenis[]" {{ !isset($capaian['type']) || $capaian['type']->type_id == 1 ? 'required' : null }}>
                                            <option value="surat" selected="selected">Surat</option>
                                            <option value="juz">Juz</option>
                                        </select>
                                    </td>
                                    <td width="60%">
                                        <select class="form-control" name="surat[]" {{ !isset($capaian['type']) || $capaian['type']->type_id == 1 ? 'required' : null }}>
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
                                        <input type="text" class="form-control" name="ayat[]" maxlength="15" placeholder="Ayat" {{ !isset($capaian['type']) || $capaian['type']->type_id == 1 ? 'required' : null }}/>
                                        <select class="form-control" name="status[]" style="display: none">
                                            @foreach($status as $s)
                                            <option value="{{ $s->id }}" {{ $s->id == '4' ? 'selected' : '' }}>{{ $s->status }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @endfor
                                @endif
                                @endif
                                @if($types->where('name','Buku')->count() > 0)
                                <tr class="book-row" {!! !isset($capaian['type']) || $capaian['type']->type_id != 2 ? ($types && $types->where('name','Buku')->count() > 0 && count($types) == 1 ? null : 'style="display: none"') : null !!}>
                                    <td colspan="3">
                                        <select class="form-control" id="bookOpt"  name="buku" {{ isset($capaian['type']) && $capaian['type']->type_id == 2 ? 'required' : null }}>
                                            @foreach($books as $b)
                                            <option value="{{ $b->id }}" {{ isset($capaian['buku']) && $capaian['buku'] == $b->id ? 'selected' : '' }}>{{ $b->titleWithPages }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @endif
                                @elseif(!$types || ($types && count($types) < 1))
                                <tr>
                                  <td class="text-center" colspan="3">Capaian khataman belum dapat diisi</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <hr>
                    <div class="card-footer">
                      <div class="row">
                        <div class="col-md-4 col-12">
                          <div class="form-group">
                            <div class="row">
                              <div class="col-12">
                                <label for="total" class="form-control-label">Total Halaman</label>
                              </div>
                              <div class="col-12">
                                @if($types && count($types) > 0)
                                <input type="number" class="input-sm form-control @error('total') is-invalid @enderror" name="total" value="{{ old('total',(isset($capaian['type']) ? $capaian['type']->total : 0)) }}" min="1" step="1" required="required"/>
                                @else
                                <input type="text" class="input-sm form-control" value="{{ isset($capaian['type']) ? $capaian['type']->total : 0 }}" disabled="disabled"/>
                                @endif
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 col-12">
                          <div class="form-group">
                            <div class="row">
                              <div class="col-12">
                                <label for="last" class="form-control-label">Halaman Tercapai</label>
                              </div>
                              <div class="col-12">
                                @if($types && count($types) > 0)
                                <input type="number" class="input-sm form-control @error('last') is-invalid @enderror" name="last" value="{{ old('last',(isset($capaian['type']) ? $capaian['type']->last : 0)) }}" min="1" step="1" required="required"/>
                                @else
                                <input type="text" class="input-sm form-control" value="{{ isset($capaian['type']) ? $capaian['type']->last : 0 }}" disabled="disabled"/>
                                @endif
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4 col-12">
                          <div class="form-group">
                            <div class="row">
                              <div class="col-12">
                                <label for="percentage" class="form-control-label">Persentase</label>
                              </div>
                              <div class="col-12">
                                <div class="input-group">
                                  <input type="text" class="input-sm form-control" name="percentage" value="{{ isset($capaian['type']) ? $capaian['type']->percentage : 0 }}" disabled="disabled"/>
                                  <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      @foreach($kategoriList as $kategori)
                      <div class="row mb-3">
                        <div class="col-12">
                          <div class="form-group mb-0">
                            <div class="row">
                              <div class="col-lg-2 col-md-3 col-12">
                                <label for="{{ $kategori }}DescsOpt" class="form-control-label">{{ ucwords($kategori) }}</label>
                              </div>
                              <div class="col-lg-10 col-md-9 col-12">
                                <div class="input-group">
                                  <select aria-label="{{ ucwords($kategori) }}Description" name="{{ $kategori }}Desc" class="form-control" id="{{ $kategori }}DescsOpt" {{ isset($capaian[$kategori]['desc']) && $descs[$kategori] && count($descs[$kategori]) > 0 ? 'required="required"' : null }}>
                                    @if($descs[$kategori] && count($descs[$kategori]) > 0)
                                    @if(!isset($capaian[$kategori]['desc']))
                                    <option value="">== Pilih ==</option>
                                    @endif
                                    @foreach($descs[$kategori] as $d)
                                    <option value="{{ $d->id }}" {{ isset($capaian[$kategori]['desc']) && $capaian[$kategori]['desc'] == $d->id ? "selected" : null }}>{{ $d->description }}</option>
                                    @endforeach
                                    @else
                                    <option value="">Deskripsi {{ $kategori }} tilawah belum diisi</option>
                                    @endif
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      @endforeach
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@else
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
                @php $isShown = false @endphp
                @foreach($kategoriList as $kategori)
                @if((!$descs[$kategori] || ($descs[$kategori] && count($descs[$kategori]) < 1)))
                @if(!$isShown)
                <div class="alert alert-light" role="alert">
                    <i class="fa fas fa-exclamation-triangle text-warning mr-2"></i><strong>Perhatian!</strong> Deskripsi {{ $kategori }} tilawah belum diisi dengan lengkap. <a href="{{ route('kependidikan.penilaian.mapel.deskripsi-khataman.index',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $kelas->level_id, 'mataPelajaran' => $mataPelajaran->id]) }}" class="text-info font-weight-bold text-decoration-none">Atur sekarang</a>
                </div>
                @php $isShown = true @endphp
                @endif
                @endif
                @endforeach
                @if($riwayatKelas && count($riwayatKelas) > 0)
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>Nama</th>
                                    <th>Capaian</th>
                                    <th>Jenis</th>
                                    <th>Persentase</th>
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
                                      @if(!isset($capaian[$r->id]['type']))
                                      <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada capaian yang dimasukkan"></i>
                                      @else
                                      <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Capaian sudah dimasukkan"></i>
                                      @endif
                                    </td>
                                    <td>
                                      {{ isset($capaian[$r->id]['type']) && $capaian[$r->id]['type']->type ? $capaian[$r->id]['type']->type->name : '-' }}
                                    </td>
                                    <td>
                                      {{ isset($capaian[$r->id]['type']) ? $capaian[$r->id]['type']->percentage.'%' : '-' }}
                                    </td>
                                    <td>
                                      <a href="{{ route($route.'.index', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id, 'siswa' => $r->studentNisLink]) }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>
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
@if($kelas && $mataPelajaranList)
@include('template.footjs.kependidikan.change-subject')
@endif
@if($siswa)
@include('template.footjs.kependidikan.change-khatam-kurdeka')
@endif
@include('template.footjs.keuangan.change-year')
@endsection
