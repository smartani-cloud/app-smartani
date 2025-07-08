@extends('template.print.A4.master')

@section('title')
{{ $siswa->student_nis}} - {{ $siswa->identitas->student_name}} - Rapor - Cover
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
            <p class="title">Rapor</p>
            <p class="title text-uppercase">{{ $unit->long_desc ? $unit->long_desc : $unit->desc }}</p>
        </div>
        <div id="dataSiswa" class="name-container m-t-185">
            <p>Nama Peserta Didik</p>
            <table class="cover-name">
                <tr>
                    <td class="font-weight-bold fs-18">{{ $siswa->identitas->student_name }}</td>
                </tr>
            </table>
            @if($unit->name == 'TK')
            <p>NIPD</p>
            <table class="cover-name">
                <tr>
                    <td>{{ $siswa->student_nis }}</td>
                </tr>
            </table>
            @else
            <p>NISN</p>
            <table class="cover-name">
                <tr>
                    <td>{{ $siswa->student_nisn }}</td>
                </tr>
            </table>
            @endif
        </div>
        <div class="ministry-container text-uppercase m-t-58">
            <p>Kementerian Pendidikan dan Kebudayaan</p>
            <p>Republik Indonesia</p>
        </div>
    </div>
</div>
@if($unit->name != 'TK')
<div class="page">
    <div class="subpage normal-margin">
        <div class="title-container">
            <p class="title">Petunjuk Pengisian</p>
        </div>
        <div id="petunjukPengisian" class="m-t-44">
            <p>Rapor merupakan ringkasan hasil penilaian terhadap seluruh aktivitas pembelajaran yang dilakukan peserta didik dalam kurun waktu tertentu. Rapor dipergunakan selama peserta didik yang bersangkutan mengikuti seluruh program pembelajaran di {{ $unit->full_name ? $unit->full_name : $unit->name }} tersebut. Berikut ini petunjuk untuk mengisi rapor:</p>
            <ol>
                <li>Identitas sekolah diisi dengan data yang sesuai dengan keberadaan {{ $unit->full_name ? $unit->full_name : $unit->name }}</li>
                <li>Keterangan tentang diri peserta didik diisi lengkap</li>
                <li>Rapor dilengkapi dengan pas foto peserta didik ukuran (3 x 4) cm berwarna.</li>
                <li>Deskripsi sikap spiritual dan sikap sosial diambil dari catatan (jurnal) perkembangan sikap peserta didik yang ditulis oleh guru mata pelajaran, guru BK, dan wali kelas</li>
                <li>Capaian peserta didik dalam pengetahuan dan keterampilan ditulis dalam bentuk angka, predikat, dan deskripsi untuk masing-masing mata pelajaran</li>
                <li>Laporan ekstrakurikuler diisi dengan nama dan nilai kegiatan ekstrakurikuler yang diikuti oleh peserta didik</li>
                <li>Saran-saran diisi dengan hal-hal yang perlu mendapatkan perhatian peserta didik</li>
                <li>Prestasi diisi dengan jenis prestasi peserta didik yang diraih dalam bidang akademik dan nonakademik</li>
                <li>Ketidakhadiran ditulis dengan data akumulasi ketidakhadiran peserta didik karena sakit, izin, atau alpa/tanpa keterangan selama satu semester</li>
                <li>Tanggapan orang tua/wali adalah tanggapan atas pencapaian hasil belajar peserta didik</li>
                <li>Keterangan pindah keluar sekolah diisi dengan alasan kepindahan. Sedangkan pindah masuk diisi dengan sekolah asal</li>
                <li>KKM (Kriteria Ketuntasan Minimal) diisi dengan nilai minimal pencapaian ketuntasan kompetensi belajar peserta didik yang ditetapkan oleh satuan pendidikan</li>
                <li>Nilai diisi dengan nilai pencapaian kompetensi belajar peserta didik</li>
                <li>Predikat untuk aspek pengetahuan dan keterampilan diisi dengan huruf A, B, C, atau D sesuai panjang interval dan KKM yang sudah ditetapkan oleh satuan pendidikan</li>
                <li>Predikat untuk aspek sikap diisi dengan Sangat Baik, Baik, Cukup, atau Kurang</li>
                <li>Deskripsi diisi uraian tentang pencapaian kompetensi peserta didik</li>
                <li>Tabel interval predikat berdasarkan KKM</li>
            </ol>
        </div>
    </div>
</div>
@endif
<div class="page">
    <div class="subpage normal-margin">
        <div class="title-container">
            <p class="title">Rapor</p>
            <p class="title text-uppercase">{{ $unit->long_desc ? $unit->long_desc : $unit->desc }}</p>
        </div>
        <div id="dataSekolah">
            <table class="unit-data">
                <tr>
                    <td>Nama Yayasan</td>
                    <td>:</td>
                    <td class="font-weight-bold">Yayasan Sekolah Islam Auliya</td>
                </tr>
                <tr>
                    <td>Nama Sekolah</td>
                    <td>:</td>
                    <td class="font-weight-bold">{{ $unit->short_desc ? $unit->short_desc : $unit->desc }}</td>
                </tr>
                <tr>
                    <td>NPSN</td>
                    <td>:</td>
                    <td>{!! $unit->npsn ? $unit->npsn : '&nbsp;' !!}</td>
                </tr>
                <tr>
                    <td>NIS/NSS/NDS</td>
                    <td>:</td>
                    <td>{!! $unit->nis ? $unit->nis : '&nbsp;' !!}</td>
                </tr>
                @if($unit->id == 2)
                @if(in_array($riwayatKelas->level->level,['1','2']))
                @php
                $address = explode(';',$unit->address)[1];
                $phone = explode(';',$unit->phone_unit)[1];
                @endphp
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ explode('-',$address)[0] }}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td>{{ explode('-',$address)[1] }}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td>Kode Pos {{ $unit->postal_code }} Telp. {{ $phone }}</td>
                </tr>
                @else
                @php
                $address = explode(';',$unit->address)[0];
                $phone = explode(';',$unit->phone_unit)[0];
                $i = 1;
                @endphp
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ explode('-',$address)[0] }}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td>{{ explode('-',$address)[1] }}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td>Kode Pos {{ $unit->postal_code }}</td>
                </tr>
                @foreach(explode('-',$phone) as $p)
                <tr>
                    @if($i == 1)
                    <td>Telepon</td>
                    <td>:</td>
                    <td>{{ $p }}</td>
                    @else
                    <td colspan="2">&nbsp;</td>
                    <td>{{ $p }}</td>
                    @endif
                    @php $i++ @endphp
                </tr>
                @endforeach
                @endif
                @else
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ explode('-',$unit->address)[0] }}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td>{{ explode('-',$unit->address)[1] }}</td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td>Kode Pos {{ $unit->postal_code }} Telp. {{ $unit->phone_unit }}</td>
                </tr>
                @endif
                <tr>
                    <td>Satuan Pendidikan</td>
                    <td>:</td>
                    <td>{{ $unit->name }}</td>
                </tr>
                <tr>
                    <td>Kelurahan/Desa</td>
                    <td>:</td>
                    <td>{{ $unit->wilayah->name }}</td>
                </tr>
                <tr>
                    <td>Kecamatan</td>
                    <td>:</td>
                    <td>{{ $unit->wilayah->kecamatanName() }}</td>
                </tr>
                <tr>
                    <td>Kota/Kabupaten</td>
                    <td>:</td>
                    <td>{{ $unit->wilayah->kabupatenName() }}</td>
                </tr>
                <tr>
                    <td>Provinsi</td>
                    <td>:</td>
                    <td>{{ $unit->wilayah->provinsiName() }}</td>
                </tr>
                <tr>
                    <td>Website</td>
                    <td>:</td>
                    <td>{{ $unit->website }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>:</td>
                    <td>{{ $unit->email }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="page">
    <div class="subpage normal-margin">
        <div class="title-container">
            <p class="title">Keterangan tentang Diri Peserta Didik</p>
        </div>
        <div id="dataPesertaDidik" class="m-t-44">
            <table class="student-data">
                <tr>
                    <td>1.</td>
                    <td>Nama Peserta Didik (Lengkap)</td>
                    <td>:</td>
                    <td>{{ $siswa->identitas->student_name }}</td>
                </tr>
                @if($unit->name == 'TK')
                <tr>
                    <td>2.</td>
                    <td>NIPD</td>
                    <td>:</td>
                    <td>{{ $siswa->student_nis }}</td>
                </tr>
                @else
                <tr>
                    <td>2.</td>
                    <td>NIPD / NISN</td>
                    <td>:</td>
                    <td>{{ $siswa->student_nis }} / {{ $siswa->student_nisn }}</td>
                </tr>
                @endif
                <tr>
                    <td>3.</td>
                    <td>Tempat, Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{ $siswa->identitas->birth_place.', '.$siswa->identitas->birthDateId }}</td>
                </tr>
                <tr>
                    <td>4.</td>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ ucwords($siswa->identitas->jeniskelamin->name) }}</td>
                </tr>
                <tr>
                    <td>5.</td>
                    <td>Agama</td>
                    <td>:</td>
                    <td>{{ $siswa->identitas->agama->name }}</td>
                </tr>
                <tr>
                    <td>6.</td>
                    <td>Alamat Peserta Didik</td>
                    <td>:</td>
                    <td>{!! wordwrap($siswa->identitas->address . ', RT ' . sprintf('%03d',$siswa->identitas->rt) . ' RW ' . sprintf('%03d',$siswa->identitas->rw),73,"</td></tr><tr><td colspan=\"3\">&nbsp;</td><td>") !!}</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td>{{ $siswa->identitas->wilayah->name.', '.$siswa->identitas->wilayah->kecamatanName().', '.$siswa->identitas->wilayah->kabupatenName() }}</td>
                </tr>
                <tr>
                    <td>7.</td>
                    <td>Nama Orang Tua</td>
                    <td>:</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>a. Ayah</td>
                    <td>:</td>
                    <td>{{ $siswa->identitas->orangtua->father_name }}</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>b. Ibu</td>
                    <td>:</td>
                    <td>{{ $siswa->identitas->orangtua->mother_name }}</td>
                </tr>
                <tr>
                    <td>8.</td>
                    <td>Alamat Orang Tua</td>
                    <td>:</td>
                    <td>{!! wordwrap($siswa->identitas->orangtua->parent_address,73,"</td></tr><tr><td colspan=\"3\">&nbsp;</td><td>") !!}</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <div id="tanda-tangan-container" class="m-t-33">
            <div id="tandaTangan">
                <table class="tanda-tangan">
                    <tr>
                        <td rowspan="4">
                            <table class="pas-foto">
                                <tr>
                                    <td>
                                        Pas Foto<br>3x4 cm
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>Tangerang Selatan, {{ $siswa->join_date ? Date::parse($siswa->join_date)->format('j F Y') : Date::parse('Asia/Jakarta')->format('j F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Kepala Sekolah,</td>
                    </tr>
                    <tr>
                        <td class="ttd">&nbsp;</td>
                    </tr>
                    <tr>
                        @php $kepsek = $unit->pegawai()->where('position_id',1)->latest()->first(); @endphp
                        <td>{{ $kepsek ? $kepsek->name : 'Tidak ditemukan' }}</td>
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
@endsection