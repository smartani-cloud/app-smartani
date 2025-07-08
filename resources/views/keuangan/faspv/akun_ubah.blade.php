<form action="{{ route('keuangan.akun.perbarui') }}" id="akun-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $akun->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <div class="row">
                <div class="col-12">
                  <label for="normal-input" class="form-control-label">Kode Akun</label>
                </div>
                <div class="col-md-6 col-12">
                  <input id="editCode" class="form-control" name="code" maxlength="18" value="{{ old('code', $akun->code) }}" required="required">
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
                  <label for="normal-input" class="form-control-label">Nama Akun</label>
                </div>
                <div class="col-12">
                  <input id="editName" class="form-control" name="name" maxlength="255" value="{{ old('name', $akun->name) }}" required="required">
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
                  <label for="normal-input" class="form-control-label">Bisa Diisi</label>
                </div>
                <div class="col-12">
                  <div class="custom-control custom-radio custom-control-inline mb-1">
                    <input type="radio" id="editFillableOpt1" name="is_fillable" class="custom-control-input" value="1" {{ old('is_fillable', $akun->is_fillable) == 1 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="editFillableOpt1">Ya</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline mb-1">
                    <input type="radio" id="editFillableOpt2" name="is_fillable" class="custom-control-input" value="0" {{ old('is_fillable', $akun->is_fillable) == 0 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="editFillableOpt2">Tidak</label>
                  </div>
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
                  <label for="normal-input" class="form-control-label">Eksklusif</label>
                </div>
                <div class="col-12">
                  <div class="custom-control custom-radio custom-control-inline mb-1">
                    <input type="radio" id="editExclusiveOpt1" name="is_exclusive" class="custom-control-input" value="1" {{ old('is_exclusive', $akun->is_exclusive) == 1 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="editExclusiveOpt1">Ya</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline mb-1">
                    <input type="radio" id="editExclusiveOpt2" name="is_exclusive" class="custom-control-input" value="0" {{ old('is_exclusive', $akun->is_exclusive) == 0 ? 'checked' : '' }}>
                    <label class="custom-control-label" for="editExclusiveOpt2">Tidak</label>
                  </div>
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
                  <label for="normal-input" class="form-control-label">Kategori</label>
                </div>
                <div class="col-12">
                  <select aria-label="Kategori" name="account_category" id="editAccountCategory" title="Kategori" class="form-control @error('account_category') is-invalid @enderror" required="required">
                    <option value="" {{ old('account_category') ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
                    @foreach($kategori as $k)
                    @if(!$k->upcategory)
                    <option value="{{ $k->id }}" class="bg-gray-400" disabled="disabled">{{ $k->name }}</option>
                    @else
                    <option value="{{ $k->id }}" {{ old('account_category', $akun->account_category_id) == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                    @endif
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
                  <label for="normal-input" class="form-control-label">Anggaran</label>
                </div>
                <div class="col-12">
                  <select class="select2-multiple form-control" name="budgeting[]" multiple="multiple" id="editBudgeting" required="required">
                  @foreach($jenisAnggaran as $j)
                  <option value="{{ $j->id }}" {{ count($budgeting) > 0 ? ($budgeting->contains($j->id) ? 'selected' : '') : '' }}>{{ $j->name }}</option>
                  @endforeach
                </select>
                </div>
              </div>
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
      <input id="save-account" type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.select2-multiple')