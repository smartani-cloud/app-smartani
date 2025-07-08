@extends('template.main.master')

@section('title')
SKBM
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">SKBM</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ route('skbm.index') }}">SKBM</a></li>
        <li class="breadcrumb-item active" aria-current="page">Lihat</li>
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
        <div class="row mb-2">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label for="academicYearOpt" class="form-control-label">Tahun Pelajaran</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $aktif->academic_year }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label for="academicYearOpt" class="form-control-label">Unit</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $unit->name }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-brand-green">Pembagian Tugas Mengajar Guru</h6>
              <div class="float-right">
                <a class="m-0 btn btn-brand-green-dark btn-sm" href="{{ route('skbm.ekspor', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}">Ekspor <i class="fas fa-file-export ml-1"></i></a>
                <button type="button" class="m-0 btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#add-form">Tambah <i class="fas fa-plus-circle ml-1"></i></button>
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
              @if($skbm && count($skbm->show) > 0)
              <div class="table-responsive">
                <table id="dataTable" class="table align-items-center table-flush">
                  <thead class="thead-light">
                    <tr>
                      <th style="width: 15px">#</th>
                      <th>Struktur/Guru Mapel</th>
                      <th>Mata Pelajaran</th>
                      <th>Nama</th>
                      <th>Jumlah Siswa Per Rombel</th>
                      <th>Beban Jam Mengajar</th>
                      <th>Tanggal SK Mengajar</th>
                      <th>SK Mengajar</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $no = 1; @endphp
                    @foreach($skbm->show as $s)
                    <tr>
                      <td>{{ $no++ }}</td>
                      <td>{{ $s->jabatan->name }}</td>
                      <td>{{ $s->mataPelajaran ? $s->mataPelajaran->subject_name : '-' }}</td>
                      <td>
                        <a href="{{ route('pegawai.detail', ['id' => $s->pegawai->id]) }}" class="text-info detail-link" target="_blank">
                          <div class="avatar-small d-inline-block">
                            <img src="{{ asset($s->pegawai->showPhoto) }}" alt="user-{{ $s->pegawai->id }}" class="avatar-img rounded-circle mr-1">
                          </div>
                          {{ $s->pegawai->name }}
                        </a>
                      </td>
                      <td>{{ $s->students ? $s->students : '-' }}</td>
                      <td>{{ $s->teaching_load ? $s->teaching_load : '-' }}</td>
                      <td>{{ $s->teaching_decree_date ? $s->teachingDecreeDateId : '-' }}</td>
                      <td>{{ $s->teaching_decree_number ? $s->teaching_decree_number : '-' }}</td>
                      <td>
                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('skbm.ubah', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}','{{ $s->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                        <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Tugas Mengajar Guru', '{{ addslashes(htmlspecialchars($s->pegawai->name)) }}', '{{ route('skbm.hapus', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name, 'id' => $s->id]) }}')"><i class="fas fa-trash"></i></a>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              @else
              <div class="text-center mx-3 mt-4 mb-5">
                <h3>Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data pegawai yang ditemukan</h6>
              </div>
              @endif
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Tambah Tugas Mengajar Guru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route('skbm.simpan', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}" id="skbm-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row mb-2">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="selectPosition" class="form-control-label">Struktural/Guru Mapel <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-12">
                    <select class="form-control" name="position" id="selectPosition" required="required">
                      <option value="" {{ old('position') ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
                      @foreach($jabatan as $j )
                      <option value="{{ $j->id }}" {{ old('position') == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                      @endforeach
                    </select>
                    @error('position')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="select2Subject" class="form-control-label">Mata Pelajaran</label>
                  </div>
                  <div class="col-12">
                    <select class="select2 form-control" name="subject" id="select2Subject">
                      @foreach($mapel as $m)
                      <option value="{{ $m->id }}" {{ old('subject') == $m->id ? 'selected' : '' }}>{{ $m->subject_name }}</option>
                      @endforeach
                    </select>
                    @error('subject')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="select2Employee" class="form-control-label">Pegawai <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-12">
                    <select class="select2 form-control" name="employee" id="select2Employee" required="required">
                      @foreach($pegawai as $p)
                      <option value="{{ $p->nip }}" {{ old('employee') == $p->nip ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endforeach
                    </select>
                    @error('employee')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="inputStudents" class="form-control-label">Jumlah Siswa Per Rombel</label>
                  </div>
                  <div class="col-xl-6 col-md-8 col-12">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputStudents" type="text" name="students" class="form-control @error('students') is-invalid @enderror" value="{{ old('students') }}">
                    </div>
                    @error('students')
                    <span class="mt-1 text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="inputTeachingLoad" class="form-control-label">Beban Jam Mengajar</label>
                  </div>
                  <div class="col-xl-6 col-md-8 col-12">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputTeachingLoad" type="text" name="teaching_load" class="form-control @error('teaching_load') is-invalid @enderror" value="{{ old('teaching_load') }}">
                    </div>
                    @error('teaching_load')
                    <span class="mt-1 text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="teachingDecreeDateInput" class="form-control-label">Tanggal SK Mengajar</label>
                  </div>
                  <div class="col-xl-8 col-md-9 col-12">
                    <div class="input-group date">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                      </div>
                      <input type="text" name="teaching_decree_date" class="form-control" value="{{ old('teaching_decree_date') }}" placeholder="Pilih tanggal" id="teachingDecreeDateInput">
                    </div>
                    @error('teaching_decree_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Nomor SK Mengajar</label>
                  </div>
                  <div class="col-12">
                    <input id="number" class="form-control" name="teaching_decree_number" maxlength="255" placeholder="Tulis nomor SK mengajar" value="{{ old('teaching_decree_number') }}">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-6 text-left">
              <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
            </div>
            <div class="col-6 text-right">
              <input type="submit" class="btn btn-brand-green-dark" value="Tambah">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Tugas Mengajar Guru</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
              <h5 class="font-weight-light mb-3">Memuat...</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" style="display: none;">
      </div>
    </div>
  </div>
</div>

@include('template.modal.konfirmasi_hapus')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.datatables')
@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.kepegawaian.select2')
@include('template.footjs.kepegawaian.skbm')
@include('template.footjs.modal.get_delete')
@include('template.footjs.modal.post_edit')
@endsection