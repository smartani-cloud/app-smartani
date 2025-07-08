@extends('template.main.psb.master')

@section('title')
{{$anak->student_nickname}}
@endsection

@section('headmeta')
  <link href="{{ asset('public/buttons.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.ortu.ortu')
@endsection

@section('content')

<div class="row mb-3">
    <!-- Pengisian Formulir -->
    <div class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pengisian Formulir</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Sudah</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengisian Formulir -->
    <div class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Biaya Observasi</div>
                    @if( $anak->status_id >= 2 )
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Lunas</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-alt fa-2x text-brand-green"></i>
                    </div>
                    @elseif( $anak->status_id == 2 )
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Proses</div>
                    </div>
                    <div class="col-auto">
                        <span style="color: Dodgerblue;">
                            <i class="fas fa-money-bill-alt fa-2x text-brand-blue"></i>
                        </span>
                    </div>
                    @else
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Menunggu</div>
                    </div>
                    <div class="col-auto">
                        <span style="color: Tomato;">
                            <i class="fas fa-comments fa-2x text-brand-red"></i>
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pengisian Formulir -->
    <div class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Observasi</div>
                    @if( $anak->status_id > 3 )
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Sudah</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-brand-green"></i>
                    </div>
                    @elseif( $anak->status_id == 3 )
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Proses</div>
                    </div>
                    <div class="col-auto">
                        <span style="color: Dodgerblue;">
                            <i class="fas fa-comments fa-2x text-brand-blue"></i>
                        </span>
                    </div>
                    @else
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Menunggu</div>
                    </div>
                    <div class="col-auto">
                        <span style="color: Tomato;">
                            <i class="fas fa-comments fa-2x text-brand-red"></i>
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pengisian Formulir -->
    <div class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pengumuman</div>
                    @if( $anak->status_id >= 4 )
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Diterima</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bullhorn fa-2x text-brand-green"></i>
                    </div>
                    @elseif( $anak->status_id == 4 )
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Proses</div>
                    </div>
                    <div class="col-auto">
                        <span style="color: Dodgerblue;">
                            <i class="fas fa-bullhorn fa-2x text-brand-blue"></i>
                        </span>
                    </div>
                    @else
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Menunggu</div>
                    </div>
                    <div class="col-auto">
                        <span style="color: Tomato;">
                            <i class="fas fa-bullhorn fa-2x text-brand-red"></i>
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pengisian Formulir -->
    <div class="col-xl-2 col-md-3 mb-2">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Bayar Daftar Ulang</div>
                    @if( $anak->status_id > 4 )
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Lunas</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-alt fa-2x text-brand-green"></i>
                    </div>
                    @elseif( $anak->status_id == 4 )

                    @if ($anak->bms->register_remain > 0)
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Proses</div>
                    </div>
                    <div class="col-auto">
                        <span style="color: Dodgerblue;">
                            <i class="fas fa-money-bill-alt fa-2x text-brand-blue"></i>
                        </span>
                    </div>
                    @else
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Lunas</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-alt fa-2x text-brand-green"></i>
                    </div>
                    @endif
                    @else
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Menunggu</div>
                    </div>
                    <div class="col-auto">
                        <span style="color: Tomato;">
                            <i class="fas fa-money-bill-alt fa-2x text-brand-red"></i>
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3">
                            Assalamu'alaikum Warahmatullahi Wabarakatuh,
                            <br>

                        @if($anak->status_id == 1)
                            Selamat Datang Ananda {{$anak->student_name}}. <br>
                            @if($anak->interview_type)
                                Terima kasih telah menunggu informasi jadwal Wawancara & Observasi <br>
                                Berikut kami informasikan <br>
                                Jadwal Wawancara & Observasi: {{date('d M Y', strtotime($anak->interview_date))}} {{$anak->interview_time}}   <br>
                                Bentuk Wawancara & Observasi: {{$anak->interview_type==1?'Online':'Offline'}}  <a href="{{$anak->interview_type==1?$anak->link:''}}"> <strong>{{$anak->interview_type==1?$anak->link:''}}</strong> </a><br><br>
                                Untuk keperluan Wawancara & Observasi dapat melakukan pembayaran Observasi/Psikotes sebesar Rp 500.000 ke {{ $anak->bank ? $anak->bank->name : 'Bank Digiyok (BD)' }} dengan nomor rekening {{ $anak->account_number ? $anak->account_number : $anak->unit->va_number }}{{ $anak->account_holder ? ' a.n. '.$anak->account_holder : 'Sekolah Digiyok' }}<br>dan bukti transfer dikirimkan melalui WhatsApp di bawah. Serta mengisi form untuk upload file foto/scan dokumen melalui <a href="{{$anak->unit->psb_document_link}}">Form Upload Dokumen</a> :<br>
                                1. Foto/Scan Akte Kelahiran Anak <br>
                                2. Foto/Scan Kartu Keluarga <br>
                                3. Foto/Scan KTP Ayah/Ibu atau Wali <br>
                                @if ($anak->unit_id == 3 || $anak->unit_id == 4)
                                4. Foto/Scan Rapot dan Ijazah Terakhir<br>
                                @endif
                            @else
                            Terima kasih telah melakukan pendaftaran online Penerimaan Siswa Baru di {{$anak->unit->name}} Digiyok. {{$anak->tahunAjaran->academic_year}}. <br>
                            Selanjutnya, mohon menunggu informasi untuk jadwal Wawancara & Observasi. <br><br>
                            Adapun dokumen yang harus disiapkan untuk keperluan Wawancara & Observasi adalah sebagai berikut : <br>
                            1. Foto/Scan Akte Kelahiran Anak <br>
                            2. Foto/Scan Kartu Keluarga <br>
                            3. Foto/Scan KTP Ayah/Ibu atau Wali <br>
                            @if ($anak->unit_id == 3 || $anak->unit_id == 4)
                            4. Foto/Scan Rapot dan Ijazah Terakhir<br>
                            @endif
                            @endif
                            <br>
                            <b>Catatan: Bagi Siswa Digiyok yang melanjutkan ke jenjang berikutnya, tidak mengikuti observasi/psikotes dan tidak dikenakan biayanya.</b><br>
                            <br>Untuk Informasi lebih lanjut dapat menghubungi Panitia Penerimaan Siswa Baru {{$anak->unit->name}} Sekolah Digiyok. <br>
                            <br>
                            @if($anak->unit->whatsapp_unit && (substr($anak->unit->whatsapp_unit, 0, 2) == "62" || substr($anak->unit->whatsapp_unit, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($anak->unit->whatsapp_unit, 0, 2) == "62" ? $anak->unit->whatsapp_unit : ('62'.substr($anak->unit->whatsapp_unit, 1)) }}&text=Assalamu'alaikum" class="btn btn-sm btn-success mr-1" target="_blank"><i class="fab fa-whatsapp mr-2"></i>Chat via WA</a>@endif Telp {{$anak->unit->phone_unit}} <br>
                        @elseif($anak->status_id == 2)
                            Selamat Datang Ananda {{$anak->student_name}}. <br>
                            Terima kasih telah melakukan pembayaran Observasi di {{$anak->unit->name}} Sekolah Digiyok TA. {{$anak->tahunAjaran->academic_year}} <br><br>
                            Berikut kami mengingatkan kembali <br>
                            Jadwal Wawancara & Observasi: {{date('d M Y', strtotime($anak->interview_date))}} {{$anak->interview_time}}   <br>
                            Bentuk Wawancara & Observasi: {{$anak->interview_type==1?'Online':'Offline'}}  <a href="{{$anak->interview_type==1?$anak->link:''}}"> <strong>{{$anak->interview_type==1?$anak->link:''}}</strong> </a><br><br>
                            
                            Diingatkan dokumen yang harus diisi atau diupload melalui <a href="{{$anak->unit->psb_document_link}}">Form Upload Dokumen</a> sebelum Wawancara & Observasi adalah sebagai berikut : <br>
                            1. Foto/Scan Akte Kelahiran Anak <br>
                            2. Foto/Scan Kartu Keluarga <br>
                            3. Foto/Scan KTP Ayah/Ibu atau Wali <br>
                            @if ($anak->unit_id == 3 || $anak->unit_id == 4)
                            4. Foto/Scan Rapot dan Ijazah Terakhir<br>
                            @endif
                            <br>
                            <b>Catatan: Bagi Siswa Digiyok yang melanjutkan ke jenjang berikutnya, tidak mengikuti observasi/psikotes dan tidak dikenakan biayanya.</b><br>
                            <br>Untuk Informasi lebih lanjut dapat menghubungi Panitia Penerimaan Siswa Baru {{$anak->unit->name}} Sekolah Digiyok. <br>
                            <br>
                            @if($anak->unit->whatsapp_unit && (substr($anak->unit->whatsapp_unit, 0, 2) == "62" || substr($anak->unit->whatsapp_unit, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($anak->unit->whatsapp_unit, 0, 2) == "62" ? $anak->unit->whatsapp_unit : ('62'.substr($anak->unit->whatsapp_unit, 1)) }}&text=Assalamu'alaikum" class="btn btn-sm btn-success mr-1" target="_blank"><i class="fab fa-whatsapp mr-2"></i>Chat via WA</a>@endif Telp {{$anak->unit->phone_unit}} <br>
                       
                        @elseif($anak->status_id == 3)
                        Terima kasih ananda {{$anak->student_name}} telah mengikuti Wawancara dan Observasi Penerimaan Siswa Baru di {{$anak->unit->name}} Sekolah Digiyok TA. {{$anak->tahunAjaran->academic_year}} <br>
                        Selanjutnya, mohon menunggu pengumuman hasil Wawancara dan Observasi. <br>
                        <br>Untuk Informasi lebih lanjut dapat menghubungi Panitia Penerimaan Siswa Baru {{$anak->unit->name}} Sekolah Digiyok. <br>
                        <br>
                        @if($anak->unit->whatsapp_unit && (substr($anak->unit->whatsapp_unit, 0, 2) == "62" || substr($anak->unit->whatsapp_unit, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($anak->unit->whatsapp_unit, 0, 2) == "62" ? $anak->unit->whatsapp_unit : ('62'.substr($anak->unit->whatsapp_unit, 1)) }}&text=Assalamu'alaikum" class="btn btn-sm btn-success mr-1" target="_blank"><i class="fab fa-whatsapp mr-2"></i>Chat via WA</a>@endif Telp {{$anak->unit->phone_unit}} <br>
                        
                        @elseif($anak->status_id == 4)
                        @if($anak->bms->register_remain > 0)
                        Selamat kepada ananda {{$anak->student_name}} telah berhasil lulus Wawancara & Observasi Penerimaan Siswa Baru di {{$anak->unit->name}} Sekolah Digiyok TA. {{$anak->tahunAjaran->academic_year}} <br>
                        <br>
                        Berdasarkan Wawancara Keuangan pada tanggal {{ $anak->interview_date ? $anak->interviewDateId : '-' }}, diinformasikan bahwa :<br>
                            @php
                            $nominal = $anak->bms->tipe->nominal()->select('bms_nominal')->where('unit_id',$anak->unit_id)->first();
                            $deduction = $anak->bms->bms_deduction ? $anak->bms->bms_deduction : '0';
                            @endphp
                            Nominal BMS {{ $anak->bms->tipe->bms_type }} Bersih : Rp {{ number_format($nominal ? ($nominal->bms_nominal-$deduction) : 0,0,",",".") }}<br>
                            @if($anak->bms->termin()->count() > 1)
                            @foreach($anak->bms->termin as $key => $t)
                            @if($key > 0)
                            Nominal BMS berkala {{ $key+1 }} : Rp {{ number_format($t ? $t->nominal : 0,0,",",".") }}<br>
                            @endif
                            @endforeach
                            @endif
                         <br>Selanjutnya Ayah Bunda dapat melakukan pembayaran Daftar Ulang dengan rincian sebagai berikut :<br>
                            Nominal Daftar Ulang : Rp {{number_format($anak->bms->register_nominal,0,",",".")}}<br>
                            Nomor Virtual Account BD : @if($anak->virtualAccount->bms_va)<span id="vaNumber" class="font-weight-bold mr-2">{{$anak->virtualAccount->bms_va}}</span><a href="javascript:void(0)" id="btnCopy" class="text-dark text-decoration-none" onclick="CopyToClipboard('vaNumber');return false;" onmouseout="outButton()"><i class="far fa-clone mr-1"></i>Salin</a>@endif<br>
                         Pembayaran dilakukan paling lambat tanggal <b>{{ $anak->paymentDeadlineDateId }}</b><br>
                         <!--Link panduan pembayaran VA BD: <a href="https://bit.ly/Panduan_PembayaranBMS_SPP">Panduan Pembayaran</a> <br>-->
                        <br>
                        Adapun nominal sisa BMS telah tercantum dalam surat komitmen keuangan.
                        <br>
                        <br>Untuk Informasi lebih lanjut dapat menghubungi Panitia Penerimaan Siswa Baru {{$anak->unit->name}} Sekolah Digiyok. <br>
                        Terima kasih sudah mendaftar di {{$anak->unit->name}} Sekolah Digiyok. <br>
                        <br>
                        @if($anak->unit->whatsapp_unit && (substr($anak->unit->whatsapp_unit, 0, 2) == "62" || substr($anak->unit->whatsapp_unit, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($anak->unit->whatsapp_unit, 0, 2) == "62" ? $anak->unit->whatsapp_unit : ('62'.substr($anak->unit->whatsapp_unit, 1)) }}&text=Assalamu'alaikum" class="btn btn-sm btn-success mr-1" target="_blank"><i class="fab fa-whatsapp mr-2"></i>Chat via WA</a>@endif Telp {{$anak->unit->phone_unit}} <br>
                        @else
                        Terima kasih atas pelunasan pembayaran daftar ulang ananda {{$anak->student_name}}. <br>
                        <br>
                        Kami ucapkan selamat bergabung di {{$anak->unit->name}} Sekolah Digiyok TA. {{$anak->tahunAjaran->academic_year}} <br>
                        Untuk kemudahan informasi antar orangtua dan sekolah, nomor handphone Ayah/Bunda akan dimasukkan dalam WA group siswa baru.<br>Mohon informasi apabila ada perubahan pada nomor handphone yang tercantum dalam formulir pendaftaran. <br>
                        <br>
                        Adapun nominal sisa BMS yang tercantum dalam surat komitmen keuangan dapat segera dilunasi.
                        <br>
                        <br>Untuk Informasi lebih lanjut dapat menghubungi Panitia Penerimaan Siswa Baru {{$anak->unit->name}} Sekolah Digiyok. <br>
                        <br>
                        @if($anak->unit->whatsapp_unit && (substr($anak->unit->whatsapp_unit, 0, 2) == "62" || substr($anak->unit->whatsapp_unit, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($anak->unit->whatsapp_unit, 0, 2) == "62" ? $anak->unit->whatsapp_unit : ('62'.substr($anak->unit->whatsapp_unit, 1)) }}&text=Assalamu'alaikum" class="btn btn-sm btn-success mr-1" target="_blank"><i class="fab fa-whatsapp mr-2"></i>Chat via WA</a>@endif Telp {{$anak->unit->phone_unit}} <br>
                        @endif
                       
                        @elseif($anak->status_id == 5)
                        Terima kasih atas pelunasan pembayaran daftar ulang ananda {{$anak->student_name}}. <br>
                        <br>
                        Kami ucapkan selamat bergabung di {{$anak->unit->name}} Sekolah Digiyok TA. {{$anak->tahunAjaran->academic_year}} <br>
                        Untuk kemudahan informasi antar orangtua dan sekolah, nomor handphone Ayah/Bunda akan dimasukkan dalam WA group siswa baru.<br>Mohon informasi apabila ada perubahan pada nomor handphone yang tercantum dalam formulir pendaftaran. <br>
                        <br>
                        Adapun nominal sisa BMS yang tercantum dalam surat komitmen keuangan dapat segera dilunasi.
                        <br>
                        <br>Untuk Informasi lebih lanjut dapat menghubungi Panitia Penerimaan Siswa Baru {{$anak->unit->name}} Sekolah Digiyok. <br>
                        <br>
                        @if($anak->unit->whatsapp_unit && (substr($anak->unit->whatsapp_unit, 0, 2) == "62" || substr($anak->unit->whatsapp_unit, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($anak->unit->whatsapp_unit, 0, 2) == "62" ? $anak->unit->whatsapp_unit : ('62'.substr($anak->unit->whatsapp_unit, 1)) }}&text=Assalamu'alaikum" class="btn btn-sm btn-success mr-1" target="_blank"><i class="fab fa-whatsapp mr-2"></i>Chat via WA</a>@endif Telp {{$anak->unit->phone_unit}} <br>
                      
                        @elseif($anak->status_id == 6)
                            Mohon maaf Ananda <strong>{{$anak->student_name}}</strong> saat ini sedang masuk kursi cadangan di {{$anak->unit->name}} Sekolah Digiyok. <br>
                        @elseif($anak->status_id == 7)
                        @elseif($anak->status_id == 8)
                        @endif
                        <br>
                        Terima Kasih. <br>
                        Wassalamu'alaikum Warahmatullahi Wabarakatuh,
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Row-->
@endsection

@section('footjs')
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/jszip.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/pdfmake.min.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/vfs_fonts.js') }}"></script>
<script src="{{ asset('vendor/datatablestambahan/buttons.html5.min.js') }}"></script>

<!-- Page level custom scripts -->
<script>
function CopyToClipboard(id){
    var r = document.createRange();
    r.selectNode(document.getElementById(id));
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(r);
    document.execCommand('copy');
    window.getSelection().removeAllRanges();
    
    var btn_copy = document.getElementById("btnCopy");
    btn_copy.classList.remove('text-dark');
    btn_copy.classList.add('text-success');
    btn_copy.innerHTML='Berhasil Tersalin!<i class="fa fa-check ml-1"></i>';
}
function outButton() {
    var btn_copy = document.getElementById("btnCopy");
    btn_copy.classList.remove('text-success');
    btn_copy.classList.add('text-dark');
    btn_copy.innerHTML='<i class="far fa-clone mr-1"></i>Salin';
}
</script>
@endsection