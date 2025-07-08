@extends('template.main.master')

@section('title')
Nilai Pengetahuan
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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Nilai Pengetahuan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nilai Pengetahuan</li>
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
                                <select name="semester" id="semester_id" class="form-control" onchange="getmapel()" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Semester ==</option>
                                    @if($semester->isEmpty() == FALSE)
                                    @foreach ($semester as $semesters)
                                    <option value="{{$semesters->id}}">{{$semesters->semester_id . " " . $semesters->semester}}</option>
                                    @endforeach
                                    @else
                                    <option value="">Data Kosong</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="getmapel">
                            <label for="kelas" class="col-sm-3 control-label">Mata Pelajaran</label>
                            <div class="col-sm-9">
                                <select name="mapel" class="form-control" id="mapel" onchange="getlevel(this.value)" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Mata Pelajaran ==</option>
                                    @if($mapel != NULL)
                                    @foreach ($mapel as $mapels)
                                    <option value="{{$mapels->id}}">{{$mapels->subject_name}}</option>
                                    @endforeach
                                    @else
                                    <option value="">Data Kosong</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="getlevel">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-9">
                                <select name="kelas" onchange="getkelas(this.value)" id="level" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tingkat Kelas ==</option>
                                    @foreach ($level as $levels)
                                    <option value="{{$levels->id}}">{{$levels->level}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="getkelas"></div>
                    </div>
                    <hr>
                    <div class="col-md-12" id="getsiswa">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<script>
    $(document).ready(function() {
        $('#getmapel').hide();
        $('#getlevel').hide();
        $('#example').DataTable({
            searching: false,
            paging: false,
            info: false
        });
    });

    function getmapel(id) {
        if (id != "") {
            $('#getmapel').show();
        }
    }

    function getlevel(id) {
        if (id != "") {
            $('#getlevel').show();
        }
    }

    function getkelas(id) {
        $('#getkelas').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('npkepsek.getkelas') }}",
            data: {
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
        var mapel = $('#mapel').val();
        var level = $('#level').val();
        $('#getsiswa').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('npkepsek.getsiswa') }}",
            data: {
                'class_id': id,
                'semester_id': smt_id,
                'mapel_id': mapel,
                'level_id': level
            },
            type: 'POST',
            success: function(response) {
                $('#getsiswa').html(response.html);
                var kelas = $('#kelas').val();
                $('#idmapel').val(mapel);
                $('#idkelas').val(kelas);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>

<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
@endsection