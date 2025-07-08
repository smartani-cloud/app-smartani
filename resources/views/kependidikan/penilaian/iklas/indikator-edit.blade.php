@if($data)
<div class="alert alert-warning" role="alert">
  <strong>Perhatian!</strong> Perubahan indikator mungkin akan berpengaruh pada rapor terdahulu
</div>
@endif
<form action="{{ route($route.'.update',['unit' => $unit->name]) }}" id="update-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  @if(isset($unitEditable) && $unitEditable && isset($unit) && !in_array(auth()->user()->role->name,['kepsek','wakasek','guru']))
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="selectUnit" class="form-control-label">Unit</label>
          </div>
          <div class="col-12">
            <select class="form-control form-control @error('editUnit') is-invalid @enderror" name="editUnit" id="selectUnit" required="required">
              @foreach($unitList as $u)
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
        <div class="row">
          <div class="col-12">
            <label for="editName" class="form-control-label">Indikator</label>
          </div>
          <div class="col-12">
            <input type="text" id="editName" class="form-control" name="editName" maxlength="100" required="required" value="{{ $data->indicator ? $data->indicator : '' }}">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div>

  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      <input type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>