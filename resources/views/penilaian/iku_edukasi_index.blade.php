@extends('template.main.master')

@section('title')
Ledger Kelas 
@endsection

@section('headmeta')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/buttons.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('vendor/datatables-button/jszip/datatables.min.css') }}" rel="stylesheet">
@endsection

@section('topbarpenilaian')
@include('template.topbar.gurumapel')
@endsection

@section('sidebar')
@include('template.sidebar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Ledger Kelas</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Penilaian</a></li>
        @if($semester || $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.ikuEdukasi.kelas') }}">Ledger Kelas</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">Ledger Kelas</li>
        @endif
        @if($semester)
        @if($kelasList && $kelas)
        <li class="breadcrumb-item"><a href="{{ route('penilaian.ikuEdukasi.kelas', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber])}}">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</a></li>
        @else
        <li class="breadcrumb-item active" aria-current="page">{{ $semester->semester_id . ' (' .$semester->semester.')' }}</li>
        @endif
        @if($kelas)
        <li class="breadcrumb-item active" aria-current="page">{{ $kelas->level->level.' '.$kelas->namakelases->class_name }}</li>
        @endif
        @endif
    </ol>
</div>

<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="yearOpt" class="form-control-label">Tahun Pelajaran</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    <div class="input-group">
                    <select aria-label="Tahun" name="tahun" class="form-control" id="yearOpt">
                      @foreach($semesterList as $s)
                      @if($s->is_active == 1 || ($s->is_active != 1 && $s->riwayatKelas()->count() > 0))
                      <option value="{{ $s->semesterLink }}" {{ $semester->id == $s->id ? 'selected' : '' }}>{{ $s->semester_id . ' (' .$s->semester.')' }}</option>
                      @endif
                      @endforeach
                    </select>
                    <a href="{{ route('penilaian.ikuEdukasi.kelas') }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.ikuEdukasi.kelas') }}">Atur</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
@if($semester)
<div class="row mb-4">
  <div class="col-12">
    <div class="card">
      <div class="card-body p-3">
          <div class="row">
            <div class="col-lg-8 col-md-10 col-12">
              <div class="form-group mb-0">
                <div class="row mb-2">
                  <div class="col-lg-3 col-md-4 col-12">
                    <label for="classOpt" class="form-control-label">Kelas</label>
                  </div>
                  <div class="col-lg-6 col-md-6 col-12">
                    @if(Auth::user()->role->name == 'guru')
                    {{ $kelas->levelName }}
                    @else
                    <div class="input-group">
                        <select aria-label="Kelas" name="kelas" class="form-control" id="classOpt">
                          @foreach($kelasList as $k)
                          <option value="{{ $k->id }}" {{ $kelas && $kelas->id == $k->id ? 'selected' : '' }}>{{ $k->levelName }}</option>
                          @endforeach
                        </select>
                        <a href="{{ route('penilaian.ikuEdukasi.kelas', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}" id="btn-select-class" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('penilaian.ikuEdukasi.kelas', ['tahun' => $semester->tahunAjaran->academicYearLink, 'semester' => $semester->semesterNumber]) }}">Pilih</a>
                    </div>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
@endif

