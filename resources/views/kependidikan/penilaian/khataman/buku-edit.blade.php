@if($data)
<div class="alert alert-warning" role="alert">
  <strong>Perhatian!</strong> Perubahan judul buku mungkin akan berpengaruh pada rapor terdahulu
</div>
@endif
<form action="{{ route($route.'.update',['unit' => $unit->name]) }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editTitle" class="form-control-label">Judul Buku <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <input type="text" id="editTitle" class="form-control" name="editTitle" maxlength="150" required="required" value="{{ $data->title ? $data->title : '' }}">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editPages" class="form-control-label">Jumlah Halaman</label>
          </div>
          <div class="col-6">
            <input type="number" class="form-control @error('editPages') is-invalid @enderror" name="editPages" min="1" value="{{ $data->total_pages ? $data->total_pages : '' }}"/>
            @error('editPages')
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