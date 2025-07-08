@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@if(isset($siswa))
@section('headmeta')
<!-- DataTables -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/jszip/datatables.min.css') }}" rel="stylesheet">
@endsection
@endif

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item"><a href="{{ route('bms.index')}}">Biaya Masuk Sekolah</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>

<div class="row">
    @php
    $siswaOpt = ['calon','siswa','alumni'];
    @endphp
    @foreach($siswaOpt as $opt)
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 {{ isset($siswa) && $siswa == $opt ? 'bg-brand-green' : 'bg-brand-green' }}">
                        <i class="mdi mdi-account-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ ucwords(($opt == 'alumni' ? 'Siswa ' : null).$opt.($opt == 'calon' ? ' Siswa' : null)) }}</div>
                    </div>
                    <div class="col-auto">
                        @if(!isset($siswa) || (isset($siswa) && $siswa != $opt))
                        <a href="{{ route($route.'.index', ['siswa' => $opt])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                        @else
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary disabled"role="button" aria-disabled="true">Pilih</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(isset($siswa))
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              @if(!isset($jenis) || $jenis != 'berkala')
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route.'.index', ['siswa' => $siswa, 'jenis' => 'tunai']) }}">Tunai</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route.'.index', ['siswa' => $siswa, 'jenis' => 'berkala']) }}">Berkala</a>
              </li>
              @else
              <li class="nav-item">
                <a class="nav-link text-brand-green" href="{{ route($route.'.index', ['siswa' => $siswa, 'jenis' => 'tunai']) }}">Tunai</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="{{ route($route.'.index', ['siswa' => $siswa, 'jenis' => 'berkala']) }}">Berkala</a>
              </li>
              @endif
            </ul>
        </div>
    </div>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card shadow">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-brand-green">{{ $active }}</h6>
      </div>
      @if(count($lists) > 0)
      <div class="card-body">
        @if(Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sukses!</strong> {{ Session::get('success') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if(Session::has('danger'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Gagal!</strong> {{ Session::get('danger') }}
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        @endif
        @if($siswa == 'calon')        
        <div class="float-right mb-2">
            <form id="registerFilterForm" action="{{ route($route.'.index', ['siswa' => $siswa]) }}" method="get">
              <input type="hidden" name="jenis" value="{{ $jenis }}">
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fa fa-check-circle"></i>
                  </span>
                </div>
                <select class="custom-select" id="register_filter" name="register_filter" onchange="if(this.value){ this.form.submit(); }">
                  <option value="semua" {!! ($register == 'semua') ? 'selected="selected"' : null !!} >Semua</option>
                  <option value="0" {!! ($register == '0') ? 'selected="selected"' : null !!} >Belum Bayar DU</option>
                  <option value="1" {!! ($register == '1') ? 'selected="selected"' : null !!} >Sudah Bayar DU</option>
                </select>
              </div>
            </form>
        </div>
        @endif
        <div class="table-responsive">
            <table id="dataTable" class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        @if(!isset($siswa) || $siswa != 'calon')
                        <th>NIPD</th>
                        <th>Nama Siswa</th>
                        @else                        
                        <th>No. PSB</th>
                        <th>Nama Calon Siswa</th>
                        @endif
                        @if(!isset($jenis) || $jenis != 'berkala')
                        <th>Nominal BMS Tunai</th>
                        <th>Potongan BMS Tunai</th>
                        <th>Tanggungan BMS Tunai Bersih</th>
                        <th>Tanggungan BMS Tunai yang Sudah Dibayarkan</th>
                        <th>Sisa Tanggungan BMS Tunai yang Harus Dibayarkan</th>
                        @else
                        <th>Nominal BMS Berkala 1</th>
                        <th>Potongan BMS Berkala 1</th>
                        <th>Tanggungan BMS Berkala 1 Bersih</th>
                        <th>Tanggungan BMS Berkala 2</th>
                        <th>Tanggungan BMS Berkala 3</th>
                        <th>Tanggungan BMS Berkala yang Sudah Dibayarkan</th>
                        <th>Sisa Tanggungan BMS Berkala yang Harus Dibayarkan</th>
                        @endif
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lists as $index => $list)
                    <tr>
                        @if($list->siswa)
                        @if(!isset($siswa) || $siswa != 'calon')
                        <td>{{ $list->siswa->student_nis }}</td>
                        <td>{{ $list->siswa->identitas->student_name }}</td>
                        @else
                        <td>{{ $list->siswa->reg_number }}</td>
                        <td>{{ $list->siswa->student_name }}</td>
                        @endif
                        @else
                        <td>{{-- $list --}}</td>
                        <td>{{-- $list --}}</td>
                        @endif
                        @if(!isset($jenis) || $jenis != 'berkala')
                        <td>{{ $list->totalBmsNominalCashWithSeparator }}</td>
                        <td>{{ $list->bmsDeductionWithSeparator }}</td>
                        <td>{{ $list->bmsNominalWithSeparator }}</td>
                        <td>{{ $list->bmsPaidWithSeparator }}</td>
                        <td>{{ $list->bmsRemainWithSeparator }}</td>
                        @else
                        @php
                        $terminCount = $list->termin()->count();
                        $scope = !isset($siswa) || $siswa != 'calon' ? 'siswa' : 'calon';
                        @endphp
                        @if($terminCount > 0)
                        @php
                        $termins = $list->termin()->{$scope}()->with('tahunPelajaran:id,academic_year_start')->get()->sortBy('tahunPelajaran.academic_year_start');
                        @endphp
                        @foreach($termins as $key => $t)
                        @if($key == 0)
                        <td>{{ number_format($t->nominal+$list->register_nominal+$list->bms_deduction, 0, ',', '.') }}</td>
                        <td>{{ $list->bmsDeductionWithSeparator }}</td>
                        <td>{{ number_format($t->nominal+$list->register_nominal, 0, ',', '.') }}</td>
                        @else
                        <td>{{ $t->nominalWithSeparator }}</td>
                        @endif
                        @endforeach
                        @if($terminCount < 3)
                        @for($i=0;$i<(3-$terminCount);$i++)
                        <td>-</td>
                        @endfor
                        @endif
                        <td>{{ $list->bmsPaidWithSeparator }}</td>
                        <td>{{ $list->bmsRemainWithSeparator }}</td>
                        @else
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                        @endif
                        @endif
                        {{--<td>Rp 
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
                        </td> --}}
                        <td>
                            {{-- <a href="#" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp; --}}
                            @if($list->bms_remain > 0)
                            <a href="{{ route('bms.reminder.wa',['siswa'=>(!isset($siswa) || !in_array($siswa,$siswaOpt))?'siswa':$siswa,'id'=>$list->id]) }}" class="btn btn-sm btn-success" target="_blank"><i class="fas fa-comment"></i></a>
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#edit-form" onclick="editModal('{{ route('bms.reminder.email.create',['siswa'=>(!isset($siswa) || !in_array($siswa,$siswaOpt))?'siswa':$siswa]) }}','{{ $list->id }}')"><i class="fas fa-envelope"></i></a>
                            @endif
                          @if(!isset($siswa) || $siswa != 'calon')
                            <Button href="#" class="btn btn-sm btn-danger" btn-danger" data-toggle="modal" data-target="#HapusModal" disabled><i class="fas fa-trash"></i></Button>
                            @php
                            $id = Crypt::encrypt($list->id);
                            $surat_url = route('bms.print',$id);
                            @endphp
                            <a href="{{$surat_url}}" target="_blank"><button class="m-0 btn btn-info btn-sm"><i class="fa fa-download"></i></button></a>
                          @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
      </div>
      @else
      @if(Session::has('success'))
      <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
        <strong>Sukses!</strong> {{ Session::get('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      @if(Session::has('danger'))
      <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
        <strong>Gagal!</strong> {{ Session::get('danger') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      <div class="text-center mx-3 my-5">
        <h3 class="text-center">Mohon Maaf,</h3>
        <h6 class="font-weight-light mb-3">Tidak ada data {{ strtolower($active) }} yang ditemukan</h6>
      </div>
      @endif
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
@endif

<!--Row-->
@endsection

@if(isset($siswa))
@section('footjs')
<!-- Plugins and scripts required by this view-->

<!-- Page level plugins -->

<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('vendor/datatables-button/jszip/datatables.min.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.global.custom-file-input')
@include('template.footjs.global.get-today-date')
@include('template.footjs.modal.post_edit')
@include('template.footjs.keuangan.datatables-thousands-dot-exportable')
<script>
$(document).ready(function () {
  datatablesExportable([2,3,4,5,6],null,'Diekspor per '+getTodayDate());
});
</script>
@endsection
@endif