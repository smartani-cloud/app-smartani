<<<<<<< HEAD
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
						<td style="width: 15%">
							Fase/Kelas
						</td>
						<td style="width: 2%">
							:
						</td>
						<td style="width: 24%">
							{{ $rapor->kelas->level->phase->name }} / {{ $rapor->kelas->level->level_romawi }}{{ $rapor->kelas->jurusan ? ' '.$rapor->kelas->jurusan->major_name.' ' : ' ' }}{{ $rapor->kelas->namakelases->class_name }}
						</td>
					</tr>
					<tr>
						@if($unit->id == 1)
						<td>NIPD</td>
						<td>:</td>
						<td>{{ $siswa->student_nis }}</td>
						@else
						<td>NISN</td>
						<td>:</td>
						<td>{{ $siswa->student_nisn }}</td>
						@endif
						<td>Semester</td>
						<td>:</td>
						<td>{{ $semester->semester }}</td>
					</tr>
					<tr>
						@if($unit->id == 1)
						<td colspan="3">&nbsp;</td>
						@else
						<td>NIPD</td>
						<td>:</td>
						<td>{{ $siswa->student_nis }}</td>
						@endif
						<td>Tahun Pelajaran</td>
						<td>:</td>
						<td>{{ $semester->tahunAjaran->academic_year }}</td>
					</tr>
				</table>
			</div>
			<div id="kurikulumMerdeka" class="m-t-22">
				<p class="komponen-rapor">I. KURIKULUM MERDEKA</p>
				<div class="m-t-16 m-b-16">
					<table class="table-border page-break-auto">
						@if($unit->id == 1)
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 37%">
								Elemen Capaian Pembelajaran
							</th>
							<th style="width: 60%">
								Deskripsi
							</th>
						</tr>
						@if($objectives && count($objectives) > 0)
                        @php $catActive = null; $i = 1; @endphp
                        @foreach($objectives as $o)
                        @if($catActive != $o->element_id)
                        @php $catActive = $o->element_id; @endphp
						<tr>
							<td class="text-center align-middle" rowspan="2">{{ $i++ }}</td>
							<td class="align-middle" rowspan="2">{{ $o->element->dev_aspect }}</td>
							<td class="align-middle">{!! isset($nilai['perkembangan'][$o->element_id]['tinggi']) ? $nilai['perkembangan'][$o->element_id]['tinggi'] : '&nbsp;' !!}</td>
						</tr>
						<tr>
							<td class="align-middle">
								{!! isset($nilai['perkembangan'][$o->element_id]['rendah']) ? $nilai['perkembangan'][$o->element_id]['rendah'] : '&nbsp;' !!}
							</td>
						</tr>
                        @endif
						@endforeach
						@else
						<tr>
							<td class="text-center align-middle" rowspan="2">&nbsp;</td>
							<td class="align-middle" rowspan="2">&nbsp;</td>
							<td class="align-middle">&nbsp;</td>
						</tr>
						<tr>
							<td class="align-middle">&nbsp;</td>
						</tr>
						@endif
						@else
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 26%">
								Mata Pelajaran
							</th>
							<th style="width: 10%">
								Nilai Akhir
							</th>
							<th style="width: 61%">
								Capaian Kompetensi
							</th>
						</tr>
						@endif
						@if($unit->id != 1)
						@php
						$active = null;
						$totalPengetahuan = $totalKeterampilan = $mapelCount = $kCounter = 0;
						@endphp
						@foreach($kelompok as $k)
						@if($k->matapelajarans()->count())
            			@if($active != $k->id && $unit->name != 'SD')
            			@if($kCounter == 1)
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div class="m-t-22">
				<div class="m-t-16 m-b-16">
					<table class="table-border page-break-auto">
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 26%">
								Mata Pelajaran
							</th>
							<th style="width: 10%">
								Nilai Akhir
							</th>
							<th style="width: 61%">
								Capaian Kompetensi
							</th>
						</tr>
            			@endif
						@php
						$kCounter++;
						$active = $k->id;
						@endphp
						@endif
						@php
						$i = 1;
						$matapelajarans = $k->matapelajarans()->whereNull('is_mulok')->whereHas('jadwalPelajaran',function($q)use($rapor,$semester){
						    $q->where([
						        'level_id' => $rapor->kelas->level_id,
						        'class_id' => $rapor->kelas->id,
						        'semester_id' => $semester->id,
						    ]);
						})->orderBy('subject_number');
						if($unit->name == 'SD'){
                            $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                                $q->where('level_id',$rapor->kelas->level_id);
                            });
                        }
						$mapelCount += $matapelajarans->count();
                        $matapelajarans = $matapelajarans->get();
						@endphp
						@foreach($matapelajarans as $m)
						<tr>
							<td class="text-center" rowspan="2" style="vertical-align: middle">{{ $i++ }}</td>
							<td rowspan="2" style="vertical-align: middle">{{ $m->subject_name }}</td>
							<td class="text-center" rowspan="2" style="vertical-align: middle">{!! isset($nilai['mapel'][$m->id]['akhir']) ? $nilai['mapel'][$m->id]['akhir'] : '&nbsp;' !!}</td>
							<td class="text-center">{!! isset($nilai['mapel'][$m->id]['tinggi']) ? $nilai['mapel'][$m->id]['tinggi'] : '&nbsp;' !!}</td>
						</tr>
						<tr>
						    <td class="text-center">{!! isset($nilai['mapel'][$m->id]['rendah']) ? $nilai['mapel'][$m->id]['rendah'] : '&nbsp;' !!}</td>
						<tr>
						@endforeach
						@php
						$j = 'a';
						$matapelajarans = $k->matapelajarans()->mulok()->whereHas('jadwalPelajaran',function($q)use($rapor,$semester){
						    $q->where([
						        'level_id' => $rapor->kelas->level_id,
						        'class_id' => $rapor->kelas->id,
						        'semester_id' => $semester->id,
						    ]);
						})->orderBy('subject_number');
						if($unit->name == 'SD'){
                            $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                                $q->where('level_id',$rapor->kelas->level_id);
                            });
                        }
						$mapelCount += $matapelajarans->count();
                        $matapelajarans = $matapelajarans->get();
						@endphp
						@if(count($matapelajarans) > 0)
						@foreach($matapelajarans as $m)
						@if($j == 'a')
						<tr>
							<td colspan="4">Muatan Lokal</td>
						</tr>
						@endif
						<tr>
							<td class="text-center" rowspan="2" style="vertical-align: middle">{{ $i++ }}</td>
							<td rowspan="2" style="vertical-align: middle">{{ $m->subject_name }}</td>
							<td class="text-center" rowspan="2" style="vertical-align: middle">{!! isset($nilai['mapel'][$m->id]['akhir']) ? $nilai['mapel'][$m->id]['akhir'] : '&nbsp;' !!}</td>
							<td class="text-center">{!! isset($nilai['mapel'][$m->id]['tinggi']) ? $nilai['mapel'][$m->id]['tinggi'] : '&nbsp;' !!}</td>
						</tr>
						<tr>
						    <td class="text-center">{!! isset($nilai['mapel'][$m->id]['rendah']) ? $nilai['mapel'][$m->id]['rendah'] : '&nbsp;' !!}</td>
						<tr>
						@php
						$j = chr(ord($j)+1);
						@endphp
						@endforeach
						@endif
						@endif
						@endforeach
						@endif
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div id="alquran" class="m-t-22">
				<p class="komponen-rapor">II. AL QURAN</p>
				<p class="text-uppercase fs-14 font-weight-bold m-l-16"">A. KHATAMAN</p>
				<div class="m-t-16 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
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
				<p class="text-uppercase fs-14 font-weight-bold m-l-16"">B. HAFALAN</p>
				@php
                $j = null;
                $b = $i = 1;
                $kategori = 'quran';
                @endphp
				<div class="m-t-16 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
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
						<tr style="background-color: #C6E0B4">
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
				@php $tadabur = false @endphp
				@if($rapor->kelas->level->level == 1 && $tadabur)
				<p class="text-uppercase fs-14 font-weight-bold m-l-16"">C. TADABUR</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border table-catatan" style="width: 100%">
						<tr>
						    @if($rapor->pas && $rapor->pas->notes)
							<td>{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->pas->notes) }}</td>
							@else
							<td>&nbsp;</td>
							@endif
						</tr>
					</table>
				</div>
				@endif
			</div>
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div id="ekstrakurikuler" class="m-t-22">
				<p class="komponen-rapor">III. EKSTRAKURIKULER</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 42%">
								Ekstrakurikuler
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
			<div id="prestasi" class="m-t-22">
				<p class="komponen-rapor">IV. JUARA</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 42%">
								Kejuaraan
							</th>
							<th style="width: 55%">
								Deskripsi
							</th>
						</tr>
						@php $i = 1 @endphp
						@if($rapor->prestasi()->count() > 0)
						@foreach($rapor->prestasi()->get() as $p)
						<tr>
							<td class="text-center">
								{{ $i++ }}
							</td>
							<td class="text-center">
								{{ $p->achievement_name }}
							</td>
							<td class="text-center">
								{{ $p->description }}
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
			<div id="ketidakhadiran" class="m-t-22">
				<p class="komponen-rapor">V. KEHADIRAN</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
							<th class="text-center" colspan="2" style="width: 100%">Kehadiran</th>
						</tr>
						<tr>
							<td class="text-center" style="width: 45%">Jumlah Hari Efektif</td>
							<td class="text-center" style="width: 55%">
								{{ $rapor->kehadiran ? $rapor->kehadiran->effective_day : '0' }} hari
							</td>
						</tr>
						<tr>
							<td class="text-center">Sakit</td>
							<td class="text-center">
								{{ $rapor->kehadiran ? $rapor->kehadiran->sick : '0' }} hari
							</td>
						</tr>
						<tr>
							<td class="text-center">Izin</td>
							<td class="text-center">
								{{ $rapor->kehadiran ? $rapor->kehadiran->leave : '0' }} hari
							</td>
						</tr>
						<tr>
							<td class="text-center">Alpa</td>
							<td class="text-center">
								{{ $rapor->kehadiran ? $rapor->kehadiran->absent : '0' }} hari
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="refleksiWaliKelas" class="m-t-22">
				<p class="komponen-rapor">VI. REFLEKSI WALI KELAS</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border table-catatan" style="width: 100%">
						<tr>
						    @php
						    $pas = $unit->id == 1 ? 'pas_tk' : 'pas';
						    @endphp
						    @if($rapor->{$pas} && $rapor->{$pas}->notes)
							<td>{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->{$pas}->notes) }}</td>
							@else
							<td>&nbsp;</td>
							@endif
						</tr>
					</table>
				</div>
			</div>
			@if($unit->id == 1)			
			<div id="refleksiOrangTua" class="m-t-22">
				<p class="komponen-rapor">VII. REFLEKSI ORANG TUA</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border table-catatan" style="width: 100%">
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
			@endif
			@if(!in_array($rapor->kelas->level->level,array('6','9','12')) && $semester->semester == 'Genap' && ($rapor->pas && $rapor->pas->conclusion))
			<div id="keputusan" class="m-t-44">
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
									@if($rapor->pas && $rapor->pas->conclusion == 'naik')
									@php
									$nextLevel = $rapor->kelas->level->nextLevel()->first();
									@endphp
									@if($nextLevel)
									Naik ke kelas {{ $nextLevel->level_romawi }} ({{ $nextLevel->level_char }})
									@endif
									@elseif($rapor->pas && $rapor->pas->conclusion == 'tinggal')
									Tinggal di kelas {{ $rapor->kelas->level->level_romawi }} ({{ $rapor->kelas->level->level_char }})
									@endif
								</span>
							</td>
						</tr>
					</table>
				</div>
			</div>
			@endif
			@if(Auth::user()->role->name == 'kepsek' || (Auth::user()->role->name != 'kepsek' && $rapor->report_status_id == 1))
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
            <p class="komponen-rapor">LAMPIRAN</p>
            @if($unit->id == 1)
            <div id="lampiranElemenCapaianPembelajaran">
                <p class="komponen-rapor">I. ELEMEN CAPAIAN PEMBELAJARAN</p>
                <div class="m-t-8 m-b-16">
                    <table class="table-border page-break-auto">
						<tr style="background-color: #C6E0B4">
                            <th style="width: 3%">
								No
							</th>
							<th style="width: 42%">
								Elemen Capaian Pembelajaran
							</th>
							<th style="width: 55%">
								Tujuan Pembelajaran
							</th>
						</tr>
						@if($objectives && count($objectives) > 0)
                        @php $catActive = null; $i = 1; @endphp
                        @foreach($objectives as $o)
                        @if($catActive != $o->element_id)
                        @php $rowspan = $objectives->where('element_id',$o->element_id)->count(); @endphp
						<tr>
							<td class="text-center align-middle" {!! 'rowspan="'.$rowspan.'"' !!}>{{ $i++ }}</td>
							<td class="align-middle" {!! 'rowspan="'.$rowspan.'"' !!}>{{ $o->element->dev_aspect }}</td>
							<td class="align-middle">{{ $o->number.'. '.$o->objective->desc }}</td>
						</tr>
                        @php $catActive = $o->element_id; @endphp
                        @else
                        <tr>
                            <td class="align-middle">{{ $o->number.'. '.$o->objective->desc }}</td>
                        </tr>
                        @endif
						@endforeach
						@else
						<tr>
							<td class="text-center align-middle">&nbsp;</td>
							<td class="align-middle">&nbsp;</td>
							<td class="align-middle">&nbsp;</td>
						</tr>
						@endif
                    </table>
                </div>
            </div>
			@else
            <div id="lampiranIndikatorPengetahuan">
                <p class="komponen-rapor">I. INDIKATOR PEMBELAJARAN</p>
                <div class="m-t-8 m-b-16">
                    <table class="table-border page-break-auto">
						<tr style="background-color: #C6E0B4">
                            <th style="width: 3%">
                                No
                            </th>
                            <th style="width: 42%">
                                Mata Pelajaran
                            </th>
                            <th style="width: 55%">
                                Tujuan Pembelajaran
                            </th>
                        </tr>
                        @php
                        $active = null;
                        @endphp
                        @foreach($kelompok as $k)
                        @if($k->matapelajarans()->count())
                        @if($active != $k->id && $unit->name != 'SD')
                        <tr>
                            <td class="font-weight-bold" colspan="3">{{ $k->group_subject_name }} {{ $k->jurusan ? $k->jurusan->major_name : '' }}</td>
                        </tr>
                        @php $active = $k->id @endphp
                        @endif
                        @php
                        $i = 1;
						$matapelajarans = $k->matapelajarans()->whereNull('is_mulok')->whereHas('jadwalPelajaran',function($q)use($rapor,$semester){
						    $q->where([
						        'level_id' => $rapor->kelas->level_id,
						        'class_id' => $rapor->kelas->id,
						        'semester_id' => $semester->id,
						    ]);
						})->orderBy('subject_number');
						if($unit->name == 'SD'){
                            $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                                $q->where('level_id',$rapor->kelas->level_id);
                            });
                        }
						$mapelCount += $matapelajarans->count();
                        $matapelajarans = $matapelajarans->get();
                        @endphp
                        @foreach($matapelajarans as $m)
                        @php
                        $subject_active = null;
                        @endphp
                        @if(isset($tpDescs[$m->id]))
                        @foreach($tpDescs[$m->id] as $tpDesc)
                        <tr>
                            @if($subject_active != $m->id)
                            <td class="text-center" rowspan="{{ count($tpDescs[$m->id]) }}">{{ $i++ }}</td>
                            <td rowspan="{{ count($tpDescs[$m->id]) }}">{{ $m->subject_name }}</td>
                            @php
                            $subject_active = $m->id;
                            $j = 'a';
                            @endphp
                            @endif
                            <td>
                                {{ $j.'. '.$tpDesc->desc }}
                            </td>
                            @php
                            $j = chr(ord($j)+1);
                            @endphp
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td>{{ $m->subject_name }}</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endif
                        @endforeach
                        @php
                        $j = 'a';
                        $matapelajarans = $k->matapelajarans()->mulok()->whereHas('jadwalPelajaran',function($q)use($rapor,$semester){
						    $q->where([
						        'level_id' => $rapor->kelas->level_id,
						        'class_id' => $rapor->kelas->id,
						        'semester_id' => $semester->id,
						    ]);
						})->orderBy('subject_number');
						if($unit->name == 'SD'){
                            $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                                $q->where('level_id',$rapor->kelas->level_id);
                            });
                        }
						$mapelCount += $matapelajarans->count();
                        $matapelajarans = $matapelajarans->get();
                        @endphp
                        @if(count($matapelajarans) > 0)
                        @foreach($matapelajarans as $m)
                        @if($j == 'a')
                        <tr>
                            <td colspan="3">Muatan Lokal</td>
                        </tr>
                        @endif
                        @php
                        $subject_active = null;
                        @endphp
                        @if(isset($tpDescs[$m->id]))
                        @foreach($tpDescs[$m->id] as $tpDesc)
                        <tr>
                            @if($subject_active != $m->id)
                            <td class="text-center" rowspan="{{ count($tpDescs[$m->id]) }}">{{ $i++ }}</td>
                            <td rowspan="{{ count($tpDescs[$m->id]) }}">{{ $m->subject_name }}</td>
                            @php
                            $subject_active = $m->id;
                            $k = 'a';
                            @endphp
                            @endif
                            <td>
                                {{ $k.'. '.$tpDesc->desc }}
                            </td>
                            @php
                            $k = chr(ord($k)+1);
                            @endphp
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td>{{ $m->subject_name }}</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endif
                        @php
                        $j = chr(ord($j)+1);
                        @endphp
                        @endforeach
                        @endif
                        @endif
                        @endforeach
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
    @if($unit->id != 1)
	<div class="page">
		<div class="subpage">
			<div id="lampiranKurikulumTilawah">
				<p class="komponen-rapor">II. INDIKATOR KOMPETENSI TILAWAH</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 37%">
								Capaian
							</th>
							<th style="width: 60%">
								Indikator
							</th>
						</tr>
						<tr>
							<td class="text-center" rowspan="4" style="vertical-align: middle">1</td>
							<td class="text-center" rowspan="4" style="vertical-align: middle">Nama Huruf dan Tanda Baca</td>
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
							<td class="text-center" rowspan="4" style="vertical-align: middle">2</td>
							<td class="text-center" rowspan="4" style="vertical-align: middle">Tajwid Dasar</td>
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
							<td class="text-center" rowspan="8" style="vertical-align: middle">3</td>
							<td class="text-center" rowspan="8" style="vertical-align: middle">Tajwid Lengkap</td>
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
	@endif
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.print.print_window')
=======
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
						<td style="width: 15%">
							Fase/Kelas
						</td>
						<td style="width: 2%">
							:
						</td>
						<td style="width: 24%">
							{{ $rapor->kelas->level->phase->name }} / {{ $rapor->kelas->level->level_romawi }}{{ $rapor->kelas->jurusan ? ' '.$rapor->kelas->jurusan->major_name.' ' : ' ' }}{{ $rapor->kelas->namakelases->class_name }}
						</td>
					</tr>
					<tr>
						@if($unit->id == 1)
						<td>NIPD</td>
						<td>:</td>
						<td>{{ $siswa->student_nis }}</td>
						@else
						<td>NISN</td>
						<td>:</td>
						<td>{{ $siswa->student_nisn }}</td>
						@endif
						<td>Semester</td>
						<td>:</td>
						<td>{{ $semester->semester }}</td>
					</tr>
					<tr>
						@if($unit->id == 1)
						<td colspan="3">&nbsp;</td>
						@else
						<td>NIPD</td>
						<td>:</td>
						<td>{{ $siswa->student_nis }}</td>
						@endif
						<td>Tahun Pelajaran</td>
						<td>:</td>
						<td>{{ $semester->tahunAjaran->academic_year }}</td>
					</tr>
				</table>
			</div>
			<div id="kurikulumMerdeka" class="m-t-22">
				<p class="komponen-rapor">I. KURIKULUM MERDEKA</p>
				<div class="m-t-16 m-b-16">
					<table class="table-border page-break-auto">
						@if($unit->id == 1)
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 37%">
								Elemen Capaian Pembelajaran
							</th>
							<th style="width: 60%">
								Deskripsi
							</th>
						</tr>
						@if($objectives && count($objectives) > 0)
                        @php $catActive = null; $i = 1; @endphp
                        @foreach($objectives as $o)
                        @if($catActive != $o->element_id)
                        @php $catActive = $o->element_id; @endphp
						<tr>
							<td class="text-center align-middle" rowspan="2">{{ $i++ }}</td>
							<td class="align-middle" rowspan="2">{{ $o->element->dev_aspect }}</td>
							<td class="align-middle">{!! isset($nilai['perkembangan'][$o->element_id]['tinggi']) ? $nilai['perkembangan'][$o->element_id]['tinggi'] : '&nbsp;' !!}</td>
						</tr>
						<tr>
							<td class="align-middle">
								{!! isset($nilai['perkembangan'][$o->element_id]['rendah']) ? $nilai['perkembangan'][$o->element_id]['rendah'] : '&nbsp;' !!}
							</td>
						</tr>
                        @endif
						@endforeach
						@else
						<tr>
							<td class="text-center align-middle" rowspan="2">&nbsp;</td>
							<td class="align-middle" rowspan="2">&nbsp;</td>
							<td class="align-middle">&nbsp;</td>
						</tr>
						<tr>
							<td class="align-middle">&nbsp;</td>
						</tr>
						@endif
						@else
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 26%">
								Mata Pelajaran
							</th>
							<th style="width: 10%">
								Nilai Akhir
							</th>
							<th style="width: 61%">
								Capaian Kompetensi
							</th>
						</tr>
						@endif
						@if($unit->id != 1)
						@php
						$active = null;
						$totalPengetahuan = $totalKeterampilan = $mapelCount = $kCounter = 0;
						@endphp
						@foreach($kelompok as $k)
						@if($k->matapelajarans()->count())
            			@if($active != $k->id && $unit->name != 'SD')
            			@if($kCounter == 1)
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div class="m-t-22">
				<div class="m-t-16 m-b-16">
					<table class="table-border page-break-auto">
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 26%">
								Mata Pelajaran
							</th>
							<th style="width: 10%">
								Nilai Akhir
							</th>
							<th style="width: 61%">
								Capaian Kompetensi
							</th>
						</tr>
            			@endif
						@php
						$kCounter++;
						$active = $k->id;
						@endphp
						@endif
						@php
						$i = 1;
						$matapelajarans = $k->matapelajarans()->whereNull('is_mulok')->whereHas('jadwalPelajaran',function($q)use($rapor,$semester){
						    $q->where([
						        'level_id' => $rapor->kelas->level_id,
						        'class_id' => $rapor->kelas->id,
						        'semester_id' => $semester->id,
						    ]);
						})->orderBy('subject_number');
						if($unit->name == 'SD'){
                            $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                                $q->where('level_id',$rapor->kelas->level_id);
                            });
                        }
						$mapelCount += $matapelajarans->count();
                        $matapelajarans = $matapelajarans->get();
						@endphp
						@foreach($matapelajarans as $m)
						<tr>
							<td class="text-center" rowspan="2" style="vertical-align: middle">{{ $i++ }}</td>
							<td rowspan="2" style="vertical-align: middle">{{ $m->subject_name }}</td>
							<td class="text-center" rowspan="2" style="vertical-align: middle">{!! isset($nilai['mapel'][$m->id]['akhir']) ? $nilai['mapel'][$m->id]['akhir'] : '&nbsp;' !!}</td>
							<td class="text-center">{!! isset($nilai['mapel'][$m->id]['tinggi']) ? $nilai['mapel'][$m->id]['tinggi'] : '&nbsp;' !!}</td>
						</tr>
						<tr>
						    <td class="text-center">{!! isset($nilai['mapel'][$m->id]['rendah']) ? $nilai['mapel'][$m->id]['rendah'] : '&nbsp;' !!}</td>
						<tr>
						@endforeach
						@php
						$j = 'a';
						$matapelajarans = $k->matapelajarans()->mulok()->whereHas('jadwalPelajaran',function($q)use($rapor,$semester){
						    $q->where([
						        'level_id' => $rapor->kelas->level_id,
						        'class_id' => $rapor->kelas->id,
						        'semester_id' => $semester->id,
						    ]);
						})->orderBy('subject_number');
						if($unit->name == 'SD'){
                            $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                                $q->where('level_id',$rapor->kelas->level_id);
                            });
                        }
						$mapelCount += $matapelajarans->count();
                        $matapelajarans = $matapelajarans->get();
						@endphp
						@if(count($matapelajarans) > 0)
						@foreach($matapelajarans as $m)
						@if($j == 'a')
						<tr>
							<td colspan="4">Muatan Lokal</td>
						</tr>
						@endif
						<tr>
							<td class="text-center" rowspan="2" style="vertical-align: middle">{{ $i++ }}</td>
							<td rowspan="2" style="vertical-align: middle">{{ $m->subject_name }}</td>
							<td class="text-center" rowspan="2" style="vertical-align: middle">{!! isset($nilai['mapel'][$m->id]['akhir']) ? $nilai['mapel'][$m->id]['akhir'] : '&nbsp;' !!}</td>
							<td class="text-center">{!! isset($nilai['mapel'][$m->id]['tinggi']) ? $nilai['mapel'][$m->id]['tinggi'] : '&nbsp;' !!}</td>
						</tr>
						<tr>
						    <td class="text-center">{!! isset($nilai['mapel'][$m->id]['rendah']) ? $nilai['mapel'][$m->id]['rendah'] : '&nbsp;' !!}</td>
						<tr>
						@php
						$j = chr(ord($j)+1);
						@endphp
						@endforeach
						@endif
						@endif
						@endforeach
						@endif
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div id="alquran" class="m-t-22">
				<p class="komponen-rapor">II. AL QURAN</p>
				<p class="text-uppercase fs-14 font-weight-bold m-l-16"">A. KHATAMAN</p>
				<div class="m-t-16 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
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
				<p class="text-uppercase fs-14 font-weight-bold m-l-16"">B. HAFALAN</p>
				@php
                $j = null;
                $b = $i = 1;
                $kategori = 'quran';
                @endphp
				<div class="m-t-16 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
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
						<tr style="background-color: #C6E0B4">
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
				@php $tadabur = false @endphp
				@if($rapor->kelas->level->level == 1 && $tadabur)
				<p class="text-uppercase fs-14 font-weight-bold m-l-16"">C. TADABUR</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border table-catatan" style="width: 100%">
						<tr>
						    @if($rapor->pas && $rapor->pas->notes)
							<td>{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->pas->notes) }}</td>
							@else
							<td>&nbsp;</td>
							@endif
						</tr>
					</table>
				</div>
				@endif
			</div>
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div id="ekstrakurikuler" class="m-t-22">
				<p class="komponen-rapor">III. EKSTRAKURIKULER</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 42%">
								Ekstrakurikuler
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
			<div id="prestasi" class="m-t-22">
				<p class="komponen-rapor">IV. JUARA</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 42%">
								Kejuaraan
							</th>
							<th style="width: 55%">
								Deskripsi
							</th>
						</tr>
						@php $i = 1 @endphp
						@if($rapor->prestasi()->count() > 0)
						@foreach($rapor->prestasi()->get() as $p)
						<tr>
							<td class="text-center">
								{{ $i++ }}
							</td>
							<td class="text-center">
								{{ $p->achievement_name }}
							</td>
							<td class="text-center">
								{{ $p->description }}
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
			<div id="ketidakhadiran" class="m-t-22">
				<p class="komponen-rapor">V. KEHADIRAN</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
							<th class="text-center" colspan="2" style="width: 100%">Kehadiran</th>
						</tr>
						<tr>
							<td class="text-center" style="width: 45%">Jumlah Hari Efektif</td>
							<td class="text-center" style="width: 55%">
								{{ $rapor->kehadiran ? $rapor->kehadiran->effective_day : '0' }} hari
							</td>
						</tr>
						<tr>
							<td class="text-center">Sakit</td>
							<td class="text-center">
								{{ $rapor->kehadiran ? $rapor->kehadiran->sick : '0' }} hari
							</td>
						</tr>
						<tr>
							<td class="text-center">Izin</td>
							<td class="text-center">
								{{ $rapor->kehadiran ? $rapor->kehadiran->leave : '0' }} hari
							</td>
						</tr>
						<tr>
							<td class="text-center">Alpa</td>
							<td class="text-center">
								{{ $rapor->kehadiran ? $rapor->kehadiran->absent : '0' }} hari
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div id="refleksiWaliKelas" class="m-t-22">
				<p class="komponen-rapor">VI. REFLEKSI WALI KELAS</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border table-catatan" style="width: 100%">
						<tr>
						    @php
						    $pas = $unit->id == 1 ? 'pas_tk' : 'pas';
						    @endphp
						    @if($rapor->{$pas} && $rapor->{$pas}->notes)
							<td>{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->{$pas}->notes) }}</td>
							@else
							<td>&nbsp;</td>
							@endif
						</tr>
					</table>
				</div>
			</div>
			@if($unit->id == 1)			
			<div id="refleksiOrangTua" class="m-t-22">
				<p class="komponen-rapor">VII. REFLEKSI ORANG TUA</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border table-catatan" style="width: 100%">
						<tr>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
			</div>
			@endif
			@if(!in_array($rapor->kelas->level->level,array('6','9','12')) && $semester->semester == 'Genap' && ($rapor->pas && $rapor->pas->conclusion))
			<div id="keputusan" class="m-t-44">
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
									@if($rapor->pas && $rapor->pas->conclusion == 'naik')
									@php
									$nextLevel = $rapor->kelas->level->nextLevel()->first();
									@endphp
									@if($nextLevel)
									Naik ke kelas {{ $nextLevel->level_romawi }} ({{ $nextLevel->level_char }})
									@endif
									@elseif($rapor->pas && $rapor->pas->conclusion == 'tinggal')
									Tinggal di kelas {{ $rapor->kelas->level->level_romawi }} ({{ $rapor->kelas->level->level_char }})
									@endif
								</span>
							</td>
						</tr>
					</table>
				</div>
			</div>
			@endif
			@if(Auth::user()->role->name == 'kepsek' || (Auth::user()->role->name != 'kepsek' && $rapor->report_status_id == 1))
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
            <p class="komponen-rapor">LAMPIRAN</p>
            @if($unit->id == 1)
            <div id="lampiranElemenCapaianPembelajaran">
                <p class="komponen-rapor">I. ELEMEN CAPAIAN PEMBELAJARAN</p>
                <div class="m-t-8 m-b-16">
                    <table class="table-border page-break-auto">
						<tr style="background-color: #C6E0B4">
                            <th style="width: 3%">
								No
							</th>
							<th style="width: 42%">
								Elemen Capaian Pembelajaran
							</th>
							<th style="width: 55%">
								Tujuan Pembelajaran
							</th>
						</tr>
						@if($objectives && count($objectives) > 0)
                        @php $catActive = null; $i = 1; @endphp
                        @foreach($objectives as $o)
                        @if($catActive != $o->element_id)
                        @php $rowspan = $objectives->where('element_id',$o->element_id)->count(); @endphp
						<tr>
							<td class="text-center align-middle" {!! 'rowspan="'.$rowspan.'"' !!}>{{ $i++ }}</td>
							<td class="align-middle" {!! 'rowspan="'.$rowspan.'"' !!}>{{ $o->element->dev_aspect }}</td>
							<td class="align-middle">{{ $o->number.'. '.$o->objective->desc }}</td>
						</tr>
                        @php $catActive = $o->element_id; @endphp
                        @else
                        <tr>
                            <td class="align-middle">{{ $o->number.'. '.$o->objective->desc }}</td>
                        </tr>
                        @endif
						@endforeach
						@else
						<tr>
							<td class="text-center align-middle">&nbsp;</td>
							<td class="align-middle">&nbsp;</td>
							<td class="align-middle">&nbsp;</td>
						</tr>
						@endif
                    </table>
                </div>
            </div>
			@else
            <div id="lampiranIndikatorPengetahuan">
                <p class="komponen-rapor">I. INDIKATOR PEMBELAJARAN</p>
                <div class="m-t-8 m-b-16">
                    <table class="table-border page-break-auto">
						<tr style="background-color: #C6E0B4">
                            <th style="width: 3%">
                                No
                            </th>
                            <th style="width: 42%">
                                Mata Pelajaran
                            </th>
                            <th style="width: 55%">
                                Tujuan Pembelajaran
                            </th>
                        </tr>
                        @php
                        $active = null;
                        @endphp
                        @foreach($kelompok as $k)
                        @if($k->matapelajarans()->count())
                        @if($active != $k->id && $unit->name != 'SD')
                        <tr>
                            <td class="font-weight-bold" colspan="3">{{ $k->group_subject_name }} {{ $k->jurusan ? $k->jurusan->major_name : '' }}</td>
                        </tr>
                        @php $active = $k->id @endphp
                        @endif
                        @php
                        $i = 1;
						$matapelajarans = $k->matapelajarans()->whereNull('is_mulok')->whereHas('jadwalPelajaran',function($q)use($rapor,$semester){
						    $q->where([
						        'level_id' => $rapor->kelas->level_id,
						        'class_id' => $rapor->kelas->id,
						        'semester_id' => $semester->id,
						    ]);
						})->orderBy('subject_number');
						if($unit->name == 'SD'){
                            $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                                $q->where('level_id',$rapor->kelas->level_id);
                            });
                        }
						$mapelCount += $matapelajarans->count();
                        $matapelajarans = $matapelajarans->get();
                        @endphp
                        @foreach($matapelajarans as $m)
                        @php
                        $subject_active = null;
                        @endphp
                        @if(isset($tpDescs[$m->id]))
                        @foreach($tpDescs[$m->id] as $tpDesc)
                        <tr>
                            @if($subject_active != $m->id)
                            <td class="text-center" rowspan="{{ count($tpDescs[$m->id]) }}">{{ $i++ }}</td>
                            <td rowspan="{{ count($tpDescs[$m->id]) }}">{{ $m->subject_name }}</td>
                            @php
                            $subject_active = $m->id;
                            $j = 'a';
                            @endphp
                            @endif
                            <td>
                                {{ $j.'. '.$tpDesc->desc }}
                            </td>
                            @php
                            $j = chr(ord($j)+1);
                            @endphp
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td>{{ $m->subject_name }}</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endif
                        @endforeach
                        @php
                        $j = 'a';
                        $matapelajarans = $k->matapelajarans()->mulok()->whereHas('jadwalPelajaran',function($q)use($rapor,$semester){
						    $q->where([
						        'level_id' => $rapor->kelas->level_id,
						        'class_id' => $rapor->kelas->id,
						        'semester_id' => $semester->id,
						    ]);
						})->orderBy('subject_number');
						if($unit->name == 'SD'){
                            $matapelajarans = $matapelajarans->whereHas('mapelKelas',function($q)use($rapor){
                                $q->where('level_id',$rapor->kelas->level_id);
                            });
                        }
						$mapelCount += $matapelajarans->count();
                        $matapelajarans = $matapelajarans->get();
                        @endphp
                        @if(count($matapelajarans) > 0)
                        @foreach($matapelajarans as $m)
                        @if($j == 'a')
                        <tr>
                            <td colspan="3">Muatan Lokal</td>
                        </tr>
                        @endif
                        @php
                        $subject_active = null;
                        @endphp
                        @if(isset($tpDescs[$m->id]))
                        @foreach($tpDescs[$m->id] as $tpDesc)
                        <tr>
                            @if($subject_active != $m->id)
                            <td class="text-center" rowspan="{{ count($tpDescs[$m->id]) }}">{{ $i++ }}</td>
                            <td rowspan="{{ count($tpDescs[$m->id]) }}">{{ $m->subject_name }}</td>
                            @php
                            $subject_active = $m->id;
                            $k = 'a';
                            @endphp
                            @endif
                            <td>
                                {{ $k.'. '.$tpDesc->desc }}
                            </td>
                            @php
                            $k = chr(ord($k)+1);
                            @endphp
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td class="text-center">{{ $i++ }}</td>
                            <td>{{ $m->subject_name }}</td>
                            <td>&nbsp;</td>
                        </tr>
                        @endif
                        @php
                        $j = chr(ord($j)+1);
                        @endphp
                        @endforeach
                        @endif
                        @endif
                        @endforeach
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
    @if($unit->id != 1)
	<div class="page">
		<div class="subpage">
			<div id="lampiranKurikulumTilawah">
				<p class="komponen-rapor">II. INDIKATOR KOMPETENSI TILAWAH</p>
				<div class="m-t-8 m-b-16">
					<table class="table-border">
						<tr style="background-color: #C6E0B4">
							<th style="width: 3%">
								No
							</th>
							<th style="width: 37%">
								Capaian
							</th>
							<th style="width: 60%">
								Indikator
							</th>
						</tr>
						<tr>
							<td class="text-center" rowspan="4" style="vertical-align: middle">1</td>
							<td class="text-center" rowspan="4" style="vertical-align: middle">Nama Huruf dan Tanda Baca</td>
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
							<td class="text-center" rowspan="4" style="vertical-align: middle">2</td>
							<td class="text-center" rowspan="4" style="vertical-align: middle">Tajwid Dasar</td>
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
							<td class="text-center" rowspan="8" style="vertical-align: middle">3</td>
							<td class="text-center" rowspan="8" style="vertical-align: middle">Tajwid Lengkap</td>
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
	@endif
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.print.print_window')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection