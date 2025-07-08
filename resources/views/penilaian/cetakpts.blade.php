@extends('template.main.master')

@section('title')
Cetak Rapor PTS
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cetak Rapor Penilaian Tengah Semester</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cetak Rapor PTS</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Tahun Pelajaran</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" value="{{$semester->semester_id . ' (' .$semester->semester.')'}}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                <input type="text" name="rombel_id" class="form-control" id="rombel" style="width:100%;" value="{{$kelas->level->level.' '.$kelas->namakelases->class_name}}" tabindex="-1" aria-hidden="true" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th colspan="2" class="text-center">Cetak Rapor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($siswa->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">Data Kosong</td>
                                </tr>
                                @else
                                @foreach ($siswa as $key => $siswas)
                                <tr>
                                    <td>{{$siswas->identitas->student_name}}</td>
                                    @if($nilairapor[$key] && $nilairapor[$key]->report_status_pts_id == 1)
                                    <td class="text-center"><a href="{{ route('pts.cetak.cover',['id'=>$siswas->id])}}" target="_blank"><button class="btn btn-brand-green"><i class="fa fa-print"></i> Cetak Cover</button></a></td>
                                    @if($siswas->unit_id == 1)
                                    <td class="text-center">
                                        <div class="btn-group">
                                          <a href="{{ route('pts.cetak.laporantk',['id'=>$siswas->id])}}" class="btn btn-brand-green" target="_blank"><i class="fa fa-print"></i> Cetak Laporan PTS</a>
                                          <button type="button" class="btn btn-brand-green dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Lainnya</span>
                                          </button>
                                          <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('pts.cetak.laporantk',['id'=>$siswas->id,'digital'=>1])}}" target="_blank">Cetak Tanpa TTD</a>
                                          </div>
                                        </div>
                                    </td>
                                    @else
                                    <td class="text-center">
                                        <div class="btn-group">
                                          <a href="{{ route('pts.cetak.laporan',['id'=>$siswas->id])}}" class="btn btn-brand-green" target="_blank"><i class="fa fa-print"></i> Cetak Laporan PTS</a>
                                          <button type="button" class="btn btn-brand-green dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="sr-only">Lainnya</span>
                                          </button>
                                          <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ route('pts.cetak.laporan',['id'=>$siswas->id,'digital'=>1])}}" target="_blank">Cetak Tanpa TTD</a>
                                          </div>
                                        </div>
                                    </td>
                                    @endif
                                    @else
                                    <td colspan="2" class="text-center">
                                        <h5><span class="badge badge-warning">Nilai Belum Divalidasi</span></h5>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
@endsection