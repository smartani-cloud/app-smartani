<<<<<<< HEAD
@extends('template.print.A4.master')

@section('title')
{{ $siswa->student_nis}} - {{ $siswa->identitas->student_name}} - Rapor - Halaman Akhir
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/report.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/report.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="page">
	<div class="subpage">
		<p class="text-center text-uppercase fs-22 font-weight-bold">Keterangan Pindah Sekolah</p>
		<p class="text-center fs-14 font-weight-bold">Nama Peserta Didik: @for($i=0;$i<=45;$i++).@endfor</p>
		<div id="keluar" class="m-t-22">
			<div class="m-t-16 m-b-16">
				<table class="table-border">
					<tr>
						<th class="text-uppercase" colspan="4">
							Keluar
						</th>
					</tr>
					<tr>
						<td class="text-center" style="width: 15%">Tanggal</td>
						<td class="text-center" style="width: 15%">Kelas yang Ditinggalkan</td>
						<td class="text-center" style="width: 35%">Sebab-Sebab Keluar atau Atas Permintaan (Tertulis)</td>
						<td class="text-center" style="width: 35%">Tanda Tangan Kepala Sekolah, Stempel Sekolah, dan Tanda Tangan Orang Tua/Wali</td>
					</tr>
					@for($j=0;$j<3;$j++)
					<tr>
						<td class="text-center" rowspan="8">&nbsp;</td>
						<td class="text-center" rowspan="8">&nbsp;</td>
						<td rowspan="8">&nbsp;</td>
						<td class="no-border-y" style="padding-top: 15px">
							@for($i=0;$i<=25;$i++).@endfor, @for($i=0;$i<=35;$i++).@endfor	
						</td>
					</tr>
					<tr>
						<td class="no-border-y">Kepala Sekolah,</td>
					</tr>
					<tr>
						<td class="ttd no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="no-border-y">
							@for($i=0;$i<=65;$i++).@endfor
						</td>
					</tr>
					<tr>
						<td class="no-border-y">NIP </td>
					</tr>
					<tr>
						<td class="no-border-y">Orang Tua/Wali,</td>
					</tr>
					<tr>
						<td class="ttd no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="no-border-top">
							@for($i=0;$i<=65;$i++).@endfor
						</td>
					</tr>
					@endfor
				</table>
			</div>
		</div>
	</div>
</div>
<div class="page">
	<div class="subpage">
		<p class="text-center text-uppercase fs-22 font-weight-bold">Keterangan Pindah Sekolah</p>
		<p class="text-center fs-14 font-weight-bold">Nama Peserta Didik: @for($i=0;$i<=45;$i++).@endfor</p>
		<div id="keluar" class="m-t-22">
			<div class="m-t-16 m-b-16">
				<table class="table-border">
					<tr>
						<th style="width: 5%">No</th>
						<th class="text-uppercase" colspan="3" style="width: 95%">Masuk</th>
					</tr>
					@for($j=0;$j<3;$j++)
					<tr>
						<td class="text-center no-border-y" style="padding-top: 15px">1</td>
						<td class="no-border-y" style="padding-top: 15px;width: 25%">Nama Peserta Didik</td>
						<td class="text-center no-border-y"style="padding-top: 15px;width: 38%">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y" style="padding-top: 15px;width: 32%">
							@for($i=0;$i<=25;$i++).@endfor, @for($i=0;$i<=35;$i++).@endfor	
						</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">2</td>
						<td class="no-border-y">Nomor Induk</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">Kepala Sekolah,</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">3</td>
						<td class="no-border-y">Nama Sekolah</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">4</td>
						<td class="no-border-y">Masuk di Sekolah Ini:</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">&nbsp</td>
						<td class="no-border-y">a. Tanggal</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">&nbsp</td>
						<td class="no-border-y">b. Di Kelas</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">@for($i=0;$i<=65;$i++).@endfor</td>
					</tr>
					<tr>
						<td class="text-center no-border-top" style="padding-bottom: 15px">5</td>
						<td class="no-border-top" style="padding-bottom: 15px">Tahun Pelajaran</td>
						<td class="text-center no-border-top" style="padding-bottom: 15px">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-top" style="padding-bottom: 15px">NIP </td>
					</tr>
					@endfor
				</table>
			</div>
		</div>
	</div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
=======
@extends('template.print.A4.master')

