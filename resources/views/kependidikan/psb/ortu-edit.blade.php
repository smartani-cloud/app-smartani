@extends('template.main.master')

@section('title')
Ubah {{ $active }}
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penerimaan Siswa Baru</a></li>
        <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route($route.'.show',['id' => $data->id]) }}">{{ $data->id}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-header py-3 bg-brand-green d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-white">Ubah {{ $active }}</h6>
                <a href="{{ route($route.'.show',['id' => $data->id]) }}" class="m-0 float-right btn btn-brand-green-dark btn-sm"><i class="fas fa-chevron-left mr-2"></i>Kembali</a>
            </div>
            <div class="card-body p-4">
              <form action="{{ route($route.'.update') }}" id="edit-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <input id="id" type="hidden" name="id" required="required" value="{{ $data->id }}">
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold text-brand-green">Info Orang Tua</h6>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold"><i class="fas fa-portrait mr-2"></i>Ayah</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="form-group">
                      <div class="row mb-3">
                        <div class="col-lg-3 col-md-4 col-12">
                          <label for="inputFatherName" class="form-control-label">Nama Ayah</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputFatherName" class="form-control" type="text" name="father_name" placeholder="Nama lengkap ayah" value="{{ old('father_name', $data->father_name) }}" maxlength="150">
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
                          <label for="inputFatherNik" class="form-control-label">NIK Ayah</label>
                        </div>
                        <div class="col-lg-6 col-md-5 col-12">
                          <input id="inputFatherNik" class="form-control" type="text" name="father_nik" value="{{ old('father_nik', $data->father_nik) }}" placeholder="Nomor Induk Kependudukan ayah" maxlength="16">
                          @error('father_nik')
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
                          <label for="inputFatherEmail" class="form-control-label">Email Ayah</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputFatherEmail" class="form-control" type="email" name="father_email" maxlength="100" value="{{ old('father_email', $data->father_email) }}">
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
                          <label for="inputFatherPhone" class="form-control-label">Nomor Seluler Ayah</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <input id="inputFatherPhone" class="form-control" type="text" name="father_phone" maxlength="15" value="{{ old('father_phone', $data->father_phone) }}">
                          <small id="fatherPhoneHelp" class="form-text text-muted">Angka [0-9], Contoh: 081234567890</small>
                          @error('father_phone')
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
                          <label for="optFatherJob" class="form-control-label">Pekerjaan Ayah</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <select aria-label="FatherJob" name="father_job" id="optFatherJob" title="FatherJob" class="select2 form-control">
                            <option value="">== Pilih ==</option>
                            @foreach($jobs as $job)
                            <option value="{{ $job->job }}" {{ old('father_job', $data->father_job) == $job->job ? 'selected' : '' }}>{{ $job->job }}</option>
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
                      <div class="row mb-3">
                        <div class="col-lg-3 col-md-4 col-12">
                          <label for="inputFatherPosition" class="form-control-label">Jabatan Ayah</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputFatherPosition" class="form-control" type="text" name="father_position" placeholder="Jabatan ayah" value="{{ old('father_position', $data->father_position) }}" maxlength="75">
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
                          <label for="inputFathersOfficeAddress" class="form-control-label">Alamat Kantor Ayah</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <textarea id="inputFathersOfficeAddress" class="form-control" name="father_job_address" maxlength="190" rows="3">{{ old('father_job_address', $data->father_job_address) }}</textarea>
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
                          <label for="inputFathersOfficePhone" class="form-control-label">Telepon Kantor Ayah</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <input id="inputFathersOfficePhone" class="form-control" type="text" name="father_phone_office" maxlength="15" value="{{ old('father_phone_office', $data->father_phone_office) }}">
                          <small id="fathersOfficePhoneHelp" class="form-text text-muted">Angka [0-9], Contoh: 02123456789</small>
                          @error('father_phone_office')
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
                          <label for="optFatherSalary" class="form-control-label">Gaji Ayah</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <select aria-label="FatherSalary" name="father_salary" id="optFatherSalary" title="FatherSalary" class="form-control">
                            <option value="">== Pilih ==</option>
                            <option value="&lt; Rp. 5.000.000" {{ old('father_salary', $data->father_salary) == "< Rp. 5.000.000"?"selected":""}}>&lt; Rp. 5.000.000</option>
                            <option value="Rp. 5.000.000 - Rp. 10.000.000" {{ old('father_salary', $data->father_salary) == "Rp. 5.000.000 - Rp. 10.000.000"?"selected":""}}>Rp. 5.000.000 - Rp. 10.000.000</option>
                            <option value="&gt; Rp. 10.000.000" {{ old('father_salary', $data->father_salary) == "> Rp. 10.000.000"?"selected":""}}>&gt; Rp. 10.000.000</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-2 mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold"><i class="fas fa-portrait mr-2"></i>Ibu</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="form-group">
                      <div class="row mb-3">
                        <div class="col-lg-3 col-md-4 col-12">
                          <label for="inputMotherName" class="form-control-label">Nama Ibu</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputMotherName" class="form-control" type="text" name="mother_name" placeholder="Nama lengkap ibu" value="{{ old('mother_name', $data->mother_name) }}" maxlength="150">
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
                          <label for="inputMotherNik" class="form-control-label">NIK Ibu</label>
                        </div>
                        <div class="col-lg-6 col-md-5 col-12">
                          <input id="inputMotherNik" class="form-control" type="text" name="mother_nik" value="{{ old('mother_nik', $data->mother_nik) }}" placeholder="Nomor Induk Kependudukan ibu" maxlength="16">
                          @error('mother_nik')
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
                          <label for="inputMotherEmail" class="form-control-label">Email Ibu</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputMotherEmail" class="form-control" type="email" name="mother_email" maxlength="100" value="{{ old('mother_email', $data->mother_email) }}">
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
                          <label for="inputMotherPhone" class="form-control-label">Nomor Seluler Ibu</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <input id="inputMotherPhone" class="form-control" type="text" name="mother_phone" maxlength="15" value="{{ old('mother_phone', $data->mother_phone) }}">
                          <small id="motherPhoneHelp" class="form-text text-muted">Angka [0-9], Contoh: 081234567890</small>
                          @error('mother_phone')
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
                          <label for="optMotherJob" class="form-control-label">Pekerjaan Ibu</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <select aria-label="MotherJob" name="mother_job" id="optMotherJob" title="MotherJob" class="select2 form-control">
                            <option value="">== Pilih ==</option>
                            @foreach($jobs as $job)
                            <option value="{{ $job->job }}" {{ old('mother_job', $data->mother_job) == $job->job ? 'selected' : '' }}>{{ $job->job }}</option>
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
                      <div class="row mb-3">
                        <div class="col-lg-3 col-md-4 col-12">
                          <label for="inputMotherPosition" class="form-control-label">Jabatan Ibu</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputMotherPosition" class="form-control" type="text" name="mother_position" placeholder="Jabatan ibu" value="{{ old('mother_position', $data->mother_position) }}" maxlength="75">
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
                          <label for="inputMothersOfficeAddress" class="form-control-label">Alamat Kantor Ibu</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <textarea id="inputMothersOfficeAddress" class="form-control" name="mother_job_address" maxlength="190" rows="3">{{ old('mother_job_address', $data->mother_job_address) }}</textarea>
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
                          <label for="inputMothersOfficePhone" class="form-control-label">Telepon Kantor Ibu</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <input id="inputMothersOfficePhone" class="form-control" type="text" name="mother_phone_office" maxlength="15" value="{{ old('mother_phone_office', $data->mother_phone_office) }}">
                          <small id="mothersOfficePhoneHelp" class="form-text text-muted">Angka [0-9], Contoh: 02123456789</small>
                          @error('mother_phone_office')
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
                          <label for="optMotherSalary" class="form-control-label">Gaji Ibu</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <select aria-label="MotherSalary" name="mother_salary" id="optMotherSalary" title="MotherSalary" class="form-control">
                            <option value="">== Pilih ==</option>
                            <option value="&lt; Rp. 5.000.000" {{ old('mother_salary', $data->mother_salary) == "< Rp. 5.000.000"?"selected":""}}>&lt; Rp. 5.000.000</option>
                            <option value="Rp. 5.000.000 - Rp. 10.000.000" {{ old('mother_salary', $data->mother_salary) == "Rp. 5.000.000 - Rp. 10.000.000"?"selected":""}}>Rp. 5.000.000 - Rp. 10.000.000</option>
                            <option value="&gt; Rp. 10.000.000" {{ old('mother_salary', $data->mother_salary) == "> Rp. 10.000.000"?"selected":""}}>&gt; Rp. 10.000.000</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-2 mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold"><i class="fas fa-home mr-2"></i>Alamat & Kontak</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="form-group">
                      <div class="row mb-3">
                        <div class="col-lg-3 col-md-4 col-12">
                          <label for="inputParentAddress" class="form-control-label">Alamat Orang Tua</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <textarea id="inputParentAddress" class="form-control" name="parent_address" maxlength="190" rows="3">{{ old('parent_address', $data->parent_address) }}</textarea>
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
                          <label for="inputAlternativePhone" class="form-control-label">Telepon Alternatif</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <input id="inputAlternativePhone" class="form-control" type="text" name="parent_phone_number" maxlength="15" value="{{ old('parent_phone_number', $data->parent_phone_number) }}">
                          <small id="alternativePhoneHelp" class="form-text text-muted">Angka [0-9], Contoh: 081234567890</small>
                          @error('parent_phone_number')
                          <span class="text-danger">{{ $message }}</span>
                          @enderror
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold text-brand-green">Info Wali</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="form-group">
                      <div class="row mb-3">
                        <div class="col-lg-3 col-md-4 col-12">
                          <label for="inputGuardianName" class="form-control-label">Nama Wali</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputGuardianName" class="form-control" type="text" name="guardian_name" placeholder="Nama lengkap wali" value="{{ old('guardian_name', $data->guardian_name) }}" maxlength="150">
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
                          <label for="inputGuardianNik" class="form-control-label">NIK Wali</label>
                        </div>
                        <div class="col-lg-6 col-md-5 col-12">
                          <input id="inputGuardianNik" class="form-control" type="text" name="guardian_nik" value="{{ old('guardian_nik', $data->guardian_nik) }}" placeholder="Nomor Induk Kependudukan wali" maxlength="16">
                          @error('guardian_nik')
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
                          <label for="inputGuardianAddress" class="form-control-label">Alamat Wali</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <textarea id="inputGuardianAddress" class="form-control" name="guardian_address" maxlength="190" rows="3">{{ old('guardian_address', $data->guardian_address) }}</textarea>
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
                          <label for="inputGuardianEmail" class="form-control-label">Email Wali</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputGuardianEmail" class="form-control" type="email" name="guardian_email" maxlength="100" value="{{ old('guardian_email', $data->guardian_email) }}">
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
                          <label for="inputguardianPhone" class="form-control-label">Nomor Seluler Wali</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <input id="inputguardianPhone" class="form-control" type="text" name="guardian_phone" maxlength="15" value="{{ old('guardian_phone', $data->guardian_phone_number) }}">
                          <small id="guardianPhoneHelp" class="form-text text-muted">Angka [0-9], Contoh: 081234567890</small>
                          @error('guardian_phone')
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
                          <label for="optGuardianJob" class="form-control-label">Pekerjaan Wali</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <select aria-label="GuardianJob" name="guardian_job" id="optGuardianJob" title="GuardianJob" class="select2 form-control">
                            <option value="">== Pilih ==</option>
                            @foreach($jobs as $job)
                            <option value="{{ $job->job }}" {{ old('guardian_job', $data->guardian_job) == $job->job ? 'selected' : '' }}>{{ $job->job }}</option>
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
                      <div class="row mb-3">
                        <div class="col-lg-3 col-md-4 col-12">
                          <label for="inputGuardianPosition" class="form-control-label">Jabatan Wali</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <input id="inputGuardianPosition" class="form-control" type="text" name="guardian_position" placeholder="Jabatan wali" value="{{ old('guardian_position', $data->guardian_position) }}" maxlength="75">
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
                          <label for="inputGuardiansOfficeAddress" class="form-control-label">Alamat Kantor Wali</label>
                        </div>
                        <div class="col-lg-9 col-md-8 col-12">
                          <textarea id="inputGuardiansOfficeAddress" class="form-control" name="guardian_job_address" maxlength="190" rows="3">{{ old('guardian_job_address', $data->guardian_job_address) }}</textarea>
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
                          <label for="inputGuardiansOfficePhone" class="form-control-label">Telepon Kantor Wali</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <input id="inputGuardiansOfficePhone" class="form-control" type="text" name="guardian_phone_office" maxlength="15" value="{{ old('guardian_phone_office', $data->guardian_phone_office) }}">
                          <small id="guardiansOfficePhoneHelp" class="form-text text-muted">Angka [0-9], Contoh: 02123456789</small>
                          @error('guardian_phone_office')
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
                          <label for="optGuardianSalary" class="form-control-label">Gaji Wali</label>
                        </div>
                        <div class="col-lg-6 col-md-8 col-12">
                          <select aria-label="GuardianSalary" name="guardian_salary" id="optGuardianSalary" title="GuardianSalary" class="form-control">
                            <option value="">== Pilih ==</option>
                            <option value="&lt; Rp. 5.000.000" {{ old('guardian_salary', $data->guardian_salary) == "< Rp. 5.000.000"?"selected":""}}>&lt; Rp. 5.000.000</option>
                            <option value="Rp. 5.000.000 - Rp. 10.000.000" {{ old('guardian_salary', $data->guardian_salary) == "Rp. 5.000.000 - Rp. 10.000.000"?"selected":""}}>Rp. 5.000.000 - Rp. 10.000.000</option>
                            <option value="&gt; Rp. 10.000.000" {{ old('guardian_salary', $data->guardian_salary) == "> Rp. 10.000.000"?"selected":""}}>&gt; Rp. 10.000.000</option>
                          </select>
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
                @if($data->childrensCount > 0)
                <hr>
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold text-brand-green">Info Anak</h6>
                  </div>
                </div>
                @if($data->siswas()->count())
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Siswa
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        @php $i = 1 @endphp
                        @foreach($data->siswas()->orderBy('birth_date','desc')->get() as $siswa)
                        {{ $i++.'. ' }}<a href="{{ route('kependidikan.kbm.siswa.show',['id' => $siswa->id]) }}" target="_blank" class="text-decoration-none text-info">{{ $siswa->student_name }}</a>{{ $siswa->latestLevel ? ' - Kelas '.$siswa->latestLevel : null }}{!! $i <= $data->siswas()->count() ? '<br>' : null !!}
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
                @endif
                @if($data->calonSiswa()->count())
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Calon Siswa
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        @php $i = 1 @endphp
                        @foreach($data->calonSiswa()->orderBy('birth_date','desc')->get() as $calon)
                        @if(in_array(auth()->user()->role_id,[1,2,3,7,8,9,11,12,13,14,17,18,20,21,25,26]))
                        {{ $i++.'. ' }}<a href="{{ route('kependidikan.psb.calonsiswa.lihat',['id' => $calon->id]) }}" target="_blank" class="text-decoration-none text-info">{{ $calon->student_name }}</a>{{ $calon->level ? ' - '.($calon->unit_id != 1 ? 'Kelas ' : null).$calon->level->level : null }}{!! $i <= $data->calonSiswa()->count() ? '<br>' : null !!}
                        @else
                        {{ $i++.'. '.$calon->student_name.($calon->level ? ' - '.($calon->unit_id != 1 ? 'Kelas ' : null).$calon->level->level : null) }}{!! $i <= $data->calonSiswa()->count() ? '<br>' : null !!}
                        @endif
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

@include('template.footjs.kepegawaian.select2-default')
@endsection
