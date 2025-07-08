@extends('keuangan.parent.ppa_show')

@section('validate')
<input type="hidden" name="validate" value="">
<input type="hidden" name="draft" value="{{ $ppaAktif->is_draft }}">
@endsection

@section('row')
  @php
  $i = 1;
  @endphp
  @foreach($ppaAktif->detail as $p)
  <tr id="p-{{ $p->id }}">
      <td>{{ $i++ }}</td>
      <td class="detail-account">{{ $p->akun->codeName }}</td>
      <td class="detail-note">{{ $p->note }}</td>
      @if(($apbyAktif && $apbyAktif->is_active == 1 && $apbyAktif->is_final != 1) && $ppaAktif->director_acc_status_id != 1)
      @php
      $apbyDetail = $p->akun->apby()->whereHas('apby',function($q)use($yearAttr,$tahun,$anggaranAktif,$accAttr){$q->where([$yearAttr => ($yearAttr == 'year' ? $tahun : $tahun->id),$accAttr => 1])->whereHas('jenisAnggaranAnggaran',function($q)use($anggaranAktif){$q->where('id',$anggaranAktif->id);})->aktif()->latest();})->where('account_id',$p->account_id)->first();
      @endphp
      <td>{{ $apbyDetail ? $apbyDetail->balanceWithSeparator : '-' }}</td>
      @endif
      <td>
          @if(!$p->pa_acc_status_id)
          <i class="fa fa-lg fa-question-circle text-{{ Auth::user()->pegawai->unit_id != 5 && $p->edited_employee_id != Auth::user()->pegawai->id ? 'secondary' : 'light' }}" data-toggle="tooltip" data-original-title="{{ Auth::user()->pegawai->unit_id != 5 && $p->edited_employee_id != Auth::user()->pegawai->id ? 'Diperiksa oleh TU/Wakasek. ' : null }}Menunggu Persetujuan {{ Auth::user()->pegawai->position_id == $anggaranAktif->anggaran->acc_position_id ? 'Anda' : $anggaranAktif->anggaran->accJabatan->name }}"></i>
          @elseif(!$ppaAktif->pa_acc_status_id && $p->pa_acc_status_id == 1)
          <i class="fa fa-lg fa-check-circle text-secondary mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($p->accPa) ? 'Anda' : $p->accPa->name }}<br>{{ date('d M Y H.i.s', strtotime($p->pa_acc_time)) }}"></i>
          @elseif($ppaAktif->pa_acc_status_id == 1 && !$p->finance_acc_status_id)
          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == 33 ? 'Anda' : 'Kepala Divisi Umum' }}"></i>
          @elseif($p->finance_acc_status_id == 1 && !$p->director_acc_status_id)
          @if($p->employee_id == Auth::user()->pegawai->id && $p->edited_status_id == 1 && $p->editPegawai->is($p->accKeuangan))
          <i class="fa fa-lg fa-check-circle text-warning mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan dengan perubahan oleh {{ Auth::user()->pegawai->is($p->accKeuangan) ? 'Anda' : $p->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($p->finance_acc_time)) }}<br>Awal: {{ $p->valuePaWithSeparator }}"></i>
          @else
          <i class="fa fa-lg fa-check-circle text-warning mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disimpan oleh {{ Auth::user()->pegawai->is($p->accKeuangan) ? 'Anda' : $p->accKeuangan->name }}<br>{{ date('d M Y H.i.s', strtotime($p->finance_acc_time)) }}"></i>
          @endif
          @elseif($p->director_acc_status_id == 1)
          @if($p->employee_id == Auth::user()->pegawai->id && $p->edited_status_id == 1 && $p->editPegawai->is($p->accDirektur))
          <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui dengan perubahan oleh {{ Auth::user()->pegawai->is($p->accDirektur) ? 'Anda' : $p->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($p->director_acc_time)) }}<br>Awal: {{ $p->valuePaWithSeparator }}"></i>
          @else
          <i class="fa fa-lg fa-check-circle text-success mr-1" data-toggle="tooltip" data-html="true" data-original-title="Disetujui oleh {{ Auth::user()->pegawai->is($p->accDirektur) ? 'Anda' : $p->accDirektur->name }}<br>{{ date('d M Y H.i.s', strtotime($p->director_acc_time)) }}"></i>
          @endif
          @else
          -
          @endif
      </td>
      <td class="detail-value" style="min-width: 200px">
          @if($ppaAktif->type_id == 2)
          {{ $p->valueWithSeparator }}
          @else
          @if($ppaAktif->pa_acc_status_id == 1 || !$apbyAktif || ($apbyAktif && ($apbyAktif->is_active == 0 || $apbyAktif->is_final == 1)))
          <input type="text" class="form-control form-control-sm" value="{{ $p->valueWithSeparator }}" disabled>
          @else
          <input name="value-{{ $p->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $p->valueWithSeparator }}">
          @endif
          @endif
      </td>
      @if(($apbyAktif && $apbyAktif->is_active == 1 && $apbyAktif->is_final != 1)
      && (($isAnggotaPa && !$ppaAktif->bbk) || ($isPa || (!$isAnggotaPa && in_array(Auth::user()->role->name, ['fam','faspv','am'])) && $ppaAktif->type_id == 2))
      && ((in_array(Auth::user()->role->name, ['fam','faspv','am']) && (($ppaAktif->type_id != 2 && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->type_id == 2)) || (($ppaAktif->type_id != 2 && $ppaAktif->pa_acc_status_id != 1) || $ppaAktif->type_id == 2)))
      <td>
    		  @if($ppaAktif->type_id == 2)
    		  <a href="{{ route('ppa.'.($ppaAktif->is_draft == 1 ? 'draft.' : null).'ubah.proposal', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'id' => $p->id]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-list-ul"></i></a>
    		  @endif
          @if($p->finance_acc_status_id != 1)
          @if($ppaAktif->type_id == 2)
          @if($apbyAktif->is_final != 1 && $ppaAktif->pa_acc_status_id != 1)
          <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('ppa.ubah.detail', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}','{{ $p->id }}')"><i class="fas fa-pen"></i></a>
          @endif
          @elseif($apbyAktif->is_final != 1)
          <button type="button" class="btn btn-sm btn-warning btn-edit" data-toggle="modal" data-target="#edit-form">
              <i class="fa fa-pen"></i>
          </button>
          @endif
          @if($apbyAktif->is_final != 1 && $ppaAktif->pa_acc_status_id != 1)
          <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Pengajuan', '{{ addslashes(htmlspecialchars($p->note)) }}', '{{ route('ppa.hapus.detail', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'submitted' => $ppaAktif->is_draft == 1 ? null : '1', 'id' => $p->id]) }}')">
              <i class="fas fa-trash"></i>
          </a>
          @endif
          @endif
      </td>
      @endif
  </tr>
  @endforeach

