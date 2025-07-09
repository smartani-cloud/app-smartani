<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Nama</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" id="editName" class="form-control form-control-sm @error('editName') is-invalid @enderror" name="editName" value="{{ old('editName',$data->name) }}" maxlength="255" required="required">
            @error('editName')
            <span class="text-danger">{{ $message }}</span>
            @enderror
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
            <label for="normal-input" class="form-control-label">Nomor SKU<i class="fa fa-lg fa-question-circle text-light ml-2" data-toggle="tooltip" data-original-title="Stock Keeping Unit"></i></label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" id="editSku" class="form-control form-control-sm @error('editSku') is-invalid @enderror" name="editSku" value="{{ old('editSku',$data->sku_number) }}" maxlength="15">
            @error('editSku')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  @if(isset($unitEditable) && $unitEditable && isset($unit) && Auth::user()->role_id != 3)
  <div class="row">
    <div class="col-lg-10 col-md-12">
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
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="selectCategory" class="form-control-label">Kategori</label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <select class="select2 form-control form-control-sm @error('editCategory') is-invalid @enderror" name="editCategory" id="selectCategory" required="required">
              @foreach($category as $c)
              <option value="{{ $c->id }}" {{ old('editCategory',$data->product_category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
            @error('editCategory')
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