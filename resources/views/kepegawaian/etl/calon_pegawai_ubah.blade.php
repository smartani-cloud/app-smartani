<form action="{{ route('calon.perbarui', ['id' => $calon->id]) }}" id="edit-form" method="post" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="return validateDate('inputBirthDate','birthDateError')">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $calon->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
      <div class="avatar-xl mx-auto"><img src="{{ asset($calon->showPhoto) }}" alt="calon-{{ $calon->id }}" class="avatar-img rounded-circle"></div>
    </div>
  </div>
  <hr>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Nama Lengkap
    </div>
    <div class="col-12">
      <h5>{{ $calon->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      NIK
    </div>
    <div class="col-12">
      <h5>{{ $calon->nik }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Jenis Kelamin
    </div>
    <div class="col-12">
      <h5>{{ ucwords($calon->jenisKelamin->name) }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12 mb-1">
      Tempat, Tanggal Lahir
    </div>
    <div class="col-12">
      <h5>{{ $calon->birth_place.', '.$calon->birthDateId }}</h5>
    </div>
  </div>
  <hr>
  <div class="row mb-2">
    <div class="col-12">
      <h6 class="font-weight-bold text-brand-green">Rekomendasi</h6>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="acceptanceOpt" class="form-control-label">Penerimaan <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            @foreach($penerimaan as $p)
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="acceptanceOpt{{ $p->id }}" name="acceptance_status" class="custom-control-input" value="{{ $p->id }}" {{ old('acceptance_status', $calon->acceptance_status_id) == $p->id ? 'checked' : '' }} required="required">
              <label class="custom-control-label" for="acceptanceOpt{{ $p->id }}">{{ ucwords($p->status) }}</label>
            </div>
            @endforeach
            @error('acceptance_status')
            <span class="text-danger d-block">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="unitRow" class="row" style="{{ old('acceptance_status', $calon->acceptance_status_id) == '1' ? '' : 'display: none;' }}">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="inputUnit" class="form-control-label">Unit Penempatan <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <div class="row">
              @foreach($unit as $u)
              <div class="col-6">
                <div class="custom-control custom-checkbox mb-1">
                  <input id="unit-{{$u->id}}" type="checkbox" name="unit[]" class="custom-control-input" value="{{ $u->id }}" {{ old('unit', $calon->units->pluck('id')->toArray()) && is_array(old('unit', $calon->units->pluck('id')->toArray())) && in_array($u->id, old('unit', $calon->units->pluck('id')->toArray() )) ? 'checked' : '' }}>
                  <label class="custom-control-label" for="unit-{{$u->id}}">{{ $u->name }}</label>
                </div>
              </div>
              @endforeach
            </div>
            @error('unit')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="positionRow" class="row" style="{{ old('acceptance_status', $calon->acceptance_status_id) == '1' ? '' : 'display: none;' }}">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="select2Position" class="form-control-label">Jabatan</label>
          </div>
          <div  class="col-12">
            <select class="select2-multiple form-control @error('position') is-invalid @enderror" name="position[]" multiple="multiple" id="select2Position" {{ (old('position') && count(old('position')) > 0) || $calon->units()->count() > 0 ? '' : 'disabled="disabled"' }}>
              @foreach($jabatan as $j)
              <option value="{{ $j->id }}" class=" bg-gray-300" {{ (old('position') && count(old('position')) > 0) || $calon->jabatans()->count() > 0 ? (old('position', $calon->jabatans->pluck('id')->toArray()) && is_array(old('position', $calon->jabatans->pluck('id')->toArray())) && in_array($j->id, old('position', $calon->jabatans->pluck('id')->toArray() )) ? 'selected' : '') : '' }} data-unit="{{ $j->unit_id }}" {{ old('unit', $calon->units->pluck('id')->toArray()) && is_array(old('unit', $calon->units->pluck('id')->toArray())) && in_array($j->unit_id,old('unit', $calon->units->pluck('id')->toArray() )) ? '' : 'disabled="disabled"' }}>{{ $j->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="statusRow" class="row" style="{{ old('acceptance_status', $calon->acceptance_status_id) == '1' ? '' : 'display: none;' }}">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="statusOpt" class="form-control-label">Status <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            @foreach($status as $s)
            <div class="custom-control custom-radio mb-1">
              <input type="radio" id="statusOpt{{ $s->id }}" name="employee_status" class="custom-control-input" value="{{ $s->id }}" {{ old('employee_status', $calon->employee_status_id) == $s->id ? 'checked' : '' }} {{ $calon->employee_status_id ? 'required' : ''}}>
              <label class="custom-control-label" for="statusOpt{{ $s->id }}">{{ $s->status }}</label>
            </div>
            @endforeach
            @error('employee_status')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div id="periodRow" class="row" style="{{ old('acceptance_status', $calon->acceptance_status_id) == '1' ? '' : 'display: none;' }}">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="statusOpt" class="form-control-label">Masa Kerja <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <div class="input-daterange input-group">
              <input type="text" class="input-sm form-control" name="period_start" placeholder="Mulai" value="{{ old('period_start', $calon->period_start) ? date('d F Y', strtotime(old('period_start', $calon->period_start))) : '' }}" {{ $calon->period_start ? 'required' : ''}}/>
              <div class="input-group-prepend">
                <span class="input-group-text">-</span>
              </div>
              <input type="text" class="input-sm form-control" name="period_end" placeholder="Selesai" value="{{ old('period_end', $calon->period_end) ? date('d F Y', strtotime(old('period_end', $calon->period_end))) : '' }}" {{ $calon->period_end ? 'required' : ''}}/>
            </div>
            @error('period_start')
            <span class="text-danger">{{ $message }}</span>
            @enderror
            @error('period_end')
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
      <input type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.acceptance')
@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.kepegawaian.positions-recommendation')
@include('template.footjs.kepegawaian.required-checkbox')
@include('template.footjs.kepegawaian.select2-multiple')