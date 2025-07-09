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

            <a href="{{route('psb.profil.edit')}}" type="submit" class="btn btn-brand-green-dark">Ubah</a>
        </div>
            <div class="card-body">
                <form action=""  method="POST">
                <div class="row">
                    <div class="col-md-8">
                        @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> {{ Session::get('success') }}
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
                            <label for="nama_ayah" class="col-sm-4 control-label">Nama Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="nama_ayah" value="{{auth()->user()->orangtua->father_name}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_ayah" class="col-sm-4 control-label">NIK Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="number" class="form-control" name="nik_ayah"  value="{{auth()->user()->orangtua->father_nik}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_ayah" class="col-sm-4 control-label">No HP Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="number" class="form-control" name="hp_ayah" value="{{auth()->user()->orangtua->father_phone}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_ayah" class="col-sm-4 control-label">email Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="email_ayah" value="{{auth()->user()->orangtua->father_email}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ayah" class="col-sm-4 control-label">Pekerjaan Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="email_ayah" value="{{auth()->user()->orangtua->father_job}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ayah" class="col-sm-4 control-label">Jabatan Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="jabatan_ayah" value="{{auth()->user()->orangtua->father_position}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ayah" class="col-sm-4 control-label">Telp Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="telp_kantor_ayah" value="{{auth()->user()->orangtua->father_phone_office}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ayah" class="col-sm-4 control-label">Alamat Kantor Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_kantor_ayah" value="{{auth()->user()->orangtua->father_job_address}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ayah" class="col-sm-4 control-label">Gaji Ayah</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_kantor_ayah" value="{{auth()->user()->orangtua->father_salary}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ibu" class="col-sm-4 control-label">Nama Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="nama_ibu" value="{{auth()->user()->orangtua->mother_name}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_ibu" class="col-sm-4 control-label">NIK Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="number" class="form-control" name="nik_ibu" value="{{auth()->user()->orangtua->mother_nik}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_ibu" class="col-sm-4 control-label">No HP Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="number" class="form-control" name="hp_ibu" value="{{auth()->user()->orangtua->mother_phone}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_ibu" class="col-sm-4 control-label">email Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="email_ibu" value="{{auth()->user()->orangtua->mother_email}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_ibu" class="col-sm-4 control-label">Pekerjaan Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="email_ibu" value="{{auth()->user()->orangtua->mother_job}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_ibu" class="col-sm-4 control-label">Jabatan Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="jabatan_ibu" value="{{auth()->user()->orangtua->mother_position}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_ibu" class="col-sm-4 control-label">Telp Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="telp_kantor_ibu" value="{{auth()->user()->orangtua->mother_phone_office}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_ibu" class="col-sm-4 control-label">Alamat Kantor Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_kantor_ibu" value="{{auth()->user()->orangtua->mother_job_address}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_ibu" class="col-sm-4 control-label">Gaji Ibu</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_kantor_ibu" value="{{auth()->user()->orangtua->mother_salary}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_ortu" class="col-sm-4 control-label">Alamat Orang Tua</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_ortu" value="{{auth()->user()->orangtua->parent_address}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="no_hp_ortu" class="col-sm-4 control-label">No HP Alternatif</label>
                            <div class="col-sm-6">
                                <input readonly type="number" class="form-control" name="no_hp_ortu" value="{{auth()->user()->orangtua->parent_phone_number}}" >
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
                                <input readonly type="text" class="form-control" name="nama_wali" value="{{auth()->user()->orangtua->guardian_name}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nik_wali" class="col-sm-4 control-label">NIK Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="number" class="form-control" name="nik_wali" value="{{auth()->user()->orangtua->guardian_nik}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="hp_wali" class="col-sm-4 control-label">No HP Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="number" class="form-control" name="hp_wali" value="{{auth()->user()->orangtua->guardian_phone_number}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="email_wali" class="col-sm-4 control-label">email Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="email_wali" value="{{auth()->user()->orangtua->guardian_email}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="pekerjaan_wali" class="col-sm-4 control-label">Pekerjaan Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_kantor_ibu" value="{{auth()->user()->orangtua->guardian_job}}" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jabatan_wali" class="col-sm-4 control-label">Jabatan Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="jabatan_wali" value="{{auth()->user()->orangtua->guardian_position}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="telp_kantor_wali" class="col-sm-4 control-label">Telp Kantor Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="telp_kantor_wali" value="{{auth()->user()->orangtua->guardian_phone_office}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_kantor_wali" class="col-sm-4 control-label">Alamat Kantor Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_kantor_wali" value="{{auth()->user()->orangtua->guardian_job_address}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="gaji_wali" class="col-sm-4 control-label">Gaji Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_kantor_ibu" value="{{auth()->user()->orangtua->guardian_salary}}" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat_wali" class="col-sm-4 control-label">Alamat Wali</label>
                            <div class="col-sm-6">
                                <input readonly type="text" class="form-control" name="alamat_wali" value="{{auth()->user()->orangtua->guardian_address}}" >
                            </div>
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