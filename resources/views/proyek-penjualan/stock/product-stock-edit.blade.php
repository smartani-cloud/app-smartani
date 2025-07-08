@if($material && count($material) > 0)
<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
@endif
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label class="form-control-label">Produk</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">{{ $data->product->name }}</div>
        </div>
      </div>
    </div>
  </div>
  @if(Auth::user()->role_id != 3)
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label class="form-control-label">Unit</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">{{ $data->product->unit ? $data->product->unit->name : '-' }}</div>
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
            <label class="form-control-label">Tahun</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">{{ $data->year }}</div>
        </div>
      </div>
    </div>
  </div>
  @if($material && count($material) > 0)
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="select2Material" class="form-control-label">Bahan Baku</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <select class="select2 form-control @error('materialSupplier') is-invalid @enderror" name="materialSupplier" id="select2Material" required="required">
              @foreach($material as $m)
              <option value="{{ $m->id }}" {{ old('materialSupplier') == $m->id ? 'selected' : '' }} data-max="{{ $m->stock_quantity }}">{{ Auth::user()->role_id == 3 ? $m->nameWithStock : $m->nameUnitWithStock }}</option>
              @endforeach
            </select>
            @error('materialSupplier')
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
            <label for="materialQty" class="form-control-label">Kuantitas</label>
          </div>
          <div class="col-lg-6 col-md-8 col-12">
            @php
            $stock = $data->quantity;
            $max = $material->first()->stock_quantity;
            if($max > $stock) $max = $stock;
            @endphp
            <input type="number" class="form-control" name="materialQty" value="1" min="1" {{ $material && count($material) > 0 ? 'max='.$max.' required="required"' : 'max="0" disabled="disabled"' }}>
            @error('materialQty')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  @else
  <div class="row">
    <div class="col-12">
      <div class="alert alert-warning" role="alert">
        <strong>Perhatian!</strong> Tidak ada stok bahan baku
      </div>
    </div>
  </div>
  @endif

  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      @if($material && count($material) > 0)
      <input id="save-data" type="submit" class="btn btn-primary" value="Tambah">
      @else
      <button type="button" class="btn btn-secondary" disabled="disabled">Tambah</button>
      @endif
    </div>
  </div>
@if($material && count($material) > 0)
</form>

<script type="text/javascript">
$(document).ready(function(){
  $('#select2Material').on('change', function(e){
    var stock = {{ $data->quantity }};
    $('input[name="materialQty"]').attr('max',0).val(1);
    var max = $('#select2Material option:selected').attr('data-max');
    if(max){
      console.log(max+' > '+stock);
      if(max > stock) max = stock;
      $('input[name="materialQty"]').attr('min',1).attr('max',max).val(1);
    }
  });
});
</script>

@include('template.footjs.global.select2')
@include('template.footjs.global.tooltip')
@endif