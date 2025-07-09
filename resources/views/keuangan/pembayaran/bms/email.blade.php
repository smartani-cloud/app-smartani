<form action="{{ route('bms.reminder.email',['siswa'=>(!isset($siswa) || $siswa != 'calon')?'siswa':'calon','id'=>$data->id]) }}" id="email-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
   @if(!isset($siswa) || $siswa != 'calon')
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
  @else
  <div class="row mb-2">
    <div class="col-12 mb-1">
      No. PSB
    </div>
    <div class="col-12 mb-2">
      <h5>{{ $data->siswa->reg_number }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Nama Calon Siswa
    </div>
    <div class="col-12 mb-2">
      <h5>{{ $data->siswa->student_name }}</h5>
    </div>
  </div>
  @endif
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
      Total BMS yang Harus Dibayarkan
    </div>
    <div class="col-12 mb-2">
      <h5>Rp {{ $data->bmsNominalWithSeparator }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Total BMS yang Sudah Dibayarkan
    </div>
    <div class="col-12 mb-2">
      <h5>Rp {{ $data->bmsPaidWithSeparator }}</h5>
    </div>
  </div>
  @php
  $tagihan = null;
  @endphp
  @if($data->termin()->count() > 1)
  @foreach($data->termin as $key => $t)
  <div class="row mb-2">
    <div class="col-12 mb-1">
      BMS Berkala {{ $key+1 }}
    </div>
    <div class="col-12 mb-2">
      <h5>{{ $t->remain > 0 ? 'Rp '.$t->remainWithSeparator : 'LUNAS' }}</h5>
      @php
      if($t->academic_year_id == $tahunAktif->id && !$tagihan) $tagihan = $t->remainWithSeparator;
      @endphp
    </div>
  </div>
  @endforeach
  @php
  if(!$tagihan)
    $tagihan = $data->bmsRemainWithSeparator;
  @endphp
  @endif
  <div class="row mb-2">
    <div class="col-12 mb-1">
      Total Tanggungan BMS
    </div>
    <div class="col-12 mb-2">
      <h5>Rp {{ $data->bmsRemainWithSeparator }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">
      BMS yang Harus Dibayarkan
    </div>
    <div class="col-12 mb-2">
      <h5>Rp {{ $tagihan ? $tagihan : $data->bmsRemainWithSeparator }}</h5>
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