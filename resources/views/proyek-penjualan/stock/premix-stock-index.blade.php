@extends('template.main.master')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
  </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div id="addItemCard" class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Buat Premix</h6>
      </div>
      <div class="card-body px-4 py-3">
        <form action="{{ route($route.'.store') }}" id="addItemForm" method="post" enctype="multipart/form-data" accept-charset="utf-8">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Premix" class="form-control-label">Nama</label>
                  </div>
                  <div class="col-lg-9 col-md-8 col-12">
                    <select class="select2 form-control form-control-sm @error('premix') is-invalid @enderror" name="premix" id="select2Premix" required="required">
                      @foreach($premixes as $p)
                      <option value="{{ $p->id }}" {{ old('premix') == $p->id ? 'selected' : '' }}>{{ Auth::user()->role_id == 3 ? $p->name : $p->nameUnit }}</option>
                      @endforeach
                    </select>
                    @error('premix')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="normal-input" class="form-control-label">Kuantitas</label>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12">
                    <input type="number" id="quantity" class="form-control @error('quantity') is-invalid @enderror number-separator" name="quantity" value="{{ old('quantity') ? old('quantity') : '1' }}" min="1" maxlength="12" required="required">
                    @error('quantity')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                  </div>
                </div>
              </div>
            </div>
          </div>
          <hr>
          <div class="alert alert-danger alert-materials" role="alert" style="display: none;">
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Material" class="form-control-label">Bahan Baku</label>
                  </div>
                  <div id="materialWrapper" class="col-lg-9 col-md-8 col-12">
                    <div class="form-row">
                      <div class="form-group col-md-9">
                        <select id="select-material-1" class="form-control" name="material[]" required="required" data-x="1">
                          <option value="">Pilih salah satu</option>
                          @foreach($material as $m)
                          <option value="{{ $m->id }}" data-max="{{ $m->stock_quantity }}">{{ Auth::user()->role_id == 3 ? $m->nameWithStock : $m->nameUnitWithStock }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group col-md-2">
                        <input id="input-material-quantity-1" type="number" class="form-control" name="materialQty[]" value="0" min="0" placeholder="Kuantitas" required="required">
                      </div>
                      <div class="form-group col-md-1 text-right">
                        <button type="button" class="btn btn-secondary" disabled="disabled"><i class="fas fa-times"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @php
          $disabledLinkStyle = 'class="text-secondary" style="pointer-events: none;"';
          @endphp
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12">
                    <a href="javascript:void(0)" id="addMaterialButton" {!! !$material || ($material && count($material) < 2) ? $disabledLinkStyle : null !!}><i class="fas fa-plus mr-2"></i>Tambah Bahan Baku</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group mb-0">
                <div class="row">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="select2Product" class="form-control-label">Produk</label>
                  </div>
                  <div id="productWrapper" class="col-lg-9 col-md-8 col-12">
                    <div class="form-row">
                      <div class="form-group col-md-9">
                        <select id="select-product-1" class="form-control" name="product[]" required="required" data-x="1">
                          <option value="">Pilih salah satu</option>
                          @foreach($product as $p)
                          <option value="{{ $p->id }}" data-max="{{ $p->stockQuantity }}">{{ Auth::user()->role_id == 3 ? $p->nameWithStock : $p->nameUnitWithStock }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="form-group col-md-2">
                        <input id="input-product-quantity-1" type="number" class="form-control" name="productQty[]" value="0" min="0" placeholder="Kuantitas" required="required">
                      </div>
                      <div class="form-group col-md-1 text-right">
                        <button type="button" class="btn btn-secondary" disabled="disabled"><i class="fas fa-times"></i></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-10 col-md-12">
              <div class="form-group">
                <div class="row mb-3">
                  <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12">
                    <a href="javascript:void(0)" id="addProductButton" {!! !$product || ($product && count($product) < 2) ? $disabledLinkStyle : null !!}><i class="fas fa-plus mr-2"></i>Tambah Produk</a>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-1">
            <div class="col-lg-10 col-md-12">
                <div class="row">
                    <div class="col-lg-9 offset-lg-3 col-md-8 offset-md-4 col-12 text-left">
                      @if($premixes && count($premixes) > 0)
                      <button type="button" class="btn btn-primary btn-make" data-toggle="modal" data-target="#make-confirm">Buat</button>
                      @else
                      <button type="button" class="btn btn-secondary btn-make" disabled="disabled" data-toggle="modal" data-target="#make-confirm">Buat</button>
                      @endif
                    </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ $active }}</h6>
      </div>
      @if(count($data) > 0)
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
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        <div class="table-responsive">
          <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 50px">#</th>
                <th>Premix</th>
                @if(Auth::user()->role_id != 3)
                <th>Unit</th>
                @endif
                <th>Tahun</th>
                <th>Stok</th>
              </tr>
            </thead>
            <tbody>
              @php $no = 1; @endphp
              @foreach($data as $d)
              <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $d->premix->name }}</td>
                @if(Auth::user()->role_id != 3)
                <td>{{ $d->premix->unit ? $d->premix->unit->name : '-' }}</td>
                @endif
                <td>{{ $d->year }}</td>
                <td>{{ $d->quantityWithSeparator }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
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
      <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
          @endforeach
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </ul>
      </div>
      @endif
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($active) }} yang ditemukan</h6>
      </div>
      @endif
    </div>
  </div>
