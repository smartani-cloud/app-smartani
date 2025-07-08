<form action="{{ route('pelatihan.materi.perbarui') }}" id="pelatihan-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $pelatihan->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Materi Pelatihan
    </div>
    <div class="col-12">
      <h5>{{ $pelatihan->name }}</h5>
    </div>
  </div>
  @if($pelatihan->desc)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Deskripsi
    </div>
    <div class="col-12">
      <h5>{{ $pelatihan->desc }}</h5>
    </div>
  </div>
  @endif
  @if($pelatihan->organizer)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Penyelenggara
    </div>
    <div class="col-12">
      <h5>{{ $pelatihan->organizer }}</h5>
    </div>
  </div>
  @endif
  @if($pelatihan->speaker)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Narasumber
    </div>
    <div class="col-12">
      <h5>{{ $pelatihan->speaker }}</h5>
    </div>
  </div>
  @endif
  @if($pelatihan->date)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Tanggal Pelaksanaan
    </div>
    <div class="col-12">
      <h5>{{ date('j M Y', strtotime($pelatihan->date)) }}</h5>
    </div>
  </div>
  @endif
  @if($pelatihan->place)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Tempat
    </div>
    <div class="col-12">
      <h5>{{ $pelatihan->place }}</h5>
    </div>
  </div>
  @endif
  @if(count($pelatihan->sasaran) > 0)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Sasaran
    </div>
    <div class="col-12">
      <h4>
        @foreach($pelatihan->sasaran as $p)
        <span class="badge badge-light font-weight-normal">{{ $p->jabatan->name }}</span>
        @endforeach
      </h4>
    </div>
  </div>
  @endif
  @if($pelatihan->semester)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Semester
    </div>
    <div class="col-12">
      <h5>{{ $pelatihan->semester->semester }}</h5>
    </div>
  </div>
  @endif
  @if($pelatihan->status)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Sifat
    </div>
    <div class="col-12">
      <h5>{{ ucwords($pelatihan->status->status) }}</h5>
    </div>
  </div>
  @endif
  @if($pelatihan->accStatus && $pelatihan->accStatus->status == 'setuju')
  <div class="row mb-2">
    <div class="col-12">
      <div class="alert alert-success" role="alert">
        <i class="fa fa-check mr-1"></i>Disetujui oleh {{ $pelatihan->accEdukasi->name }} pada {{ $pelatihan->education_acc_time ? date('j F Y', strtotime($pelatihan->education_acc_time)) : '-' }}
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
              <input type="radio" id="accOpt{{ $a->id }}" name="acc_status" class="custom-control-input" value="{{ $a->id }}" {{ $pelatihan->education_acc_status_id == $a->id ? 'checked' : '' }} required="required">
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
      <input id="save-training" type="submit" class="btn btn-success" value="Simpan">
    </div>
  </div>
  @endif
</form>
