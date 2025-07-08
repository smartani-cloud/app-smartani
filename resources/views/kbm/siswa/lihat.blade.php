@extends('template.main.master')

@section('title')
Lihat Data Siswa
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Lihat Data Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/siswa/{{ $siswa->is_lulus == 1 ? 'alumni' : 'aktif' }}">Siswa</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $siswa->id }}</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
        <div class="card-header py-3 bg-brand-green-dark d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Form Lihat Siswa</h6>
        </div>
            <div class="card-body">
                <form action="/kependidikan/kbm/siswa/tambah"  method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Program Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="unit" class="form-control-label">Program</label>
                            </div>
                            <div class="col-sm-6">
                                {{ $siswa->unit->name }}
                            </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Umum Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nis" class="col-sm-4 control-label">NIPD<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nis" placeholder="Nomor Induk Siswa" value="{{ $siswa->student_nis }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">Nama Lengkap<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama" placeholder="Nama" value="{{ $siswa->identitas->student_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_pendek" class="col-sm-4 control-label">Nama Panggilan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_pendek" placeholder="Nama" value="{{ $siswa->identitas->student_nickname }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nisn" class="col-sm-4 control-label">NISN<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nisn" placeholder="NISN" value="{{ $siswa->student_nisn }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tempat_lahir" class="col-sm-4 control-label">Tempat Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="tempat_lahir" placeholder="Tempat Lahir" value="{{ $siswa->identitas->birth_place }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-sm-4 control-label">Tanggal Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" name="tanggal_lahir"  value="{{ $siswa->identitas->birth_date }}" disabled>
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <label for="jenis_kelamin" class="col-sm-4 control-label">Jenis Kelamin<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="jenis_kelamin" class="select2 form-control select2-hidden-accessible auto_width" id="jenis_kelamin" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="1">Laki-Laki</option>
                                    <option value="2">Perempuan</option>
                                </select>
                            </div>
                        </div> -->
                        <!-- Jenis Kelamin -->
                        <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="jenis_kelamin" class="form-control-label">Jenis Kelamin</label>
                            </div>
                            <div class="col-sm-6">
                                {{ $siswa->identitas->gender_id?$siswa->identitas->jeniskelamin->name:'-' }}
                            </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Agama</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="relgion" placeholder="1" value="{{ $siswa->identitas->religion_id?$siswa->identitas->agama->name:'-' }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="anak_ke" class="col-sm-4 control-label">Anak ke-</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="anak_ke" placeholder="1" value="{{ $siswa->identitas->child_of }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Status Keluarga</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="anak_ke" placeholder="1" value="{{ $siswa->identitas->family_status }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelompok" class="col-sm-4 control-label">Tanggal Masuk</label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" name="tanggal_masuk" placeholder="" value="{{ $siswa->join_date }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Semester Masuk</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="tanggal_masuk" placeholder="" value="{{ $siswa->semester->semester_id }}" disabled>
                            </div>
                        </div>
                        @if($siswa->is_lulus != 1)
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="tanggal_masuk" placeholder="" value="{{ $siswa->level?$siswa->level->level:'-' }}" disabled>
                            </div>
                        </div>
                        @endif
                        <hr>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Alamat Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat" class="col-sm-4 control-label">Alamat</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat" placeholder="Alamat" value="{{ $siswa->identitas->address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">No Rumah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="no_rumah" placeholder="001" value="{{ $siswa->identitas->address_number }}" disabled>
                            </div>
                        </div>
                        <!-- Alamat RT dan RW -->
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">RT</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="rt" placeholder="RT" value="{{ $siswa->identitas->rt }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rw" class="col-sm-4 control-label">RW</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="rw" placeholder="RW" value="{{ $siswa->identitas->rw}}" disabled>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-12">
                                <label for="inputRw" class="form-control-label">RT <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-3 col-md-4 col-8">
                                <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                                <input id="inputRw" type="text" name="rt" class="form-control @error('rt') is-invalid @enderror" value="{{ old('rt') }}" required="required">
                                </div>
                                @error('rt')
                                <span class="mt-1 text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-12">
                                <label for="inputRw" class="form-control-label">RW <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-3 col-md-4 col-8">
                                <div class="input-group bootstrap-touchspin bootstrap-touchspin-injected">
                                <input id="inputRw" type="text" name="rw" class="form-control @error('rw') is-invalid @enderror" value="{{ old('rw') }}" required="required">
                                </div>
                                @error('rw')
                                <span class="mt-1 text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            </div>
                        </div> -->
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Provinsi</label>
                            <div class="col-sm-6">
                                <select name="provinsi" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                    <option value="" id="provinsi">{{$provinsi}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kabupaten/Kota</label>
                            <div class="col-sm-6">
                                <select name="kabupaten" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                    <option value="" id="kabupaten">{{$kabupaten}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kecamatan</label>
                            <div class="col-sm-6">
                                <select name="kecamatan" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                    <option value="" id="kecamatan">{{$kecamatan}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Desa/Kelurahan</label>
                            <div class="col-sm-6">
                                <select name="desa" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                    <option value="" id="desa">{{$desa}}</option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Orang Tua</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ayah" class="col-sm-4 control-label">Nama Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_ayah" placeholder="Nama Ayah" value="{{ $siswa->identitas->orangtua->father_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_ayah" class="col-sm-4 control-label">NIK Ayah</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_ayah" placeholder="NIK Ayah" value="{{ $siswa->identitas->orangtua->father_nik }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_ayah" class="col-sm-4 control-label">No HP Ayah</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_ayah" placeholder="No HP Ayah" value="{{ $siswa->identitas->orangtua->father_phone }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_ayah" class="col-sm-4 control-label">email Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_ayah" placeholder="Email Ayah" value="{{ $siswa->identitas->orangtua->father_email }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ayah" class="col-sm-4 control-label">Pekerjaan Ayah</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->identitas->orangtua->father_job }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ayah" class="col-sm-4 control-label">Jabatan Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_ayah" placeholder="Jabatan Ayah" value="{{ $siswa->identitas->orangtua->father_position }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ayah" class="col-sm-4 control-label">Telp Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_ayah" placeholder="" value="{{ $siswa->identitas->orangtua->father_phone_office }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ayah" class="col-sm-4 control-label">Alamat Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_ayah" placeholder="" value="{{ $siswa->identitas->orangtua->father_job_address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ayah" class="col-sm-4 control-label">Gaji Ayah</label>
                            <div class="col-sm-6">
                                <select name="gaji_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->identitas->orangtua->father_salary }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ibu" class="col-sm-4 control-label">Nama Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_ibu" placeholder="Nama Ibu" value="{{ $siswa->identitas->orangtua->mother_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_ibu" class="col-sm-4 control-label">NIK Ibu</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_ibu" placeholder="NIK Ibu" value="{{ $siswa->identitas->orangtua->mother_nik }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_ibu" class="col-sm-4 control-label">No HP Ibu</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_ibu" placeholder="No HP Ibu" value="{{ $siswa->identitas->orangtua->mother_phone }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_ibu" class="col-sm-4 control-label">email Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_ibu" placeholder="Email Ibu" value="{{ $siswa->identitas->orangtua->mother_email }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ibu" class="col-sm-4 control-label">Pekerjaan Ibu</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->identitas->orangtua->mother_job }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ibu" class="col-sm-4 control-label">Jabatan Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_ibu" placeholder="Jabatan Ibu" value="{{ $siswa->identitas->orangtua->mother_position }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ibu" class="col-sm-4 control-label">Telp Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_ibu" placeholder="" value="{{ $siswa->identitas->orangtua->mother_phone_office }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ibu" class="col-sm-4 control-label">Alamat Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_ibu" placeholder="" value="{{ $siswa->identitas->orangtua->mother_job_address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ibu" class="col-sm-4 control-label">Gaji Ibu</label>
                            <div class="col-sm-6">
                                <select name="gaji_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="" selected>{{ $siswa->identitas->orangtua->mother_salary }}</option>
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_ortu" class="col-sm-4 control-label">Alamat Orang Tua</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_ortu" placeholder="Alamat Orang Tua" value="{{ $siswa->identitas->orangtua->parent_address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_hp_ortu" class="col-sm-4 control-label">No HP Alternatif</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="no_hp_ortu" placeholder="08222*****" value="{{ $siswa->identitas->orangtua->parent_phone_number }}" disabled>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Wali</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_wali" class="col-sm-4 control-label">Nama Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_wali" placeholder="Nama Wali" value="{{ $siswa->identitas->orangtua->guardian_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_wali" class="col-sm-4 control-label">NIK Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_wali" placeholder="NIK Wali" value="{{ $siswa->identitas->orangtua->guardian_nik }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_wali" class="col-sm-4 control-label">No HP Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_wali" placeholder="No HP Wali" value="{{ $siswa->identitas->orangtua->guardian_phone_number }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_wali" class="col-sm-4 control-label">email Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_wali" placeholder="Email Wali" value="{{ $siswa->identitas->orangtua->guardian_email }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_wali" class="col-sm-4 control-label">Pekerjaan Wali</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="" selected>{{ $siswa->identitas->orangtua->guardian_job}}</option>
                                        <option value="">== Pilih Pekerjaan ==</option>
                                        <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        <option value="Wiraswasta">Wiraswasta</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_wali" class="col-sm-4 control-label">Jabatan Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_wali" placeholder="Jabatan Wali" value="{{ $siswa->identitas->orangtua->guardian_position }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_wali" class="col-sm-4 control-label">Telp Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_wali" placeholder="" value="{{ $siswa->identitas->orangtua->guardian_phone_office }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_wali" class="col-sm-4 control-label">Alamat Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_wali" placeholder="" value="{{ $siswa->identitas->orangtua->guardian_job_address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_wali" class="col-sm-4 control-label">Gaji Wali</label>
                            <div class="col-sm-6">
                                <select name="gaji_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->identitas->orangtua->guardian_salary }}</option>
                                        <option value="">== Pilih ==</option>
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_wali" class="col-sm-4 control-label">Alamat Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_wali" placeholder="Alamat Wali" value="{{ $siswa->identitas->orangtua->guardian_address }}" disabled>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Sekolah Asal</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Asal Sekolah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="asal_sekolah" placeholder="Asal Sekolah" value="{{ $siswa->origin_school }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_asal_sekolah" class="col-sm-4 control-label">Alamat Asal Sekolah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_asal_sekolah" placeholder="Alamat Asal Sekolah" value="{{ $siswa->origin_school_address }}" disabled>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Saudara Kandung</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Nama Saudara Kandung di Auliya</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="saudara_nama" placeholder="Nama" value="{{ $siswa->sibling_name?$siswa->sibling_name:'-' }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas Saudara Kandung</label>
                            <div class="col-sm-6">
                                <select name="saudara_kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ ($siswa->sibling_level_id)?$siswa->levelsaudara->level:'-' }}</option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Sumber Informasi</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="info_dari" class="col-sm-4 control-label">Informasi Dari</label>
                            <div class="col-sm-6">
                                <select name="info_dari" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->info_from }}</option>
                                        <option value="">== Pilih ==</option>
                                        <option value="Orangtua Sekolah Digiyok">Orangtua Sekolah Digiyok</option>
                                        <option value="Guru/Staf">Guru/Staf</option>
                                        <option value="Brosur">Brosur</option>
                                        <option value="Spanduk">Spanduk</option>
                                        <option value="Website dan Sosmed Sekolah Digiyok">Website dan Sosmed Sekolah Digiyok</option>
                                        <option value="Media Cetak">Media Cetak</option>
                                        <option value="Sering Lewat">Sering Lewat</option>
                                        <option value="Teman">Teman</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="info_nama" class="col-sm-4 control-label">Nama</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="info_nama" placeholder="Nama" value="{{ $siswa->info_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="posisi" class="col-sm-4 control-label">Posisi / Kelas</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="posisi" placeholder="" value="{{ $siswa->position }}" disabled>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-md-12">
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-purple-dark">Simpan</button>
                        </div>
                    </div> -->
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


<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>

<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
<script src="{{asset('js/wilayah.js')}}"></script>



@endsection