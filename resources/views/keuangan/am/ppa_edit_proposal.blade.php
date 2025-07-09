<<<<<<< HEAD
@extends('keuangan.parent.ppa_edit_proposal')

@section('alert')
@if(($apbyAktif && $apbyAktif->is_active == 1) && ($isPa || (!$isPa && $ppaAktif->pa_acc_status_id == 1)) && $ppaAktif->finance_acc_status_id != 1)
<div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
  <i class="fa fa-info-circle text-info mr-2"></i>Jumlah dan subtotal akan diperbarui ketika tombol "Simpan" diklik
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
   <span aria-hidden="true">&times;</span>
 </button>
</div>
@endif
@endsection

@section('row')
  @php
  $i = 1;
  @endphp
  @foreach($ppaDetail->proposals as $p)
  <tr id="p-{{ $p->id }}">
      <td class="font-weight-bold">{{ $i }}</td>
      @php
      $isActColExist = ($apbyAktif && $apbyAktif->is_active == 1 && $apbyAktif->is_final != 1) && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1) ? true : false;
      @endphp
      @if($ppaAktif && !$ppaAktif->lppa)
      <td class="font-weight-bold" colspan="{{ $isActColExist ? 4 : 3 }}">
        @if((!$isAnggotaPa && $ppaAktif->pa_acc_status_id != 1) || $ppaAktif->finance_acc_status_id == 1 || !$apbyAktif || ($apbyAktif && $apbyAktif->is_active == 0))
        {{ $p->title }}
        @else
        <input name="title-{{ $p->id }}" type="text" class="form-control form-control-sm" value="{{ $p->title }}" maxlength="100" required="required">
        @endif
      </td>
      <td>
        <a href="javascript:void(0)" class="btn btn-sm btn-brand-green-dark" data-toggle="modal" data-target="#detailModal" onclick="detailModal('{{ route('proposal-ppa.detail.modal', ['id' => $p->id]) }}')"><i class="fas fa-info-circle"></i></a>
        @if($p->finance_acc_status_id != 1)
        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('ppa.ubah.proposal.desc',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'submitted' => $ppaAktif->is_draft == 1 ? null : '1', 'id' => $ppaDetail->id]) }}','{{ $p->id }}')"><i class="fas fa-pen"></i></a>
        @endif
      </td>
      @else
      <td class="font-weight-bold" colspan="{{ $isActColExist ? 5 : 4 }}">{{ $p->title }}</td>
      @endif
  </tr>
  @php
  $j = 1;
  @endphp
  @foreach($p->details as $d)
  <tr id="d-{{ $d->id }}">
      <td>{{ $i.'.'.($j++) }}</td>
      @if((!$isAnggotaPa && $ppaAktif->pa_acc_status_id != 1) || $ppaAktif->finance_acc_status_id == 1 || !$apbyAktif || ($apbyAktif && $apbyAktif->is_active == 0))
      <td><input type="text" class="form-control form-control-sm" value="{{ $d->desc }}" disabled="disabled"></td>
      <td><input type="text" class="form-control form-control-sm" value="{{ $d->priceWithSeparator }}" disabled="disabled"></td>
      <td><input type="text" class="form-control form-control-sm" value="{{ $d->quantityWithSeparator }}" disabled="disabled"></td>
      @else
      <td class="detail-desc"><input name="desc-{{ $d->id }}" type="text" class="form-control form-control-sm" value="{{ $d->desc }}" maxlength="255" required="required"></td>
      <td class="detail-price"><input name="price-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->priceWithSeparator }}"></td>
      <td class="detail-qty"><input name="qty-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->quantityWithSeparator }}"></td>
      @endif
      <td class="detail-value">{{ $d->valueWithSeparator }}</td>
      @if(($apbyAktif && $apbyAktif->is_active == 1) && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
      <td>
          @if($p->finance_acc_status_id != 1)
          <button type="button" class="btn btn-sm btn-warning btn-edit" data-toggle="modal" data-target="#edit-detail-form">
            <i class="fa fa-pen"></i>
          </button>
          <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Pengajuan', '{{ addslashes(htmlspecialchars($d->desc)) }}', '{{ route('ppa.hapus.proposal.item', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'submitted' => $ppaAktif->is_draft == 1 ? null : '1', 'id' => $ppaDetail->id, 'item' => $d->id]) }}')">
              <i class="fas fa-trash"></i>
          </a>
          @endif
      </td>
      @endif
  </tr>
  @endforeach
  @php
  $i++;
  @endphp
  @endforeach

@endsection

@section('footer')
  @if(($apbyAktif && $apbyAktif->is_active == 1) && ($isPa || (!$isPa && $ppaAktif->pa_acc_status_id == 1)) && $ppaAktif->finance_acc_status_id != 1)
  <div class="row">
      <div class="col-12">
          <div class="text-center">
              <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
          </div>
      </div>
  </div>
  @endif
