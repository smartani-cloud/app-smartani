@extends('template.print.master')

@section('title')
{{ $siswa->student_nis}} - {{ $siswa->identitas->student_name}} - Referensi Ijazah
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
            <p class="text-center text-uppercase fs-22 font-weight-bold">DAFTAR NILAI</p>
            <p class="text-center text-uppercase fs-18 font-weight-bold">{{ $unit->long_desc ? $unit->long_desc : $unit->desc }}</p>
            <p class="text-center text-uppercase fs-18 font-weight-bold">TAHUN PELAJARAN {{ $semester->tahunAjaran->academic_year }}</p>
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
                    </tr>
                    <tr>
                        <td style="width: 20%">
                            Tempat dan Tanggal Lahir
                        </td>
                        <td style="width: 2%">
                            :
                        </td>
                        <td style="width: 19%">
                            {{ $siswa->identitas->birth_place }}, {{ $siswa->identitas->birth_date ? Date::parse($siswa->identitas->birth_date)->format('j F Y') : Date::now('Asia/Jakarta')->format('j F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Nomor Induk Peserta Didik
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            {{ $siswa->student_nis }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Nomor Induk Siswa Nasional
                        </td>
                        <td>
                            :
                        </td>
                        <td>
                            {{ $siswa->student_nisn }}
                        </td>
                    </tr>
                </table>
            </div>
            <div id="kurikulumNasional" class="m-t-22">
                <div class="m-l-18">
                    <div class="m-t-16 m-b-16">
                        <table class="table-border">
                            <tr>
                                <th style="width: 5%">
                                    No
                                </th>
                                <th style="width: 40%">
                                    Mata Pelajaran<br>(Kurikulum 2013)
                                </th>
                                <th style="width: 10%">
                                    Nilai Ujian Sekolah
                                </th>
                            </tr>
                            @php
                            $active = null;
                            @endphp
                            @foreach($kelompok as $k)
                            @if($k->matapelajarans()->count())
                            @if($active != $k->id)
                            <tr>
                                <td class="font-weight-bold" colspan="3">{{ $k->group_subject_name }} {{ $k->jurusan ? $k->jurusan->major_name : '' }}</td>
                            </tr>
                            @php $active = $k->id @endphp
                            @endif
                            @php
                            $i = 1;
                            $matapelajarans = $k->matapelajarans()->whereNull('is_mulok')->get();
                            @endphp
                            @foreach($matapelajarans as $m)
                            <tr>
                                <td class="text-center">{{ $i++ }}</td>
                                <td>{{ $m->subject_name }}</td>
                                @php
                                $pengetahuan = $skhb->nilai()->where('subject_id',$m->id)->first();
                                @endphp
                                <td class="text-center">{{ $pengetahuan ? ($pengetahuan->avg ? number_format((float)$pengetahuan->avg, 0, ',', '') : '') : '' }}</td>
                            </tr>
                            @endforeach
                            @php
                            $j = 'a';
                            $matapelajarans = $k->matapelajarans()->mulok()->get();
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
                                $pengetahuan = $skhb->nilai()->where('subject_id',$m->id)->first();
                                @endphp
                                <td class="text-center">{{ $pengetahuan ? ($pengetahuan->avg ? number_format((float)$pengetahuan->avg, 0, ',', '') : '') : '' }}</td>
                            </tr>
                            @php
                            $j = chr(ord($j)+1);
                            @endphp
                            @endforeach
                            @endif
                            @endif
                            @endforeach
                            <tr>
                                <td class="text-center" colspan="2">Rata-rata</td>
                                <td class="text-center">{{ number_format((float)$skhb->nilai()->average('avg'), 0, ',', '') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @if($skhb->report_status_id == 1)
        <div id="tandaTangan" class="m-t-28">
            <table class="tanda-tangan">
                <tr>
                    <td>&nbsp;</td>
                    <td>Tangerang Selatan, {{ Date::now('Asia/Jakarta')->format('j F Y') }}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>Mengetahui,</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>Kepala Sekolah</td>
                </tr>
                <tr>
                    <td class="ttd">&nbsp;</td>
                    <td class="ttd">&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>{{ $skhb->hm_name }}</td>
                </tr>
            </table>
        </div>
        @endif
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

@if($skhb->report_status_id == 1)
<script>
    window.onload = function() {
        window.print();
    }
</script>
@endif
<!-- Page level custom scripts -->
@endsection