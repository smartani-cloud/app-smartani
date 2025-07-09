<<<<<<< HEAD
<form action="{{ route('predikat.iklas.perbarui') }}" id="predikat-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $rpd->predicate }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
       Predikat
    </div>
    <div class="col-12">
      <h5>
      @for($j=0;$j<$rpd->predicate;$j++)&#9733;
      @endfor
      </h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="reasonOpt" class="form-control-label">Deskripsi</label>
          </div>
          <div class="col-12">
            <textarea type="text" name="deskripsi" class="form-control" maxlength="73">{{ $rpd ? $rpd->description : '' }}</textarea>
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
<form action="{{ route('predikat.iklas.perbarui') }}" id="predikat-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $rpd->predicate }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
       Predikat
    </div>
    <div class="col-12">
      <h5>
      @for($j=0;$j<$rpd->predicate;$j++)&#9733;
      @endfor
      </h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="reasonOpt" class="form-control-label">Deskripsi</label>
          </div>
          <div class="col-12">
            <textarea type="text" name="deskripsi" class="form-control" maxlength="73">{{ $rpd ? $rpd->description : '' }}</textarea>
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