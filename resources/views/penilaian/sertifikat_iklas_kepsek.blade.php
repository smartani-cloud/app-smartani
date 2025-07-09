<<<<<<< HEAD
@extends('template.print.A4.master')

@section('title')
{{ $sertif->siswa->student_nisn}} - {{ $sertif->siswa->identitas->student_name}} - Sertifikat IKLaS
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/iklas.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/iklas.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="watermark">
    <div class="page">
        <div class="subpage" style="text-align: center;">
            <div class="title-container p-t-238">
                <span class="title" style="font-weight: bold;text-align: center;font-size: 18pt;">SERTIFIKAT KOMPETENSI IKLaS</span>
            </div>
            <div id="dataSiswa" class="m-t-100">
                <table class="cover-data">
                    <tr>
                        <td>Diberikan Kepada :</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-decoration:underline;font-size : 14pt; font-weight: bold;">{{ $sertif->siswa->identitas->student_name }}</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            Telah menuntaskan kegiatan pembelajaran kompetensi IKLaS selama
                            <?php if ($sertif->unit->name == 'SD') {
                                echo "6";
                            } else {
                                echo "3";
                            } ?> tahun
                        </td>
                    </tr>
                    <tr>
                        <td>di {{ $sertif->unit->long_desc ? $sertif->unit->long_desc : $sertif->unit->desc }} dengan predikat :</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;font-size:16pt;">
                            @if($nilai->detail()->average('predicate') >= 4)
                            SANGAT BAIK
                            @elseif($nilai->detail()->average('predicate') >= 3)
                            BAIK
                            @elseif($nilai->detail()->average('predicate') >= 2)
                            CUKUP BAIK
                            @else
                            KURANG
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Tangerang Selatan, {{ $sertif->certificate_date ? Date::parse($sertif->certificate_date)->format('j F Y') : Date::now('Asia/Jakarta')->format('j F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Kepala {{ $sertif->unit->long_desc ? $sertif->unit->long_desc : $sertif->unit->desc }}</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>{{ $sertif->hm_name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="page">
        <div class="subpage">
            <p class="text-center fs-22 font-weight-bold p-t-50">CAPAIAN KOMPETENSI IKLaS</p>
            <p class="text-center text-uppercase fs-18 font-weight-bold">{{ $sertif->unit->long_desc ? $sertif->unit->long_desc : $sertif->unit->desc }}</p>
            <p class="text-center fs-18 font-weight-bold">Tahun Pelajaran {{ $sertif->tahunAjaran->academic_year }}</p>
            <div id="dataSiswa" class="m-t-22 m-l-36 m-r-36">
                <table>
                    <tr>
                        <td style="width: 17%">
                            Nama
                        </td>
                        <td style="width: 2%">
                            :
                        </td>
                        <td style="width: 40%">
                            {{ $sertif->siswa->identitas->student_name }}
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
                            {{ $sertif->siswa->student_nisn }}
                        </td>
                    </tr>
                </table>
            </div>
            <div id="kurikulumIklas" class="m-t-22">
                <div class="m-l-36 m-r-36">
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
                            @endphp

                            @foreach($iklas as $i)
                            @if($category_active != $i->iklas_cat)
                            @php
                            $category_active = $i->iklas_cat;
                            $rowspan = $iklas->where('iklas_cat',$i->iklas_cat)->count()+1;
                            @endphp
                            <tr>
                                <td class="text-center" rowspan="{{ $rowspan }}" style="vertical-align: top">{{ $i->iklas_cat }}</td>
                                <td class="font-weight-bold" colspan="2">{{ $i->competence }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td>{{ $i->categoryNumber.' '.$i->category }}</td>
                                <td class="text-center">
                                    @if($nilai->detail && $nilai->detail()->where('iklas_ref_id',$i->id)->count() > 0)
                                    @php
                                    $stars = $nilai->detail()->where('iklas_ref_id',$i->id)->first()->predicate;
                                    @endphp
                                    @for($i=0;$i<$stars;$i++)
                                    <i class="fas fa-star"></i>
                                    @endfor
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="m-t-16 m-b-16">
                        <table class="fs-12">
                            <tr>
                                <td colspan="3">
                                    Catatan:
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
{{ $sertif->siswa->student_nisn}} - {{ $sertif->siswa->identitas->student_name}} - Sertifikat IKLaS
@endsection

@section('headmeta')
<!-- Custom styles for this template -->
<link href="{{ asset('css/docs/iklas.css') }}" rel="stylesheet" type="text/css" media="screen">
<link href="{{ asset('css/docs/iklas.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

@section('content')
<div class="watermark">
    <div class="page">
        <div class="subpage" style="text-align: center;">
            <div class="title-container p-t-238">
                <span class="title" style="font-weight: bold;text-align: center;font-size: 18pt;">SERTIFIKAT KOMPETENSI IKLaS</span>
            </div>
            <div id="dataSiswa" class="m-t-100">
                <table class="cover-data">
                    <tr>
                        <td>Diberikan Kepada :</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-decoration:underline;font-size : 14pt; font-weight: bold;">{{ $sertif->siswa->identitas->student_name }}</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            Telah menuntaskan kegiatan pembelajaran kompetensi IKLaS selama
                            <?php if ($sertif->unit->name == 'SD') {
                                echo "6";
                            } else {
                                echo "3";
                            } ?> tahun
                        </td>
                    </tr>
                    <tr>
                        <td>di {{ $sertif->unit->long_desc ? $sertif->unit->long_desc : $sertif->unit->desc }} dengan predikat :</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;font-size:16pt;">
                            @if($nilai->detail()->average('predicate') >= 4)
                            SANGAT BAIK
                            @elseif($nilai->detail()->average('predicate') >= 3)
                            BAIK
                            @elseif($nilai->detail()->average('predicate') >= 2)
                            CUKUP BAIK
                            @else
                            KURANG
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Tangerang Selatan, {{ $sertif->certificate_date ? Date::parse($sertif->certificate_date)->format('j F Y') : Date::now('Asia/Jakarta')->format('j F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Kepala {{ $sertif->unit->long_desc ? $sertif->unit->long_desc : $sertif->unit->desc }}</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>{{ $sertif->hm_name }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="page">
        <div class="subpage">
            <p class="text-center fs-22 font-weight-bold p-t-50">CAPAIAN KOMPETENSI IKLaS</p>
            <p class="text-center text-uppercase fs-18 font-weight-bold">{{ $sertif->unit->long_desc ? $sertif->unit->long_desc : $sertif->unit->desc }}</p>
            <p class="text-center fs-18 font-weight-bold">Tahun Pelajaran {{ $sertif->tahunAjaran->academic_year }}</p>
            <div id="dataSiswa" class="m-t-22 m-l-36 m-r-36">
                <table>
                    <tr>
                        <td style="width: 17%">
                            Nama
                        </td>
                        <td style="width: 2%">
                            :
                        </td>
                        <td style="width: 40%">
                            {{ $sertif->siswa->identitas->student_name }}
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
                            {{ $sertif->siswa->student_nisn }}
                        </td>
                    </tr>
                </table>
            </div>
            <div id="kurikulumIklas" class="m-t-22">
                <div class="m-l-36 m-r-36">
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
                            @endphp

                            @foreach($iklas as $i)
                            @if($category_active != $i->iklas_cat)
                            @php
                            $category_active = $i->iklas_cat;
                            $rowspan = $iklas->where('iklas_cat',$i->iklas_cat)->count()+1;
                            @endphp
                            <tr>
                                <td class="text-center" rowspan="{{ $rowspan }}" style="vertical-align: top">{{ $i->iklas_cat }}</td>
                                <td class="font-weight-bold" colspan="2">{{ $i->competence }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td>{{ $i->categoryNumber.' '.$i->category }}</td>
                                <td class="text-center">
                                    @if($nilai->detail && $nilai->detail()->where('iklas_ref_id',$i->id)->count() > 0)
                                    @php
                                    $stars = $nilai->detail()->where('iklas_ref_id',$i->id)->first()->predicate;
                                    @endphp
                                    @for($i=0;$i<$stars;$i++)
                                    <i class="fas fa-star"></i>
                                    @endfor
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="m-t-16 m-b-16">
                        <table class="fs-12">
                            <tr>
                                <td colspan="3">
                                    Catatan:
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
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level custom scripts -->
@include('template.footjs.print.print_window')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection