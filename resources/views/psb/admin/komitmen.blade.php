@extends('template.print.A4.master')

@section('title')
{{ $calon->reg_number.' - ' }}{{ $calon->student_name }} - Surat Komitmen Keuangan{{ $calon->unit ? '- '.$calon->unit->name : '' }}
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/commitment.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/commitment.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="page">
	<div class="subpage normal-margin">
        <p class="text-center text-uppercase font-weight-bold" style="margin-top:-30px">Surat Komitmen Keuangan</p>
    	<table class="table-no-border">
    		<tr>
        		<td colspan="4">Yang bertanda tangan di bawah ini:</td>
        	</tr>
        	<tr>
        		<td class="p-b-0" style="width:205px">Nama Lengkap Ayah/Ibu</td>
        		<td class="p-b-0" style="width:14px">:</td>
        		<td class="p-b-0" colspan="2">{{ $calon->orangtua ? ($calon->orangtua->father_name ? $calon->orangtua->father_name : ($calon->orangtua->mother_name ? $calon->orangtua->mother_name : ($calon->orangtua->guardian_name ? $calon->orangtua->guardian_name : '-'))) : '-'  }}</td>
        	</tr>
        	<tr>
        		<td class="p-t-0 p-b-0 fs-11" colspan="4">(Orang tua dari calon siswa)</td>
        	</tr>
        	<tr>
        		<td>Nama Lengkap (Calon siswa)</td>
        		<td>:</td>
        		<td colspan="2">{{ $calon->student_name }}</td>
        	</tr>
        	<tr>
        		<td>Asal Sekolah</td>
        		<td>:</td>
        		<td colspan="2">{{ $calon->origin_school ? ($calon->origin_school == 'SIT Auliya' ? $calon->origin_school : $calon->origin_school_address ) : '-' }}</td>
        	</tr>
        	<tr>
        		<td>No. Pendaftaran</td>
        		<td>:</td>
        		<td style="width:132px">{{ $calon->reg_number ? $calon->reg_number : '-' }}</td>
        		<td>{!! $calon->unit ? '&#128505; '.$calon->unit->name : '&nbsp;' !!}</td>
        	</tr>
    	</table>
    	<p class="p-b-8 lh-1-2">Dengan ini saya menyatakan bahwa saya bersedia dan sanggup untuk memenuhi biaya pendidikan sebagai berikut:</p>
        <div class="commitment-section lh-1-2">
        	<ol class="financial-commitment fs-11 m-b-0">
	        	<li>Biaya Masuk Sekolah (BMS) {{ $calon->unit ? $calon->unit->name : 'TK, SD, SMP, SMA' }} (pilih salah satu)
	        		<ol type="a" class="financial-commitment">
	        			<li>Pilihan 1: BMS Tunai
			        		<table class="table-border">
			        			<tr>
			        				<td class="text-center" rowspan="2">Uraian Biaya</td>
			        				<td class="text-center" {{ $calon->unit ? '' : 'colspan="4"' }}>Unit</td>
			        			</tr>
			        			<tr>
			        			    @if($calon->unit)
			        				<td class="text-center">{{ $calon->unit->name }}</td>
			        			    @else
			        				<td class="text-center">TK</td>
			        				<td class="text-center">SD</td>
			        				<td class="text-center">SMP</td>
			        				<td class="text-center">SMA</td>
			        				@endif
			        			</tr>
			        			<tr>
			        				<td class="text-center">BMS</td>
        	        				@php
        	        				$bms = [
        	        				    'TK' => 16250000,
        	        				    'SD' => 31950000,
        	        				    'SMP' => 26450000,
        	        				    'SMA' => 26450000,
        	        				];
        	        				@endphp
        			        	    @if($calon->unit)
        			        		<td class="text-center">Rp {{ number_format($bms[$calon->unit->name], 0, ',', '.') }}</td>
        			        		@else
        	        				<td class="text-center">Rp 16.250.000</td>
        	        				<td class="text-center">Rp 31.950.000</td>
        	        				<td class="text-center">Rp 26.450.000</td>
        	        				<td class="text-center">Rp 26.450.000</td>
        			        		@endif
			        			</tr>
			        		</table>
	        			</li>
	        			<li>
	        				<span>Pilihan 2: BMS Berkala</span>
			        		<table class="table-border">
			        			@php
			        			$berkala = [
        	        				'TK' => [12285000,5265000,null],
        	        				'SD' => [25950000,5560000,5560000],
        	        				'SMP' => [17950000,6250000,6250000],
        	        			    'SMA' => [17950000,6250000,6250000]
        	        			];
        	        			$berkalaCount = count(array_filter($berkala[$calon->unit->name],function($x){return !empty($x);}));
			        			@endphp
			        			<tr>
			        				<td class="text-center" rowspan="2">Unit Biaya</td>
			        				<td class="text-center" {{ $calon->unit ? 'colspan='.$berkalaCount : 'colspan="3"' }}>BMS</td>
			        			</tr>
			        			<tr>
			        			    @if($calon->unit)
			        			    @for($i=1;$i<=$berkalaCount;$i++)
			        				<td class="text-center">Berkala {{ $i }}</td>
			        				@endfor
			        			    @else
			        				<td class="text-center">Berkala 1</td>
			        				<td class="text-center">Berkala 2</td>
			        				<td class="text-center">Berkala 3</td>
			        				@endif
			        			</tr>
			        			@if($calon->unit)
			        			<tr>
			        				<td class="text-center">{{ $calon->unit->name }}</td>
			        				@for($i=0;$i<$berkalaCount;$i++)
			        				<td class="text-center">{{ $berkala[$calon->unit->name][$i] ? 'Rp '.number_format($berkala[$calon->unit->name][$i], 0, ',', '.') : '-' }}</td>
			        				@endfor
			        			</tr>
			        			@else
			        			<tr>
			        				<td class="text-center">TK</td>
			        				<td class="text-center">Rp 12.285.000</td>
			        				<td class="text-center">Rp 5.265.000</td>
			        				<td class="text-center">-</td>
			        			</tr>
			        			<tr>
			        				<td class="text-center">SD</td>
			        				<td class="text-center">Rp 25.950.000</td>
			        				<td class="text-center">Rp 5.560.000</td>
			        				<td class="text-center">Rp 5.560.000</td>
			        			</tr>
			        			<tr>
			        				<td class="text-center">SMP</td>
			        				<td class="text-center">Rp 17.950.000</td>
			        				<td class="text-center">Rp 6.250.000</td>
			        				<td class="text-center">Rp 6.250.000</td>
			        			</tr>
			        			<tr>
			        				<td class="text-center">SMA</td>
			        				<td class="text-center">Rp 17.950.000</td>
			        				<td class="text-center">Rp 6.250.000</td>
			        				<td class="text-center">Rp 6.250.000</td>
			        			</tr>
			        			@endif
			        		</table>
			        		<br>
			        		<span>Cara Pembayaran Angsuran BMS Tunai Dan BMS Berkala 1:</span>
			        		<table class="table-schedule">
			        			<tr>
			        				<td class="text-center" style="width:120px">Keterangan</td>
			        				<td class="text-center">Waktu Pembayaran</td>
			        				<td class="text-center">Jumlah</td>
			        			</tr>
			        			<tr>
			        				<td>BMS 50 %</td>
			        				<td class="text-center">Tgl @for($i=0;$i<50;$i++).@endfor</td>
			        				<td class="text-center">Rp @for($i=0;$i<50;$i++).@endfor</td>
			        			</tr>
			        			@for($j=1;$j<6;$j++)
			        			<tr>
			        				<td>Angsuran {{ $j }}</td>
			        				<td class="text-center">Tgl @for($i=0;$i<50;$i++).@endfor</td>
			        				<td class="text-center">Rp @for($i=0;$i<50;$i++).@endfor</td>
			        			</tr>
			        			@endfor
			        			<tr>
			        				<td class="text-center font-weight-bold" colspan="2">Total</td>
			        				<td class="text-center">Rp @for($i=0;$i<50;$i++).@endfor</td>
			        			</tr>
			        		</table>
			        		<span class="fs-9">Keterangan:</span>
			        		<ol class="financial-commitment fs-9">
			        			<li>Orang tua melakukan pembayaran BMS setelah menerima surat pengumuman kelulusan.</li>
								<li>Jika pembayaran 50% dari BMS tidak diterima oleh panitia sampai batas waktu daftar ulang, calon siswa dianggap mengundurkan diri.</li>
								<li>Jika siswa mengundurkan diri, BMS yang sudah dibayarkan <b>tidak dapat dikembalikan</b>.</li>
								<li>Jika berdasarkan hasil observasi ditetapkan bahwa siswa ada catatan khusus, dan kemudian siswa mengundurkan diri maka BMS dapat dikembalikan 100%.</li>

			        		</ol>
	        			</li>
	        		</ol>
	        	</li>
	        </ol>
	    </div>
    </div>
