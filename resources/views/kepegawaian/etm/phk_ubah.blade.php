<<<<<<< HEAD
<form action="{{ route('phk.perbarui') }}" id="pendidikan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $phk->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
	  Nama
    </div>
    <div class="col-12">
	  <h5>{{ $phk->pegawai->name }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="reasonOpt" class="form-control-label">Alasan PHK</label>
          </div>
          <div class="col-12">
            @foreach($alasan as $a)
            <div class="custom-control custom-radio mb-1">
              <input type="radio" id="reasonOpt{{ $a->id }}" name="reason" class="custom-control-input" value="{{ $a->id }}" {{ old('reason', $phk->dismassal_reason_id) == $a->id ? 'checked' : '' }} required="required">
              <label class="custom-control-label" for="reasonOpt{{ $a->id }}">{{ $a->reason }}</label>
            </div>
            @endforeach
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
      <input id="save-dismassal" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
=======
<form action="{{ route('phk.perbarui') }}" id="pendidikan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $phk->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
	  Nama
    </div>
    <div class="col-12">
	  <h5>{{ $phk->pegawai->name }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="reasonOpt" class="form-control-label">Alasan PHK</label>
          </div>
          <div class="col-12">
            @foreach($alasan as $a)
            <div class="custom-control custom-radio mb-1">
              <input type="radio" id="reasonOpt{{ $a->id }}" name="reason" class="custom-control-input" value="{{ $a->id }}" {{ old('reason', $phk->dismassal_reason_id) == $a->id ? 'checked' : '' }} required="required">
              <label class="custom-control-label" for="reasonOpt{{ $a->id }}">{{ $a->reason }}</label>
            </div>
            @endforeach
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
      <input id="save-dismassal" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</form>