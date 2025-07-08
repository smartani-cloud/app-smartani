<div class="modal fade" id="disjoin-confirm" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info border-0">
        <h5 class="modal-title text-white">Konfirmasi PHK Pegawai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body px-5 py-4">
        <div class="row mb-2">
          <div class="col-12">
            Apakah Anda yakin ingin mengonfirmasi data <span class="name font-weight-bold"></span> telah di-PHK sebagai pegawai?
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="#" id="disjoin-link" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-info">Ya, Konfirmasi</button>
        </form>
      </div>
    </div>
  </div>
</div>