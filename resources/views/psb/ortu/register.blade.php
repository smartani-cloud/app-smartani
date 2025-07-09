<<<<<<< HEAD
@extends('template.main.psb.master')

@section('title')
Pendaftaran Siswa Baru {{$unit_name}}
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
    <h1 class="h3 mb-0 text-gray-800">Pendaftaran Calon Siswa Baru Unit {{$unit_name}}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pendaftaran Calon Siswa Baru Unit {{$unit_name}}</a></li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
        <div class="card-header py-3 bg-brand-green-dark d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Form Pendaftaran Calon Siswa Baru Unit {{$unit_name}}</h6>
        </div>
            <div class="card-body">
                @if(Session::has('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Gagal!</strong> {{ Session::get('danger') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
                @if($unit->psb_active == 1)
                <form action="{{route('psb.siswa.store')}}"  method="POST">
                @method('POST')
                @csrf
                <input type="hidden" name="unit_id" value="{{$unit_id}}">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Siswa Baru?</label>
                            <div class="col-sm-6">
                                @if($unit->new_admission_active == 1 || $unit->transfer_admission_active == 1)
                                <select name="siswa_baru" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if($unit->new_admission_active == 1)
                                        <option value="1" {{ $unit->new_admission_active == 1 ? 'selected' : null }}>Baru</option>
                                        @else
                                        <option value="" class="bg-gray-300" disabled="disabled">Baru</option>
                                        @endif
                                        @if($unit->transfer_admission_active == 1)
                                        <option value="2" {{ $unit->transfer_admission_active == 1 && $unit->new_admission_active != 1 ? 'selected' : null }}>Pindahan</option>
                                        @else                                        
                                        <option value="" class="bg-gray-300" disabled="disabled">Pindahan</option>
                                        @endif
                                </select>
                                @error('siswa_baru')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @endif
                            </div>
                        </div>
                        <div class="form-group row kelas" style="{{ ($unit->transfer_admission_active == 1 && $unit->new_admission_active != 1) ? null : 'display: none'}}">
                            <label for="kelas" class="col-sm-4 control-label">Kelas<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width select-transfer" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    @foreach( $levels as $index => $level)
                                        <option value="{{ $level->id }}" {{$index==0?'selected':''}}>{{ $level->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Semester Ajaran</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Semester Ajaran</label>
                            <div class="col-sm-6">
                                <select name="tahun_ajaran" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                        <option value="">== Pilih Semester ==</option>
                                    @foreach( $tahunAjaran as $index => $ta)
                                        <option id="semester_{{$index}}" value="{{ $ta->id }}" style="display: {{ ($index == 2 && $unit->new_admission_active == 1) || ($unit->transfer_admission_active == 1 && $unit->new_admission_active != 1) ?'block':'none'}}" {{$index==2?'selected':''}}>{{ $ta->semester_id }} ({{$ta->semester}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Sekolah Asal</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Asal Sekolah<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="asal_sekolah" id="" class="select2 form-control auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    @if(auth()->user()->orangtua->siswas()->count() > 0)<option value="Sekolah Digiyok">Sekolah Digiyok</option>@endif
                                    <option value="Sekolah Lain" selected="selected">Sekolah Lain</option>
                                </select>
                            </div>
                        </div>
                        <div id="asal_sekolah_lain" style="display:block">
                            <div class="form-group row">
                                <label for="alamat_asal_sekolah" class="col-sm-4 control-label">Nama Asal Sekolah</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="alamat_asal_sekolah" placeholder="Nama Asal Sekolah" value="">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Umum Calon Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="existingOpt" class="col-sm-4 control-label">Calon Siswa Pernah di Digiyok?<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="existingOpt1" name="existing" class="custom-control-input select-new" value="1" required="required" checked="checked">
                                    <label class="custom-control-label" for="existingOpt1">Tidak</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="existingOpt2" name="existing" class="custom-control-input select-new" value="2" required="required" {!! auth()->user()->orangtua->siswas()->count() > 0 ? null : 'disabled="disabled"' !!}>
                                    <label class="custom-control-label" for="existingOpt2">Pernah</label>
                                </div>
                                @error('existing')
                                <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @if(auth()->user()->orangtua->siswas()->count() > 0)
                        <div id="siswa_exist" style="display: none">
                            <div class="form-group row">
                                <label for="siswa_id" class="col-sm-4 control-label">Siswa<span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <select name="siswa_id" id="siswa_id" class="select2 form-control auto_width select-exist" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="" selected>== Pilih Calon Siswa ==</option>
                                        @foreach( auth()->user()->orangtua->siswas as $index => $anak )
                                        @if(auth()->user()->orangtua->calonSiswa()->count() < 1 || (auth()->user()->orangtua->calonSiswa()->count() > 0 && !in_array($anak->id,auth()->user()->orangtua->calonSiswa()->select('student_id')->get()->pluck('student_id')->toArray())))
                                        <option value="{{$anak->id}}" >{{$anak->student_name}}</option>
                                        @else
                                        <option value="" class="bg-gray-300" disabled="disabled">{{$anak->student_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div id="databaru">
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">NIK Calon Siswa<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="nik"  placeholder="Nomor Induk Kependudukan" minlength="16" maxlength="16" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">Nama Lengkap<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="nama" placeholder="Nama" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_pendek" class="col-sm-4 control-label">Nama Panggilan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="nama_pendek" placeholder="Nama" value="" required="required">
                            </div>
                        </div>
                        {{-- <div class="form-group row">
                            <label for="nisn" class="col-sm-4 control-label">NISN</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nisn" placeholder="NISN" value="" >
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label for="tempat_lahir" class="col-sm-4 control-label">Tempat Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="tempat_lahir" placeholder="Tempat Lahir" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-sm-4 control-label">Tanggal Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control select-new" name="tanggal_lahir"  value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jenis_kelamin" class="col-sm-4 control-label">Jenis Kelamin<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                @foreach($jeniskelamin as $j)
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="genderOpt{{ $j->id }}" name="jenis_kelamin" class="custom-control-input select-new" value="{{ $j->id }}" required="required">
                                <label class="custom-control-label" for="genderOpt{{ $j->id }}">{{ ucwords($j->name) }}</label>
                                </div>
                                @endforeach
                                @error('gender')
                                <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="agama" class="col-sm-4 control-label">Agama<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="agama" class="select2 form-control select2-hidden-accessible auto_width select-new" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" readonly="readonly" required="required">
                                    <option value="" id="">== Pilih Agama ==</option>
                                @foreach( $agamas as $agama )
                                    <option value="{{ $agama->id }}" selected>{{ $agama->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <!--<div class="form-group row">
                            <label for="anak_ke" class="col-sm-4 control-label">Anak ke-</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="anak_ke" placeholder="1" value="" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Status Keluarga</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="status_anak" placeholder="1" value="" >
                            </div>
                        </div>-->
                        <hr>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Alamat Calon Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat" class="col-sm-4 control-label">Alamat<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="alamat" placeholder="Alamat" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">No Rumah<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="no_rumah" placeholder="001" value="" required="required">
                            </div>
                        </div>
                        <!-- Alamat RT dan RW -->
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">RT<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control select-new" name="rt" placeholder="RT" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rw" class="col-sm-4 control-label">RW<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control select-new" name="rw" placeholder="RW" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Provinsi<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="provinsi" class="select2 form-control auto_width select-new" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    <option value="" id="provinsi">== Pilih Provinsi ==</option>
                                @foreach( $listprovinsi as $list )
                                    <option value="{{ $list->code }}">{{ $list->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kabupaten/Kota<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="kabupaten" class="select2 form-control auto_width select-new"  id="kabupaten" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    <option value="" id="kabupaten">== Pilih Kabupaten ==</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kecamatan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="kecamatan" class="select2 form-control auto_width select-new"  id="kecamatan" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    <option value="" id="kecamatan">== Pilih Kecamatan ==</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Desa/Kelurahan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="desa" class="select2 form-control auto_width select-new"  id="desa" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    <option value="" id="desa">== Pilih Desa/Kelurahan ==</option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Saudara Kandung</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Nama Saudara Kandung di Digiyok</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="saudara_nama" placeholder="Nama" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas Saudara Kandung</label>
                            <div class="col-sm-6">
                                <select name="saudara_kelas" class="select2 form-control auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">Tidak Ada</option>
                                    @foreach( $kelases as $level)
                                        <option value="{{ $level->id }}">{{ $level->level }}</option>
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
                            <label for="info_dari" class="col-sm-4 control-label">Informasi Dari<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="info_dari" class="form-control auto_width select-new" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                        <option value="">== Pilih ==</option>
                                        <option value="Orang Tua Digiyok">Orangtua Digiyok</option>
                                        <option value="Guru/Staf">Guru/Staf</option>
                                        <option value="Brosur">Brosur</option>
                                        <option value="Spanduk">Spanduk</option>
                                        <option value="Website dan Sosmed Digiyok">Website dan Sosmed Digiyok</option>
                                        <option value="Media Cetak">Media Cetak</option>
                                        <option value="Sering Lewat">Sering Lewat</option>
                                        <option value="Teman">Teman</option>
                                </select>
                            </div>
                        </div>
                        <div id="append_pegawai" style="display:none">
                            <div class="form-group row">
                                <label for="info_nama" class="col-sm-4 control-label">Nama</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="info_nama" placeholder="Nama" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="posisi" class="col-sm-4 control-label">Posisi / Kelas</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="posisi" placeholder="" value="">
                                </div>
                            </div>
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
                @else
                <div class="alert alert-light text-dark alert-dismissible fade show" role="alert">
                    <i class="fa fa-info-circle text-info mr-2"></i>Mohon maaf, saat ini kami belum membuka pendaftaran siswa baru untuk unit {{ $unit_name }}.<br>Ikuti terus media sosial Sekolah Digiyok untuk mendapatkan info-info terbaru.
                </div>
                @endif
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
<!-- Select2 -->
<script src="/vendor/select2/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function(){
        $('select[name="info_dari"]').on('change', function() {
            console.log('masuk sini');
            var info_dari = this.value;
            var pegawai = false;
            if(info_dari == "Orang Tua Digiyok"){
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
            if(asal_sekolah == "Sekolah Digiyok"){
                sekolah_lain = true;
            }
            if(sekolah_lain == true){
                $('div[id="asal_sekolah_lain"]').hide();
            }else{
                $('div[id="asal_sekolah_lain"]').show();
            }
        });
        $('input[name="existing"]').on('change', function() {
            var riwayat = this.value;
            var baru = false;
            if(riwayat == "1"){
                baru = true;
            }
            if(baru == false){
                $('div[id="databaru"]').hide();
                $('div[id="siswa_exist"]').show();
                $('.select-new').removeAttr('required','required');
                $('.select-exist').attr('required','required');
            }else{
                $('div[id="databaru"]').show();
                $('div[id="siswa_exist"]').hide();
                $('.select-exist').removeAttr('required','required');
                $('.select-new').attr('required','required');
            }
        });
        $('select[name="siswa_baru"]').on('change', function() {
            var siswa_baru = this.value;
            if(siswa_baru == 1){
                $('.kelas').hide();
                $('option[id="semester_0"]').hide();
                $('option[id="semester_1"]').hide();
                $('option[id="semester_2"]').attr('selected','selected');
                $('option[id="semester_3"]').hide();
                $('.select-transfer').removeAttrattr('required','required');
                
            }else{
                $('.kelas').show();
                $('option[id="semester_0"]').show();
                $('option[id="semester_1"]').show();
                $('option[id="semester_3"]').show();
                $('.select-transfer').attr('required','required');
            }
        });
    });
</script>



=======
@extends('template.main.psb.master')

@section('title')
Pendaftaran Siswa Baru {{$unit_name}}
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
    <h1 class="h3 mb-0 text-gray-800">Pendaftaran Calon Siswa Baru Unit {{$unit_name}}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pendaftaran Calon Siswa Baru Unit {{$unit_name}}</a></li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
        <div class="card-header py-3 bg-brand-green-dark d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-white">Form Pendaftaran Calon Siswa Baru Unit {{$unit_name}}</h6>
        </div>
            <div class="card-body">
                @if(Session::has('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Gagal!</strong> {{ Session::get('danger') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
                @if($unit->psb_active == 1)
                <form action="{{route('psb.siswa.store')}}"  method="POST">
                @method('POST')
                @csrf
                <input type="hidden" name="unit_id" value="{{$unit_id}}">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Siswa Baru?</label>
                            <div class="col-sm-6">
                                @if($unit->new_admission_active == 1 || $unit->transfer_admission_active == 1)
                                <select name="siswa_baru" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        @if($unit->new_admission_active == 1)
                                        <option value="1" {{ $unit->new_admission_active == 1 ? 'selected' : null }}>Baru</option>
                                        @else
                                        <option value="" class="bg-gray-300" disabled="disabled">Baru</option>
                                        @endif
                                        @if($unit->transfer_admission_active == 1)
                                        <option value="2" {{ $unit->transfer_admission_active == 1 && $unit->new_admission_active != 1 ? 'selected' : null }}>Pindahan</option>
                                        @else                                        
                                        <option value="" class="bg-gray-300" disabled="disabled">Pindahan</option>
                                        @endif
                                </select>
                                @error('siswa_baru')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @endif
                            </div>
                        </div>
                        <div class="form-group row kelas" style="{{ ($unit->transfer_admission_active == 1 && $unit->new_admission_active != 1) ? null : 'display: none'}}">
                            <label for="kelas" class="col-sm-4 control-label">Kelas<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="kelas" class="select2 form-control select2-hidden-accessible auto_width select-transfer" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    @foreach( $levels as $index => $level)
                                        <option value="{{ $level->id }}" {{$index==0?'selected':''}}>{{ $level->level }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Semester Ajaran</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Semester Ajaran</label>
                            <div class="col-sm-6">
                                <select name="tahun_ajaran" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                        <option value="">== Pilih Semester ==</option>
                                    @foreach( $tahunAjaran as $index => $ta)
                                        <option id="semester_{{$index}}" value="{{ $ta->id }}" style="display: {{ ($index == 2 && $unit->new_admission_active == 1) || ($unit->transfer_admission_active == 1 && $unit->new_admission_active != 1) ?'block':'none'}}" {{$index==2?'selected':''}}>{{ $ta->semester_id }} ({{$ta->semester}})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Sekolah Asal</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Asal Sekolah<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="asal_sekolah" id="" class="select2 form-control auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    @if(auth()->user()->orangtua->siswas()->count() > 0)<option value="Sekolah Digiyok">Sekolah Digiyok</option>@endif
                                    <option value="Sekolah Lain" selected="selected">Sekolah Lain</option>
                                </select>
                            </div>
                        </div>
                        <div id="asal_sekolah_lain" style="display:block">
                            <div class="form-group row">
                                <label for="alamat_asal_sekolah" class="col-sm-4 control-label">Nama Asal Sekolah</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="alamat_asal_sekolah" placeholder="Nama Asal Sekolah" value="">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Umum Calon Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="existingOpt" class="col-sm-4 control-label">Calon Siswa Pernah di Digiyok?<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="existingOpt1" name="existing" class="custom-control-input select-new" value="1" required="required" checked="checked">
                                    <label class="custom-control-label" for="existingOpt1">Tidak</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="existingOpt2" name="existing" class="custom-control-input select-new" value="2" required="required" {!! auth()->user()->orangtua->siswas()->count() > 0 ? null : 'disabled="disabled"' !!}>
                                    <label class="custom-control-label" for="existingOpt2">Pernah</label>
                                </div>
                                @error('existing')
                                <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @if(auth()->user()->orangtua->siswas()->count() > 0)
                        <div id="siswa_exist" style="display: none">
                            <div class="form-group row">
                                <label for="siswa_id" class="col-sm-4 control-label">Siswa<span class="text-danger">*</span></label>
                                <div class="col-sm-6">
                                    <select name="siswa_id" id="siswa_id" class="select2 form-control auto_width select-exist" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="" selected>== Pilih Calon Siswa ==</option>
                                        @foreach( auth()->user()->orangtua->siswas as $index => $anak )
                                        @if(auth()->user()->orangtua->calonSiswa()->count() < 1 || (auth()->user()->orangtua->calonSiswa()->count() > 0 && !in_array($anak->id,auth()->user()->orangtua->calonSiswa()->select('student_id')->get()->pluck('student_id')->toArray())))
                                        <option value="{{$anak->id}}" >{{$anak->student_name}}</option>
                                        @else
                                        <option value="" class="bg-gray-300" disabled="disabled">{{$anak->student_name}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div id="databaru">
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">NIK Calon Siswa<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="nik"  placeholder="Nomor Induk Kependudukan" minlength="16" maxlength="16" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama" class="col-sm-4 control-label">Nama Lengkap<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="nama" placeholder="Nama" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_pendek" class="col-sm-4 control-label">Nama Panggilan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="nama_pendek" placeholder="Nama" value="" required="required">
                            </div>
                        </div>
                        {{-- <div class="form-group row">
                            <label for="nisn" class="col-sm-4 control-label">NISN</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nisn" placeholder="NISN" value="" >
                            </div>
                        </div> --}}
                        <div class="form-group row">
                            <label for="tempat_lahir" class="col-sm-4 control-label">Tempat Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="tempat_lahir" placeholder="Tempat Lahir" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="tanggal_lahir" class="col-sm-4 control-label">Tanggal Lahir<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control select-new" name="tanggal_lahir"  value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="jenis_kelamin" class="col-sm-4 control-label">Jenis Kelamin<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                @foreach($jeniskelamin as $j)
                                <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="genderOpt{{ $j->id }}" name="jenis_kelamin" class="custom-control-input select-new" value="{{ $j->id }}" required="required">
                                <label class="custom-control-label" for="genderOpt{{ $j->id }}">{{ ucwords($j->name) }}</label>
                                </div>
                                @endforeach
                                @error('gender')
                                <span class="text-danger d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="agama" class="col-sm-4 control-label">Agama<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="agama" class="select2 form-control select2-hidden-accessible auto_width select-new" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" readonly="readonly" required="required">
                                    <option value="" id="">== Pilih Agama ==</option>
                                @foreach( $agamas as $agama )
                                    <option value="{{ $agama->id }}" selected>{{ $agama->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <!--<div class="form-group row">
                            <label for="anak_ke" class="col-sm-4 control-label">Anak ke-</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control" name="anak_ke" placeholder="1" value="" >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Status Keluarga</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="status_anak" placeholder="1" value="" >
                            </div>
                        </div>-->
                        <hr>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Informasi Alamat Calon Siswa</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="alamat" class="col-sm-4 control-label">Alamat<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="alamat" placeholder="Alamat" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">No Rumah<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control select-new" name="no_rumah" placeholder="001" value="" required="required">
                            </div>
                        </div>
                        <!-- Alamat RT dan RW -->
                        <div class="form-group row">
                            <label for="rt" class="col-sm-4 control-label">RT<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control select-new" name="rt" placeholder="RT" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rw" class="col-sm-4 control-label">RW<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control select-new" name="rw" placeholder="RW" value="" required="required">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Provinsi<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="provinsi" class="select2 form-control auto_width select-new" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    <option value="" id="provinsi">== Pilih Provinsi ==</option>
                                @foreach( $listprovinsi as $list )
                                    <option value="{{ $list->code }}">{{ $list->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kabupaten/Kota<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="kabupaten" class="select2 form-control auto_width select-new"  id="kabupaten" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    <option value="" id="kabupaten">== Pilih Kabupaten ==</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kecamatan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="kecamatan" class="select2 form-control auto_width select-new"  id="kecamatan" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    <option value="" id="kecamatan">== Pilih Kecamatan ==</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Desa/Kelurahan<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="desa" class="select2 form-control auto_width select-new"  id="desa" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                    <option value="" id="desa">== Pilih Desa/Kelurahan ==</option>
                                </select>
                            </div>
                        </div>
                        <hr/>
                        <div class="row mb-4">
                            <div class="col-12">
                            <h6 class="font-weight-bold text-brand-green">Saudara Kandung</h6>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="asal_sekolah" class="col-sm-4 control-label">Nama Saudara Kandung di Digiyok</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="saudara_nama" placeholder="Nama" value="">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas Saudara Kandung</label>
                            <div class="col-sm-6">
                                <select name="saudara_kelas" class="select2 form-control auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">Tidak Ada</option>
                                    @foreach( $kelases as $level)
                                        <option value="{{ $level->id }}">{{ $level->level }}</option>
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
                            <label for="info_dari" class="col-sm-4 control-label">Informasi Dari<span class="text-danger">*</span></label>
                            <div class="col-sm-6">
                                <select name="info_dari" class="form-control auto_width select-new" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" required="required">
                                        <option value="">== Pilih ==</option>
                                        <option value="Orang Tua Digiyok">Orangtua Digiyok</option>
                                        <option value="Guru/Staf">Guru/Staf</option>
                                        <option value="Brosur">Brosur</option>
                                        <option value="Spanduk">Spanduk</option>
                                        <option value="Website dan Sosmed Digiyok">Website dan Sosmed Digiyok</option>
                                        <option value="Media Cetak">Media Cetak</option>
                                        <option value="Sering Lewat">Sering Lewat</option>
                                        <option value="Teman">Teman</option>
                                </select>
                            </div>
                        </div>
                        <div id="append_pegawai" style="display:none">
                            <div class="form-group row">
                                <label for="info_nama" class="col-sm-4 control-label">Nama</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="info_nama" placeholder="Nama" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="posisi" class="col-sm-4 control-label">Posisi / Kelas</label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="posisi" placeholder="" value="">
                                </div>
                            </div>
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
                @else
                <div class="alert alert-light text-dark alert-dismissible fade show" role="alert">
                    <i class="fa fa-info-circle text-info mr-2"></i>Mohon maaf, saat ini kami belum membuka pendaftaran siswa baru untuk unit {{ $unit_name }}.<br>Ikuti terus media sosial Sekolah Digiyok untuk mendapatkan info-info terbaru.
                </div>
                @endif
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
<!-- Select2 -->
<script src="/vendor/select2/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function(){
        $('select[name="info_dari"]').on('change', function() {
            console.log('masuk sini');
            var info_dari = this.value;
            var pegawai = false;
            if(info_dari == "Orang Tua Digiyok"){
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
            if(asal_sekolah == "Sekolah Digiyok"){
                sekolah_lain = true;
            }
            if(sekolah_lain == true){
                $('div[id="asal_sekolah_lain"]').hide();
            }else{
                $('div[id="asal_sekolah_lain"]').show();
            }
        });
        $('input[name="existing"]').on('change', function() {
            var riwayat = this.value;
            var baru = false;
            if(riwayat == "1"){
                baru = true;
            }
            if(baru == false){
                $('div[id="databaru"]').hide();
                $('div[id="siswa_exist"]').show();
                $('.select-new').removeAttr('required','required');
                $('.select-exist').attr('required','required');
            }else{
                $('div[id="databaru"]').show();
                $('div[id="siswa_exist"]').hide();
                $('.select-exist').removeAttr('required','required');
                $('.select-new').attr('required','required');
            }
        });
        $('select[name="siswa_baru"]').on('change', function() {
            var siswa_baru = this.value;
            if(siswa_baru == 1){
                $('.kelas').hide();
                $('option[id="semester_0"]').hide();
                $('option[id="semester_1"]').hide();
                $('option[id="semester_2"]').attr('selected','selected');
                $('option[id="semester_3"]').hide();
                $('.select-transfer').removeAttrattr('required','required');
                
            }else{
                $('.kelas').show();
                $('option[id="semester_0"]').show();
                $('option[id="semester_1"]').show();
                $('option[id="semester_3"]').show();
                $('.select-transfer').attr('required','required');
            }
        });
    });
</script>



>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection