@extends('template.main.psb.master')

@section('title')
Profil Saya
@endsection

@section('sidebar')
@include('template.sidebar.ortu.ortu')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Profil</h1>
    {{-- <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
    </ol> --}}
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
        <div class="card-header py-3 bg-brand-green-dark d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Profil</h6>
        </div>
            <div class="card-body">
                <form action="{{route('psb.profil.update')}}"  method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>{{ Session::get('success') }}!</strong> 
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Orang Tua</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ayah" class="col-sm-4 control-label">Nama Ayah<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="father_name" value="{{auth()->user()->orangtua->father_name}}" placeholder="Nama Ayah">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_ayah" class="col-sm-4 control-label">NIK Ayah<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="father_nik"  value="{{auth()->user()->orangtua->father_nik}}" placeholder="NIK Ayah">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_ayah" class="col-sm-4 control-label">No HP Ayah<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="father_phone_number" value="{{auth()->user()->orangtua->father_phone}}" placeholder="No HP Ayah">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_ayah" class="col-sm-4 control-label">email Ayah<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="father_email" value="{{auth()->user()->orangtua->father_email}}" placeholder="Email Ayah">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ayah" class="col-sm-4 control-label">Pekerjaan Ayah</label>
                            <div class="col-sm-6">
                                <select name="father_job" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Pekerjaan ==</option>father_job
                                        <option value="Pegawai Negeri" {{auth()->user()->orangtua->father_job=="Pegawai Negeri"?"selected":""}}>Pegawai Negeri</option>
                                        <option value="Pegawai Swasta" {{auth()->user()->orangtua->father_job=="Pegawai Swasta"?"selected":""}}>Pegawai Swasta</option>
                                        <option value="Wiraswasta" {{auth()->user()->orangtua->father_job=="Wiraswasta"?"selected":""}}>Wiraswasta</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ayah" class="col-sm-4 control-label">Jabatan Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="father_position" value="{{auth()->user()->orangtua->father_position}}" placeholder="Jabatan Ayah">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ayah" class="col-sm-4 control-label">Telp Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="father_phone_office" value="{{auth()->user()->orangtua->father_phone_office}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ayah" class="col-sm-4 control-label">Alamat Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="father_job_address" value="{{auth()->user()->orangtua->father_job_address}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="father_salary" class="col-sm-4 control-label">Gaji Ayah</label>
                            <div class="col-sm-6">
                                <select name="gaji_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih ==</option>
                                    <option value="&lt; Rp. 5.000.000" {{auth()->user()->orangtua->father_salary=="< Rp. 5.000.000"?"selected":""}}>&lt; Rp. 5.000.000</option>
                                    <option value="Rp. 5.000.000 - Rp. 10.000.000" {{auth()->user()->orangtua->father_salary=="Rp. 5.000.000 - Rp. 10.000.000"?"selected":""}}>Rp. 5.000.000 - Rp. 10.000.000</option>
                                    <option value="&gt; Rp. 10.000.000" {{auth()->user()->orangtua->father_salary=="> Rp. 10.000.000"?"selected":""}}>&gt; Rp. 10.000.000</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ibu" class="col-sm-4 control-label">Nama Ibu<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="mother_name" value="{{auth()->user()->orangtua->mother_name}}" placeholder="Nama Ibu">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_ibu" class="col-sm-4 control-label">NIK Ibu<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="mother_nik" value="{{auth()->user()->orangtua->mother_nik}}" placeholder="NIK Ibu">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_ibu" class="col-sm-4 control-label">No HP Ibu<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="mother_phone_number" value="{{auth()->user()->orangtua->mother_phone}}" placeholder="No HP Ibu">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_ibu" class="col-sm-4 control-label">email Ibu<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="mother_email" value="{{auth()->user()->orangtua->mother_email}}" placeholder="Email Ibu">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ibu" class="col-sm-4 control-label">Pekerjaan Ibu</label>
                            <div class="col-sm-6">
                                <select name="mother_job" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih Pekerjaan ==</option>
                                        <option value="Ibu Rumah Tangga" {{auth()->user()->orangtua->mother_job=="Ibu Rumah Tangga"?"selected":""}}>Ibu Rumah Tangga</option>
                                        <option value="Pegawai Negeri" {{auth()->user()->orangtua->mother_job=="Pegawai Negeri"?"selected":""}}>Pegawai Negeri</option>
                                        <option value="Pegawai Swasta" {{auth()->user()->orangtua->mother_job=="Pegawai Swasta"?"selected":""}}>Pegawai Swasta</option>
                                        <option value="Wiraswasta" {{auth()->user()->orangtua->mother_job=="Wiraswasta"?"selected":""}}>Wiraswasta</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ibu" class="col-sm-4 control-label">Jabatan Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="mother_position" value="{{auth()->user()->orangtua->mother_position}}" placeholder="Jabatan Ibu">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ibu" class="col-sm-4 control-label">Telp Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="mother_phone_office" value="{{auth()->user()->orangtua->mother_phone_office}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ibu" class="col-sm-4 control-label">Alamat Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="mother_job_address" value="{{auth()->user()->orangtua->mother_job_address}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ibu" class="col-sm-4 control-label">Gaji Ibu</label>
                            <div class="col-sm-6">
                                <select name="mother_salary" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">== Pilih ==</option>
                                        <option value="&lt; Rp. 5.000.000" {{auth()->user()->orangtua->mother_salary=="< Rp. 5.000.000"?"selected":""}}>&lt; Rp. 5.000.000</option>
                                        <option value="Rp. 5.000.000 - Rp. 10.000.000" {{auth()->user()->orangtua->mother_salary=="Rp. 5.000.000 - Rp. 10.000.000"?"selected":""}}>Rp. 5.000.000 - Rp. 10.000.000</option>
                                        <option value="&gt; Rp. 10.000.000" {{auth()->user()->orangtua->mother_salary=="> Rp. 10.000.000"?"selected":""}}>&gt; Rp. 10.000.000</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_ortu" class="col-sm-4 control-label">Alamat Orang Tua</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="parent_address" value="{{auth()->user()->orangtua->parent_address}}" placeholder="Alamat Orang Tua">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_hp_ortu" class="col-sm-4 control-label">No HP Alternatif</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="parent_phone_number" value="{{auth()->user()->orangtua->parent_phone_number}}" placeholder="08222*****">
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
                                <input type="text" class="form-control" name="guardian_name" value="{{auth()->user()->orangtua->guardian_name}}" placeholder="Nama Wali">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_wali" class="col-sm-4 control-label">NIK Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guardian_nik" value="{{auth()->user()->orangtua->guardian_nik}}" placeholder="NIK Wali">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_wali" class="col-sm-4 control-label">No HP Wali</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="guardian_phone_number" value="{{auth()->user()->orangtua->guardian_phone_number}}" placeholder="No HP Wali">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_wali" class="col-sm-4 control-label">email Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="guardian_email" value="{{auth()->user()->orangtua->guardian_email}}" placeholder="Email Wali">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_wali" class="col-sm-4 control-label">Pekerjaan Wali</label>
                            <div class="col-sm-6">
                                <select name="guardian_job" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
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
                                <input type="text" class="form-control" name="guardian_position" value="{{auth()->user()->orangtua->guardian_position}}" placeholder="Jabatan Wali">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_wali" class="col-sm-4 control-label">Telp Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="guardian_phone_office" value="{{auth()->user()->orangtua->guardian_phone_office}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_wali" class="col-sm-4 control-label">Alamat Kantor Wali</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="guardian_job_address" value="{{auth()->user()->orangtua->guardian_job_address}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_wali" class="col-sm-4 control-label">Gaji Wali</label>
                            <div class="col-sm-6">
                                <select name="guardian_salary" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
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
                                <input type="text" class="form-control" name="guardian_address" value="{{auth()->user()->orangtua->guardian_address}}" placeholder="Alamat Wali">
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
@include('template.footjs.kepegawaian.rtrw')


@endsection