</div>
<!--Row-->

<div class="modal fade" id="make-confirm" tabindex="-1" role="dialog" aria-labelledby="acceptModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-primary">
          <i class="material-icons text-primary">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin membuat <span class="item font-weight-bold"></span>?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary mr-1" data-dismiss="modal">Tidak</button>
        <button type="button" class="btn btn-primary btn-make" style="background: #2e59d9">Ya, Buat</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function(){
    var maxMaterialField = {{ $material ? $material->count() : 0 }}; //Input fields increment limitation
    var addMaterialButton = $('#addMaterialButton'); //Add button selector
    var materialWrapper = $('#materialWrapper'); //Input field wrapper
    var x = 1; //Initial field counter is 1
    var role = {!! Auth::user()->role_id !!};

    $.fn.getParent = function(num){
      var last = this[0];
      for(var i = 0; i < num; i++){
        last = last.parentNode;
      }
      return $(last);
    };

    //Once add button is clicked
    $(addMaterialButton).click(function(){
        //Check maximum number of input fields
        if(x < maxMaterialField){ 
            x++; //Increment field counter
            var row = $('<div></div>').addClass('form-row');
            var selectMaterial = $('<select></select>').addClass('form-control').attr({id: 'select-material-'+x,name: 'material[]',"data-x": x}).prop("required", true);
            var material = {!! $material ? (Auth::user()->role_id == 3 ? $material->values()->map->only(['id', 'nameWithStock', 'stock_quantity'])->toJson() : $material->values()->map->only(['id', 'nameUnitWithStock', 'stock_quantity'])->toJson()) : 'null' !!};
            if(Object.keys(material).length > 0){
              console.log('Count materials: ' + Object.keys(material).length);
              var option = $('<option></option>').attr('value','').html('Pilih salah satu');
              selectMaterial.append(option);
              $.each(material,function(key,item){
                  if(role == 3)
                  var option = $('<option></option>').attr({value: item.id,'data-max': item.stock_quantity}).html(item.nameWithStock);
                else
                  var option = $('<option></option>').attr({value: item.id,'data-max': item.stock_quantity}).html(item.nameUnitWithStock);
                  selectMaterial.append(option);
              });
            }
            else{
                selectMaterial.prop("disabled", true);
            }
            var md9 = $('<div></div>').addClass('form-group col-md-9').html(selectMaterial);
            
            var inputQty = $('<input>').addClass('form-control').attr({id: 'input-material-quantity-'+x,type: 'number',name: 'materialQty[]',value: '0',min: '0',placeholder: 'Kuantitas',"data-x": x}).prop("required", true);
            var md2 = $('<div></div>').addClass('form-group col-md-2').html(inputQty);

            var faTimes = $('<i></i>').addClass('fa fa-times');
            var btnRemove = $('<button></button>').addClass('btn btn-danger btn-remove').attr('type', 'button').html(faTimes);
            var md1 = $('<div></div>').addClass('form-group col-md-1 text-right').html(btnRemove);

            row.append(md9, md2, md1);
            $(materialWrapper).append(row); //Add field html
            checkSelected('material');
        }
    });

    $(materialWrapper).on('change', 'select[name="material[]"]', function(e){
      var dataX = $(this).attr('data-x');
      var max = $(this).find('option:selected').attr('data-max');
      var defaultVal = 0;
      if($(this).val().length != 0) defaultVal = 1;
      console.log('Max quantity: '+max);
      $('#input-material-quantity-'+dataX).attr('max',0).val(defaultVal);
      if(max){
        console.log('#input-material-quantity-'+dataX);
        $('#input-material-quantity-'+dataX).attr('max',max).val(defaultVal);
      }
      checkSelected('material');
    });
    
    //Once remove button is clicked
    $(materialWrapper).on('click', '.btn-remove', function(e){
        e.preventDefault();
        $(this).getParent(2).remove(); //Remove field html
        x--; //Decrement field counter
        checkSelected('material');
    });

    var maxProductField = {{ $product ? count($product) : 0 }}; //Input fields increment limitation
    var addProductButton = $('#addProductButton'); //Add button selector
    var productWrapper = $('#productWrapper'); //Input field wrapper
    var y = 1; //Initial field counter is 1

    //Once add button is clicked
    $(addProductButton).click(function(){
        //Check maximum number of input fields
        if(y < maxProductField){ 
            y++; //Increment field counter
            var row = $('<div></div>').addClass('form-row');
            var selectProduct = $('<select></select>').addClass('form-control').attr({id: 'select-product-'+y,name: 'product[]',"data-x": y}).prop("required", true);
            var product = {!! $product ? (Auth::user()->role_id == 3 ? $product->values()->map->only(['id', 'nameWithStock', 'stockQuantity'])->toJson() : $product->values()->map->only(['id', 'nameUnitWithStock', 'stockQuantity'])->toJson()) : 'null' !!};
            if(Object.keys(product).length > 0){
              console.log('Count products: ' + Object.keys(product).length);
              var option = $('<option></option>').attr('value','').html('Pilih salah satu');
              selectProduct.append(option);
              $.each(product,function(key,item){
                if(role == 3)
                  var option = $('<option></option>').attr({value: item.id,'data-max': item.stockQuantity}).html(item.nameWithStock);
                else
                  var option = $('<option></option>').attr({value: item.id,'data-max': item.stockQuantity}).html(item.nameUnitWithStock);
                  selectProduct.append(option);
              });
            }
            else{
                selectProduct.prop("disabled", true);
            }
            var md9 = $('<div></div>').addClass('form-group col-md-9').html(selectProduct);
            
            var inputQty = $('<input>').addClass('form-control').attr({id: 'input-product-quantity-'+y,type: 'number',name: 'productQty[]',value: '0',min: '0',placeholder: 'Kuantitas',"data-x": y}).prop("required", true);
            var md2 = $('<div></div>').addClass('form-group col-md-2').html(inputQty);

            var faTimes = $('<i></i>').addClass('fa fa-times');
            var btnRemove = $('<button></button>').addClass('btn btn-danger btn-remove').attr('type', 'button').html(faTimes);
            var md1 = $('<div></div>').addClass('form-group col-md-1 text-right').html(btnRemove);

            row.append(md9, md2, md1);
            $(productWrapper).append(row); //Add field html
            checkSelected('product');
        }
    });

    $(productWrapper).on('change', 'select[name="product[]"]', function(e){
      var dataX = $(this).attr('data-x');
      var max = $(this).find('option:selected').attr('data-max');
      var defaultVal = 0;
      if($(this).val().length != 0) defaultVal = 1;
      console.log('Max quantity: '+max);
      $('#input-product-quantity-'+dataX).attr('max',0).val(defaultVal);
      if(max){
        console.log('#input-material-quantity-'+dataX);
        $('#input-product-quantity-'+dataX).attr('max',max).val(defaultVal);
      }
      checkSelected('product');
    });
    
    //Once remove button is clicked
    $(productWrapper).on('click', '.btn-remove', function(e){
        e.preventDefault();
        $(this).getParent(2).remove(); //Remove field html
        y--; //Decrement field counter
        checkSelected('product');
    });

    function checkSelected(select){
      $('select[name^="'+select+'"]').each(function () {
          $('option:not(:first)',this).removeAttr('disabled').removeClass('bg-gray-300');
      });
      $('select[name^="'+select+'"]').each(function () {
        var idSelected = $(this).attr('data-x');
        var selected = $(this).children("option:selected").val();
        $('select[name^="'+select+'"]').each(function () {
          var dataX = $(this).attr('data-x');
          if(selected && (dataX != idSelected)){
            $('option[value='+selected+']',this).attr('disabled','disabled').addClass('bg-gray-300');
          }
          //console.log('option[value='+selected+']');
        });
      });
      checkSelectedCount();
    }

    function checkSelectedCount(){
      if(x == maxMaterialField){
        if($(addMaterialButton).hasClass("text-secondary") == false){
          $(addMaterialButton).addClass("text-secondary").css('pointer-events','none');
        }
      }
      else{
        $(addMaterialButton).removeClass("text-secondary").removeAttr('style');
      }
      if(y == maxProductField){
        if($(addProductButton).hasClass("text-secondary") == false){
          $(addProductButton).addClass("text-secondary").css('pointer-events','none');
        }
      }
      else{
        $(addProductButton).removeClass("text-secondary").removeAttr('style');
      }
    }
});
</script>

