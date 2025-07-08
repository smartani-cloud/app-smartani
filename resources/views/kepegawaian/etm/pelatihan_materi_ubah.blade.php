<form action="{{ route('pelatihan.materi.perbarui') }}" id="pelatihan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $pelatihan->id }}">
  <div class="row mb-2">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Materi Pelatihan <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <input id="name" class="form-control" name="name" maxlength="255" value="{{ $pelatihan->name }}" required="required" {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
            @error('name')
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
            <label for="normal-input" class="form-control-label">Deskripsi</label>
          </div>
          <div class="col-12">
            <textarea id="desc" class="form-control" name="desc" maxlength="255" rows="3">{{ $pelatihan->desc }}</textarea>
            @error('desc')
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
            <label for="selectOrganizer" class="form-control-label">Penyelenggara <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <select class="form-control" name="organizer" id="selectOrganizer" required="required" {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
              <option value="" {{ $pelatihan->organizer_id ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
              @foreach($unit as $u)
              <option value="{{ $u->id }}" {{ $pelatihan->organizer_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
              @endforeach
            </select>
            @error('organizer')
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
            <label for="editSpeakerCategory" class="form-control-label">Jenis Narasumber <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="editSpeakerCategory1" name="speaker_category" class="custom-control-input" value="1" {{ $pelatihan->speaker_id && !$pelatihan->speaker_name ? 'checked' : '' }} required="required" {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
              <label class="custom-control-label" for="editSpeakerCategory1">Civitas</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="editSpeakerCategory2" name="speaker_category" class="custom-control-input" value="2" {{ $pelatihan->speaker_name ? 'checked' : '' }} required="required" {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
              <label class="custom-control-label" for="editSpeakerCategory2">Lainnya</label>
            </div>
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
            <label for="editSpeaker" class="form-control-label">Narasumber <span class="text-danger">*</span></label>
          </div>
          <div id="editSpeakerIdCol"  class="col-12" style="{{ $pelatihan->speaker_id ? '' : 'display: none' }}">
            <select class="select2 form-control" name="speaker" id="editSpeaker" {{ $pelatihan->speaker_id && !$pelatihan->speaker_name ? 'required="required"' : '' }} {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
              <option value="" {{ $pelatihan->speaker_id ? '' : 'selected' }} disabled="disabled">Pilih dari daftar pegawai</option>
              @foreach($pegawai as $p)
              <option value="{{ $p->id }}" {{ $pelatihan->speaker_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
            @error('speaker')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
          <div id="editSpeakerNameCol" class="col-12" style="{{ $pelatihan->speaker_name ? '' : 'display: none' }}">
            <input id="speakerName" class="form-control" name="speaker_name" value="{{ $pelatihan->speaker_name }}" maxlength="255" placeholder="Nama lengkap narasumber"  {{ $pelatihan->speaker_name ? 'required="required"' : '' }} {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
            @error('speaker_name')
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
            <label for="dateInput" class="form-control-label">Tanggal Pelaksanaan</label>
          </div>
          <div class="col-xl-8 col-md-9 col-12">
            <div class="input-group date">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
              </div>
              <input type="text" name="date" class="form-control" value="{{ $pelatihan->date ? date('d F Y', strtotime($pelatihan->date)) : '' }}" placeholder="Pilih tanggal" id="dateInput">
            </div>
            @error('date')
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
            <label for="normal-input" class="form-control-label">Tempat</label>
          </div>
          <div class="col-12">
            <input id="place" class="form-control" name="place" value="{{ $pelatihan->place }}" maxlength="255">
            @error('place')
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
            <label for="position" class="form-control-label">Sasaran <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <select class="select2-multiple form-control" name="position[]" multiple="multiple" id="position" required="required" {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
              @foreach($jabatan as $j)
              <option value="{{ $j->id }}" {{ count($sasaran) > 0 ? ($sasaran->contains($j->id) ? 'selected' : '') : '' }}>{{ $j->name }}</option>
              @endforeach
            </select>
            @if($pelatihan->education_acc_status_id != 1)
            <button type="button" class="btn btn-brand-green-dark btn-sm btn-select-all mt-2" data-target="position">Pilih Semua</button>
            @endif
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
            <label for="editSemesterOpt" class="form-control-label">Semester <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            @foreach($semester as $s)
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="editSemesterOpt{{ $s->semesterNumber }}" name="semester" class="custom-control-input" value="{{ $s->semesterNumber }}" {{ $pelatihan->semester->semesterNumber == $s->semesterNumber ? 'checked' : '' }} required="required" {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
              <label class="custom-control-label" for="editSemesterOpt{{ $s->semesterNumber }}">{{ ucwords($s->semester) }}</label>
            </div>
            @endforeach
            @error('semester')
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
            <label for="editStatusOpt" class="form-control-label">Sifat <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            @foreach($status as $s)
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="editStatusOpt{{ $s->id }}" name="status" class="custom-control-input" value="{{ $s->id }}" {{ $pelatihan->mandatory_status_id == $s->id ? 'checked' : '' }} required="required" {{ $pelatihan->education_acc_status_id == 1 ? 'disabled="disabled"' : '' }}>
              <label class="custom-control-label" for="editStatusOpt{{ $s->id }}">{{ ucwords($s->status) }}</label>
            </div>
            @endforeach
            @error('status')
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
      <input id="save-training" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.kepegawaian.edit-training-speaker')
@include('template.footjs.kepegawaian.select2-multiple')
