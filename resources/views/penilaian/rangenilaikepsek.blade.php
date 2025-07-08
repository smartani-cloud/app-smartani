@extends('template.main.master')

@section('title')
Pengaturan Range Nilai Predikat
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
    <h1 class="h3 mb-0 text-gray-800">Pengaturan Range Nilai Predikat</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Range Nilai Predikat</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <form action="{{route('rangekepsek.simpan')}}" method="POST">
                            @csrf
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
                                    <select name="mapel" class="form-control" id="idmapel" onchange="getlevel(this.value)" style="width:100%;" tabindex="-1" aria-hidden="true">
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
                            <div class="form-group row" id="getlevel"></div>
                            <div class="card-footer">
                                <div class="alert alert-secondary" role="alert">
                                    <strong>Catatan</strong> : Dikarenakan penggunaan sistem pembulatan, harap pengisian nilai <i>range</i> predikat dikurangi 0.5.<br>Sebagai contoh: Nilai A adalah lebih sama dengan <strong>85</strong>, 
                                    maka yang dimasukkan adalah <strong>84.5 .</strong><br>Penulisan angka menggunakan <strong>TITIK</strong> dan bukan <strong>KOMA.</strong>
                                </div>
                            </div>
                            <div id="getkd">&nbsp;</div>
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
<script>
    function getlevel(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('rangekepsek.getlevel') }}",
            data: {
                'mapel_id': id
            },
            type: 'POST',
            success: function(response) {
                $('#getlevel').html(response.html);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }

    function getkd(id) {
        var mapel_id = $('#idmapel').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('rangekepsek.getrange') }}",
            data: {
                'level_id': id,
                'mapel_id': mapel_id
            },
            type: 'POST',
            success: function(response) {
                $('#getkd').html(response.html);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
@endsection