<form action="{{ route($route.'.update',['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber, 'tingkat' => $tingkat->id, 'mataPelajaran' => $mataPelajaran->id]) }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $item->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editDesc" class="form-control-label">Deskripsi</label>
          </div>
          <div class="col-12">
            <textarea id="editDesc" class="form-control" name="editDesc" maxlength="150" rows="3" required="required">{{ $item->description }}</textarea>
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