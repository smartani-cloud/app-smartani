<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  @if($editable)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="select2EditUnit" class="form-control-label">Unit</label>
          </div>
          <div class="col-12">
            <select class="form-control @error('editUnit') is-invalid @enderror" name="editUnit" id="select2EditUnit" required="required">
              @foreach($unit as $u)
              <option value="{{ $u->id }}" {{ old('editUnit',$data->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
              @endforeach
            </select>
            @error('editUnit')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  @else
  <div class="row mb-2">
    <div class="col-12 mb-1">Unit</div>
    <div class="col-12">
      <h5>{{ $data->unit->name }}</h5>
    </div>
  </div>
  @endif
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Nominal</label>
          </div>
          <div class="col-8">
            <input type="text" id="editNominal" class="form-control @error('editNominal') is-invalid @enderror number-separator" name="editNominal" value="{{ old('editNominal',$data->sppNominalWithSeparator)  }}" maxlength="15" required="required">
            @error('editNominal')
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
      <input id="save-data" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>