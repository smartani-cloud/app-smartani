<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="editDesc" class="form-control-label">Deskripsi</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" id="editDesc" class="form-control form-control-sm @error('desc') is-invalid @enderror" name="editDesc" maxlength="255" required="required" value="{{ $data->desc ? $data->desc : '' }}">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="editAmount" class="form-control-label">Nominal</label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" id="editAmount" class="form-control form-control-sm @error('editAmount') is-invalid @enderror number-separator" name="editAmount" value="{{ old('editAmount',$data->amountWithSeparator) }}" maxlength="15" required="required">
            @error('editAmount')
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

@include('template.footjs.kepegawaian.tooltip')