@endsection
=======
@extends('keuangan.parent.ppa_edit_proposal')

@section('alert')
@if(($apbyAktif && $apbyAktif->is_active == 1) && ($isPa || (!$isPa && $ppaAktif->pa_acc_status_id == 1)) && $ppaAktif->finance_acc_status_id != 1)
<div class="alert alert-light alert-dismissible fade show mx-3" role="alert">
  <i class="fa fa-info-circle text-info mr-2"></i>Jumlah dan subtotal akan diperbarui ketika tombol "Simpan" diklik
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
   <span aria-hidden="true">&times;</span>
 </button>
</div>
@endif
@endsection

@section('row')
  @php
  $i = 1;
  @endphp
  @foreach($ppaDetail->proposals as $p)
  <tr id="p-{{ $p->id }}">
      <td class="font-weight-bold">{{ $i }}</td>
      @php
      $isActColExist = ($apbyAktif && $apbyAktif->is_active == 1 && $apbyAktif->is_final != 1) && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1) ? true : false;
      @endphp
      @if($ppaAktif && !$ppaAktif->lppa)
      <td class="font-weight-bold" colspan="{{ $isActColExist ? 4 : 3 }}">
        @if((!$isAnggotaPa && $ppaAktif->pa_acc_status_id != 1) || $ppaAktif->finance_acc_status_id == 1 || !$apbyAktif || ($apbyAktif && $apbyAktif->is_active == 0))
        {{ $p->title }}
        @else
        <input name="title-{{ $p->id }}" type="text" class="form-control form-control-sm" value="{{ $p->title }}" maxlength="100" required="required">
        @endif
      </td>
      <td>
        <a href="javascript:void(0)" class="btn btn-sm btn-brand-green-dark" data-toggle="modal" data-target="#detailModal" onclick="detailModal('{{ route('proposal-ppa.detail.modal', ['id' => $p->id]) }}')"><i class="fas fa-info-circle"></i></a>
        @if($p->finance_acc_status_id != 1)
        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('ppa.ubah.proposal.desc',['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'submitted' => $ppaAktif->is_draft == 1 ? null : '1', 'id' => $ppaDetail->id]) }}','{{ $p->id }}')"><i class="fas fa-pen"></i></a>
        @endif
      </td>
      @else
      <td class="font-weight-bold" colspan="{{ $isActColExist ? 5 : 4 }}">{{ $p->title }}</td>
      @endif
  </tr>
  @php
  $j = 1;
  @endphp
  @foreach($p->details as $d)
  <tr id="d-{{ $d->id }}">
      <td>{{ $i.'.'.($j++) }}</td>
      @if((!$isAnggotaPa && $ppaAktif->pa_acc_status_id != 1) || $ppaAktif->finance_acc_status_id == 1 || !$apbyAktif || ($apbyAktif && $apbyAktif->is_active == 0))
      <td><input type="text" class="form-control form-control-sm" value="{{ $d->desc }}" disabled="disabled"></td>
      <td><input type="text" class="form-control form-control-sm" value="{{ $d->priceWithSeparator }}" disabled="disabled"></td>
      <td><input type="text" class="form-control form-control-sm" value="{{ $d->quantityWithSeparator }}" disabled="disabled"></td>
      @else
      <td class="detail-desc"><input name="desc-{{ $d->id }}" type="text" class="form-control form-control-sm" value="{{ $d->desc }}" maxlength="255" required="required"></td>
      <td class="detail-price"><input name="price-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->priceWithSeparator }}"></td>
      <td class="detail-qty"><input name="qty-{{ $d->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $d->quantityWithSeparator }}"></td>
      @endif
      <td class="detail-value">{{ $d->valueWithSeparator }}</td>
      @if(($apbyAktif && $apbyAktif->is_active == 1) && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
      <td>
          @if($p->finance_acc_status_id != 1)
          <button type="button" class="btn btn-sm btn-warning btn-edit" data-toggle="modal" data-target="#edit-detail-form">
            <i class="fa fa-pen"></i>
          </button>
          <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Pengajuan', '{{ addslashes(htmlspecialchars($d->desc)) }}', '{{ route('ppa.hapus.proposal.item', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'submitted' => $ppaAktif->is_draft == 1 ? null : '1', 'id' => $ppaDetail->id, 'item' => $d->id]) }}')">
              <i class="fas fa-trash"></i>
          </a>
          @endif
      </td>
      @endif
  </tr>
  @endforeach
  @php
  $i++;
  @endphp
  @endforeach

@endsection

@section('footer')
  @if(($apbyAktif && $apbyAktif->is_active == 1) && ($isPa || (!$isPa && $ppaAktif->pa_acc_status_id == 1)) && $ppaAktif->finance_acc_status_id != 1)
  <div class="row">
      <div class="col-12">
          <div class="text-center">
              <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
          </div>
      </div>
  </div>
  @endif
@endsection
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
