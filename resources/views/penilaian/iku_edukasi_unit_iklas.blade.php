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
                                @php
                                $competences = $refIklas->groupBy('competence')->keys()->all();
                                @endphp
                                @foreach($competences as $c)
                                @php
                                $categoryCount = $refIklas->where('competence',$c)->count();
                                @endphp
                                <th colspan="{{ $categoryCount > 0 ? ($categoryCount*2) : '2'}}">{{ $c }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($refIklas as $i)
                                <th colspan="2">{{ $i->categoryNumber.' '.$i->category }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($refIklas as $i)
                                <th>Rerata Rapor Kelas</th>
                                <th>Rerata Level</th>
                                @endforeach
                        </thead>
                        <tbody>
                            @php
                            $levelActive = null;
                            @endphp
                            @foreach($kelasList->sortBy('levelName')->all() as $k)
                            <tr>
                                <td>{{ $k->levelName }}</td>
                                @foreach($refIklas as $i)
                                @php
                                $nilai = $classes ? $classes->where('id',$k->id)->where('iklas_ref_id',$i->id)->first() : null;
                                @endphp
                                <td>
                                    @if($nilai)
                                    {{ $nilai['avg'] }}<i class="fas fa-star ml-2"></i>
                                    @else
                                    {{ '-' }}
                                    @endif
                                </td>
                                @if(!$levelActive || ($levelActive && ($levelActive != $k->level->level)))
                                @php
                                $levelActive = $k->level->level;
                                $classActive = $k->id;
                                @endphp
                                @endif
                                @if($classActive == $k->id)
                                @php
                                $thisLevelClasses = $kelasList->where('level_id',$k->level_id);
                                $thisLevelActiveClasses = $classes->whereIn('id',$thisLevelClasses->pluck('id'))->where('iklas_ref_id',$i->id)->count();
                                $rowspan = $thisLevelClasses->count();
                                $sumAverage = $classes->whereIn('id',$thisLevelClasses->pluck('id'))->where('iklas_ref_id',$i->id)->sum('avg');
                                if($thisLevelActiveClasses > 0 && $sumAverage < 1) $average = '0';
                                else{
                                  $average = $thisLevelActiveClasses > 0 ? number_format((float)($sumAverage/$thisLevelActiveClasses), 0, ',', '') : '-';
                                }
                                @endphp
                                <td rowspan="{{ $rowspan }}">{{ $average }}<i class="fas fa-star ml-2"></i></td>
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
