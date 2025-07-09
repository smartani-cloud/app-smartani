<<<<<<< HEAD
@extends('template.main.master')

@section('title')
Rentang Nilai
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Atur Rentang Nilai</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur Rentang Nilai</li>
  </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Rentang Nilai</h6>
                <button type="button" class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#add-form">Tambah <i class="fas fa-plus-circle ml-1"></i></button>
            </div>
            <div class="card-body p-3">
              @if(Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(Session::has('danger'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ Session::get('danger') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(count($psc) > 0)
              @foreach($psc->where('status_id',1)->all() as $p)
              <div class="row ml-1">
                <div class="col-12 mb-3">
                  <div class="row py-2 rounded border border-success mr-2">
                    <div class="col-8 d-flex align-items-center">
                      <div class="d-none d-md-block mr-3">
                        <div class="icon-circle bg-gray-500">
                          <i class="fas fa-th-list text-white"></i>
                        </div>
                      </div>
                      <div class="d-block">
                        <div>
                          <span class="font-weight-bold text-dark mr-1">{{ $p->name }}</span>
                          <span class="badge badge-primary">Aktif</span>
                        </div>
                        <small>{{ implode(", ",$p->gradeSorted->toArray()) }}</small>
                        <div class="mt-2">
                          <a href="javascript:void(0)" class="font-weight-bold text-brand-green-dark text-decoration-none" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('psc.rentang.ubah') }}','{{ $p->id }}')">Ubah</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-4 d-flex justify-content-end align-items-center">
                      <i class="fa fa-check text-success mr-3"></i>
                    </div>
                  </div>
                </div>
              </div>
      			  @endforeach
      			  @foreach($psc->where('status_id','!=',1)->all() as $p)
      			  <div class="row ml-1">
                <div class="col-12 mb-3">
                  <div class="row py-2 rounded border border-light mr-2">
                    <div class="col-8 d-flex align-items-center">
                      <div class="d-none d-md-block mr-3">
                        <div class="icon-circle bg-gray-500">
                          <i class="fas fa-th-list text-white"></i>
                        </div>
                      </div>
                      <div class="d-block">
            						<div>
            							<span class="font-weight-bold text-dark">{{ $p->name }}</span>
            						</div>
            						<small>{{ implode(", ",$p->gradeSorted->toArray()) }}</small>
            						<div class="mt-2">
            							<a href="javascript:void(0)" class="font-weight-bold text-brand-green-dark text-decoration-none" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('psc.rentang.ubah') }}','{{ $p->id }}')">Ubah</a>
                          @if($p->isEditable)
            							<span class="text-gray-200 mx-2">|</span>
            							<a href="javascript:void(0)" class="font-weight-bold text-brand-green-dark text-decoration-none" data-target="#delete-confirm" onclick="deleteModal('Rentang Nilai', '{{ addslashes(htmlspecialchars($p->name)) }}', '{{ route('psc.rentang.hapus', $p->id) }}')">Hapus</a>
                          @endif
            						</div>
                      </div>
                    </div>
                    <div class="col-4 d-flex justify-content-end align-items-center">
                      <a href="{{ route('psc.rentang.aktif', $p->id) }}" class="btn btn-sm btn-outline-brand-green-dark px-3">Pilih</a>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
              @else
              <div class="text-center mx-3 my-5">
                  <h3 class="text-center">Mohon Maaf,</h3>
                  <h6 class="font-weight-light mb-3">Tidak ada daftar rentang nilai yang ditemukan</h6>
              </div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Tambah Rentang Nilai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route('psc.rentang.simpan') }}" id="rentang-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Nama Daftar</label>
                  </div>
                  <div class="col-12">
                    <input type="text" id="name" class="form-control" name="name" maxlength="255" required="required">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Rentang Nilai</label>
                  </div>
                  <div class="col-12">
        					  @for($i=0;$i<5;$i++)
        					  <div class="row mb-2">
        						  <div class="col-4">
        							<input type="text" class="form-control" name="grade[]" placeholder="Huruf" maxlength="3" required="required">
        						  </div>
        						  <div class="col-8">
        							<div class="input-group">
        							  <input type="number" class="input-sm form-control" name="start[]" placeholder="Awal" value="0.000" min="0" max="4.9" step="0.001" required="required"/>
        							  <div class="input-group-prepend">
        								<span class="input-group-text">-</span>
        							  </div>
        							  <input type="number" class="input-sm form-control" name="end[]" placeholder="Akhir" value="0.100" min="0.1" max="5" step="0.001" required="required"/>
        							</div>
        						  </div>
        					  </div>
        					  @endfor
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-6 text-left">
              <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
            </div>
            <div class="col-6 text-right">
              <input id="save-grade-set" type="submit" class="btn btn-brand-green-dark" value="Tambah">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Rentang Nilai</h5>
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

@include('template.modal.konfirmasi_hapus')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.modal.post_edit_item')
@include('template.footjs.modal.get_delete')
=======
@extends('template.main.master')

