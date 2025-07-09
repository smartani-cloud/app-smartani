@extends('template.main.master')

@section('title')
Password Verifikasi
@endsection

@section('headjs')
<link href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')

@if ($message = Session::get('error'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Gagal!</strong> {{$message}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@elseif ($message = Session::get('sukses'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Sukses!</strong> {{$message}}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Password Verifikasi</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Password Verifikasi</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Password Verifikasi</h6>
            </div>
            <div class="card-body px-4 pt-4 pb-4">
                <form action="{{ route('pwkepsek.simpan') }}" id="password-form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-lg-10 col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-12">
                                        <label for="normal-input" class="form-control-label">Password Akun</label>
                                    </div>
                                    <div class="col-lg-9 col-md-8 col-12">
                                        <div class="input-group">
                                            <input class="form-control" type="password" name="pw_akun" maxlength="255" required="">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary btn-toggle-visibility" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-10 col-md-12">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-12">
                                        <label for="normal-input" class="form-control-label">Password Verifikasi Baru</label>
                                    </div>
                                    <div class="col-lg-9 col-md-8 col-12">
                                        <div class="input-group">
                                            <input class="form-control" type="password" name="new_pass" minlength="6" maxlength="255" required="">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary btn-toggle-visibility" type="button"><i class="fa fa-eye" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                        <small class="form-text"><i class="fa fa-info-circle"></i> Minimal terdiri dari 6 karakter</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-10 col-md-12">
                            <div class="text-right">
                                <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Password Visibility -->
<script src="{{ asset('js/password-visibility.js') }}"></script>
@endsection