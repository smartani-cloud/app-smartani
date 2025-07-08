<div class="modal fade" id="delete-confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger border-0">
        <h5 class="modal-title text-white">Batalkan <span class="title"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body p-5">
        Apakah Anda yakin ingin membatalkan pengajuan PHK <span class="item font-weight-bold"></span>?
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="#" id="delete-link" method="post">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
        </form>
      </div>
    </div>
  </div>
</div>