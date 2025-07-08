<div class="modal fade" id="edit-all-form" tabindex="-1" role="dialog" aria-labelledby="editAllModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-purple border-0">
        <h5 class="modal-title text-white">Atur Penempatan Struktural Sekaligus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route('struktural.perbarui.semua') }}" id="struktural-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Masa Penempatan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-12">
                    <div class="input-daterange input-group">
                      <input id="inputPeriodStart" type="text" class="input-sm form-control" name="input_period_start" placeholder="Mulai" required="required"/>
                      <div class="input-group-prepend">
                        <span class="input-group-text">-</span>
                      </div>
                      <input id="inputPeriodEnd" type="text" class="input-sm form-control" name="input_period_end" placeholder="Selesai" required="required"/>
                    </div>
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
              <input id="save-all" type="submit" class="btn btn-brand-purple-dark" value="Simpan">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>