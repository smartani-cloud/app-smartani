<form action="{{ route($route.'.update') }}" id="edit-data-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="selectEditType" class="form-control-label">Jenis Tanaman</label>
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <select class="select2 form-control form-control-sm @error('editType') is-invalid @enderror" name="editType" id="selectEditType" required="required">
              @foreach($types as $t)
              <option value="{{ $t->id }}" {{ old('editType',$data->type_id) == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Nama</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" id="editName" class="form-control form-control-sm @error('editName') is-invalid @enderror" name="editName" value="{{ old('editName',$data->name) }}" maxlength="255" required="required">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Nama Ilmiah</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" id="editScientificName" class="form-control form-control-sm @error('editScientificName') is-invalid @enderror" name="editScientificName" value="{{ old('editScientificName',$data->scientific_name) }}" maxlength="255">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Durasi Siklus Tanam</label>
          </div>
          <div class="col-lg-2 col-md-4 col-6">
            <div class="input-group input-group-sm">
              <input type="text" id="editGrowthCycleDays" class="form-control form-control-sm @error('editGrowthCycleDays') is-invalid @enderror number-separator" name="editGrowthCycleDays" value="{{ old('editGrowthCycleDays',$data->growth_cycle_days) }}" maxlength="10" required="required">
              <div class="input-group-append">
                <span class="input-group-text">hari</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Jumlah Panen per Lubang</label>
          </div>
          <div class="col-lg-3 col-md-4 col-12">
            <div class="input-daterange input-group input-group-sm" id="edit_yield_per_hole_range">
              <input type="text" id="editYieldPerHoleMin" class="form-control form-control-sm @error('editYieldPerHoleMin') is-invalid @enderror number-separator" name="editYieldPerHoleMin" placeholder="Min" value="{{ old('editYieldPerHoleMin',$data->yield_per_hole_min) }}" maxlength="10" required="required"/>
              <div class="input-group-prepend">
                <span class="input-group-text">-</span>
              </div>
              <input type="text" id="editYieldPerHoleMax" class="form-control form-control-sm @error('editYieldPerHoleMax') is-invalid @enderror number-separator" name="editYieldPerHoleMax" placeholder="Maks" value="{{ old('editYieldPerHoleMax',$data->yield_per_hole_max) }}" maxlength="10" required="required"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Berat per Buah (gram)</label>
          </div>
          <div class="col-lg-3 col-md-4 col-12">
            <div class="input-daterange input-group input-group-sm" id="edit_fruit_weight_range">
              <input type="text" id="editFruitWeightMin" class="form-control form-control-sm @error('editFruitWeightMin') is-invalid @enderror number-separator" name="editFruitWeightMin" placeholder="Min" value="{{ old('editFruitWeightMin',(int)$data->fruit_weight_min_g) }}" maxlength="10" required="required"/>
              <div class="input-group-prepend">
                <span class="input-group-text">-</span>
              </div>
              <input type="text" id="editFruitWeightMax" class="form-control form-control-sm @error('editFruitWeightMax') is-invalid @enderror number-separator" name="editFruitWeightMax" placeholder="Maks" value="{{ old('editFruitWeightMax',(int)$data->fruit_weight_max_g) }}" maxlength="10" required="required"/>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-10 col-md-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Frekuensi Penyiraman per Hari</label>
          </div>
          <div class="col-lg-3 col-md-4 col-12">
            <div class="input-daterange input-group input-group-sm" id="edit_daily_watering_range">
              <input type="text" id="editDailyWateringMin" class="form-control form-control-sm @error('editDailyWateringMin') is-invalid @enderror number-separator" name="editDailyWateringMin" placeholder="Min" value="{{ old('editDailyWateringMin',$data->daily_watering_min) }}" maxlength="10" required="required"/>
              <div class="input-group-prepend">
                <span class="input-group-text">-</span>
              </div>
              <input type="text" id="editDailyWateringMax" class="form-control form-control-sm @error('editDailyWateringMax') is-invalid @enderror number-separator" name="editDailyWateringMax" placeholder="Maks" value="{{ old('editDailyWateringMax',$data->daily_watering_max) }}" maxlength="10" required="required"/>
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
      <input id="save-data" type="submit" class="btn btn-brand-purple" value="Simpan">
    </div>
  </div>
</form>


@include('template.footjs.global.datepicker')