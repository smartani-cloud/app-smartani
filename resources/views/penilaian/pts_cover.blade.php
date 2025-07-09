@extends('template.print.A4.master')

@section('title')
{{ $siswa->student_nis}} - {{ $siswa->identitas->student_name}} - LTS - Cover
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/report_cover.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/report_cover.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="page">
    <div class="subpage">
        <div class="title-container">
            <img class="logo" src="{{ asset('img/logo/logo-vertical.png') }}" />
            <p class="title">Laporan Tengah Semester</p>
            <p class="title text-uppercase">{{ $unit->long_desc ? $unit->long_desc : $unit->desc }}</p>
        </div>
        <div id="dataSiswa" class="m-t-152">
            <table class="cover-data">
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $siswa->identitas->student_name }}</td>
                </tr>
                @if($unit->name == 'TK')
                <tr>
                    <td>NIPD</td>
                    <td>:</td>
                    <td>{{ $siswa->student_nis }}</td>
                </tr>
                @else
                <tr>
                    <td>NISN</td>
                    <td>:</td>
                    <td>{{ $siswa->student_nisn }}</td>
                </tr>
                @endif
                <tr>
                    <td>Kelas</td>
                    <td>:</td>
                    <td>{{ $siswa->kelas->level->level_romawi }}{{ $siswa->kelas->jurusan ? ' '.$siswa->kelas->jurusan->major_name.' ' : ' ' }}{{ $siswa->kelas->namakelases->class_name }}</td>
                </tr>
                <tr>
                    <td>Semester</td>
                    <td>:</td>
                    <td>{{ $semester->semester }}</td>
                </tr>
                <tr>
                    <td>Tahun Pelajaran</td>
                    <td>:</td>
                    <td>{{ $semester->tahunAjaran->academic_year }}</td>
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
@endsection