<<<<<<< HEAD
<div class="modal fade" id="edit-all-form" tabindex="-1" role="dialog" aria-labelledby="editAllModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-purple border-0">
        <h5 class="modal-title text-white">Atur Tanggal Penetapan Sekaligus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($kategori->placement.'.perbarui.semua', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}" id="{{ $kategori->placement }}-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="placementDateInput" class="form-control-label">Penetapan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-xl-8 col-md-9 col-12">
					<div class="input-group date">
					  <div class="input-group-prepend">
						<span class="input-group-text"><i class="fas fa-calendar"></i></span>
					  </div>
					  <input type="text" name="placement_date" class="form-control" placeholder="Pilih tanggal" id="placementDateInput" required="required">
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
=======
<div class="modal fade" id="edit-all-form" tabindex="-1" role="dialog" aria-labelledby="editAllModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-purple border-0">
        <h5 class="modal-title text-white">Atur Tanggal Penetapan Sekaligus</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($kategori->placement.'.perbarui.semua', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}" id="{{ $kategori->placement }}-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="placementDateInput" class="form-control-label">Penetapan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-xl-8 col-md-9 col-12">
					<div class="input-group date">
					  <div class="input-group-prepend">
						<span class="input-group-text"><i class="fas fa-calendar"></i></span>
					  </div>
					  <input type="text" name="placement_date" class="form-control" placeholder="Pilih tanggal" id="placementDateInput" required="required">
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
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
