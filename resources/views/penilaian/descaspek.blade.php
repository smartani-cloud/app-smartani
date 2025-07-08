@extends('template.main.master')

@section('title')
Deskripsi Aspek Perkembangan
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('headmeta')
<script src="{{asset('vendor/ckeditor/ckeditor.js')}}"></script>
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
    <h1 class="h3 mb-0 text-gray-800">Deskripsi Aspek Perkembangan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Deskripsi Aspek Perkembangan</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
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
                        <select class="form-control" name="level" id="idlevel" id="kelas" readonly>
                            <option value="">== Pilih ==</option>
                            @foreach($level as $levels)
                            <option value="{{$levels->id}}" <?php if ($kelasampu->level_id == $levels->id) echo "selected"; ?>>{{$levels->level}}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <a class="m-0 float-right btn btn-brand-green-dark btn-sm mb-2" href="javascript:void(0)" data-toggle="modal" data-target="#TambahModal" onclick="levelid()">Tambah <i class="fas fa-plus"></i></a>
                            </div>
                        </div>
                        <table class="table align-items-center table-flush" id="tabelaspek">
                            <thead class="thead-light">
                                <tr>
                                    <th>Aspek Perkembangan</th>
                                    <th>Predikat</th>
                                    <th>Deskripsi</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($desc->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center">Data Kosong</td>
                                </tr>
                                @else
                                @foreach ($desc as $descs)
                                <tr>
                                    <td>
                                        {{$descs->aspek->dev_aspect}}
                                    </td>
                                    <td>
                                        {{$descs->predicate}}
                                    </td>
                                    <td>
                                        {!! $descs->description !!}
                                    </td>
                                    <td class="text-right">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal" onclick="ubah(<?php echo $descs->id; ?>)"><i class="fas fa-pen"></i></a>
                                        &nbsp;<a href="javascript:void(0)" data-toggle="modal" data-target="#HapusModal" onclick="hapus(<?php echo $descs->id; ?>)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
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
<!--Row-->

<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form action="{{route('descaspek.tambah')}}" method="POST">
                @csrf
                <input type="hidden" name="level_id" id="idlevelsubmit" />
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Tambah Deskripsi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-12 control-label">Aspek Perkembangan</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="aspek_id" required>
                                <option value="">== Pilih ==</option>
                                @foreach ($aspek as $aspeks)
                                <option value="{{$aspeks->id}}">{{$aspeks->dev_aspect}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-12 control-label">Predikat</label>
                        <div class="col-sm-12">
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
                        <label for="predikat" class="col-sm-12 control-label">Deskripsi</label>
                        <div class="col-sm-12">
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <textarea name="deskripsi" rows="10" class="ckeditor form-control" id="tambahdesc" required></textarea>
                                </div>
                            </div>
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

            <form action="{{route('descaspek.ubah')}}" method="POST">
                @csrf
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Ubah Deskripsi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="getubah">
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
                <p>Apakah Anda yakin akan menghapus data tersebut?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('descaspek.hapus')}}" method="POST">
                    @csrf
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footjs')
<script type="text/javascript">
    function ubah(id, deskripsi, predikat, aspek) {

        $('#getubah').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('descaspek.getubahdesc') }}",
            data: {
                'id': id
            },
            type: 'POST',
            success: function(response) {
                $('#getubah').html(response.html);
                CKEDITOR.replace('ubahdesc');
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }

    function hapus(id) {
        $('#hapusid').val(id);
    }

    function levelid() {
        var idlevel = $('#idlevel').val();
        $('#idlevelsubmit').val(idlevel);
    }

    function getdesc(id) {
        $('#getdesc').html("");
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ route('descaspek.getdesc') }}",
            data: {
                'level_id': id
            },
            type: 'POST',
            success: function(response) {
                $('#getdesc').html(response.html);
                $('#idlevelsubmit').val(id);
                $('#tabelaspek').DataTable({
                    "columnDefs": [{
                        "width": "12%",
                        "targets": 3
                    }]
                });
            },
            error: function(xhr, textStatus, thrownError) {
                alert(xhr + "\n" + textStatus + "\n" + thrownError);
            }
        });
    }
</script>

<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
@endsection