@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('headmeta')
<!-- Select2 -->
<link href="{{ asset('vendor/select2/dist/css/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/select2/dist/css/select2-bootstrap4.min.css') }}" rel="stylesheet">
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        {{-- <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li> --}}
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <ul class="nav nav-pills p-3">
              <li class="nav-item">
                <a class="nav-link {{ !isset($status) || !in_array($status,['sukses','refund','ditolak']) ? 'active' : 'text-brand-green' }}" href="{{ route($route.'.index', ['status' => 'menunggu']) }}">Menunggu</a>
              </li>
              @php
              $statuses = ['sukses','refund','ditolak'];
              @endphp
              @foreach($statuses as $s)
              <li class="nav-item">
                <a class="nav-link {{ isset($status) && $status == $s ? 'active' : 'text-brand-green' }}" href="{{ route($route.'.index', ['status' => $s]) }}">{{ ucwords($s) }}</a>
              </li>
              @endforeach
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
          <div class="table-responsive">
            <table id="dataTable" class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>NIPD</th>
                        <th>Nama</th>
                        <th>Transaksi Asal</th>
                        <th>Nominal Awal</th>
                        <th>Target</th>
                        <th>Refund</th>
						@if($editable)
                        <th>Aksi</th>
						@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($datas as $data)
                    <tr>
                        @php
                        $isCalon = $data->origin == 3 ? true : false;
                        @endphp
                        <td>{{$data->created_at}}</td>
                        @if($data->transactionOrigin)
                        <td>{{$isCalon ? ($data->transactionOrigin->siswa ? $data->transactionOrigin->siswa->reg_number : 'Tidak ditemukan') : $data->transactionOrigin->siswa->student_nis}}</td>
                        <td>{{$isCalon ? ($data->transactionOrigin->siswa ? $data->transactionOrigin->siswa->student_name : 'Tidak ditemukan') : $data->transactionOrigin->siswa->identitas->student_name}}</td>
                        @else
                        <td>-</td>
                        <td>-</td>
                        @endif
                        <td>{{in_array($data->origin,[1,3])?'BMS':'SPP'}}</td>
                        <td>Rp {{$data->nominalWithSeparator}}</td>
                        <td>

                            @foreach ($data->transactionTarget as $target)
                            {{$target->is_student == 0 ? ($target->student ? $target->student->reg_number : 'Tidak ditemukan') : $target->student->student_nis}} <br>
                            {{$target->is_student == 0 ? ($target->student ? $target->student->student_name : 'Tidak ditemukan') : $target->student->identitas->student_name }} <br>
                            Rp {{$target->nominalWithSeparator}} ({{$target->transaction_type==1?'BMS':'SPP'}})
                            <br><br>
                            @endforeach

                        </td>
                        <td>Rp {{$data->refundWithSeparator}}</td>
						@if($editable)
                        <td>
                            @if($data->transactionOrigin)
                            <a href="#" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#ubahModal" 
                            data-name="{{$isCalon ? $data->transactionOrigin->siswa->student_name : $data->transactionOrigin->siswa->identitas->student_name}}" 
                            data-total="{{$data->nominalWithSeparator}}"
                            data-origin="{{$data->origin}}"
                            data-student_id="{{($data->transactionOrigin->siswa->id)}}" data-id="{{$data->id}}"><i class="fa fa-pencil-alt"></i></a>
                            <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#tolakModal" data-name="{{$isCalon ? $data->transactionOrigin->siswa->student_name : $data->transactionOrigin->siswa->identitas->student_name}}" data-id="{{$data->id}}"><i class="fa fa-ban"></i></a>
							@if(in_array(Auth::user()->pegawai->position_id,[57]))
                            <a href="#" class="btn btn-sm btn-success" data-toggle="modal" data-target="#ubahKategori" data-name="{{$isCalon ? $data->transactionOrigin->siswa->student_name : $data->transactionOrigin->siswa->identitas->student_name}}" data-id="{{$data->id}}"><i class="fa fa-check"></i></a>
							@endif
                            @endif
                        </td>
						@endif
                    </tr>
                    @endforeach
                </tbody>
                <!-- <tfoot>
                    <tr>
                        <th colspan="3">Total Diterima</th>
                        <th>Rp 8.500.000</th>
                    </tr>
                </tfoot> -->
            </table>
          </div>
      </div>
    </div>
  </div>