@section('title')
Rentang Nilai
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kepegawaian.'.Auth::user()->role->name)
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Atur Rentang Nilai</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="./">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('psc.index') }}">Performance Scorecard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur Rentang Nilai</li>
  </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Rentang Nilai</h6>
                <button type="button" class="m-0 float-right btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#add-form">Tambah <i class="fas fa-plus-circle ml-1"></i></button>
            </div>
            <div class="card-body p-3">
              @if(Session::has('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sukses!</strong> {{ Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(Session::has('danger'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Gagal!</strong> {{ Session::get('danger') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              @endif
              @if(count($psc) > 0)
              @foreach($psc->where('status_id',1)->all() as $p)
              <div class="row ml-1">
                <div class="col-12 mb-3">
                  <div class="row py-2 rounded border border-success mr-2">
                    <div class="col-8 d-flex align-items-center">
                      <div class="d-none d-md-block mr-3">
                        <div class="icon-circle bg-gray-500">
                          <i class="fas fa-th-list text-white"></i>
                        </div>
                      </div>
                      <div class="d-block">
                        <div>
                          <span class="font-weight-bold text-dark mr-1">{{ $p->name }}</span>
                          <span class="badge badge-primary">Aktif</span>
                        </div>
                        <small>{{ implode(", ",$p->gradeSorted->toArray()) }}</small>
                        <div class="mt-2">
                          <a href="javascript:void(0)" class="font-weight-bold text-brand-green-dark text-decoration-none" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('psc.rentang.ubah') }}','{{ $p->id }}')">Ubah</a>
                        </div>
                      </div>
                    </div>
                    <div class="col-4 d-flex justify-content-end align-items-center">
                      <i class="fa fa-check text-success mr-3"></i>
                    </div>
                  </div>
                </div>
              </div>
      			  @endforeach
      			  @foreach($psc->where('status_id','!=',1)->all() as $p)
      			  <div class="row ml-1">
                <div class="col-12 mb-3">
                  <div class="row py-2 rounded border border-light mr-2">
                    <div class="col-8 d-flex align-items-center">
                      <div class="d-none d-md-block mr-3">
                        <div class="icon-circle bg-gray-500">
                          <i class="fas fa-th-list text-white"></i>
                        </div>
                      </div>
                      <div class="d-block">
            						<div>
            							<span class="font-weight-bold text-dark">{{ $p->name }}</span>
            						</div>
            						<small>{{ implode(", ",$p->gradeSorted->toArray()) }}</small>
            						<div class="mt-2">
            							<a href="javascript:void(0)" class="font-weight-bold text-brand-green-dark text-decoration-none" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('psc.rentang.ubah') }}','{{ $p->id }}')">Ubah</a>
                          @if($p->isEditable)
            							<span class="text-gray-200 mx-2">|</span>
            							<a href="javascript:void(0)" class="font-weight-bold text-brand-green-dark text-decoration-none" data-target="#delete-confirm" onclick="deleteModal('Rentang Nilai', '{{ addslashes(htmlspecialchars($p->name)) }}', '{{ route('psc.rentang.hapus', $p->id) }}')">Hapus</a>
                          @endif
            						</div>
                      </div>
                    </div>
                    <div class="col-4 d-flex justify-content-end align-items-center">
                      <a href="{{ route('psc.rentang.aktif', $p->id) }}" class="btn btn-sm btn-outline-brand-green-dark px-3">Pilih</a>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
              @else
              <div class="text-center mx-3 my-5">
                  <h3 class="text-center">Mohon Maaf,</h3>
                  <h6 class="font-weight-light mb-3">Tidak ada daftar rentang nilai yang ditemukan</h6>
              </div>
              @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Tambah Rentang Nilai</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route('psc.rentang.simpan') }}" id="rentang-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Nama Daftar</label>
                  </div>
                  <div class="col-12">
                    <input type="text" id="name" class="form-control" name="name" maxlength="255" required="required">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Rentang Nilai</label>
                  </div>
                  <div class="col-12">
        					  @for($i=0;$i<5;$i++)
        					  <div class="row mb-2">
        						  <div class="col-4">
        							<input type="text" class="form-control" name="grade[]" placeholder="Huruf" maxlength="3" required="required">
        						  </div>
        						  <div class="col-8">
        							<div class="input-group">
        							  <input type="number" class="input-sm form-control" name="start[]" placeholder="Awal" value="0.000" min="0" max="4.9" step="0.001" required="required"/>
        							  <div class="input-group-prepend">
        								<span class="input-group-text">-</span>
        							  </div>
        							  <input type="number" class="input-sm form-control" name="end[]" placeholder="Akhir" value="0.100" min="0.1" max="5" step="0.001" required="required"/>
        							</div>
        						  </div>
        					  </div>
        					  @endfor
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-6 text-left">
              <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
            </div>
            <div class="col-6 text-right">
              <input id="save-grade-set" type="submit" class="btn btn-brand-green-dark" value="Tambah">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Ubah Rentang Nilai</h5>
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

@include('template.modal.konfirmasi_hapus')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.modal.post_edit_item')
@include('template.footjs.modal.get_delete')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection