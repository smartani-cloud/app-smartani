<form action="{{ route('pendidikanterakhir.perbarui') }}" id="pendidikan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $pendidikan->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Pendidikan Terakhir</label>
          </div>
          <div class="col-12">
            @if($pendidikan->name)
            <input id="name" class="form-control" name="name" maxlength="255" placeholder="mis. SD, SMP, SMA" required="required" value="{{ $pendidikan->name }}">
            @else
            <input id="name" class="form-control" name="name" maxlength="255" placeholder="mis. SD, SMP, SMA" required="required">
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12 pt-2">
            <label for="normal-input" class="form-control-label">Deskripsi</label>
          </div>
          <div class="col-12">
            @if($pendidikan->desc)
            <textarea id="desc" class="form-control" name="desc" maxlength="255" rows="3">{{ $pendidikan->desc }}</textarea>
            @else
            <textarea id="desc" class="form-control" name="desc" maxlength="255" rows="3"></textarea>
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
      <input id="save-recent-education" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>