</div>

@if($editable)
<!-- Modal Acc -->
<div id="ubahKategori" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <form action="{{route($route.'.update')}}" method="POST">
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="material-icons">&#xe5ca;</i>
                    </div>
                    <h4 class="modal-title w-100">Setujui Perubahan Transaksi</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                <p id="name"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        @csrf
                        <input type="text" name="id" id="id" class="id" hidden/>
                    <button type="submit" class="btn btn-success">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Acc -->
<div id="tolakModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <form action="{{route($route.'.destroy')}}" method="POST">
                @method('DELETE')
                <div class="modal-header flex-column">
                    <div class="icon-box">
                        <i class="material-icons">&#xE5CD;</i>
                    </div>
                    <h4 class="modal-title w-100">Tolak Perubahan Transaksi</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>

                <div class="modal-body">
                <p id="name"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        @csrf
                        <input type="text" name="id" id="id" class="id" hidden/>
                    <button type="submit" class="btn btn-danger">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal ubahKategori -->
<div id="ubahModal" class="modal fade">
    <div class="modal-dialog modal-confirm">
        <div class="modal-content">
            <form action="{{route($route.'.store')}}" method="POST">
                @method('PUT')
                <div class="modal-header flex-column">
                    {{-- <div class="icon-box">
                        <i class="material-icons">&#xe5ca;</i>
                    </div> --}}
                    <h4 class="modal-title w-100">Ubah Transaksi</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                      <label for="nama_siswa" class="col-form-label">Siswa</label>
                      <input type="text" name="nama_siswa" class="form-control" id="nama_siswa" value="" disabled>
                    </div>

                    <div class="form-group">
                        <label for="jenis_pembayaran" class="col-form-label">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran" class="form-control auto_width" id="jenis_pembayaran" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="1">BMS</option>
                            <option value="2" selected>SPP</option>
                        </select>
                    </div>

                    <input type="hidden" name="id" class="form-control" id="id" value="0" disabled>
                    <input type="hidden" name="is_student" id="is_student" value="1">
                    <input type="hidden" name="total" class="form-control" id="total" value="0" disabled>
                    <div class="form-group">
                      <label for="nominal_siswa" class="col-form-label">Nominal</label>
                      <input type="text" name="nominal_siswa" class="form-control number-separator" id="nominal_siswa" value="0" required>
                    </div>

                    <div class="form-group">
                        <label for="split" class="col-form-label">Split dengan pembayaran lain?</label>
                        <select name="split" class="form-control auto_width" id="split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="0" selected>Tidak</option>
                            <option value="1" >Ya</option>
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="split" class="col-form-label">Kategori</label>
                        <select name="category_split" class="form-control auto_width" id="category_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            <option value="calon" selected>Calon Siswa</option>
                            <option value="siswa" >Siswa</option>
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="unit_split" class="col-form-label">Unit Calon Siswa</label>
                        <select name="unit_split" class="form-control auto_width" id="unit_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                            @foreach (getUnits() as $index => $units)
                                <option value="{{$units->id}}" {{$index==0?'selected':''}}>{{$units->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="siswa_split" class="col-form-label">Pilih Calon Siswa</label>
                        <select name="siswa_split" class="select2-hidden-accessible form-control auto_width" id="siswa_split" style="width:100%;" tabindex="-1" aria-hidden="true">
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                        <label for="jenis_pembayaran_split" class="col-form-label">Jenis Pembayaran</label>
                        <select name="jenis_pembayaran_split" class="select2 form-control auto_width" id="jenis_pembayaran_split" style="width:100%;" tabindex="-1" aria-hidden="true" readonly>
                            <option value="1" selected>BMS</option>
                            <option value="2" class="bg-gray-300" disabled>SPP</option>
                        </select>
                    </div>

                    <div class="form-group split-siswa">
                      <label for="nominal_split" class="col-form-label">Nominal</label>
                      <input type="text" name="nominal_split" class="form-control number-separator" id="nominal_split" value="0" required>
                    </div>

                    <div class="form-group">
                      <label for="refund" class="col-form-label">Refund</label>
                      <input type="text" readonly name="refund" class="form-control number-separator" id="refund" value="0" required>
                    </div>


                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        @csrf
                        <input type="text" name="student_id" id="student_id" class="id" hidden/>
                        <input type="text" name="id" id="id" class="id" hidden/>
                    <button type="submit" class="btn btn-success">Ya</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif
<!--Row-->

@endsection

@section('footjs')
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Easy Number Separator JS -->
<script src="{{ asset('vendor/easy-number-separator/easy-number-separator.js') }}"></script>

<!-- Select2 -->
<script src="{{ asset('vendor/select2/dist/js/select2.min.js') }}"></script>

<!-- Number with Commas -->
<script src="{{ asset('js/number-with-commas.js') }}"></script>

<!-- Page level custom scripts -->
@include('template.footjs.kbm.datatables')

@if($editable)
<script>
    $(document).ready(function()
    {
        $('#ubahKategori').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var name = button.data('name') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
            modal.find('p[id="name"]').text('Apakah Anda yakin akan mengubah kategori transaksi '+name+'?');
        })

        $('#tolakModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attributes
            var name = button.data('name') // Extract info from data-* attributes
            var modal = $(this)
            modal.find('input[name="id"]').val(id)
            modal.find('p[id="name"]').text('Apakah Anda yakin akan menolak perubahan transaksi '+name+'?');
        })

        $('.select2-hidden-accessible').select2({
            theme: 'bootstrap4'
        });

        $('#nominal_siswa').on('change', function() {
            hitungSemua();
        });
        $('#nominal_split').on('change', function() {
            hitungSemua();
        });
        $('#refund').on('change', function() {
            hitungSemua();
        });
        $('#split').on('change', function() {
            var value = this.value;
            $('#nominal_split').val(0);
            hitungSemua();
            if(value == 1){
                $('.split-siswa').show();
            }else{
                $('.split-siswa').hide();
            }
        });
        $('#category_split').on('change', function() {
            var str = this.value;
            if(this.value == 'calon') str += ' Siswa';
            str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                return letter.toUpperCase();
            });
            $('#jenis_pembayaran_split option[value="2"]').prop('disabled',false);
            if($('#jenis_pembayaran_split option[value="2"]').hasClass('bg-gray-300'))
                $('#jenis_pembayaran_split option[value="2"]').removeClass('bg-gray-300');
            $('#jenis_pembayaran_split').removeAttr('readonly');
            if(this.value == 'calon'){
                $('#jenis_pembayaran_split').val(1);
                $('#jenis_pembayaran_split option[value="2"]').prop('disabled',true);
                $('#jenis_pembayaran_split option[value="2"]').addClass('bg-gray-300');
                $('#jenis_pembayaran_split').attr('readonly','readonly');
            }
            $('label[for="unit_split"]').html('Unit '+str);
            $('label[for="siswa_split"]').html('Pilih '+str);
            getSiswaList($('#unit_split').val());
        });

        $('#unit_split').on('change', function() {
            getSiswaList(this.value);
        });

        $('#jenis_pembayaran').on('change', function() {
            getSiswaList($('#unit_split').val());
        });
        $('#jenis_pembayaran_split').on('change', function() {
            getSiswaList($('#unit_split').val());
        });
        
        const unitnya = $('#unit_split').val();
        getSiswaList(unitnya);

        $('#ubahModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id');
            var name = button.data('name');
            var total = button.data('total');
            var student_id = button.data('student_id');
            var origin = button.data('origin');
            var is_student = origin==3?0:1;
            $('#jenis_pembayaran option[value="2"]').prop('disabled',false);
            if($('#jenis_pembayaran option[value="2"]').hasClass('bg-gray-300'))
                $('#jenis_pembayaran option[value="2"]').removeClass('bg-gray-300');
            $('#jenis_pembayaran').removeAttr('readonly');
            if(origin == 3){
                $('#jenis_pembayaran_split').val(1);
                $('#jenis_pembayaran option[value="2"]').prop('disabled',true);
                $('#jenis_pembayaran option[value="2"]').addClass('bg-gray-300');
                $('#jenis_pembayaran').attr('readonly','readonly');
            }
            var modal = $(this);
            modal.find('input[name="id"]').val(id);
            $('#is_student').val(is_student);
            $('#nama_siswa').val(name);
            $('#student_id').val(student_id);
            $('#total').val(total);
            $('#nominal_siswa').val(total);
            $('#nominal_split').val(0);
            $('#refund').val(0);
            $('#split').val(0);
            $('.split-siswa').hide();
            $('#jenis_pembayaran').val(origin==3?1:origin);
            $('#jenis_pembayaran_split').val(origin==1?2:1);
            hitungSemua();
            modal.find('p[id="name"]').text('Apakah Anda yakin akan mengubah data transaksi '+name+'?');
        });
        
    })

    function hitungSemua(){
        var total = parseInt($('#total').val().replace(/\./g, ""));
        var nominal_siswa = parseInt($('#nominal_siswa').val().replace(/\./g, ""));
        var nominal_split = parseInt($('#nominal_split').val().replace(/\./g, ""));
        var refund = total - (nominal_siswa + nominal_split);

        $('#refund').val(numberWithCommas(refund));
        
        if(nominal_siswa > total){
            $('#nominal_siswa').val(numberWithCommas(total-nominal_split));
            $('#refund').val(0);
        }else if(refund < 0){
            $('#nominal_split').val(numberWithCommas(total-nominal_siswa));
            console.log(total-nominal_siswa);
            $('#refund').val(0);
        }
    }

    function getSiswaList(unit){
        const jenis_split = $('#jenis_pembayaran_split').val();
        const jenis = $('#jenis_pembayaran').val();
        const student_id = $('#student_id').val();
        var category = $('#category_split').val();
        if(category == 'calon'){
            jQuery.ajax({
                url: "{{ route('spp.list-calon') }}/"+unit,
                type : "GET",
                beforeSend  : function() {
                    $('#siswa_split').prop('disabled',true);
                },
                success:function(data)
                {
                    $('.option-siswa').remove();
                    data.map((item, index) => {
                        if(item[0] == student_id && jenis == jenis_split){

                        }else{
                            const valuenya = '<option class="option-siswa" value="'+item[0]+'" selected>'+item[1] + ' - ' + item[2]+'</option>';
                            $('#siswa_split').append(valuenya);
                        }
                    });
                    $('#siswa_split').prop('disabled',false);
                }
            });
        }
        else{
            jQuery.ajax({
                url: "{{ route('spp.list-siswa') }}/"+unit,
                type : "GET",
                beforeSend  : function() {
                    $('#siswa_split').prop('disabled',true);
                },
                success:function(data)
                {
                    $('.option-siswa').remove();
                    data.map((item, index) => {
                        if(item[0] == student_id && jenis == jenis_split){

                        }else{
                            const valuenya = '<option class="option-siswa" value="'+item[0]+'" selected>'+item[1] + ' - ' + item[2]+'</option>';
                            $('#siswa_split').append(valuenya);
                        }
                    });
                    $('#siswa_split').prop('disabled',false);
                }
            });
        }
    }
</script>
@endif
@endsection