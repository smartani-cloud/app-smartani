@extends('template.main.master')

@section('title')
Ubah Calon Pegawai
@endsection

@section('headmeta')
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<style>
.select2-container .select2-results__option[aria-disabled=true] {
  background-color: #dddfeb!important;
}
</style>
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Ubah Calon Pegawai</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rekrutmen.index') }}">Rekrutmen</a></li>
    <li class="breadcrumb-item"><a href="{{ route('calon.index') }}">Calon Pegawai</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-4">
        <form action="{{ route('calon.perbarui', ['id' => $calon->id]) }}" id="edit-form" method="post" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="return validateDate('inputBirthDate','birthDateError')">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input id="id" type="hidden" name="id" required="required" value="{{ $calon->id }}">
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-green">Info Umum</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputName" class="form-control-label">Nama Lengkap <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input id="inputName" class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="Nama lengkap dan gelar" value="{{ old('name', $calon->name) }}" maxlength="255" required="required">
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputNickname" class="form-control-label">Nama Panggilan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    <input id="inputNickname" class="form-control @error('nickname') is-invalid @enderror" type="text" name="nickname"  value="{{ old('nickname', $calon->nickname) }}" maxlength="255" required="required">
                    @error('nickname')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputPhoto" class="form-control-label">Foto <span class="text-danger"></span></label>
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    <img src="{{ $calon->photo ? asset($calon->photoPath) : asset('img/avatar/default.png') }}" id="preview" class="img-thumbnail photo-preview">
                    <input type="file" name="photo" class="file d-none" accept="image/jpg,image/jpeg,image/png">
                    <div class="input-group mt-3">
                      <input type="text" class="form-control @error('photo') is-invalid @enderror" disabled placeholder="Ubah foto..." id="file">
                      <div class="input-group-append">
                        <button type="button" class="browse btn btn-brand-green-dark">Pilih</button>
                      </div>
                    </div>
                    <small id="photoHelp" class="form-text text-muted">Ekstensi .jpg, .jpeg, .png dan maksimum 5 MB</small>
                    @error('photo')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputNik" class="form-control-label">NIK <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    <input id="inputNik" class="form-control @error('nik') is-invalid @enderror" type="text" name="nik"  value="{{ old('nik', $calon->nik) }}" placeholder="Nomor Induk Kependudukan" maxlength="16" required="required">
                    @error('nik')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputNpwp" class="form-control-label">NPWP</label>
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    <input id="inputNpwp" class="form-control @error('npwp') is-invalid @enderror" type="text" name="npwp" value="{{ old('npwp', $calon->npwp) }}" placeholder="Nomor Pokok Wajib Pajak" maxlength="15">
                    @error('npwp')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputNuptk" class="form-control-label">NUPTK</label>
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    <input id="inputNuptk" class="form-control @error('nuptk') is-invalid @enderror" type="text" name="nuptk" value="{{ old('nuptk', $calon->nuptk) }}" placeholder="Nomor Unik Pendidik dan Tenaga Kependidikan" maxlength="16">
                    @error('nuptk')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputNrg" class="form-control-label">NRG</label>
                  </div>
                  <div class="col-lg-6 col-md-5 col-12">
                    <input id="inputNrg" class="form-control @error('nrg') is-invalid @enderror" type="text" name="nrg" value="{{ old('nrg', $calon->nrg) }}" placeholder="Nomor Registrasi Guru" maxlength="14">
                    @error('nrg')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="genderOpt" class="form-control-label">Jenis Kelamin <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @foreach($jeniskelamin as $j)
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="genderOpt{{ $j->id }}" name="gender" class="custom-control-input" value="{{ $j->id }}" required="required" {{ old('gender', $calon->gender_id) == $j->id ? 'checked' : '' }}>
                      <label class="custom-control-label" for="genderOpt{{ $j->id }}">{{ ucwords($j->name) }}</label>
                    </div>
                    @endforeach
                    @error('gender')
                    <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputBirthPlace" class="form-control-label">Tempat Lahir <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-7 col-md-6 col-12">
                    <input id="inputBirthPlace" class="form-control @error('birth_place') is-invalid @enderror" type="text" name="birth_place" value="{{ old('birth_place', $calon->birth_place) }}" maxlength="255" required="required">
                    @error('birth_place')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputBirthDate" class="form-control-label">Tanggal Lahir <span class="text-danger">*</span></label>
                  </div>
                  <div id="inputBirthDate" class="col-lg-9 col-md-8 col-12">
                    <div class="row">
                      <div class="col-4">
                        <select aria-label="Tanggal" name="birthday_day" id="day" title="Tanggal" class="form-control @error('birthday_day') is-invalid @enderror" required="required">
                          <option value="" {{ old('birthday_day', date('d',strtotime($calon->birth_date))) ? '' : 'selected' }} disabled="disabled">Tanggal</option>
                          @for($i=1;$i<=31;$i++)
                          <option value="{{ $i }}" {{ old('birthday_day', date('d',strtotime($calon->birth_date))) == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                      </div>
                      <div class="col-4">
                        <select aria-label="Bulan" name="birthday_month" id="month" title="Bulan" class="form-control @error('birthday_month') is-invalid @enderror" required="required">
                          <option value="" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) ? '' : 'selected' }} disabled="disabled">Bulan</option>
                          <option value="1" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 1 ? 'selected' : '' }}>Jan</option>
                          <option value="2" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 2 ? 'selected' : '' }}>Feb</option>
                          <option value="3" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 3 ? 'selected' : '' }}>Mar</option>
                          <option value="4" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 4 ? 'selected' : '' }}>Apr</option>
                          <option value="5" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 5 ? 'selected' : '' }}>Mei</option>
                          <option value="6" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 6 ? 'selected' : '' }}>Jun</option>
                          <option value="7" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 7 ? 'selected' : '' }}>Jul</option>
                          <option value="8" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 8 ? 'selected' : '' }}>Agu</option>
                          <option value="9" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 9 ? 'selected' : '' }}>Sep</option>
                          <option value="10" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 10 ? 'selected' : '' }}>Okt</option>
                          <option value="11" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 11 ? 'selected' : '' }}>Nov</option>
                          <option value="12" {{ old('birthday_month', date('m',strtotime($calon->birth_date))) == 12 ? 'selected' : '' }}>Des</option>
                        </select>
                      </div>
                      <div class="col-4">
                        @php
                        $year_start = date('Y', strtotime("-15 years"));
                        $year_end = date('Y', strtotime("-70 years"));
                        @endphp
                        <select aria-label="Tahun" name="birthday_year" id="year" title="Tahun" class="form-control @error('birthday_year') is-invalid @enderror" required="required">
                          <option value="" {{ old('birthday_year', date('Y',strtotime($calon->birth_date))) ? '' : 'selected' }} disabled="disabled">Tahun</option>
                          @for($i=$year_start;$i>=$year_end;$i--)
                          <option value="{{ $i }}" {{ old('birthday_year', date('Y',strtotime($calon->birth_date))) == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
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
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="marriageOpt" class="form-control-label">Status Pernikahan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @foreach($pernikahan as $p)
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="marriageOpt{{ $p->id }}" name="marriage_status" class="custom-control-input" value="{{ $p->id }}" {{ old('marriage_status', $calon->marriage_status_id) == $p->id ? 'checked' : '' }} required="required">
                      <label class="custom-control-label" for="marriageOpt{{ $p->id }}">{{ ucwords($p->status) }}</label>
                    </div>
                    @endforeach
                    @error('marriage_status')
                    <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-green">Info Alamat dan Kontak</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="provinsiOpt" class="form-control-label">Provinsi <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Provinsi" name="provinsi" class="form-control @error('provinsi') is-invalid @enderror" id="provinsiOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('provinsi', substr($calon->alamat->code,0,2)) ? '' : 'selected' }}>== Pilih Provinsi ==</option>
                      @foreach($provinsi as $p)
                      <option value="{{ $p->code }}" {{ old('provinsi', substr($calon->alamat->code,0,2)) == $p->code ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endforeach
                    </select>
                    @error('provinsi')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="kabupatenOpt" class="form-control-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Kabupaten" name="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror" id="kabupatenOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('kabupaten', substr($calon->alamat->code,0,5)) ? '' : 'selected' }}>== Pilih Kabupaten/Kota ==</option>
                      @foreach($kabupaten as $k)
                      <option value="{{ $k->code }}" {{ old('kabupaten', substr($calon->alamat->code,0,5)) == $k->code ? 'selected' : '' }}>{{ $k->name }}</option>
                      @endforeach
                    </select>
                    @error('kabupaten')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="kecamatanOpt" class="form-control-label">Kecamatan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Kecamatan" name="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror" id="kecamatanOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('kecamatan', substr($calon->alamat->code,0,8)) ? '' : 'selected' }}>== Pilih Kecamatan ==</option>
                      @foreach($kecamatan as $k)
                      <option value="{{ $k->code }}" {{ old('kecamatan', substr($calon->alamat->code,0,8)) == $k->code ? 'selected' : '' }}>{{ $k->name }}</option>
                      @endforeach
                    </select>
                    @error('kecamatan')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="desaOpt" class="form-control-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Desa" name="desa" class="form-control @error('desa') is-invalid @enderror" id="desaOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('desa', $calon->alamat->code) ? '' : 'selected' }}>== Pilih Desa/Kelurahan ==</option>
                      @foreach($desa as $d)
                      <option value="{{ $d->code }}" {{ old('desa', $calon->alamat->code) == $d->code ? 'selected' : '' }}>{{ $d->name }}</option>
                      @endforeach
                    </select>
                    @error('desa')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputAddress" class="form-control-label">Alamat <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <textarea id="inputAddress" class="form-control @error('address') is-invalid @enderror" name="address" maxlength="255" rows="3" required="required">{{ old('address', $calon->address) }}</textarea>
                    @error('address')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputRt" class="form-control-label">RT <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-8">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputRt" type="text" name="rt" class="form-control @error('rt') is-invalid @enderror" value="{{ old('rt', $calon->rt) }}" required="required">
                    </div>
                    @error('rt')
                    <span class="mt-1 text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputRw" class="form-control-label">RW <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-3 col-md-4 col-8">
                    <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                      <input id="inputRw" type="text" name="rw" class="form-control @error('rw') is-invalid @enderror" value="{{ old('rw', $calon->rw) }}" required="required">
                    </div>
                    @error('rw')
                    <span class="mt-1 text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputEmail" class="form-control-label">Email <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <input id="inputEmail" class="form-control @error('email') is-invalid @enderror" type="email" name="email" maxlength="255" value="{{ old('email', $calon->email) }}" required="required">
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputPhoneNumber" class="form-control-label">Nomor Seluler <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-6 col-md-8 col-12">
                    <input id="inputPhoneNumber" class="form-control @error('phone_number') is-invalid @enderror" type="text" name="phone_number" maxlength="15" value="{{ old('phone_number', $calon->phone_number) }}"  required="required">
                    <small id="emailHelp" class="form-text text-muted">Angka [0-9], Contoh: 081234567890</small>
                    @error('phone_number')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-green">Pendidikan</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputRecentEducation" class="form-control-label">Pendidikan Terakhir <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="PendidikanTerakhir" name="recent_education" id="inputRecentEducation" title="PendidikanTerakhir" class="form-control @error('recent_education') is-invalid @enderror" required="required">
                      <option value="" {{ old('recent_education', $calon->recent_education_id) ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
                      @foreach($pendidikan as $p)
                      <option value="{{ $p->id }}" {{ old('recent_education', $calon->recent_education_id) == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endforeach
                    </select>
                    @error('recent_education')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          @if($calon->university_id)
          <div id="universityRow" class="row">
          @else
          <div id="universityRow" class="row" style="display: none;">
          @endif
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputUniversity" class="form-control-label">Universitas <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Universitas" name="university" id="inputUniversity" title="Universitas" class="form-control @error('university') is-invalid @enderror" {{ $calon->university_id ? 'required' : ''}}>
                      <option value="" {{ old('university', $calon->university_id) ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
                      @foreach($universitas as $u)
                      <option value="{{ $u->id }}" {{ old('university', $calon->university_id) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                      @endforeach
                    </select>
                    @error('university')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputAcademicBackground" class="form-control-label">Program Studi <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="ProgramStudi" name="academic_background" id="inputAcademicBackground" title="ProgramStudi" class="form-control @error('academic_background') is-invalid @enderror" required="required">
                      <option value="" {{ old('academic_background', $calon->academic_background_id) ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
                      @foreach($latar as $l)
                      <option value="{{ $l->id }}" {{ old('academic_background', $calon->academic_background_id) == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                      @endforeach
                    </select>
                    @error('academic_background')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-green">Hasil Tes</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputCompetency" class="form-control-label">Kompetensi <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <textarea id="inputCompetency" class="form-control @error('competency') is-invalid @enderror" name="competency" maxlength="255" rows="3" required="required">{{ old('competency', $calon->competency_test) }}</textarea>
                    @error('competency')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputPsychological" class="form-control-label">Psikotes <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <textarea id="inputPsychological" class="form-control @error('psychological') is-invalid @enderror" name="psychological" maxlength="255" rows="3" required="required">{{ old('psychological', $calon->psychological_test) }}</textarea>
                    @error('psychological')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-green">Rekomendasi</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="acceptanceOpt" class="form-control-label">Penerimaan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @foreach($penerimaan as $p)
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="acceptanceOpt{{ $p->id }}" name="acceptance_status" class="custom-control-input" value="{{ $p->id }}" {{ old('acceptance_status', $calon->acceptance_status_id) == $p->id ? 'checked' : '' }} required="required">
                      <label class="custom-control-label" for="acceptanceOpt{{ $p->id }}">{{ ucwords($p->status) }}</label>
                    </div>
                    @endforeach
                    @error('acceptance_status')
                    <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="unitRow" class="row" style="{{ old('acceptance_status', $calon->acceptance_status_id) == '1' ? '' : 'display: none;' }}">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="inputUnit" class="form-control-label">Unit Penempatan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <div class="row">
                      @foreach($unit as $u)
                      <div class="col-6">
                        <div class="custom-control custom-checkbox mb-1">
                          <input id="unit-{{$u->id}}" type="checkbox" name="unit[]" class="custom-control-input" value="{{ $u->id }}" {{ old('unit', $calon->units->pluck('id')->toArray()) && is_array(old('unit', $calon->units->pluck('id')->toArray())) && in_array($u->id, old('unit', $calon->units->pluck('id')->toArray() )) ? 'checked' : '' }}>
                          <label class="custom-control-label" for="unit-{{$u->id}}">{{ $u->name }}</label>
                        </div>
                      </div>
                      @endforeach
                    </div>
                    @error('unit')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="positionRow" class="row" style="{{ old('acceptance_status', $calon->acceptance_status_id) == '1' ? '' : 'display: none;' }}">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Position" class="form-control-label">Jabatan</label>
                  </div>
                  <div  class="col-lg-9 col-md-8 col-12">
                    <select class="select2-multiple form-control @error('position') is-invalid @enderror" name="position[]" multiple="multiple" id="select2Position" {{ (old('position') && count(old('position')) > 0) || $calon->units()->count() > 0 ? '' : 'disabled="disabled"' }}>
                      @foreach($jabatan as $j)
                      <option value="{{ $j->id }}" class=" bg-gray-300" {{ (old('position') && count(old('position')) > 0) || $calon->jabatans()->count() > 0 ? (old('position', $calon->jabatans->pluck('id')->toArray()) && is_array(old('position', $calon->jabatans->pluck('id')->toArray())) && in_array($j->id, old('position', $calon->jabatans->pluck('id')->toArray() )) ? 'selected' : '') : '' }} data-unit="{{ $j->unit_id }}" {{ old('unit', $calon->units->pluck('id')->toArray()) && is_array(old('unit', $calon->units->pluck('id')->toArray())) && in_array($j->unit_id,old('unit', $calon->units->pluck('id')->toArray() )) ? '' : 'disabled="disabled"' }}>{{ $j->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="statusRow" class="row" style="{{ old('acceptance_status', $calon->acceptance_status_id) == '1' ? '' : 'display: none;' }}">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="statusOpt" class="form-control-label">Status <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @foreach($status as $s)
                    <div class="custom-control custom-radio mb-1">
                      <input type="radio" id="statusOpt{{ $s->id }}" name="employee_status" class="custom-control-input" value="{{ $s->id }}" {{ old('employee_status', $calon->employee_status_id) == $s->id ? 'checked' : '' }} {{ $calon->employee_status_id ? 'required' : ''}}>
                      <label class="custom-control-label" for="statusOpt{{ $s->id }}">{{ $s->status }}</label>
                    </div>
                    @endforeach
                    @error('employee_status')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="periodRow" class="row" style="{{ old('acceptance_status', $calon->acceptance_status_id) == '1' ? '' : 'display: none;' }}">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="statusOpt" class="form-control-label">Masa Kerja <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-6 col-md-8 col-12">
                    <div class="input-daterange input-group">
                      <input type="text" class="input-sm form-control" name="period_start" placeholder="Mulai" value="{{ old('period_start', $calon->period_start) ? date('d F Y', strtotime(old('period_start', $calon->period_start))) : '' }}" {{ $calon->period_start ? 'required' : ''}}/>
                      <div class="input-group-prepend">
                        <span class="input-group-text">-</span>
                      </div>
                      <input type="text" class="input-sm form-control" name="period_end" placeholder="Selesai" value="{{ old('period_end', $calon->period_end) ? date('d F Y', strtotime(old('period_end', $calon->period_end))) : '' }}" {{ $calon->period_end ? 'required' : ''}}/>
                    </div>
                    @error('period_start')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @error('period_end')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="text-right">
                <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap-datepicker/locales/bootstrap-datepicker.id.min.js') }}"></script>
<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>
<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<!-- Birth Date Validation -->
<script src="{{ asset('js/date-validation.js') }}"></script>
<!-- Image Preview -->
<script src="{{ asset('js/image-preview.js') }}"></script>
<!-- Wilayah Indonesia -->
<script src="{{ asset('js/wilayah.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.acceptance')
@include('template.footjs.kepegawaian.datepicker')
@include('template.footjs.kepegawaian.positions-recommendation')
@include('template.footjs.kepegawaian.rtrw')
@include('template.footjs.kepegawaian.select2-default')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.kepegawaian.required-checkbox')
@include('template.footjs.kepegawaian.university')
@endsection