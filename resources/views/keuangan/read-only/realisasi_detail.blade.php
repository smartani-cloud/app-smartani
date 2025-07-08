@extends('template.main.master')

@section('title')
Realisasi
@endsection

@section('headmeta')
<meta name="csrf-token" content="{{ Session::token() }}" />
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-2">
  <h1 class="h3 mb-0 text-gray-800">Realisasi</h1>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('keuangan.index')}}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index')}}">Realisasi</a></li>
    @if($jenisAktif)
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index', ['jenis' => $jenisAktif->link])}}">{{ $jenisAktif->name }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('realisasi.index', ['jenis' => $jenisAktif->link,'tahun' => !$isYear ? $tahun->academicYearLink : $tahun])}}">{{ !$isYear ? $tahun->academic_year : $tahun }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $anggaranAktif->anggaran->name }}</li>
    @endif
  </ol>
</div>
{{--
<div class="row">
    @foreach($jenisAnggaran as $j)
    @if($jenisAktif == $j)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-brand-green">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled="disabled">Pilih</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    @php
    $anggaranCount = $jenisAnggaranCount->where('id',$j->id)->pluck('anggaranCount')->values()->first();
    @endphp
    @if($anggaranCount > 0)
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-brand-green">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('realisasi.index', ['jenis' => $j->link])}}" class="btn btn-sm btn-outline-brand-green">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body p-0">
                <div class="row align-items-center mx-0">
                    <div class="col-auto px-3 py-2 bg-secondary">
                        <i class="mdi mdi-file-document-outline mdi-24px text-white"></i>
                    </div>
                    <div class="col">
                        <div class="h6 mb-0 font-weight-bold text-gray-800">{{ $j->name }}</div>
                    </div>
                    <div class="col-auto">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary disabled"role="button" aria-disabled="true">Pilih</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif
    @endforeach
</div>
--}}
@if($jenisAktif)
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
                      @if($years && count($years) > 0)
                      @foreach($years as $y)
                        <option value="{{ $y }}" {{ $isYear && $tahun == $y ? 'selected' : ''}}>{{ $y }}</option>
                      @endforeach
                      @elseif($isYear)
                      @if($tahun != date('Y'))
                      <option value="" disabled="disabled" selected>Pilih</option>
                      @endif
                      <option value="{{ date('Y') }}" {{ $tahun == date('Y') ? 'selected' : '' }}>{{ date('Y') }}</option>
                      @endif
                      @if((!$academicYears && !$isYear) || ($academicYears && count($academicYears) > 0))
                      @foreach($tahunPelajaran as $t)
                      <option value="{{ $t->academicYearLink }}" {{ !$isYear && $tahun->id == $t->id ? 'selected' : '' }}>{{ $t->academic_year }}</option>
                      @endforeach
                      @endif
                    </select>
                    <a href="{{ route('realisasi.index', ['jenis' => $jenisAktif->link]) }}" id="btn-select-year" class="btn btn-brand-green ml-2 pt-2" data-href="{{ route('realisasi.index', ['jenis' => $jenisAktif->link]) }}">Pilih</a>
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

