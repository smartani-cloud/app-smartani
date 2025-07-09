<form action="{{ route('psc.rentang.perbarui') }}" id="rentang-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $set->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Nama Daftar</label>
          </div>
          <div class="col-12">
            <input type="text" id="name" class="form-control" name="name" maxlength="255" value="{{ $set->name }}" required="required">
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
			     @for($i=0;$i<5;$i++)
            <div class="row mb-2">
              <div class="col-4">
                <input type="text" class="form-control" name="grade[]" placeholder="Huruf" maxlength="3" value="{{ isset($set->grade[$i]) ? $set->grade[$i]->name : '' }}" required="required">
              </div>
              <div class="col-8">
                <div class="input-group">
                  <input type="number" class="input-sm form-control" name="start[]" placeholder="Awal" value="{{ isset($set->grade[$i]) ? $set->grade[$i]->start : '' }}" min="0" max="4.9" step="0.001" required="required"/>
                  <div class="input-group-prepend">
                    <span class="input-group-text">-</span>
                  </div>
                  <input type="number" class="input-sm form-control" name="end[]" placeholder="Akhir" value="{{ isset($set->grade[$i]) ? $set->grade[$i]->end : '' }}" min="0.1" max="5" step="0.001" required="required"/>
                </div>
              </div>
            </div>
			     @endfor
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
      <input id="save-range" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.datepicker')
