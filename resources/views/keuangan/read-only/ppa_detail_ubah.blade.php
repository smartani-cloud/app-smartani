<<<<<<< HEAD
<form action="{{ route('ppa.perbarui.detail', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaDetail->ppa->firstNumber]) }}" id="editPpaDetailForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input type="hidden" name="editId" value="{{ $ppaDetail->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label class="form-control-label">Akun Anggaran</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" class="form-control form-control-sm" name="editAccount" value="{{ $ppaDetail->akun->codeName }}" disabled="disabled">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="select2EditProposal" class="form-control-label">Keterangan</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <select class="select2-multiple form-control form-control-sm @error('editProposals') is-invalid @enderror" name="editProposals[]" multiple="multiple" id="select2EditProposal" required="required">
      				@foreach($proposals as $p)
      				<option value="{{ $p->id }}" {{ $ppaDetail->proposals()->count() > 0 && $ppaDetail->proposals->pluck('id')->contains($p->id) ? 'selected' : '' }} data-amount="{{ $p->total_value }}">{{ $p->title.' - '.$p->totalValueWithSeparator.' ['.$p->pegawai->nickname.', '.$p->jabatan->name.']' }}</option>
      				@endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($ppaDetail->ppa->type_id == 2)
  <div class="row" style="display: none">
  @else
  <div class="row">
  @endif
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Jumlah</label>
          </div>
          <div class="col-lg-6 col-md-8 col-12">
            <input type="text" id="editValue" class="form-control form-control-sm" name="editTotal" value="{{ $ppaDetail->proposals()->count() > 0 ? number_format($ppaDetail->proposals()->sum('total_value'), 0, ',', '.') : 0 }}" disabled="disabled">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-1">
    <div class="col-12">
      <div class="row">
        <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
          <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Simpan">
        </div>
      </div>
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.select2-multiple')
<script type="text/javascript">
$(document).ready(function(){
  selectSumAmount('#select2EditProposal','data-amount','#editValue');
});
=======
<form action="{{ route('ppa.perbarui.detail', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaDetail->ppa->firstNumber]) }}" id="editPpaDetailForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input type="hidden" name="editId" value="{{ $ppaDetail->id }}">
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label class="form-control-label">Akun Anggaran</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <input type="text" class="form-control form-control-sm" name="editAccount" value="{{ $ppaDetail->akun->codeName }}" disabled="disabled">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="select2EditProposal" class="form-control-label">Keterangan</label>
          </div>
          <div class="col-lg-9 col-md-8 col-12">
            <select class="select2-multiple form-control form-control-sm @error('editProposals') is-invalid @enderror" name="editProposals[]" multiple="multiple" id="select2EditProposal" required="required">
      				@foreach($proposals as $p)
      				<option value="{{ $p->id }}" {{ $ppaDetail->proposals()->count() > 0 && $ppaDetail->proposals->pluck('id')->contains($p->id) ? 'selected' : '' }} data-amount="{{ $p->total_value }}">{{ $p->title.' - '.$p->totalValueWithSeparator.' ['.$p->pegawai->nickname.', '.$p->jabatan->name.']' }}</option>
      				@endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  @if($ppaDetail->ppa->type_id == 2)
  <div class="row" style="display: none">
  @else
  <div class="row">
  @endif
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-lg-3 col-md-4 col-12">
            <label for="normal-input" class="form-control-label">Jumlah</label>
          </div>
          <div class="col-lg-6 col-md-8 col-12">
            <input type="text" id="editValue" class="form-control form-control-sm" name="editTotal" value="{{ $ppaDetail->proposals()->count() > 0 ? number_format($ppaDetail->proposals()->sum('total_value'), 0, ',', '.') : 0 }}" disabled="disabled">
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-1">
    <div class="col-12">
      <div class="row">
        <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
          <input type="submit" class="btn btn-sm btn-brand-green-dark" value="Simpan">
        </div>
      </div>
    </div>
  </div>
</form>

@include('template.footjs.kepegawaian.select2-multiple')
<script type="text/javascript">
$(document).ready(function(){
  selectSumAmount('#select2EditProposal','data-amount','#editValue');
});
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
</script>