<<<<<<< HEAD
<form action="{{ route('psc.peran.perbarui') }}" id="peran-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $jabatan->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Jabatan
    </div>
    <div class="col-12">
      <h5>{{ $jabatan->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Kategori
    </div>
    <div class="col-12">
      <h5>{{ $jabatan->kategori->name }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editCreate" class="form-control-label">Buat Aspek <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <select class="form-control" name="editCreate[]" id="editCreate" required="required">
              @foreach($penempatan as $p)
              <option value="{{ $p->id }}" {{ in_array($p->id,$jabatan->pscRoleMappingCheck(1)) ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editValidate" class="form-control-label">Validasi</label>
          </div>
          <div class="col-12">
            <select class="form-control" name="editValidate[]" id="editValidate">
              <option value="" >Tidak Ada</option>
              @foreach($struktural as $s)
              <option value="{{ $s->id }}" {{ in_array($s->id,$jabatan->pscRoleMappingCheck(2)) ? 'selected' : '' }}>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editView" class="form-control-label">Lihat Rapor <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <select class="select2-multiple form-control" name="editView[]" multiple="multiple" id="editView" required="required">
              @foreach($struktural as $s)
              <option value="{{ $s->id }}" {{ in_array($s->id,$jabatan->pscRoleMappingCheck(3)) ? 'selected' : '' }}>{{ $s->name }}</option>
              @endforeach
            </select>
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
      <input id="save-range" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

=======
<form action="{{ route('psc.peran.perbarui') }}" id="peran-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $jabatan->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Jabatan
    </div>
    <div class="col-12">
      <h5>{{ $jabatan->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Kategori
    </div>
    <div class="col-12">
      <h5>{{ $jabatan->kategori->name }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editCreate" class="form-control-label">Buat Aspek <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <select class="form-control" name="editCreate[]" id="editCreate" required="required">
              @foreach($penempatan as $p)
              <option value="{{ $p->id }}" {{ in_array($p->id,$jabatan->pscRoleMappingCheck(1)) ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editValidate" class="form-control-label">Validasi</label>
          </div>
          <div class="col-12">
            <select class="form-control" name="editValidate[]" id="editValidate">
              <option value="" >Tidak Ada</option>
              @foreach($struktural as $s)
              <option value="{{ $s->id }}" {{ in_array($s->id,$jabatan->pscRoleMappingCheck(2)) ? 'selected' : '' }}>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="editView" class="form-control-label">Lihat Rapor <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <select class="select2-multiple form-control" name="editView[]" multiple="multiple" id="editView" required="required">
              @foreach($struktural as $s)
              <option value="{{ $s->id }}" {{ in_array($s->id,$jabatan->pscRoleMappingCheck(3)) ? 'selected' : '' }}>{{ $s->name }}</option>
              @endforeach
            </select>
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
      <input id="save-range" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@include('template.footjs.kepegawaian.select2-multiple')