<form action="{{ route('evaluasi.perbarui') }}" id="eval-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $eval->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
    Nama
    </div>
    <div class="col-12">
    <h5>{{ $eval->pegawai->name }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="inputTempPsc" class="form-control-label">PSC Sementara</label>
          </div>
          <div class="col-12">
            <select aria-label="PscSementara" name="temp_psc" id="inputTempPsc" title="PscSementara" class="form-control @error('temp_psc') is-invalid @enderror">
              <option value="" {{ old('temp_psc', $eval->temp_psc_grade_id) ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
              @foreach($psc as $p)
              <option value="{{ $p->id }}" {{ old('temp_psc', $eval->temp_psc_grade_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12 pt-2">
            <label for="supervision" class="form-control-label">Hasil Supervisi</label>
          </div>
          <div class="col-12">
            <textarea id="supervision" class="form-control" name="supervision" maxlength="255" rows="3">{{ $eval->supervision_result }}</textarea>
          </div>
        </div>
      </div>
    </div>
  </div> <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12 pt-2">
            <label for="interview" class="form-control-label">Hasil Interview</label>
          </div>
          <div class="col-12">
            <textarea id="interview" class="form-control" name="interview" maxlength="255" rows="3">{{ $eval->interview_result }}</textarea>
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
      <input id="save-recent-education" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.datepicker')
