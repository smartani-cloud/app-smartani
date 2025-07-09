@extends('keuangan.parent.ppa_show')

@section('row')
  @php
  $i = 1;
  @endphp
  @foreach($ppaAktif->detail as $p)
  <tr id="p-{{ $p->id }}">
      <td>{{ $i++ }}</td>
      <td class="detail-account">{{ $p->akun->codeName }}</td>
      <td class="detail-note">{{ $p->note }}</td>
      <td>
          @if(!$p->pa_acc_status_id)
          <i class="fa fa-lg fa-question-circle text-light" data-toggle="tooltip" data-original-title="Menunggu Pemeriksaan {{ Auth::user()->pegawai->position_id == $anggaranAktif->anggaran->acc_position_id ? 'Anda' : $anggaranAktif->anggaran->accJabatan->name }}"></i>
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
          @if($p->pa_acc_status_id == 1 || !$apbyAktif || ($apbyAktif && $apbyAktif->is_active == 0))
          <input type="text" class="form-control form-control-sm" value="{{ $p->valueWithSeparator }}" disabled>
          @else
          <input name="value-{{ $p->id }}" type="text" class="form-control form-control-sm number-separator" value="{{ $p->valueWithSeparator }}">
          @endif
          @endif
      </td>
      @if(($apbyAktif && $apbyAktif->is_active == 1) && $isAnggotaPa && ((in_array(Auth::user()->role->name, ['fam','faspv']) && $ppaAktif->finance_acc_status_id != 1) || $ppaAktif->pa_acc_status_id != 1))
      <td>
          @if($p->pa_acc_status_id != 1)
          @if($ppaAktif->type_id == 2)
          <a href="{{ route('ppa.'.($ppaAktif->is_draft == 1 ? 'draft.' : null).'ubah.proposal', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'id' => $p->id]) }}" class="btn btn-sm btn-brand-green-dark"><i class="fas fa-list-ul"></i></a>
          <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('ppa.ubah.detail', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber]) }}','{{ $p->id }}')"><i class="fas fa-pen"></i></a>
          @else
          <button type="button" class="btn btn-sm btn-warning btn-edit" data-toggle="modal" data-target="#edit-form">
              <i class="fa fa-pen"></i>
          </button>
          @endif
          <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-confirm" onclick="deleteModal('Pengajuan', '{{ addslashes(htmlspecialchars($p->note)) }}', '{{ route('ppa.hapus.detail', ['jenis' => $jenisAktif->link, 'tahun' => !$isYear ? $tahun->academicYearLink : $tahun, 'anggaran' => $anggaranAktif->anggaran->link, 'nomor' => $ppaAktif->firstNumber, 'submitted' => $ppaAktif->is_draft == 1 ? null : '1', 'id' => $p->id]) }}')">
              <i class="fas fa-trash"></i>
          </a>
          @endif
      </td>
      @endif
  </tr>
  @endforeach

@endsection

@section('footer')
  @if($ppaAktif->type_id != 2)
  @if(($apbyAktif && $apbyAktif->is_active == 1) && $ppaAktif->pa_acc_status_id != 1 && $ppaAktif->detail()->where(function($q){$q->where('pa_acc_status_id','!=',1)->orWhereNull('pa_acc_status_id');})->count() > 0)
  <div class="row">
      <div class="col-12">
          <div class="text-center">
              <button class="btn btn-brand-green-dark" type="submit">Simpan</button>
          </div>
      </div>
  </div>
  @endif
  @endif
@endsection