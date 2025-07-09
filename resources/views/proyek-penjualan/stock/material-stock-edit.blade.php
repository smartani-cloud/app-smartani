<<<<<<< HEAD
<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="select2EditMaterial" class="form-control-label">Bahan Baku</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <select class="select2 form-control @error('editMaterial') is-invalid @enderror" name="editMaterial" id="select2EditMaterial" required="required">
              @foreach($material as $m)
              <option value="{{ $m->id }}" {{ old('editMaterial',$data->material_id) == $m->id ? 'selected' : '' }} data-unit="{{ $m->unit_id }}">{{ Auth::user()->role_id == 3 ? $m->name : $m->nameUnit }}</option>
              @endforeach
            </select>
            @error('editMaterial')
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
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="select2EditSupplier" class="form-control-label">Pemasok</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            @php
            $selectedUnit = $material && count($material) > 0 ? $material->first()->unit_id : null;
            @endphp
            <select class="select2 form-control @error('editSupplier') is-invalid @enderror" name="editSupplier" id="select2EditSupplier" required="required">
              @foreach($supplier as $s)
              <option value="{{ $s->id }}" {{ old('editSupplier',$data->supplier_id) == $s->id ? 'selected' : '' }} data-unit="{{ $s->unit_id }}" {{ $selectedUnit && $s->unit_id != $selectedUnit ? 'disabled="disabled"' : null }}>{{ Auth::user()->role_id == 3 ? $s->name : $s->nameUnit }}</option>
              @endforeach
            </select>
            @error('editSupplier')
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
            <label for="normal-input" class="form-control-label">MOQ<i class="fa fa-lg fa-question-circle text-light ml-2" data-toggle="tooltip" data-original-title="Minimum Order Quantity"></i></label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" id="editMoq" class="form-control @error('editMoq') is-invalid @enderror number-separator" name="editMoq" value="{{ old('editMoq') ? old('editMoq') : $data->moqWithSeparator }}" maxlength="12" required="required">
            @error('editMoq')
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
            <label for="normal-input" class="form-control-label">Harga</label>
          </div>
          <div class="col-lg-6 col-md-8 col-12">
            <input type="text" id="editPrice" class="form-control @error('editPrice') is-invalid @enderror number-separator" name="editPrice" value="{{ old('editPrice') ? old('editPrice') : $data->priceWithSeparator }}" maxlength="15" required="required">
            @error('editPrice')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group row">
        <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-4 col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="logCheck" name="log">
            <label class="form-check-label" for="logCheck">
              Simpan sebagai periode harga baru
            </label>
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

@include('template.footjs.global.select2')
@include('template.footjs.global.tooltip')

@if(Auth::user()->role_id != 3)
<script type="text/javascript">
$(document).ready(function(){
    $('select[name="editMaterial"]').change(function() {
      var unitSelected = $(this).children("option:selected").attr('data-unit');
      $('select[name="editSupplier"] option').each(function(){
        $(this).removeAttr('disabled').removeClass('bg-gray-300');
      });
      if(unitSelected){
        console.log(unitSelected);
        $('select[name="editSupplier"] option[data-unit!='+unitSelected+']').each(function(){
          $(this).attr('disabled','disabled').addClass('bg-gray-300');
        });
      }
    });
});
</script>
=======
<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="select2EditMaterial" class="form-control-label">Bahan Baku</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <select class="select2 form-control @error('editMaterial') is-invalid @enderror" name="editMaterial" id="select2EditMaterial" required="required">
              @foreach($material as $m)
              <option value="{{ $m->id }}" {{ old('editMaterial',$data->material_id) == $m->id ? 'selected' : '' }} data-unit="{{ $m->unit_id }}">{{ Auth::user()->role_id == 3 ? $m->name : $m->nameUnit }}</option>
              @endforeach
            </select>
            @error('editMaterial')
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
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="select2EditSupplier" class="form-control-label">Pemasok</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            @php
            $selectedUnit = $material && count($material) > 0 ? $material->first()->unit_id : null;
            @endphp
            <select class="select2 form-control @error('editSupplier') is-invalid @enderror" name="editSupplier" id="select2EditSupplier" required="required">
              @foreach($supplier as $s)
              <option value="{{ $s->id }}" {{ old('editSupplier',$data->supplier_id) == $s->id ? 'selected' : '' }} data-unit="{{ $s->unit_id }}" {{ $selectedUnit && $s->unit_id != $selectedUnit ? 'disabled="disabled"' : null }}>{{ Auth::user()->role_id == 3 ? $s->name : $s->nameUnit }}</option>
              @endforeach
            </select>
            @error('editSupplier')
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
            <label for="normal-input" class="form-control-label">MOQ<i class="fa fa-lg fa-question-circle text-light ml-2" data-toggle="tooltip" data-original-title="Minimum Order Quantity"></i></label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" id="editMoq" class="form-control @error('editMoq') is-invalid @enderror number-separator" name="editMoq" value="{{ old('editMoq') ? old('editMoq') : $data->moqWithSeparator }}" maxlength="12" required="required">
            @error('editMoq')
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
            <label for="normal-input" class="form-control-label">Harga</label>
          </div>
          <div class="col-lg-6 col-md-8 col-12">
            <input type="text" id="editPrice" class="form-control @error('editPrice') is-invalid @enderror number-separator" name="editPrice" value="{{ old('editPrice') ? old('editPrice') : $data->priceWithSeparator }}" maxlength="15" required="required">
            @error('editPrice')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group row">
        <div class="col-lg-6 offset-lg-3 col-md-8 offset-md-4 col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="logCheck" name="log">
            <label class="form-check-label" for="logCheck">
              Simpan sebagai periode harga baru
            </label>
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

@include('template.footjs.global.select2')
@include('template.footjs.global.tooltip')

@if(Auth::user()->role_id != 3)
<script type="text/javascript">
$(document).ready(function(){
    $('select[name="editMaterial"]').change(function() {
      var unitSelected = $(this).children("option:selected").attr('data-unit');
      $('select[name="editSupplier"] option').each(function(){
        $(this).removeAttr('disabled').removeClass('bg-gray-300');
      });
      if(unitSelected){
        console.log(unitSelected);
        $('select[name="editSupplier"] option[data-unit!='+unitSelected+']').each(function(){
          $(this).attr('disabled','disabled').addClass('bg-gray-300');
        });
      }
    });
});
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endif