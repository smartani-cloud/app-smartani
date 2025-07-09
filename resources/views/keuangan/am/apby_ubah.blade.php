<form action="{{ route('apby.transfer',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link]) }}" id="transfer-form" method="post" enctype="multipart/form-data" accept-charset="utf-8">
  {{ csrf_field() }}
  {{ method_field('PUT') }}
  <input id="id" type="hidden" name="id" required="required" value="{{ $detail->id }}">
  <div class="row mb-2">
    <div class="col-12 mb-1">Akun Asal</div>
    <div class="col-12">
    <h5>{{ $detail->akun->codeName }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">Rencana</div>
    <div class="col-12">
    <h5>{{ $detail->valueWithSeparator }}</h5>
    </div>
  </div>
  <div class="row mb-2">
    <div class="col-12 mb-1">Saldo</div>
    <div class="col-12">
    <h5>{{ $detail->balanceWithSeparator }}</h5>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Akun Tujuan</label>
          </div>
          <div class="col-12">
            <select class="select2 form-control @error('account') is-invalid @enderror" name="account" id="select2Account" required="required">
              @foreach($details as $d)
              @if($d->id != $detail->id)
              <option value="{{ $d->id }}" data-balance="{{ $d->balanceWithSeparator }}">{{ $d->akun->codeName.' ('.$d->balanceWithSeparator.')' }}</option>
              @else
              <option value="" class="bg-gray-300" disabled="disabled">{{ $d->akun->codeName }}</option>
              @endif
              @endforeach
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
  @php
  $value = $balance = 0;
  $firstAccount = count($details) > 1 ? $details->where('id','!=',$detail->id)->first() : null;
  if($firstAccount){
    $value = $firstAccount->valueWithSeparator;
    $balance = $firstAccount->balanceWithSeparator;
  }
  @endphp
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <div class="row">
          <div class="col-12">
            <label for="normal-input" class="form-control-label">Jumlah</label>
          </div>
          <div class="col-md-8 col-12">
            <input type="text" id="amount" class="form-control @error('amount') is-invalid @enderror number-separator" name="amount" value="0" required="required">
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
      <input id="transfer-submit" type="submit" class="btn btn-brand-green-dark" value="Transfer">
    </div>
  </div>
</form>

<!-- <script type="text/javascript">
  $(function() {
    $('#select2Account').on('change',function(){
      if($(this).val()){
        var balance = $('option:selected',this).data('balance');
      }
    });
    $('#amount').on('input',function(){
      if($(this).val()){
        var value = $(this).val();
        var max = {{ $detail->balance }};
        value = value.replace(/\./g, '');
        if(value > max){
          $('#transfer-submit').prop("disabled", true)
        }
        else{
          $('#transfer-submit').prop("disabled", false)
        }
      }
    });
  });
</script> -->
