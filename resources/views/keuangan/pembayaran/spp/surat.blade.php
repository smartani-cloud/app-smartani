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
      $hari = Date::createFromFormat('Y-m-d',$spp_bill->year.'-'.$spp_bill->month.'-17')->format('l, d F Y');
    @endphp
      <p class="p-b-8 lh-1-2">Tangerang Selatan, {{$day.' '.$month.' '.$year}}</p>
    	<table class="table-no-border">
        	<tr>
        		<td class="p-b-0" style="width:105px">Perihal</td>
        		<td class="p-b-0" style="width:14px">:</td>
        		<td class="p-b-0" colspan="2"><b>Pemberitahuan SPP</b></td>
        		{{-- <td class="p-b-0" colspan="2">{{ $calon->orangtua ? ($calon->orangtua->father_name ? $calon->orangtua->father_name : ($calon->orangtua->mother_name ? $calon->orangtua->mother_name : ($calon->orangtua->guardian_name ? $calon->orangtua->guardian_name : '-'))) : '-'  }}</td> --}}
        	</tr>
    	</table>
      <br><br>
    	<p class="p-b-8 lh-1-2">Kepada Yth,</p>
    	<p class="p-b-8 lh-1-2">Orang Tua/Wali Ananda</p>
    	<p class="p-b-8 lh-1-2"> <b>{{ $calon->identitas->student_name }} (Kelas {{$calon->level ? $calon->level->level_romawi : ''}} {{$calon->kelas->major_id ? $calon->kelas->jurusan->major_name.' ':''}}- {{$calon->kelas->namakelases->class_name}} )</b> </p>
    	{{-- <p class="p-b-8 lh-1-2"> <b>{{ $calon->identitas->orangtua ? ($calon->identitas->orangtua->father_name ? $calon->identitas->orangtua->father_name : ($calon->identitas->orangtua->mother_name ? $calon->identitas->orangtua->mother_name : ($calon->identitas->orangtua->guardian_name ? $calon->identitas->orangtua->guardian_name : '-'))) : '-'  }} (Kelas {{$calon->level->level_romawi}} {{$calon->kelas->major_id ? $calon->kelas->jurusan->major_name.' ':''}}- {{$calon->kelas->namakelases->class_name}} )</b> </p> --}}
    	<p class="p-b-8 lh-1-2">{{$calon->unit->desc}}</p>
    	<p class="p-b-8 lh-1-2">di Tempat</p>
      <br><br>
    	<p class="p-b-8 lh-1-2">Assalamu'alaikum Warahmatullahi Wabarakatuh,</p>
    	<p class="p-b-8 lh-1-2">Bersama ini kami informasikan pembayaran SPP Ananda sebagai berikut:</p>
    	<table class="table-no-border">
        <tr>
          <td class="p-b-0" style="width:205px">Tagihan Sebelumnya</td>
          <td class="p-b-0" style="width:14px">:</td>
          <td class="p-b-0" colspan="2">Rp {{number_format($bill_before, 0, ',', '.')}}</td>
        </tr>
        <tr>
          <td class="p-b-0" style="width:205px">Spp Bulan {{monthText($spp_bill->month).' '.$spp_bill->year}}</td>
          <td class="p-b-0" style="width:14px">:</td>
          <td class="p-b-0" colspan="2"><u>Rp {{number_format($spp_bill->spp_nominal - $spp_bill->spp_paid, 0, ',', '.')}}</u></td>
        </tr>
        <tr>
          <td class="p-b-0" style="width:205px">Total yang harus dibayarkan</td>
          <td class="p-b-0" style="width:14px">:</td>
          <td class="p-b-0" colspan="2"><b>Rp {{ number_format($calon->spp->remain, 0, ',', '.') }}</b></td>
        </tr>
      </table>
      <br><br>
      <p class="p-b-8 lh-1-2">Sekiranya Ayah Bunda berkenan untuk memberikan konfirmasi pembayaran SPP tersebut paling lambat <b>pada hari {{$hari}}</b> kepada bagian Administrasi Keuangan {{$calon->unit->desc}} di nomor {{$calon->unit->phone_unit}} (via Telphone ) atau {{$calon->unit->whatsapp_unit}} (via WhatsApp ), dikarenakan belum tercatat pada Rekening Koran Auliya per tanggal 10 {{monthText($spp_bill->month).' '.$year}}.</p>
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