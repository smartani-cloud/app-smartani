<form action="{{ route('iku.'.$category->nameLc.'.perbarui',['tahun' => $tahun->academicYearLink,'unit' => $unitAktif->name]) }}" id="edit-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
	{{ method_field('PUT') }}
	<input id="id" type="hidden" name="id" required="required" value="{{ $indikator->id }}">
	<div class="row mb-2">
    <div class="col-12 mb-1">Aspek</div>
		<div class="col-12">
		  <h5>{{ $indikator->aspek->aspek->name }}</h5>
		</div>
	</div>
  <div class="row mb-2">
    <div class="col-12 mb-1">Indikator Kinerja Utama</div>
		<div class="col-12">
      <h5>{{ $indikator->name }}</h5>
		</div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">Objek</div>
		<div class="col-12">
      <h5>{{ $indikator->object }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">Alat Ukur</div>
    <div class="col-12">
      <h5>{{ $indikator->mt }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">Target</div>
    <div class="col-12">
      <h5>{{ $indikator->target }}</h5>
    </div>
  </div>
  @php
  $nilaiIndikator = $nilai ? $nilai->detail()->where('indicator_id',$indikator->id)->first() : null;
  @endphp
  <div class="row mb-2">
    <div class="col-12 mb-1">Capaian</div>
    <div class="col-12">
      <h5>{{ $nilaiIndikator && ($nilaiIndikator->is_achieved == 1) ? 'Tercapai' : 'Tidak Tercapai' }}</h5>
    </div>
	</div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Berkas Pendukung</label>
          </div>
          <div class="col-12">
            <h5>{{ $nilaiIndikator && $nilaiIndikator->attachment ? 'Ada' : 'Tidak Ada' }}</h5>
            <input type="file" name="editAttachment" class="file d-none">
            <div class="input-group">
              <input type="text" class="form-control @error('editAttachment') is-invalid @enderror" disabled placeholder="{{ $nilaiIndikator && $nilaiIndikator->attachment ? 'Ubah' : 'Unggah' }} berkas..." id="file">
              <div class="input-group-append">
                <button type="button" class="browse btn btn-brand-green-dark">Pilih</button>
              </div>
            </div>
            <small id="attachmentHelp" class="form-text text-muted">Maksimum 5 MB. Kompres dengan ekstensi .zip jika berkas lebih dari satu.</small>
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
            <label for="normal-input" class="form-control-label">Pranala Pendukung</label>
          </div>
          <div class="col-12">
            <input id="editLink" class="form-control" name="editLink" value="{{ $nilaiIndikator && $nilaiIndikator->link ? $nilaiIndikator->link : '' }}" maxlength="255">
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
            <label for="normal-input" class="form-control-label">Catatan</label>
          </div>
          <div class="col-12">
            <textarea id="editNote" class="form-control" name="editNote" maxlength="255" rows="3">{{ $nilaiIndikator && $nilaiIndikator->note ? $nilaiIndikator->note : '' }}</textarea>
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
      <input id="save-indicator" type="submit" class="btn btn-brand-green-dark" value="Ubah">
    </div>
  </div>
</form>

<script>
$(document).on("click", ".browse", function() {
  var file = $(this).parents().find(".file");
  file.trigger("click");
});
$('input[type="file"]').change(function(e) {
  var fileName = e.target.files[0].name;
  $("#file").val(fileName);
});
</script>