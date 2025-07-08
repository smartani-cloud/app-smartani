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
							NISN
						</td>
						<td>
							:
						</td>
						<td>
							{{ $siswa->student_nisn }}
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
			<div id="kurikulumNasional" class="m-t-22">
				<p class="komponen-rapor">I. KURIKULUM NASIONAL</p>
				<div class="m-l-18">
					<p class="text-uppercase fs-14 font-weight-bold">A. SIKAP SPIRITUAL DAN SOSIAL</p>
					<div class="m-t-16 m-b-16">
						<table class="table-border">
							<tr>
								<th colspan="2" style="width: 50%">
									Sikap Spiritual
								</th>
								<th colspan="2" style="width: 50%">
									Sikap Sosial
								</th>
							</tr>
							<tr>
								<td class="text-center">Predikat</td>
								<td class="text-center">Deskripsi</td>
								<td class="text-center">Predikat</td>
								<td class="text-center">Deskripsi</td>
							</tr>
                            <tr>
                                @if($rapor->sikap && $rapor->sikap()->spiritual()->count() > 0)
                                <td class="text-center">{{ $rapor->sikap()->spiritual()->first()->predicate }}</td>
                                <td class="text-center">{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->sikap()->spiritual()->first()->description)  }}</td>
                                @else
                                <td class="text-center">&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                @endif
                                @if($rapor->sikap && $rapor->sikap()->sosial()->count() > 0)
                                <td class="text-center">{{ $rapor->sikap()->sosial()->first()->predicate }}</td>
                                <td class="text-center">{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->sikap()->sosial()->first()->description) }}</td>
                                @else
                                <td class="text-center">&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                                @endif
                            </tr>
						</table>
					</div>
					<p class="text-uppercase fs-14 font-weight-bold">B. PENGETAHUAN DAN KETERAMPILAN</p>
					<div class="m-t-16 m-b-16">
						<table class="table-border page-break-auto">
							<tr>
								<th style="width: 3%" rowspan="2">
									No
								</th>
								<th style="width: 26%" rowspan="2">
									Mata Pelajaran
								</th>
								<th style="width: 10%" rowspan="2">
									KKM
								</th>
								<th style="width: 20%" colspan="2">
									Pengetahuan
								</th>
								<th style="width: 20%" colspan="2">
									Keterampilan
								</th>
								<th style="width: 21%"  rowspan="2">
									Deskripsi Pengetahuan
								</th>
							</tr>
							<tr>
								<th style="width: 10%">
									Nilai
								</th>
								<th style="width: 10%">
									Predikat
								</th>
								<th style="width: 10%">
									Nilai
								</th>
								<th style="width: 10%">
									Predikat
								</th>
							</tr>
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
	</div>
	<div class="page">
		<div class="subpage">
			<div class="m-t-22">
				<div class="m-l-18">
					<div class="m-t-16 m-b-16">
						<table class="table-border page-break-auto">
							<tr>
								<th style="width: 3%" rowspan="2">
									No
								</th>
								<th style="width: 26%" rowspan="2">
									Mata Pelajaran
								</th>
								<th style="width: 10%" rowspan="2">
									KKM
								</th>
								<th style="width: 20%" colspan="2">
									Pengetahuan
								</th>
								<th style="width: 20%" colspan="2">
									Keterampilan
								</th>
								<th style="width: 21%"  rowspan="2">
									Deskripsi Pengetahuan
								</th>
							</tr>
							<tr>
								<th style="width: 10%">
									Nilai
								</th>
								<th style="width: 10%">
									Predikat
								</th>
								<th style="width: 10%">
									Nilai
								</th>
								<th style="width: 10%">
									Predikat
								</th>
							</tr>
                			@endif
							<tr>
								<td class="font-weight-bold" colspan="8">{{ $k->group_subject_name }} {{ $k->jurusan ? $k->jurusan->major_name : '' }}</td>
							</tr>
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
								<td class="text-center">{{ $i++ }}</td>
								<td>{{ $m->subject_name }}</td>
                                @php
                                $kkm = $m->kkm()->where('semester_id',$semester->id)->count() > 0  ? $m->kkm()->where('semester_id',$semester->id)->first()->kkm : null;
                                @endphp
                                <td class="text-center">{{ $kkm ? $kkm : '' }}</td>
								@php
								$pengetahuan = $rapor->pengetahuan()->where('subject_id',$m->id)->first();
								$score_knowledge = $pengetahuan ? ($pengetahuan->score_knowledge ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '') : '';
								@endphp
								<td class="text-center {{ $pengetahuan && ($score_knowledge < $kkm) ? 'text-danger' : '' }}">{{ $score_knowledge }}</td>
								@php
                                if(is_numeric($score_knowledge)){
                                	$totalPengetahuan += $score_knowledge;
                                }
								@endphp
								<td class="text-center">{{ $pengetahuan ? ($pengetahuan->deskripsi && $pengetahuan->deskripsi->predicate ? $pengetahuan->deskripsi->predicate : '') : '' }}</td>
								@php
								$keterampilan = $rapor->keterampilan()->where('subject_id',$m->id)->first();
								$score_skill = $keterampilan ? ($keterampilan->mean ? number_format((float)$keterampilan->mean, 0, ',', '') : '') : '';
								@endphp
								<td class="text-center">{{ $score_skill }}</td>
								@php
                                if(is_numeric($score_skill)){
                                	$totalKeterampilan += $score_skill;
                                }
								@endphp
								<td class="text-center">{{ $keterampilan ? ($keterampilan->deskripsi && $keterampilan->deskripsi->predicate ? $keterampilan->deskripsi->predicate : '') : '' }}</td>
								<td class="text-center">{{ $pengetahuan ? ($pengetahuan->deskripsi && $pengetahuan->deskripsi->description ? (strlen($pengetahuan->deskripsi->description) > 80 ? substr($pengetahuan->deskripsi->description, 0, strpos($pengetahuan->deskripsi->description, ' ', 80)).' ...' : $pengetahuan->deskripsi->description) : '') : '' }}</td>
							</tr>
							@if($unit->name == 'SD' && (($rapor->kelas->level->level < 4 && $i == 7) || ($rapor->kelas->level->level >= 4 && $i == 8)))
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="page">
		<div class="subpage">
			<div class="m-t-22">
				<div class="m-l-18">
					<div class="m-t-16 m-b-16">
						<table class="table-border page-break-auto">
							<tr>
								<th style="width: 3%" rowspan="2">
									No
								</th>
								<th style="width: 26%" rowspan="2">
									Mata Pelajaran
								</th>
								<th style="width: 10%" rowspan="2">
									KKM
								</th>
								<th style="width: 20%" colspan="2">
									Pengetahuan
								</th>
								<th style="width: 20%" colspan="2">
									Keterampilan
								</th>
								<th style="width: 21%"  rowspan="2">
									Deskripsi Pengetahuan
								</th>
							</tr>
							<tr>
								<th style="width: 10%">
									Nilai
								</th>
								<th style="width: 10%">
									Predikat
								</th>
								<th style="width: 10%">
									Nilai
								</th>
								<th style="width: 10%">
									Predikat
								</th>
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
								<td class="text-center" rowspan="{{ count($matapelajarans)+1 }}" style="vertical-align: top">{{ $i }}</td>
								<td colspan="7">Muatan Lokal</td>
							</tr>
							@endif
							<tr>
								<td>{{ $j }}. {{ $m->subject_name }}</td>
                                @php
                                $kkm = $m->kkm()->where('semester_id',$semester->id)->count() > 0  ? $m->kkm()->where('semester_id',$semester->id)->first()->kkm : null;
                                @endphp
                                <td class="text-center">{{ $kkm ? $kkm : '' }}</td>
								@php
								$pengetahuan = $rapor->pengetahuan()->where('subject_id',$m->id)->first();
								$score_knowledge = $pengetahuan ? ($pengetahuan->score_knowledge ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '') : '';
								@endphp
								<td class="text-center {{ $pengetahuan && ($score_knowledge < $kkm) ? 'text-danger' : '' }}">{{ $score_knowledge }}</td>
								@php
                                if(is_numeric($score_knowledge)){
                                	$totalPengetahuan += $score_knowledge;
                                }
								@endphp
								<td class="text-center">{{ $pengetahuan ? ($pengetahuan->deskripsi && $pengetahuan->deskripsi->predicate ? $pengetahuan->deskripsi->predicate : '') : '' }}</td>
								@php
								$keterampilan = $rapor->keterampilan()->where('subject_id',$m->id)->first();
								$score_skill = $keterampilan ? ($keterampilan->mean ? number_format((float)$keterampilan->mean, 0, ',', '') : '') : '';
								@endphp
								<td class="text-center">{{ $score_skill }}</td>
								@php
                                if(is_numeric($score_skill)){
                                	$totalKeterampilan += $score_skill;
                                }
								@endphp
								<td class="text-center">{{ $keterampilan ? ($keterampilan->deskripsi && $keterampilan->deskripsi->predicate ? $keterampilan->deskripsi->predicate : '') : '' }}</td>
								<td class="text-center">{{ $pengetahuan ? ($pengetahuan->deskripsi && $pengetahuan->deskripsi->description ? (strlen($pengetahuan->deskripsi->description) > 80 ? substr($pengetahuan->deskripsi->description, 0, strpos($pengetahuan->deskripsi->description, ' ', 80)).' ...' : $pengetahuan->deskripsi->description) : '') : '' }}</td>
							</tr>
							@php
							$j = chr(ord($j)+1);
							@endphp
							@endforeach
							@endif
							@endif
							@endforeach
							<tr>
								<td class="text-center" colspan="3">Jumlah Nilai</td>
								<td class="text-center" colspan="2">{{ $totalPengetahuan }}</td>
								<td class="text-center" colspan="2">{{ $totalKeterampilan }}</td>
								<td rowspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td class="text-center" colspan="3">Rata-rata</td>
								<td class="text-center" colspan="2">{{ $mapelCount ? number_format((float)($totalPengetahuan/$mapelCount), 0, ',', '') : '0' }}</td>
								<td class="text-center" colspan="2">{{ $mapelCount ? number_format((float)($totalKeterampilan/$mapelCount), 0, ',', '') : '0' }}</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
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
			<div id="prestasi" class="m-t-22">
				<p class="komponen-rapor">IV. PRESTASI</p>
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 42%">
									Jenis Prestasi
								</th>
								<th style="width: 55%">
									Keterangan
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
			</div>
			<div id="ketidakhadiran" class="m-t-22">
				<p class="komponen-rapor">V. KEHADIRAN</p>
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border" style="width: 50%">
							<tr>
								<td class="text-center" style="width: 8%">1</td>
								<td style="width: 42%">
									Jumlah Hari Efektif
								</td>
								<td class="text-center" style="width: 50%">
									{{ $rapor->kehadiran ? $rapor->kehadiran->effective_day : '0' }} hari
								</td>
							</tr>
							<tr>
								<td class="text-center">2</td>
								<td>
									Sakit
								</td>
								<td class="text-center">
									{{ $rapor->kehadiran ? $rapor->kehadiran->sick : '0' }} hari
								</td>
							</tr>
							<tr>
								<td class="text-center">3</td>
								<td>Izin</td>
								<td class="text-center">
									{{ $rapor->kehadiran ? $rapor->kehadiran->leave : '0' }} hari
								</td>
							</tr>
							<tr>
								<td class="text-center">4</td>
								<td>Alpa</td>
								<td class="text-center">
									{{ $rapor->kehadiran ? $rapor->kehadiran->absent : '0' }} hari
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div id="catatanWaliKelas" class="m-t-22">
				<p class="komponen-rapor">VI. CATATAN WALI KELAS</p>
				<div class="m-l-18">
					<div class="m-t-8 m-b-16">
						<table class="table-border table-catatan">
							<tr>
							    @if($rapor->pas && $rapor->pas->notes)
								<td>{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->pas->notes) }}</td>
								@else
								<td>&nbsp;</td>
								@endif
							</tr>
						</table>
					</div>
				</div>
			</div>
			@if(!in_array($rapor->kelas->level->level,array('6','9','12')) && $semester->semester == 'Genap' && ($rapor->pas && $rapor->pas->conclusion))
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
            <div id="lampiranIndikatorPengetahuan">
                <p class="komponen-rapor">I. KOMPETENSI DASAR PENGETAHUAN</p>
                <div class="m-l-18">
                    <div class="m-t-8 m-b-16">
                        <table class="table-border page-break-auto">
                            <tr>
                                <th style="width: 3%">
                                    No
                                </th>
                                <th style="width: 20%">
                                    Mata Pelajaran
                                </th>
                                <th style="width: 27%">
                                    Kompetensi Dasar
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
                            $matapelajarans = $matapelajarans->get();
                            @endphp
                            @foreach($matapelajarans as $m)
                            @php
                            $subject_active = null;
                            $indikatorPengetahuan = $rapor->kelas->level->indikatorPengetahuan()->where([
                                'semester_id' => $semester->id,
                                'subject_id' => $m->id
                            ])->has('detail')->first();
                            $indikators_count = $indikatorPengetahuan ? $indikatorPengetahuan->detail()->count() : 0;
                            @endphp
                            @if($indikatorPengetahuan)
                            @foreach($indikatorPengetahuan->detail as $indikator)
                            <tr>
                                @if($subject_active != $m->id)
                                <td class="text-center" rowspan="{{ $indikators_count }}">{{ $i++ }}</td>
                                <td rowspan="{{ $indikators_count }}">{{ $m->subject_name }}</td>
                                @php
                                $subject_active = $m->id;
                                $j = 'a';
                                @endphp
                                @endif
                                <td>
                                    {{ $j.'. '.$indikator->indicator }}
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
                            $matapelajarans = $matapelajarans->get();
                            @endphp
                            @if(count($matapelajarans) > 0)
                            @foreach($matapelajarans as $m)
                            @php
                            $indikatorPengetahuans = $rapor->kelas->level->indikatorPengetahuan()->where('semester_id', $semester->id)->whereIn('subject_id',$matapelajarans->pluck('id'))->has('detail')->get();
                            $total_indicators = 0;
                            foreach($indikatorPengetahuans as $ip){
                                $total_indicators += $ip ? $ip->detail()->count() : 0;
                            }
                            $have_no_indicators = (count($matapelajarans))-(count($indikatorPengetahuans));
                            $rowspan = $total_indicators + $have_no_indicators + 1;
                            @endphp
                            @if($j == 'a')
                            <tr>
                                <td class="text-center" rowspan="{{ $rowspan }}" style="vertical-align: top">{{ $i }}</td>
                                <td colspan="2">Muatan Lokal</td>
                            </tr>
                            @endif
                            @php
                            $subject_active = null;
                            $indikatorPengetahuan = $rapor->kelas->level->indikatorPengetahuan()->where([
                                'semester_id' => $semester->id,
                                'subject_id' => $m->id
                            ])->has('detail')->first();
                            $indikators_count = $indikatorPengetahuan ? $indikatorPengetahuan->detail()->count() : 0;
                            @endphp
                            @if($indikatorPengetahuan)
                            @foreach($indikatorPengetahuan->detail as $indikator)
                            <tr>
                                @if($subject_active != $m->id)
                                <td rowspan="{{ $indikators_count }}">{{ $j }}. {{ $m->subject_name }}</td>
                                @php
                                $subject_active = $m->id;
                                $k = 'a';
                                @endphp
                                @endif
                                <td>
                                    {{ $k.'. '.$indikator->indicator }}
                                </td>
                                @php
                                $k = chr(ord($k)+1);
                                @endphp
                            </tr>
                            @endforeach
                            @else
                            <tr>
                                <td>{{ $j }}. {{ $m->subject_name }}</td>
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
            </div>
        </div>
    </div>
	<div class="page">
		<div class="subpage">
			<div id="lampiranKurikulumTilawah">
				<p class="komponen-rapor">II. INDIKATOR KOMPETENSI TILAWAH</p>
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