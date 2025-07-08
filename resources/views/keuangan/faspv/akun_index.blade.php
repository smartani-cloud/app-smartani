@extends('template.main.master')

@section('title')
Akun Anggaran
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Akun Anggaran</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">Akun Anggaran</li>
  </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Akun Anggaran</h6>
                <div class="m-0 float-right">
                  <a href="{{ route('keuangan.akun.urutkan') }}" class="btn btn-brand-green-dark btn-sm">Urutkan <i class="fas fa-sort-numeric-down ml-1"></i></a>
                  <button type="button" class="btn btn-brand-green-dark btn-sm" data-toggle="modal" data-target="#add-form">Tambah <i class="fas fa-plus-circle ml-1"></i></button>
                </div>
            </div>
            @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
              <strong>Sukses!</strong> {{ Session::get('success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            @if(Session::has('danger'))
            <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
              <strong>Gagal!</strong> {{ Session::get('danger') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
               <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            @if(count($akun) > 0)
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Bisa Diisi</th>
                            <th>Ekslusif</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th style="width: 120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                      @php $no = 1; @endphp
                      @foreach($akun as $a)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $a->code }}</td>
                            <td>{{ $a->name }}</td>
                            <td>
                              @if($a->is_fillable == 1)
                              <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Ya"></i>
                              @elseif($a->is_fillable == 0)
                              <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="Tidak"></i>
                              @else
                              -
                              @endif
                            </td>
                            <td>
                              @if($a->is_exclusive == 1)
                              <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Ya"></i>
                              @elseif($a->is_exclusive == 0)
                              <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="Tidak"></i>
                              @else
                              -
                              @endif
                            </td>
                            <td>
                              {{ $a->kategori ? $a->kategori->name : '-'}}
                            </td>
                            <td>
                              @if(!$a->deleted_at)
                              <i class="fa fa-lg fa-check-circle text-success" data-toggle="tooltip" data-original-title="Aktif"></i>
                              @else
                              <i class="fa fa-lg fa-times-circle text-danger" data-toggle="tooltip" data-original-title="Tidak"></i>
                              @endif
                            </td>
                            @php
                            $usedCount = $exUsedCount = null;
                            if($a->is_exclusive == 0){
                              $usedCount = $a->ppa()->whereHas('ppa',function($q)use($tahunPelajaran,$tahun){
                                $q->where(function($q)use($tahunPelajaran,$tahun){
                                  $q->where(function($q)use($tahunPelajaran){
                                    $q->where('academic_year_id', $tahunPelajaran->id)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                      $q->where('name','LIKE','APB-KSO%');
                                    })->where(function($q){
                                      $q->where(function($q){
                                        $q->doesntHave('lppa')->whereHas('bbk.bbk',function($q){
                                          $q->has('jenisAnggaran')->where(function($q){
                                            $q->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                                          });
                                        });
                                      })->orWhereHas('lppa',function($q){
                                        $q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                      });
                                    });
                                  })->orWhere(function($q)use($tahun){
                                    $q->where('year', $tahun)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                      $q->where('name','APBY');
                                    })->where(function($q){
                                      $q->where(function($q){
                                        $q->doesntHave('lppa')->whereHas('bbk.bbk',function($q){
                                          $q->has('jenisAnggaran')->where(function($q){
                                            $q->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                          });
                                        });
                                      })->orWhereHas('lppa',function($q){
                                        $q->where('finance_acc_status_id','!=',1)->orWhereNull('finance_acc_status_id');
                                      });
                                    });
                                  });
                                });
                              })->count();
                            }
                            elseif($a->is_exclusive == 1){
                              $exUsedCount = $a->ppa()->whereHas('ppa',function($q)use($tahunPelajaran,$tahun){
                                $q->where(function($q)use($tahunPelajaran,$tahun){
                                  $q->where(function($q)use($tahunPelajaran){
                                    $q->where('academic_year_id', $tahunPelajaran->id)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                      $q->where('name','LIKE','APB-KSO%');
                                    })->whereHas('bbk.bbk',function($q){
                                      $q->has('jenisAnggaran')->where(function($q){
                                        $q->where('director_acc_status_id','!=',1)->orWhereNull('director_acc_status_id');
                                      });
                                    });
                                  })->orWhere(function($q)use($tahun){
                                    $q->where('year', $tahun)->whereHas('jenisAnggaranAnggaran.jenis',function($q){
                                      $q->where('name','APBY');
                                    })->whereHas('bbk.bbk',function($q){
                                      $q->has('jenisAnggaran')->where(function($q){
                                        $q->where('president_acc_status_id','!=',1)->orWhereNull('president_acc_status_id');
                                      });
                                    });
                                  });
                                });
                              })->count();
                            }
                            @endphp
                            <td>
                                <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('keuangan.akun.ubah') }}','{{ $a->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                                @if(!$a->deleted_at && (($a->is_exclusive != 1 && $usedCount < 1) || ($a->is_exclusive == 1 && $exUsedCount < 1)))
                                <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Akun Anggaran', '{{ addslashes(htmlspecialchars($a->name)) }}', '{{ route('keuangan.akun.hapus', ['id' => $a->id]) }}')"><i class="fas fa-trash"></i></a>
                                @else
                                <button type="button" class="btn btn-sm btn-secondary" disabled="disabled"><i class="fas fa-trash"></i></button>
                                @endif
                            </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center mx-3 my-5">
                <h3 class="text-center">Mohon Maaf,</h3>
                <h6 class="font-weight-light mb-3">Tidak ada data akun anggaran yang ditemukan</h6>
            </div>
            @endif
            <div class="card-footer"></div>
        </div>
    </div>
</div>
<!--Row-->

<div class="modal fade" id="add-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Tambah Akun Anggaran</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route('keuangan.akun.simpan') }}" id="akun-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
        {{ csrf_field() }}
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-12">
                    <label for="normal-input" class="form-control-label">Kode Akun</label>
                  </div>
                  <div class="col-md-6 col-12">
                    <input id="code" class="form-control" name="code" maxlength="18" required="required">
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
                    <label for="normal-input" class="form-control-label">Nama Akun</label>
                  </div>
                  <div class="col-12">
                    <input id="name" class="form-control" name="name" maxlength="255" required="required">
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
                    <label for="normal-input" class="form-control-label">Bisa Diisi</label>
                  </div>
                  <div class="col-12">
                    <div class="custom-control custom-radio custom-control-inline mb-1">
                      <input type="radio" id="fillableOpt1" name="is_fillable" class="custom-control-input" value="1" {{ old('is_fillable') == 1 ? 'checked' : '' }}>
                      <label class="custom-control-label" for="fillableOpt1">Ya</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline mb-1">
                      <input type="radio" id="fillableOpt2" name="is_fillable" class="custom-control-input" value="0" {{ old('is_fillable') == 0 ? 'checked' : '' }}>
                      <label class="custom-control-label" for="fillableOpt2">Tidak</label>
                    </div>
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
                    <label for="normal-input" class="form-control-label">Eksklusif</label>
                  </div>
                  <div class="col-12">
                    <div class="custom-control custom-radio custom-control-inline mb-1">
                      <input type="radio" id="exclusiveOpt1" name="is_exclusive" class="custom-control-input" value="1" {{ old('is_exclusive') == 1 ? 'checked' : '' }}>
                      <label class="custom-control-label" for="exclusiveOpt1">Ya</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline mb-1">
                      <input type="radio" id="exclusiveOpt2" name="is_exclusive" class="custom-control-input" value="0" {{ old('is_exclusive') == 0 ? 'checked' : '' }}>
                      <label class="custom-control-label" for="exclusiveOpt2">Tidak</label>
                    </div>
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
                    <label for="normal-input" class="form-control-label">Kategori</label>
                  </div>
                  <div class="col-12">
                    <select aria-label="Kategori" name="account_category" id="inputAccountCategory" title="Kategori" class="form-control @error('account_category') is-invalid @enderror" required="required">
                      <option value="" {{ old('account_category') ? '' : 'selected' }} disabled="disabled">Pilih salah satu</option>
                      @foreach($kategori as $k)
                      @if(!$k->upcategory)
                      <option value="{{ $k->id }}" class="bg-gray-400" disabled="disabled">{{ $k->name }}</option>
                      @else
                      <option value="{{ $k->id }}" {{ old('account_category') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
                      @endif
                      @endforeach
                    </select>
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
                    <label for="normal-input" class="form-control-label">Anggaran</label>
                  </div>
                  <div class="col-12">
                    <select class="select2-multiple form-control" name="budgeting[]" multiple="multiple" id="budgeting" required="required">
                    @foreach($jenisAnggaran as $j)
                    <option value="{{ $j->id }}">{{ $j->name }}</option>
                    @endforeach
                  </select>
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
              <input id="save-academic-background" type="submit" class="btn btn-brand-green-dark" value="Tambah">
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
        <h5 class="modal-title text-white">Ubah Akun Anggaran</h5>
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

<!-- Page level plugins -->

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.kepegawaian.select2-multiple')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
@endsection