<script type="text/javascript">
$(function() {
  $.fn.checkButton = function(value,button){
    if($(value).val()){
      $(button).prop('disabled', false);
      if($(button).hasClass("btn-secondary")){
        $(button).removeClass("btn-secondary");
        $(button).addClass("btn-primary");
      }
    }
    else{
      $(button).prop('disabled', true);
      if($(button).hasClass("btn-primary")){
        $(button).removeClass("btn-primary");
        $(button).addClass("btn-secondary");
      }
    }
  };
  $('#select2Premix').on('change',function(){
    $(this).checkButton('#select2Premix','#addItemCard .btn-make');
  });
  $('#addItemCard').on("click",'.btn-primary',function(e){
    e.preventDefault();

    var premix = $('#select2Premix option:selected').html();
    var qty = $('#quantity').val();
    $('#make-confirm .item').html(qty+' '+premix);
  });
  $('#make-confirm').on("click",'.btn-make',function(e){
    e.preventDefault();
    /* when the submit button in the modal is clicked, submit the form */
    var materialsCount = 0;
    var productsCount = 0;
    var materialQtyCount = 0;
    var productQtyCount = 0;
    var invalid = 0;
    $('select[name^="material"]').each(function () {
       if($(this).val().length > 0) materialsCount++;
    });
    $('select[name^="product"]').each(function () {
       if($(this).val().length > 0) productsCount++;
    });
    $('input[name^="materialQty"]').each(function () {
       var value = parseInt($(this).val(),10);
       if(value > 0) materialQtyCount++;
       var max = parseInt($(this).attr('max'),10);
       if(value > max){
         invalid++;
       }
       //console.log(value+' > '+max+' = '+(value > max));
    });
    $('input[name^="productQty"]').each(function () {
       var value = parseInt($(this).val(),10);
       if(value > 0) productQtyCount++;
       var max = parseInt($(this).attr('max'),10);
       if(value > max){
         invalid++;
       }
       //console.log(value+' > '+max+' = '+(value > max));
    });
    if((materialsCount > 0 || productsCount > 0) && invalid == 0){
      $('#addItemForm').submit();
    }
    else if(materialsCount < 1 && productsCount < 1){
      $('.alert-materials').html('Mohon masukkan setidaknya satu bahan baku atau produk').fadeIn(500);
      $('#make-confirm').modal('hide');
    }
    else if(invalid > 0){
      alert('Gagal!\nPastikan kuantitas bahan baku atau produk tidak melebihi stok yang tersedia');
      e.preventDefault();
      $('#make-confirm').modal('hide');
    }
  });
});
</script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.global.select2')
@include('template.footjs.global.tooltip')
@endsection