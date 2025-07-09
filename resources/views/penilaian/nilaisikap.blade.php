@extends('template.main.master')

@section('title')
Nilai Sikap
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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nilai Sikap</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nilai Sikap</li>
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
                        <form action="{{route('nilaisikap.simpan')}}" method="POST">
                            @csrf
                            <table class="table align-items-center table-flush">
                                <thead class="bg-brand-green text-white">
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th class="text-center">Sikap Spiritual</th>
                                        <th class="text-center">Sikap Sosial</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($siswa)
                                    <?php $i = 0; ?>
                                    @foreach ($siswa as $siswa)
                                    <tr>
                                        <td>{{$siswa->identitas->student_name}}</td>
                                        <td>

                                            <!-- Buat Cek Data Sebelumnya klo masih butuh -->

                                            <!--   

                                            <select class="col-sm-4 form-control" disabled>
                                                @if($rpd)
                                                <option value="">== Sudah Menggunakan Data Baru ==</option>
                                                @foreach($rpd as $spiritual)
                                                <option value="{{$spiritual->id}}" <?php if ($nilaispiritual[$i] != NULL) {
                                                                                        if ($nilaispiritual[$i]->rpd_id == $spiritual->id) {
                                                                                            echo 'selected';
                                                                                        }
                                                                                    } ?>>
                                                    {{$spiritual->predicate}} (Data Sebelumnya)
                                                </option>
                                                @endforeach
                                                @else
                                                <option value="">Data Kosong</option>
                                                @endif
                                            </select> 

                                            -->


                                            <select name="spiritual[]" class="form-control">
                                                @if($rpd_spiritual)
                                                <option value="">== Pilih ==</option>
                                                @foreach($rpd_spiritual as $spiritual)
                                                <option value="{{$spiritual->id}}" <?php if ($nilaispiritual[$i] != NULL) {
                                                                                        if ($nilaispiritual[$i]->rpd_id == $spiritual->id) {
                                                                                            echo 'selected';
                                                                                        }
                                                                                    } ?>>
                                                    {{$spiritual->predicate}}
                                                </option>
                                                @endforeach
                                                @else
                                                <option value="">Data Kosong</option>
                                                @endif
                                            </select>
                                        </td>
                                        <td>

                                        <!-- Kalo Pengen Liat data lama -->
                                        
                                        <!-- 

                                            <select class="form-control" disabled>
                                                @if($rpd)
                                                <option value="">== Sudah Menggunakan Data Baru ==</option>
                                                @foreach($rpd as $sosial)
                                                <option value="{{$sosial->id}}" <?php if ($nilaisosial[$i] != NULL) {
                                                                                    if ($nilaisosial[$i]->rpd_id == $sosial->id) {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                                    {{$sosial->predicate}} (Data Sebelumnya)
                                                </option>
                                                @endforeach
                                                @else
                                                <option value="">Data Kosong</option>
                                                @endif
                                            </select>

                                         -->
                                            <select name="sosial[]" class="form-control">
                                                @if($rpd_sosial)
                                                <option value="">== Pilih ==</option>
                                                @foreach($rpd_sosial as $sosial)
                                                <option value="{{$sosial->id}}" <?php if ($nilaisosial[$i] != NULL) {
                                                                                    if ($nilaisosial[$i]->rpd_id == $sosial->id) {
                                                                                        echo 'selected';
                                                                                    }
                                                                                } ?>>
                                                    {{$sosial->predicate}}
                                                </option>
                                                @endforeach
                                                @else
                                                <option value="">Data Kosong</option>
                                                @endif
                                            </select>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="3" class="text-center">Data Kosong</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>

                            @if($siswa)
                            @if($countrapor > 0)
                            @if($validasi > 0)
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                            </div>
                            @endif
                            @else
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
                            </div>
                            @endif
                            @endif
                        </form>
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