@extends('template.main.master')

@section('title')
Detail {{ $active }}
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penerimaan Siswa Baru</a></li>
        <li class="breadcrumb-item"><a href="{{ route($route.'.index') }}">{{ $active }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $data->id }}</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card h-100">
            <div class="card-header py-3 bg-brand-green d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-white">Detail {{ $active }}</h6>
                @if(in_array(auth()->user()->role->name,['aspv']))
                <a href="{{ route($route.'.edit',['id' => $data->id]) }}" class="m-0 float-right btn btn-brand-green-dark btn-sm"><i class="fas fa-pen mr-2"></i>Ubah</a>
                @endif
            </div>
            <div class="card-body p-4">
                @if(Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Sukses!</strong> {{ Session::get('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold text-brand-green">Info Orang Tua</h6>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold"><i class="fas fa-portrait mr-2"></i>Ayah</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Nama Ayah
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->father_name }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        NIK Ayah
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->father_nik }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Email Ayah
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->father_email }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Nomor Seluler Ayah
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->father_phone }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Pekerjaan Ayah
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->father_job }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Jabatan Ayah
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->father_position }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Alamat Kantor Ayah
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->father_job_address }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Telepon Kantor Ayah
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->father_phone_office }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Gaji Ayah
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->father_salary }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-2 mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold"><i class="fas fa-portrait mr-2"></i>Ibu</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Nama Ibu
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->mother_name }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        NIK Ibu
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->mother_nik }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Email Ibu
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->mother_email }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Nomor Seluler Ibu
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->mother_phone }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Pekerjaan Ibu
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->mother_job }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Jabatan Ibu
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->mother_position }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Alamat Kantor Ibu
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->mother_job_address }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Telepon Kantor Ibu
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->mother_phone_office }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Gaji Ibu
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->mother_salary }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row mt-2 mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold"><i class="fas fa-home mr-2"></i>Alamat & Kontak</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Alamat Orang Tua
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->parent_address }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Telepon Alternatif
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->parent_phone_number }}
                      </div>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold text-brand-green">Info Wali</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Nama Wali
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->guardian_name }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        NIK Wali
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->guardian_nik }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Alamat Wali
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->guardian_address }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Email Wali
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->guardian_email }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Nomor Seluler Wali
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->guardian_phone_number }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Pekerjaan Wali
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->guardian_job }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Jabatan Wali
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->guardian_position }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Alamat Kantor Wali
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        {{ $data->guardian_job_address }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Telepon Kantor Wali
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->guardian_phone_office }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Gaji Wali
                      </div>
                      <div class="col-lg-6 col-md-8 col-12">
                        {{ $data->guardian_salary }}
                      </div>
                    </div>
                  </div>
                </div>
                @if($data->users()->where('role_id',36)->count() > 0)
                <hr>
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold text-brand-green">Info Akun</h6>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Username
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        @php $i = 1 @endphp
                        @foreach($data->users()->where('role_id',36)->get() as $login)
                        {{ $login->username }}{!! $i++ <= $data->users()->where('role_id',36)->count() ? '<br>' : null !!}
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
                @endif
                @if($data->childrensCount > 0)
                <hr>
                <div class="row mb-3">
                  <div class="col-12">
                    <h6 class="font-weight-bold text-brand-green">Info Anak</h6>
                  </div>
                </div>
                @if($data->siswas()->count())
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Siswa
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        @php $i = 1 @endphp
                        @foreach($data->siswas()->select('id','student_name')->orderBy('birth_date','desc')->get() as $siswa)
                        @if(in_array(auth()->user()->role_id,[11,12,13,15,16,18,29,30,31]) || (in_array(auth()->user()->role_id,[1,2,3,7,9]) && auth()->user()->pegawai->unit_id == $siswa->latestUnit))
                        {{ $i++.'. ' }}<a href="{{ route('kependidikan.kbm.siswa.show',['id' => $siswa->id]) }}" target="_blank" class="text-decoration-none text-info">{{ $siswa->student_name }}</a>{{ $siswa->latestLevel ? ' - Kelas '.$siswa->latestLevel : null }}{!! $i <= $data->siswas()->count() ? '<br>' : null !!}
                        @else
                        {{ $i++.'. '.$siswa->student_name.($siswa->latestLevel ? ' - Kelas '.$siswa->latestLevel : null) }}{!! $i <= $data->siswas()->count() ? '<br>' : null !!}
                        @endif
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
                @endif
                @if($data->calonSiswa()->count())
                <div class="row">
                  <div class="col-lg-10 col-md-12">
                    <div class="row mb-3">
                      <div class="col-lg-3 col-md-4 col-12">
                        Calon Siswa
                      </div>
                      <div class="col-lg-9 col-md-8 col-12">
                        @php $i = 1 @endphp
                        @foreach($data->calonSiswa()->select('id','student_name','unit_id','level_id')->orderBy('birth_date','desc')->get() as $calon)
                        @if(in_array(auth()->user()->role_id,[11,12,13,14,17,18,20,21,25,26]) || (in_array(auth()->user()->role_id,[1,2,3,7,8,9]) && auth()->user()->pegawai->unit_id == $calon->unit_id))
                        {{ $i++.'. ' }}<a href="{{ route('kependidikan.psb.calonsiswa.lihat',['id' => $calon->id]) }}" target="_blank" class="text-decoration-none text-info">{{ $calon->student_name }}</a>{{ $calon->level ? ' - '.($calon->unit_id != 1 ? 'Kelas ' : null).$calon->level->level : null }}{!! $i <= $data->calonSiswa()->count() ? '<br>' : null !!}
                        @else
                        {{ $i++.'. '.$calon->student_name.($calon->level ? ' - '.($calon->unit_id != 1 ? 'Kelas ' : null).$calon->level->level : null) }}{!! $i <= $data->calonSiswa()->count() ? '<br>' : null !!}
                        @endif
                        @endforeach
                      </div>
                    </div>
                  </div>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
<!--Row-->

@endsection
