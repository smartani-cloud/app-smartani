@extends('template.main.psb.master')

@section('title')
Ubah Password
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
            <h6 class="m-0 font-weight-bold text-white">Ubah Password</h6>
        </div>
            <div class="card-body">
                <form action="{{route('psb.password.post')}}"  method="POST">
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
                        @if(Session::has('danger'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>{{ Session::get('danger') }}!</strong> 
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label for="nama_ayah" class="col-sm-2 control-label">Password Lama<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="password" class="form-control" name="old_password" placeholder="Password Lama">
                                <span class="text-danger old-password"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ayah" class="col-sm-2 control-label">Password Baru<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="password" class="form-control" name="new_password" placeholder="Password Baru">
                                <span class="text-danger new-password"></span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nama_ayah" class="col-sm-2 control-label">Ulangi Password Baru<span class="text-danger">*</span></label>
                            <div class="col-sm-3">
                                <input type="password" class="form-control" name="new_password_repeat" placeholder="Ulangi Password Baru">
                                <span class="text-danger repeat-password"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="text-center mt-4">
                            <button type="submit" disabled class="btn btn-brand-green-dark">Simpan</button>
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

<script>

    function checkPassword(){
        var old_password = $('input[name="old_password"]').val();
        var new_password = $('input[name="new_password"]').val();
        var new_password_repeat = $('input[name="new_password_repeat"]').val();

        if(old_password && new_password && new_password_repeat){
            if(new_password == old_password){
                $('.old-password').text('');
                $('.new-password').text('Password baru tidak boleh sama');
                $('.repeat-password').text('');
                $('button[type="submit"]').prop('disabled', true);
            }else if(new_password == new_password_repeat){
                $('.old-password').text('');
                $('.new-password').text('');
                $('.repeat-password').text('');
                $('button[type="submit"]').prop('disabled', false);
            }else{
                $('.old-password').text('');
                $('.new-password').text('');
                $('.repeat-password').text('Ulangi password baru tidak sama');
                $('button[type="submit"]').prop('disabled', true);
            }
        }else{
            $('button[type="submit"]').prop('disabled', true);
            if(old_password){
                $('.old-password').text('');
            }else{
                $('.old-password').text('Wajib diisi');
            }
            if(new_password){
                $('.new-password').text('');
            }else{
                $('.new-password').text('Wajib diisi');
            }
            if(new_password_repeat){
                $('.repeat-password').text('');
            }else{
                $('.repeat-password').text('Wajib diisi');
            }
        }
    }

    $(document).ready(function(){
        $('input[name="old_password"]').on('input', function() {
            checkPassword();
        });
        $('input[name="new_password"]').on('input', function() {
            checkPassword();
        });
        $('input[name="new_password_repeat"]').on('input', function() {
            checkPassword();
        });
    });
    
</script>

<!-- Page level custom scripts -->
@include('template.footjs.kepegawaian.rtrw')


@endsection