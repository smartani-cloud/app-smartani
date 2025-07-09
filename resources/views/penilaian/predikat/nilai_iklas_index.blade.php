@extends('template.main.master')

@section('title')
Predikat IKLaS
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
    <h1 class="h3 mb-0 text-gray-800">Predikat & Deskripsi IKLaS</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        <li class="breadcrumb-item active" aria-current="page">Predikat IKLaS</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Deskripsi Predikat Nilai IKLaS</h6>
                @if($rpd && (implode(',',$rpd->pluck('predicate')->toArray()) == '1,2,3,4,5'))
                <button type="button" class="m-0 float-right btn btn-secondary btn-sm" disabled="disabled">Tambah <i class="fas fa-plus"></i></button>
                @else
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="javascript:void(0)" data-toggle="modal" data-target="#TambahModal">Tambah <i class="fas fa-plus"></i></a>
                @endif
            </div>
            <div class="card-body p-3">
                <div class="alert alert-secondary" role="alert">
                    <i class="fa fas fa-exclamation-triangle text-yellow mr-2"></i><strong>Catatan</strong> : Harap isikan predikat dan deskripsi dengan lengkap. Kekurangan dalam pengaturan predikat dan deskripsi akan berpengaruh pada tampilan cetak rapor.
                </div>
                @if($rpd && count($rpd) > 0)
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 15px">#</th>
                                <th style="width: 250px">Predikat</th>
                                <th>Deskripsi</th>
                                <th style="width: 120px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rpd as $no => $r)
                            <tr>
                                <td>{{$no+1}}</td>
                                <td>
                                @for($i=0;$i<$r->predicate;$i++)<i class="fas fa-star"></i>
                                @endfor
                                </td>
                                <td>{{$r->description}}</td>
                                <td>
                                    <a href="javascript:void(0)" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('predikat.iklas.ubah') }}','{{ $r->predicate }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else       
                <div class="text-center mx-3 my-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada deskripsi predikat IKLaS yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!$rpd || ($rpd && (implode(',',$rpd->pluck('predicate')->toArray()) != '1,2,3,4,5')))
<!-- Modal Tambah -->
<div class="modal fade" id="TambahModal" tabindex="-1" role="dialog" aria-labelledby="TambahModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('predikat.iklas.tambah') }}" method="POST">
                @csrf
                <div class="modal-header bg-brand-green-dark text-white">
                    <h5 class="modal-title" id="exampleModalLongTitle">Tambah Deskripsi Predikat IKLaS</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Predikat</label>
                        <div class="col-sm-4">
                            <select name="predikat" class="form-control">
                                @for($i=1;$i<=5;$i++)
                                @if($rpd && !in_array($i,$rpd->pluck('predicate')->toArray()))
                                <option value="{{ $i }}">
                                @for($j=0;$j<$i;$j++)&#9733;
                                @endfor
                                </option>
                                @endif
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="predikat" class="col-sm-4 control-label">Deskripsi</label>
                        <div class="col-sm-8">
                            <textarea type="text" name="deskripsi" class="form-control" maxlength="73"></textarea>
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
@endif

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Deskripsi Predikat IKLaS</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
              <h5 class="font-weight-light mb-3">Memuat...</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" style="display: none;">
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
        document.getElementById(predikat).selected = true;
        $('#ubahpredikat').val(predikat);
        $('#ubahdeskripsi').val(desc);
    }

    function hapus(id) {
        $('#hapusid').val(id);
    }
</script>
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.modal.post_edit')
@endsection