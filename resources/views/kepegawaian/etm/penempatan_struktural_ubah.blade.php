<form action="{{ route('struktural.perbarui', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}" id="penempatan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $detail->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Nama
    </div>
    <div class="col-12">
      <h5>{{ $detail->pegawai->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="selectPosition" class="form-control-label">Penempatan <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <select class="form-control" name="position" id="selectPosition" required="required">
              <option value="" {{ old('position') ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
              @foreach($jabatan as $j )
              <option value="{{ $j->id }}" {{ $detail->jabatan->id == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
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
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Masa Penempatan <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <div class="input-daterange input-group">
              <input type="text" class="input-sm form-control" name="period_start" placeholder="Mulai" value="{{ date('d F Y', strtotime($detail->period_start)) }}" required="required"/>
              <div class="input-group-prepend">
                <span class="input-group-text">-</span>
              </div>
              <input type="text" class="input-sm form-control" name="period_end" placeholder="Selesai" value="{{ date('d F Y', strtotime($detail->period_end)) }}" required="required"/>
            </div>
            @if($errors->any())
            <div class="alert alert-danger">
              <ul>
                @if($errors->first('period_start'))
                <li>{{ $errors->first('period_start') }}</li>
                @elseif($errors->first('period_end'))
                <li>{{ $errors->first('period_end') }}</li>
                @endif
              </ul>
            </div>
            @endif
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
