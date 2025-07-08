@extends('template.main.master')

@section('title')
Indikator Kompetensi Pengetahuan
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
    <h1 class="h3 mb-0 text-gray-800">Indikator Kompetensi Pengetahuan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Indikator Kompetensi Pengetahuan</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Indikator Kompetensi Pengetahuan</h6>
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#TambahModal">Tambah <i class="fas fa-plus"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
							<th style="width: 15px">#</th>
                            <th>Tingkat Kelas</th>
							<th>Mata Pelajaran</th>
                            <th>Indikator</th>
							<th style="width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($indikator && $indikator->isEmpty() == FALSE)
						@php $no = 1; @endphp
                        @foreach($indikator as $i)
						@if($i->detail()->count() > 0)
						@foreach($i->detail as $d)
                        <tr>
							<td>{{ $no++ }}</td>
                            <td>{{$i->level_id ? $i->level->level : '' }}</td>
                            <td>{{$i->subject_id ? $i->mataPelajaran->subject_name : '' }}</td>
                            <td>{{$d->indicator}}</td>
                            <td class="text-right">
                                <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#UbahModal" onclick="ubah(<?php echo $d->id; ?>, '<?php echo $i->level->level; ?>', '<?php echo $i->mataPelajaran->subject_name; ?>', '<?php echo $d->indicator; ?>')"><i class="fas fa-pen"></i></a>
                                &nbsp;<a href="javascript:void(0)" data-toggle="modal" data-target="#HapusModal" onclick="hapus(<?php echo $d->id; ?>)" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        @endforeach
                        @else
                        <tr>
                            <td colspan="4" class="text-center">Data Kosong</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form action="{{route('indikator.pengetahuan.tambah')}}" method="POST">
                @csrf
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Tambah Indikator</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Tingkat Kelas</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="level" style="width:100%;" tabindex="-1" aria-hidden="true">
                                <option value="">== Pilih Tingkat Kelas ==</option>
                                @foreach ($level as $levels)
                                <option value="{{$levels->id}}">{{$levels->level}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Mata Pelajaran</label>
                        <div class="col-sm-8">
                            <select class="form-control" name="subject" required>
                                <option value="">== Pilih ==</option>
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
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Indikator</label>
                        <div class="col-sm-8">
                            <textarea name="indikator" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand-green-dark">Tambah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Ubah -->
<div class="modal fade" id="UbahModal" tabindex="-1" role="dialog" aria-labelledby="UbahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form action="{{route('indikator.pengetahuan.ubah')}}" method="POST">
                @csrf
                <input type="hidden" name="id" id="ubahid">
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Ubah Indikator</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
				
						  <div class="row mb-2">
							<div class="col-12 mb-1">
								 Tingkat Kelas
							</div>
							<div class="col-12">
							  <h5 id="ubah_level">
							  </h5>
							</div>
						  </div>
						    <div class="row mb-2">
								<div class="col-12 mb-1">
									 Mata Pelajaran
								</div>
								<div class="col-12">
								  <h5 id="ubah_subject">
								  </h5>
								</div>
							  </div>
							  <div class="row mb-2">
								<div class="col-12 mb-1">
									 Indikator
								</div>
								<div class="col-12">
									<textarea name="indikator" id="ubah_indikator" class="form-control"></textarea>
								</div>
							  </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-brand-green-dark">Simpan</button>
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
                <form action="{{route('indikator.pengetahuan.hapus')}}" method="POST">
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
    function ubah(id, level, subject, indicator) {
        $('#ubahid').val(id);
        $('#ubah_level').html(level);
		$('#ubah_subject').html(subject);
        $('#ubah_indikator').val(indicator);
    }

    function hapus(id) {
        $('#hapusid').val(id);
    }
</script>
@endsection