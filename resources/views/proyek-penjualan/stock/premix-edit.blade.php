<<<<<<< HEAD
<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Nama</label>
          </div>
          <div class="col-12">
            <input type="text" id="name" class="form-control" name="name" maxlength="255" required="required" value="{{ $data->name ? $data->name : '' }}">
            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  @if(isset($unitEditable) && $unitEditable && isset($unit) && Auth::user()->role_id != 3)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="selectUnit" class="form-control-label">Divisi</label>
          </div>
          <div class="col-12">
            <select class="form-control form-control @error('editUnit') is-invalid @enderror" name="editUnit" id="selectUnit" required="required">
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

  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      <input id="save-data" type="submit" class="btn btn-primary" value="Simpan">
    </div>
  </div>
=======
<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Nama</label>
          </div>
          <div class="col-12">
            <input type="text" id="name" class="form-control" name="name" maxlength="255" required="required" value="{{ $data->name ? $data->name : '' }}">
            @error('name')
            <span class="text-danger">{{ $message }}</span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
  @if(isset($unitEditable) && $unitEditable && isset($unit) && Auth::user()->role_id != 3)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-12">
            <label for="selectUnit" class="form-control-label">Divisi</label>
          </div>
          <div class="col-12">
            <select class="form-control form-control @error('editUnit') is-invalid @enderror" name="editUnit" id="selectUnit" required="required">
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

  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      <input id="save-data" type="submit" class="btn btn-primary" value="Simpan">
    </div>
  </div>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</form>