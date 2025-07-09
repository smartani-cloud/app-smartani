<<<<<<< HEAD
@extends('template.main.master')

@section('title')
{{$title}} Daftar Ulang
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
    <h1 class="h3 mb-0 text-gray-800">{{$title}} Daftar Ulang </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Psb</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Calon Siswa</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{$title}} Daftar Ulang </li>
    </ol>
</div>

@if($title == 'Belum Lunas')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              @if(!isset($bayar) || $bayar != 'belum')
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route, ['bayar' => 'sebagian']) }}">Membayar Sebagian</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route, ['bayar' => 'belum']) }}">Belum Membayar</a>
              </li>
              @else
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route, ['bayar' => 'sebagian']) }}">Membayar Sebagian</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route, ['bayar' => 'belum']) }}">Belum Membayar</a>
              </li>
              @endif
            </ul>
        </div>
    </div>
</div>
@endif

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                {{-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-brand-green">Bayar Daftar Ulang</h6>
                </div> --}}
                <div class="row">
                    <div class="col-md-8">
                        <form action="/{{Request::path()}}" method="GET">
                        @csrf
                        @if($title == 'Belum Lunas')
                        <input type="hidden" name="bayar" value="{{ $bayar }}">
                        @endif
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
                <h6 class="m-0 font-weight-bold text-brand-green-dark">{{$title}} Daftar Ulang</h6>
                @if(in_array(Auth::user()->role->name,['ctl']))
                <form action="/{{Request::path().'/ekspor'}}" method="get">
                    <input type="hidden" name="level" value="{{ $request->level }}">
                    <input type="hidden" name="year" value="{{ $request->year }}">
                    @if(isset($bayar))<input type="hidden" name="bayar" value="{{ $bayar }}">@endif
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
                @if(Session::has('sukses'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('sukses') }}
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
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>VA</th>
                                <th>Nominal Daftar Ulang</th>
                                <th>Tanggungan Daftar Ulang</th>
                                <th>Info Asal Sekolah</th>
                                @if(!in_array(auth()->user()->role->name,['am','aspv','cspv']))
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( $lists as $index => $list )
                            @if ($list->siswa && $list->siswa->status_id == 4) 
                            <tr>
                                <td>{{$list->siswa->student_name}}</td>
                                <td>{{$list->siswa->level->level}}</td>
                                <td>{{$list->siswa->virtualAccount->bms_va}}</td>
                                <td>Rp {{number_format($list->register_nominal)}}</td>
                                <td>Rp {{number_format($list->register_nominal - $list->register_paid)}}</td>
                                <td>{{ucwords($list->siswa->origin_school)}}</td>
                                @if(!in_array(auth()->user()->role->name,['am','aspv','cspv']))
                                <td>
                                    @if($title == "Sudah Lunas")
                                    @php
                                    $whatsAppText = rawurlencode("Assalamu'alaikum Ayah Bunda Ananda ".$list->siswa->student_name.". Terima kasih telah melunasi pembayaran Daftar Ulang. Kami mengucapkan kepada Ananda ".$list->siswa->student_name." SELAMAT BERGABUNG di ".$list->siswa->unit->islamic_name." DIGIYOK. Untuk informasi lebih lanjut Ayah Bunda silakan mengakses Aplikasi DIGIYOK melalui link: ".route('psb.index'));
                                    @endphp
                                    @if($list->siswa->orangtua->mother_phone && (substr($list->siswa->orangtua->mother_phone, 0, 2) == "62" || substr($list->siswa->orangtua->mother_phone, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($list->siswa->orangtua->mother_phone, 0, 2) == "62" ? $list->siswa->orangtua->mother_phone : ('62'.substr($list->siswa->orangtua->mother_phone, 1)) }}&text={{ $whatsAppText }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-bell"></i></a>@endif
                                    @if( in_array((auth()->user()->role_id), array(8)))
                                    <a href="#" class="btn btn-sm btn-success"   data-toggle="modal" data-target="#DiterimaModal" data-id="{{$list->candidate_student_id}}"><i class="fas fa-check"></i></a>
                                    @endif
                                    @elseif($title == "Belum Lunas")
                                    <a href="#" class="btn btn-sm btn-success"   data-toggle="modal" data-target="#UbahDaftarUlang" data-du="{{($list->register_nominal)}}" data-id="{{$list->candidate_student_id}}"><i class="fa fa-info-circle"></i></a>
                                    @php
                                    $whatsAppText = rawurlencode("Assalamu’alaikum Ayah Bunda Ananda ".$list->siswa->student_name.". Terima kasih telah menunggu hasil Wawancara dan Observasi. Ayah Bunda dapat melihat pengumuman hasilnya pada Aplikasi DIGIYOK melalui link: ".route('psb.index')." Link ini juga menginformasikan mengenai pembayaran uang sekolah sesuai dengan kesepakatan Wawancara Keuangan.");
                                    @endphp
                                    @if($list->siswa->orangtua->mother_phone && (substr($list->siswa->orangtua->mother_phone, 0, 2) == "62" || substr($list->siswa->orangtua->mother_phone, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($list->siswa->orangtua->mother_phone, 0, 2) == "62" ? $list->siswa->orangtua->mother_phone : ('62'.substr($list->siswa->orangtua->mother_phone, 1)) }}&text={{ $whatsAppText }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-bell"></i></a>@endif
                                    @endif
                                    <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#BatalBayarModal" data-id="{{$list->siswa->id}}"><i class="fas fa-trash"></i></a>
                                </td>
                                @endif
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if( in_array((auth()->user()->role_id), array(8)))
<!-- Modal Konfirmasi -->
<div id="DiterimaModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xe5ca;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin untuk Konfirmasi?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('kependidikan.psb.konfirmasiLunas')}}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="modal-body" id="form_penerimaan" style="display:block">
                    <div class="form-group">
                      <label for="year_spp" class="col-form-label">Tahun Mulai SPP</label>
                      <input type="number" name="year_spp" class="form-control" id="year_spp" value="2021" required>
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
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-success">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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

<!-- Modal Ubah Daftar Ulang -->
<div id="UbahDaftarUlang" class="modal fade">
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
                <h4 class="modal-title w-100">Ubah nominal daftar ulang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('kependidikan.psb.ubah-du')}}" method="POST">
            @csrf
            <input type="hidden" name="unit_bms">
            <div class="modal-body" id="form_penerimaan" style="display:block">
                <div class="form-group">
                  <label for="bms_daftar_ulang" class="col-form-label">Daftar Ulang</label>
                  <input type="text" name="bms_daftar_ulang" class="form-control number-separator" id="bms_daftar_ulang" value="0" required>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id="next_button" style="display:block">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="text" name="id" id="id" class="id" hidden/>
                <button type="submit" class="btn btn-success">Ubah</button>
            </div>
            </form>
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
    $(document).ready(function()
    {

        $('#UbahDaftarUlang').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var ud = button.data('du') // Extract info from data-* attributes
            var modal = $(this);
            modal.find('input[name="id"]').val(id);
            modal.find('input[name="bms_daftar_ulang"]').val(ud);
        })
        $('#WawancaraDone').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#DiterimaModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#DicadangkanModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#WawancaraLink').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#BatalBayarModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
    })
