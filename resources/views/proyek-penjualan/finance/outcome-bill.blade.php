@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
Ubah {{ $active }}
@endsection

@section('headmeta')
<!-- Bootstrap DatePicker -->
<link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.show',['id' => $data->id]) }}">{{ $data->id }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route($route.'.edit',['id' => $data->id]) }}">Ubah</a></li>
    <li class="breadcrumb-item active" aria-current="page">Bahan Baku</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-body p-3">
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Nomor</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->date ? $data->name : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Tanggal</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->date ? $data->dateId : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Divisi</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->unit ? $data->unit->name : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @if($data->project)
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Proyek</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  <a href="{{ route('proposal.show',['id' => $data->project]) }}" target="_blank" class="text-decoration-none text-info">{{ $data->project->name }}</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Status</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ $data->status ? $data->status->name : '-' }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-0">
          <div class="col-lg-8 col-md-10 col-12">
            <div class="form-group mb-0">
              <div class="row">
                <div class="col-lg-3 col-md-4 col-12">
                  <label class="form-control-label">Dibuat</label>
                </div>
                <div class="col-lg-9 col-md-8 col-12">
                  {{ date('d M Y H.i', strtotime($data->created_at)) }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="{{ route($route.'.edit',['id' => $data->id]) }}" class="btn btn-sm btn-light">Kembali</a>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12 mb-4">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Bahan Baku</h6>
      </div>
      <div class="card-body px-4 py-3">
        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $item->materialSupplier->name }}</div>
        <div class="mt-1">{{ $item->quantityBuyWithSeparator.' x Rp '.$item->priceWithSeparator }}</div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-4 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Harga MOQ</div>
            <div id="totalNominal" class="h5 mb-0 font-weight-bold text-gray-800">{{ $item->amountMoqWithSeparator }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-calculator fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Pembayaran</div>
            <div id="totalPaid" class="h5 mb-0 font-weight-bold text-gray-800">{{ $item->paidWithSeparator }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-12 mb-4">
    <div class="card shadow h-100 py-2">
      <div class="card-body">
        <div class="row no-gutters align-items-center">
          <div class="col mr-2">
            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
            Total Tagihan</div>
            <div id="totalRemain" class="h5 mb-0 font-weight-bold text-gray-800">{{ $item->remainWithSeparator }}</div>
          </div>
          <div class="col-auto">
            <i class="fas fa-minus fa-2x text-gray-300"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran</h6>
      </div>
      @if($item->payments()->count() > 0)
      <div class="card-body">
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
        @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        <div class="table-responsive">
          <table class="table table-bordered" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Tanggal</th>
                <th>Ketarangan</th>
                <th>Nominal</th>
                <th style="width: 120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @php
              $no = 1;
              @endphp
              @foreach($item->payments()->orderBy('date')->get() as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->dateId }}</td>
                <td>{{ $d->note }}</td>
                <td>{{ $d->valueWithSeparator }}</td>
                <td>
                  <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route($route.'.payment.edit') }}','{{ $d->id }}')" data-toggle="modal" data-target="#edit-form"><i class="fas fa-pen"></i></a>
                  <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Riwayat Pembayaran', '{!! addslashes(htmlspecialchars($d->name)) !!}', '{{ route($route.'.refund', ['id' => $data->id, 'material' => $item->id,'item' => $d->id]) }}')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @if($item->remain > 0)
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              <button class="btn btn-sm btn-primary px-3" type="button" data-toggle="modal" data-target="#pay-confirm">Bayar</button>
            </div>
          </div>
        </div>
        @endif
      </div>
      @else
      @if(Session::has('success'))
      <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <strong>Sukses!</strong> {{ Session::get('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      @if(Session::has('danger'))
      <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <strong>Gagal!</strong> {{ Session::get('danger') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      @if($errors->any())
      <div class="alert alert-danger m-3">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
      @endif
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Belum ada data riwayat pembayaran yang ditemukan</h6>
        <div class="row">
          <div class="col-12">
            <div class="text-center">
              <button class="btn btn-sm btn-primary px-3" type="button" data-toggle="modal" data-target="#pay-confirm">Bayar</button>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
<!--Row-->

@if($item->remain > 0)
<div class="modal fade" id="pay-confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Bayar Angsuran</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-body p-4">
        <form action="{{ route($route.'.pay',['id'=>$data->id,'material'=>$item->id]) }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="paymentDateInput" class="form-control-label">Tanggal<span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <div class="input-group date">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                      </div>
                      <input type="text" name="payment_date" class="form-control form-control-sm @error('payment_date') is-invalid @enderror" value="{{ date('d F Y') }}" placeholder="Pilih tanggal" id="paymentDateInput" required="required">
                    </div>
                    @error('payment_date')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="paymentNominal" class="form-control-label">Nominal<span class="text-danger">*</span></label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="paymentNominal" class="form-control form-control-sm @error('payment_nominal') is-invalid @enderror number-separator" name="payment_nominal" value="{{ old('payment_nominal') }}" maxlength="12" required="required">
                    <small class="form-text text-muted">Jika nominal melebihi tagihan, maka nominal yang akan tersimpan hanya senilai total tagihan yang ada</small>
                    @error('payment_nominal')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="paymentBalance" class="form-control-label">Sisa Tagihan</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="text" id="paymentBalance" class="form-control form-control-sm number-separator" name="payment_balance" value="{{ $item->remainWithSeparator }}" maxlength="12" disabled="disabled">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="paymentNote" class="form-control-label">Keterangan</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <textarea id="paymentNote" class="form-control form-control-sm @error('payment_note') is-invalid @enderror" name="payment_note" maxlength="50" rows="2"></textarea>
                    @error('payment_note')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
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
              <input type="submit" class="btn btn-primary" value="Bayar">
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

@endif
<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary border-0">
        <h5 class="modal-title text-white">Ubah</h5>
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

@include('template.modal.delete-confirm')

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- Bootstrap Datepicker -->
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Number with Commas -->
<script src="{{ asset('js/number-with-commas.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.datepicker')
@include('template.footjs.global.tooltip')
@include('template.footjs.modal.post_edit')
@include('template.footjs.modal.get_delete')
<script>
$(function(){
    var bill = parseInt($('#totalRemain').html().replace(/\./g, ""));
    $('#paymentNominal').on('keyup', function(e){
      var total = $(this).val() ? parseInt($(this).val().replace(/\./g, "")) : 0;
      var balance = bill-total;
      $('#paymentBalance').val(numberWithCommas(balance));
    });
    $('#edit-form').on("keyup",'#editPaymentNominal',function(e){
      e.preventDefault();
      var total = $(this).val() ? parseInt($(this).val().replace(/\./g, "")) : 0;
      var def = $(this).attr('data-default') ? $(this).attr('data-default') : 0;
      var balance = bill-(total-def);
      $('#editPaymentBalance').val(numberWithCommas(balance));
    });
});  
</script>
@endsection