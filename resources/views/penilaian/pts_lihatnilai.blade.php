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
        <div id="kurikulumNasional" class="m-t-22 m-b-16">
            <table class="table-border">
                @php
                $column = $nilai_harian->countBy('score_knowledge_id')->max();
                @endphp
                <tr>
                    <th style="width: 3%">
                        No
                    </th>
                    <th style="width: 26%">
                        Mata Pelajaran
                    </th>
                    <th colspan="{{ $column }}" style="width: 26%">
                        Penilaian Harian
                    </th>
                    <th style="width: 5%">
                        PTS
                    </th>
                    <th style="width: 12%">
                        Keterampilan
                    </th>
                    <th style="width: 8%">
                        Sikap
                    </th>
                    <th style="width: 20%">
                        Deskripsi
                    </th>
                </tr>
                @php
                $active = null;
                $j = 1;
                @endphp
                @foreach($kelompok as $k)
                @if($k->matapelajarans()->count() > 0)
                @if($active != $k->id && $unit->name != 'SD')
                <tr>
                    <td class="font-weight-bold" colspan="{{ $j == 1 ? $column+6 : $column+5 }}">{{ $k->group_subject_name }} {{ $k->jurusan ? $k->jurusan->major_name : '' }}</td>
                </tr>
                @php
                $active = $k->id;
                $j++;
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
                $matapelajarans = $matapelajarans->get();
                @endphp
                @foreach($matapelajarans as $m)
                <tr>
                    <td class="text-center">{{ $i++ }}</td>
                    <td>{{ $m->subject_name }}</td>
                    @php
                    $pengetahuan = $rapor->pengetahuan()->where('subject_id',$m->id)->first();
                    $keterampilan = $rapor->keterampilan()->where('subject_id',$m->id)->first();
                    $sikap = $rapor->sikap_pts()->where('subject_id',$m->id)->first();
                    $m = 1;
                    @endphp
                    @if($pengetahuan)
                    @foreach($nilai_harian->where('score_knowledge_id',$pengetahuan->id) as $h)
                    <td class="text-center">{{ number_format((float)$h->score, 0, ',', '') }}</td>
                    @php $m++ @endphp
                    @endforeach
                    @endif
                    @if($m <= $column) @for($l=$m; $l<=$column; $l++) <td class="text-center">-</td>
                        @endfor
                        @endif
                        <td class="text-center">{{ $pengetahuan && $pengetahuan->pts ? number_format((float)$pengetahuan->pts, 0, ',', '') : '' }}</td>
                        @php
                        $nilai_keterampilan = $keterampilan ? number_format((float)$keterampilan->nilaiketerampilandetail()->whereNotNull('score')->average('score'), 0, ',', '') : '';
                        @endphp
                        <td class="text-center">{{ $nilai_keterampilan == 0 ? '-' : $nilai_keterampilan }}</td>
                        @php
                        $nilai_sikap = $sikap ? $sikap->predicate : '';
                        @endphp
                        <td class="text-center">{{ $nilai_sikap }}</td>
                        @if(($unit->name == 'SD' && $j == 1) || ($unit->name != 'SD' && $j == 2))
                        <td rowspan="{{ $total_rows-1 }}" style="vertical-align: top">{{ $rapor->pts && $rapor->pts->description ? str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->pts->description) : '' }}</td>
                        @endif
                        @php $j++ @endphp
                </tr>
                @endforeach
                @php
                    $j = 'a';
                    $n = 1;
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
                    @if($j == 'a')
                    <tr>
                        <td class="text-center" rowspan="{{ count($matapelajarans)+1 }}" style="vertical-align: top">{{ $i }}</td>
                        <td colspan="{{ $column+4 }}">Muatan Lokal</td>
                    </tr>
                    @endif
                    <tr>
                    <td>{{ $j }}. {{ $m->subject_name }}</td>
                    @php
                    $pengetahuan = $rapor->pengetahuan()->where('subject_id',$m->id)->first();
                    $keterampilan = $rapor->keterampilan()->where('subject_id',$m->id)->first();
                    $sikap = $rapor->sikap_pts()->where('subject_id',$m->id)->first();
                    $m = 1;
                    @endphp
                    @if($pengetahuan)
                    @foreach($nilai_harian->where('score_knowledge_id',$pengetahuan->id) as $h)
                    <td class="text-center">{{ number_format((float)$h->score, 0, ',', '') }}</td>
                    @php $m++ @endphp
                    @endforeach
                    @endif
                    @if($m <= $column)
                        @for($l=$m; $l<=$column; $l++) <td class="text-center">-</td>@endfor
                    @endif
                    <td class="text-center">{{ $pengetahuan && $pengetahuan->pts ? number_format((float)$pengetahuan->pts, 0, ',', '') : '' }}</td>
                    @php
                    $nilai_keterampilan = $keterampilan ? number_format((float)$keterampilan->nilaiketerampilandetail()->whereNotNull('score')->average('score'), 0, ',', '') : '';
                    @endphp
                    <td class="text-center">{{ $nilai_keterampilan == 0 ? '-' : $nilai_keterampilan }}</td>
                    @php
                    $nilai_sikap = $sikap ? $sikap->predicate : '';
                    @endphp
                    <td class="text-center">{{ $nilai_sikap }}</td>
                    @if(($unit->name == 'SD' && $j == 1) || ($unit->name != 'SD' && $j == 2))
                    <td rowspan="{{ $total_rows-1 }}" style="vertical-align: top">{{ str_replace("@nama",$siswa->identitas->student_name ? $siswa->identitas->student_name : '',$rapor->pts->description) }}</td>
                    @endif
                </tr>
                @php
                $j = chr(ord($j)+1);
                $n++;
                @endphp
                @endforeach
                @endif
                @endif
                @endforeach
            </table>
        </div>
        <!--<div id="ekstrakurikuler" class="m-t-22">
            <table class="table-border">
                <tr>
                    <th style="width: 3%">
                        No
                    </th>
                    <th style="width: 37%">
                        Ekstrakurikuler
                    </th>
                    <th style="width: 60%">
                        Keterangan
                    </th>
                </tr>

                @php $i = 1 @endphp
                @foreach($rapor->ekstra()->get() as $e)
                <tr>
                    <td class="text-center">
                        {{ $i++ }}
                    </td>
                    <td class="text-center">
                        {{ $e->extra_name }}
                    </td>
                    <td class="text-center">
                        {{ $e->description }}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>-->
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
                        {{ isset($rapor->pts->sick) ? $rapor->pts->sick ? $rapor->pts->sick : '0' : '0' }} hari
                    </td>
                </tr>
                <tr>
                    <td class="text-center">2</td>
                    <td>Izin</td>
                    <td class="text-center">
                        {{ isset($rapor->pts->leave) ? $rapor->pts->leave ? $rapor->pts->leave : '0' : '0' }} hari
                    </td>
                </tr>
                <tr>
                    <td class="text-center">3</td>
                    <td>Alpa</td>
                    <td class="text-center">
                        {{ isset($rapor->pts->absent) ? $rapor->pts->absent ? $rapor->pts->absent : '0' : '0' }} hari
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
@endsection