</script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.kbm.hideelement')
=======
@extends('template.main.master')

@section('title')
{{$title}} Daftar Ulang
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
    <h1 class="h3 mb-0 text-gray-800">{{$title}} Daftar Ulang </h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Psb</a></li>
        <li class="breadcrumb-item"><a href="javascript:void(0)">Calon Siswa</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{$title}} Daftar Ulang </li>
    </ol>
</div>

@if($title == 'Belum Lunas')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              @if(!isset($bayar) || $bayar != 'belum')
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route, ['bayar' => 'sebagian']) }}">Membayar Sebagian</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route, ['bayar' => 'belum']) }}">Belum Membayar</a>
              </li>
              @else
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route, ['bayar' => 'sebagian']) }}">Membayar Sebagian</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route, ['bayar' => 'belum']) }}">Belum Membayar</a>
              </li>
              @endif
            </ul>
        </div>
    </div>
</div>
@endif

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                {{-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-brand-green">Bayar Daftar Ulang</h6>
                </div> --}}
                <div class="row">
                    <div class="col-md-8">
                        <form action="/{{Request::path()}}" method="GET">
                        @csrf
                        @if($title == 'Belum Lunas')
                        <input type="hidden" name="bayar" value="{{ $bayar }}">
                        @endif
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
                <h6 class="m-0 font-weight-bold text-brand-green-dark">{{$title}} Daftar Ulang</h6>
                @if(in_array(Auth::user()->role->name,['ctl']))
                <form action="/{{Request::path().'/ekspor'}}" method="get">
                    <input type="hidden" name="level" value="{{ $request->level }}">
                    <input type="hidden" name="year" value="{{ $request->year }}">
                    @if(isset($bayar))<input type="hidden" name="bayar" value="{{ $bayar }}">@endif
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
                @if(Session::has('sukses'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('sukses') }}
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
                                <th>Nama</th>
                                <th>Kelas</th>
                                <th>VA</th>
                                <th>Nominal Daftar Ulang</th>
                                <th>Tanggungan Daftar Ulang</th>
                                <th>Info Asal Sekolah</th>
                                @if(!in_array(auth()->user()->role->name,['am','aspv','cspv']))
                                <th>Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ( $lists as $index => $list )
                            @if ($list->siswa && $list->siswa->status_id == 4) 
                            <tr>
                                <td>{{$list->siswa->student_name}}</td>
                                <td>{{$list->siswa->level->level}}</td>
                                <td>{{$list->siswa->virtualAccount->bms_va}}</td>
                                <td>Rp {{number_format($list->register_nominal)}}</td>
                                <td>Rp {{number_format($list->register_nominal - $list->register_paid)}}</td>
                                <td>{{ucwords($list->siswa->origin_school)}}</td>
                                @if(!in_array(auth()->user()->role->name,['am','aspv','cspv']))
                                <td>
                                    @if($title == "Sudah Lunas")
                                    @php
                                    $whatsAppText = rawurlencode("Assalamu'alaikum Ayah Bunda Ananda ".$list->siswa->student_name.". Terima kasih telah melunasi pembayaran Daftar Ulang. Kami mengucapkan kepada Ananda ".$list->siswa->student_name." SELAMAT BERGABUNG di ".$list->siswa->unit->islamic_name." DIGIYOK. Untuk informasi lebih lanjut Ayah Bunda silakan mengakses Aplikasi DIGIYOK melalui link: ".route('psb.index'));
                                    @endphp
                                    @if($list->siswa->orangtua->mother_phone && (substr($list->siswa->orangtua->mother_phone, 0, 2) == "62" || substr($list->siswa->orangtua->mother_phone, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($list->siswa->orangtua->mother_phone, 0, 2) == "62" ? $list->siswa->orangtua->mother_phone : ('62'.substr($list->siswa->orangtua->mother_phone, 1)) }}&text={{ $whatsAppText }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-bell"></i></a>@endif
                                    @if( in_array((auth()->user()->role_id), array(8)))
                                    <a href="#" class="btn btn-sm btn-success"   data-toggle="modal" data-target="#DiterimaModal" data-id="{{$list->candidate_student_id}}"><i class="fas fa-check"></i></a>
                                    @endif
                                    @elseif($title == "Belum Lunas")
                                    <a href="#" class="btn btn-sm btn-success"   data-toggle="modal" data-target="#UbahDaftarUlang" data-du="{{($list->register_nominal)}}" data-id="{{$list->candidate_student_id}}"><i class="fa fa-info-circle"></i></a>
                                    @php
                                    $whatsAppText = rawurlencode("Assalamu’alaikum Ayah Bunda Ananda ".$list->siswa->student_name.". Terima kasih telah menunggu hasil Wawancara dan Observasi. Ayah Bunda dapat melihat pengumuman hasilnya pada Aplikasi DIGIYOK melalui link: ".route('psb.index')." Link ini juga menginformasikan mengenai pembayaran uang sekolah sesuai dengan kesepakatan Wawancara Keuangan.");
                                    @endphp
                                    @if($list->siswa->orangtua->mother_phone && (substr($list->siswa->orangtua->mother_phone, 0, 2) == "62" || substr($list->siswa->orangtua->mother_phone, 0, 1) == "0"))<a href="https://api.whatsapp.com/send?phone={{ substr($list->siswa->orangtua->mother_phone, 0, 2) == "62" ? $list->siswa->orangtua->mother_phone : ('62'.substr($list->siswa->orangtua->mother_phone, 1)) }}&text={{ $whatsAppText }}" class="btn btn-sm btn-warning" target="_blank"><i class="fas fa-bell"></i></a>@endif
                                    @endif
                                    <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#BatalBayarModal" data-id="{{$list->siswa->id}}"><i class="fas fa-trash"></i></a>
                                </td>
                                @endif
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if( in_array((auth()->user()->role_id), array(8)))
<!-- Modal Konfirmasi -->
<div id="DiterimaModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <div class="modal-header flex-column">
                <div class="icon-box">
                    <i class="material-icons">&#xe5ca;</i>
                </div>
                <h4 class="modal-title w-100">Apakah Anda yakin untuk Konfirmasi?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('kependidikan.psb.konfirmasiLunas')}}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="modal-body" id="form_penerimaan" style="display:block">
                    <div class="form-group">
                      <label for="year_spp" class="col-form-label">Tahun Mulai SPP</label>
                      <input type="number" name="year_spp" class="form-control" id="year_spp" value="2021" required>
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
                    <input type="text" name="id" id="id" class="id" hidden/>
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-success">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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

