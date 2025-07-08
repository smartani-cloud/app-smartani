<div class="row mb-2">
  <div class="col-12 mb-1">Bahan Baku</div>
  <div class="col-12">
    <h5>{{ $data->material->name }}</h5>
  </div>
</div>
<div class="row mb-2">
  <div class="col-12 mb-1">Pemasok</div>
  <div class="col-12">
    <h5>{{ $data->supplier->name }}</h5>
  </div>
</div>
<div class="row mb-2">
  <div class="col-12 mb-1">MOQ<i class="fa fa-lg fa-question-circle text-light ml-2" data-toggle="tooltip" data-original-title="Minimum Order Quantity"></i></div>
  <div class="col-12">
    <h5>{{ $data->moqWithSeparator }}</h5>
  </div>
</div>
<div class="row mb-2">
  <div class="col-12 mb-1">Harga</div>
  @php
  $lastLog = $data->priceLogs()->select('created_at')->latest()->first();
  $last = $lastLog->createdAtIdShort;
  @endphp
  <div class="col-12">
    <div class="mb-1"><small class="text-muted">{{ $last }} - Sekarang<i class="fas fa-check text-success ml-1"></i></small></div>
    <h5>{{ $data->priceWithSeparator }}</h5>
    @foreach($data->priceLogs()->latest()->get() as $p)
    @php
    if($loop->last){
      $start = $data->createdAtIdShort;
    }
    else{
      $previousLog = $data->priceLogs()->select('created_at')->where('id','<',$p->id)->latest()->first();
      $start = $previousLog->createdAtIdShort;
    }
    @endphp
    <div class="mb-1"><small class="text-muted">{{ $start }} - {{ $last }}</small></div>
    <h5>{{ $p->priceWithSeparator }}</h5>
    @php
    $last = $p->createdAtIdShort;
    @endphp
    @endforeach
  </div>
</div>

<div class="row mt-3">
  <div class="col-12">
    <button type="button" class="btn btn-light" data-dismiss="modal">Kembali</button>
  </div>
</div>

@include('template.footjs.global.tooltip')