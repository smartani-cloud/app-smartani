<div class="alert alert-danger fade show" role="alert">
  <strong>Perhatian!</strong> Mengubah IKU PSC ini akan mengubah juga IKU PSC yang ada pada jabatan lainnya
</div>
<form action="{{ route('psc.aspek.posisi.perbarui',['position' => $position]) }}" id="edit-aspek-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $indikatorJabatan->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-md-4 col-12">
            <label for="editParent" class="form-control-label">IKU Induk <span class="text-danger">*</span></label>
          </div>
          <div class="col-md-8 col-12">
            <select class="form-control form-control-sm @error('editParent') is-invalid @enderror" name="editParent" required="required">
              @foreach($indicators as $i)
              <option value="{{ $i['id'] }}" {{ $i['id'] == $indikatorJabatan->indikator->parent_id ? 'selected' : '' }} >{{ $i['name'] }}</option>
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
        <div class="row mb-3">
          <div class="col-md-4 col-12">
            <label for="editName" class="form-control-label">Indikator Kinerja Utama <span class="text-danger">*</span></label>
          </div>
          <div class="col-md-8 col-12">
            <input type="text" class="form-control form-control-sm" name="editName" maxlength="255" value="{{ $indikatorJabatan->indikator->name }}" required="required"/>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($usedCount > 0)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-md-4 col-12">
            <label class="form-control-label">Penilai</label>
          </div>
          <div class="col-md-8 col-12">
            @if($indikatorJabatan->indikator->penilai()->count() > 0)
            {{ implode(', ',$indikatorJabatan->indikator->penilai()->select('name')->pluck('name')->toArray()) }}
            <i class="fa fa-lg fa-question-circle text-light ml-1" data-toggle="tooltip" data-original-title="Belum bisa diubah karena masih ada proses penilaian aktif"></i>
            @else
            -
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
  @else
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-md-4 col-12">
            <label for="editGrader" class="form-control-label">Penilai <span class="text-danger">*</span></label>
          </div>
          <div class="col-md-8 col-12">
            <select class="select2-multiple form-control form-control-sm @error('editGrader') is-invalid @enderror" name="editGrader[]" multiple="multiple" required="required">
              @foreach($penempatan as $p)
              <option value="{{ $p->id }}" {{ ($indikatorJabatan->indikator->penilai()->count() > 0) && (in_array($p->id,$indikatorJabatan->indikator->penilai->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  @endif
  <div class="row mt-3">
    <div class="col-6 text-left">
      <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Kembali</button>
    </div>
    <div class="col-6 text-right">
      <input id="save-indicator" type="submit" class="btn btn-brand-green-dark btn-sm" value="Ubah">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.kepegawaian.tooltip')