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
                @if($rapor[$siswa->id])
                @else
                <div class="text-center mx-3 my-5">
                  <h3 class="text-center">Mohon Maaf,</h3>
                  <h6 class="font-weight-light mb-3">Tidak ada data peserta didik yang ditemukan</h6>
                </div>
                @endif
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
                @if($riwayatKelas && count($riwayatKelas) > 0)
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                            <thead class="bg-brand-green text-white">
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>Nama</th>
                                    @if($canOverview)
                                    <th>{{ $unit->id == 1 ? 'Elemen' : 'NAR' }}</th>
                                    <th>IKLaS</th>
                                    <th>Khataman</th>
                                    <th>Qur'an</th>
                                    @foreach($kategoriList['hafalan'] as $kategori)
                                    <th>{{ ucwords($kategori->mem_type) }}</th>
                                    @endforeach
                                    @endif
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($riwayatKelas as $r)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $r->identitas->student_name }}</td>
                                    @if($canOverview)
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir'] : '-' }}</td>
                                    <td>
                                      @if(!$competencies || ($competencies && count($competencies) < 1))
                                      <i class="fa fa-lg fa-exclamation-circle text-warning" data-toggle="tooltip" data-original-title="Belum dapat mengisi nilai"></i>
                                      @else
                                      @if(!isset($count[$r->id]['iklas']) || $count[$r->id]['iklas'] == 0)
                                      <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada nilai yang dimasukkan"></i>
                                      @elseif($count[$r->id]['iklas'] > 0 && $count[$r->id]['iklas'] < count($competencies))
                                      <i class="fa fa-lg fa-tasks text-info" data-toggle="tooltip" data-original-title="Sebagian nilai sudah dimasukkan"></i>
                                      @else
                                      <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Nilai sudah lengkap"></i>
                                      @endif
                                      @endif
                                    </td>
                                    <td>
                                      @if(!isset($capaian[$r->id]['khataman']['status']) || $capaian[$r->id]['khataman']['status'] <= 0)
                                      <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Belum ada capaian yang dimasukkan"></i>
                                      @elseif($capaian[$r->id]['khataman']['status'] > 0 && $capaian[$r->id]['khataman']['status'] < 3)
                                      <i class="fa fa-lg fa-check-circle text-secondary" data-toggle="tooltip" data-original-title="Sebagian capaian sudah dimasukkan"></i>
                                      @else
                                      <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Seluruh capaian sudah dimasukkan"></i>
                                      @endif
                                    </td>
                                    <td>
                                      @if(!isset($capaian[$r->id]['quran']) || ($capaian[$r->id]['quran'] && $capaian[$r->id]['quran']['hafalan'] == 0))
                                      0
                                      @elseif($capaian[$r->id]['quran']['hafalan'] > 0)
                                      @if($capaian[$r->id]['quran']['desc'])
                                      <span class="font-weight-bold text-success" data-toggle="tooltip" data-original-title="Deskripsi qur'an sudah ditentukan">{{ $capaian[$r->id]['quran']['hafalan'] }}</span>
                                      @else
                                      {{ $capaian[$r->id]['quran']['hafalan'] }}
                                      @endif
                                      @endif
                                    </td>
                                    @foreach($kategoriList['hafalan'] as $kategori)
                                    @php $kategori = $kategori->mem_type @endphp
                                    <td>
                                      @if(!isset($capaian[$r->id][$kategori]) || ($capaian[$r->id][$kategori] && $capaian[$r->id][$kategori]['hafalan'] == 0))
                                      0
                                      @elseif($capaian[$r->id][$kategori]['hafalan'] > 0)
                                      @if($capaian[$r->id][$kategori]['desc'])
                                      <span class="font-weight-bold text-success" data-toggle="tooltip" data-original-title="Deskripsi {{ strtolower($kategori) }} sudah ditentukan">{{ $capaian[$r->id][$kategori]['hafalan'] }}</span>
                                      @else
                                      {{ $capaian[$r->id][$kategori]['hafalan'] }}
                                      @endif
                                      @endif
                                    </td>
                                    @endforeach
                                    @endif
                                    <td>
                                      @if($rapor[$r->id] && $rapor[$r->id]->report_status_id == 1)
                                      <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Rapor sudah divalidasi"></i>
                                      @else
                                      <i class="fa fa-lg fa-exclamation-circle text-warning" data-toggle="tooltip" data-original-title="Rapor belum divalidasi"></i>
                                      @endif
                                    </td>
                                    <td>
                                      @if(!$rapor[$r->id])
                                      <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fa fa-eye mr-2"></i>Pratinjau Rapor</button>
                                      @elseif($rapor[$r->id] && $rapor[$r->id]->report_status_id == 1)
                                      <a href="{{ route('pas.cetak.cover',['id'=>$r->id])}}" target="_blank" class="btn  btn-sm btn-brand-green"><i class="fa fa-print mr-2"></i>Cetak Cover</a>
                                      <div class="btn-group">
                                        <a href="{{ route($route.'.report', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $r->studentNisLink]) }}" class="btn btn-sm btn-brand-green-dark" target="_blank"><i class="fa fa-print mr-2"></i>Cetak Rapor</a>
                                        <button type="button" class="btn btn-sm btn-brand-green-dark dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          <span class="sr-only">Lainnya</span>
                                        </button>
                                        <div class="dropdown-menu">
                                          <a class="dropdown-item" href="{{ route($route.'.report', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $r->studentNisLink,'digital'=>1]) }}" target="_blank">Cetak Tanpa TTD</a>
                                        </div>
                                      </div>
                                      <a href="{{ route('pas.cetak.akhir',['id'=>$r->id]) }}" target="_blank" class="btn btn-sm btn-secondary"><i class="fa fa-print mr-2"></i>Cetak Halaman Akhir</a>
                                      @else
                                      <a href="{{ route($route.'.report', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $r->studentNisLink]) }}" class="btn btn-sm btn-brand-green-dark mr-2" target="_blank"><i class="fa fa-eye mr-2"></i>Pratinjau Rapor</a>
                                      @endif
                                      @if(isset($acceptable[$r->id]) && $acceptable[$r->id])
                                      @if($rapor[$r->id]->report_status_pts_id == 1)
                                      <a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#accept-confirm" onclick="acceptModal('Peserta Didik', '{{ addslashes(htmlspecialchars($r->identitas->student_name)) }}', '{{ route($route.'.accept', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'siswa' => $r->studentNisLink]) }}')">
                                        <i class="fa fa-check mr-2"></i>Validasi
                                      </a>
                                      @else
                                      <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fa fa-check mr-2"></i>Validasi</button>
                                      @endif
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

@include('template.modal.konfirmasi_validasi')

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
@include('template.footjs.modal.put_accept')
@endsection
