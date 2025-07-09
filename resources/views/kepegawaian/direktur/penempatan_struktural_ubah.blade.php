<<<<<<< HEAD
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
  </div><div class="row mb-2">
    <div class="col-12 mb-1">
      Penetapan
    </div>
    <div class="col-12">
      <h5>{{ $detail->placement_date ? date('j M Y', strtotime($detail->placement_date)) : '-' }}</h5>
    </div>
  </div>
  @if($detail->accStatus && $detail->accStatus->status == 'setuju')
  <div class="row mb-2">
    <div class="col-12">
      <div class="alert alert-success" role="alert">
        <i class="fa fa-check mr-1"></i>Disetujui oleh {{ $detail->accPegawai->name }} pada {{ $detail->acc_time ? date('j F Y', strtotime($detail->acc_time)) : '-' }}
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
  </div>
  @else
  @if($detail->placement_date)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="accOpt" class="form-control-label">Persetujuan <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            @foreach($acc as $a)
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="accOpt{{ $a->id }}" name="acc_status" class="custom-control-input" value="{{ $a->id }}" {{ $detail->acc_status_id == $a->id ? 'checked' : '' }} required="required">
              <label class="custom-control-label" for="accOpt{{ $a->id }}">{{ ucwords($a->status) }}</label>
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
      <input id="save-dismassal" type="submit" class="btn btn-brand-purple-dark" value="Simpan">
    </div>
  </div>
  @else
   <div class="row mb-2">
    <div class="col-12">
      <div class="alert alert-secondary" role="alert">
        <i class="fa fa-clock mr-1"></i>Menunggu Administration Supervisor mengisi tanggal penetapan
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
  </div>
  @endif
  @endif
</form>
=======
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
  </div><div class="row mb-2">
    <div class="col-12 mb-1">
      Penetapan
    </div>
    <div class="col-12">
      <h5>{{ $detail->placement_date ? date('j M Y', strtotime($detail->placement_date)) : '-' }}</h5>
    </div>
  </div>
  @if($detail->accStatus && $detail->accStatus->status == 'setuju')
  <div class="row mb-2">
    <div class="col-12">
      <div class="alert alert-success" role="alert">
        <i class="fa fa-check mr-1"></i>Disetujui oleh {{ $detail->accPegawai->name }} pada {{ $detail->acc_time ? date('j F Y', strtotime($detail->acc_time)) : '-' }}
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
  </div>
  @else
  @if($detail->placement_date)
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="accOpt" class="form-control-label">Persetujuan <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            @foreach($acc as $a)
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="accOpt{{ $a->id }}" name="acc_status" class="custom-control-input" value="{{ $a->id }}" {{ $detail->acc_status_id == $a->id ? 'checked' : '' }} required="required">
              <label class="custom-control-label" for="accOpt{{ $a->id }}">{{ ucwords($a->status) }}</label>
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
      <input id="save-dismassal" type="submit" class="btn btn-brand-purple-dark" value="Simpan">
    </div>
  </div>
  @else
   <div class="row mb-2">
    <div class="col-12">
      <div class="alert alert-secondary" role="alert">
        <i class="fa fa-clock mr-1"></i>Menunggu Administration Supervisor mengisi tanggal penetapan
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
  </div>
  @endif
  @endif
</form>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
