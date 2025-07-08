@if($data)
<div class="alert alert-warning" role="alert">
  <strong>Perhatian!</strong> Perubahan {{ strtolower($active) }} mungkin akan berpengaruh pada rapor terdahulu
</div>
@endif
<form action="{{ route($route.'.update') }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editName" class="form-control-label">Nama Elemen Capaian Belajar</label>
          </div>
          <div class="col-12">
            <input type="text" id="editName" class="form-control" name="editName" maxlength="100" required="required" value="{{ $data->dev_aspect ? $data->dev_aspect : '' }}">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div>

  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      <input type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>