<div class="row">
    <div class="{{ $apbyAktif && $apbyAktif->detail()->count() > 0 && (($isKso && $apbyAktif->director_acc_status_id == 1) || (!$isKso && $apbyAktif->president_acc_status_id == 1))? '' : 'col-lg-6 ' }}col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Pengguna Anggaran</div>
                        <h6 class="mb-0">{{ $anggaranAktif->anggaran->accJabatan->name }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="{{ $apbyAktif && $apbyAktif->detail()->count() > 0 && (($isKso && $apbyAktif->director_acc_status_id == 1) || (!$isKso && $apbyAktif->president_acc_status_id == 1))? 'col-lg-4 ' : 'col-lg-6 ' }}col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle {{ $apbyAktif && $apbyAktif->detail()->count() > 0 ? 'bg-brand-green' : 'bg-secondary' }}">
                          <i class="fas fa-calculator text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Rencana</div>
                        <h6 id="summary" class="mb-0">
                            @if($apbyAktif && $apbyAktif->detail()->count() > 0)
                            @if(($isKso && $apbyAktif->director_acc_status_id != 1) || (!$isKso && $apbyAktif->president_acc_status_id != 1))
                            {{ number_format($total->get('anggaran'), 0, ',', '.') }}
                            @else
                            {{ $apbyAktif->totalValueWithSeparator }}
                            @endif
                            @else
                            0
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($apbyAktif && $apbyAktif->detail()->count() > 0 && (($isKso && $apbyAktif->director_acc_status_id == 1) || (!$isKso && $apbyAktif->president_acc_status_id == 1)))
	<div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-shopping-cart text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Realisasi</div>
                        <h6 class="mb-0">
							@if(($isKso && $apbyAktif->director_acc_status_id != 1) || (!$isKso && $apbyAktif->president_acc_status_id != 1))
                            0
                            @else
                            {{ $apbyAktif->totalUsedWithSeparator }}
                            @endif
						</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-12 mb-4">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <div class="icon-circle bg-brand-green">
                          <i class="fas fa-coins text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500">Total Selisih</div>
                        <h6 class="mb-0">
							@if(($isKso && $apbyAktif->director_acc_status_id != 1) || (!$isKso && $apbyAktif->president_acc_status_id != 1))
                            {{ number_format($total->get('anggaran'), 0, ',', '.') }}
                            @else
                            {{ $apbyAktif->totalBalanceWithSeparator }}
                            @endif
                        </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
				<h6 class="m-0 font-weight-bold text-brand-green">Kemajuan Realisasi</h6>
			</div>
            <div class="card-body pt-1 pb-4 px-4">
				@php
				$globalPercentage = $apbyAktif->total_value > 0 ? (($apbyAktif->total_used/$apbyAktif->total_value)*100) : 0;
				$globalPercentage = ($globalPercentage > 0) ? $globalPercentage : 0;
				$color = 'secondary';
				if($globalPercentage >= 0 && $globalPercentage <= 50) $color = 'secondary';
				elseif($globalPercentage > 50 && $globalPercentage <= 75) $color = 'warning';
				else $color = 'success';
				@endphp
				<div class="d-flex align-items-center">
                    <div class="mr-3">{{ number_format($globalPercentage, 0, ',', '.') }}%</div>
					<div style="width:100%">
						<div class="progress">
							<div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $globalPercentage }}%" aria-valuenow="{{ $globalPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">{{ $anggaranAktif->anggaran->name }}</h6>
            </div>
                @if($apbyAktif && $apbyAktif->detail()->count() > 0)
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th style="white-space: nowrap">No Akun</th>
                                <th>Nama Akun</th>
								<th>Rencana</th>
								<th>Realisasi Terkini</th>
                                <th>Realisasi PPB</th>
                                <th>Realisasi RPPA</th>
								{{--<th>Selisih</th>--}}
                                <th>Selisih PPB</th>
                                <th>Selisih RPPA</th>
								<th>Sisa Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $i = 1;
                            $apbyDetail = $apbyAktif->detail()->whereHas('akun.kategori.parent',function($q){$q->where('name','Belanja');});
                            @endphp
                            @if($apbyDetail->count() > 0)
                            @foreach($apbyDetail->with('akun')->get()->sortBy('akun.sort_order')->all() as $d)
                            <tr>
                                <td>{{ $d->akun->code }}</td>
                                <td>{{ $d->akun->name }}</td>
                                <td>{{ $d->valueWithSeparator }}</td>
								@if($d->akun->is_fillable < 1)
								@php
								$thisUsed = $apbyAktif->detail()->whereHas('akun',function($q)use($d){$q->where('code','LIKE',$d->akun->code.'%')->where('is_fillable',1);})->sum('used');
								@endphp
                                <td>{{ number_format($thisUsed, 0, ',', '.') }}</td>
								@else
								<td>{{ $d->usedWithSeparator }}</td>
								@endif
                                <td>{{ $ppbValue[$d->akun->id]['used'] }}</td>
                                <td>{{ $rppaValue[$d->akun->id]['used'] }}</td>
                                @if($d->akun->is_fillable < 1)
								@php
								$thisBalance = $apbyAktif->detail()->whereHas('akun',function($q)use($d){$q->where('code','LIKE',$d->akun->code.'%')->where('is_fillable',1);})->sum('balance');
								@endphp
                                {{--<td>{{ number_format($thisBalance, 0, ',', '.') }}</td>--}}
								@else
								{{--<td>{{ $d->balanceWithSeparator }}</td>--}}
								@endif
                                <td>{{ $ppbValue[$d->akun->id]['balance'] }}</td>
                                <td>{{ $rppaValue[$d->akun->id]['balance'] }}</td>
								@php
								if($d->akun->is_fillable < 1){
									$percentage = $d->value > 0 ? (($thisBalance/$d->value)*100) : 0;
								}
								else{
									$percentage = $d->value > 0 ? (($d->balance/$d->value)*100) : 0;
								}
								$percentage = ($percentage > 0) ? $percentage : 0;
								$color = 'secondary';
								if($percentage >= 0 && $percentage <= 25) $color = 'danger';
								elseif($percentage > 25 && $percentage <= 50) $color = 'warning';
								else $color = 'success';
								@endphp
								<td>
									<div class="progress">
										<div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $percentage }}%" data-toggle="tooltip" data-placement="left" title="{{ number_format($percentage, 0, ',', '.') }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
								</td>
                            </tr>
                            @php $i++ @endphp
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center mx-3 my-5">
                    <h3 class="text-center">Mohon Maaf,</h3>
                    <h6 class="font-weight-light mb-3">Tidak ada data akun anggaran yang ditemukan</h6>
                </div>
                @endif
                <div class="card-footer"></div>
        </form>
        </div>
    </div>
</div>
@endif
<!--Row-->

@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@include('template.footjs.keuangan.change-year')
@endsection