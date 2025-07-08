@extends('keuangan.parent.ppa_edit_proposal')

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
      <td class="font-weight-bold" colspan="{{ $isActColExist ? 4 : 3 }}">{{ $p->title }}</td>
      <td><a href="javascript:void(0)" class="btn btn-sm btn-brand-green-dark" data-toggle="modal" data-target="#detailModal" onclick="detailModal('{{ route('proposal-ppa.detail.modal', ['id' => $p->id]) }}')"><i class="fas fa-info-circle"></i></a></td>
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
      <td>{{ $d->desc }}</td>
      <td>{{ $d->priceWithSeparator }}</td>
      <td>{{ $d->quantityWithSeparator }}</td>
      <td>{{ $d->valueWithSeparator }}</td>
      @if(($apbyAktif && $apbyAktif->is_active == 1) && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
      <td>&nbsp;</td>
      @endif
  </tr>
  @endforeach
  @php
  $i++;
  @endphp
  @endforeach

@endsection
