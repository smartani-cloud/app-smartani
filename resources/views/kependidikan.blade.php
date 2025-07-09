@extends('template.main.master')

@section('title')
Dasbor
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Beranda</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Beranda</li>
    </ol>
</div>
<!-- Semua -->
<div class="row mb-3">
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Siswa ({{$namaunit}})</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jmlsiswacewe + $jmlsiswalaki}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if($unit == 5)
<!-- TK -->
<div class="row mb-3">
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Siswa TK</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jmlsiswacewe1 + $jmlsiswalaki1}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Siswa SD</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jmlsiswalaki2 + $jmlsiswacewe2}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Siswa SMP</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jmlsiswalaki3 + $jmlsiswacewe3}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Siswa SMA</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{$jmlsiswalaki4 + $jmlsiswacewe4}}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
<!--Row-->


<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <form action="/{{Request::path()}}" method="GET">
                        {{-- <form action="/kependidikan/psb/{{$link}}" method="POST"> --}}
                        @csrf
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Unit</label>
                                <div class="col-sm-5">
                                    <select name="unit" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">Semua</option>
                                        <?php 
                                            $units = \App\Http\Services\Kbm\UnitSelector::listUnit();
                                        ?>
                                        @foreach( $units as $unit)
                                            <option value="{{$unit->name}}" {{$request->unit==$unit->name?'selected':''}}>{{$unit->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="kelas" class="col-sm-3 control-label">Tahun Ajaran</label>
                                <div class="col-sm-5">
                                    <select name="year" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                        <option value="">Semua</option>
                                        <?php 
                                            $years = \App\Http\Services\Kbm\AcademicYearSelector::activeToNext();
                                        ?>
                                        @foreach ($years as $year)
                                            <option value="{{$year->academic_year_start}}" {{$request->year==$year->academic_year_start?'selected':''}}>{{$year->academic_year}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-brand-green-dark btn-sm" type="submit">Saring</button>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Tabel Pendaftaran Siswa Baru</h6>
                        </div>
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th rowspan="2">Unit</th>
                                    <th rowspan="2">Tahun</th>
                                    <th colspan="3">Sudah Daftar Online</th>
                                    @if(in_array(Auth::user()->role->id,[1,2,3,7,8,9,14,17,18,20,21,25,26]))
                                        <th colspan="3">Biaya Observasi</th>
                                        <th colspan="3">Wawancara</th>
                                    @endif
                                    <th colspan="3">Diterima</th>
                                    <th colspan="3">Lunas DU</th>
                                    @if(in_array(Auth::user()->role->id,[1,2,3,7,8,9,14,17,18,20,21,25,26]))
                                        <th colspan="3">Diresmikan</th>
                                        <th colspan="3">Dicadangkan</th>
                                        <th colspan="3">Pembatalan DU</th>
                                    @endif
                                </tr>
                                <tr>
                                    <th>Internal</th>
                                    <th>Eksternal</th>
                                    <th>Total</th>
                                    @if(in_array(Auth::user()->role->id,[1,2,3,7,8,9,14,17,18,20,21,25,26]))
                                        <th>Internal</th>
                                        <th>Eksternal</th>
                                        <th>Total</th>
                                        <th>Internal</th>
                                        <th>Eksternal</th>
                                        <th>Total</th>
                                    @endif
                                    <th>Internal</th>
                                    <th>Eksternal</th>
                                    <th>Total</th>
                                    <th>Internal</th>
                                    <th>Eksternal</th>
                                    <th>Total</th>
                                    @if(in_array(Auth::user()->role->id,[1,2,3,7,8,9,14,17,18,20,21,25,26]))
                                        <th>Internal</th>
                                        <th>Eksternal</th>
                                        <th>Total</th>
                                        <th>Internal</th>
                                        <th>Eksternal</th>
                                        <th>Total</th>
                                        <th>Internal</th>
                                        <th>Eksternal</th>
                                        <th>Total</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $datas as $index => $data )
                                <tr>
                                    <td>{{$index+1}}</td>
                                    <td>{{$data->unit->name}}</td>
                                    <td>{{$data->tahunAkademik->academic_year}}</td>
                                    <td>{{$data->register_intern}}</td>
                                    <td>{{$data->register_extern}}</td>
                                    <td><b>{{$data->register_intern+$data->register_extern}}</b></td>
                                    @if(in_array(Auth::user()->role->id,[1,2,3,7,8,9,14,17,18,20,21,25,26]))
                                        <td>{{$data->saving_seat_intern}}</td>
                                        <td>{{$data->saving_seat_extern}}</td>
                                        <td><b>{{$data->saving_seat_intern+$data->saving_seat_extern}}</b></td>
                                        <td>{{$data->interview_intern}}</td>
                                        <td>{{$data->interview_extern}}</td>
                                        <td><b>{{$data->interview_intern+$data->interview_extern}}</b></td>
                                    @endif
                                    <td>{{$data->accepted_intern}}</td>
                                    <td>{{$data->accepted_extern}}</td>
                                    <td><b>{{$data->accepted_intern+$data->accepted_extern}}</b></td>
                                    <td>{{$data->reapply_intern}}</td>
                                    <td>{{$data->reapply_extern}}</td>
                                    <td><b>{{$data->reapply_intern+$data->reapply_extern}}</b></td>
                                    @if(in_array(Auth::user()->role->id,[1,2,3,7,8,9,14,17,18,20,21,25,26]))
                                        <td>{{$data->stored_intern}}</td>
                                        <td>{{$data->stored_extern}}</td>
                                        <td><b>{{$data->stored_intern+$data->stored_extern}}</b></td>
                                        <td>{{$data->reserved_intern}}</td>
                                        <td>{{$data->reserved_extern}}</td>
                                        <td><b>{{$data->reserved_intern+$data->reserved_extern}}</b></td>
                                        <td>{{$data->canceled_intern}}</td>
                                        <td>{{$data->canceled_extern}}</td>
                                        <td><b>{{$data->canceled_intern+$data->canceled_extern}}</b></td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
@include('template.footjs.kbm.cetakdatatables')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
@endsection