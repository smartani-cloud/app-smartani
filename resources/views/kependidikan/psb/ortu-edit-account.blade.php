<form action="{{ route($route.'.account.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  @if($data->father_name)
  <div class="row mb-2">
    <div class="col-12 mb-1">Nama Ayah</div>
    <div class="col-12">
      <h5>{{ $data->father_name }}</h5>
    </div>
  </div>
  @endif
  @if($data->mother_name)
  <div class="row mb-2">
    <div class="col-12 mb-1">Nama Ibu</div>
    <div class="col-12">
      <h5>{{ $data->mother_name }}</h5>
    </div>
  </div>
  @endif
  @if($data->guardian_name)
  <div class="row mb-2">
    <div class="col-12 mb-1">Nama Wali</div>
    <div class="col-12">
      <h5>{{ $data->guardian_name }}</h5>
    </div>
  </div>
  @endif
  @if($data->childrens)
  <div class="row mb-2">
    <div class="col-12 mb-1">Nama Anak</div>
    <div class="col-12">
      <h5>{{ $data->childrens }}</h5>
    </div>
  </div>
  @endif
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Username</label>
          </div>
          <div class="col-12">
            <input type="text" id="editUsername" class="form-control @error('editUsername') is-invalid @enderror" name="editUsername" value="{{ old('editUsername',$data->loginUser->username)  }}" required="required">
            @error('editUsername')
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
      <input id="save-data" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>