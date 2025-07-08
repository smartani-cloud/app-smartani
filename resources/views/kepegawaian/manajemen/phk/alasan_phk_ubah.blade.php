<form action="{{ route('alasanphk.perbarui') }}" id="phk-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $alasan->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Alasan PHK</label>
          </div>
          <div class="col-12">
            @if($alasan->reason)
            <textarea id="reason" class="form-control" name="reason" maxlength="255" rows="3" required="required">{{ $alasan->reason }}</textarea>
            @else
            <textarea id="reason" class="form-control" name="reason" maxlength="255" rows="3" required="required"></textarea>
            @endif
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
      <input id="save-dismassal-reason" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>