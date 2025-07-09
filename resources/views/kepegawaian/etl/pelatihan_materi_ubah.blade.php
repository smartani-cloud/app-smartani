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
            <input id="name" class="form-control" name="name" maxlength="255" value="{{ $pelatihan->name }}" required="required">
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
  @if($pelatihan->organizer)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Penyelenggara
    </div>
    <div class="col-12 mb-2">
      <h5>{{ $pelatihan->organizer }}</h5>
    </div>
  </div>
  @endif
  <div class="row mb-2">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editSpeakerCategory" class="form-control-label">Jenis Narasumber <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="editSpeakerCategory1" name="speaker_category" class="custom-control-input" value="1" {{ $pelatihan->speaker_id && !$pelatihan->speaker_name ? 'checked' : '' }} required="required" >
              <label class="custom-control-label" for="editSpeakerCategory1">Pegawai</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="editSpeakerCategory2" name="speaker_category" class="custom-control-input" value="2" {{ $pelatihan->speaker_name ? 'checked' : '' }} required="required">
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
            <select class="select2 form-control" name="speaker" id="editSpeaker" {{ $pelatihan->speaker_id && !$pelatihan->speaker_name ? 'required="required"' : '' }}>
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
            <input id="speakerName" class="form-control" name="speaker_name" value="{{ $pelatihan->speaker_name }}" maxlength="255" placeholder="Nama lengkap narasumber"  {{ $pelatihan->speaker_name ? 'required="required"' : '' }}>
            @error('speaker_name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($pelatihan->date)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Tanggal Pelaksanaan
    </div>
    <div class="col-12">
      <h5>{{ date('j M Y', strtotime($pelatihan->date)) }}</h5>
    </div>
  </div>
  @endif
  @if($pelatihan->place)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Tempat
    </div>
    <div class="col-12">
      <h5>{{ $pelatihan->place }}</h5>
    </div>
  </div>
  @endif
  @if(count($pelatihan->sasaran) > 0)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Sasaran
    </div>
    <div class="col-12">
      <h4>
        @foreach($pelatihan->sasaran as $p)
        <span class="badge badge-light font-weight-normal">{{ $p->jabatan->name }}</span>
        @endforeach
      </h4>
    </div>
  </div>
  @endif
  @if($pelatihan->semester)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Semester
    </div>
    <div class="col-12">
      <h5>{{ $pelatihan->semester->semester }}</h5>
    </div>
  </div>
  @endif
  @if($pelatihan->status)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Sifat
    </div>
    <div class="col-12">
      <h5>{{ ucwords($pelatihan->status->status) }}</h5>
    </div>
  </div>
  @endif
  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      <input id="save-training" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.edit-training-speaker')
