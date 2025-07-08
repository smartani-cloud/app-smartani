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
            <form class="login100-form validate-form" action="{{route('psb.register')}}" method="post">
                @csrf
                <div class="login100-logo m-b-5">
                    <img src="{{ asset('img/logo/logomark.png') }}">
                </div>
                <span class="login100-form-subtitle p-b-27">DIGIYOK<i> Sekolah</i></span>
                <span class="login100-form-subtitle p-b-27">Form Pendaftaran Siswa Baru</span>

                @if(isset($lock) && $lock->value == 1)
                <div class="alert alert-light text-dark alert-dismissible fade show" role="alert">
                    <i class="fa fa-info-circle text-info mr-2"></i>Mohon maaf, saat ini kami belum membuka pendaftaran siswa baru.<br>Ikuti terus media sosial DIGIYOK untuk mendapatkan info-info terbaru.
                </div>
                @else
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
                  @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                  @endif
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Memiliki Putra/i di DIGIYOK?</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <select class="input1005 select-existing" style="width:100%; border-style:none" name="siswa" id="" required="required">
                                <option class="input1005" value="0" selected>Tidak</option>
                                <option class="input1005" value="1">Ya</option>
                            </select>
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    
                    <div id="nipd_siswa" class="row align-items-center" style="display:none">
                        <label class="col-md-3 col-xs-4">NIPD Siswa</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005 select-exist" type="text" name="nipd" placeholder="NIPD Siswa DIGiYOK">
                        </div>
                    </div>
                <div id="ortu">
                    <div class="alert alert-warning mt-2">
                        Semua form wajib diisi
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Nama Ayah / Wali</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005 select-new" type="text" name="father_name" value="{{ old('father_name') }}" placeholder="Nama Ayah" required="required">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">No HP Ayah / Wali</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005 select-new" type="number" name="father_phone" value="{{ old('father_phone') }}" placeholder="No HP Ayah" required="required">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Email Ayah / Wali</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005 select-new" type="email" name="father_email" value="{{ old('father_email') }}" placeholder="Email Ayah (Menjadi Username Login)" required="required">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <br>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Nama Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005 select-new" type="text" name="mother_name" placeholder="Nama Ibu" value="{{ old('mother_name') }}" required="required">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">No HP Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005 select-new" type="number" name="mother_phone" placeholder="No HP Ibu" value="{{ old('mother_phone') }}" required="required">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <label class="col-md-3 col-xs-4">Email Ibu</label>
                        <div class="col-md-9 col-xs-6 wrap-input1005 validate-input m-b-10" data-validate="Masukkan No Pendaftaran">
                            <input class="input1005 select-new" type="email" name="mother_email" placeholder="Email Ibu" value="{{ old('mother_email') }}" required="required">
                            <span class="focus-input1005"></span>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-md-12 col-xs-12">
                            Note :<br>1. Email Ayah menjadi username pada saat login<br>2. No. handphone Ibu akan dijadikan komunikasi dengan DIGIYOK via Whatsapp</label>
                    </div>
                </div>
                <div class="text-center mt-2">
                    <div class="container-login100-form-btn">
                        <button class="login100-form-btn" onclick="showmodals()">
                            Daftar
                        </button>
                    </div>
                </div>
                @endif
				<div class="text-center m-t-45">
				    <a href="{{ route('psb.index') }}" class="text-secondary font-weight-bold">Kembali ke Halaman Awal</a>
				</div>
            </form>
        </div>
<!--Row-->

@endsection
@section('footjs')
<div class="modal fade" id="problem-confirm" tabindex="-1" role="dialog" aria-labelledby="problemModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header bg-danger border-0">
        <h5 class="modal-title text-white">Email Sudah Terdaftar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
        </button>
      </div>
      
      <div class="modal-body px-5 py-4">
        <div class="row mb-2">
          <div class="col-12">
            Ayah/Bunda mengalami kendala pada PSB DIGIYOK? Hubungi admin kami{{ $units && count($units) > 0 ? ':' : null }}
          </div>
        </div>
        @if($units && count($units) > 0)
        <div class="row mb-2">
          <div class="col-12">
            @php
            $whatsAppText = rawurlencode("Assalamu'alaikum Admin, saya mengalami kendala pada PSB DIGIYOK.");
            @endphp
            @foreach($units as $unit)
            @if($unit->whatsapp_unit && (substr($unit->whatsapp_unit, 0, 2) == "62" || substr($unit->whatsapp_unit, 0, 1) == "0"))
            <a href="https://api.whatsapp.com/send?phone={{ substr($unit->whatsapp_unit, 0, 2) == "62" ? $unit->whatsapp_unit : ('62'.substr($unit->whatsapp_unit, 1)) }}&text={{ $whatsAppText }}" class="btn btn-success"><i class="fab fa-whatsapp mr-2"></i>{{ $unit->name }}</a>
            @endif
            @endforeach
          </div>
        </div>
        @endif
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light mr-1" data-dismiss="modal">Tidak{{ $units && count($units) > 0 ? ', Terima Kasih' : null }}</button>
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('vendor/bootstrap-touchspin/js/jquery.bootstrap-touchspin.js') }}"></script>

<script src="{{asset('js/wilayah.js')}}"></script>
<script>
    $('#problem-confirm').modal('show');
</script>
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
    $(document).ready(function(){
        $('select[name="siswa"]').on('change', function() {
            var asal_sekolah = this.value;
            var punya = false;
            if(asal_sekolah == 1){
                punya = true;
            }
            if(punya == true){
                $('div[id="nipd_siswa"]').show();
                $('div[id="ortu"]').hide();
                $('.select-exist').attr('required','required');
                $('.select-new').removeAttr('required','required');
            }else{
                $('div[id="nipd_siswa"]').hide();
                $('div[id="ortu"]').show();
                $('.select-new').attr('required','required');
                $('.select-exist').removeAttr('required','required');
            }
        });
    });
</script>

@include('template.footjs.psb.pendaftaran')
@endsection