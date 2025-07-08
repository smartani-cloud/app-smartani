@extends('template.main.master')

@section('title')
{{$title}}
@endsection

@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
    <h1 class="h3 mb-0 text-gray-800">Calon Siswa {{$title}}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Psb</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Calon Siswa</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                    <form action="/{{Request::path()}}" method="GET">
                    {{-- <form action="/kependidikan/psb/{{$link}}" method="POST"> --}}
                    @csrf
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-5">
                                <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">Semua</option>
                                    <?php 
                                        $levels = \App\Http\Services\Kbm\KelasSelector::listKelas();
                                    ?>
                                    @foreach( $levels as $tingkat)
                                        <option value="{{$tingkat->level}}" {{$request->level==$tingkat->level?'selected':''}}>{{$tingkat->level}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tahun Ajaran</label>
                            <div class="col-sm-5">
                                <select name="year" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="">Semua</option>
                                    <?php 
                                        $years = \App\Http\Services\Kbm\AcademicYearSelector::activeToNext();
                                    ?>
                                    @foreach ($years as $year)
                                        <option value="{{$year->academic_year_start}}" {{$request->year==$year->academic_year_start?'selected':''}}>{{$year->academic_year}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-brand-green-dark btn-sm" type="submit">Saring</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green-dark">Calon Siswa {{$title}}</h6>
        @if(in_array(Auth::user()->role->name,['ctl']) || (in_array(Auth::user()->role->name,['sek']) && in_array($status_id,[1,2,3,4])))
        <form action="/{{Request::path().'/ekspor'}}" method="get">
            <input type="hidden" name="level" value="{{ $request->level }}">
            <input type="hidden" name="year" value="{{ $request->year }}">
            <button type="submit" class="m-0 float-right btn btn-brand-green-dark btn-sm">Ekspor<i class="fas fa-file-export ml-2"></i></button>
        </form>
        @endif
      </div>
      <div class="card-body">
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sukses!</strong> {{ Session::get('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ Session::get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif
        <div class="table-responsive">
            <table id="dataTable" class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>No Pendaftaran</th>
                        <th>Tanggal Daftar</th>
                        @if(in_array($status_id,[1,2,3,4]))
                        <th>Baru/Pindahan</th>
                        <th>Tahun Pelajaran</th>
                        @endif
                        <th>Program</th>
                        <th>Tingkat Kelas</th>
                        <th>Nama</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Info Asal Sekolah</th>
                        <th>Asal Informasi</th>
                        @if ($title == 'Formulir Terisi')
                        <th>Wawancara dan Observasi</th>
                        @endif
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach( $calons as $index => $calon )
                    <tr>
                        <td>{{$calon->reg_number}}</td>
                        <td>{{ date('d-m-Y', strtotime($calon->created_at)) }}</td>
                        @if(in_array($status_id,[1,2,3,4]))
                        <td>{{$calon->statusSiswa ? $calon->statusSiswa->status : '-'}}</td>
                        <td>{{$calon->semester ? $calon->semester->semester_id : '-'}}</td>
                        @endif
                        <td>{{$calon->unit->name}}</td>
                        <td>{{$calon->level->level}}</td>
                        <td>{{$calon->student_name}}</td>
                        <td @if(!$calon->isBirthDateValid) class="text-danger" @endif>{{ date('d-m-Y', strtotime($calon->birth_date)) }}</td>
                        <td>{{ucwords($calon->jeniskelamin->name)}}</td>
                        <td>{{ucwords($calon->origin_school)}}</td>
                        <td>{{ucwords($calon->info_from)}}</td>
                        @if ($title == 'Formulir Terisi')
                        <td>
                            {{$calon->interview_type==2?'Offline':$calon->link}} <br>{{$calon->interview_date}}  {{$calon->interview_time}}
                        </td>
                        @endif
                        <td>
                            <a href="/kependidikan/psb/calon/lihat/{{$calon->id}}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                            @if($status_id == 1 )
                            @if(in_array(Auth::user()->role->name,['sek']))
                            <a href="#" class="btn btn-sm btn-info" data-toggle="modal" data-target="#WawancaraLink" data-link="{{$calon->link}}" data-id="{{$calon->id}}"data-name="{{$calon->student_name}}" data-link="{{$calon->link}}" data-interview_date="{{$calon->interview_date}}" data-interview_time="{{$calon->interview_time}}" {{-- data-bank="{{$calon->bank_id ? $calon->bank_id : '13'}}" --}} data-account_number="{{$calon->account_number ? $calon->account_number : $calon->unit->va_number}}" {{-- data-account_holder="{{$calon->account_holder}}"--}}
                            >
                                <i class="fa fa-info-circle"></i>
                            </a>
                            @endif
                            @if(!in_array(auth()->user()->role->name,['am','aspv','cspv','lay']))
                            @php
                            if($calon->interview_date || $calon->interview_time){
                                $whatsAppText = rawurlencode("Assalamu'alaikum Ayah Bunda Ananda ".$calon->student_name.". Terima kasih sudah menunggu informasi jadwal Wawancara dan Observasi. Ayah Bunda dapat mengisi form upload file foto/scan dokumen terkait pada link: ".$calon->unit->psb_document_link." dan melakukan pembayaran biaya Observasi/Psikotes. Adapun jadwal Wawancara dan Observasi. Ayah Bunda dapat mengakses Aplikasi MUDA melalui link: ".route('psb.index').". Catatan: Bagi Siswa MUDA yang melanjutkan ke jenjang berikutnya, tidak mengikuti observasi/psikotes dan tidak dikenakan biayanya.");
                            }
                            else{
                                $whatsAppText = rawurlencode("Assalamu’alaikum Ayah Bunda Ananda ".$calon->student_name.". Terima kasih sudah mendaftar di ".$calon->unit->islamic_name." MUDA. Sementara menunggu informasi jadwal Wawancara dan Observasi, Ayah Bunda dapat menyiapkan sejumlah dokumen. Untuk informasi/proses lebih lanjut silakan mengakses Aplikasi MUDA melalui link: ".route('psb.index').". Catatan: Bagi Siswa MUDA yang melanjutkan ke jenjang berikutnya, tidak mengikuti observasi/psikotes dan tidak dikenakan biayanya.");
                            }
                            @endphp
                            @if($calon->orangtua->mother_phone && (substr($calon->orangtua->mother_phone, 0, 2) == "62" || substr($calon->orangtua->mother_phone, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($calon->orangtua->mother_phone, 0, 2) == "62" ? $calon->orangtua->mother_phone : ('62'.substr($calon->orangtua->mother_phone, 1)) }}&text={{ $whatsAppText }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-bell"></i></a>@endif
                            @if(in_array(Auth::user()->role->name,['keu']))
                            @if($calon->isBirthDateValid)
                            <a href="#" class="btn btn-sm btn-success" data-name="{{$calon->student_name}}" data-toggle="modal" data-target="#SavingSeatModal" data-id="{{$calon->id}}"><i class="fas fa-check"></i></a>
                            @else
                            <button type="button" class="btn btn-sm btn-secondary" disabled><i class="fas fa-check"></i></button>
                            @endif
                            @endif
                            @endif
                            @elseif($status_id == 2)
                            @if(!in_array(auth()->user()->role->name,['am','aspv','cspv','lay']))
                            @if($calon->interview_date || $calon->interview_time)
                            @php
                            $whatsAppText = rawurlencode("Assalamu'alaikum Ayah Bunda ananda ".$calon->student_name.". Terima kasih telah melakukan pembayaran biaya Observasi/Psikotes. Ayah Bunda dapat mengisi form upload file foto/scan dokumen pada link: ".$calon->unit->psb_document_link." untuk keperluan Wawancara dan Observasi dengan jadwal tercantum dalam Aplikasi MUDA pada link: ".route('psb.index').". Catatan: Bagi Siswa MUDA yang melanjutkan ke jenjang berikutnya, tidak mengikuti observasi/psikotes dan tidak dikenakan biayanya.");
                            @endphp
                            @if($calon->orangtua->mother_phone && (substr($calon->orangtua->mother_phone, 0, 2) == "62" || substr($calon->orangtua->mother_phone, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($calon->orangtua->mother_phone, 0, 2) == "62" ? $calon->orangtua->mother_phone : ('62'.substr($calon->orangtua->mother_phone, 1)) }}&text={{ $whatsAppText }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-bell"></i></a>@endif
                            @endif
                            @if($calon->semester_id)
                            @if(in_array(Auth::user()->role->name,['keu']))
                            <a href="#" class="btn btn-sm btn-success" data-name="{{$calon->student_name}}" data-toggle="modal" data-target="#WawancaraDone" data-id="{{$calon->id}}" data-unit="{{$calon->unit_id}}">
                                <i class="fas fa-check"></i>
                            </a>
                            @endif
                            @endif
                            @endif
                            @elseif($status_id == 3)
                            @if(!in_array(auth()->user()->role->name,['am','aspv','cspv','lay']))
                            <!-- <a href="{{ route('kependidikan.psb.komitmen',['id' => $calon->id]) }}" class="btn btn-sm btn-brand-green" target="_blank"><i class="fas fa-print"></i></a> -->
                            @php
                            $whatsAppText = rawurlencode("Assalamu'alaikum Ayah Bunda Ananda ".$calon->student_name.". Terima kasih kepada Ananda yang telah mengikuti Wawancara dan Observasi. Untuk informasi lebih lanjut, Ayah Bunda silakan mengakses Aplikasi MUDA melalui link: ".route('psb.index'));
                            @endphp
                            @if($calon->orangtua->mother_phone && (substr($calon->orangtua->mother_phone, 0, 2) == "62" || substr($calon->orangtua->mother_phone, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($calon->orangtua->mother_phone, 0, 2) == "62" ? $calon->orangtua->mother_phone : ('62'.substr($calon->orangtua->mother_phone, 1)) }}&text={{ $whatsAppText }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-bell"></i></a>@endif
                            @endif
                            @if(in_array(Auth::user()->role->name,['sek']))
                            <a href="#" class="btn btn-sm btn-success"   data-name="{{$calon->student_name}}" data-toggle="modal" data-target="#DiterimaModal" data-id="{{$calon->id}}"><i class="fas fa-check"></i></a>
                            <a href="#" class="btn btn-sm btn-danger"   data-name="{{$calon->student_name}}" data-toggle="modal" data-target="#DicadangkanModal" data-id="{{$calon->id}}"><i class="fas fa-times"></i></a>
                            @endif
                            @elseif($status_id == 4)

                            @elseif($status_id == 5)
                            @if( in_array((auth()->user()->role_id), array(20)))
                                <a href="#" class="btn btn-sm btn-success"   data-name="{{$calon->student_name}}" data-toggle="modal" data-target="#ResmikanModal" data-id="{{$calon->id}}"><i class="fas fa-check"></i></a>
                                <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#BatalBayarModal" data-id="{{$calon->id}}"><i class="fas fa-trash"></i></a>
                            @endif
                            @if(in_array((auth()->user()->role->name), ['fam','faspv']))
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#awalSppModal" data-id="{{$calon->id}}" data-name="{{$calon->student_name}}" data-year="{{$calon->year_spp}}" data-month="{{$calon->month_spp}}"><i class="fas fa-calendar-alt"></i></a>
                            @endif
                            @elseif($status_id == 6)
                            @if(in_array(Auth::user()->role->name,['sek']))
                            <a href="#" class="btn btn-sm btn-success" data-name="{{$calon->student_name}}" data-toggle="modal" data-target="#DiterimaModal" data-id="{{$calon->id}}"><i class="fas fa-check"></i></a>
                            @endif
                            @elseif($status_id == 7)
                            @if(in_array(Auth::user()->role->name,['ctl']))
                                <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#revertBatalBayarModal" data-id="{{$calon->id}}"><i class="fas fa-trash"></i></a>
                            @endif
                            @endif
                            @if (in_array($status_id,[1,2]))
                            @if(!in_array(auth()->user()->role->name,['am','aspv','cspv','lay']))
                                <a href="#" class="btn btn-sm btn-danger"  data-name="{{$calon->student_name}}" data-toggle="modal" data-target="#HapusModal" data-id="{{$calon->id}}"><i class="fas fa-trash"></i></a>
                            @endif
                            @endif
                            @if($status_id >= 3)
                                @if(in_array(auth()->user()->role->name,['fam']))
                                    <a href="#" class="btn btn-sm btn-success" data-du="{{number_format($calon->bms->register_nominal,0,',','.')}}" data-name="{{$calon->student_name}}" data-toggle="modal" data-target="#ubahBms" data-id="{{$calon->id}}" data-unit="{{$calon->unit_id}}">
                                        <i class="far fa-edit"></i>
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

@if( in_array((auth()->user()->role_id), array(20)))
<!-- Modal Resmikan -->
<div id="ResmikanModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xe5ca;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('kependidikan.psb.resmikan')}}" method="POST">
            @csrf
            <div class="modal-body" id="form_penerimaan" style="display:block">
                <br>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="text" name="id" id="id" class="id" hidden/>
                <button type="submit" class="btn btn-success">Ya</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endif

@if(in_array((auth()->user()->role->name), ['fam','faspv']))
<!-- Modal Konfirmasi -->
<div id="awalSppModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <h4 class="modal-title w-100">Ubah Awal Mula SPP</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('kependidikan.psb.ubah-awal-spp')}}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="modal-body" id="form_penerimaan" style="display:block">
                    <div class="form-group">
                      <label for="year_spp" class="col-form-label">Nama</label>
                      <input type="text" name="name" class="form-control" value="-" disabled>
                    </div>
                    <div class="form-group">
                      <label for="year_spp" class="col-form-label">Tahun Mulai SPP</label>
                      <input type="number" name="year_spp" class="form-control" id="year_spp" value="{{date('Y')}}" min="2000" required>
                    </div>
                    <div class="form-group">
                        <label for="month_spp" class="col-form-label">Bulan Mulai SPP</label>
                        <select name="month_spp" class="select2 form-control select2-hidden-accessible auto_width" id="month_spp" style="width:100%;" tabindex="-1" aria-hidden="true" required>
                            <option value="1" selected>Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <input type="hidden" name="id" id="id" class="id"/>
                    <button type="submit" class="btn btn-success">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($status_id == 2)
<!-- Modal Diterima -->
<div id="WawancaraDone" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column" id="form_yakin" style="display:none">
                <div class="icon-box">
                    <i class="material-icons">&#xe5ca;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-header flex-column" id="form_title" style="display:block">
                <h4 class="modal-title w-100">Wawancara Keuangan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('kependidikan.psb.diterimaDone')}}" method="POST">
            @csrf
            <input type="hidden" name="unit_bms">
            <div class="modal-body" id="form_penerimaan" style="display:block">
                <div class="form-group">
                    <label for="type_pembayaran" class="col-form-label">Tipe BMS</label>
                    <select name="type_pembayaran" class="select2 form-control select2-hidden-accessible auto_width" id="type_pembayaran" style="width:100%;" tabindex="-1" aria-hidden="true">
                        <option value="1" selected>Tunai</option>
                        <option value="2">Berkala</option>
                    </select>
                </div>
                <div class="form-group">
                  <label for="bms_total" class="col-form-label">BMS Tunai</label>
                  <input type="text" readonly name="bms_total" class="form-control number-separator" id="bms_total" value="0" required>
                </div>
                <div class="form-group">
                  <label for="bms_potongan" class="col-form-label">Potongan BMS</label>

                  <input type="hidden" name="bms_potongan" id="bms_potongan" value="0" required>
                  <select name="potongan" class="form-control" name="potongan" required="required"/>
                    <option value="0">== Pilih ==</option>
                    @foreach($deductions as $d)
                    <option value="{{ $d->isPercentage ? $d->percentage : $d->nominal }}" data-is-percentage="{{ $d->isPercentage ? 1 : 0 }}">{{ $d->isPercentage ? $d->nameWithPercentage : $d->nameWithNominal }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="bms_bersih" class="col-form-label">BMS Bersih</label>
                  <input type="text" name="bms_bersih" class="form-control number-separator" id="bms_bersih" value="0" readonly required>
                </div>
                <div class="form-group">
                  <label for="bms_daftar_ulang" class="col-form-label">Daftar Ulang</label>
                  <input type="text" name="bms_daftar_ulang" class="form-control number-separator" id="bms_daftar_ulang" value="0" required>
                </div>
                <div class="form-group">
                  <label for="bms_sisa_bms" class="col-form-label">Sisa BMS</label>
                  <input type="text" name="bms_sisa_bms[]" class="form-control number-separator" id="bms_sisa_bms" value="0" readonly required>
                </div>
                <div class="form-group bms-berkala-2" style="display: none">
                  <label for="bms_berkala_2" class="col-form-label">BMS Berkala 2</label>
                  <input type="text" name="bms_sisa_bms[]" class="form-control number-separator" id="bms_berkala_2" value="0" readonly required>
                </div>
                <div class="form-group bms-berkala-3" style="display: none">
                  <label for="bms_berkala_3" class="col-form-label">BMS Berkala 3</label>
                  <input type="text" name="bms_sisa_bms[]" class="form-control number-separator" id="bms_berkala_3" value="0" readonly required>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id="yakin_button" style="display:none">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="text" name="id" id="id" class="id" hidden/>
                <button type="submit" class="btn btn-success">Ya</button>
            </div>
            <div class="modal-footer justify-content-center" id="next_button" style="display:block">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="text" name="id" id="id" class="id" hidden/>
                <button type="button" class="btn btn-success" onclick="nextTerima()">Terima</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endif

@if($status_id >= 3)
<!-- Modal Ubah BMS -->
<div id="ubahBms" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column" id="form_yakin" style="display:none">
                <div class="icon-box">
                    <i class="material-icons">&#xe5ca;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-header flex-column" id="form_title" style="display:block">
                <h4 class="modal-title w-100">Ubah BMS</h4>
                <h4 class="modal-title w-100" id="ubahbmsname">Ubah BMS</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{url('/kependidikan/psb/ubah-bms')}}" method="POST">
            @csrf
            <input type="hidden" name="unit_bms">
            <div class="modal-body" id="form_penerimaan" style="display:block">
                <div class="form-group">
                    <label for="type_pembayaran" class="col-form-label">Tipe BMS</label>
                    <select name="type_pembayaran" class="select2 form-control select2-hidden-accessible auto_width" id="type_pembayaran" style="width:100%;" tabindex="-1" aria-hidden="true">
                        <option value="1" selected>Tunai</option>
                        <option value="2">Berkala</option>
                    </select>
                </div>
                <div class="form-group">
                  <label for="bms_total" class="col-form-label">BMS Tunai</label>
                  <input type="text" readonly name="bms_total" class="form-control number-separator" id="bms_total" value="0" required>
                </div>
                <div class="form-group">
                  <label for="bms_potongan" class="col-form-label">Potongan BMS</label>
                  <input type="hidden" name="bms_potongan" id="bms_potongan" value="0" required>
                  <select name="potongan" class="form-control" name="potongan" required="required"/>
                    <option value="0">== Pilih ==</option>
                    @foreach($deductions as $d)
                    <option value="{{ $d->isPercentage ? $d->percentage : $d->nominal }}" data-is-percentage="{{ $d->isPercentage ? 1 : 0 }}">{{ $d->isPercentage ? $d->nameWithPercentage : $d->nameWithNominal }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label for="bms_bersih" class="col-form-label">BMS Bersih</label>
                  <input type="text" name="bms_bersih" class="form-control number-separator" id="bms_bersih" value="0" readonly required>
                </div>
                <div class="form-group">
                  <label for="bms_daftar_ulang" class="col-form-label">Daftar Ulang</label>
                  <input type="text" name="bms_daftar_ulang" class="form-control number-separator" id="bms_daftar_ulang" value="0" {{$title=="Peresmian Siswa"?'readonly':''}} required>
                </div>
                <div class="form-group">
                  <label for="bms_sisa_bms" class="col-form-label">Sisa BMS</label>
                  <input type="text" name="bms_sisa_bms[]" class="form-control number-separator" id="bms_sisa_bms" value="0" readonly required>
                </div>
                <div class="form-group bms-berkala-2" style="display: none">
                  <label for="bms_berkala_2" class="col-form-label">BMS Berkala 2</label>
                  <input type="text" name="bms_sisa_bms[]" class="form-control number-separator" id="bms_berkala_2" value="0" readonly required>
                </div>
                <div class="form-group bms-berkala-3" style="display: none">
                  <label for="bms_berkala_3" class="col-form-label">BMS Berkala 3</label>
                  <input type="text" name="bms_sisa_bms[]" class="form-control number-separator" id="bms_berkala_3" value="0" readonly required>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id="yakin_button" style="display:none">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="text" name="id" id="id" class="id" hidden/>
                <button type="submit" class="btn btn-success">Ya</button>
            </div>
            <div class="modal-footer justify-content-center" id="next_button" style="display:block">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="text" name="id" id="id" class="id" hidden/>
                <button type="button" class="btn btn-success" onclick="nextTerima()">Ubah</button>
            </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal Dicadangkan -->
<div id="DicadangkanModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xE5CD;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan mencadangkan <b id="pDicadangkan"></b>?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('kependidikan.psb.dicadangkanDone')}}" method="POST">
                    @csrf
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Cadangkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Siswa -->
<div id="HapusModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xE5CD;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan menghapus <b id="pDihapus"></b>?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('kependidikan.psb.hapus')}}" method="POST">
                    @csrf
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Saving Seat Done -->
<div id="SavingSeatModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xe5ca;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin Ananda <b id="pDiterima"></b> telah membayar untuk observasi/psikotes?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('kependidikan.psb.acc-saving-seat')}}" method="POST">
                    @csrf
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-success">Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Wawancara Done -->
<div id="DiterimaModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xe5ca;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan menerima 
                    <b id="pDiterima"></b>?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('kependidikan.psb.wawancaraDone')}}" method="POST">
                    @csrf
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-success">Ya</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Wawancara Link -->
<div id="WawancaraLink" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <form action="{{route('kependidikan.psb.wawancaraLink')}}" method="POST">
        <div class="modal-content">
            @csrf
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xe02c;</i>
                </div>
                <h4 class="modal-title w-100">Informasi Wawancara dan Observasi siswa</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="tipe_wawancara" class="col-form-label">Tipe Wawancara dan Observasi</label>
                    <select name="tipe_wawancara" class="select2 form-control select2-hidden-accessible auto_width" id="tipe_wawancara" style="width:100%;" tabindex="-1" aria-hidden="true">
                        <option value="1" selected>Online</option>
                        <option value="2">Offline</option>
                    </select>
                </div>
                <div class="form-group link-wawancara">
                  <label for="year_spp" class="col-form-label">Link Wawancara dan Observasi</label>
                  <input type="text" name="link" class="form-control" id="link" value="2021">
                </div>
                <div class="form-group">
                  <label for="year_spp" class="col-form-label">Tanggal Wawancara dan Observasi</label>
                  <input type="date" name="interview_date" class="form-control" id="interview_date" value="2021" required>
                </div>
                <div class="form-group">
                  <label for="year_spp" class="col-form-label">Jam Wawancara dan Observasi</label>
                  <input type="time" name="interview_time" class="form-control" id="interview_time" value="2021" required>
                </div>
                {{--
                @if($banks && count($banks) > 0)
                <div class="form-group">
                    <label for="bank" class="col-form-label">Nama Bank</label>
                    <select name="bank" class="select2 form-control" id="bank">
                        @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ $bank->name == "Bank Syariah Indonesia" ? 'selected' : null }}>{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                --}}
                <div class="form-group">
                  <label for="account_number" class="col-form-label">Nomor Rekening</label>
                  <input type="text" name="account_number" class="form-control" id="account_number">
                </div>
                {{-- <div class="form-group">
                  <label for="account_holder" class="col-form-label">Nama Pemilik Rekening</label>
                  <input type="text" name="account_holder" class="form-control" id="account_holder">
                </div> --}}
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-success">Kirim</button>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- Modal BatalBayar -->
<div id="BatalBayarModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xE5CD;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan membatalkan pembayaran calon siswa?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('kependidikan.psb.batalDaftarUlang')}}" method="POST">
                    @csrf
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <button type="submit" class="btn btn-danger">Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal RevertBatalBayar -->
<div id="revertBatalBayarModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xE5CD;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin akan mengurungkan pembatalan pembayaran calon siswa?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="{{route('kependidikan.psb.revertBatalDaftarUlang')}}" method="POST">
                    @csrf
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <button type="submit" class="btn btn-danger">Urungkan</button>
                </form>
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

<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>
<script>
    function hitung(){

        var bms_total = parseInt($('#bms_total').val().replace(/\./g, ""));
        //var bms_potongan = parseInt($('#bms_potongan').val().replace(/\./g, ""));
        var potongan = $('select[name="potongan"]').val();
        var isPercentage = $('select[name="potongan"]').find('option:selected').attr('data-is-percentage');
        var bms_potongan = 0;
        if(isPercentage > 0)
            bms_potongan = (potongan/100)*bms_total;
        else
            bms_potongan = potongan;
        bms_potongan = Math.round(bms_potongan);
        var bms_bersih = bms_total - bms_potongan;
        $('#bms_potongan').val(bms_potongan);
        $('#bms_bersih').val(bms_bersih);

        var bms_daftar_ulang = parseInt($('#bms_daftar_ulang').val().replace(/\./g, ""));
        var bms_sisa_bms = bms_bersih - bms_daftar_ulang;
        $('#bms_sisa_bms').val(bms_sisa_bms);
        console.log(bms_total,bms_potongan);
    }
    $(document).ready(function()
    {
        $('#WawancaraDone').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var name_calon = button.data('name') // Extract info from data-* attributes
            var unit = button.data('unit') // Extract info from data-* attributes
            var modal = $(this)
            jQuery.ajax({
                url: '/kependidikan/psb/saving-seat/cari?unit_bms='+unit+'&type_pembayaran=1',
                type:'GET',
                success:function(data){
                    $('input[name="bms_total"]').val(data);
                    hitung();
                }
            });
            modal.find('input[name="id"]').val(id);
            modal.find('input[name="unit_bms"]').val(unit);

            modal.find('label[for="bms_total"]').text('BMS Tunai');
            modal.find('label[for="bms_potongan"]').text('Potongan BMS Tunai');
            modal.find('label[for="bms_bersih"]').text('BMS Tunai Bersih');
            modal.find('label[for="bms_sisa_bms"]').text('Sisa BMS Tunai');
        });
        $('#ubahBms').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var name_calon = button.data('name') // Extract info from data-* attributes
            var unit = button.data('unit') // Extract info from data-* attributes
            var du = button.data('du') // Extract info from data-* attributes
            var modal = $(this)
            jQuery.ajax({
                url: '/kependidikan/psb/saving-seat/cari?unit_bms='+unit+'&type_pembayaran=1',
                type:'GET',
                success:function(data){
                    $('input[name="bms_total"]').val(data);
                    hitung();
                }
            });
            modal.find('input[name="id"]').val(id);
            modal.find('input[name="unit_bms"]').val(unit);
            modal.find('#ubahbmsname').text(name_calon);
            modal.find('#bms_daftar_ulang').val(du);

            modal.find('label[for="bms_total"]').text('BMS Tunai');
            modal.find('label[for="bms_potongan"]').text('Potongan BMS Tunai');
            modal.find('label[for="bms_bersih"]').text('BMS Tunai Bersih');
            modal.find('label[for="bms_sisa_bms"]').text('Sisa BMS Tunai');
        });
        $('#DiterimaModal').on('show.bs.modal', function (event) {
            var page1 = document.getElementById("form_yakin");
            var page2 = document.getElementById("form_penerimaan");
            var page3 = document.getElementById("yakin_button");
            var page4 = document.getElementById("next_button");
            var page5 = document.getElementById("form_title");
            page1.style.display = "none";
            page2.style.display = "block";
            page3.style.display = "none";
            page4.style.display = "block";
            page5.style.display = "block";
            var button = $(event.relatedTarget) // Button that triggered the modal
            var name_calon = button.data('name') // Extract info from data-* attributes
            $('b[id="pDiterima"]').text(name_calon);
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        });

        $('#SavingSeatModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var name_calon = button.data('name') // Extract info from data-* attributes
            $('b[id="pDiterima"]').text(name_calon);
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        });
        $('#DicadangkanModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var name_calon = button.data('name') // Extract info from data-* attributes
            $('b[id="pDicadangkan"]').text(name_calon);
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        });
        $('#HapusModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var id = button.data('id'); // Extract info from data-* attributes
            var name_calon = button.data('name'); // Extract info from data-* attributes
            $('b[id="pDihapus"]').text(name_calon);
            var modal = $(this);
            modal.find('input[id="hapusid"]').val(id);
        });
        $('#WawancaraLink').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var link = button.data('link') // Extract info from data-* attributes
            var interview_date = button.data('interview_date') // Extract info from data-* attributes
            var interview_time = button.data('interview_time') // Extract info from data-* attributes
            var observation_link = button.data('observation_link') // Extract info from data-* attributes
            var observation_date = button.data('observation_date') // Extract info from data-* attributes
            var observation_time = button.data('observation_time') // Extract info from data-* attributes
            //var bank = button.data('bank') // Extract info from data-* attributes
            var account_number = button.data('account_number') // Extract info from data-* attributes
            //var account_holder = button.data('account_holder') // Extract info from data-* attributes
            var modal = $(this)
            console.log(link)
            modal.find('input[name="id"]').val(id)
            modal.find('input[name="link"]').val(link)
            modal.find('input[name="interview_date"]').val(interview_date)
            modal.find('input[name="interview_time"]').val(interview_time)
            modal.find('input[name="observation_link"]').val(observation_link)
            modal.find('input[name="observation_date"]').val(observation_date)
            modal.find('input[name="observation_time"]').val(observation_time)
            if(typeof bank !== 'undefined') modal.find('select[name="bank"]').val(bank)
            if(typeof account_number !== 'undefined') modal.find('input[name="account_number"]').val(account_number)
            if(typeof account_holder !== 'undefined') modal.find('input[name="account_holder"]').val(account_holder)
        });
        $('#ResmikanModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        });
        $('#BatalBayarModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#revertBatalBayarModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#awalSppModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var name_calon = button.data('name') // Extract info from data-* attributes
            var year = button.data('year') // Extract info from data-* attributes
            var month = button.data('month') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
            modal.find('input[name="name"]').val(name_calon)
            modal.find('input[name="year_spp"]').val(year)
            modal.find('select[name="month_spp"]').val(month)
        });
        $('select[name="tipe_wawancara"]').on('change',function(){
            var tipe_wawancara = this.value;
            if(tipe_wawancara == 1){
                $('.link-wawancara').show();
            }else{
                $('.link-wawancara').hide();

            }
        });

        $('select[name="bms_termin"]').on('change', function() {
            // alert( this.value );
            var bms_nominal = parseInt($('#bms_nominal').val().replace(/\./g, ""));
            var bms_deduction = parseInt($('#bms_deduction').val().replace(/\./g, ""));
            var bms_termin = this.value;
            console.log(bms_nominal, bms_deduction, bms_termin);
            var hasil = (bms_nominal-bms_deduction)/bms_termin;
            // alert( hasil );
            $('#bms_per_termin').val(hasil);
        });
        $('input[name="bms_potongan"]').on('change', function() {
            hitung();
        });
        $('select[name="potongan"]').on('change', function() {
            hitung();
        });
        $('input[name="bms_daftar_ulang"]').on('change', function() {
            hitung();
        });
        $('input[name="bms_nominal"]').on('change', function() {
            var bms_nominal = this.value.replace(/\./g, "");
            var bms_deduction = parseInt($('#bms_deduction').val().replace(/\./g, ""));
            var bms_termin = parseInt($('#bms_termin').val());
            console.log(bms_nominal, bms_deduction, bms_termin);
            var test = (bms_nominal-bms_deduction)/bms_termin;
            $('#bms_per_termin').val(test);
        });
        $('input[name="bms_deduction"]').on('change', function() {
            var bms_nominal = parseInt($('#bms_nominal').val().replace(/\./g, ""));
            var bms_deduction = this.value.replace(/\./g, "");
            var bms_termin = parseInt($('#bms_termin').val());
            console.log(bms_nominal, bms_deduction, bms_termin);
            var test = (bms_nominal-bms_deduction)/bms_termin;
            $('#bms_per_termin').val(test);
        });

        $('select[name="type_pembayaran"]').on('change', function() {
            var type_pembayaran = $('select[name="type_pembayaran"]').val();
            var unit_bms = $('input[name="unit_bms"]').val();
            jQuery.ajax({
                url: '/kependidikan/psb/saving-seat/cari?unit_bms='+unit_bms+'&type_pembayaran='+type_pembayaran+'',
                type:'GET',
                success:function(data){
                    $('input[name="bms_total"]').val(data);
                    hitung();
                }
            });

            if(type_pembayaran == 1){
                $('label[for="bms_total"]').text('BMS Tunai');
                $('label[for="bms_potongan"]').text('Potongan BMS Tunai');
                $('label[for="bms_bersih"]').text('BMS Tunai Bersih');
                $('label[for="bms_sisa_bms"]').text('Sisa BMS Tunai');
                $('.bms-berkala-2').hide();
                $('.bms-berkala-3').hide();
            }else{
                $('label[for="bms_total"]').text('BMS Berkala 1');
                $('label[for="bms_potongan"]').text('Potongan BMS Berkala 1');
                $('label[for="bms_bersih"]').text('BMS Berkala 1 Bersih');
                $('label[for="bms_sisa_bms"]').text('Sisa BMS Berkala 1');
                jQuery.ajax({
                    url: '/kependidikan/psb/saving-seat/cari?unit_bms='+unit_bms+'&type_pembayaran=3',
                    type:'GET',
                    success:function(data){
                        $('#bms_berkala_2').val(data);
                        hitung();
                    }
                });
                jQuery.ajax({
                    url: '/kependidikan/psb/saving-seat/cari?unit_bms='+unit_bms+'&type_pembayaran=4',
                    type:'GET',
                    success:function(data){
                        $('#bms_berkala_3').val(data);
                        hitung();
                    }
                });
                $('.bms-berkala-2').show();
                if(unit_bms != 1){
                    $('.bms-berkala-3').show();
                }
            }
        });

        $( 'input[name="du_nominal"]' ).mask('000.000.000', {reverse: true});
        $( 'input[name="bms_deduction"]' ).mask('000.000.000', {reverse: true});
        $( 'input[name="bms_nominal"]' ).mask('000.000.000', {reverse: true});
        $( 'input[name="bms_termin"]' ).mask('000.000.000', {reverse: true});

    })
</script>
<script>
    function nextTerima() {
        var page1 = document.getElementById("form_yakin");
        var page2 = document.getElementById("form_penerimaan");
        var page3 = document.getElementById("yakin_button");
        var page4 = document.getElementById("next_button");
        var page5 = document.getElementById("form_title");
        page1.style.display = "block";
        page2.style.display = "none";
        page3.style.display = "block";
        page4.style.display = "none";
        page5.style.display = "none";
    }
</script>


<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.kbm.hideelement')
@endsection