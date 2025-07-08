@extends('template.main.master')

@section('title')
Kurikulum IKLaS
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
    <h1 class="h3 mb-0 text-gray-800">Kurikulum IKLaS</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">IKLaS</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
				@if(!$rpd || ($rpd && (implode(',',$rpd->pluck('predicate')->toArray()) != '1,2,3,4,5')))
				<div class="alert alert-light" role="alert">
                    <i class="fa fas fa-exclamation-triangle text-warning mr-2"></i><strong>Perhatian!</strong> Predikat dan deskripsi nilai IKLaS belum diisi dengan lengkap. <a href="{{ route('predikat.iklas.index') }}" class="text-info font-weight-bold text-decoration-none">Atur sekarang</a>
                </div>
				@endif
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
                        <div class="form-group row">
                            <label for="rombel" class="col-sm-3 control-label">Nama Siswa</label>
                            <div class="col-sm-9">
                                <select name="nama_siswa" onchange="getNilaiIklas(this.value)" class="form-control" id="nama_siswa" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    @if ($siswa)
                                    <option value="">== Pilih ==</option>
                                    @foreach ($siswa as $siswa)
                                    <option value="{{$siswa->id}}">{{$siswa->identitas->student_name}}</option>
                                    @endforeach
                                    @else
                                    <option value="">Data Kosong</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div id="getNilaiIklas"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<script type="text/javascript">
    function getNilaiIklas(id) {
        $('#getNilaiIklas').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('iklas.getNilai') }}",
            data: {
                'siswa_id': id
            },
            type: 'POST',
            success: function(response) {
                $('#getNilaiIklas').html(response.html);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
@endsection