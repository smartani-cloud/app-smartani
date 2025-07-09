<<<<<<< HEAD
<form action="{{ route('skbm.perbarui', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}" id="skbm-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $detail->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Struktural/Guru Mapel
    </div>
    <div class="col-12">
      <h5>{{ $detail->jabatan->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Mata Pelajaran
    </div>
    <div class="col-12">
      <h5>{{ $detail->mataPelajaran ? $detail->mataPelajaran->subject_name : '-' }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Nama Pegawai
    </div>
    <div class="col-12">
      <h5>{{ $detail->pegawai->name }}</h5>
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
              <input id="inputStudents" type="text" name="students" class="form-control @error('students') is-invalid @enderror" value="{{ $detail->students }}">
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
              <input id="inputTeachingLoad" type="text" name="teaching_load" class="form-control @error('teaching_load') is-invalid @enderror" value="{{ $detail->teaching_load }}">
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
              <input type="text" name="teaching_decree_date" class="form-control" value="{{ $detail->teaching_decree_date ? date('d F Y', strtotime($detail->teaching_decree_date)) : ''}}" placeholder="Pilih tanggal" id="teachingDecreeDateInput">
              @error('teaching_decree_date')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
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
            <input id="number" class="form-control" name="teaching_decree_number" maxlength="255" placeholder="Tulis nomor SK mengajar" value="{{ $detail->teaching_decree_number }}">
            @error('teaching_decree_number')
            <span class="text-danger">{{ $message }}</span>
            @enderror
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
      <input id="save-dismassal" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.kepegawaian.skbm')
=======
<form action="{{ route('skbm.perbarui', ['tahunpelajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}" id="skbm-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $detail->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Struktural/Guru Mapel
    </div>
    <div class="col-12">
      <h5>{{ $detail->jabatan->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Mata Pelajaran
    </div>
    <div class="col-12">
      <h5>{{ $detail->mataPelajaran ? $detail->mataPelajaran->subject_name : '-' }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Nama Pegawai
    </div>
    <div class="col-12">
      <h5>{{ $detail->pegawai->name }}</h5>
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
              <input id="inputStudents" type="text" name="students" class="form-control @error('students') is-invalid @enderror" value="{{ $detail->students }}">
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
              <input id="inputTeachingLoad" type="text" name="teaching_load" class="form-control @error('teaching_load') is-invalid @enderror" value="{{ $detail->teaching_load }}">
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
              <input type="text" name="teaching_decree_date" class="form-control" value="{{ $detail->teaching_decree_date ? date('d F Y', strtotime($detail->teaching_decree_date)) : ''}}" placeholder="Pilih tanggal" id="teachingDecreeDateInput">
              @error('teaching_decree_date')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
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
            <input id="number" class="form-control" name="teaching_decree_number" maxlength="255" placeholder="Tulis nomor SK mengajar" value="{{ $detail->teaching_decree_number }}">
            @error('teaching_decree_number')
            <span class="text-danger">{{ $message }}</span>
            @enderror
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
      <input id="save-dismassal" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.kepegawaian.skbm')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
