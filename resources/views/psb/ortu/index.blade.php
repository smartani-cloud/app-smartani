@extends('template.main.psb.master')

@section('title')
Pendaftaran Siswa Baru
@endsection

@section('sidebar')
@include('template.sidebar.ortu.ortu')
@endsection

@section('content')
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        @if(Auth::user()->created_at == Auth::user()->updated_at)
                        <div class="alert alert-info show" role="alert">
                            <ul class="mb-0">
                                <li>Username : {{ Auth::user()->username }}</li>
                                <li>Password : Gunakan Nomor Handphone Ayah</li>
                            </ul>
                        </div>
                        @endif
                        <div class="alert alert-light show" role="alert">
                            <i class="fa fa-info-circle text-info mr-2"></i><strong>Info:</strong> Untuk melakukan pendaftaran siswa, silakan klik fitur <a href="{{ route('psb.siswa.create') }}" class="text-info font-weight-bold">pendaftaran calon siswa</a>
                        </div>
                        @if(Session::has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> {{ Session::get('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        @if(Session::has('danger'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          <strong>Gagal!</strong> {{ Session::get('danger') }}
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        @endif
                        <img src="{{asset('img/psb/welcome.jpeg')}}"  class="img-fluid" alt="Responsive image">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Page level custom scripts -->
@endsection