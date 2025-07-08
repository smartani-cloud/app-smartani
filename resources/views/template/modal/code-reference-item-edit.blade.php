<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Kode</label>
          </div>
          <div class="col-lg-3 col-md-4 col-6">
            <input type="text" id="editCode" class="form-control form-control-sm @error('editCode') is-invalid @enderror" name="editCode" value="{{ old('editCode',$data->code) }}" maxlength="255" required="required">
            @error('editCode')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Nama</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" id="editName" class="form-control form-control-sm @error('editName') is-invalid @enderror" name="editName" value="{{ old('editName',$data->name) }}" maxlength="255" required="required">
            @error('editName')
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
      <input id="save-data" type="submit" class="btn btn-brand-purple" value="Simpan">
    </div>
  </div>
</form>