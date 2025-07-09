<form action="{{ route('phk.simpan') }}" id="phk-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $pegawai->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
	  Nama
    </div>
    <div class="col-12">
	  <h5>{{ $pegawai->name }}</h5>
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
              <input type="radio" id="reasonOpt{{ $a->id }}" name="reason" class="custom-control-input" value="{{ $a->id }}" required="required">
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
      <input id="save-dismassal" type="submit" class="btn btn-danger" value="Ajukan">
    </div>
  </div>
</form>