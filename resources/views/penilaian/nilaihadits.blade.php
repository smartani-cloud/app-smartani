@extends('template.main.master')

@section('title')
Hadits & Doa
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
    <h1 class="h3 mb-0 text-gray-800">Nilai Hadits dan Doa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nilai Hadits dan Doa</li>
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
                                <input type="hidden" id="semester_id" value="{{$semester->id}}" />
                                <input type="text" class="form-control" value="{{$semester->semester_id . ' (' .$semester->semester.')'}}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Mata Pelajaran</label>
                            <div class="col-sm-9">
                                <select name="mapel" class="form-control" id="mapel" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                    @if($agama != NULL)
                                    <option value="{{$agama->id}}">{{$agama->subject_name}}</option>
                                    @else
                                    <option value="">Data Kosong</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="getlevel">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-9">
                                <select name="kelas" onchange="getkelas(this.value)" class="form-control" id="level" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tingkat Kelas ==</option>
                                    @foreach ($level as $levels)
                                    <option value="{{$levels->id}}">{{$levels->level}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="getkelas"></div>
                        <div id="getsiswa"></div>
                    </div>
                    <div class="col-md-12" id="getHafalan">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<script type="text/javascript">
    function getkelas(id) {
        var mapel = $('#mapel').val();
        $('#getkelas').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('hadits.getkelas') }}",
            data: {
                'mapel_id': mapel,
                'level_id': id
            },
            type: 'POST',
            success: function(response) {
                $('#getkelas').html(response.html);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }

    function getsiswa(id) {
        var smt_id = $('#semester_id').val();
        $('#getsiswa').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('hadits.getsiswa') }}",
            data: {
                'class_id': id
            },
            type: 'POST',
            success: function(response) {
                $('#getsiswa').html(response.html);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }

    function getHafalan(id) {
        $('#getHafalan').html("");
        var kelas = $('#kelas').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('hadits.getHadits') }}",
            data: {
                'siswa_id': id,
                'class_id': kelas
            },
            type: 'POST',
            success: function(response) {
                $('#getHafalan').html(response.html);
                $('#idkelas').val(kelas);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
@endsection