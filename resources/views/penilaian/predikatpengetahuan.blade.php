@extends('template.main.master')

@section('title')
Predikat Pengetahuan
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
    <h1 class="h3 mb-0 text-gray-800">Predikat & Deskripsi Pengetahuan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Predikat Pengetahuan</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Predikat & Deskripsi Nilai Pengetahuan</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group row">
                            <label for="ajaran_id" class="col-sm-3 control-label">Tahun Pelajaran</label>
                            <div class="col-sm-4">
                                <input type="hidden" id="semester_id" value="{{$semester->id}}" />
                                <input type="text" class="form-control" value="{{$semester->semester_id . ' (' .$semester->semester.')'}}" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Mata Pelajaran</label>
                            <div class="col-sm-4">
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
                        <div id="getdesc"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="alert alert-secondary" role="alert">
                    <strong>Catatan</strong> : Harap isikan predikat dan deskripsi dengan lengkap yaitu meliputi predikat A, B, C, dan D. Kekurangan dalam pengaturan predikat dan deskripsi akan berpengaruh pada tampilan cetak rapor.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form action="{{route('predikatpengetahuan.tambah')}}" method="POST">
                @csrf
                <input type="hidden" name="idmapel" id="idmapelsubmit">
                <input type="hidden" name="idlevel" id="idlevelsubmit">
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Tambah Predikat - Deskripsi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Predikat</label>
                        <div class="col-sm-6">
                            <select class="form-control" name="predikat" required>
                                <option value="">== Pilih ==</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Deskripsi</label>
                        <div class="col-sm-8">
                            <textarea type="text" name="deskripsi" class="form-control" maxlength="80" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Ubah -->
<div class="modal fade" id="UbahModal" tabindex="-1" role="dialog" aria-labelledby="UbahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form action="{{route('predikatpengetahuan.ubah')}}" method="POST">
                @csrf
                <input type="hidden" name="id" id="ubahid">
                <input type="hidden" name="idmapel" id="idmapelsubmit">
                <input type="hidden" name="idlevel" id="idlevelsubmit">
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Ubah Predikat - Deskripsi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Predikat</label>
                        <div class="col-sm-6">
                            <!--<input type="text" maxlength="1" name="predikat" id="ubahpredikat" class="form-control" required>-->
                            <select name="predikat" id="ubahpredikat" class="form-control" required>
                                <option value="">== Pilih ==</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Deskripsi</label>
                        <div class="col-sm-8">
                            <textarea type="text" name="deskripsi" id="ubahdeskripsi" class="form-control" maxlength="80" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div id="HapusModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xE5CD;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan menghapus data tersebut?.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('predikatpengetahuan.hapus')}}" method="POST">
                    @csrf
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!--Row-->
@endsection

@section('footjs')
<script>
    function ubah(id, predikat, desc) {
        $('#ubahid').val(id);
        $('#ubahpredikat').val(predikat);
        $('#ubahdeskripsi').val(desc);
    }

    function hapus(id) {
        $('#hapusid').val(id);
    }

    function getlevel(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('predikatpengetahuan.getlevel') }}",
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

    function getdeskripsi(id) {
        var mapel_id = $('#idmapel').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('predikatpengetahuan.getdesc') }}",
            data: {
                'level_id': id,
                'mapel_id': mapel_id
            },
            type: 'POST',
            success: function(response) {
                $('#getdesc').html(response.html);
                var idmapel = $('#idmapel').val();
                var idlevel = $('#idlevel').val();
                $('#idmapelsubmit').val(idmapel);
                $('#idlevelsubmit').val(idlevel);
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>
@endsection