@extends('penilaian.iku_edukasi_grafik_index')

@section('ledger')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Grafik</h6>
            </div>
            <div class="card-body p-3">
                <div class="chartWrapper">
                    <div class="chartAreaWrapper">
                        <div class="chartAreaInnerWrapper">
                            <canvas id="percentBarChart"></canvas>
                        </div>
                    </div>
                    <canvas id="percentBarChartAxis" height="350" width="0"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
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
                                <th colspan="{{ count($mataPelajaran) > 0 ? count($mataPelajaran) : '1' }}">Mata Pelajaran</th>
                            </tr>
                            <tr>
                                @foreach($matapelajarans as $m)
                                <th>{{ $m->subject_name }}</th>
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
                                @foreach($matapelajarans as $m)
                                @php
                                $nilai = $classes ? $classes->where('id',$k->id)->where('subject_id',$m->id)->first() : null;
                                @endphp
                                <td>{{ $nilai && $nilai['checked'] ? $nilai['percentage'].'%' : (!$nilai['checked'] ? '-' : '') }}</td>
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
