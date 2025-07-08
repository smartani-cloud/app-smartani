@extends('template.main.master')

@section('title')
Referensi Nilai Ijazah
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
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
@elseif ($message = Session::get('kurang'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Sukses!</strong> Data berhasil disimpan!
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Peringatan!</strong> {!!$message!!}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Cetak Referensi Nilai Ijazah</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cetak Ref Ijazah</li>
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
                                <input type="text" class="form-control" value="{{$semesteraktif->semester_id . ' (' .$semesteraktif->semester.')'}}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                <input type="text" name="rombel_id" class="form-control" id="rombel" style="width:100%;" value="{{$kelas->level->level.' '.$kelas->namakelases->class_name}}" tabindex="-1" aria-hidden="true" disabled>
                            </div>
                        </div>
                        <hr>
                        <form action="{{route('refijazah.generate')}}" method="POST">
                            <div class="form-group row">
                                @csrf
                                <input type="hidden" name="class_id" value="{{$kelas->id}}" />
                                <label for="rombel" class="col-sm-12 control-label font-weight-bold">Nilai Rapor</label>
                                <div class="col-sm-12"><label for="select2Multiple">Semester Range</label></div>
                                <div class="col-sm-6">
                                    <select class="select2-multiple form-control" name="semester[]" multiple="multiple" id="select2Multiple" required>
                                        @foreach($semester as $semesters)
                                        <option value="{{$semesters->id}}">{{$semesters->semester_id.' - ('.$semesters->semester.')'}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <label for="rombel" class="col-sm-12 control-label font-weight-bold">Persentase</label>
                                <div class="col-sm-3">Nilai Rapor</div>
                                <div class="col-sm-3">Nilai Praktek</div>
                                <div class="col-sm-3">Nilai USP</div>
                                <div class="col-sm-3">&nbsp;</div>
                                <div class="col-sm-3">
                                    <input type="number" maxlength="2" name="nilai_akhir" placeholder="Nilai Rapor" class="form-control" style="width:100%;" value="50" tabindex="-1" aria-hidden="true" required>
                                </div>
                                <div class="col-sm-3">
                                    <input type="number" maxlength="2" name="nilai_praktek" placeholder="Nilai Praktek" class="form-control" style="width:100%;" value="25" tabindex="-1" aria-hidden="true" required>
                                </div>
                                <div class="col-sm-3">
                                    <input type="number" maxlength="2" name="nilai_usp" placeholder="Nilai USP" class="form-control" style="width:100%;" value="25" tabindex="-1" aria-hidden="true" required>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" class="btn btn-success h-100">Generate</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th class="text-center">Cetak Referensi Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($siswa)
                                @foreach ($siswa as $key => $siswas)
                                <tr>
                                    <td>{{$siswas->identitas->student_name}}</td>
                                    <td class="text-center">
                                        <?php if ($skhb[$key]) { ?>
                                            @if ($skhb[$key]->report_status_id == 0)
                                            <form action="{{route('refijazah.lihatnilai')}}" target="_blank" method="POST">
                                                @csrf
                                                <input type="hidden" name="wali" value="1">
                                                <input type="hidden" name="id" value="{{$siswas->id}}">
                                                <input type="hidden" name="semester" value="{{$semesteraktif->id}}">
                                                <input type="hidden" name="level_id" value="{{$siswas->kelas->level_id}}">
                                                <input type="hidden" name="major_id" value="{{$siswas->kelas->major_id}}">
                                                <button type="submit" class="btn btn-info btn-sm"><i class="fa fas fa-eye"></i> Lihat Nilai</button>&nbsp;
                                                <a href="{{route('refijazah.regenerate', ['id' => $siswas->id])}}"><button type="button" class="btn btn-success btn-sm"><i class="fa fas fa-cog"></i> Regenerate</button></a>
                                            </form>
                                            @else
                                            <form action="{{route('refijazah.lihatnilai')}}" target="_blank" method="POST">
                                                @csrf
                                                <input type="hidden" name="wali" value="1">
                                                <input type="hidden" name="id" value="{{$siswas->id}}">
                                                <input type="hidden" name="semester" value="{{$semesteraktif->id}}">
                                                <input type="hidden" name="level_id" value="{{$siswas->kelas->level_id}}">
                                                <input type="hidden" name="major_id" value="{{$siswas->kelas->major_id}}">
                                                <button type="submit" class="btn btn-brand-green btn-sm"><i class="fa fas fa-print"></i> Cetak Referensi Ijazah</button>&nbsp;
                                            </form>
                                            @endif
                                        <?php } else { ?>
                                            Data Kosong
                                        <?php } ?>
                                    </td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="2" class="text-center">Data Kosong</td>
                                </tr>
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
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            placeholder: "== Pilih Semester ==",
            allowClear: true
        });
    });
</script>
@endsection