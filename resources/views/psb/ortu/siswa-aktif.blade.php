@extends('template.main.psb.master')

@section('title')
{{$anak->student_nickname}}
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

<div class="row mb-3">
    
    <div class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">VA Biaya Masuk Sekolah</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$anak->siswas()->latest()->first()->virtualAccount?$anak->siswas()->latest()->first()->virtualAccount->bms_va:'Belum disetting'}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">VA SPP</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$anak->siswas()->latest()->first()->virtualAccount?$anak->siswas()->latest()->first()->virtualAccount->spp_va:'Belum disetting'}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-credit-card fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<!--Row-->
@endsection

@section('footjs')
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/vfs_fonts.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/buttons.html5.min.js') }}"></script>

<!-- Page level custom scripts -->
@endsection