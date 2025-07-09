@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('title')
Ubah {{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.show', ['id' => $data->id]) }}">{{ $data->nik }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Ubah</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-purple-dark">Ubah {{ $active }}</h6>
      </div>
      <div class="card-body">
        <form action="{{ route($route.'.update') }}" id="edit-form" method="post" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="return validateDate('inputBirthDate','birthDateError');">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Info Umum</h6>
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
                    <input id="inputName" class="form-control @error('name') is-invalid @enderror" type="text" name="name" placeholder="Nama lengkap dan gelar" value="{{ old('name', $data->name) }}" maxlength="255" required="required">
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
                    <input id="inputNickname" class="form-control @error('nickname') is-invalid @enderror" type="text" name="nickname" value="{{ old('nickname', $data->nickname) }}" maxlength="255" required="required">
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
                    <img src="{{ $data->photo ? asset($data->photoPath) : asset('img/avatar/default.png') }}" id="preview" class="img-thumbnail photo-preview">
                    <input type="file" name="photo" class="file d-none" accept="image/jpg,image/jpeg,image/png">
                    <div class="input-group mt-3">
                      <input type="text" class="form-control @error('photo') is-invalid @enderror" disabled placeholder="Ubah foto..." id="file">
                      <div class="input-group-append">
                        <button type="button" class="browse btn btn-brand-green-dark">Pilih</button>
                      </div>
                    </div>
                    <small id="photoHelp" class="form-text text-muted">Ekstensi .jpg, .jpeg, .png, .webp dan maksimum 1 MB</small>
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
                    <input id="inputNik" class="form-control @error('nik') is-invalid @enderror" type="text" name="nik"  value="{{ old('nik', $data->nik) }}" placeholder="Nomor Induk Kependudukan" maxlength="16" required="required">
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
                    <input id="inputNpwp" class="form-control @error('npwp') is-invalid @enderror" type="text" name="npwp" value="{{ old('npwp', $data->npwp) }}" placeholder="Nomor Pokok Wajib Pajak" maxlength="15">
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
                    <label for="genderOpt" class="form-control-label">Jenis Kelamin <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    @foreach($genders as $g)
                    <div class="custom-control custom-radio custom-control-inline">
                      <input type="radio" id="genderOpt{{ $g->id }}" name="gender" class="custom-control-input" value="{{ $g->id }}" required="required" {{ old('gender', $data->gender_id) == $g->id ? 'checked' : '' }}>
                      <label class="custom-control-label" for="genderOpt{{ $g->id }}">{{ ucwords($g->name) }}</label>
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
                    <input id="inputBirthPlace" class="form-control @error('birth_place') is-invalid @enderror" type="text" name="birth_place" value="{{ old('birth_place', $data->birth_place) }}" maxlength="255" required="required">
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
                        <select aria-label="Day" name="birthday_day" id="day" title="Day" class="form-control @error('birthday_day') is-invalid @enderror" required="required">
                          <option value="" {{ old('birthday_day', date('d',strtotime($data->birth_date))) ? '' : 'selected' }} disabled="disabled">Tanggal</option>
                          @for($i=1;$i<=31;$i++)
                          <option value="{{ $i }}" {{ old('birthday_day', date('d',strtotime($data->birth_date))) == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                      </div>
                      <div class="col-4">
                        <select aria-label="Month" name="birthday_month" id="month" title="Month" class="form-control @error('birthday_month') is-invalid @enderror" required="required">
                          <option value="" {{ old('birthday_month', date('m',strtotime($data->birth_date))) ? '' : 'selected' }} disabled="disabled">Bulan</option>
                          <option value="1" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 1 ? 'selected' : '' }}>Jan</option>
                          <option value="2" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 2 ? 'selected' : '' }}>Feb</option>
                          <option value="3" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 3 ? 'selected' : '' }}>Mar</option>
                          <option value="4" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 4 ? 'selected' : '' }}>Apr</option>
                          <option value="5" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 5 ? 'selected' : '' }}>Mei</option>
                          <option value="6" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 6 ? 'selected' : '' }}>Jun</option>
                          <option value="7" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 7 ? 'selected' : '' }}>Jul</option>
                          <option value="8" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 8 ? 'selected' : '' }}>Agu</option>
                          <option value="9" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 9 ? 'selected' : '' }}>Sep</option>
                          <option value="10" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 10 ? 'selected' : '' }}>Okt</option>
                          <option value="11" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 11 ? 'selected' : '' }}>Nov</option>
                          <option value="12" {{ old('birthday_month', date('m',strtotime($data->birth_date))) == 12 ? 'selected' : '' }}>Des</option>
                        </select>
                      </div>
                      <div class="col-4">
                        @php
                        $year_start = date('Y', strtotime("-15 years"));
                        $year_end = date('Y', strtotime("-70 years"));
                        @endphp
                        <select aria-label="Year" name="birthday_year" id="year" title="Year" class="form-control @error('birthday_year') is-invalid @enderror" required="required">
                          <option value="" {{ old('birthday_year', date('Y',strtotime($data->birth_date))) ? '' : 'selected' }} disabled="disabled">Tahun</option>
                          @for($i=$year_start;$i>=$year_end;$i--)
                          <option value="{{ $i }}" {{ old('birthday_year', date('Y',strtotime($data->birth_date))) == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="row mb-4">
            <div class="col-12">
              <h6 class="font-weight-bold text-brand-purple-dark">Info Alamat dan Kontak</h6>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="provinceOpt" class="form-control-label">Provinsi <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Province" name="province" class="form-control @error('province') is-invalid @enderror" id="provinceOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('province', $data->region->provinceCode) ? '' : 'selected' }}>== Pilih Provinsi ==</option>
                      @foreach($provinces as $p)
                      <option value="{{ $p->code }}" {{ old('province', $data->region->provinceCode) == $p->code ? 'selected' : '' }}>{{ $p->name }}</option>
                      @endforeach
                    </select>
                    @error('province')
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
                    <label for="cityOpt" class="form-control-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="City" name="city" class="form-control @error('city') is-invalid @enderror" id="cityOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('city', $data->region->cityCode) ? '' : 'selected' }}>== Pilih Kabupaten/Kota ==</option>
                      @foreach($cities as $c)
                      <option value="{{ $c->code }}" {{ old('city', $data->region->cityCode) == $c->code ? 'selected' : '' }}>{{ $c->name }}</option>
                      @endforeach
                    </select>
                    @error('city')
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
                    <label for="subdistrictOpt" class="form-control-label">Kecamatan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Subdistrict" name="subdistrict" class="form-control @error('subdistrict') is-invalid @enderror" id="subdistrictOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('subdistrict', $data->region->subdistrictCode) ? '' : 'selected' }}>== Pilih Kecamatan ==</option>
                      @foreach($subdistricts as $s)
                      <option value="{{ $s->code }}" {{ old('subdistrict', $data->region->subdistrictCode) == $s->code ? 'selected' : '' }}>{{ $s->name }}</option>
                      @endforeach
                    </select>
                    @error('subdistrict')
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
                    <label for="villageOpt" class="form-control-label">Desa/Kelurahan <span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select aria-label="Village" name="village" class="form-control @error('village') is-invalid @enderror" id="villageOpt" tabindex="-1" aria-hidden="true" required="required">
                      <option value="" {{ old('village', $data->region->villageCode) ? '' : 'selected' }}>== Pilih Desa/Kelurahan ==</option>
                      @foreach($villages as $v)
                      <option value="{{ $v->code }}" {{ old('village', $data->region->villageCode) == $v->code ? 'selected' : '' }}>{{ $v->name }}</option>
                      @endforeach
                    </select>
                    @error('village')
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
                    <textarea id="inputAddress" class="form-control @error('address') is-invalid @enderror" name="address" maxlength="255" rows="3" required="required">{{ old('address',$data->address) }}</textarea>
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
                      <input id="inputRt" type="text" name="rt" class="form-control @error('rt') is-invalid @enderror" value="{{ old('rt', $data->rt) }}" required="required">
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
                      <input id="inputRw" type="text" name="rw" class="form-control @error('rw') is-invalid @enderror" value="{{ old('rw', $data->rw) }}" required="required">
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
                    <input id="inputEmail" class="form-control @error('email') is-invalid @enderror" type="email" name="email" maxlength="255" value="{{ old('email', $data->email) }}" required="required">
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
                    <input id="inputPhoneNumber" class="form-control @error('phone_number') is-invalid @enderror" type="text" name="phone_number" maxlength="15" value="{{ old('phone_number', $data->phone_number) }}"  required="required">
                    <small id="emailHelp" class="form-text text-muted">Angka [0-9], Contoh: 081234567890</small>
                    @error('phone_number')
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
                <button class="btn btn-success" type="submit">Simpan</button>
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

<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>

<!-- Birth Date Validation -->
<script src="{{ asset('js/date-validation.js') }}"></script>
<!-- Image Preview -->
<script src="{{ asset('js/image-preview.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.input-rt-rw')
@include('template.footjs.global.select-region')
@include('template.footjs.global.tooltip')

@endsection