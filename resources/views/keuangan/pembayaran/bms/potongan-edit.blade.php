<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editName" class="form-control-label">Nama</label>
          </div>
          <div class="col-12">
            <input type="text" id="editName" class="form-control @error('editName') is-invalid @enderror" name="editName" value="{{ old('editName',$data->name) }}" required="required"/>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($data->isPercentage)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editPercentage" class="form-control-label">Potongan</label>
          </div>
          <div class="col-6">
            <div class="input-group">
              <input type="number" class="input-sm form-control @error('editPercentage') is-invalid @enderror" name="editPercentage" value="{{ old('editPercentage',$data->percentage) }}" min="0" max="100" step="0.01" required="required"/>
              <div class="input-group-append">
                <span class="input-group-text">%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @else
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editNominal" class="form-control-label">Potongan</label>
          </div>
          <div class="col-8">
            <input type="text" id="editNominal" class="form-control @error('editNominal') is-invalid @enderror number-separator" name="editNominal" value="{{ old('editNominal',$data->nominalWithSeparator) }}" maxlength="15">
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif

  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      <input id="save-data" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>