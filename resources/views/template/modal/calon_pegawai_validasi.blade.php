<div class="modal fade" id="validate-confirm" tabindex="-1" role="dialog" aria-labelledby="validateModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success border-0">
        <h5 class="modal-title text-white">Persetujuan Calon Pegawai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body px-5 py-4">
        <div class="row mb-2">
          <div class="col-12">
            Apakah Anda yakin ingin menyetujui rekomendasi calon pegawai sebagai berikut:
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            Nama
          </div>
          <div class="col-8">
            <span class="name font-weight-bold"></span>
          </div>
        </div>
        <div class="row">
          <div class="col-4">
            Rekomendasi Penerimaan
          </div>
          <div class="col-8">
            <span class="acceptance font-weight-bold"></span>
          </div>
        </div>
        <div id="unitRecommendation" class="row">
          <div class="col-4">
            Unit Penempatan
          </div>
          <div class="col-8">
            <span class="unit font-weight-bold"></span>
          </div>
        </div>
        <div id="positionRecommendation" class="row">
          <div class="col-4">
            Jabatan
          </div>
          <div class="col-8">
            <span class="position font-weight-bold"></span>
          </div>
        </div>
        <div id="statusRecommendation" class="row">
          <div class="col-4">
            Status
          </div>
          <div class="col-8">
            <span class="status font-weight-bold"></span>
          </div>
        </div>
        <div id="periodRecommendation" class="row">
          <div class="col-4">
            Masa Kerja
          </div>
          <div class="col-8">
            <span class="period font-weight-bold"></span>
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