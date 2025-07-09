@extends('template.main.master')

@section('title')
Lihat Data Siswa
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Lihat Data Calon Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penerimaan Siswa Baru</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Calon Siswa</a></li>
        <li class="breadcrumb-item active" aria-current="page">Lihat</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
        <div class="card-header py-3 bg-brand-green-dark d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Form Lihat Calon Siswa</h6>
        </div>
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sukses!</strong> {{ Session::get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
            <div class="card-body">
                <form action="{{route('kependidikan.psb.tahun-angkatan')}}"  method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Tahun Ajaran</h6>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-10">
                                <label for="unit" class="form-control-label">Tahun Ajaran<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="academic_year" placeholder="Nomor Induk Siswa" value="{{ $siswa->tahunAjaran->academic_year }}" disabled>
                            </div>
                            </div>
                        </div>
                        @if ($siswa->semester_id)
                        <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-10">
                                <label for="unit" class="form-control-label">Semester<span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="academic_year" placeholder="Nomor Induk Siswa" value="{{ $siswa->semester->semester }}" disabled>
                            </div>
                            </div>
                        </div>
                        @endif
                        @if(!in_array(Auth::user()->role->name,['am','aspv','cspv','lay']))
                        <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-10">
                            </div>
                            <div class="col-md-4">
                                <div class="text-center mt-4">
                                    <input type="hidden" name="siswa_id" value="{{$siswa->id}}">
                                    <button type="button" class="btn btn-brand-green-dark" data-toggle="modal" data-target="#UbahTahunAkademik" data-id="{{$siswa->id}}">Ubah Tahun Ajaran</button>
                                </div>
                            </div>
                            </div>
                        </div>
                        @endif
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Program Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-12">
                                <label for="unit" class="form-control-label">Program <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-lg-9 col-md-8 col-12">
                                <input type="radio" id="unitOpt" name="unit" class="custom-control-input" required="required" checked>
                                <label class="custom-control-label" for="unitOpt">{{ $siswa->unit->name }}</label>
                            </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-md-4 col-12">
                                    <label for="unit" class="form-control-label">Kelas <span class="text-danger">*</span></label>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" value="{{ $siswa->level ? $siswa->level->level : '-'}}" disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Umum Siswa</h6>
                            </div>
                        </div>
                        @if ($siswa->status_id != 5)
                        @if(in_array(Auth::user()->role->name,['admin','sek','keu']))
                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col-lg-3 col-md-4 col-10"></div>
                                <div class="col-md-4">
                                    <div class="text-center mt-4">
                                        <a class="btn btn-brand-green-dark" href="{{route('kependidikan.psb.calonsiswa.edit',$siswa->id)}}">Ubah data siswa</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                        @if($siswa->status_id == 5)
                        @if(in_array(Auth::user()->role->name,['ctl']))
                        <div class="form-group">
                            <div class="row mb-3">
                            <div class="col-lg-3 col-md-4 col-10">
                            </div>
                            <div class="col-md-4">
                                <div class="text-center mt-4">
                                    <a class="btn btn-brand-green-dark" href="{{route('kependidikan.psb.calonsiswa.edit',$siswa->id)}}">Ubah data siswa</a>
                                </div>
                            </div>
                            </div>
                        </div>
                        @endif
                        @endif
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">NIK<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nik"  placeholder="Nomor Induk Kependudukan" maxlength="16" value="{{ $siswa->nik }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">Nama Lengkap<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama" placeholder="Nama" value="{{ $siswa->student_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_pendek" class="col-sm-4 control-label">Nama Panggilan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_pendek" placeholder="Nama" value="{{ $siswa->student_nickname }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nisn" class="col-sm-4 control-label">NISN</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nisn" placeholder="NISN" value="{{ $siswa->student_nisn }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tempat_lahir" class="col-sm-4 control-label">Tempat Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="tempat_lahir" placeholder="Tempat Lahir" value="{{ $siswa->birth_place }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-sm-4 control-label">Tanggal Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" name="tanggal_lahir"  value="{{ $siswa->birth_date }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jenis_kelamin" class="col-sm-4 control-label">Jenis Kelamin<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="genderOpt" name="jenis_kelamin" class="custom-control-input" value="" required="required" checked>
                                <label class="custom-control-label" for="genderOpt">{{ $siswa->gender_id? ucwords($siswa->jeniskelamin->name) : '-' }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Agama</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="agama" placeholder="1" value="{{ $siswa->religion_id?$siswa->agama->name:'-' }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="tanggal_masuk" placeholder="" value="{{ $siswa->level ? $siswa->level->level : '-' }}" disabled>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Alamat Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat" class="col-sm-4 control-label">Alamat</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat" placeholder="Alamat" value="{{ $siswa->address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">No Rumah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="no_rumah" placeholder="001" value="{{ $siswa->address_number }}" disabled>
                            </div>
                        </div>
                        <!-- Alamat RT dan RW -->
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">RT</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="rt" placeholder="RT" value="{{ $siswa->rt }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rw" class="col-sm-4 control-label">RW</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="rw" placeholder="RW" value="{{ $siswa->rw}}" disabled>
                            </div>
                        </div>
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
                                <input type="text" class="form-control" name="nama_ayah" placeholder="Nama Ayah" value="{{ $siswa->orangtua->father_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_ayah" class="col-sm-4 control-label">NIK Ayah</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_ayah" placeholder="NIK Ayah" value="{{ $siswa->orangtua->father_nik }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_ayah" class="col-sm-4 control-label">No HP Ayah</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_ayah" placeholder="No HP Ayah" value="{{ $siswa->orangtua->father_phone }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_ayah" class="col-sm-4 control-label">email Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_ayah" placeholder="Email Ayah" value="{{ $siswa->orangtua->father_email }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ayah" class="col-sm-4 control-label">Pekerjaan Ayah</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->orangtua->father_job }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ayah" class="col-sm-4 control-label">Jabatan Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_ayah" placeholder="Jabatan Ayah" value="{{ $siswa->orangtua->father_position }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ayah" class="col-sm-4 control-label">Telp Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_ayah" placeholder="" value="{{ $siswa->orangtua->father_phone_office }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ayah" class="col-sm-4 control-label">Alamat Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_ayah" placeholder="" value="{{ $siswa->orangtua->father_job_address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ayah" class="col-sm-4 control-label">Gaji Ayah</label>
                            <div class="col-sm-6">
                                <select name="gaji_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->orangtua->father_salary }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ibu" class="col-sm-4 control-label">Nama Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_ibu" placeholder="Nama Ibu" value="{{ $siswa->orangtua->mother_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_ibu" class="col-sm-4 control-label">NIK Ibu</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_ibu" placeholder="NIK Ibu" value="{{ $siswa->orangtua->mother_nik }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_ibu" class="col-sm-4 control-label">No HP Ibu</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_ibu" placeholder="No HP Ibu" value="{{ $siswa->orangtua->mother_phone }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_ibu" class="col-sm-4 control-label">email Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_ibu" placeholder="Email Ibu" value="{{ $siswa->orangtua->mother_email }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ibu" class="col-sm-4 control-label">Pekerjaan Ibu</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->orangtua->mother_job }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ibu" class="col-sm-4 control-label">Jabatan Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_ibu" placeholder="Jabatan Ibu" value="{{ $siswa->orangtua->mother_position }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ibu" class="col-sm-4 control-label">Telp Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_ibu" placeholder="" value="{{ $siswa->orangtua->mother_phone_office }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ibu" class="col-sm-4 control-label">Alamat Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_ibu" placeholder="" value="{{ $siswa->orangtua->mother_job_address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ibu" class="col-sm-4 control-label">Gaji Ibu</label>
                            <div class="col-sm-6">
                                <select name="gaji_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="" selected>{{ $siswa->orangtua->mother_salary }}</option>
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_ortu" class="col-sm-4 control-label">Alamat Orang Tua</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_ortu" placeholder="Alamat Orang Tua" value="{{ $siswa->orangtua->parent_address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_hp_ortu" class="col-sm-4 control-label">No HP Alternatif</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="no_hp_ortu" placeholder="08222*****" value="{{ $siswa->orangtua->parent_phone_number }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pegawai_auliya" class="col-sm-4 control-label">Pegawai Auliya?</label>
                            <div class="col-sm-6">
                                <select name="pegawai_auliya" class="form-control auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="0">Tidak</option>
                                        <option value="1" {{$siswa->orangtua->employee_id?'selected':''}}>Ya</option>
                                </select>
                            </div>
                        </div>
                        @if ($siswa->orangtua->employee_id)
                        <div class="form-group row">
                            <label for="pegawai" class="col-sm-4 control-label">Pegawai</label>
                            <div class="col-sm-6">
                            <select name="pegawai" class="form-control auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                <option value="">Pilih</option>
                                @foreach ($pegawais as $pegawai)
                                    <option value="{{$pegawai->id}}" {{$siswa->orangtua->employee_id==$pegawai->id?'selected':''}}>{{$pegawai->name}}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        @endif
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Wali</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_wali" class="col-sm-4 control-label">Nama Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_wali" placeholder="Nama Wali" value="{{ $siswa->orangtua->guardian_name }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_wali" class="col-sm-4 control-label">NIK Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_wali" placeholder="NIK Wali" value="{{ $siswa->orangtua->guardian_nik }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_wali" class="col-sm-4 control-label">No HP Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_wali" placeholder="No HP Wali" value="{{ $siswa->orangtua->guardian_phone_number }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_wali" class="col-sm-4 control-label">email Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_wali" placeholder="Email Wali" value="{{ $siswa->orangtua->guardian_email }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_wali" class="col-sm-4 control-label">Pekerjaan Wali</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="" selected>{{ $siswa->orangtua->guardian_job}}</option>
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
                                <input type="text" class="form-control" name="jabatan_wali" placeholder="Jabatan Wali" value="{{ $siswa->orangtua->guardian_position }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_wali" class="col-sm-4 control-label">Telp Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_wali" placeholder="" value="{{ $siswa->orangtua->guardian_phone_office }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_wali" class="col-sm-4 control-label">Alamat Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_wali" placeholder="" value="{{ $siswa->orangtua->guardian_job_address }}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_wali" class="col-sm-4 control-label">Gaji Wali</label>
                            <div class="col-sm-6">
                                <select name="gaji_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                        <option value="">{{ $siswa->orangtua->guardian_salary }}</option>
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
                                <input type="text" class="form-control" name="alamat_wali" placeholder="Alamat Wali" value="{{ $siswa->orangtua->guardian_address }}" disabled>
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
                                        <option value="Orangtua Auliya">Orangtua Auliya</option>
                                        <option value="Guru/Staf">Guru/Staf</option>
                                        <option value="Brosur">Brosur</option>
                                        <option value="Spanduk">Spanduk</option>
                                        <option value="Website dan Sosmed Auliya Keren">Website dan Sosmed Auliya Keren</option>
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
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                        </div>
                    </div> -->
                </div>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- @if ($siswa->status_id == 1) --}}
<!-- Modal UbahTahunAkademik -->
<div id="UbahTahunAkademik" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column" id="form_title" style="display:block">
                <h4 class="modal-title w-100">Penerimaan Siswa</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('kependidikan.psb.tahun-angkatan')}}" method="POST">
            @csrf
            <div class="modal-body" id="form_penerimaan" style="display:block">
                <div class="form-group">
                    <label for="tahun_akademik" class="col-form-label">Tahun Akademik</label>
                    <select name="tahun_akademik" class="select2 form-control select2-hidden-accessible auto_width" id="tahun_akademik" style="width:100%;" tabindex="-1" aria-hidden="true">
                        @foreach ($tahunAjaran as $tahun)                        
                            <option value="{{$tahun->id}}" {{$tahun->id==$siswa->academic_year_id?'selected':''}}>{{$tahun->academic_year}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="semester" class="col-form-label">Semester</label>
                    <select name="semester" class="select2 form-control select2-hidden-accessible auto_width" id="semester" style="width:100%;" tabindex="-1" aria-hidden="true">
                        <option value="Ganjil" {{ $siswa->semester&&$siswa->semester->semester=='Ganjil'?'selected':''}}>Ganjil</option>
                        <option value="Genap" {{ $siswa->semester&&$siswa->semester->semester=='Genap'?'selected':''}}>Genap</option>
                    </select>
                </div>
                @if($siswa->status_id == 5)
                <div class="form-group">
                  <label for="year_spp" class="col-form-label">Tahun Mulai SPP</label>
                  <input type="number" name="year_spp" class="form-control" id="year_spp" value="{{ $siswa->year_spp ? $siswa->year_spp : date('Y') }}" min="2000" required>
                </div>
                <div class="form-group">
                    <label for="month_spp" class="col-form-label">Bulan Mulai SPP</label>
                    <select name="month_spp" class="select2 form-control select2-hidden-accessible auto_width" id="month_spp" style="width:100%;" tabindex="-1" aria-hidden="true" required>
                        <option value="1" {{ $siswa->month_spp == '1' ? 'selected' : null }}>Januari</option>
                        <option value="2" {{ $siswa->month_spp == '2' ? 'selected' : null }}>Februari</option>
                        <option value="3" {{ $siswa->month_spp == '3' ? 'selected' : null }}>Maret</option>
                        <option value="4" {{ $siswa->month_spp == '4' ? 'selected' : null }}>April</option>
                        <option value="5" {{ $siswa->month_spp == '5' ? 'selected' : null }}>Mei</option>
                        <option value="6" {{ $siswa->month_spp == '6' ? 'selected' : null }}>Juni</option>
                        <option value="7" {{ $siswa->month_spp == '7' ? 'selected' : null }}>Juli</option>
                        <option value="8" {{ $siswa->month_spp == '8' ? 'selected' : null }}>Agustus</option>
                        <option value="9" {{ $siswa->month_spp == '9' ? 'selected' : null }}>September</option>
                        <option value="10" {{ $siswa->month_spp == '10' ? 'selected' : null }}>Oktober</option>
                        <option value="11" {{ $siswa->month_spp == '11' ? 'selected' : null }}>November</option>
                        <option value="12" {{ $siswa->month_spp == '12' ? 'selected' : null }}>Desember</option>
                    </select>
                </div>
                @endif
            </div>
            <div class="modal-footer justify-content-center" style="display:block">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="text" name="id" id="id" value="{{$siswa->id}}" class="id" hidden/>
                <button type="submit" class="btn btn-success">Simpan</button>
            </div>
            </form>
        </div>
    </div>
</div>
{{-- @endif --}}


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