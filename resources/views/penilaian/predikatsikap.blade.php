@extends('template.main.master')

@section('title')
Predikat Sikap
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
    <h1 class="h3 mb-0 text-gray-800">Predikat & Deskripsi Sikap</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Predikat Sikap</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Predikat & Deskripsi Penilaian Sikap</h6>
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#TambahModal">Tambah <i class="fas fa-plus"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Sikap</th>
                            <th>Predikat</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($rpd)
                        @foreach ($rpd as $no => $rpd)
                        <tr>
                            <td>{{$no+1}}</td>
                            <td>{{$rpd->RpdType->rpd_type}}</td>
                            <td>{{$rpd->predicate}}</td>
                            <td>{{$rpd->description}}</td>
                            <td>
                                <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal" onclick="ubah(<?php echo $rpd->id; ?>, '<?php echo $rpd->predicate; ?>', '<?php echo $rpd->description; ?>', '<?php echo $rpd->rpd_type_id; ?>')"><i class="fas fa-pen"></i></a>
                                &nbsp;<a href="javascript:void(0)" data-toggle="modal" data-target="#HapusModal" onclick="hapus(<?php echo $rpd->id; ?>)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="text-center">Data Kosong</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
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

            <form action="{{route('predikatsikap.tambah')}}" method="POST">
                @csrf
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Tambah Predikat - Deskripsi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Predikat</label>
                        <div class="col-sm-2">
                            <!-- <input type="text" maxlength="1" name="predikat" class="form-control"> -->
                            <select name="predikat" class="form-control">
                                <option value=""> </option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Sikap</label>
                        <div class="col-sm-6">
                            <!-- <input type="text" maxlength="1" name="predikat" class="form-control"> -->
                            <select name="sikap" class="form-control">
                                <option value=""></option>
                                <option value="7">Spiritual</option>
                                <option value="8">Sosial</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Deskripsi</label>
                        <div class="col-sm-8">
                            <textarea type="text" name="deskripsi" class="form-control" maxlength="100"></textarea>
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

            <form action="{{route('predikatsikap.ubah')}}" method="POST">
                @csrf
                <input type="hidden" name="id" id="ubahid">
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Ubah Predikat - Deskripsi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Predikat</label>
                        <div class="col-sm-2">
                            <!-- <input type="text" maxlength="1" name="predikat" class="form-control"> -->
                            <select name="predikat" class="form-control">
                                <option value=""> </option>
                                <option id="A" value="A">A</option>
                                <option id="B" value="B">B</option>
                                <option id="C" value="C">C</option>
                                <option id="D" value="D">D</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Sikap</label>
                        <div class="col-sm-6">
                            <!-- <input type="text" maxlength="1" name="predikat" class="form-control"> -->
                            <select name="sikap" class="form-control">
                                <option id="gadasikap" value=""></option>
                                <option id="7" value="7">Spiritual</option>
                                <option id="8" value="8">Sosial</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Deskripsi</label>
                        <div class="col-sm-8">
                            <textarea type="text" name="deskripsi" id="ubahdeskripsi" class="form-control" maxlength="100"></textarea>
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
                <form action="{{route('predikatsikap.hapus')}}" method="POST">
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
    function ubah(id, predikat, desc, type) {
        $('#ubahid').val(id);
        if(type == 1){
            document.getElementById('gadasikap').selected = true;
        }else{
            document.getElementById(type).selected = true;
        }
        document.getElementById(predikat).selected = true;
        $('#ubahpredikat').val(predikat);
        $('#ubahdeskripsi').val(desc);
    }

    function hapus(id) {
        $('#hapusid').val(id);
    }
</script>
@endsection