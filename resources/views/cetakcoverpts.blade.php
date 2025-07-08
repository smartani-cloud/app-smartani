<!DOCTYPE html>
<html>

<head>
    <title>Cetak Rapor - Sistem Informasi SIT Auliya</title>
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <style>
        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .subpage {
            padding: 1cm;
            border: 5px red solid;
            height: 257mm;
            outline: 2cm #FFEAEA solid;
        }

        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

            .page {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
        }
    </style>
</head>

<body style="color:black;" onload="window.print()">

    <div class="book">
        <div class="page">
            <div class="row">
                <div class="col-md-12 text-center">
                    <img src="{{asset('img/logo/logo-vertical.png')}}" style="width: 100px;height: auto;margin-left:auto; margin-right:auto;margin-top:50px;margin-bottom:50px;" />
                    <h2 style="text-align: center;">LAPORAN TENGAH SEMESTER<br />SEKOLAH MENENGAH ATAS ISLAM TERPADU AULIYA</h2>
                </div>
                <div class="col-3">&nbsp;</div>
                <div class="col-6" style="margin-top: 250px;">
                    <table width="100%">
                        <tr>
                            <td width="40%">Nama</td>
                            <td width="10%">:</td>
                            <td width="50%">Indra Kusuma</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="40%">NISN</td>
                            <td width="10%">:</td>
                            <td width="50%">123456</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="40%">Kelas</td>
                            <td width="10%">:</td>
                            <td width="50%">12 RPL</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="40%">Semester</td>
                            <td width="10%">:</td>
                            <td width="50%">Ganjil</td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td width="40%">Tahun Pelajaran</td>
                            <td width="10%">:</td>
                            <td width="50%">2020/2021</td>
                        </tr>
                    </table>
                </div>
                <div class="col-3">&nbsp;</div>
            </div>
        </div>
    </div>
</body>

</html>