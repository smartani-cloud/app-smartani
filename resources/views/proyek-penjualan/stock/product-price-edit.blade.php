<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="editProduct" class="form-control-label">Produk</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            {{ $data->product->productName }}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="selectEditType" class="form-control-label">Jenis Penjualan</label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            @if($types && $disabledTypes && $disabledTypes->count() < count($types))
            <select class="form-control form-control-sm @error('editType') is-invalid @enderror" name="editType" id="selectType" required="required">
              @foreach($types as $t)
              <option value="{{ $t->id }}" {{ old('editType',$data->sales_type_id) == $t->id ? 'selected' : '' }} {{ $data->sales_type_id != $t->id && in_array($t->id,$disabledTypes->toArray()) ? 'disabled' : '' }}>{{ $t->name }}</option>
              @endforeach
            </select>
            @error('editType')
            <span class="text-danger">{{ $message }}</span>
            @enderror
            @else
            {{ $data->salesType->name }}
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="editPrice" class="form-control-label">Harga</label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" id="editPrice" class="form-control form-control-sm @error('editPrice') is-invalid @enderror number-separator" name="editPrice" value="{{ old('editPrice',$data->priceWithSeparator) }}" maxlength="10" required="required">
            @error('editPrice')
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

@include('template.footjs.global.tooltip')