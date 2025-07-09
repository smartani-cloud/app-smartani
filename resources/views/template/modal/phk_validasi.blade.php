<<<<<<< HEAD
<div class="modal fade" id="disjoin-confirm" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success border-0">
        <h5 class="modal-title text-white">Setujui PHK Pegawai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body px-5 py-4">
        <div class="row mb-2">
          <div class="col-12">
			Pastikan Anda sudah mengonsultasikan keputusan ini dengan ketua yayasan terlebih dahulu.<br>
            Apakah Anda yakin ingin menyetujui pengajuan PHK <span class="name font-weight-bold"></span>?
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="#" id="disjoin-link" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
=======
<div class="modal fade" id="disjoin-confirm" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success border-0">
        <h5 class="modal-title text-white">Setujui PHK Pegawai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body px-5 py-4">
        <div class="row mb-2">
          <div class="col-12">
			Pastikan Anda sudah mengonsultasikan keputusan ini dengan ketua yayasan terlebih dahulu.<br>
            Apakah Anda yakin ingin menyetujui pengajuan PHK <span class="name font-weight-bold"></span>?
          </div>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="#" id="disjoin-link" method="post">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <button type="submit" class="btn btn-success">Ya, Setujui</button>
        </form>
      </div>
    </div>
  </div>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</div>