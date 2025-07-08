<form action="{{ route($route.'.payment.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="editPaymentDateInput" class="form-control-label">Tanggal<span class="text-danger">*</span></label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <div class="input-group date">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
              </div>
              <input type="text" name="edit_payment_date" class="form-control form-control-sm @error('edit_payment_date') is-invalid @enderror" value="{{ $data->date ? date('d F Y', strtotime($data->date)) : date('d F Y') }}" placeholder="Pilih tanggal" id="editPaymentDateInput" required="required">
            </div>
            @error('edit_payment_date')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="editPaymentNominal" class="form-control-label">Nominal<span class="text-danger">*</span></label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" id="editPaymentNominal" class="form-control form-control-sm @error('edit_payment_nominal') is-invalid @enderror number-separator" name="edit_payment_nominal" value="{{ old('edit_payment_nominal',($data->value ? $data->valueWithSeparator : 0)) }}" data-default="{{ $data->value ? $data->value : 0 }}" maxlength="12" required="required">
            <small class="form-text text-muted">Jika nominal melebihi tagihan, maka nominal yang akan tersimpan hanya senilai total tagihan yang ada</small>
            @error('edit_payment_nominal')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="editPaymentBalance" class="form-control-label">Sisa Tagihan</label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" id="editPaymentBalance" class="form-control form-control-sm number-separator" name="edit_payment_balance" value="{{ $data->outcomeMaterial->remainWithSeparator }}" maxlength="12" disabled="disabled">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="editPaymentNote" class="form-control-label">Keterangan</label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <textarea id="editPaymentNote" class="form-control form-control-sm @error('edit_payment_note') is-invalid @enderror" name="edit_payment_note" maxlength="50" rows="2">{{ $data->note }}</textarea>
            @error('edit_payment_note')
            <span class="text-danger">{{ $message }}</span>
            @enderror
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
      <input id="save-data" type="submit" class="btn btn-primary" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.global.datepicker')