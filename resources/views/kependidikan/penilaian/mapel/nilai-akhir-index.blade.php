<<<<<<< HEAD
@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<style>
 th {
    white-space: nowrap;
 }
</style>
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
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Persentase Nilai Akhir</h6>
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
                <form action="{{ route($route.'.update', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]) }}" id="percentage-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                {{ csrf_field() }}
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-12">
                            <label for="naf_percentage" class="form-control-label">NAF</label><i class="fa fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Nilai Akhir Formatif"></i>
                          </div>
                          <div class="col-12">
                            <div class="input-group">
                              <input type="number" class="input-sm form-control percentage @error('naf_percentage') is-invalid @enderror" name="naf_percentage" value="{{ old('naf_percentage',($percentages ? $percentages->naf_percentage : 0)) }}" min="0" max="100" step="0.01" required="required"/>
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-12">
                            <label for="nas_percentage" class="form-control-label">NAS</label><i class="fa fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Nilai Akhir Sumatif"></i>
                          </div>
                          <div class="col-12">
                            <div class="input-group">
                              <input type="number" class="input-sm form-control percentage @error('nas_percentage') is-invalid @enderror" name="nas_percentage" value="{{ old('nas_percentage',($percentages ? $percentages->nas_percentage : 0)) }}" min="0" max="100" step="0.01" required="required"/>
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-12">
                            <label for="ntss_percentage" class="form-control-label">NTSS</label><i class="fa fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Nilai Tengah Semester Sumatif"></i>
                          </div>
                          <div class="col-12">
                            <div class="input-group">
                              <input type="number" class="input-sm form-control percentage @error('ntss_percentage') is-invalid @enderror" name="ntss_percentage" value="{{ old('ntss_percentage',($percentages ? $percentages->ntss_percentage : 0)) }}" min="0" max="100" step="0.01" required="required"/>
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-12">
                            <label for="nass_percentage" class="form-control-label">NASS</label><i class="fa fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Nilai Akhir Semester Sumatif"></i>
                          </div>
                          <div class="col-12">
                            <div class="input-group">
                              <input type="number" class="input-sm form-control percentage @error('nass_percentage') is-invalid @enderror" name="nass_percentage" value="{{ old('nass_percentage',($percentages ? $percentages->nass_percentage : 0)) }}" min="0" max="100" step="0.01" required="required"/>
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <button id="btnSave" class="btn btn-brand-green-dark" type="submit">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                @if($riwayatKelas && count($riwayatKelas) > 0)
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                        <table id="dataTable" class="table  align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>Nama</th>
                                    <th>Proyek</th>
                                    @if($tpfCount)
                                    @for($i=1;$i<=$tpfCount->kd;$i++)
                                    <th>{{ 'PF-'.$i }}</th>
                                    @endfor
                                    @endif
                                    <th>NAF</th>
                                    @if($tpDescs && count($tpDescs) > 0)
                                    @foreach($tpDescs as $t)
                                    <th>{{ $t->code }}</th>
                                    @endforeach
                                    @endif
                                    <th>NAS</th>
                                    <th>NTSS</th>
                                    <th>NASS</th>
                                    <th>NA Rapor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($riwayatKelas as $r)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td><label style="width: 150px;">{{ $r->identitas->student_name }}</label></td>
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->projectWithSeparator : 0 }}</td>
                                    @if($tpfCount)
                                    @for($i=1;$i<=$tpfCount->kd;$i++)
                                    <td>{{ isset($nilai[$r->id][$i]) ? $nilai[$r->id][$i] : 0 }}</td>
                                    @endfor
                                    @endif
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nafWithSeparator : 0 }}</td>
                                    @if($tpDescs && count($tpDescs) > 0)
                                    @foreach($tpDescs as $t)
                                    <td>{{ isset($nilai[$r->id][$t->id]) ? $nilai[$r->id][$t->id] : 0 }}</td>
                                    @endforeach
                                    @endif
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nasWithSeparator : 0 }}</td>
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->ntssWithSeparator : 0 }}</td>
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nassWithSeparator : 0 }}</td>
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->narWithSeparator : 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

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
<script>
 $(function(){
    $('.percentage').bind('keyup mouseup', function(e){
      var total = 0;
      var hasValue = 0;
      $('.percentage').each(function () {
         var value = parseFloat($(this).val());
         if(value > 0){
            total += value;
            hasValue++;
         }
      });
      if(hasValue > 0){
        if(total === 100){
            $('#btnSave').removeAttr('disabled');
            if($('#btnSave').hasClass('btn-secondary')){
                if(!$('#btnSave').hasClass('btn-brand-green-dark')){
                    $('#btnSave').addClass('btn-brand-green-dark');
                }
                $('#btnSave').removeClass('btn-secondary');
            }
        }
        else{
            $('#btnSave').attr('disabled','disabled');
            if($('#btnSave').hasClass('btn-brand-green-dark')){
                if(!$('#btnSave').hasClass('btn-secondary')){
                    $('#btnSave').addClass('btn-secondary');
                }
                $('#btnSave').removeClass('btn-brand-green-dark');
            }
        }
      }
    });
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

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<style>
 th {
    white-space: nowrap;
 }
</style>
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
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Persentase Nilai Akhir</h6>
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
                <form action="{{ route($route.'.update', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'kelas' => $kelas->id, 'mataPelajaran' => $mataPelajaran->id]) }}" id="percentage-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                {{ csrf_field() }}
                  <div class="row">
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-12">
                            <label for="naf_percentage" class="form-control-label">NAF</label><i class="fa fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Nilai Akhir Formatif"></i>
                          </div>
                          <div class="col-12">
                            <div class="input-group">
                              <input type="number" class="input-sm form-control percentage @error('naf_percentage') is-invalid @enderror" name="naf_percentage" value="{{ old('naf_percentage',($percentages ? $percentages->naf_percentage : 0)) }}" min="0" max="100" step="0.01" required="required"/>
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-12">
                            <label for="nas_percentage" class="form-control-label">NAS</label><i class="fa fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Nilai Akhir Sumatif"></i>
                          </div>
                          <div class="col-12">
                            <div class="input-group">
                              <input type="number" class="input-sm form-control percentage @error('nas_percentage') is-invalid @enderror" name="nas_percentage" value="{{ old('nas_percentage',($percentages ? $percentages->nas_percentage : 0)) }}" min="0" max="100" step="0.01" required="required"/>
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-12">
                            <label for="ntss_percentage" class="form-control-label">NTSS</label><i class="fa fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Nilai Tengah Semester Sumatif"></i>
                          </div>
                          <div class="col-12">
                            <div class="input-group">
                              <input type="number" class="input-sm form-control percentage @error('ntss_percentage') is-invalid @enderror" name="ntss_percentage" value="{{ old('ntss_percentage',($percentages ? $percentages->ntss_percentage : 0)) }}" min="0" max="100" step="0.01" required="required"/>
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 col-12">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-12">
                            <label for="nass_percentage" class="form-control-label">NASS</label><i class="fa fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Nilai Akhir Semester Sumatif"></i>
                          </div>
                          <div class="col-12">
                            <div class="input-group">
                              <input type="number" class="input-sm form-control percentage @error('nass_percentage') is-invalid @enderror" name="nass_percentage" value="{{ old('nass_percentage',($percentages ? $percentages->nass_percentage : 0)) }}" min="0" max="100" step="0.01" required="required"/>
                              <div class="input-group-append">
                                <span class="input-group-text">%</span>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <button id="btnSave" class="btn btn-brand-green-dark" type="submit">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                @if($riwayatKelas && count($riwayatKelas) > 0)
                    <div class="table-responsive">
                        <table class="table align-items-center table-sm" style="width:100%">
                        <table id="dataTable" class="table  align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px">#</th>
                                    <th>Nama</th>
                                    <th>Proyek</th>
                                    @if($tpfCount)
                                    @for($i=1;$i<=$tpfCount->kd;$i++)
                                    <th>{{ 'PF-'.$i }}</th>
                                    @endfor
                                    @endif
                                    <th>NAF</th>
                                    @if($tpDescs && count($tpDescs) > 0)
                                    @foreach($tpDescs as $t)
                                    <th>{{ $t->code }}</th>
                                    @endforeach
                                    @endif
                                    <th>NAS</th>
                                    <th>NTSS</th>
                                    <th>NASS</th>
                                    <th>NA Rapor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($riwayatKelas as $r)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td><label style="width: 150px;">{{ $r->identitas->student_name }}</label></td>
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->projectWithSeparator : 0 }}</td>
                                    @if($tpfCount)
                                    @for($i=1;$i<=$tpfCount->kd;$i++)
                                    <td>{{ isset($nilai[$r->id][$i]) ? $nilai[$r->id][$i] : 0 }}</td>
                                    @endfor
                                    @endif
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nafWithSeparator : 0 }}</td>
                                    @if($tpDescs && count($tpDescs) > 0)
                                    @foreach($tpDescs as $t)
                                    <td>{{ isset($nilai[$r->id][$t->id]) ? $nilai[$r->id][$t->id] : 0 }}</td>
                                    @endforeach
                                    @endif
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nasWithSeparator : 0 }}</td>
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->ntssWithSeparator : 0 }}</td>
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->nassWithSeparator : 0 }}</td>
                                    <td>{{ isset($nilai[$r->id]['akhir']) ? $nilai[$r->id]['akhir']->narWithSeparator : 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

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
<script>
 $(function(){
    $('.percentage').bind('keyup mouseup', function(e){
      var total = 0;
      var hasValue = 0;
      $('.percentage').each(function () {
         var value = parseFloat($(this).val());
         if(value > 0){
            total += value;
            hasValue++;
         }
      });
      if(hasValue > 0){
        if(total === 100){
            $('#btnSave').removeAttr('disabled');
            if($('#btnSave').hasClass('btn-secondary')){
                if(!$('#btnSave').hasClass('btn-brand-green-dark')){
                    $('#btnSave').addClass('btn-brand-green-dark');
                }
                $('#btnSave').removeClass('btn-secondary');
            }
        }
        else{
            $('#btnSave').attr('disabled','disabled');
            if($('#btnSave').hasClass('btn-brand-green-dark')){
                if(!$('#btnSave').hasClass('btn-secondary')){
                    $('#btnSave').addClass('btn-secondary');
                }
                $('#btnSave').removeClass('btn-brand-green-dark');
            }
        }
      }
    });
});
</script>
@endsection
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
