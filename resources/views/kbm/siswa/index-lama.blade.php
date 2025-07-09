@extends('template.main.master')

@section('title')
Daftar Siswa
@endsection

@section('headmeta')
  <link href="{{ asset('public/buttons.dataTables.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Siswa</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Belajar Mengajar</a></li>
        <li class="breadcrumb-item active" aria-current="page">Siswa</li>
    </ol>
</div>

<div class="row mb-3">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-body">
                @if(Session::has('sukses'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('sukses') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                <div class="row">
                    @if(Request::path()!='kependidikan/kbm/siswa/alumni')
                    <div class="col-md-8">
                    <form action="/kependidikan/kbm/siswa" method="POST">
                    @csrf
                        <div class="form-group row">
                            <label for="kelas" class="col-sm-3 control-label">Tingkat Kelas</label>
                            <div class="col-sm-5">
                                <select name="level" class="select2 form-control select2-hidden-accessible auto_width" id="kelas" style="width:100%;" tabindex="-1" aria-hidden="true">
                                    <option value="semua">Semua</option>
                                    @foreach( $levels as $tingkat)
                                    @if( $level == $tingkat->id )
                                        <option value="{{$tingkat->id}}" selected>{{$tingkat->level}}</option>
                                    @else
                                    <option value="{{$tingkat->id}}">{{$tingkat->level}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <button class="btn btn-brand-purple-dark btn-sm" type="submit">Saring</button>
                        </div>
                    </form>
                    </div>
                    @endif
                    <div class="table-responsive">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-brand-purple">Siswa</h6>
                            @if( in_array((auth()->user()->role_id), array(1,5)))
                            <a class="m-0 float-right btn btn-brand-purple-dark btn-sm" href="/kependidikan/kbm/siswa/tambah">Tambah <i class="fas fa-plus"></i></a>
                            @endif
                        </div>
                        @if($siswas->isEmpty())
                        @else
                        <table id="dataTable" class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    {{-- <th id="hide" style="display:none" >ID</th>
                                    <th id="hide" style="display:none" >No Pendaftaran</th>
                                    <th id="hide" style="display:none" >Program</th>
                                    <th id="hide" style="display:none" >Tanggal Daftar</th>
                                    <th id="hide" style="display:none" >Tahun Ajaran</th>
                                    <th id="hide" style="display:none" >Tingkat Kelas</th> --}}
                                    <th>NIPD</th>
                                    <th>NISN</th>
                                    <th>Nama</th>
                                    {{-- <th id="hide" style="display:none" >Nama Panggilan</th>
                                    <th id="hide" style="display:none" >Tempat Lahir</th> --}}
                                    <th>Tanggal Lahir</th>
                                    <th>Jenis Kelamin</th>
                                    {{-- <th id="hide" style="display:none" >Agama</th>
                                    <th id="hide" style="display:none" >Anak Ke</th>
                                    <th id="hide" style="display:none" >Status Anak</th>
                                    <th id="hide" style="display:none" >Alamat</th>
                                    <th id="hide" style="display:none" >No</th>
                                    <th id="hide" style="display:none" >RT</th>
                                    <th id="hide" style="display:none" >RW</th>
                                    <th id="hide" style="display:none" >Wilayah</th>
                                    <th id="hide" style="display:none" >Nama Ayah</th>
                                    <th id="hide" style="display:none" >NIK Ayah</th>
                                    <th id="hide" style="display:none" >HP Ayah</th>
                                    <th id="hide" style="display:none" >Email Ayah</th>
                                    <th id="hide" style="display:none" >Pekerjaan Ayah</th>
                                    <th id="hide" style="display:none" >Jabatan Ayah</th>
                                    <th id="hide" style="display:none" >Telp Kantor Ayah</th>
                                    <th id="hide" style="display:none" >Alamat Kantor Ayah</th>
                                    <th id="hide" style="display:none" >Gaji Ayah</th>
                                    <th id="hide" style="display:none" >Nama Ibu</th>
                                    <th id="hide" style="display:none" >NIK Ibu</th>
                                    <th id="hide" style="display:none" >HP Ibu</th>
                                    <th id="hide" style="display:none" >HP Email Ibu</th>
                                    <th id="hide" style="display:none" >Pekerjaan Ibu</th>
                                    <th id="hide" style="display:none" >Jabatan Ibu</th>
                                    <th id="hide" style="display:none" >Telp Kantor Ibu</th>
                                    <th id="hide" style="display:none" >Alamat Kantor Ibu</th>
                                    <th id="hide" style="display:none" >Gaji Ibu</th>
                                    <th id="hide" style="display:none" >NIP (Orang tua yang bekerja di Auliya)</th>
                                    <th id="hide" style="display:none" >Alamat Orang Tua</th>
                                    <th id="hide" style="display:none" >HP Alternatif</th>
                                    <th id="hide" style="display:none" >Nama Wali</th>
                                    <th id="hide" style="display:none" >NIK Wali</th>
                                    <th id="hide" style="display:none" >HP Wali</th>
                                    <th id="hide" style="display:none" >HP Email Wali</th>
                                    <th id="hide" style="display:none" >Pekerjaan Wali</th>
                                    <th id="hide" style="display:none" >Jabatan Wali</th>
                                    <th id="hide" style="display:none" >Telp Kantor Wali</th>
                                    <th id="hide" style="display:none" >Alamat Kantor Wali</th>
                                    <th id="hide" style="display:none" >Gaji Wali</th>
                                    <th id="hide" style="display:none" >Alamat Wali</th>
                                    <th id="hide" style="display:none" >Asal Sekolah</th>
                                    <th id="hide" style="display:none" >Saudara Kandung</th>
                                    <th id="hide" style="display:none" >Nama Saudara</th>
                                    <th id="hide" style="display:none" >Info Dari</th>
                                    <th id="hide" style="display:none" >Nama</th>
                                    <th id="hide" style="display:none" >Posisi</th>
                                    <th id="hide" style="display:none" >class_id</th> --}}
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach( $siswas as $index => $siswa)
                                <tr>
                                    {{-- <td id="hide" style="display:none" >{{ $siswa->id}}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->reg_number}}</td>
                                    <th id="hide" style="display:none" >{{ $siswa->unit->name }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->join_date }}</th>
                                    <td id="hide" style="display:none" >{{ $siswa->semester_id?$siswa->semester->semester_id:'-' }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->level_id?$siswa->level->level:'-' }}</td> --}}
                                    <td>
                                    {{ $siswa->student_nis }}
                                    </td>
                                    <td>{{ $siswa->student_nisn }}</td>
                                    <td>{{ $siswa->identitas->student_name }}</td>
                                    {{-- <td id="hide" style="display:none" >{{ $siswa->identitas->student_nickname }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->birth_place }}</td> --}}
                                    <td>{{ $siswa->identitas->birth_date }}</td>
                                    <td>{{ $siswa->identitas->gender_id?ucwords($siswa->identitas->jeniskelamin->name):'' }}</td>
                                    {{-- <td id="hide" style="display:none" >{{ $siswa->religion_id?$siswa->agama->name:'-' }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->child_of }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->family_status }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->address }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->address_number }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->rt }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->rw }}</td>
                                    @if($siswa->identitas->region_id != null)
                                        <td id="hide" style="display:none" >
                                        {{ $siswa->identitas->wilayah->name }}, {{ $siswa->identitas->wilayah->kecamatanName() }}, {{ $siswa->identitas->wilayah->kabupatenName() }}, {{ $siswa->identitas->wilayah->provinsiName() }}
                                        </td>
                                    @endif
                                    @if($siswa->identitas->region_id == null)
                                    <td id="hide" style="display:none" ></td>
                                    @endif
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->father_name }}</td>
                                    <td id="hide" style="display:none" >{{ strlen($siswa->identitas->orangtua->father_nik)>20?decrypt($siswa->identitas->orangtua->father_nik):$siswa->identitas->orangtua->father_nik }}</td>
                                    <td id="hide" style="display:none" >{{ strlen($siswa->identitas->orangtua->father_phone)>20?decrypt($siswa->identitas->orangtua->father_phone):$siswa->identitas->orangtua->father_phone }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->father_phone }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->father_email }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->father_job }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->father_position }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->father_phone_office }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->father_job_address }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->father_salary }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->mother_name }}</td>
                                    <td id="hide" style="display:none" >{{ strlen($siswa->identitas->orangtua->mother_nik)>20?decrypt($siswa->identitas->orangtua->mother_nik):$siswa->identitas->orangtua->mother_nik }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->orangtua->mother_nik }}</td>
                                    <td id="hide" style="display:none" >{{ strlen($siswa->identitas->orangtua->mother_phone)>20?decrypt($siswa->identitas->orangtua->mother_phone):$siswa->identitas->orangtua->mother_phone }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->mother_phone }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->mother_email }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->mother_job }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->mother_position }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->mother_phone_office }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->mother_job_address }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->mother_salary }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->employee_id }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->parent_address }}</td>
                                    <td id="hide" style="display:none" >{{ strlen($siswa->identitas->orangtua->parent_address)>20?decrypt($siswa->identitas->orangtua->parent_address):$siswa->identitas->orangtua->parent_address }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->orangtua->parent_address }}</td>
                                    <td id="hide" style="display:none" >{{ strlen($siswa->identitas->orangtua->parent_phone_number)>20?decrypt($siswa->identitas->orangtua->parent_phone_number):$siswa->identitas->orangtua->parent_phone_number }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->parent_phone_number }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_name }}</td>
                                    <td id="hide" style="display:none" >{{ strlen($siswa->identitas->orangtua->guardian_nik)>20?decrypt($siswa->identitas->orangtua->guardian_nik):$siswa->identitas->orangtua->guardian_nik }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->orangtua->guardian_nik }}</td>
                                    <td id="hide" style="display:none" >{{ strlen($siswa->identitas->orangtua->guardian_phone_number)>20?decrypt($siswa->identitas->orangtua->guardian_phone_number):$siswa->identitas->orangtua->guardian_phone_number }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_phone_number }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_email }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_job}}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_position }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_phone_office }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_job_address }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_salary }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->orangtua->guardian_address }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->origin_school }}, {{ $siswa->origin_school_address }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->identitas->sibling_name?$siswa->identitas->sibling_name:'-' }}</td>
                                    <td id="hide" style="display:none" >{{ ($siswa->identitas->sibling_level_id)?$siswa->identitas->levelsaudara->level:'-' }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->info_from }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->info_name }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->position }}</td>
                                    <td id="hide" style="display:none" >{{ $siswa->class_id }}</td> --}}
                                    <td>
                                        <a href="../siswa/lihat/{{ $siswa->id }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>&nbsp;
                                        @if( in_array((auth()->user()->role_id), array(1,7,18,30,31)))
                                        <a href="../siswa/ubah/{{ $siswa->id }}" class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>&nbsp;
                                        @endif
                                        @if( in_array((auth()->user()->role_id), array(1,2)))
                                        <a href="#" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#HapusModal{{$siswa->id}}"><i class="fas fa-trash"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@if( in_array((auth()->user()->role_id), array(1,2,18)))
@foreach( $siswas as $index => $siswa)
<!-- Modal Hapus -->
<div id="HapusModal{{$siswa->id}}" class="modal fade">
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
                <p>Apakah Anda yakin akan menghapus data {{$siswa->student_name}}?.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form action="/kependidikan/kbm/siswa/hapus/{{$siswa->id}}" method="POST">
                    @csrf
                    <input type="hidden" id="hapusid" name="id">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif
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
<!-- <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script> -->

<!-- Page level custom scripts -->
@include('template.footjs.kbm.cetakdatatables')
@include('template.footjs.kbm.hideelement')
@endsection