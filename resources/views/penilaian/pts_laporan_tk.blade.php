<<<<<<< HEAD
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
<div class="page">
	<div class="subpage">
		<p class="text-center text-uppercase fs-22 font-weight-bold">Laporan Tengah Semester</p>
		<p class="text-center text-uppercase fs-18 font-weight-bold">Taman Kanak-Kanak Islam Terpadu Auliya</p>
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
		<div id="perkembangan" class="m-t-22 m-b-16">
			<table class="table-border">
				<tr>
					<th style="width: 3%">
						No
					</th>
					<th style="width: 27%">
						Aspek Perkembangan
					</th>
					<th style="width: 15%">
						Perkembangan
					</th>
					<th style="width: 50%">
						Deskripsi
					</th>
				</tr>
				@php $i = 1; @endphp
				@foreach($aspek as $a)
				<tr>
					<td class="text-center align-top">{{ $i++ }}</td>
					<td class="align-top">{{ $a->dev_aspect }}</td>
					@php
					$nilai = $rapor->pts_tk->nilai()->where('development_aspect_id',$a->id)->first();
					@endphp
					@if($nilai)
					<td class="text-center align-top">
						{{ $nilai->predicate }}
					</td>
					<td class="align-top">
						{!! str_replace("@nama",$siswa->identitas->student_nickname ? $siswa->identitas->student_nickname : $siswa->identitas->student_name,$nilai->description) !!}
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
<div class="page">
    <div class="subpage">
		<div id="ketidakhadiran" class="m-t-22">
			<table class="table-border" style="width: 50%">
				<tr>
					<th class="text-center" style="width: 8%">No</th>
					<th class="text-center" colspan="2">
						Kehadiran
					</th>
				</tr>
				<tr>
					<td class="text-center">1</td>
					<td>
						Sakit
					</td>
					<td class="text-center">
						@if($rapor->report_status_pts_id == 0)
						{{ $rapor->pts_tk->sick ? $rapor->pts_tk->sick : '0' }} hari
						@else
						{{ $rapor->kehadiran->sick ? $rapor->kehadiran->sick : '0' }} hari
						@endif
					</td>
				</tr>
				<tr>
					<td class="text-center">2</td>
					<td>Izin</td>
					<td class="text-center">
						@if($rapor->report_status_pts_id == 0)
						{{ $rapor->pts_tk->leave ? $rapor->pts_tk->leave : '0' }} hari
						@else
						{{ $rapor->kehadiran->leave ? $rapor->kehadiran->leave : '0' }} hari
						@endif
					</td>
				</tr>
				<tr>
					<td class="text-center">3</td>
					<td>Alpa</td>
					<td class="text-center">
						@if($rapor->report_status_pts_id == 0)
						{{ $rapor->pts_tk->absent ? $rapor->pts_tk->absent : '0' }} hari
						@else
						{{ $rapor->kehadiran->absent ? $rapor->kehadiran->absent : '0' }} hari
						@endif
					</td>
				</tr>
			</table>
		</div>
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
				</tr>
			</table>
		</div>
	</div>
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
{{ $siswa->student_nis}} - {{ $siswa->identitas->student_name}} - LTS - {{ $semester->semester_id }}
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/report.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/report.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="page">
	<div class="subpage">
		<p class="text-center text-uppercase fs-22 font-weight-bold">Laporan Tengah Semester</p>
		<p class="text-center text-uppercase fs-18 font-weight-bold">Taman Kanak-Kanak Islam Terpadu Auliya</p>
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
		<div id="perkembangan" class="m-t-22 m-b-16">
			<table class="table-border">
				<tr>
					<th style="width: 3%">
						No
					</th>
					<th style="width: 27%">
						Aspek Perkembangan
					</th>
					<th style="width: 15%">
						Perkembangan
					</th>
					<th style="width: 50%">
						Deskripsi
					</th>
				</tr>
				@php $i = 1; @endphp
				@foreach($aspek as $a)
				<tr>
					<td class="text-center align-top">{{ $i++ }}</td>
					<td class="align-top">{{ $a->dev_aspect }}</td>
					@php
					$nilai = $rapor->pts_tk->nilai()->where('development_aspect_id',$a->id)->first();
					@endphp
					@if($nilai)
					<td class="text-center align-top">
						{{ $nilai->predicate }}
					</td>
					<td class="align-top">
						{!! str_replace("@nama",$siswa->identitas->student_nickname ? $siswa->identitas->student_nickname : $siswa->identitas->student_name,$nilai->description) !!}
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
<div class="page">
    <div class="subpage">
		<div id="ketidakhadiran" class="m-t-22">
			<table class="table-border" style="width: 50%">
				<tr>
					<th class="text-center" style="width: 8%">No</th>
					<th class="text-center" colspan="2">
						Kehadiran
					</th>
				</tr>
				<tr>
					<td class="text-center">1</td>
					<td>
						Sakit
					</td>
					<td class="text-center">
						@if($rapor->report_status_pts_id == 0)
						{{ $rapor->pts_tk->sick ? $rapor->pts_tk->sick : '0' }} hari
						@else
						{{ $rapor->kehadiran->sick ? $rapor->kehadiran->sick : '0' }} hari
						@endif
					</td>
				</tr>
				<tr>
					<td class="text-center">2</td>
					<td>Izin</td>
					<td class="text-center">
						@if($rapor->report_status_pts_id == 0)
						{{ $rapor->pts_tk->leave ? $rapor->pts_tk->leave : '0' }} hari
						@else
						{{ $rapor->kehadiran->leave ? $rapor->kehadiran->leave : '0' }} hari
						@endif
					</td>
				</tr>
				<tr>
					<td class="text-center">3</td>
					<td>Alpa</td>
					<td class="text-center">
						@if($rapor->report_status_pts_id == 0)
						{{ $rapor->pts_tk->absent ? $rapor->pts_tk->absent : '0' }} hari
						@else
						{{ $rapor->kehadiran->absent ? $rapor->kehadiran->absent : '0' }} hari
						@endif
					</td>
				</tr>
			</table>
		</div>
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
				</tr>
			</table>
		</div>
	</div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.print.print_window')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection