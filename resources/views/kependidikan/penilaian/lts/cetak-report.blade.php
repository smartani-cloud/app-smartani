@extends('template.print.A4.master')

@section('title')
{{ $siswa->student_nis}} - {{ $siswa->identitas->student_name}} - LTS - {{ $semester->semester_id }}
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
			<p class="text-center text-uppercase fs-22 font-weight-bold">Laporan Tengah Semester</p>
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

	            </table>
	        </div>
			<div id="kurikulumMerdeka" class="m-t-22 m-b-16">
				<table class="table-border">
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
						<th rowspan="2" style="width: 3%">
							No
						</th>
						<th rowspan="2" style="width: 26%">
							Mata Pelajaran
						</th>
						<th colspan="{{ $maxCount }}" style="width: 26%">
							Penilaian Sumatif
						</th>
						<th rowspan="2" style="width: 5%">
							PTS
						</th>
						<th rowspan="2" style="width: 60%">
							Capaian Kompetensi
						</th>
					</tr>
					<tr style="background-color: #C6E0B4">
						@if($maxCount > 0)
						@for($ps = 1; $ps <= $maxCount; $ps++)
						<th>PS {{ $ps }}</th>
						@endfor
						@else
						<th>&nbsp;</th>
						@endif
					</tr>
					@php
					$active = null;
					$mapelCount = 0;
					@endphp
					@foreach($kelompok as $k)
					@if($k->matapelajarans()->count())
        			@if($active != $k->id && $unit->name != 'SD')
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
					@if($matapelajarans && count($matapelajarans) > 0)
					@foreach($matapelajarans as $m)
					<tr>
						<td class="text-center" rowspan="2" style="vertical-align: middle">{{ $i++ }}</td>
						<td rowspan="2" style="vertical-align: middle">{{ $m->subject_name }}</td>
						@php
						$count = 0;
						@endphp
						@if(isset($tpDescs[$m->id])  && count($tpDescs[$m->id]) > 0)
                        @foreach($tpDescs[$m->id]->take($maxCount) as $t)
                        <td class="text-center" rowspan="2" style="vertical-align: middle">{{ isset($nilai['mapel'][$m->id]['sumatif'][$t->id]) ? $nilai['mapel'][$m->id]['sumatif'][$t->id] : 0 }}</td>
                        @php
                        $count++;
                        @endphp
						@endforeach
						@endif
						@while($count < $maxCount)
						<td class="text-center" rowspan="2" style="vertical-align: middle">&nbsp;</td>
                        @php
                        $count++;
                        @endphp
						@endwhile
						<td class="text-center" rowspan="2" style="vertical-align: middle">{!! isset($nilai['mapel'][$m->id]['tengah']) ? $nilai['mapel'][$m->id]['tengah'] : '&nbsp;' !!}</td>
						<td class="text-center">{!! isset($nilai['mapel'][$m->id]['tinggi']) ? $nilai['mapel'][$m->id]['tinggi'] : '&nbsp;' !!}</td>
					</tr>
					<tr>
					    <td class="text-center">{!! isset($nilai['mapel'][$m->id]['rendah']) ? $nilai['mapel'][$m->id]['rendah'] : '&nbsp;' !!}</td>
					<tr>
					@endforeach
					@endif
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
					@if($matapelajarans && count($matapelajarans) > 0)
					@foreach($matapelajarans as $m)
					@if($j == 'a')
					<tr>
						<td colspan="{{ $maxCount > 0 ? 4+$maxCount : '4' }}">Muatan Lokal</td>
					</tr>
					@endif
					<tr>
						<td class="text-center" rowspan="2" style="vertical-align: middle">{{ $i++ }}</td>
						<td rowspan="2" style="vertical-align: middle">{{ $m->subject_name }}</td>
						@php
						$count = 0;
						@endphp
						@if(isset($tpDescs[$m->id])  && count($tpDescs[$m->id]) > 0)
                        @foreach($tpDescs[$m->id]->take($maxCount) as $t)
                        <td class="text-center" rowspan="2" style="vertical-align: middle">{{ isset($nilai['mapel'][$m->id]['sumatif'][$t->id]) ? $nilai['mapel'][$m->id]['sumatif'][$t->id] : 0 }}</td>
                        @php
                        $count++;
                        @endphp
						@endforeach
						@endif
						@while($count < $maxCount)
						<td class="text-center" rowspan="2" style="vertical-align: middle">&nbsp;</td>
                        @php
                        $count++;
                        @endphp
						@endwhile
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
			<div id="ketidakhadiran" class="m-t-22">
				<table class="table-border" style="width: 50%">
					<tr style="background-color: #C6E0B4">
						<th class="text-center" style="width: 8%">No</th>
						<th class="text-center" colspan="2">Kehadiran</th>
					</tr>
					<tr>
						@php
						$pts = $unit->id == 1 ? 'pts_tk' : 'pts';
						@endphp
						<td class="text-center">1</td>
						<td {!! $unit->id == 1 ? 'class="text-center"' : null !!}>Sakit</td>
						<td class="text-center">
							@if($rapor->report_status_pts_id == 1)
							{{ isset($rapor->{$pts}->sick) ? $rapor->{$pts}->sick ? $rapor->{$pts}->sick : '0' : '0' }} hari
							@else
							{{ isset($rapor->kehadiran->sick) ? $rapor->kehadiran->sick ? $rapor->kehadiran->sick : '0' : '0' }} hari
							@endif
	                    </td>
	                </tr>
	                <tr>
	                    <td class="text-center">2</td>
	                    <td {!! $unit->id == 1 ? 'class="text-center"' : null !!}>Izin</td>
	                    <td class="text-center">
							@if($rapor->report_status_pts_id == 1)
							{{ isset($rapor->{$pts}->leave) ? $rapor->{$pts}->leave ? $rapor->{$pts}->leave : '0' : '0' }} hari
							@else
							{{ isset($rapor->kehadiran->leave) ? $rapor->kehadiran->leave ? $rapor->kehadiran->leave : '0' : '0' }} hari
							@endif
	                    </td>
	                </tr>
	                <tr>
	                    <td class="text-center">3</td>
	                    <td {!! $unit->id == 1 ? 'class="text-center"' : null !!}>Alpa</td>
	                    <td class="text-center">
							@if($rapor->report_status_pts_id == 1)
							{{ isset($rapor->{$pts}->absent) ? $rapor->{$pts}->absent ? $rapor->{$pts}->absent : '0' : '0' }} hari
							@else
							{{ isset($rapor->kehadiran->absent) ? $rapor->kehadiran->absent ? $rapor->kehadiran->absent : '0' : '0' }} hari
							@endif
	                    </td>
	                </tr>
				</table>
			</div>
			@if($rapor->report_status_pts_id == 1)
			<div id="tandaTangan" class="m-t-33">
				<table class="tanda-tangan">
					<tr>
	                    <td>&nbsp;</td>
	                    <td>Tangerang Selatan, {{ $pts_date ? Date::parse($pts_date)->format('j F Y') : Date::now('Asia/Jakarta')->format('j F Y') }}</td>
	                </tr>
	                <tr>
	                    <td>Mengetahui,</td>
	                    <td>&nbsp;</td>
	                </tr>
	                <tr>
	                    <td>Kepala Sekolah,</td>
	                    <td>Wali Kelas,</td>
	                </tr>
	                <tr>
	                    <td class="ttd">
	                    	{!! $digital && $pts_date && $rapor->hm_name ? QrCode::size(84)->generate('Dokumen LTS Elektronik ini sah dan sudah divalidasi oleh Kepala '.$unit->desc.', '.$rapor->hm_name.' pada '.Date::parse($pts_date)->format('l, j F Y').' melalui SISTA Auliya') : '&nbsp;' !!}
	                    </td>
	                    <td class="ttd">
	                        {!! $digital && $pts_date && $rapor->hr_name ? QrCode::size(84)->generate('Dokumen LTS Elektronik ini sah dan sudah difinalisasi oleh Wali Kelas '.$unit->desc.', '.$rapor->hr_name.' pada '.Date::parse($pts_date)->format('l, j F Y').' melalui SISTA Auliya') : '&nbsp;' !!}
	                    </td>
	                </tr>
	                <tr>
	                    <td>{{ $rapor->hm_name }}</td>
	                    <td>{{ $rapor->hr_name }}</td>
	            </table>
	        </div>
	        @else
	        <div class="m-t-33">
	        	<table class="unvalidated">
	        		<tr>
	        			<td class="text-center text-uppercase font-weight-bold">Dokumen laporan tengah semester ini belum divalidasi</td>
	        		</tr>
	        	</table>
	        </div>
	        @endif
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