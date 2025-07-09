@extends('template.main.master')

@section('title')
Nilai Keterampilan
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
    <h1 class="h3 mb-0 text-gray-800">Nilai Keterampilan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nilai Keterampilan</li>
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
                                <select name="kelas" id="level" onchange="getkelas(this.value)" class="form-control" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tingkat Kelas ==</option>
                                    @foreach ($level as $levels)
                                    <option value="{{$levels->id}}">{{$levels->level}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="getkelas"></div>
                        <div class="card-footer">
                            <div class="alert alert-secondary" role="alert">
                                <strong>Catatan</strong> : Harap isikan predikat dan deskripsi dengan lengkap yaitu meliputi predikat A, B, C, dan D pada menu Predikat Keterampilan. Kekurangan dalam pengaturan predikat dan deskripsi akan membuat data tidak tersimpan dan berpengaruh pada tampilan cetak rapor.
                            </div>
                            <div class="alert alert-danger" role="alert">
                                <strong><center>CAUTION!</center></strong><center>Sebelum memasukan data nilai, mohon pastikan Anda masih masuk dalam sistem dan mohon untuk melakukan penyimpanan data nilai secara reguler.</center>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12" id="getsiswa"></div>
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
        $('#getlevel').hide();
        $('#example').DataTable({
            searching: false,
            paging: false,
            info: false
        });
    });

    function getkelas(id) {
        var mapel = $('#mapel').val();
        $('#getkelas').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('pengetahuan.getkelas') }}",
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

    function getlevel(id) {
        if (id != "") {
            $('#getlevel').show();
        }
    }

    function getsiswa(id) {
        var mapel = $('#mapel').val();
        var level = $('#level').val();
        var smt_id = $('#semester_id').val();
        $('#getsiswa').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('keterampilan.getsiswa') }}",
            data: {
                'class_id': id,
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