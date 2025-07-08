<form action="{{ route('struktural.perbarui', ['tahunajaran' => $aktif->academicYearLink, 'unit' => $unit->name]) }}" id="penempatan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $detail->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Nama
    </div>
    <div class="col-12">
      <h5>{{ $detail->pegawai->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Penempatan
    </div>
    <div class="col-12">
      <h5>{{ $detail->jabatan->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Masa Penempatan
    </div>
    <div class="col-12">
      <h5>{{ date('j M Y', strtotime($detail->period_start)).' s.d. '.date('j M Y', strtotime($detail->period_end)) }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="placementDateInput" class="form-control-label">Penetapan <span class="text-danger">*</span></label>
          </div>
          <div class="col-xl-8 col-md-9 col-12">
            <div class="input-group date">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
              </div>
              <input type="text" name="placement_date" class="form-control" value="{{ $detail->placement_date ? date('d F Y', strtotime($detail->placement_date)) : ''}}" placeholder="Pilih tanggal" id="placementDateInput" required="required">
            </div>
            @error('placement_date')
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
      <input id="save-dismassal" type="submit" class="btn btn-brand-purple-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.datepicker')