@endsection

@section('footer')
  @if(($apbyAktif && $apbyAktif->is_active == 1 && $apbyAktif->is_final != 1) && ($ppaAktif->pa_acc_status_id != 1 || $ppaAktif->detail()->where(function($q){$q->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');})->count() > 0))
  <div class="row">
      <div class="col-12">
          <div class="text-center">
              @if($ppaAktif->type_id == 2)
              <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#saveAccept">Ajukan</button>
              @else
              <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
              <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#saveAccept">Simpan & Ajukan</button>
              @endif
          </div>
      </div>
  </div>
  @endif
@endsection

@section('accept-modal')
@if(($apbyAktif && $apbyAktif->is_active == 1 && $apbyAktif->is_final != 1) && $ppaAktif && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
<div class="modal fade" id="saveAccept" tabindex="-1" role="dialog" aria-labelledby="simpanSetujuiModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-confirm" role="document">
    <div class="modal-content">
      <div class="modal-header flex-column">
        <div class="icon-box border-secondary">
          <i class="material-icons text-secondary">&#xE5CA;</i>
        </div>
        <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body p-1">
        Apakah Anda yakin ingin {{ $ppaAktif->type_id == 2 ? 'mengajukan' : 'menyimpan dan mengajukan' }} semua alokasi dana yang ada?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger mr-1" data-dismiss="modal">Tidak</button>
        <button type="submit" id="saveAcceptBtn" class="btn btn-primary" data-form="ppa-form">Ya, {{ $ppaAktif->type_id == 2 ? 'Ajukan' : 'Simpan & Ajukan' }}</button>
      </div>
    </div>
  </div>
</div>
@endif
@endsection

@section('accept-script')
@if(($apbyAktif && $apbyAktif->is_active == 1 && $apbyAktif->is_final != 1) && $ppaAktif && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
@include('template.footjs.modal.post_save_accept')
@endif
@endsection