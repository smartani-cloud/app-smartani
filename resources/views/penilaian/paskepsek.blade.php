<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Nilai Akhir Semester
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
    <h1 class="h3 mb-0 text-gray-800">Nilai Akhir Semester</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nilai Akhir Semester</li>
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
                                <select class="form-control" name="semester_id" id="semester_id">
                                    @foreach($semester->sortBy('semester_id')->all() as $semesters)
                                    @if($semesters->is_active == 1 || ($semesters->is_active != 1 && $semesters->riwayatKelas()->count() > 0))
                                    <option value="{{$semesters->id}}" <?php if ($semesters->id == $semesteraktif->id) echo "selected"; ?>>{{$semesters->semester_id . ' (' .$semesters->semester.')'}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-9">
                                <select name="kelas" onchange="getkelas(this.value)" class="form-control" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tingkat Kelas ==</option>
                                    @foreach ($level as $levels)
                                    <option value="{{$levels->id}}">{{$levels->level}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="getkelas"></div>
                    </div>
                    <div class="col-md-12" id="getsiswa">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->

<!-- Modal Konfirmasi -->
<div id="ConfirmModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box" style="border: 3px solid #66bb6a;">
                    <i class="material-icons" style="color: #66bb6a;">done_all</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan melakukan validasi semua data siswa dikelas tersebut?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('paskepsek.validasi')}}" method="POST">
                    @csrf
                    <input type="hidden" id="class_id" name="class_id">
                    <button type="submit" class="btn btn-success" style="background-color: #66bb6a;">Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Naik Kelas -->
<div id="NaikModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box" style="border: 3px solid #a7248c;">
                    <i class="material-icons" style="color: #a7248c;">publish</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan melakukan validasi kenaikan kelas semua data siswa dikelas tersebut?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('paskepsek.naikkelas')}}" method="POST">
                    @csrf
                    <input type="hidden" id="class_idnaik" name="class_id">
                    <button type="submit" class="btn btn-success" style="background-color: #a7248c;">Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footjs')
<script type="text/javascript">
    function getkelas(id) {
        var smt_id = $('#semester_id').val();
        $('#getkelas').html("");
        $('#getsiswa').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('paskepsek.getkelas') }}",
            data: {
                'level_id': id,
                'semester_id': smt_id
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
            url: "{{ route('paskepsek.getsiswa') }}",
            data: {
                'class_id': id,
                'semester_id': smt_id
            },
            type: 'POST',
            success: function(response) {
                $('#getsiswa').html(response.html);
                $('#class_id').val(id);
                $('#class_idnaik').val(id);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
=======
@extends('template.main.master')

@section('title')
Nilai Akhir Semester
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
    <h1 class="h3 mb-0 text-gray-800">Nilai Akhir Semester</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nilai Akhir Semester</li>
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
                                <select class="form-control" name="semester_id" id="semester_id">
                                    @foreach($semester->sortBy('semester_id')->all() as $semesters)
                                    @if($semesters->is_active == 1 || ($semesters->is_active != 1 && $semesters->riwayatKelas()->count() > 0))
                                    <option value="{{$semesters->id}}" <?php if ($semesters->id == $semesteraktif->id) echo "selected"; ?>>{{$semesters->semester_id . ' (' .$semesters->semester.')'}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-9">
                                <select name="kelas" onchange="getkelas(this.value)" class="form-control" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">== Pilih Tingkat Kelas ==</option>
                                    @foreach ($level as $levels)
                                    <option value="{{$levels->id}}">{{$levels->level}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="getkelas"></div>
                    </div>
                    <div class="col-md-12" id="getsiswa">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->

<!-- Modal Konfirmasi -->
<div id="ConfirmModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box" style="border: 3px solid #66bb6a;">
                    <i class="material-icons" style="color: #66bb6a;">done_all</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan melakukan validasi semua data siswa dikelas tersebut?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('paskepsek.validasi')}}" method="POST">
                    @csrf
                    <input type="hidden" id="class_id" name="class_id">
                    <button type="submit" class="btn btn-success" style="background-color: #66bb6a;">Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Naik Kelas -->
<div id="NaikModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box" style="border: 3px solid #a7248c;">
                    <i class="material-icons" style="color: #a7248c;">publish</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan melakukan validasi kenaikan kelas semua data siswa dikelas tersebut?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('paskepsek.naikkelas')}}" method="POST">
                    @csrf
                    <input type="hidden" id="class_idnaik" name="class_id">
                    <button type="submit" class="btn btn-success" style="background-color: #a7248c;">Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footjs')
<script type="text/javascript">
    function getkelas(id) {
        var smt_id = $('#semester_id').val();
        $('#getkelas').html("");
        $('#getsiswa').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('paskepsek.getkelas') }}",
            data: {
                'level_id': id,
                'semester_id': smt_id
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
            url: "{{ route('paskepsek.getsiswa') }}",
            data: {
                'class_id': id,
                'semester_id': smt_id
            },
            type: 'POST',
            success: function(response) {
                $('#getsiswa').html(response.html);
                $('#class_id').val(id);
                $('#class_idnaik').val(id);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection