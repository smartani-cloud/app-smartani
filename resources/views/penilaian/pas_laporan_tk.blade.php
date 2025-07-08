@extends('template.print.A4.master')

@section('title')
{{ $siswa->student_nis}} - {{ $siswa->identitas->student_name}} - Rapor - {{ $semester->semester_id }}
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/report.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/report.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="watermark">
	<div class="page">
		<div class="subpage">
			<p class="text-center text-uppercase fs-22 font-weight-bold">Rapor</p>
			<p class="text-center text-uppercase fs-18 font-weight-bold">{{ $unit->long_desc ? $unit->long_desc : $unit->desc }}</p>
			<div id="dataSiswa" class="m-t-22">
				<table>
					<tr>
						<td style="width: 17%">
							Nama Peserta Didik
						</td>
						<td style="width: 2%">
							:
						</td>
						<td style="width: 40%">
							{{ $siswa->identitas->student_name }}
						</td>
						<td style="width: 20%">
							Kelas
						</td>
						<td style="width: 2%">
							:
						</td>
						<td style="width: 19%">
							{{ $rapor->kelas->level->level_romawi }}{{ $rapor->kelas->jurusan ? ' '.$rapor->kelas->jurusan->major_name.' ' : ' ' }}{{ $rapor->kelas->namakelases->class_name }}
						</td>
					</tr>
					<tr>
						<td>
							NIPD
						</td>
						<td>
							:
						</td>
						<td>
							{{ $siswa->student_nis }}
						</td>
						<td>
							Semester
						</td>
						<td>
							:
						</td>
						<td>
							{{ $semester->semester }}
						</td>
					</tr>
					<tr>
						<td colspan="3">
							&nbsp;
						</td>
						<td>
							Tahun Pelajaran
						</td>
						<td>
							:
						</td>
						<td>
							{{ $semester->tahunAjaran->academic_year }}
						</td>
					</tr>
				</table>
			</div>
			<div id="perkembangan" class="m-t-22">
				<p class="komponen-rapor">I. PERKEMBANGAN</p>
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border page-break-auto">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 27%">
									Aspek Perkembangan dan Indikator
								</th>
								<th style="width: 15%">
									Perkembangan
								</th>
								<th style="width: 50%">
									Deskripsi
								</th>
							</tr>
							@php
							$active = null;
							$j = 'A';
							$a = count($aspek) > 0? $aspek->shift() : null;
							@endphp
							@if($a->indikator()->aktif()->where('level_id',$rapor->kelas->level->id)->count() > 0)
							@if($active != $a->id)
							<tr>
								<td class="font-weight-bold" colspan="4">{{ $j }}. {{ $a->dev_aspect }}</td>
							</tr>
							@php
							$active = $a->id;
							$j = chr(ord($j)+1);
							@endphp
							@endif
							@php
							$k = 1;
							$indikators = null;
							if($semester->is_active == 1)
							    $indikators = $a->indikator()->aktif()->where('level_id',$rapor->kelas->level->id)->get();
							else
							    $indikators = $rapor->pas_tk && $rapor->pas_tk->nilai()->count() > 0 ? $a->indikator()->where('level_id',$rapor->kelas->level->id)->whereIn('id',$rapor->pas_tk->nilai()->pluck('aspect_indicator_id'))->get() : null;
							@endphp
							@foreach($indikators as $i)
							<tr>
								<td class="text-center align-top">{{ $k++ }}</td>
								<td class="align-top">{{ $i->indicator }}</td>
								@php
								$nilai_aspek = $rapor->pas_tk && $rapor->pas_tk->nilai()->count() > 0 ? $rapor->pas_tk->nilai()->where('aspect_indicator_id',$i->id)->first() : null;
								@endphp
								@if($nilai_aspek)
								<td class="text-center align-top">
									{{ $nilai_aspek->predicate }}
								</td>
								<td class="align-top">
									{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$nilai_aspek->description) }}
								</td>
								@else
								<td class="text-center align-top">&nbsp;</td>
								<td class="align-top">&nbsp;</td>
								@endif
							</tr>
							@endforeach
							@endif
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	@if(count($aspek) > 0)
	@foreach($aspek as $a)
	@if($a->indikator()->aktif()->where('level_id',$rapor->kelas->level->id)->count() > 0)
	<div class="page">
		<div class="subpage">
			<div class="m-t-22">
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 27%">
									Aspek Perkembangan dan Indikator
								</th>
								<th style="width: 15%">
									Perkembangan
								</th>
								<th style="width: 50%">
									Deskripsi
								</th>
							</tr>
							@if($active != $a->id)
							<tr>
								<td class="font-weight-bold" colspan="4">{{ $j }}. {{ $a->dev_aspect }}</td>
							</tr>
							@php
							$active = $a->id;
							$j = chr(ord($j)+1);
							@endphp
							@endif
							@php
							$k = 1;
							$indikators = null;
							if($semester->is_active == 1)
							    $indikators = $a->indikator()->aktif()->where('level_id',$rapor->kelas->level->id)->get();
							else
							    $indikators = $rapor->pas_tk ? $a->indikator()->where('level_id',$rapor->kelas->level->id)->whereIn('id',$rapor->pas_tk->nilai()->pluck('aspect_indicator_id'))->get() : null;
							@endphp
							@foreach($indikators as $i)
							<tr>
								<td class="text-center align-top">{{ $k++ }}</td>
								<td class="align-top">{{ $i->indicator }}</td>
								@php
								$nilai_aspek = $rapor->pas_tk ? $rapor->pas_tk->nilai()->where('aspect_indicator_id',$i->id)->first() : null;
								@endphp
								@if($nilai_aspek)
								<td class="text-center align-top">
									{{ $nilai_aspek->predicate }}
								</td>
								<td class="align-top">
									{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$nilai_aspek->description) }}
								</td>
								@else
								<td class="text-center align-top">&nbsp;</td>
								<td class="align-top">&nbsp;</td>
								@endif
							</tr>
							@endforeach
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
	@endforeach
	@endif
	<div class="page">
		<div class="subpage">
			<div id="alquran" class="m-t-22">
				<p class="komponen-rapor">II. AL QURAN</p>
				<div class="m-l-18">
					<p class="text-uppercase fs-14 font-weight-bold">A. KHATAMAN</p>
					<div class="m-t-16 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 45%">
									Capaian Tilawah
								</th>
								<th style="width: 55%">
									Deskripsi
								</th>
							</tr>
							<tr>
	                            <td class="text-center" rowspan="2">
	                            	@if(isset($capaian['khataman']['type']))
	                            	@php $isKhatamanFilled = false @endphp
	                            	@if($capaian['khataman']['type']->type_id == 1 && $capaian['khataman']['quran'] && count($capaian['khataman']['quran']) > 0)
	                            	@php
		                            $khatamanQuran = null;
		                            $qCount = 1;
		                            foreach($capaian['khataman']['quran'] as $c){
		                            	if($c->jenis && $c->jenis->mem_type == 'Juz'){
				                            $status = $c->status ? $c->status->status : null;
				                            $surat[$qCount] = $c->juz ? $c->juz->juz : ($c->surat ? $c->surat->surah : null);
				                            if($status && $status != 'Penuh'){
				                                $surat[$qCount] = $surat[$qCount].' ('.$status.')';
				                            }
			                        	}
			                        	elseif($c->jenis && $c->jenis->mem_type == 'Surat'){
			                        		$surat[$qCount] = $c->surat ? $c->surat->surah.($c->verse ? ' ('.$c->verse.')' : null) : null;
			                        	}
			                        	if(isset($surat[$qCount])){
			                        		if(!$khatamanQuran) $khatamanQuran = $surat[$qCount];
			                        		else $khatamanQuran = $khatamanQuran.' - '.$surat[$qCount];
			                        	}
			                        	$qCount++;
			                        }
		                            @endphp
	                            	{{ $khatamanQuran ? $khatamanQuran : null }}
	                            	@php $isKhatamanFilled = true; @endphp
	                        		@elseif($capaian['khataman']['type']->type_id == 2 && $capaian['khataman']['buku'])
	                        		{{ $capaian['khataman']['buku'] }}
	                        		@php $isKhatamanFilled = true; @endphp
	                        		@endif
	                        		{!! isset($capaian['khataman']['type']) ? ($isKhatamanFilled ? '<br>' : null).$capaian['khataman']['type']->percentage.'%' : null !!}
	                        		@endif
	                            </td>
	                            <td class="text-center" style="vertical-align: middle">{!! isset($capaian['khataman']['kelancaran']['desc']) ? $capaian['khataman']['kelancaran']['desc'] : '&nbsp;' !!}</td>
	                        </tr>
	                        <tr>
	                        	<td class="text-center" style="vertical-align: middle">{!! isset($capaian['khataman']['kebagusan']['desc']) ? $capaian['khataman']['kebagusan']['desc'] : '&nbsp;' !!}</td>
	                        </tr>
						</table>
					</div>
				</div>
				<div class="m-l-18">
					<p class="text-uppercase fs-14 font-weight-bold">B. HAFALAN</p>
					@php
	                $j = null;
	                $b = $i = 1;
	                $kategori = 'quran';
	                @endphp
					<div class="m-t-16 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									B.{{ $b++ }}
								</th>
								<th style="width: 42%">
									Capaian Hafalan Quran
								</th>
								<th style="width: 55%">
									Deskripsi
								</th>
							</tr>
	                        @php
	                        $j = 'a';
	                        @endphp
	                        @if($capaian[$kategori]['hafalan'] && count($capaian[$kategori]['hafalan']) > 0)
	                        @foreach($capaian[$kategori]['hafalan'] as $c)
	                        <tr>
	                            <td class="text-center">{{ $i++ }}</td>
	                            @php
	                            $surat = null;
	                            if($c->jenis && $c->jenis->mem_type == 'Juz'){
		                            $status = $c->status ? $c->status->status : null;
		                            $surat = $c->juz ? $c->juz->juz : ($c->surat ? $c->surat->surah : null);
		                            if($status && $status != 'Penuh'){
		                                $surat = $surat.' ('.$status.')';
		                            }
	                        	}
	                        	elseif($c->jenis && $c->jenis->mem_type == 'Surat'){
	                        		$surat = $c->surat ? $c->surat->surah.($c->verse ? ' ('.$c->verse.')' : null) : null;
	                        	}
	                            @endphp
	                            <td>{!! $surat ? $surat : '&nbsp;' !!}</td>
	                            @if($j == 'a')
	                            <td class="text-center" rowspan="{{ count($capaian[$kategori]['hafalan']) }}" style="vertical-align: middle">{{ $capaian[$kategori]['desc'] }}</td>
	                            @endif
	                        </tr>
	                        @php
	                        $j = chr(ord($j)+1);
	                        @endphp
	                        @endforeach
	                        @else
	                        <tr>
	                            <td class="text-center">&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td class="text-center">&nbsp;</td>
	                        </tr>
	                        @endif
						</table>
					</div>
					@php
	                $jenis = null;
	                @endphp
	                @if($kategoriList['hafalan'] && count($kategoriList['hafalan']) > 0)
	                @foreach($kategoriList['hafalan'] as $kategori)
	                @php
	                $i = 1;
	                $kategori = $kategori->mem_type;
	                @endphp
	                @if($jenis != $kategori)
					<div class="m-t-16 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									B.{{ $b++ }}
								</th>
								<th style="width: 42%">
									Capaian Hafalan {{ $kategori }}
								</th>
								<th style="width: 55%">
									Deskripsi
								</th>
							</tr>
	                        @php
	                        $jenis = $kategori;
	                        $j = 'a';
	                        @endphp
	                        @endif
	                        @if($capaian[$kategori]['hafalan'] && count($capaian[$kategori]['hafalan']) > 0)
	                        @foreach($capaian[$kategori]['hafalan'] as $c)
	                        <tr>
	                            <td class="text-center">{{ $i++ }}</td>
	                            <td>{{ $c->desc }}</td>
	                            @if($j == 'a')
	                            <td class="text-center" rowspan="{{ count($capaian[$kategori]['hafalan']) }}" style="vertical-align: middle">{{ $capaian[$kategori]['desc'] }}</td>
	                            @endif
	                        </tr>
	                        @php
	                        $j = chr(ord($j)+1);
	                        @endphp
	                        @endforeach
	                        @else
	                        <tr>
	                            <td class="text-center">&nbsp;</td>
	                            <td>&nbsp;</td>
	                            <td class="text-center">&nbsp;</td>
	                        </tr>
	                        @endif
						</table>
					</div>
					@endforeach
					@endif
				</div>
			</div>
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div id="ekstrakurikuler" class="m-t-22">
				<p class="komponen-rapor">III. EKSTRAKURIKULER</p>
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 42%">
									Kegiatan Ekstrakurikuler
								</th>
								<th style="width: 55%">
									Deskripsi
								</th>
							</tr>
							@php $i = 1 @endphp
							@if($rapor->ekstra()->count() > 0)
							@foreach($rapor->ekstra()->get() as $e)
							<tr>
								<td class="text-center">
									{{ $i++ }}
								</td>
								<td class="text-center">
									{{ $e->extra_name }}
								</td>
								<td class="text-center">
									{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$e->description) }}
								</td>
							</tr>
							@endforeach
							@else
							<tr>
								<td class="text-center">&nbsp;</td>
								<td class="text-center">&nbsp;</td>
								<td class="text-center">&nbsp;</td>
							</tr>
							@endif
						</table>
					</div>
				</div>
			</div>
			<div id="ketidakhadiran" class="m-t-22">
				<p class="komponen-rapor">IV. KEHADIRAN</p>
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border" style="width: 50%">
							<tr>
								<td class="text-center" style="width: 8%">1</td>
								<td style="width: 42%">
									Jumlah Hari Efektif
								</td>
								<td class="text-center" style="width: 50%">
									{{ $rapor->kehadiran->effective_day ? $rapor->kehadiran->effective_day : '0' }} hari
								</td>
							</tr>
							<tr>
								<td class="text-center">2</td>
								<td>
									Sakit
								</td>
								<td class="text-center">
									{{ $rapor->kehadiran->sick ? $rapor->kehadiran->sick : '0' }} hari
								</td>
							</tr>
							<tr>
								<td class="text-center">3</td>
								<td>Izin</td>
								<td class="text-center">
									{{ $rapor->kehadiran->leave ? $rapor->kehadiran->leave : '0' }} hari
								</td>
							</tr>
							<tr>
								<td class="text-center">4</td>
								<td>Alpa</td>
								<td class="text-center">
									{{ $rapor->kehadiran->absent ? $rapor->kehadiran->absent : '0' }} hari
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div id="catatanWaliKelas" class="m-t-22">
				<p class="komponen-rapor">V. CATATAN GURU</p>
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border table-catatan" style="width: 100%">
							<tr>
								<td>{{ $rapor->pas_tk ? str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->pas_tk->notes) : null }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			@if($rapor->kelas->level->level != 'TK B' && $semester->semester == 'Genap' && ($rapor->pas_tk && $rapor->pas_tk->conclusion))
			<div id="keputusan" class="m-t-44">
				<div class="m-l-18">
					<div class="m-b-16">
						<table class="table-keputusan">
							<tr>
								<td class="font-weight-bold">Keputusan:</td>
							</tr>
							<tr>
								<td style="padding-top: 0; padding-bottom: 2px;">
									Berdasarkan pencapaian kompetensi pada semester ke-1 dan ke-2, peserta didik ditetapkan
								</td>
							</tr>
							<tr>
								<td class="font-weight-bold" style="padding-top: 2px;">
									<span class="fs-14">
										@if($rapor->pas_tk->conclusion == 'naik')
										@php
										$nextLevel = $rapor->kelas->level->nextLevel()->first();
										@endphp
										@if($nextLevel)
										Naik ke {{ $nextLevel->level }}
										@endif
										@elseif($rapor->pas_tk->conclusion == 'tinggal')
										Tinggal di {{ $rapor->kelas->level->level }}
										@endif
									</span>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			@endif
			@if($rapor->report_status_id == 1)
			<div id="tandaTangan" class="m-t-33">
				<table class="tanda-tangan">
					<tr>
						<td>&nbsp;</td>
						<td>Tangerang Selatan, {{ $pas_date ? Date::parse($pas_date)->format('j F Y') : Date::now('Asia/Jakarta')->format('j F Y') }}</td>
					</tr>
					<tr>
						<td>Mengetahui,</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Orang Tua/Wali,</td>
						<td>Wali Kelas,</td>
					</tr>
					<tr>
						<td class="ttd">&nbsp;</td>
						<td class="ttd">
							{!! $digital && $pas_date && $rapor->hr_name ? QrCode::size(84)->generate('Dokumen Rapor Elektronik ini sah dan sudah difinalisasi oleh Wali Kelas '.$unit->desc.', '.$rapor->hr_name.' pada '.Date::parse($pas_date)->format('l, j F Y').' melalui SISTA Auliya') : '&nbsp;' !!}
						</td>
					</tr>
					<tr>
						<td>{{ $siswa->identitas->orangtua->father_name ? $siswa->identitas->orangtua->father_name : ($siswa->identitas->orangtua->mother_name ? $siswa->identitas->orangtua->father_name : ($siswa->identitas->orangtua->guardian_name ? $siswa->identitas->orangtua->guardian_name : '...')) }}</td>
						<td>{{ $rapor->hr_name }}</td>
					</tr>
					<tr>
						<td colspan="2">Mengetahui,</td>
					</tr>
					<tr>
						<td colspan="2">Kepala Sekolah,</td>
					</tr>
					<tr>
						<td class="ttd" colspan="2">
							{!! $digital && $pas_date && $rapor->hm_name ? QrCode::size(84)->generate('Dokumen Rapor Elektronik ini sah dan sudah divalidasi oleh Kepala '.$unit->desc.', '.$rapor->hm_name.' pada '.Date::parse($pas_date)->format('l, j F Y').' melalui SISTA Auliya') : '&nbsp;' !!}
						</td>
					</tr>
					<tr>
						<td colspan="2">{{ $rapor->hm_name }}</td>
					</tr>
				</table>
			</div>
			@else
			<div class="m-t-33">
				<table class="unvalidated">
					<tr>
						<td class="text-center text-uppercase font-weight-bold">Dokumen rapor ini belum divalidasi</td>
					</tr>
				</table>
			</div>
			@endif
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div id="lampiranKurikulumTilawah">
				<p class="komponen-rapor">I. INDIKATOR KOMPETENSI TILAWAH</p>
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 37%">
									Kompetensi
								</th>
								<th style="width: 60%">
									Indikator
								</th>
							</tr>
							<tr>
								<td class="text-center" rowspan="4" style="vertical-align: top">1</td>
								<td rowspan="4" style="vertical-align: top">Nama Huruf dan Tanda Baca</td>
								<td>a. Nama Huruf Hijaiyyah</td>
							</tr>
							<tr>
								<td>b. Tanda baca A-I-U</td>
							</tr>
							<tr>
								<td>c. Tanda baca AN-IN-UN</td>
							</tr>
							<tr>
								<td>d. Huruf Sambung</td>
							</tr>
							<tr>
								<td class="text-center" rowspan="4" style="vertical-align: top">2</td>
								<td rowspan="4" style="vertical-align: top">Tajwid Dasar</td>
								<td>a. Huruf Mati</td>
							</tr>
							<tr>
								<td>b. Huruf Panjang</td>
							</tr>
							<tr>
								<td>c. Huruf Ganda</td>
							</tr>
							<tr>
								<td>d. Huruf Terpisah di Awal Surat</td>
							</tr>
							<tr>
								<td class="text-center" rowspan="8" style="vertical-align: top">3</td>
								<td rowspan="8" style="vertical-align: top">Tajwid Lengkap</td>
								<td>a. 4 Jenis Nun Mati / Tanwin</td>
							</tr>
							<tr>
								<td>b. Bacaan Lam dan Ro</td>
							</tr>
							<tr>
								<td>c. 3 Jenis Mim Mati</td>
							</tr>
							<tr>
								<td>d. Angka Arab</td>
							</tr>
							<tr>
								<td>e. 9 Jenis Tanda Berhenti</td>
							</tr>
							<tr>
								<td>f. Bacaan Panjang I</td>
							</tr>
							<tr>
								<td>g. Bacaan Panjang II</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.print.print_window')
@endsection