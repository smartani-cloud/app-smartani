@extends('penilaian.iku_edukasi_unit_index')

@section('ledger')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Daftar Kelas</h6>
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
                @if(count($kelasList) > 0)
                <div class="table-responsive">
                    <table id="dataTable" class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th rowspan="3">Kelas</th>
                                <th colspan="{{ count($mataPelajaran) > 0 ? (count($mataPelajaran)*2) : '2'}}">Mata Pelajaran</th>
                            </tr>
                            <tr>
                                @foreach($kelompok as $kel)
                                @if($kel->matapelajarans()->count())
                                @php
                                $matapelajarans = $kel->matapelajarans()->select('subject_name')->whereNull('is_mulok')->orderBy('subject_number')->get();
                                $mulok = $kel->matapelajarans()->select('subject_name')->mulok()->orderBy('subject_number');
                                if($mulok->count() > 0){
                                    $matapelajarans = $matapelajarans->concat($mulok->get());
                                }
                                @endphp
                                @foreach($matapelajarans as $m)
                                <th colspan="2">{{ $m->subject_name }}</th>
                                @endforeach
                                @endif
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($mataPelajaran as $m)
                                <th>Rerata Rapor Kelas</th>
                                <th>Rerata Level</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $levelActive = null;
                            @endphp
                            @foreach($kelasList->sortBy('levelName')->all() as $k)
                            <tr>
                                <td>{{ $k->levelName }}</td>
                                @foreach($kelompok as $kel)
                                @if($kel->matapelajarans()->count())
                                @php
                                $matapelajarans = $kel->matapelajarans()->select('id')->whereNull('is_mulok')->orderBy('subject_number')->get();
                                $mulok = $kel->matapelajarans()->select('id')->mulok()->orderBy('subject_number');
                                if($mulok->count() > 0){
                                    $matapelajarans = $matapelajarans->concat($mulok->get());
                                }
                                @endphp
                                @foreach($matapelajarans as $m)
                                @php
                                $nilai = $classes ? $classes->where('id',$k->id)->where('subject_id',$m->id)->first() : null;
                                @endphp
                                <td>{{ $nilai && $nilai['checked'] ? $nilai['avg'] : (!$nilai['checked'] ? '-' : '') }}</td>
                                @if(!$levelActive || ($levelActive && ($levelActive != $k->level->level)))
                                @php
                                $levelActive = $k->level->level;
                                $classActive = $k->id;
                                @endphp
                                @endif
                                @if($classActive == $k->id)
                                @php
                                $thisLevelClasses = $kelasList->where('level_id',$k->level_id);
                                $thisLevelActiveClasses = $classes->whereIn('id',$thisLevelClasses->pluck('id'))->where('subject_id',$m->id)->where('checked',true)->count();
                                $rowspan = $thisLevelClasses->count();
                                $sumAverage = $classes->whereIn('id',$thisLevelClasses->pluck('id'))->where('subject_id',$m->id)->sum('avg');
                                if($thisLevelActiveClasses > 0 && $sumAverage < 1) $average = '0';
                                else{
                                  $average = $thisLevelActiveClasses > 0 ? number_format((float)($sumAverage/$thisLevelActiveClasses), 0, ',', '') : '-';
                                }
                                @endphp
                                <td rowspan="{{ $rowspan }}">{{ $average }}</td>
                                @endif
                                @endforeach
                                @endif
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data kelas yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
