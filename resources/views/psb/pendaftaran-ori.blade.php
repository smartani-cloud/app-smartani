@extends('template.login.master')

@section('title')
Pendaftaran @endsection

@section('headmeta')
<!-- Main styles required by this view -->
<link href="{{ asset('css/utilpsb.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/loginpsb.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
        <div class="wrap-menu100 p-l-30 p-r-30 p-t-30 p-b-30">
            <form class="login100-form validate-form" action="/psb/daftar" method="post">
                @csrf
                <input class="input1005" type="text" name="unit_id" value="{{$unit_id}}" style="display:none">
                <div class="login100-logo m-b-5">
                    <img src="{{ asset('img/logo/logomark.png') }}">
                </div>
                <span class="login100-form-title">{{$unit_name}} IT AULIYA</span>
                <span class="login100-form-subtitle p-b-27">Form Pendaftaran Siswa Baru</span>

                @if(Session::has('success'))
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ Session::get('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  @endif
                  @if(Session::has('danger'))
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ Session::get('danger') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  @endif



                  <!-- Page 1 -->
                <div id="page1" style="display:block">
                
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Tahun Pelajaran</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="academic_year_id" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true" required>
                                <option class="input1005" value="" id="">== Pilih Tahun Pelajaran ==</option>
                            @foreach( $tahunajarans as $tahunajaran )
                                <option class="input1005" value="{{ $tahunajaran->id }}">{{ $tahunajaran->academic_year }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Kelas</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="level_id" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true" required>
                                <option class="input1005" value="" id="">== Pilih Kelas ==</option>
                            @foreach( $kelases as $kelas )
                                <option class="input1005" value="{{ $kelas->id }}">{{ $kelas->level }}</option>
                            @endforeach
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Nama Lengkap</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukan Nama Lengkap">
                            <input class="input1005" type="text" name="name" placeholder="Nama Lengkap" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Nama Panggilan</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Nama Panggilan">
                            <input class="input1005" type="text" name="nickname" placeholder="Nama Panggilan" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Tempat Lahir</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan Tempat Lahir">
                            <input class="input1005" type="text" name="born_place" placeholder="Tempat Lahir" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Tanggal Lahir</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan Tanggal Lahir">
                            <input class="input1005" type="date" name="born_date" placeholder="Tanggal Lahir" max="2021-05-20" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Jenis Kelamin</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="gender_id" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true" required>
                                <option class="input1005" value="" id="">== Pilih Jenis Kelamin ==</option>
                            @foreach( $jeniskelamin as $jk )
                                <option class="input1005" value="{{ $jk->id }}">{{ $jk->name }}</option>
                            @endforeach
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Agama</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="religion_id" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true" required>
                                <option class="input1005" value="" id="">== Pilih Agama ==</option>
                            @foreach( $agamas as $agama )
                                <option class="input1005" value="{{ $agama->id }}">{{ $agama->name }}</option>
                            @endforeach
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="container-login100-form-btn">
                        <button type="button" class="login100-form-btn"  onclick="show2()">
                            Selanjutnya
                        </button>
                    </div>
                </div>
                
                <!-- Page 2 -->

                <div id="page2" style="display:none">
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Alamat Rumah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="text" name="address" placeholder="Alamat Rumah" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Nomor Rumah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="address_number" placeholder="Nomor Rumah" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">RT</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="rt" placeholder="RT" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">RW</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="rw" placeholder="RW" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Provinsi</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="provinsi" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true" required>
                                <option value="" id="kecamatan">== Pilih Provinsi ==</option>
                            @foreach( $provinsis as $provinsi )
                                <option value="{{ $provinsi->code }}">{{ $provinsi->name }}</option>
                            @endforeach
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Kota / Kabupaten</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="kabupaten" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true" disabled required>
                                <option value="" id="kabupaten">== Pilih Kabupaten/Kota ==</option>
                            </select>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Kecamatan</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="kecamatan" class="input1005" id="kelas" style="width:100%; border-style:none"  tabindex="-1" aria-hidden="true" disabled required>
                                <option value="" id="kecamatan">== Pilih Kecamatan ==</option>
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Kelurahan / Desa</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                                <select name="desa" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true" disabled required>
                                    <option value="" id="desa">== Pilih Desa/Kelurahan ==</option>
                                </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="container-login100-form-btn d-flex justify-content-between">
                        <button type="button" class="login100-form-btn-2" onclick="show1()">
                            Sebelumnya
                        </button>
                        <button type="button" class="login100-form-btn" onclick="show3()">
                            Selanjutnya
                        </button>
                    </div>
                </div>

                <!-- Page 3 -->

                

                <div id="page3" style="display:none">

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Nama Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="text" name="father_name" placeholder="Nama Ayah">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">No KTP Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="father_ktp" placeholder="No KTP Ayah">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">No HP Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="father_phone" placeholder="No HP Ayah">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Email Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="email" name="father_email" placeholder="Email Ayah">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Pekerjaan Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="father_job" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true">
                                <option value="">== Pilih Pekerjaan ==</option>
                                <option value="Pegawai Negeri">Pegawai Negeri</option>
                                <option value="Pegawai Swasta">Pegawai Swasta</option>
                                <option value="Wiraswasta">Wiraswasta</option>
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Jabatan Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="text" name="father_position" placeholder="Jabatan Ayah">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Telp Kantor Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="father_job_phone" placeholder="Telp Kantor Ayah">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Alamat Kantor Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="text" name="father_job_address" placeholder="Alamat Kantor Ayah">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Gaji Ayah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                                <select name="father_salary" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih ==</option>
                                    <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="container-login100-form-btn d-flex justify-content-between">
                        <button type="button" class="login100-form-btn-2" onclick="show2()">
                            Sebelumnya
                        </button>
                        <button type="button" class="login100-form-btn" onclick="show4()">
                            Selanjutnya
                        </button>
                    </div>
                </div>


                <!-- Page 4 -->

                <div id="page4" style="display:none">

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Nama Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="text" name="mother_name" placeholder="Nama Ibu">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">No KTP Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="mother_ktp" placeholder="No KTP Ibu">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">No HP Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="mother_phone" placeholder="No HP Ibu">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Email Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="email" name="mother_email" placeholder="Email Ibu">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Pekerjaan Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="mother_job" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true">
                                <option value="">== Pilih Pekerjaan ==</option>
                                <option value="Pegawai Negeri">Pegawai Negeri</option>
                                <option value="Pegawai Swasta">Pegawai Swasta</option>
                                <option value="Wiraswasta">Wiraswasta</option>
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Jabatan Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="text" name="mother_position" placeholder="Jabatan Ibu">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Telp Kantor Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="number" name="mother_job_phone" placeholder="Telp Kantor Ibu">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Alamat Kantor Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="text" name="mother_job_address" placeholder="Alamat Kantor Ibu">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Gaji Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                                <select name="mother_salary" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih ==</option>
                                    <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                                </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="container-login100-form-btn d-flex justify-content-between">
                        <button type="button" class="login100-form-btn-2" onclick="show3()">
                            Sebelumnya
                        </button>
                        <button  type="button" class="login100-form-btn" onclick="show5()">
                            Selanjutnya
                        </button>
                    </div>
                </div>
                <!-- Page 5 -->

                <div id="page5" style="display:none">

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Asal Sekolah</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select name="asal_sekolah" class="input1005" id="kelas" style="width:100%; border-style:none" tabindex="-1" aria-hidden="true" required>
                                <option class="input1005" value="SIT Auliya" id="sit_auliya">SIT Auliya</option>
                                <option class="input1005" value="Sekolah Lain" id="sekolah_lain">Sekolah Lain</option>
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div style="display: none;" id="alamat_sekolah_lain">
                        <div class="row align-items-center">
                            <label class="col-md-3 col-xs-4">Nama Sekolah</label>
                            <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                                <input class="input1005" type="text" name="alamat_asal_sekolah" placeholder="Nama Sekolah Lain">
                                <span class="focus-input1005"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Pegawai Auliya?</label>
                        <div class="col-md-9 col-xs-6 m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input type="checkbox" id="myCheck" onclick="checkBox()">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div id="InputPegawai" style="display: none;">
                        <div class="row align-items-center">
                            <label class="col-md-3 col-xs-4">Nomor NIP</label>
                            <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                                <input class="input1005" type="text" name="nip" placeholder="NIP Orang Tua">
                                <span class="focus-input1005"></span>
                            </div>
                        </div>
                    </div>

                    <div class="container-login100-form-btn d-flex justify-content-between">
                        <button type="button" class="login100-form-btn-2" onclick="show4()">
                            Sebelumnya
                        </button>
                        <button type="button" class="login100-form-btn" onclick="show6()">
                            Selanjutnya
                        </button>
                    </div>
                </div>


                <!-- Page 6 -->

                <div id="page6" style="display:none">

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Username</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="text" name="username" placeholder="Username" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Password</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005" type="password" name="password" placeholder="Password" required>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="container-login100-form-btn d-flex justify-content-between">
                        <button type="button" class="login100-form-btn-2" onclick="show5()">
                            Sebelumnya
                        </button>
                        <button class="login100-form-btn" onclick="showmodals()">
                            Daftar
                        </button>
                    </div>
                </div>

            </form>
        </div>
<!--Row-->
@endsection
@section('footjs')

<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>

<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
<script src="{{asset('js/wilayah.js')}}"></script>
<script>
    $(function(){
        var dtToday = new Date();

        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();

        if(month < 10)
            month = '0' + month.toString();
        if(day < 10)
            day = '0' + day.toString();

        var maxDate = year + '-' + month + '-' + day;    
        $('input[name="born_date"]').attr('max', maxDate);
    });
</script>

@include('template.footjs.psb.pendaftaran')
@endsection