<<<<<<< HEAD
<div class="modal fade" id="end-confirm" tabindex="-1" role="dialog" aria-labelledby="endModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success border-0">
        <h5 class="modal-title text-white">Pelatihan Telah Selesai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body px-5 py-4">
        <div class="row mb-2">
          <div class="col-12">
            Apakah Anda yakin pelatihan ini telah selesai diselenggarakan pada:
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            Hari, Tanggal
          </div>
          <div class="col-8">
            <span class="modal-date font-weight-bold"></span>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            Tempat
          </div>
          <div class="col-8">
            <span class="modal-place font-weight-bold"></span>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Belum</button>
        <form action="#" id="end-link" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Selesai</button>
        </form>
      </div>
    </div>
  </div>
=======
<div class="modal fade" id="end-confirm" tabindex="-1" role="dialog" aria-labelledby="endModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success border-0">
        <h5 class="modal-title text-white">Pelatihan Telah Selesai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body px-5 py-4">
        <div class="row mb-2">
          <div class="col-12">
            Apakah Anda yakin pelatihan ini telah selesai diselenggarakan pada:
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            Hari, Tanggal
          </div>
          <div class="col-8">
            <span class="modal-date font-weight-bold"></span>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            Tempat
          </div>
          <div class="col-8">
            <span class="modal-place font-weight-bold"></span>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Belum</button>
        <form action="#" id="end-link" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Selesai</button>
        </form>
      </div>
    </div>
  </div>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</div>