<div class="modal fade" id="validate-confirm" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success border-0">
        <h5 class="modal-title text-white">Setujui IKU</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body px-5 py-4">
        <div class="row mb-2">
          <div class="col-12">
			Apakah Anda yakin ingin menyetujui pengajuan IKU <span class="name font-weight-bold"></span>?
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="#" id="validate-link" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
</div>