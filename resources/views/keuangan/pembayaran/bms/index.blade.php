@extends('template.main.master')

@section('title')
Biaya Masuk Sekolah
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Biaya Masuk Sekolah</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">Biaya Masuk Sekolah</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                    {{-- <form action="/keuangan/bms/siswa" method="POST">
                    @csrf
                        @if($unit_id==5)
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Unit</label>
                            <div class="col-sm-5">
                                <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="semua">Semua</option>
                                    <option value="1" selected>TK</option>
                                    <option value="2" selected>SD</option>
                                    <option value="3" selected>SMP</option>
                                    <option value="4" selected>SMA</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-5">
                                <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="semua">Semua</option>
                                    @foreach( $levels as $tingkat)
                                    @if( $level == $tingkat->id )
                                        <option value="{{$tingkat->id}}" id="level_kelas" selected>{{$tingkat->level}}</option>
                                    @else
                                    <option value="{{$tingkat->id}}" id="level_kelas">{{$tingkat->level}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-brand-green-dark btn-sm" type="submit">Saring</button>
                        </div>
                    </form> --}}
                    </div>
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-green">Biaya Masuk Sekolah</h6>
                            <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="/keuangan/bms/tambah">Tambah <i class="fas fa-plus"></i></a>
                        </div>
                        @if(Session::has('sukses'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> {{ Session::get('sukses') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        @endif
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>NIPD</th>
                                    <th>Nama</th>
                                    <th>Nominal BMS</th>
                                    <th>Tanggungan BMS Total</th>
                                    <th>Tanggungan BMS Tahun Ini</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $lists as $index => $list )
                                    
                                <tr>
                                    <td>{{$list->siswa->student_nis}}</td>
                                    <td>{{$list->siswa->identitas->student_name}}</td>
                                    <td>Rp {{number_format($list->bms_nominal)}}</td>
                                    <td>Rp {{number_format($list->bms_remain)}}</td>
                                    <td>Rp 
                                        @php
                                            $ada = false;
                                            $remain = 0;
                                        @endphp
                                        @if (count($list->terminTahun)>0)
                                            @foreach ($list->terminTahun as $termin)
                                                @if($termin->academic_year_id == $tahun_aktif->id)
                                                    {{number_format($termin->remain)}}
                                                    @php
                                                        $ada = true;
                                                        $remain = $termin->remain;
                                                    @endphp
                                                    @endif
                                            @endforeach
                                            @if (!$ada)
                                                {{number_format($list->bms_remain)}}
                                                @php
                                                    $remain = $list->bms_remain;
                                                @endphp
                                            @endif
                                        @else
                                            {{number_format($list->bms_remain)}}
                                            @php
                                                $remain = $list->bms_remain;
                                            @endphp
                                        @endif
                                    </td>
                                    <td>
                                        {{-- <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp; --}}
                                        @if($list->bms_remain > 0)
                                        <a href="{{ route('bms.reminder.wa',['id'=>$list->id]) }}" class="btn btn-sm btn-success" target="_blank"><i class="fas fa-comment"></i></a>
                                        <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('bms.reminder.email.create') }}','{{ $list->id }}')"><i class="fas fa-envelope"></i></a>
                                        @endif
                                        <Button href="#" class="btn btn-sm btn-danger" btn-danger" data-toggle="modal" data-target="#HapusModal" disabled><i class="fas fa-trash"></i></Button>
                                        @php
                                        $id = Crypt::encrypt($list->id);
                                        $surat_url = route('bms.cetak',$id);
                                        @endphp
                                        <a href="{{$surat_url}}"><button class="m-0 btn btn-info btn-sm"><i class="fa fa-download"></i></button></a>
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
</div>

<div class="modal fade" id="edit-form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-brand-green border-0">
        <h5 class="modal-title text-white">Kirim Email Pengingat Tanggungan BMS</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">x</span>
        </button>
      </div>

      <div class="modal-load p-4">
        <div class="row">
          <div class="col-12">
            <div class="text-center my-5">
              <i class="fa fa-spin fa-circle-notch fa-lg text-brand-green"></i>
              <h5 class="font-weight-light mb-3">Memuat...</h5>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-body p-4" style="display: none;">
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
@include('template.footjs.global.custom-file-input')
@include('template.footjs.modal.post_edit')
@include('template.footjs.kbm.datatables')
@endsection