@if($semester && $kelas)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Siswa</h6>
                <!-- <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="{{ route('penilaian.ikuEdukasi.kelas') }}">Ekspor <i class="fas fa-file-export ml-1"></i></a> -->
            </div>
            <div class="card-body p-3">
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
                @if($kelas->riwayat()->where('semester_id',$semester->id)->count() > 0)
                <div class="table-responsive">
                    <table id="dataTable" class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 15px" rowspan="2">#</th>
                                <th rowspan="2">Nama</th>
                                <th colspan="{{ count($mataPelajaran) }}">Nilai Pengetahuan</th>
                                <th rowspan="2">Jumlah Nilai Pengetahuan</th>
                                <th rowspan="2">Rerata Nilai Pengetahuan</th>
                                <th rowspan="2">Ranking</th>
                                <th colspan="{{ count($mataPelajaran) }}">Nilai Keterampilan</th>
                                <th rowspan="2">Jumlah Nilai Keterampilan</th>
                                <th rowspan="2">Rerata Nilai Keterampilan</th>
                            </tr>
                            <tr>
                                <!-- Nilai Pengetahuan -->
                                @foreach($mataPelajaran as $m)
                                <th>{{ $m->subject_acronym }}</th>
                                @endforeach
                                <!-- Nilai Keterampilan -->
                                @foreach($mataPelajaran as $m)
                                <th>{{ $m->subject_acronym }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $no = 1;
                            $siswas = $ranks = null;
                            $riyawatKelas = $kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->with(['siswa' => function ($q){$q->select('id','student_id')->with('identitas:id,student_name');}])->get()->sortBy('siswa.identitas.student_name');
                            foreach($riyawatKelas as $r){
                                $s = $r->siswa()->select('id')->first();
                                $rapor = $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                                
                                $totalPengetahuan = 0;
                                
                                foreach($mataPelajaran as $m){
                                    $pengetahuan = $rapor ? $rapor->pengetahuan()->select('score_knowledge')->where('subject_id',$m->id)->whereNotNull('score_knowledge')->first() : null;
                                    $score_knowledge = $pengetahuan ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '-';
                                    if($score_knowledge != '-'){
                                        $totalPengetahuan += $score_knowledge;
                                    }
                                }
                                
                                $siswa = collect([
                                    [
                                        'id' => $s->id,
                                        'total' => $totalPengetahuan
                                    ]
                                ]);
                                if($siswas){
                                    $siswas = $siswas->concat($siswa);
                                }
                                else{
                                    $siswas = $siswa;
                                }
                            }
                            if($siswas){
                                $ranks = collect($siswas->sortByDesc('total')->values()->all());
                                $ranks = $ranks->map(function($item, $index) {
                                    $item['rank'] = $index + 1;
                                    return $item;
                                });
                            }
                            @endphp
                            @foreach($kelas->riwayat()->select('student_id')->where('semester_id',$semester->id)->has('siswa')->with(['siswa'=>function($q){return $q->select('id','student_id');}])->get()->pluck('siswa') as $s)
                            @php
                            $rapor = $s->nilaiRapor()->select('id')->where('semester_id', $semester->id)->first();
                            $totalKeterampilan = 0;
                            @endphp
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>{{ $s->identitas->student_name }}</td>
                                @foreach($mataPelajaran as $m)
                                @php
                                $pengetahuan = $rapor ? $rapor->pengetahuan()->select('score_knowledge')->where('subject_id',$m->id)->whereNotNull('score_knowledge')->first() : null;
                                $score_knowledge = $pengetahuan ? number_format((float)$pengetahuan->score_knowledge, 0, ',', '') : '-';
                                @endphp
                                <td>{{ $score_knowledge }}</td>
                                @endforeach
                                @php
                                $rank = $ranks ? $ranks->where('id',$s->id)->first() : null;
                                @endphp
                                <td>{{ $rank ? $rank['total'] : '-' }}</td>
                                <td>{{ $rank ? number_format((float)($rank['total']/(count($mataPelajaran))), 0, ',', '') : '-' }}</td>
                                <td>{{ $rank ? $rank['rank'] : '-' }}</td>
                                @foreach($mataPelajaran as $m)
                                @php
                                $keterampilan = $rapor ? $rapor->keterampilan()->select('mean')->where('subject_id',$m->id)->whereNotNull('mean')->first() : null;
                                $score_skill = $keterampilan ? number_format((float)$keterampilan->mean, 0, ',', '') : '-';
                                if($score_skill != '-'){
                                    $totalKeterampilan += $score_skill;
                                }
                                @endphp
                                <td class="text-center">{{ $score_skill }}</td>
                                @endforeach
                                <td>{{ $totalKeterampilan }}</td>
                                <td>{{ number_format((float)($totalKeterampilan/(count($mataPelajaran))), 0, ',', '') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data siswa yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
<!--Row-->
@endsection

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
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.global.datatables-button')
@if($semester && $kelasList)
@include('template.footjs.kependidikan.change-class')
@endif
@include('template.footjs.keuangan.change-year')
@endsection
