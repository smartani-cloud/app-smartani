<form action="{{ route($route.'.perbarui') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">{{ $active }}</label>
          </div>
          <div class="col-12">
            @if($data->status)
            <textarea id="name" class="form-control" name="name" maxlength="255" rows="3" required="required">{{ substr(strstr($data->status," "), 1) }}</textarea>
            @else
            <textarea id="name" class="form-control" name="name" maxlength="255" rows="3" required="required"></textarea>
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
      <input id="save-data" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>