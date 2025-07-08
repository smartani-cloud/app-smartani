<form action="{{ route('evaluasi.perbarui') }}" id="eval-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $eval->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
    Nama
    </div>
    <div class="col-12">
    <h5>{{ $eval->pegawai->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
    PSC Tahun Lalu
    </div>
    <div class="col-12">
    <h5>-</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
    PSC Sementara
    </div>
    <div class="col-12">
    <h5>{{ $eval->pscSementara ? $eval->pscSementara->name : '-' }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Hasil Supervisi
    </div>
    <div class="col-12">
      <h5>{{ $eval->supervision_result? $eval->supervision_result : '-' }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Hasil Interview
    </div>
    <div class="col-12">
      <h5>{{ $eval->interview_result ? $eval->interview_result : '-' }}</h5>
    </div>
  </div>
  <hr>
  <div class="row mb-2">
    <div class="col-12">
      <h6 class="font-weight-bold text-brand-green">Rekomendasi</h6>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Kelanjutan
    </div>
    <div class="col-12">
      <h5>{{ $eval->rekomendasiLanjut ? ucwords($eval->rekomendasiLanjut->status) : '-' }}</h5>
    </div>
  </div>
  @if($eval->recommend_status_id == 1)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Status
    </div>
    <div class="col-12">
      <h5>{{ $eval->rekomendasiStatus ? $eval->rekomendasiStatus->status : '-' }}</h5>
    </div>
  </div>
  @elseif($eval->recommend_status_id == 2)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Alasan
    </div>
    <div class="col-12">
      <h5>{{ $eval->alasan ? $eval->alasan->reason : '-' }}</h5>
    </div>
  </div>
  @endif
  @if($eval->accStatus && $eval->accStatus->status == 'setuju')
  <div class="row mb-2">
    <div class="col-12">
      <div class="alert alert-success" role="alert">
        <i class="fa fa-check mr-1"></i>Disetujui oleh {{ $eval->accEdukasi->name }} pada {{ $eval->education_acc_time ? date('j F Y', strtotime($eval->education_acc_time)) : '-' }}
      </div>
    </div>
  </div>
  <div class="row mt-3">
    <div class="col-12 text-left">
      <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
    </div>
  </div>
  @else
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
              <input type="radio" id="accOpt{{ $a->id }}" name="acc_status" class="custom-control-input" value="{{ $a->id }}" {{ $eval->education_acc_status_id == $a->id ? 'checked' : '' }} required="required">
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
      <input type="submit" class="btn btn-brand-green-dark" value="Simpan">
    </div>
  </div>
  @endif
</form>
