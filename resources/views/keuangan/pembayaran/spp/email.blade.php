<form action="{{ route('spp.reminder.email',['id'=>$data->id]) }}" id="email-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  <div class="row mb-2">
    <div class="col-12 mb-1">
      NIPD
    </div>
    <div class="col-12 mb-2">
      <h5>{{ $data->siswa->student_nis }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Nama Siswa
    </div>
    <div class="col-12 mb-2">
      <h5>{{ $data->siswa->identitas->student_name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Unit
    </div>
    <div class="col-12 mb-2">
      <h5>{{ $data->unit->name }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Per Bulan
    </div>
    <div class="col-12 mb-2">
      <h5>{{ $date }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Total Tanggungan SPP
    </div>
    <div class="col-12 mb-2">
      <h5>Rp {{ number_format(($data->total-($data->paid+$data->deduction)), 0, ',', '.') }},-</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="emailOpt" class="form-control-label">Email Tujuan <span class="text-danger">*</span></label>
          </div>
          <div class="col-12">
            @foreach($emailCol as $i => $e)
            @php
            $label = null;
            switch($e){
                case 'father_email':
                    $label = 'Email Ayah';
                    break;
                case 'mother_email':
                    $label = 'Email Ibu';
                    break;
                case 'guardian_email':
                    $label = 'Email Wali';
                    break;
                default:
                    break;
            }
            @endphp
            <div class="custom-control custom-radio">
              <input type="radio" id="emailOpt{{ $i+1 }}" name="email" class="custom-control-input" value="{{ $e }}" {{ $i == 0 ? 'checked="checked"' : '' }} required="required">
              <label class="custom-control-label" for="emailOpt{{ $i+1 }}">{{ $label }}</label>
            </div>
            @endforeach
            <!--<div class="custom-control custom-radio">-->
            <!--  <input type="radio" id="emailOptOther" name="email" class="custom-control-input" value="other" required="required">-->
            <!--  <label class="custom-control-label" for="emailOptOther">Lainnya</label>-->
            <!--</div>-->
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Lampiran</label>
          </div>
          <div class="col-12">
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="fa fa-paperclip"></i>
                </span>
              </div>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFile" name="file" accept=".pdf" style="background-color: #00a!important;border-color: #c7c19f!important;">
                <label class="custom-file-label" for="customFile">Pilih berkas (.pdf)</label>
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
      <input type="submit" class="btn btn-brand-green-dark" value="Kirim">
    </div>
  </div>
</form>

@include('template.footjs.global.custom-file-input')