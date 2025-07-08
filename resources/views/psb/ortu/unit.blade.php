@extends('template.main.psb.master')

@section('title')
Pendaftaran Siswa Baru
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
    <h1 class="h3 mb-0 text-gray-800">Pendaftaran Siswa Baru</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pendaftaran Siswa Baru</a></li>
    </ol>
</div>

<div class="row mb-3">
    @foreach($units as $u)
    @if($u->psb_active == 1)
    <!-- Pengisian Formulir -->
    <a href="/psb/pendaftaran-siswa?unit={{$u->name}}" class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Form Pendaftaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$u->name}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
    @else
    <!-- Pengisian Formulir -->
    <div class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Form Pendaftaran</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$u->name}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endforeach

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
            }else{
                $('div[id="asal_sekolah_lain"]').show();
            }
        });
    });
</script>



@endsection