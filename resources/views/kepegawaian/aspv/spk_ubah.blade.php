<<<<<<< HEAD
<form action="{{ route('spk.perbarui') }}" id="spk-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $spk->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
    Nama
    </div>
    <div class="col-12">
    <h5>{{ $spk->employee_name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
    Alamat
    </div>
    <div class="col-12">
    <h5>{{ $spk->employee_address }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
    Status
    </div>
    <div class="col-12">
    <h5>{{ $spk->employee_status }}</h5>
    </div>
  </div>
  <!-- <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Nomor Surat <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <input id="number" class="form-control" name="number" maxlength="255" placeholder="Tulis nomor surat" value="{{ $spk->reference_number }}" required="required">
          </div>
        </div>
      </div>
    </div>
  </div> -->
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Masa <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <div class="input-daterange input-group">
              <input type="text" class="input-sm form-control" name="period_start" placeholder="Mulai" value="{{ $spk->period_start ? date('d F Y', strtotime($spk->period_start)) : '' }}" required="required"/>
              <div class="input-group-prepend">
                <span class="input-group-text">-</span>
              </div>
              <input type="text" class="input-sm form-control" name="period_end" placeholder="Selesai" value="{{ $spk->period_end ? date('d F Y', strtotime($spk->period_end)) : '' }}" required="required"/>
            </div>
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
      <input id="save-work-agreement" type="submit" class="btn btn-brand-purple-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.datepicker')
=======
<form action="{{ route('spk.perbarui') }}" id="spk-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $spk->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
    Nama
    </div>
    <div class="col-12">
    <h5>{{ $spk->employee_name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
    Alamat
    </div>
    <div class="col-12">
    <h5>{{ $spk->employee_address }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
    Status
    </div>
    <div class="col-12">
    <h5>{{ $spk->employee_status }}</h5>
    </div>
  </div>
  <!-- <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Nomor Surat <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <input id="number" class="form-control" name="number" maxlength="255" placeholder="Tulis nomor surat" value="{{ $spk->reference_number }}" required="required">
          </div>
        </div>
      </div>
    </div>
  </div> -->
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Masa <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            <div class="input-daterange input-group">
              <input type="text" class="input-sm form-control" name="period_start" placeholder="Mulai" value="{{ $spk->period_start ? date('d F Y', strtotime($spk->period_start)) : '' }}" required="required"/>
              <div class="input-group-prepend">
                <span class="input-group-text">-</span>
              </div>
              <input type="text" class="input-sm form-control" name="period_end" placeholder="Selesai" value="{{ $spk->period_end ? date('d F Y', strtotime($spk->period_end)) : '' }}" required="required"/>
            </div>
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
      <input id="save-work-agreement" type="submit" class="btn btn-brand-purple-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.datepicker')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
