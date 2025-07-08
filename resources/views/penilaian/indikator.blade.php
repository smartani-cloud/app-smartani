@extends('template.main.master')

@section('title')
Indikator Aspek Perkembangan
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
    <h1 class="h3 mb-0 text-gray-800">Indikator Aspek Perkembangan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Indikator Aspek Perkembangan</li>
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
                        @if(!$level)
                        <input type="text" class="form-control" value="Data Kosong" disabled>
                        @else
                        <input type="text" class="form-control" value="{{$level->level}}" disabled>
                        <!-- <select class="form-control" name="level" id="idlevel" onchange="getindikator(this.value)" id="kelas" required>
                            <option value="">== Pilih ==</option>
                            <option value="{{$level->id}}">{{$level->level}}</option>
                        </select> -->
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" id="getindikator">
                        <a class="m-0 float-right btn btn-brand-green-dark btn-sm mb-2" href="javascript:void(0)" data-toggle="modal" data-target="#TambahModal">Tambah <i class="fas fa-plus"></i></a>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Aspek Perkembangan</th>
                                        <th>Indikator</th>
                                        <th class="text-right" style="min-width:120px">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!$indikator)
                                    <tr>
                                        <td colspan="3" class="text-center">Data Kosong</td>
                                    </tr>
                                    @else
                                    @foreach ($indikator as $indikators)
                                    <tr>
                                        <td>{{$indikators->aspek->dev_aspect}}</td>
                                        <td>{{$indikators->indicator}}</td>
                                        <td class="text-right">
                                            @php
                                            $validated = $indikators->nilai()->whereHas('rapor',function($query){
                                                $query->whereHas('nilairapor',function($query){
                                                    $query->where('report_status_id',1);
                                                });
                                            })->get();
                                            @endphp
                                            @if($validated->count() < 1)
                                            <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal" onclick="ubah(<?php echo $indikators->id; ?>, '<?php echo addslashes(htmlspecialchars($indikators->indicator)); ?>', '<?php echo $indikators->development_aspect_id; ?>')"><i class="fas fa-pen"></i></a>
                                            @else
                                            <button class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-pen"></i></button>
                                            @endif
                                            <a href="javascript:void(0)" data-toggle="modal" data-target="#HapusModal" onclick="hapus(<?php echo $indikators->id; ?>)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
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

            <form action="{{route('indikatoraspek.tambah')}}" method="POST">
                @csrf
                <input type="hidden" name="level_id" id="idlevelsubmit" value="{{ $level->id }}"/>
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Tambah Indikator</h5>
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
                        <label for="predikat" class="col-sm-12 control-label">Indikator</label>
                        <div class="col-sm-12">
                            <input type="text" name="indikator" class="form-control" />
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

            <form action="{{route('indikatoraspek.ubah')}}" method="POST">
                @csrf
                <input type="hidden" name="id" id="ubahid">
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Ubah Indikator</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-12 control-label">Aspek Perkembangan</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="aspek_id" id="ubahaspek" required>
                                <option value="">== Pilih ==</option>
                                @foreach ($aspek as $aspeks)
                                <option value="{{$aspeks->id}}">{{$aspeks->dev_aspect}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-12 control-label">Deskripsi</label>
                        <div class="col-sm-12">
                            <input type="text" name="indikator" id="ubahindikator" class="form-control"></input>
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
                <p>Apakah Anda yakin akan menghapus data tersebut?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('indikatoraspek.hapus')}}" method="POST">
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
<script>
    function ubah(id, indikator, aspek) {
        $('#ubahid').val(id);
        $('#ubahaspek').val(aspek);
        $('#ubahindikator').val(indikator);
    }

    function hapus(id) {
        $('#hapusid').val(id);
    }

    // function getindikator(id) {
    //     $('#getindikator').html("");
    //     $.ajaxSetup({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         }
    //     });
    //     $.ajax({
    //         url: "{{ route('indikatoraspek.getindikator') }}",
    //         data: {
    //             'level_id': id
    //         },
    //         type: 'POST',
    //         success: function(response) {
    //             $('#getindikator').html(response.html);
    //             $('#idlevelsubmit').val(id);
    //         },
    //         error: function(xhr, textStatus, thrownError) {
    //             alert(xhr + "\n" + textStatus + "\n" + thrownError);
    //         }
    //     });
    // }
</script>
@endsection