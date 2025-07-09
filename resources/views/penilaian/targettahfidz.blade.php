@extends('template.main.master')

@section('title')
Pengaturan Target Tahfidz
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
    <h1 class="h3 mb-0 text-gray-800">Pengaturan Target Tahfidz</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Target Tahfidz</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="form-group row">
                    <label for="ajaran_id" class="col-sm-3 control-label">Tahun Pelajaran</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" value="{{$semester->semester_id . ' (' .$semester->semester.')'}}" disabled>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="kelas_id" class="col-sm-3 control-label">Tingkat Kelas</label>
                    <div class="col-sm-6">
                        @if($level->isEmpty())
                        <select class="form-control" name="level" required>
                            <option value="">Data Kosong</option>
                        </select>
                        @else
                        <select class="form-control" name="level" id="idlevel" onchange="gettarget(this.value)" id="kelas" required>
                            <option value="">== Pilih ==</option>
                            @foreach($level as $levels)
                            <option value="{{$levels->id}}">{{$levels->level}}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="gettarget"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<script>
    function gettarget(id) {
        $('#gettarget').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('targettahfidz.gettarget') }}",
            data: {
                'level_id': id
            },
            type: 'POST',
            success: function(response) {
                $('#gettarget').html(response.html);
                $('#idlevelsubmit').val(id);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
@endsection