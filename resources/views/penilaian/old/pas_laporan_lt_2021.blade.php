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
							$totalPengetahuan = $totalKeterampilan = $mapelCount = 0;
							@endphp
							@foreach($kelompok as $k)
							@if($k->matapelajarans()->count())
                			@if($active != $k->id && $unit->name != 'SD')
							<tr>
								<td class="font-weight-bold" colspan="8">{{ $k->group_subject_name }} {{ $k->jurusan ? $k->jurusan->major_name : '' }}</td>
							</tr>
							@php $active = $k->id @endphp
							@endif
							@php
							$i = 1;
							$matapelajarans = $k->matapelajarans()->whereNull('is_mulok')->orderBy('subject_number');
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
								<td class="text-center">{{ $pengetahuan ? ($pengetahuan->deskripsi && $pengetahuan->deskripsi->description ? str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$pengetahuan->deskripsi->description) : '') : '' }}</td>
							</tr>
							@endforeach
							@php
							$j = 'a';
							$matapelajarans = $k->matapelajarans()->mulok()->orderBy('subject_number');
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
								<td class="text-center">{{ $pengetahuan ? ($pengetahuan->deskripsi && $pengetahuan->deskripsi->description ? str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '', $pengetahuan->deskripsi->description) : '') : '' }}</td>
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
			<div id="kurikulumIklas" class="m-t-22">
				<p class="komponen-rapor">II. KURIKULUM IKLaS</p>
				<div class="m-l-18">
					<p class="fs-14 font-weight-bold">(Islami, Karakter Sukses, Literasi Era 4.0, Skill Abad 21)</p>
					<div class="m-t-16 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 47%">
									Kompetensi
								</th>
								<th style="width: 50%">
									Predikat
								</th>
							</tr>
							@php
							$category_active = null;
							$nilai = $rapor->iklas;
							@endphp

							@foreach($iklas as $i)
							@if($category_active != $i->iklas_cat)
							@php
							$category_active = $i->iklas_cat;
							$rowspan = $iklas->where('iklas_cat',$i->iklas_cat)->count()+1;
							@endphp
							<tr>
								<td class="text-center" rowspan="{{ $rowspan }}" style="vertical-align: top">{{ $i->iklas_cat }}</td>
								<td class="font-weight-bold" colspan="3">{{ $i->competence }}</td>
							</tr>
							@endif
							<tr>
								<td>{{ $i->categoryNumber.' '.$i->category }}</td>
								<td class="text-center">
									@php
									$scoreIklas = null;
									@endphp
                                    @if($nilai && $nilai->detail()->where('iklas_ref_id',$i->id)->count() > 0)
                                    @php
                                    $scoreIklas = $nilai->detail()->where('iklas_ref_id',$i->id)->first();
                                    $stars = $scoreIklas->predicate;
                                    @endphp
                                    @for($i=0;$i<$stars;$i++) <i class="fas fa-star"></i>
                                        @endfor
                                    @endif
								</td>
							</tr>
							@endforeach
						</table>
					</div>
					<div class="m-t-16 m-b-16">
						<table class="fs-11">
							<tr>
								<td colspan="3">
									Catatan kriteria:
								</td>
							</tr>
							<tr>
								<td>
									<i class="fas fa-star m-r-5"></i>Belum Terlihat
								</td>
								<td>
									<i class="fas fa-star"></i>
									<i class="fas fa-star m-r-5"></i>Terlihat
								</td>
								<td>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star m-r-5"></i>Berkembang
								</td>
							</tr>
							<tr>
								<td>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star m-r-5"></i>Mulai Konsisten
								</td>
								<td>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star"></i>
									<i class="fas fa-star m-r-5"></i>Konsisten
								</td>
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
				<p class="komponen-rapor">III. AL QURAN</p>
				<div class="m-l-18">
					<p class="text-uppercase fs-14 font-weight-bold">A. TILAWAH</p>
					<div class="m-t-16 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 82%">
									Kompetensi
								</th>
								<th style="width: 15%">
									Predikat
								</th>
							</tr>
							@php $i = 1 @endphp
							@foreach($tilawah as $t)
							<tr>
								<td class="text-center">
									{{ $i++ }}
								</td>
								<td class="text-center">
									{{ $t->tilawah_type }}
								</td>
								<td class="text-center">
									@php $predicate = $rapor->tilawah ? $rapor->tilawah->detail()->where('tilawah_type_id',$t->id)->whereNotNull('predicate')->first()->predicate : null; @endphp
									{{ $predicate ? $predicate : '' }}
								</td>
							</tr>
							@endforeach
						</table>
					</div>
					<div class="m-t-16 m-b-16">
						<div class="fs-11">
							<p class="d-inline m-r-3">
								Keterangan:
							</p>
							<p class="d-inline m-r-15">
								<span class="font-weight-bold m-r-5">A</span> Sangat Baik
							</p>
							<p class="d-inline m-r-15">
								<span class="font-weight-bold m-r-5">B</span>Baik
							</p>
							<p class="d-inline m-r-15">
								<span class="font-weight-bold m-r-5">C</span>Cukup
							</p>
						</div>
					</div>
				</div>
				<div class="m-l-18">
					<p class="text-uppercase fs-14 font-weight-bold">B. HAFALAN</p>
					<div class="m-t-16 m-b-16">
						<table class="table-border table-target">
							<tr>
								<td class="font-weight-bold text-center text-uppercase" style="width: 40%">Target Tahfidz</td>
								<td class="text-center text-uppercase" style="width: 60%">{{ $targetTahfidz && count($targetTahfidz) > 0 ? $targetTahfidz[0] : '' }}</td>
							</tr>
						</table>
					</div>
					<div class="m-t-16 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 42%">
									Nama Surat
								</th>
								<th style="width: 10%">
									Predikat
								</th>
								<th style="width: 45%">
									Deskripsi
								</th>
							</tr>
							@php $i = 1 @endphp
							@if($rapor->tahfidz)
							@foreach($rapor->tahfidz->detail as $t)
							<tr>
								<td class="text-center">
									{{ $i }}
								</td>
                                @php
                                $status = $t->status ? $t->status->status : null;
                                $surat = $t->juz ? $t->juz->juz : ($t->surat ? $t->surat->surah : '-');
                                if($status && $status != 'Penuh'){
                                    $surat = $surat.' ('.$status.')';
                                }
                                @endphp
                                <td class="text-center">
                                    {{ $surat }}
                                </td>
								<td class="text-center">
									{{ $t->predicate }}
								</td>
								@if($i == 1)
								<td class="text-center" rowspan="{{ $rapor->tahfidz->detail()->count() }}">
									{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->tahfidz->deskripsi->description) }}
								</td>
								@endif
								@php $i++ @endphp
							</tr>
							@endforeach
							@endif
							@if($i == 1)
							<tr>
								<td class="text-center">&nbsp;</td>
								<td class="text-center">&nbsp;</td>
								<td class="text-center">&nbsp;</td>
								<td class="text-center">&nbsp;</td>
							</tr>
							@endif
						</table>
					</div>
				</div>
				<div class="m-l-18">
					<p class="text-uppercase fs-14 font-weight-bold">C. HAFALAN HADITS DAN DOA</p>
					<div class="m-t-16 m-b-16">
						<table class="table-border">
							<tr>
								<th style="width: 3%">
									No
								</th>
								<th style="width: 82%">
									Hafalan
								</th>
								<th style="width: 15%">
									Predikat
								</th>
							</tr>
							@php
                            $jenis = null;
                            $i = 1;
                            $j = null;
                            @endphp
                            @foreach($hafalan as $h)
                            @php
                            $nilaiHafalan = $rapor->hafalan && $rapor->hafalan->nilai ? $rapor->hafalan->nilai()->where('mem_type_id',$h->id) : null;
                            @endphp
                            @if($jenis != $h->id)
                            <tr>
                                @if($nilaiHafalan && $nilaiHafalan->count() > 0)
                                <td class="text-center" rowspan="{{ $nilaiHafalan->count()+1 }}" style="vertical-align: top">{{ $i++ }}</td>
                                @else
                                <td class="text-center" rowspan="2" style="vertical-align: top">{{ $i++ }}</td>
                                @endif
                                <td class="font-weight-bold" colspan="2">{{ $h->mem_type }}</td>
                            </tr>
                            @php
                            $jenis = $h->id;
                            $j = 'a';
                            @endphp
                            @endif
                            @php
                            $nilaihafalandetail = $nilaiHafalan && $nilaiHafalan->count() > 0 ? $nilaiHafalan->get() : null;
                            @endphp
                            @if($nilaihafalandetail && count($nilaihafalandetail) > 0)
                            @foreach($nilaihafalandetail as $n)
                            <tr>
                                <td>{{ $j }}. {{ $n->hadits_doa }}</td>
                                <td class="text-center">
                                    {{ $n->predicate }}
                                </td>
                            </tr>
                            @php
                            $j = chr(ord($j)+1);
                            @endphp
                            @endforeach
                            @else
                            <tr>
                                <td>&nbsp;</td>
                                <td class="text-center">&nbsp;</td>
                            </tr>
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
			<div id="ekstrakurikuler" class="m-t-22">
				<p class="komponen-rapor">IV. EKSTRAKURIKULER</p>
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
				<p class="komponen-rapor">V. PRESTASI</p>
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
				<p class="komponen-rapor">VI. KEHADIRAN</p>
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
				<p class="komponen-rapor">VII. CATATAN WALI KELAS</p>
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
			<p class="komponen-rapor">LAMPIRAN</p>
			<div id="lampiranKurikulumIklas">
				<p class="komponen-rapor">I. TABEL INDIKATOR KOMPETENSI IKLaS</p>
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
							@php
							$category_active = null;
							$indikators = $rapor->kelas->level->indikatorIklas;
							@endphp
                            @if($indikators)
							@foreach($iklas as $i)
							@if($category_active != $i->iklas_cat)
							@php
							$category_active = $i->iklas_cat;
							$categories = $iklas->where('iklas_cat',$i->iklas_cat)->pluck('id');
							$total_indicators = $rapor->kelas->level->indikatorIklas->detail()->whereIn('iklas_ref_id',$categories)->count();
							$have_indicators = $rapor->kelas->level->indikatorIklas->detail()->whereIn('iklas_ref_id',$categories)->pluck('iklas_ref_id')->unique()->all();
							$have_no_indicators = $categories->diff($have_indicators)->count();
							$rowspan = $total_indicators + $have_no_indicators + 1;
							@endphp
							<tr>
								<td class="text-center" rowspan="{{ $rowspan }}" style="vertical-align: top">{{ $i->iklas_cat }}</td>
								<td class="font-weight-bold" colspan="2">{{ $i->competence }}</td>
							</tr>
							@endif
							@php
							$competency_active = null;
							$indikators = $rapor->kelas->level->indikatorIklas->detail()->where('iklas_ref_id',$i->id);
							$indikators_count = $indikators->count();
							@endphp
							@if($indikators_count > 0)
							@foreach($indikators->get() as $indikator)
							<tr>
								@if($competency_active != $i->categoryNumber)
								<td rowspan="{{ $indikators_count }}">{{ $i->categoryNumber.' '.$i->category }}</td>
								@php
								$competency_active = $i->categoryNumber;
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
								<td>{{ $i->categoryNumber.' '.$i->category }}</td>
								<td>&nbsp;</td>
							</tr>
							@endif
							@endforeach
							@endif
						</table>
					</div>
				</div>
			</div>
			<div id="lampiranKurikulumIklas">
				<p class="komponen-rapor">II. TABEL INDIKATOR KOMPETENSI TILAWAH</p>
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