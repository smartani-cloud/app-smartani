@extends('template.main.master')

@section('title')
Referensi Ijazah
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
    <h1 class="h3 mb-0 text-gray-800">Referensi Ijazah</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Ref Ijazah</li>
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
                                <select class="form-control" name="semester_id" id="semester_id" disabled>
                                    <option value="{{$semester->id}}">{{$semester->semester_id . ' (' .$semester->semester.')'}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-9">
                                <select name="kelas" class="form-control" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true" disabled>
                                    <option value="{{$level->id}}">{{$level->level}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Nama Kelas</label>
                            <div class="col-sm-9">
                                @if($kelas->isEmpty())
                                <select class="form-control" name="kelas" required>
                                    <option value="">Data Kosong</option>
                                </select>
                                @else
                                <select class="form-control" name="kelas" onchange="getsiswa(this.value)" id="kelas" required>
                                    <option value="">== Pilih ==</option>
                                    @foreach($kelas as $kelases)
                                    <option value="{{$kelases->id}}">{{$kelases->level->level.' '.$kelases->namakelases->class_name}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                        </div>
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
                <form action="{{route('refijazah.validasi')}}" method="POST">
                    @csrf
                    <input type="hidden" id="class_id" name="class_id">
                    <button type="submit" class="btn btn-success" style="background-color: #66bb6a;">Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footjs')
<script type="text/javascript">
    function getsiswa(id) {
        var smt_id = $('#semester_id').val();
        $('#getsiswa').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('refijazah.getsiswa') }}",
            data: {
                'class_id': id,
                'semester_id': smt_id
            },
            type: 'POST',
            success: function(response) {
                $('#getsiswa').html(response.html);
                $('#class_id').val(id);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
@endsection