@section('title')
{{ $siswa->student_nis}} - {{ $siswa->identitas->student_name}} - Rapor - Halaman Akhir
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/report.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/report.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="page">
	<div class="subpage">
		<p class="text-center text-uppercase fs-22 font-weight-bold">Keterangan Pindah Sekolah</p>
		<p class="text-center fs-14 font-weight-bold">Nama Peserta Didik: @for($i=0;$i<=45;$i++).@endfor</p>
		<div id="keluar" class="m-t-22">
			<div class="m-t-16 m-b-16">
				<table class="table-border">
					<tr>
						<th class="text-uppercase" colspan="4">
							Keluar
						</th>
					</tr>
					<tr>
						<td class="text-center" style="width: 15%">Tanggal</td>
						<td class="text-center" style="width: 15%">Kelas yang Ditinggalkan</td>
						<td class="text-center" style="width: 35%">Sebab-Sebab Keluar atau Atas Permintaan (Tertulis)</td>
						<td class="text-center" style="width: 35%">Tanda Tangan Kepala Sekolah, Stempel Sekolah, dan Tanda Tangan Orang Tua/Wali</td>
					</tr>
					@for($j=0;$j<3;$j++)
					<tr>
						<td class="text-center" rowspan="8">&nbsp;</td>
						<td class="text-center" rowspan="8">&nbsp;</td>
						<td rowspan="8">&nbsp;</td>
						<td class="no-border-y" style="padding-top: 15px">
							@for($i=0;$i<=25;$i++).@endfor, @for($i=0;$i<=35;$i++).@endfor	
						</td>
					</tr>
					<tr>
						<td class="no-border-y">Kepala Sekolah,</td>
					</tr>
					<tr>
						<td class="ttd no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="no-border-y">
							@for($i=0;$i<=65;$i++).@endfor
						</td>
					</tr>
					<tr>
						<td class="no-border-y">NIP </td>
					</tr>
					<tr>
						<td class="no-border-y">Orang Tua/Wali,</td>
					</tr>
					<tr>
						<td class="ttd no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="no-border-top">
							@for($i=0;$i<=65;$i++).@endfor
						</td>
					</tr>
					@endfor
				</table>
			</div>
		</div>
	</div>
</div>
<div class="page">
	<div class="subpage">
		<p class="text-center text-uppercase fs-22 font-weight-bold">Keterangan Pindah Sekolah</p>
		<p class="text-center fs-14 font-weight-bold">Nama Peserta Didik: @for($i=0;$i<=45;$i++).@endfor</p>
		<div id="keluar" class="m-t-22">
			<div class="m-t-16 m-b-16">
				<table class="table-border">
					<tr>
						<th style="width: 5%">No</th>
						<th class="text-uppercase" colspan="3" style="width: 95%">Masuk</th>
					</tr>
					@for($j=0;$j<3;$j++)
					<tr>
						<td class="text-center no-border-y" style="padding-top: 15px">1</td>
						<td class="no-border-y" style="padding-top: 15px;width: 25%">Nama Peserta Didik</td>
						<td class="text-center no-border-y"style="padding-top: 15px;width: 38%">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y" style="padding-top: 15px;width: 32%">
							@for($i=0;$i<=25;$i++).@endfor, @for($i=0;$i<=35;$i++).@endfor	
						</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">2</td>
						<td class="no-border-y">Nomor Induk</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">Kepala Sekolah,</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">3</td>
						<td class="no-border-y">Nama Sekolah</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">4</td>
						<td class="no-border-y">Masuk di Sekolah Ini:</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">&nbsp</td>
						<td class="no-border-y">a. Tanggal</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">&nbsp;</td>
					</tr>
					<tr>
						<td class="text-center no-border-y">&nbsp</td>
						<td class="no-border-y">b. Di Kelas</td>
						<td class="text-center no-border-y">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-y">@for($i=0;$i<=65;$i++).@endfor</td>
					</tr>
					<tr>
						<td class="text-center no-border-top" style="padding-bottom: 15px">5</td>
						<td class="no-border-top" style="padding-bottom: 15px">Tahun Pelajaran</td>
						<td class="text-center no-border-top" style="padding-bottom: 15px">@for($i=0;$i<=70;$i++).@endfor</td>
						<td class="no-border-top" style="padding-bottom: 15px">NIP </td>
					</tr>
					@endfor
				</table>
			</div>
		</div>
	</div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection