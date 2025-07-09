  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Nama Daftar</label>
          </div>
          <div class="col-12">
            {{ $set->name }}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Rentang Nilai</label>
          </div>
          <div class="col-12">
			     @for($i=0;$i<$set->grade()->count();$i++)
            <div class="row mb-2">
              <div class="col-4">
                <input type="text" class="form-control" name="grade[]" placeholder="Huruf" value="{{ isset($set->grade[$i]) ? $set->grade[$i]->name : '' }}" disabled="disabled">
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="text" class="input-sm form-control" name="start[]" placeholder="Awal" value="{{ isset($set->grade[$i]) ? $set->grade[$i]->start : '' }}" disabled="disabled"/>
                  <div class="input-group-prepend">
                    <span class="input-group-text">-</span>
                  </div>
                  <input type="text" class="input-sm form-control" name="end[]" placeholder="Akhir" value="{{ isset($set->grade[$i]) ? $set->grade[$i]->end : '' }}" disabled="disabled"/>
                </div>
              </div>
            </div>
			     @endfor
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="alert alert-danger" role="alert">
        Rentang nilai sudah tidak dapat diubah karena sudah menjadi acuan nilai akhir
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
  </div>