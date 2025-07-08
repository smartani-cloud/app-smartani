<div class="modal fade" id="delete-confirm" tabindex="-1" role="dialog" aria-labelledby="hapusModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box">
          <i class="material-icons">&#xE889;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin menunda <span class="item font-weight-bold"></span> dari daftar <span class="title text-lowercase"></span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak</button>
        <form action="#" id="delete-link" method="post">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}
          <button type="submit" class="btn btn-danger">Ya, Tunda</button>
        </form>
      </div>
    </div>
  </div>
</div>
