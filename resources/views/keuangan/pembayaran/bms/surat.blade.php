@extends('template.print.A4.master')

@section('title')
{{ $calon->identitas->student_name }} - Surat Tagihan SPP {{ $calon->unit ? '- '.$calon->unit->name : '' }}
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/commitment.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/commitment.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="page">
	<div class="subpage normal-margin">
    @php
      setlocale(LC_TIME, 'id_ID');
      \Carbon\Carbon::setLocale('id');
      $day = Carbon\Carbon::now()->format("d");
      $month = monthText(Carbon\Carbon::now()->format("m"));
      $year = Carbon\Carbon::now()->format("Y");
      $date_now = Carbon\Carbon::now()->format("Y-m").'-17';
      Date::setLocale('id');
      $hari = Date::createFromFormat('Y-m-d',$date_now)->format('l, d F Y');
    @endphp
      <p class="p-b-8 lh-1-2">Tangerang Selatan, {{$day.' '.$month.' '.$year}}</p>
    	<table class="table-no-border">
        	<tr>
        		<td class="p-b-0" style="width:105px">Perihal</td>
        		<td class="p-b-0" style="width:14px">:</td>
        		<td class="p-b-0" colspan="2"><b>Pemberitahuan BMS</b></td>
        	</tr>
    	</table>
      <br><br>
    	<p class="p-b-8 lh-1-2">Kepada Yth,</p>
    	<p class="p-b-8 lh-1-2">Orang Tua/Wali Ananda</p>
    	<p class="p-b-8 lh-1-2"> <b>{{ $calon->identitas->student_name }} (Kelas {{$calon->level->level_romawi}} {{$calon->kelas ? $calon->kelas->jurusan ?$calon->kelas->jurusan->major_name.' ':'':''}}- {{$calon->kelas?$calon->kelas->namakelases->class_name:''}} )</b> </p>
    	<p class="p-b-8 lh-1-2">{{$calon->unit->desc}}</p>
    	<p class="p-b-8 lh-1-2">di Tempat</p>
      <br><br>
    	<p class="p-b-8 lh-1-2">Assalamu'alaikum Warahmatullahi Wabarakatuh,</p>
    	<p class="p-b-8 lh-1-2">Bersama ini kami informasikan pembayaran BMS Ananda sebagai berikut:</p>
    	<table class="table-no-border">
        <tr>
          <td class="p-b-0" style="width:305px">Total BMS yang harus dibayarkan</td>
          <td class="p-b-0" style="width:14px">:</td>
          <td class="p-b-0" colspan="2">Rp {{ number_format($bms->bms_nominal, 0, ',', '.') }}</td>
        </tr>
        <tr>
          <td class="p-b-0" style="width:205px">BMS yang sudah dibayarkan</td>
          <td class="p-b-0" style="width:14px">:</td>
          <td class="p-b-0" colspan="2"><u>Rp {{ number_format($bms->bms_paid, 0, ',', '.') }}</u></td>
        </tr>
        <tr style="border-top: 1pt solid black;">
          <td class="p-b-0" style="width:205px"><b>Total Tanggungan BMS</b></td>
          <td class="p-b-0" style="width:14px">:</td>
          <td class="p-b-0" colspan="2"><b>Rp {{ number_format($bms->bms_nominal - $bms->bms_paid, 0, ',', '.') }}</b></td>
        </tr>
        <tr>
          <td class="p-b-0" style="width:205px"><b>BMS yang harus dibayarkan</b></td>
          <td class="p-b-0" style="width:14px">:</td>
          <td class="p-b-0" colspan="2"><b>Rp {{ number_format($bms->bms_remain, 0, ',', '.') }}</b></td>
        </tr>
      </table>
      <br><br>
      <p class="p-b-8 lh-1-2">Sekiranya Ayah Bunda berkenan untuk memberikan konfirmasi pembayaran BMS tersebut paling lambat <b>pada hari {{$hari}}</b> kepada bagian Administrasi Keuangan {{$calon->unit->desc}} di nomor {{$calon->unit->phone_unit}} (via Telphone ) atau {{$calon->unit->whatsapp_unit}} (via WhatsApp ), dikarenakan belum tercatat pada Rekening Koran Auliya per tanggal {{$day.' '.$month.' '.$year}}.</p>
      <br>
      <p class="p-b-8 lh-1-2">Demikian surat pemberitahuan ini kami sampaikan atas perhatian Ayah Bunda kami ucapkan terima kasih. Wassalamu’alaikum Warahmatullahi Wabarakatuh.</p>
      <br><br>
      <p class="p-b-8 lh-1-2">Kepala {{$calon->unit->desc}}</p>
      <br><br><br>
      {{-- <p class="p-b-8 lh-1-2">{{$calon->unit->kepala[0]->name}}</p> --}}
      <p class="p-b-8 lh-1-2"><b>{{$calon->unit->kepala[0]->name}}</b></p>
      <br><br>
      <p class="p-b-8 lh-1-2" style="font-size: 12px"> 
        <center>
          Dokumen ini resmi diterbitkan melalui Sistem Informasi Sekolah Islam Terpadu AULIYA (SISTA).
        </center> 
      </p>
      <p class="p-b-8 lh-1-2" style="font-size: 12px">
        <center>
          Kebenaran dan keabsahan atas data yang ditampilkan dapat dipertanggungjawabkan.
        </center>
      </p>
    </div>
  </div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
<!-- include('template.footjs.print.print_window') -->
@endsection