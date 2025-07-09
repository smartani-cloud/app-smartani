@extends('penilaian.iku_edukasi_persen_index')

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
                @if(count($kelasList) > 0 && $mataPelajaran && count($mataPelajaran) > 0)
                <div class="table-responsive">
                    <table id="dataTable" class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th rowspan="3">Kelas</th>
                                <th colspan="{{ count($mataPelajaran) > 0 ? count($mataPelajaran) : '1' }}">Mata Pelajaran</th>
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
                                <th>{{ $m->subject_name }}</th>
                                @endforeach
                                @endif
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($mataPelajaran as $m)
                                <th>Prosentase Pencapaian</th>
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
                                <td>{{ $nilai && $nilai['checked'] ? $nilai['percentage'].'%' : (!$nilai['checked'] ? '-' : '') }}</td>
                                @endforeach
                                @endif
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @elseif(count($kelasList) < 1)
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data kelas yang ditemukan</h6>
                </div>
                @else
                <div class="text-center mx-3 mt-4 mb-5">
                    <h3 >Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data mata pelajaran yang ditemukan</h6>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