<!-- Modal Ubah Daftar Ulang -->
<div id="UbahDaftarUlang" class="modal fade">
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
                <h4 class="modal-title w-100">Ubah nominal daftar ulang</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="{{route('kependidikan.psb.ubah-du')}}" method="POST">
            @csrf
            <input type="hidden" name="unit_bms">
            <div class="modal-body" id="form_penerimaan" style="display:block">
                <div class="form-group">
                  <label for="bms_daftar_ulang" class="col-form-label">Daftar Ulang</label>
                  <input type="text" name="bms_daftar_ulang" class="form-control number-separator" id="bms_daftar_ulang" value="0" required>
                </div>
            </div>
            <div class="modal-footer justify-content-center" id="next_button" style="display:block">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <input type="text" name="id" id="id" class="id" hidden/>
                <button type="submit" class="btn btn-success">Ubah</button>
            </div>
            </form>
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
    $(document).ready(function()
    {

        $('#UbahDaftarUlang').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var ud = button.data('du') // Extract info from data-* attributes
            var modal = $(this);
            modal.find('input[name="id"]').val(id);
            modal.find('input[name="bms_daftar_ulang"]').val(ud);
        })
        $('#WawancaraDone').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#DiterimaModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#DicadangkanModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#WawancaraLink').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
        $('#BatalBayarModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
        })
    })
</script>

<!-- Page level custom scripts -->
@include('template.footjs.global.datatables')
@include('template.footjs.kbm.hideelement')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection