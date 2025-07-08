<!doctype html>
<html lang="en-US">

<head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <title>Informasi Tanggungan BMS</title>
    <meta name="description" content="Informasi Tagihan BMS AULIYA.">
    <style type="text/css">
        table.statement {
          border: 2px solid #8c237f;
          border-collapse: collapse;
        }
        table.statement tr td p {
          margin: 0 0 .5em 0;
        }
    </style>
</head>

<body marginheight="0" topmargin="0" marginwidth="0" style="margin: 0px; background-color: #f2f3f8;" leftmargin="0">
    <!--100% body table-->
    <table cellspacing="0" border="0" cellpadding="0" width="100%" bgcolor="#f2f3f8"
        style="@import url(https://fonts.googleapis.com/css?family=SourceSansPro:300,400,600,700|Open+Sans:300,400,600,700); font-family: 'Open Sans', sans-serif;">
        <tr>
            <td>
                <table style="background-color: #f2f3f8; max-width:670px;  margin:0 auto;" width="100%" border="0"
                    align="center" cellpadding="0" cellspacing="0">
                    <tbody>
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                          <a href="https://sekolahauliya.sch.id" title="logo" target="_blank">
                            <img width="96" src="{{ asset('img/logo/logo-vertical-96px.png') }}" title="logo" alt="logo">
                          </a>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <table width="95%" border="0" align="center" cellpadding="0" cellspacing="0"
                                style="max-width:670px;background:#fff; border-radius:10px;-webkit-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);-moz-box-shadow:0 6px 18px 0 rgba(0,0,0,.06);box-shadow:0 6px 18px 0 rgba(0,0,0,.06);">
                                <tr>
                                    <td style="padding:0 35px;">
                                        <div style="color:#455056; font-size:15px;line-height:24px; margin:2em 0;">
                                            <p>Assalamu’alaikum Ayah Bunda,</p>
                                            <p>Bersama ini kami informasikan Tanggungan BMS{{ $data->termin()->count() > 1 ? ' Berkala' : null }} ananda sebagai berikut:</p>
                                            <table class="statement" width="530" height="173" align="center" bgcolor="#FFFFFF" style="margin-top: 2em;margin-bottom: 2em;">
                                                <tbody>
                                                <tr style="border-bottom: 2px solid #8c237f">
                                                    <td bgcolor="#FFFFFF" colspan="3" align="center" valign="middle">
                                                        <b>RINCIAN TANGGUNGAN {{ strtoupper($bmsType) }}</b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="220" align="left" valign="middle" style="padding: 5px 10px 0 10px;">
                                                        <p>Nama{{ $siswa ? (' '.ucwords($siswa == 'calon' ? $siswa.' Siswa' : $siswa)) : null }}</p>
                                                    </td>
                                                    <td width="10" style="padding: 5px 0 0 0;"><p>:</p></td>
                                                    <td align="left" valign="middle" style="padding: 5px 10px 0 10px;">
                                                        <p><b>{{ strtoupper($siswa == 'calon' ? $data->siswa->student_name : $data->siswa->identitas->student_name) }}</b></p>
                                                    </td>
                                                </tr>
                                                @if($siswa == 'siswa')
                                                <tr>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>Kelas</p>
                                                    </td>
                                                    <td width="10"><p>:</p></td>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>{{ $kelas }}</p>
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>Nomor VA BMS</p>
                                                    </td>
                                                    <td width="10"><p>:</p></td>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>{{ $data->siswa->virtualAccount->bms_va }}</p>
                                                    </td>
                                                </tr>
                                                @php
                                                $tagihan = $totalRemain = null;
                                                @endphp
                                                @if($data->termin()->count() > 1)
                                                @foreach($data->termin()->{$siswa}()->with('tahunPelajaran:id,academic_year_start')->get()->sortBy('tahunPelajaran.academic_year_start') as $key => $t)
                                                <tr>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>Tanggungan BMS Berkala {{ $key+1 }}</p>
                                                    </td>
                                                    <td width="10"><p>:</p></td>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        @if($key == 0)
                                                    <p>{{ 'Rp. '.number_format($t->nominal+$data->register_nominal+$data->bms_deduction, 0, ',', '.').($t->remain+($data->register_nominal-$data->register_paid) < 1 ? ' (LUNAS)' : null) }}</p>
                                                        @else
                                                        <p>{{ 'Rp. '.$t->nominalWithSeparator.($t->remain < 1 ? ' (LUNAS)' : null) }}</p>
                                                        @endif
                                                        @php
                                                        if($t->academic_year_id == $tahunAktif->id && !$tagihan) $tagihan = $t->remainWithSeparator;
                                                        if($t->tahunPelajaran->academic_year_start <= $tahunAktif->academic_year_start){
                                                            if(!$totalRemain) $totalRemain = 0;
                                                            $totalRemain += $t->remain;
                                                            if($key == 0)
                                                                $totalRemain += $data->register_nominal-$data->register_paid;
                                                        }
                                                        @endphp
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @php
                                                if(!$tagihan)
                                                    $tagihan = $data->bmsRemainWithSeparator;
                                                if(!$totalRemain)
                                                    $totalRemain = $data->bms_remain;
                                                @endphp
                                                @else
                                                <tr>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>Tanggungan {{ $bmsType }} Bersih</p>
                                                    </td>
                                                    <td width="10"><p>:</p></td>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>Rp. {{ $data->bmsNominalWithSeparator }}</p>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>Tanggungan BMS yang sudah dibayarkan</p>
                                                    </td>
                                                    <td width="10"><p>:</p></td>
                                                    <td align="left" valign="middle" style="padding: 0 10px;">
                                                        <p>Rp. {{ $data->bmsPaidWithSeparator }}</p>
                                                    </td>
                                                </tr>
                                                @endif
                                                {{--<tr style="border-top: 2px solid #8c237f">
                                                    <td align="left" valign="middle" style="padding: 5px 10px 0 10px;">
                                                        <p>Total Tanggungan BMS</p>
                                                    </td>
                                                    <td width="10"><p>:</p></td>
                                                    <td align="left" valign="middle" style="padding: 5px 10px 0 10px;">
                                                        <p>Rp. {{ $data->bmsRemainWithSeparator }}</p>
                                                    </td>
                                                </tr>--}}
                                                <tr style="border-top: 2px solid #8c237f">
                                                    <td align="left" valign="middle" style="padding: 5px 10px 0 10px;">
                                                        <p>{{ $bmsType == 'BMS Tunai' ? 'Sisa ' : null }}Tanggungan {{ $bmsType }} yang harus dibayarkan</p>
                                                    </td>
                                                    <td width="10"><p>:</p></td>
                                                    <td align="left" valign="middle" style="padding: 5px 10px 0 10px;">
                                                        <p><b>Rp. {{ $totalRemain ?  number_format($totalRemain, 0, ',', '.') : $data->bmsRemainWithSeparator }}</b></p>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <p>{{ $bmsType == 'BMS Tunai' ? 'Berdasarkan catatan kami, pembayaran Sisa Tanggungan BMS ananda sebagaimana tersebut di atas, belum tercatat pada rekening Auliya per hari ini. ' : null }}Mohon Ayah Bunda segera melakukan pembayaran{{ $bmsType == 'BMS Berkala' ? ' Tanggungan '. $bmsType.' sebagaimana tersebut di atas' : null }} melalui VA BMS Ananda di Bank BSI, selambat-lambatnya sebelum penerimaan Rapor semester {{ $semesterAktif ? $semesterAktif->semesterNumber : '1' }} Tahun Pelajaran ini.</p>
                                            <p>Informasi lebih lanjut dapat menghubungi bagian Administrasi Keuangan {{ str_replace("Auliya","AULIYA",$unit->desc) }} di nomor {!! ($unitPhone ? $unitPhone." (via <i>Telephone</i>)".($financeAdmin ? " atau " : null) : null).($financeAdmin ? $financeAdmin->phoneNumberWithDashId." (via <i>WhatsApp</i>)." : ($unitPhone ? "." : null)) !!}</p>
                                            <p>Wassalamu’alaikum Warahmatullahi Wabarakatuh.</p>
                                            @if($hm)
                                            <p>Kepala {{ str_replace("Auliya","AULIYA",$unit->desc) }}</p>
                                            <p>ttd</p>
                                            <p><b>{{ $hm->name }}</b></p>
                                            @endif
                                            <p style="font-size:10px; font-style:italic; text-align:center; line-height: 1.3">Dokumen ini resmi diterbitkan melalui Sistem Informasi Sekolah Islam Terpadu AULIYA (SISTA).<br>
                                            Kebenaran dan keabsahan atas data yang ditampilkan dapat dipertanggungjawabkan.</p>
                                        </div>
                                    </td>
                                </tr>
                                @if($units && count($units) > 0)
                                <tr>
                                    <td style="background-color: #2c2f34; padding:0 35px; border-radius: 0 0 10px 10px;">
                                        <div style="color:#fff; font-size:15px; line-height:24px; margin: 0 0 1em 0; padding: 7px 0 14px 0;">
                                            <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                                                <tbody>
                                                    @php
                                                    $count = 1;
                                                    $unitLeft = count($units);
                                                    @endphp
                                                    @foreach($units as $unit)
                                                    @if($count == 1)
                                                    <tr>
                                                    @endif
                                                        <td width="{{ count($units) > 1 ? '50' : '100' }} %" style="vertical-align: top;">
                                                            <h4 style="font-size: 14px; margin-top: 10px; margin-bottom: 10px;">{{ $unit->short_desc }}</h4>
                                                            @php
                                                            $address = explode(";",$unit->address);
                                                            $phone_unit = explode(";",$unit->phone_unit);
                                                            $i = 0;
                                                            @endphp
                                                            @foreach($address as $a)
                                                            @if($i > 0)
                                                            @endif
                                                            @php
                                                            $line = explode("-",$a);
                                                            @endphp
                                                            @if(count($line) > 0)
                                                            @foreach($line as $l)
                                                            <p style="margin: {{ $loop->first ? '5px 0 0 0' : '0' }};"><span style="font-size: 8pt;">{{ $l }}</span></p>
                                                            @endforeach
                                                            @else
                                                            <p style="margin: 0;"><span style="font-size: 8pt;">{{ $a }}</span></p>
                                                            @endif
                                                            <p style="margin: 0;"><span style="font-size: 8pt;">{{ $unit->wilayah->name.', '.$unit->wilayah->kecamatanName() }}</span></p>
                                                            @php
                                                            $phone = explode("-",$phone_unit[$i]);
                                                            $j = 1;
                                                            $max = count($phone);
                                                            @endphp
                                                            @foreach($phone as $p)
                                                            <p style="margin: 0;">
                                                            @if($j == 1)
                                                            <span style="font-size: 8pt;">Telp : {{ $p }}
                                                            @else
                                                            <span style="font-size: 8pt; padding-left: 29px;">{{ $p }}
                                                            @endif
                                                            </span></p>
                                                            @php $j++ @endphp
                                                            @endforeach
                                                            @php $i++ @endphp
                                                            @endforeach
                                                            <p style="margin: 0;"><span style="font-size: 8pt;">Email : <a href="mailto:{{ $unit->email }}" style="color: #4bb463; text-decoration: none;">{{ $unit->email }}</span></p>
                                                        </td>
                                                    @if(($unitLeft > 1 && $count == 2) || ($unitLeft == 1))
                                                    </tr>
                                                    @endif
                                                    @php
                                                    $count++;
                                                    $unitLeft--;
                                                    if($count == 3) $count = 1;
                                                    @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:20px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align:center;">
                            <p style="font-size:14px; color:rgba(69, 80, 86, 0.7411764705882353); line-height:18px; margin:0 0 0;">&copy; <strong>www.sekolahauliya.sch.id</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:80px;">&nbsp;</td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <!--/100% body table-->
</body>

</html>