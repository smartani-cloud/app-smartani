<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  @if($data->sales()->count() > 0)
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Perhatian!</strong> Mengubah data pembeli ini akan berdampak kepada data pembeli pada penjualan yang pernah dilakukan sebelumnya.
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if(isset($unitEditable) && $unitEditable && isset($unit) && Auth::user()->role_id != 3)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="selectUnit" class="form-control-label">Divisi</label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <select class="form-control form-control-sm @error('editUnit') is-invalid @enderror" name="editUnit" id="selectUnit" required="required">
              @foreach($unit as $u)
              <option value="{{ $u->id }}" {{ old('editUnit',$data->unit_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
              @endforeach
            </select>
            @error('editUnit')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Nama Pembeli</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" id="editName" class="form-control form-control-sm @error('editName') is-invalid @enderror" name="editName" value="{{ old('editName',$data->billing_to) }}" maxlength="255" required="required">
            @error('editName')
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
            <label for="normal-input" class="form-control-label">Nomor Telepon Pembeli</label>
          </div>
          <div class="col-lg-3 col-md-4 col-8">
            <input type="text" id="editPhoneNumber" class="form-control form-control-sm @error('editPhoneNumber') is-invalid @enderror" name="editPhoneNumber" value="{{ old('editPhoneNumber',$data->billing_phone_number) }}" placeholder="mis. 081234567890" maxlength="15">
            @error('editPhoneNumber')
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
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Nama Penerima</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" id="editShippingTo" class="form-control form-control-sm @error('editShippingTo') is-invalid @enderror" name="editShippingTo" value="{{ old('editShippingTo',$data->shipping_to) }}" maxlength="255" required="required">
            @error('editShippingTo')
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
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Alamat Penerima</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" id="editAddress1" class="form-control form-control-sm @error('editAddress1') is-invalid @enderror" name="editAddress1" value="{{ old('editAddress1',$data->shipping_address_1) }}" maxlength="255" required="required">
            @error('editAddress1')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12">
            <input type="text" id="editAddress2" class="form-control form-control-sm @error('editAddress2') is-invalid @enderror" name="editAddress2" value="{{ old('editAddress2',$data->shipping_address_2) }}" maxlength="255">
            @error('editAddress2')
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
            <label for="normal-input" class="form-control-label">Kode Pos Penerima</label>
          </div>
          <div class="col-lg-2 col-md-3 col-6">
            <input type="text" id="editPostalCode" class="form-control form-control-sm @error('editPostalCode') is-invalid @enderror" name="editPostalCode" value="{{ old('editPostalCode',$data->shipping_postal_code) }}" maxlength="5">
            @error('editPostalCode')
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
            <label for="normal-input" class="form-control-label">Nomor Telepon Penerima</label>
          </div>
          <div class="col-lg-3 col-md-4 col-8">
            <input type="text" id="editShippingPhoneNumber" class="form-control form-control-sm @error('editShippingPhoneNumber') is-invalid @enderror" name="editShippingPhoneNumber" value="{{ old('editShippingPhoneNumber',$data->shipping_phone_number) }}" placeholder="mis. 081234567890" maxlength="15">
            @error('editShippingPhoneNumber')
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