</div>
<div class="page">
	<div class="subpage normal-margin">
        <div class="commitment-section lh-1-2">
        	<ol class="financial-commitment fs-11 m-b-0" start="2">
	        	<li>Sumbangan Pembinaan Pendidikan (SPP) {{ $calon->unit ? $calon->unit->name : 'TK, SD, SMP, SMA' }}
	        		<table class="table-border">
	        			<tr>
	        				<td class="text-center" rowspan="2">Uraian Biaya</td>
	        				<td class="text-center" {{ $calon->unit ? '' : 'colspan="4"' }}>Unit</td>
	        			</tr>
	        			<tr>
			        	    @if($calon->unit)
			        		<td class="text-center">{{ $calon->unit->name }}</td>
			        		@else
			        		<td class="text-center">TK</td>
			        		<td class="text-center">SD</td>
			        		<td class="text-center">SMP</td>
			        		<td class="text-center">SMA</td>
			        		@endif
	        			</tr>
	        			<tr>
	        				<td class="text-center">SPP*</td>
	        				@php
	        				$spp = [
	        				    'TK' => 1300000,
	        				    'SD' => 1800000,
	        				    'SMP' => 1550000,
	        				    'SMA' => 1600000,
	        				];
	        				@endphp
			        	    @if($calon->unit)
			        		<td class="text-center">Rp {{ number_format($spp[$calon->unit->name], 0, ',', '.') }}</td>
			        		@else
	        				<td class="text-center">Rp 1.300.000</td>
	        				<td class="text-center">Rp 1.800.000</td>
	        				<td class="text-center">Rp 1.550.000</td>
	        				<td class="text-center">Rp 1.600.000</td>
			        		@endif
	        			</tr>
	        		</table>
	        		<span class="fs-9">*Pembayaran SPP paling lambat tanggal 10 setiap bulan.</span>
	        	</li>
	        	<li>Dana Kontribusi<br>
	        		Dengan ini saya bersedia memberikan dana kontribusi untuk pengembangan pendidikan di Sekolah Islam Terpadu AULIYA sebesar Rp @for($i=0;$i<36;$i++).@endfor<br>
	        		(@for($i=0;$i<172;$i++).@endfor)
	        	</li>
	        	<li>Orang tua bersedia menerima konsekuensi dari sekolah jika tidak komitmen terhadap kesepakatan ini.</li>
        	</ol>
    	</div>
    	<table class="table-no-border">
    		<tr>
        		<td class="p-l-23 p-b-0" colspan="3">Tangerang Selatan, @for($i=0;$i<36;$i++).@endfor</td>
        	</tr>
        	<tr>
        		<td class="text-center p-b-0" style="width:250px">Calon Orang Tua Siswa,</td>
        		<td class="p-b-0" style="width:250px">&nbsp;</td>
        		<td class="text-center p-b-0 fs-11 border-all">Diketahui Oleh</td>
        	</tr>
        	<tr>
        		<td colspan="2" class="p-t-2 p-b-2">&nbsp;</td>
        		<td class="text-center p-t-2 p-b-2 fs-11 border-x">Petugas PSB</td>
        	</tr>
        	<tr>
        		<td class="p-t-12 p-b-10 p-l-23 fs-11">Materai 6000</td>
        		<td class="p-t-12 p-b-10">&nbsp;</td>
        		<td class="p-t-12 p-b-10 border-x">&nbsp;</td>
        	</tr>
        	<tr>
        		<td colspan="2" class="p-t-2 p-b-0">&nbsp;</td>
        		<td class="text-center p-t-2 p-b-0 fs-8 border-x">@for($i=0;$i<25;$i++){{'_'}}@endfor</td>
        	</tr>
        	<tr>
        		<td class="text-center p-t-0 p-b-2 fs-11">(@for($i=0;$i<25;$i++){{'_'}}@endfor)</td>
        		<td class="p-t-0">&nbsp;</td>
        		<td class="text-center p-t-0 p-b-2 fs-8 border-x border-bottom">Nama Jelas</td>
        	</tr>
        	<tr>
        		<td class="text-center p-t-0">(Ayah/Ibu)</td>
        		<td colspan="2" class="p-t-0">&nbsp;</td>
        	</tr>
    	</table>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
<!-- include('template.footjs.print.print_window') -->
@endsection