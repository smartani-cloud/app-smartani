@extends('template.main.master')

@section('title')
Ubah Mata Pelajaran
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Ubah Mata Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item"><a href="/kependidikan/kbm/pelajaran/mata-pelajaran">Mata Pelajaran</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ubah</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                @if(Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Gagal!</strong> {{ Session::get('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                <form action="{{ $mapel->id }}"  method="POST">
                @method('PUT')
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="nama_mapel" class="col-sm-4 control-label">Nama Mata Pelajaran</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" name="nama_mapel" placeholder="Nama Mata Pelajaran" value="{{ $mapel->subject_name }} {{ $mapel->is_mulok==1?'(Mulok)':'' }}" disabled>
                            </div>
                        </div>
                        @if($unit !==1)
                        <div class="form-group row">
                            <label for="kode_mapel" class="col-sm-4 control-label">Kode Mata Pelajaran</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control @error('kode_mapel') is-invalid @enderror" name="kode_mapel" placeholder="Kode Mata Pelajaran" value="{{ old('kode_mapel',$mapel->subject_acronym) }}" required="required">
                                @error('kode_mapel')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nomor_mapel" class="col-sm-4 control-label">Nomor Mata Pelajaran</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control @error('nomor_mapel') is-invalid @enderror" name="nomor_mapel" placeholder="Nomor" value="{{ old('nomor_mapel',$mapel->subject_number) }}" min="1" {{ in_array((auth()->user()->role_id), array(1,2)) ? null : 'disabled' }}>
                                @error('nomor_mapel')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @endif
                        @if( in_array((auth()->user()->role_id), array(1,2)))
                        @if($unit == 2)
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-4 control-label">Kelas</label>
                            <div class="col-sm-6">
                                @foreach($levels as $index => $level)
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="customCheck{{ $index+1 }}" name="kelas[]" value="{{ $level->id }}" {{ (old('kelas') && in_array($level->id,old('kelas'))) || (!old('kelas') && $mapellevels->contains($level->id)) ? 'checked' : null }}>
                                    <label class="custom-control-label" for="customCheck{{ $index+1 }}">{{ $level->level }}</label>
                                </div>
                                @endforeach
                                @error('kelas')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @endif
                        @endif
                        @if(in_array($unit,[2,3,4]))
                        <div class="form-group row">
                            <label for="mulok" class="col-sm-4 control-label">Muatan Lokal ?</label>
                            <div class="col-sm-6">
                                <select name="mulok" class="select2 form-control select2-hidden-accessible auto_width @error('mulok') is-invalid @enderror" id="mulok" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="0" {{ old('mulok') == 0 || !$mapel->is_mulok ? 'selected' : null }}>Bukan</option>
                                    <option value="1" {{ old('mulok',$mapel->is_mulok) == 1 ? 'selected' : null }}>Ya</option>
                                </select>
                                @error('mulok')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label for="kmp_id" class="col-sm-4 control-label">Kelompok</label>
                            <div class="col-sm-6">
                                <select name="kmp_id" class="select2 form-control select2-hidden-accessible auto_width @error('kmp_id') is-invalid @enderror" id="kmp_id" style="width:100%;" tabindex="-1" aria-hidden="true" {{ in_array((auth()->user()->role_id), array(1,2)) ? ' required="required"' : 'disabled' }}>
                                    @foreach( $kmplists as $kmp )
                                    <option value="{{ $kmp->id }}" {{ old('kmp_id',$mapel->group_subject_id) == $kmp->id ? 'selected' : null }}>{{ $kmp->group_subject_name }}</option>
                                    @endforeach
                                </select>
                                @error('kmp_id')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @if($unit !==1)
                        <div class="form-group row">
                            <label for="kelompok" class="col-sm-4 control-label">KKM</label>
                            <div class="col-sm-6">
                                <input type="number" class="form-control @error('kkm') is-invalid @enderror" name="kkm" placeholder="KKM" value="{{ old('kkm',($kkm ? $kkm : '75')) }}" min="51" max="100">
                                @error('kkm')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @endif
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
