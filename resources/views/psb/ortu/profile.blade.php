@extends('template.main.psb.master')

@section('title')
Profil
@endsection
 
@section('headmeta')
  <link href="{{ asset('public/buttons.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.ortu.ortu')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Profil</h1>
    {{-- <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pendaftaran Calon Siswa Baru Unit {{$unit_name}}</a></li>
    </ol> --}}
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
        <div class="card-header py-3 bg-brand-green-dark d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Data saya</h6>
        </div>
            <div class="card-body">
                <form action="{{route('psb.profil.update')}}"  method="POST">
                @method('POST')
                @csrf
                <div class="row">
                    
                    <div class="row mb-4">
                        <div class="col-12">
                        <h6 class="font-weight-bold text-brand-green">Informasi Orang Tua</h6>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_ayah" class="col-sm-4 control-label">Nama Ayah</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="nama_ayah" placeholder="Nama Ayah">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_ayah" class="col-sm-4 control-label">NIK Ayah</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="nik_ayah" placeholder="NIK Ayah">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="hp_ayah" class="col-sm-4 control-label">No HP Ayah</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="hp_ayah" placeholder="No HP Ayah">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email_ayah" class="col-sm-4 control-label">email Ayah</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="email_ayah" placeholder="Email Ayah">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pekerjaan_ayah" class="col-sm-4 control-label">Pekerjaan Ayah</label>
                        <div class="col-sm-6">
                            <select name="pekerjaan_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Pekerjaan ==</option>
                                    <option value="Pegawai Negeri">Pegawai Negeri</option>
                                    <option value="Pegawai Swasta">Pegawai Swasta</option>
                                    <option value="Wiraswasta">Wiraswasta</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jabatan_ayah" class="col-sm-4 control-label">Jabatan Ayah</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="jabatan_ayah" placeholder="Jabatan Ayah">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="telp_kantor_ayah" class="col-sm-4 control-label">Telp Kantor Ayah</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="telp_kantor_ayah" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="alamat_kantor_ayah" class="col-sm-4 control-label">Alamat Kantor Ayah</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="alamat_kantor_ayah" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gaji_ayah" class="col-sm-4 control-label">Gaji Ayah</label>
                        <div class="col-sm-6">
                            <select name="gaji_ayah" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                <option value="">== Pilih ==</option>
                                <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nama_ibu" class="col-sm-4 control-label">Nama Ibu</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="nama_ibu" placeholder="Nama Ibu">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_ibu" class="col-sm-4 control-label">NIK Ibu</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="nik_ibu" placeholder="NIK Ibu">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="hp_ibu" class="col-sm-4 control-label">No HP Ibu</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="hp_ibu" placeholder="No HP Ibu">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email_ibu" class="col-sm-4 control-label">email Ibu</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="email_ibu" placeholder="Email Ibu">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pekerjaan_ibu" class="col-sm-4 control-label">Pekerjaan Ibu</label>
                        <div class="col-sm-6">
                            <select name="pekerjaan_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Pekerjaan ==</option>
                                    <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                    <option value="Pegawai Negeri">Pegawai Negeri</option>
                                    <option value="Pegawai Swasta">Pegawai Swasta</option>
                                    <option value="Wiraswasta">Wiraswasta</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="jabatan_ibu" class="col-sm-4 control-label">Jabatan Ibu</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="jabatan_ibu" placeholder="Jabatan Ibu">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="telp_kantor_ibu" class="col-sm-4 control-label">Telp Kantor Ibu</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="telp_kantor_ibu" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="alamat_kantor_ibu" class="col-sm-4 control-label">Alamat Kantor Ibu</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="alamat_kantor_ibu" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gaji_ibu" class="col-sm-4 control-label">Gaji Ibu</label>
                        <div class="col-sm-6">
                            <select name="gaji_ibu" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih ==</option>
                                    <option value="&lt; Rp. 5.000.000">&lt; Rp. 5.000.000</option>
                                    <option value="Rp. 5.000.000 - Rp. 10.000.000">Rp. 5.000.000 - Rp. 10.000.000</option>
                                    <option value="&gt; Rp. 10.000.000">&gt; Rp. 10.000.000</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="alamat_ortu" class="col-sm-4 control-label">Alamat Orang Tua</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="alamat_ortu" placeholder="Alamat Orang Tua">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="no_hp_ortu" class="col-sm-4 control-label">No HP Alternatif</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="no_hp_ortu" placeholder="08222*****">
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
                            <input type="text" class="form-control" name="nama_wali" placeholder="Nama Wali">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="nik_wali" class="col-sm-4 control-label">NIK Wali</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="nik_wali" placeholder="NIK Wali">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="hp_wali" class="col-sm-4 control-label">No HP Wali</label>
                        <div class="col-sm-6">
                            <input type="number" class="form-control" name="hp_wali" placeholder="No HP Wali">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email_wali" class="col-sm-4 control-label">email Wali</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="email_wali" placeholder="Email Wali">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="pekerjaan_wali" class="col-sm-4 control-label">Pekerjaan Wali</label>
                        <div class="col-sm-6">
                            <select name="pekerjaan_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
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
                            <input type="text" class="form-control" name="jabatan_wali" placeholder="Jabatan Wali">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="telp_kantor_wali" class="col-sm-4 control-label">Telp Kantor Wali</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="telp_kantor_wali" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="alamat_kantor_wali" class="col-sm-4 control-label">Alamat Kantor Wali</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="alamat_kantor_wali" placeholder="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="gaji_wali" class="col-sm-4 control-label">Gaji Wali</label>
                        <div class="col-sm-6">
                            <select name="gaji_wali" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
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
                            <input type="text" class="form-control" name="alamat_wali" placeholder="Alamat Wali">
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
<!-- Plugins and scripts harusnyarequired by this view-->


<!-- Bootstrap Touchspin -->
<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>

<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
<script src="{{asset('js/wilayah.js')}}"></script>
<!-- Select2 -->
<script src="/vendor/select2/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function(){
        $('select[name="info_dari"]').on('change', function() {
            console.log('masuk sini');
            var info_dari = this.value;
            var pegawai = false;
            if(info_dari == "Orangtua Auliya"){
                pegawai = true;
            }else if(info_dari == "Guru/Staf"){
                pegawai = true;
            }
            if(pegawai == true){
                $('div[id="append_pegawai"]').show();
            }else{
                $('div[id="append_pegawai"]').hide();
            }
        });
        $('select[name="asal_sekolah"]').on('change', function() {
            var asal_sekolah = this.value;
            var sekolah_lain = false;
            if(asal_sekolah == "SIT Auliya"){
                sekolah_lain = true;
            }
            if(sekolah_lain == true){
                $('div[id="asal_sekolah_lain"]').hide();
                $('div[id="databaru"]').hide();
                $('div[id="siswa_exist"]').show();
            }else{
                $('div[id="asal_sekolah_lain"]').show();
                $('div[id="databaru"]').show();
                $('div[id="siswa_exist"]').hide();
            }
        });
    });
</script>



@endsection