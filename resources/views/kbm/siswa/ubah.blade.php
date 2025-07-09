<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Ubah Data Siswa
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Ubah Data Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/siswa/{{ $siswa->is_lulus == 1 ? 'alumni' : 'aktif' }}">Siswa</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/siswa/lihat/{{ $siswa->id }}">{{ $siswa->id }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
        <div class="card-header py-3 bg-brand-green-dark d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Form Ubah Siswa</h6>
        </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="/kependidikan/kbm/siswa/ubah/{{$siswa->id}}"  method="POST">
                @method('PUT')
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
                                <label for="unit" class="form-control-label">Program <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                @if(in_array(Auth::user()->role->name,['aspv']))
                                @foreach($units as $unit)
                                @if($unit->id !== 5)
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="unitOpt{{ $unit->id }}" name="unit" class="custom-control-input" value="{{ $unit->id }}" required="required" {{ old('unit') == $unit->id ? 'checked' : '' }}{{ $unit->id == $siswa->unit_id ? 'checked' : '' }}>
                                <label class="custom-control-label" for="unitOpt{{ $unit->id }}">{{ ucwords($unit->name) }}</label>
                                </div>
                                @endif
                                @endforeach
                                @error('unit')
                                <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                                @else
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="unitOpt{{ $siswa->unit->id }}" name="unit" class="custom-control-input" value="{{ $siswa->unit->id }}" required="required" checked readonly>
                                <label class="custom-control-label" for="unitOpt{{ $siswa->unit->id }}">{{ $siswa->unit->name }}</label>
                                </div>
                                @endif
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
                            <label for="nik" class="col-sm-4 control-label">NIK<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nik"  placeholder="Nomor Induk Kependudukan" maxlength="16" value="{{ $siswa->identitas->nik }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nis" class="col-sm-4 control-label">NIPD<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nis" placeholder="Nomor Induk Siswa" value="{{ $siswa->student_nis }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">Nama Lengkap<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama" placeholder="Nama" value="{{ $siswa->identitas->student_name }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_pendek" class="col-sm-4 control-label">Nama Panggilan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_pendek" placeholder="Nama" value="{{ $siswa->identitas->student_nickname }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nisn" class="col-sm-4 control-label">NISN<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nisn" placeholder="NISN" value="{{ $siswa->student_nisn }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tempat_lahir" class="col-sm-4 control-label">Tempat Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="tempat_lahir" placeholder="Tempat Lahir" value="{{ $siswa->identitas->birth_place }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-sm-4 control-label">Tanggal Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" name="tanggal_lahir"  value="{{ $siswa->identitas->birth_date }}" >
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
                                <label for="jenis_kelamin" class="form-control-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                @foreach($jeniskelamin as $j)
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="genderOpt{{ $j->id }}" name="jenis_kelamin" class="custom-control-input" value="{{ $j->id }}" required="required" {{ old('gender') == $j->id ? 'checked' : '' }}{{ $j->id == $siswa->identitas->gender_id ? 'checked' : '' }}>
                                <label class="custom-control-label" for="genderOpt{{ $j->id }}">{{ ucwords($j->name) }}</label>
                                </div>
                                @endforeach
                                @error('gender')
                                <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Agama</label>
                            <div class="col-sm-6">
                                <select name="agama" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="">== Pilih Agama ==</option>
                                @foreach( $agamas as $agama )
                                @if( $agama->id == $siswa->identitas->religion_id )
                                    <option value="{{ $agama->id }}" selected>{{ $agama->name }}</option>
                                @endif
                                    <option value="{{ $agama->id }}">{{ $agama->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="anak_ke" class="col-sm-4 control-label">Anak ke-</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="anak_ke" placeholder="1" value="{{ $siswa->identitas->child_of }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Status Keluarga</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="status_anak" placeholder="1" value="{{ $siswa->identitas->family_status }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelompok" class="col-sm-4 control-label">Tanggal Masuk</label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" name="tanggal_masuk" placeholder="" value="{{ $siswa->join_date }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Semester Masuk</label>
                            <div class="col-sm-6">
                                <select name="semester_masuk" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Semester ==</option>
                                    @foreach( $semesters as $semester)
                                    @if( $semester->id == $siswa->semester_id)
                                        <option value="{{ $semester->id }}" selected>{{ $semester->semester_id }}</option>
                                    @else
                                        <option value="{{ $semester->id }}">{{ $semester->semester_id }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if($siswa->is_lulus != 1)
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas</label>
                            <div class="col-sm-6">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Kelas ==</option>
                                    @foreach( $levels as $level)
                                    @if($siswa->level_id==$level->id)
                                        <option value="{{ $level->id }}" selected>{{ $level->level }}</option>
                                    @else
                                        <option value="{{ $level->id }}">{{ $level->level }}</option>
                                    @endif
                                    @endforeach
                                </select>
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
                                <input type="text" class="form-control" name="alamat" placeholder="Alamat" value="{{ $siswa->identitas->address }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">No Rumah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="no_rumah" placeholder="001" value="{{ $siswa->identitas->address_number }}" >
                            </div>
                        </div>
                        <!-- Alamat RT dan RW -->
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">RT</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="rt" placeholder="RT" value="{{ $siswa->identitas->rt }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rw" class="col-sm-4 control-label">RW</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="rw" placeholder="RW" value="{{ $siswa->identitas->rw}}" >
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
                                <select name="provinsi" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="provinsi">== Pilih Provinsi ==</option>
                                @foreach( $listprovinsi as $list )
                                @if($provinsi == $list->name)
                                    <option value="{{ $list->code }}" selected>{{ $list->name }}</option>
                                @else
                                    <option value="{{ $list->code }}">{{ $list->name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kabupaten/Kota</label>
                            <div class="col-sm-6">
                                <select name="kabupaten" class="select2 form-control select2-hidden-accessible auto_width"  id="kabupaten" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="kabupaten">== Pilih Kabupaten ==</option>
                                @foreach( $listkabupaten as $list )
                                @if($kabupaten == $list->name)
                                    <option value="{{ $list->code }}" id="kabupaten" selected>{{ $list->name }}</option>
                                @else
                                    <option value="{{ $list->code }}" id="kabupaten">{{ $list->name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kecamatan</label>
                            <div class="col-sm-6">
                                <select name="kecamatan" class="select2 form-control select2-hidden-accessible auto_width"  id="kecamatan" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="kecamatan">== Pilih Kecamatan ==</option>
                                @foreach( $listkecamatan as $list )
                                @if($kecamatan == $list->name)
                                    <option value="{{ $list->code }}" id="kecamatan" selected>{{ $list->name }}</option>
                                @else
                                    <option value="{{ $list->code }}" id="kecamatan">{{ $list->name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Desa/Kelurahan</label>
                            <div class="col-sm-6">
                                <select name="desa" class="select2 form-control select2-hidden-accessible auto_width"  id="desa" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="desa">== Pilih Desa/Kelurahan ==</option>
                                @foreach( $listdesa as $list )
                                @if($desa == $list->name)
                                    <option value="{{ $list->code }}" id="desa" selected>{{ $list->name }}</option>
                                @else
                                    <option value="{{ $list->code }}" id="desa">{{ $list->name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Orang Tua</h6>
                            </div>
                        </div>
                        @php
                        $employeeId = $siswa && $siswa->identitas && $siswa->identitas->orangtua? $siswa->identitas->orangtua->employee_id : null;
                        @endphp
                        <div class="form-group row">
                            <label for="employeeOpt" class="col-sm-4 control-label">Civitas Auliya?</label>
                            <div class="col-sm-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                  <input type="radio" id="employeeOpt2" name="employeeOpt" class="custom-control-input" value="no" required="required" {{ !$employeeId ? 'checked' : null }}>
                                  <label class="custom-control-label" for="employeeOpt2">Tidak</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                  <input type="radio" id="employeeOpt1" name="employeeOpt" class="custom-control-input" value="yes" required="required" {{ $employeeId ? 'checked' : null }}>
                                  <label class="custom-control-label" for="employeeOpt1">Ya</label>
                                </div>
                            </div>
                        </div>
                        <div id="employeeRow" {!! !$employeeId ? 'style="display: none;"' : null !!}>
                            <div class="form-group row">
                                <label for="employee" class="col-sm-4 control-label">Nama Civitas</label>
                                <div class="col-sm-6">
                                    <select name="employee" class="select2 form-control select2-hidden-accessible auto_width" id="employee" style="width:100%;" tabindex="-1" aria-hidden="true" {!! !$employeeId ? 'disabled="disabled"' : null !!}>
                                        <option value="">== Pilih ==</option>
                                        @foreach($pegawais as $key => $pegawai)
                                        <option value="{{ $key }}" {{ $siswa && $siswa->identitas && $siswa->identitas->orangtua && $siswa->identitas->orangtua->employee_id == $key ? 'selected' : null }}>{{ $pegawai }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="nama_ayah" class="col-sm-4 control-label">Nama Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_ayah" placeholder="Nama Ayah" value="{{ $siswa->identitas->orangtua->father_name }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="nik_ayah" class="col-sm-4 control-label">NIK Ayah</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_ayah" placeholder="NIK Ayah" value="{{ $siswa->identitas->orangtua->father_nik }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="hp_ayah" class="col-sm-4 control-label">No HP Ayah</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_ayah" placeholder="No HP Ayah" value="{{ $siswa->identitas->orangtua->father_phone }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="email_ayah" class="col-sm-4 control-label">email Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_ayah" placeholder="Email Ayah" value="{{ $siswa->identitas->orangtua->father_email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ayah" class="col-sm-4 control-label">Pekerjaan Ayah</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if( $siswa->identitas->orangtua->father_job=="Pegawai Negeri")
                                            <option value="Pegawai Negeri" selected>Pegawai Negeri</option>
                                        @else
                                            <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->father_job=="Pegawai Swasta")
                                            <option value="Pegawai Swasta" selected>Pegawai Swasta</option>
                                        @else
                                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->father_job=="Wiraswasta")
                                            <option value="Wiraswasta" selected>Wiraswasta</option>
                                        @else
                                            <option value="Wiraswasta">Wiraswasta</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ayah" class="col-sm-4 control-label">Jabatan Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_ayah" placeholder="Jabatan Ayah" value="{{ $siswa->identitas->orangtua->father_position }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ayah" class="col-sm-4 control-label">Telp Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_ayah" placeholder="" value="{{ $siswa->identitas->orangtua->father_phone_office }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ayah" class="col-sm-4 control-label">Alamat Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_ayah" placeholder="" value="{{ $siswa->identitas->orangtua->father_job_address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ayah" class="col-sm-4 control-label">Gaji Ayah</label>
                            <div class="col-sm-6">
                                <select name="gaji_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih ==</option>
                                    @if( $siswa->identitas->orangtua->father_salary == "< Rp. 5.000.000" )
                                        <option value="&lt; Rp. 5.000.000" selected>&lt; Rp. 5.000.000</option>
                                    @else
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->father_salary == "Rp. 5.000.000 - Rp. 10.000.000" )
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000" selected>Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @else
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->father_salary == '> Rp. 10.000.000' )
                                        <option value="&gt; Rp. 10.000.000" selected>&gt; Rp. 10.000.000</option>
                                    @else
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="nama_ibu" class="col-sm-4 control-label">Nama Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_ibu" placeholder="Nama Ibu" value="{{ $siswa->identitas->orangtua->mother_name }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="nik_ibu" class="col-sm-4 control-label">NIK Ibu</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_ibu" placeholder="NIK Ibu" value="{{ $siswa->identitas->orangtua->mother_nik }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="hp_ibu" class="col-sm-4 control-label">No HP Ibu</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_ibu" placeholder="No HP Ibu" value="{{ $siswa->identitas->orangtua->mother_phone }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="email_ibu" class="col-sm-4 control-label">email Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_ibu" placeholder="Email Ibu" value="{{ $siswa->identitas->orangtua->mother_email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ibu" class="col-sm-4 control-label">Pekerjaan Ibu</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if( $siswa->identitas->orangtua->mother_job=="Pegawai Negeri")
                                            <option value="Pegawai Negeri" selected>Pegawai Negeri</option>
                                        @else
                                            <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->mother_job=="Pegawai Swasta")
                                            <option value="Pegawai Swasta" selected>Pegawai Swasta</option>
                                        @else
                                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->mother_job=="Wiraswasta")
                                            <option value="Wiraswasta" selected>Wiraswasta</option>
                                        @else
                                            <option value="Wiraswasta">Wiraswasta</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ibu" class="col-sm-4 control-label">Jabatan Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_ibu" placeholder="Jabatan Ibu" value="{{ $siswa->identitas->orangtua->mother_position }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ibu" class="col-sm-4 control-label">Telp Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_ibu" placeholder="" value="{{ $siswa->identitas->orangtua->mother_phone_office }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ibu" class="col-sm-4 control-label">Alamat Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_ibu" placeholder="" value="{{ $siswa->identitas->orangtua->mother_job_address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ibu" class="col-sm-4 control-label">Gaji Ibu</label>
                            <div class="col-sm-6">
                                <select name="gaji_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if( $siswa->identitas->orangtua->mother_salary == "< Rp. 5.000.000" )
                                        <option value="&lt; Rp. 5.000.000" selected>&lt; Rp. 5.000.000</option>
                                    @else
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->mother_salary == "Rp. 5.000.000 - Rp. 10.000.000" )
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000" selected>Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @else
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->mother_salary == '> Rp. 10.000.000' )
                                        <option value="&gt; Rp. 10.000.000" selected>&gt; Rp. 10.000.000</option>
                                    @else
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="alamat_ortu" class="col-sm-4 control-label">Alamat Orang Tua</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_ortu" placeholder="Alamat Orang Tua" value="{{ $siswa->identitas->orangtua->parent_address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_hp_ortu" class="col-sm-4 control-label">No HP Alternatif</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="no_hp_ortu" placeholder="08222*****" value="{{ $siswa->identitas->orangtua->parent_phone_number }}">
                            </div>
                        </div>                        
                        @endif
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Wali</h6>
                            </div>
                        </div>
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="nama_wali" class="col-sm-4 control-label">Nama Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_wali" placeholder="Nama Wali" value="{{ $siswa->identitas->orangtua->guardian_name }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="nik_wali" class="col-sm-4 control-label">NIK Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_wali" placeholder="NIK Wali" value="{{ $siswa->identitas->orangtua->guardian_nik }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="hp_wali" class="col-sm-4 control-label">No HP Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_wali" placeholder="No HP Wali" value="{{ $siswa->identitas->orangtua->guardian_phone_number }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="email_wali" class="col-sm-4 control-label">email Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_wali" placeholder="Email Wali" value="{{ $siswa->identitas->orangtua->guardian_email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_wali" class="col-sm-4 control-label">Pekerjaan Wali</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if( $siswa->identitas->orangtua->guardian_job=="Pegawai Negeri")
                                            <option value="Pegawai Negeri" selected>Pegawai Negeri</option>
                                        @else
                                            <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->guardian_job=="Pegawai Swasta")
                                            <option value="Pegawai Swasta" selected>Pegawai Swasta</option>
                                        @else
                                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->guardian_job=="Wiraswasta")
                                            <option value="Wiraswasta" selected>Wiraswasta</option>
                                        @else
                                            <option value="Wiraswasta">Pegawai Negeri</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_wali" class="col-sm-4 control-label">Jabatan Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_wali" placeholder="Jabatan Wali" value="{{ $siswa->identitas->orangtua->guardian_position }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_wali" class="col-sm-4 control-label">Telp Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_wali" placeholder="" value="{{ $siswa->identitas->orangtua->guardian_phone_office }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_wali" class="col-sm-4 control-label">Alamat Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_wali" placeholder="" value="{{ $siswa->identitas->orangtua->guardian_job_address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_wali" class="col-sm-4 control-label">Gaji Wali</label>
                            <div class="col-sm-6">
                                <select name="gaji_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if( $siswa->identitas->orangtua->guardian_salary == "< Rp. 5.000.000" )
                                        <option value="&lt; Rp. 5.000.000" selected>&lt; Rp. 5.000.000</option>
                                    @else
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->guardian_salary == "Rp. 5.000.000 - Rp. 10.000.000" )
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000" selected>Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @else
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->guardian_salary == '> Rp. 10.000.000' )
                                        <option value="&gt; Rp. 10.000.000" selected>&gt; Rp. 10.000.000</option>
                                    @else
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_wali" class="col-sm-4 control-label">Alamat Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_wali" placeholder="Alamat Wali" value="{{ $siswa->identitas->orangtua->guardian_address }}">
                            </div>
                        </div>
                        @endif
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Sekolah Asal</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Asal Sekolah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="asal_sekolah" placeholder="Asal Sekolah" value="{{ $siswa->origin_school }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_asal_sekolah" class="col-sm-4 control-label">Alamat Asal Sekolah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_asal_sekolah" placeholder="Alamat Asal Sekolah" value="{{ $siswa->origin_school_address }}">
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Saudara Kandung</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Nama Saudara Kandung</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="saudara_nama" placeholder="Nama" value="{{ $siswa->sibling_name?$siswa->sibling_name:'-' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas Saudara Kandung</label>
                            <div class="col-sm-6">
                                <select name="saudara_kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @foreach( $levels as $level)
                                    @if($siswa->sibling_level_id==$level->id)
                                        <option value="{{ $level->id }}" selected>{{ $level->level }}</option>
                                    @else
                                        <option value="{{ $level->id }}">{{ $level->level }}</option>
                                    @endif
                                    @endforeach
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
                                <select name="info_dari" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="{{ $siswa->info_from }}">{{ $siswa->info_from }}</option>
                                        <option value="">== Pilih ==</option>
                                        <option value="Orangtua">Orangtua</option>
                                        <option value="Guru/Staf">Guru/Staf</option>
                                        <option value="Brosur">Brosur</option>
                                        <option value="Spanduk">Spanduk</option>
                                        <option value="Media Cetak">Media Cetak</option>
                                        <option value="Sering Lewat">Sering Lewat</option>
                                        <option value="Teman">Teman</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="info_nama" class="col-sm-4 control-label">Nama</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="info_nama" placeholder="Nama" value="{{ $siswa->info_name }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="posisi" class="col-sm-4 control-label">Posisi / Kelas</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="posisi" placeholder="" value="{{ $siswa->position }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
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

<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>

<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
<script src="{{asset('js/wilayah.js')}}"></script>

<!-- Page level custom scripts -->
<script>
    $(document).ready(function () {
      $('input[name="employeeOpt"]').on('change',function(){
        var employeeOpt = $(this).val();
        if(employeeOpt == 'yes'){
          $('select[name="employee"]').prop("required", true).prop("disabled", false);
          $('#employeeRow').fadeIn('normal');
        }else{
          $('select[name="employee"]').prop("required", false).prop("disabled", true);
          $('#employeeRow').fadeOut('normal');
        }
      });
    });
</script>
=======
@extends('template.main.master')

@section('title')
Ubah Data Siswa
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Ubah Data Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/siswa/{{ $siswa->is_lulus == 1 ? 'alumni' : 'aktif' }}">Siswa</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/siswa/lihat/{{ $siswa->id }}">{{ $siswa->id }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
        <div class="card-header py-3 bg-brand-green-dark d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Form Ubah Siswa</h6>
        </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="/kependidikan/kbm/siswa/ubah/{{$siswa->id}}"  method="POST">
                @method('PUT')
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
                                <label for="unit" class="form-control-label">Program <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                @if(in_array(Auth::user()->role->name,['aspv']))
                                @foreach($units as $unit)
                                @if($unit->id !== 5)
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="unitOpt{{ $unit->id }}" name="unit" class="custom-control-input" value="{{ $unit->id }}" required="required" {{ old('unit') == $unit->id ? 'checked' : '' }}{{ $unit->id == $siswa->unit_id ? 'checked' : '' }}>
                                <label class="custom-control-label" for="unitOpt{{ $unit->id }}">{{ ucwords($unit->name) }}</label>
                                </div>
                                @endif
                                @endforeach
                                @error('unit')
                                <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                                @else
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="unitOpt{{ $siswa->unit->id }}" name="unit" class="custom-control-input" value="{{ $siswa->unit->id }}" required="required" checked readonly>
                                <label class="custom-control-label" for="unitOpt{{ $siswa->unit->id }}">{{ $siswa->unit->name }}</label>
                                </div>
                                @endif
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
                            <label for="nik" class="col-sm-4 control-label">NIK<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nik"  placeholder="Nomor Induk Kependudukan" maxlength="16" value="{{ $siswa->identitas->nik }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nis" class="col-sm-4 control-label">NIPD<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nis" placeholder="Nomor Induk Siswa" value="{{ $siswa->student_nis }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">Nama Lengkap<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama" placeholder="Nama" value="{{ $siswa->identitas->student_name }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_pendek" class="col-sm-4 control-label">Nama Panggilan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_pendek" placeholder="Nama" value="{{ $siswa->identitas->student_nickname }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nisn" class="col-sm-4 control-label">NISN<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nisn" placeholder="NISN" value="{{ $siswa->student_nisn }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tempat_lahir" class="col-sm-4 control-label">Tempat Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="tempat_lahir" placeholder="Tempat Lahir" value="{{ $siswa->identitas->birth_place }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-sm-4 control-label">Tanggal Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" name="tanggal_lahir"  value="{{ $siswa->identitas->birth_date }}" >
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
                                <label for="jenis_kelamin" class="form-control-label">Jenis Kelamin <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-sm-6">
                                @foreach($jeniskelamin as $j)
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="genderOpt{{ $j->id }}" name="jenis_kelamin" class="custom-control-input" value="{{ $j->id }}" required="required" {{ old('gender') == $j->id ? 'checked' : '' }}{{ $j->id == $siswa->identitas->gender_id ? 'checked' : '' }}>
                                <label class="custom-control-label" for="genderOpt{{ $j->id }}">{{ ucwords($j->name) }}</label>
                                </div>
                                @endforeach
                                @error('gender')
                                <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Agama</label>
                            <div class="col-sm-6">
                                <select name="agama" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="">== Pilih Agama ==</option>
                                @foreach( $agamas as $agama )
                                @if( $agama->id == $siswa->identitas->religion_id )
                                    <option value="{{ $agama->id }}" selected>{{ $agama->name }}</option>
                                @endif
                                    <option value="{{ $agama->id }}">{{ $agama->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="anak_ke" class="col-sm-4 control-label">Anak ke-</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="anak_ke" placeholder="1" value="{{ $siswa->identitas->child_of }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Status Keluarga</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="status_anak" placeholder="1" value="{{ $siswa->identitas->family_status }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelompok" class="col-sm-4 control-label">Tanggal Masuk</label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" name="tanggal_masuk" placeholder="" value="{{ $siswa->join_date }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Semester Masuk</label>
                            <div class="col-sm-6">
                                <select name="semester_masuk" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Semester ==</option>
                                    @foreach( $semesters as $semester)
                                    @if( $semester->id == $siswa->semester_id)
                                        <option value="{{ $semester->id }}" selected>{{ $semester->semester_id }}</option>
                                    @else
                                        <option value="{{ $semester->id }}">{{ $semester->semester_id }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if($siswa->is_lulus != 1)
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas</label>
                            <div class="col-sm-6">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Kelas ==</option>
                                    @foreach( $levels as $level)
                                    @if($siswa->level_id==$level->id)
                                        <option value="{{ $level->id }}" selected>{{ $level->level }}</option>
                                    @else
                                        <option value="{{ $level->id }}">{{ $level->level }}</option>
                                    @endif
                                    @endforeach
                                </select>
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
                                <input type="text" class="form-control" name="alamat" placeholder="Alamat" value="{{ $siswa->identitas->address }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">No Rumah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="no_rumah" placeholder="001" value="{{ $siswa->identitas->address_number }}" >
                            </div>
                        </div>
                        <!-- Alamat RT dan RW -->
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">RT</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="rt" placeholder="RT" value="{{ $siswa->identitas->rt }}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rw" class="col-sm-4 control-label">RW</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="rw" placeholder="RW" value="{{ $siswa->identitas->rw}}" >
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
                                <select name="provinsi" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="provinsi">== Pilih Provinsi ==</option>
                                @foreach( $listprovinsi as $list )
                                @if($provinsi == $list->name)
                                    <option value="{{ $list->code }}" selected>{{ $list->name }}</option>
                                @else
                                    <option value="{{ $list->code }}">{{ $list->name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kabupaten/Kota</label>
                            <div class="col-sm-6">
                                <select name="kabupaten" class="select2 form-control select2-hidden-accessible auto_width"  id="kabupaten" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="kabupaten">== Pilih Kabupaten ==</option>
                                @foreach( $listkabupaten as $list )
                                @if($kabupaten == $list->name)
                                    <option value="{{ $list->code }}" id="kabupaten" selected>{{ $list->name }}</option>
                                @else
                                    <option value="{{ $list->code }}" id="kabupaten">{{ $list->name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kecamatan</label>
                            <div class="col-sm-6">
                                <select name="kecamatan" class="select2 form-control select2-hidden-accessible auto_width"  id="kecamatan" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="kecamatan">== Pilih Kecamatan ==</option>
                                @foreach( $listkecamatan as $list )
                                @if($kecamatan == $list->name)
                                    <option value="{{ $list->code }}" id="kecamatan" selected>{{ $list->name }}</option>
                                @else
                                    <option value="{{ $list->code }}" id="kecamatan">{{ $list->name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Desa/Kelurahan</label>
                            <div class="col-sm-6">
                                <select name="desa" class="select2 form-control select2-hidden-accessible auto_width"  id="desa" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="" id="desa">== Pilih Desa/Kelurahan ==</option>
                                @foreach( $listdesa as $list )
                                @if($desa == $list->name)
                                    <option value="{{ $list->code }}" id="desa" selected>{{ $list->name }}</option>
                                @else
                                    <option value="{{ $list->code }}" id="desa">{{ $list->name }}</option>
                                @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Orang Tua</h6>
                            </div>
                        </div>
                        @php
                        $employeeId = $siswa && $siswa->identitas && $siswa->identitas->orangtua? $siswa->identitas->orangtua->employee_id : null;
                        @endphp
                        <div class="form-group row">
                            <label for="employeeOpt" class="col-sm-4 control-label">Civitas Auliya?</label>
                            <div class="col-sm-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                  <input type="radio" id="employeeOpt2" name="employeeOpt" class="custom-control-input" value="no" required="required" {{ !$employeeId ? 'checked' : null }}>
                                  <label class="custom-control-label" for="employeeOpt2">Tidak</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                  <input type="radio" id="employeeOpt1" name="employeeOpt" class="custom-control-input" value="yes" required="required" {{ $employeeId ? 'checked' : null }}>
                                  <label class="custom-control-label" for="employeeOpt1">Ya</label>
                                </div>
                            </div>
                        </div>
                        <div id="employeeRow" {!! !$employeeId ? 'style="display: none;"' : null !!}>
                            <div class="form-group row">
                                <label for="employee" class="col-sm-4 control-label">Nama Civitas</label>
                                <div class="col-sm-6">
                                    <select name="employee" class="select2 form-control select2-hidden-accessible auto_width" id="employee" style="width:100%;" tabindex="-1" aria-hidden="true" {!! !$employeeId ? 'disabled="disabled"' : null !!}>
                                        <option value="">== Pilih ==</option>
                                        @foreach($pegawais as $key => $pegawai)
                                        <option value="{{ $key }}" {{ $siswa && $siswa->identitas && $siswa->identitas->orangtua && $siswa->identitas->orangtua->employee_id == $key ? 'selected' : null }}>{{ $pegawai }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="nama_ayah" class="col-sm-4 control-label">Nama Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_ayah" placeholder="Nama Ayah" value="{{ $siswa->identitas->orangtua->father_name }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="nik_ayah" class="col-sm-4 control-label">NIK Ayah</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_ayah" placeholder="NIK Ayah" value="{{ $siswa->identitas->orangtua->father_nik }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="hp_ayah" class="col-sm-4 control-label">No HP Ayah</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_ayah" placeholder="No HP Ayah" value="{{ $siswa->identitas->orangtua->father_phone }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="email_ayah" class="col-sm-4 control-label">email Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_ayah" placeholder="Email Ayah" value="{{ $siswa->identitas->orangtua->father_email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ayah" class="col-sm-4 control-label">Pekerjaan Ayah</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if( $siswa->identitas->orangtua->father_job=="Pegawai Negeri")
                                            <option value="Pegawai Negeri" selected>Pegawai Negeri</option>
                                        @else
                                            <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->father_job=="Pegawai Swasta")
                                            <option value="Pegawai Swasta" selected>Pegawai Swasta</option>
                                        @else
                                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->father_job=="Wiraswasta")
                                            <option value="Wiraswasta" selected>Wiraswasta</option>
                                        @else
                                            <option value="Wiraswasta">Wiraswasta</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ayah" class="col-sm-4 control-label">Jabatan Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_ayah" placeholder="Jabatan Ayah" value="{{ $siswa->identitas->orangtua->father_position }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ayah" class="col-sm-4 control-label">Telp Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_ayah" placeholder="" value="{{ $siswa->identitas->orangtua->father_phone_office }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ayah" class="col-sm-4 control-label">Alamat Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_ayah" placeholder="" value="{{ $siswa->identitas->orangtua->father_job_address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ayah" class="col-sm-4 control-label">Gaji Ayah</label>
                            <div class="col-sm-6">
                                <select name="gaji_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih ==</option>
                                    @if( $siswa->identitas->orangtua->father_salary == "< Rp. 5.000.000" )
                                        <option value="&lt; Rp. 5.000.000" selected>&lt; Rp. 5.000.000</option>
                                    @else
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->father_salary == "Rp. 5.000.000 - Rp. 10.000.000" )
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000" selected>Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @else
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->father_salary == '> Rp. 10.000.000' )
                                        <option value="&gt; Rp. 10.000.000" selected>&gt; Rp. 10.000.000</option>
                                    @else
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="nama_ibu" class="col-sm-4 control-label">Nama Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_ibu" placeholder="Nama Ibu" value="{{ $siswa->identitas->orangtua->mother_name }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="nik_ibu" class="col-sm-4 control-label">NIK Ibu</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_ibu" placeholder="NIK Ibu" value="{{ $siswa->identitas->orangtua->mother_nik }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="hp_ibu" class="col-sm-4 control-label">No HP Ibu</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_ibu" placeholder="No HP Ibu" value="{{ $siswa->identitas->orangtua->mother_phone }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="email_ibu" class="col-sm-4 control-label">email Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_ibu" placeholder="Email Ibu" value="{{ $siswa->identitas->orangtua->mother_email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ibu" class="col-sm-4 control-label">Pekerjaan Ibu</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if( $siswa->identitas->orangtua->mother_job=="Pegawai Negeri")
                                            <option value="Pegawai Negeri" selected>Pegawai Negeri</option>
                                        @else
                                            <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->mother_job=="Pegawai Swasta")
                                            <option value="Pegawai Swasta" selected>Pegawai Swasta</option>
                                        @else
                                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->mother_job=="Wiraswasta")
                                            <option value="Wiraswasta" selected>Wiraswasta</option>
                                        @else
                                            <option value="Wiraswasta">Wiraswasta</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ibu" class="col-sm-4 control-label">Jabatan Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_ibu" placeholder="Jabatan Ibu" value="{{ $siswa->identitas->orangtua->mother_position }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ibu" class="col-sm-4 control-label">Telp Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_ibu" placeholder="" value="{{ $siswa->identitas->orangtua->mother_phone_office }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ibu" class="col-sm-4 control-label">Alamat Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_ibu" placeholder="" value="{{ $siswa->identitas->orangtua->mother_job_address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ibu" class="col-sm-4 control-label">Gaji Ibu</label>
                            <div class="col-sm-6">
                                <select name="gaji_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if( $siswa->identitas->orangtua->mother_salary == "< Rp. 5.000.000" )
                                        <option value="&lt; Rp. 5.000.000" selected>&lt; Rp. 5.000.000</option>
                                    @else
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->mother_salary == "Rp. 5.000.000 - Rp. 10.000.000" )
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000" selected>Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @else
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->mother_salary == '> Rp. 10.000.000' )
                                        <option value="&gt; Rp. 10.000.000" selected>&gt; Rp. 10.000.000</option>
                                    @else
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="alamat_ortu" class="col-sm-4 control-label">Alamat Orang Tua</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_ortu" placeholder="Alamat Orang Tua" value="{{ $siswa->identitas->orangtua->parent_address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_hp_ortu" class="col-sm-4 control-label">No HP Alternatif</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="no_hp_ortu" placeholder="08222*****" value="{{ $siswa->identitas->orangtua->parent_phone_number }}">
                            </div>
                        </div>                        
                        @endif
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Wali</h6>
                            </div>
                        </div>
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="nama_wali" class="col-sm-4 control-label">Nama Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_wali" placeholder="Nama Wali" value="{{ $siswa->identitas->orangtua->guardian_name }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="nik_wali" class="col-sm-4 control-label">NIK Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="nik_wali" placeholder="NIK Wali" value="{{ $siswa->identitas->orangtua->guardian_nik }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,7,30,31)))
                        <div class="form-group row">
                            <label for="hp_wali" class="col-sm-4 control-label">No HP Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="hp_wali" placeholder="No HP Wali" value="{{ $siswa->identitas->orangtua->guardian_phone_number }}">
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,30,31)))
                        <div class="form-group row">
                            <label for="email_wali" class="col-sm-4 control-label">email Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="email_wali" placeholder="Email Wali" value="{{ $siswa->identitas->orangtua->guardian_email }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_wali" class="col-sm-4 control-label">Pekerjaan Wali</label>
                            <div class="col-sm-6">
                                <select name="pekerjaan_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if( $siswa->identitas->orangtua->guardian_job=="Pegawai Negeri")
                                            <option value="Pegawai Negeri" selected>Pegawai Negeri</option>
                                        @else
                                            <option value="Pegawai Negeri">Pegawai Negeri</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->guardian_job=="Pegawai Swasta")
                                            <option value="Pegawai Swasta" selected>Pegawai Swasta</option>
                                        @else
                                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                                        @endif
                                        @if( $siswa->identitas->orangtua->guardian_job=="Wiraswasta")
                                            <option value="Wiraswasta" selected>Wiraswasta</option>
                                        @else
                                            <option value="Wiraswasta">Pegawai Negeri</option>
                                        @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_wali" class="col-sm-4 control-label">Jabatan Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="jabatan_wali" placeholder="Jabatan Wali" value="{{ $siswa->identitas->orangtua->guardian_position }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_wali" class="col-sm-4 control-label">Telp Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="telp_kantor_wali" placeholder="" value="{{ $siswa->identitas->orangtua->guardian_phone_office }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_wali" class="col-sm-4 control-label">Alamat Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_kantor_wali" placeholder="" value="{{ $siswa->identitas->orangtua->guardian_job_address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_wali" class="col-sm-4 control-label">Gaji Wali</label>
                            <div class="col-sm-6">
                                <select name="gaji_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if( $siswa->identitas->orangtua->guardian_salary == "< Rp. 5.000.000" )
                                        <option value="&lt; Rp. 5.000.000" selected>&lt; Rp. 5.000.000</option>
                                    @else
                                        <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->guardian_salary == "Rp. 5.000.000 - Rp. 10.000.000" )
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000" selected>Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @else
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    @endif
                                    @if( $siswa->identitas->orangtua->guardian_salary == '> Rp. 10.000.000' )
                                        <option value="&gt; Rp. 10.000.000" selected>&gt; Rp. 10.000.000</option>
                                    @else
                                        <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_wali" class="col-sm-4 control-label">Alamat Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_wali" placeholder="Alamat Wali" value="{{ $siswa->identitas->orangtua->guardian_address }}">
                            </div>
                        </div>
                        @endif
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Sekolah Asal</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Asal Sekolah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="asal_sekolah" placeholder="Asal Sekolah" value="{{ $siswa->origin_school }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_asal_sekolah" class="col-sm-4 control-label">Alamat Asal Sekolah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="alamat_asal_sekolah" placeholder="Alamat Asal Sekolah" value="{{ $siswa->origin_school_address }}">
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Saudara Kandung</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Nama Saudara Kandung</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="saudara_nama" placeholder="Nama" value="{{ $siswa->sibling_name?$siswa->sibling_name:'-' }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas Saudara Kandung</label>
                            <div class="col-sm-6">
                                <select name="saudara_kelas" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @foreach( $levels as $level)
                                    @if($siswa->sibling_level_id==$level->id)
                                        <option value="{{ $level->id }}" selected>{{ $level->level }}</option>
                                    @else
                                        <option value="{{ $level->id }}">{{ $level->level }}</option>
                                    @endif
                                    @endforeach
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
                                <select name="info_dari" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="{{ $siswa->info_from }}">{{ $siswa->info_from }}</option>
                                        <option value="">== Pilih ==</option>
                                        <option value="Orangtua">Orangtua</option>
                                        <option value="Guru/Staf">Guru/Staf</option>
                                        <option value="Brosur">Brosur</option>
                                        <option value="Spanduk">Spanduk</option>
                                        <option value="Media Cetak">Media Cetak</option>
                                        <option value="Sering Lewat">Sering Lewat</option>
                                        <option value="Teman">Teman</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="info_nama" class="col-sm-4 control-label">Nama</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="info_nama" placeholder="Nama" value="{{ $siswa->info_name }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="posisi" class="col-sm-4 control-label">Posisi / Kelas</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="posisi" placeholder="" value="{{ $siswa->position }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
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

<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>

<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
<script src="{{asset('js/wilayah.js')}}"></script>

<!-- Page level custom scripts -->
<script>
    $(document).ready(function () {
      $('input[name="employeeOpt"]').on('change',function(){
        var employeeOpt = $(this).val();
        if(employeeOpt == 'yes'){
          $('select[name="employee"]').prop("required", true).prop("disabled", false);
          $('#employeeRow').fadeIn('normal');
        }else{
          $('select[name="employee"]').prop("required", false).prop("disabled", true);
          $('#employeeRow').fadeOut('normal');
        }
      });
    });
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection