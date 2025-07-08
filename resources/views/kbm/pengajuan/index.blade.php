@extends('template.main.master')

@section('title')
Pengajuan Kelas
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Kelas</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pengajuan Kelas</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Pengajuan Kelas{{ !$kelases->isEmpty()?'':' : Tidak ada pengajuan kelas' }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                @if (!$kelases->isEmpty())
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Kelas</th>
                                <th>Nama Kelas</th>
                                <th>Wali Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $kelases as $index => $kelas)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $kelas->level->level }}</td>
                                <td>{{ $kelas->namakelases->class_name }}</td>
                                <td>{{ $kelas->walikelas ? $kelas->walikelas->name : '-' }}</td>
                                <td>
                                    <a href="/kependidikan/kbm/kelas/pengajuan-kelas/lihat/{{$kelas->id}}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                </div>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Kelas yang telah disetujui{{ !$setuju->isEmpty()?'':' : Tidak ada' }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                @if (!$setuju->isEmpty())
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Kelas</th>
                                <th>Nama Kelas</th>
                                <th>Wali Kelas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach( $setuju as $index => $kelas)
                            <tr>
                                <td>{{ $index+1 }}</td>
                                <td>{{ $kelas->level->level }}</td>
                                <td>{{ $kelas->namakelases->class_name }}</td>
                                <td>{{ $kelas->walikelas ? $kelas->walikelas->name : '-' }}</td>
                                <td>
                                    <a href="/kependidikan/kbm/kelas/pengajuan-kelas/lihat/{{$kelas->id}}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                </div>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>


<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Kelas yang belum mengajukan{{ !$tunggu->isEmpty()?'':' : Tidak ada' }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
            @if (!$tunggu->isEmpty())
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Wali Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach( $tunggu as $index => $kelas)
                        <tr>
                            <td>{{ $index+1 }}</td>
                            <td>{{ $kelas->level->level }}</td>
                            <td>{{ $kelas->namakelases->class_name }}</td>
                            <td>{{ $kelas->walikelas ? $kelas->walikelas->name : '-' }}</td>
                            <td>
                                <a href="/kependidikan/kbm/kelas/pengajuan-kelas/lihat/{{$kelas->id}}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            </div>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>



<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>

@include('template.footjs.kbm.datatables')
@endsection