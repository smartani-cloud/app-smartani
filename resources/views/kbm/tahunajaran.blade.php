<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Tahun Pelajaran
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tahun Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tahun Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Tahun Pelajaran Sekarang</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ is_null($aktif)?'Belum diatur':$aktif->academic_year}}" readonly="">
                            </div>
                        </div>
                        @if( in_array((auth()->user()->role_id), array(1,13)))
                        <form action="tahun-ajaran/ubah"  method="POST">
                        @csrf
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Ubah Tahun Pelajaran</label>
                            <div class="col-sm-9">
                                <select name="tahun" class="select2 form-control select2-hidden-accessible auto_width" id="tahun" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tahun Ajaran ==</option>
                                    @foreach( $tahuns as $tahun )
                                    @if( $tahun->is_active==0 )
                                        <option value="{{ $tahun->id }}">{{ $tahun->academic_year }}</option>
                                    @else
                                        <option value="{{ $tahun->id }}" selected>{{ $tahun->academic_year }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green"></h6>
                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="" data-toggle="modal" data-target="#TambahModal">Tambah Tahun Pelajaran <i class="fas fa-plus"></i></a>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                        </div>
                        </form>
                        @endif
                    </div>
                    <div class="col-md-12">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Semester Aktif</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ is_null($smsaktif)?'Belum diatur':$smsaktif->semester_id.' ('.$smsaktif->semester.')'}}" readonly="">
                            </div>
                        </div>
                        @if( in_array((auth()->user()->role_id), array(1,13)))
                        <form action="tahun-ajaran/ubah-semester"  method="POST">
                        @csrf
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Ubah Semester Aktif</label>
                            <div class="col-sm-9">
                                <select name="semester" class="select2 form-control select2-hidden-accessible auto_width" id="tahun" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Semester ==</option>
                                    @foreach( $semesters as $semester )
                                    @if( $semester->is_active==0 )
                                        <option value="{{ $semester->id }}">{{ $semester->semester_id }} ({{$semester->semester}})</option>
                                    @else
                                        <option value="{{ $semester->id }}" selected>{{ $semester->semester_id }} ({{$semester->semester}})</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                        </div>
                        </form>
                        @endif
                    </div>
                    <div class="col-md-12">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="tahun-ajaran/tambah"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Tambah Tahun Ajaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="academic_year_start" class="col-sm-3 control-label">Tahun Ajaran</label>
                    <div class="col-sm-4">
                        <input type="number" name="academic_year_start" class="form-control">
                    </div>
                    <p>/</p>
                    <div class="col-sm-4">
                        <input type="number" name="academic_year_end" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
=======
@extends('template.main.master')

@section('title')
Tahun Pelajaran
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tahun Pelajaran</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tahun Pelajaran</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Tahun Pelajaran Sekarang</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ is_null($aktif)?'Belum diatur':$aktif->academic_year}}" readonly="">
                            </div>
                        </div>
                        @if( in_array((auth()->user()->role_id), array(1,13)))
                        <form action="tahun-ajaran/ubah"  method="POST">
                        @csrf
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Ubah Tahun Pelajaran</label>
                            <div class="col-sm-9">
                                <select name="tahun" class="select2 form-control select2-hidden-accessible auto_width" id="tahun" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tahun Ajaran ==</option>
                                    @foreach( $tahuns as $tahun )
                                    @if( $tahun->is_active==0 )
                                        <option value="{{ $tahun->id }}">{{ $tahun->academic_year }}</option>
                                    @else
                                        <option value="{{ $tahun->id }}" selected>{{ $tahun->academic_year }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green"></h6>
                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="" data-toggle="modal" data-target="#TambahModal">Tambah Tahun Pelajaran <i class="fas fa-plus"></i></a>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                        </div>
                        </form>
                        @endif
                    </div>
                    <div class="col-md-12">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Semester Aktif</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{ is_null($smsaktif)?'Belum diatur':$smsaktif->semester_id.' ('.$smsaktif->semester.')'}}" readonly="">
                            </div>
                        </div>
                        @if( in_array((auth()->user()->role_id), array(1,13)))
                        <form action="tahun-ajaran/ubah-semester"  method="POST">
                        @csrf
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Ubah Semester Aktif</label>
                            <div class="col-sm-9">
                                <select name="semester" class="select2 form-control select2-hidden-accessible auto_width" id="tahun" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Semester ==</option>
                                    @foreach( $semesters as $semester )
                                    @if( $semester->is_active==0 )
                                        <option value="{{ $semester->id }}">{{ $semester->semester_id }} ({{$semester->semester}})</option>
                                    @else
                                        <option value="{{ $semester->id }}" selected>{{ $semester->semester_id }} ({{$semester->semester}})</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                        </div>
                        </form>
                        @endif
                    </div>
                    <div class="col-md-12">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

        <form action="tahun-ajaran/tambah"  method="POST">
        @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Tambah Tahun Ajaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label for="academic_year_start" class="col-sm-3 control-label">Tahun Ajaran</label>
                    <div class="col-sm-4">
                        <input type="number" name="academic_year_start" class="form-control">
                    </div>
                    <p>/</p>
                    <div class="col-sm-4">
                        <input type="number" name="academic_year_end" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Tambah</button>
            </div>
        </form>
    </div>
  </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection