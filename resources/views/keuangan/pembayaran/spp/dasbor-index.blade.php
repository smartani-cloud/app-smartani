@extends('template.main.master')

@section('title')
{{ $active }}
@endsection

@section('sidebar')
@include('template.sidebar.keuangan.pengelolaan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $active }}</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="javascript:void(0)">Pembayaran Uang Sekolah</a></li>
        <li class="breadcrumb-item"><a href="{{ route('spp.index')}}">Sumbangan Pembinaan Pendidikan</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $active }}</li>
    </ol>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-brand-green">Total SPP Terbayar</h6>
            </div>
            <div class="card-body pt-1 pb-4 px-4">
                @php
                $globalPercentage = $lists->sum('percentage') > 0 && count($lists) > 0 ? ($lists->sum('percentage')/count($lists)) : 0;
                $globalPercentage = ($globalPercentage > 0) ? $globalPercentage : 0;
                $color = 'secondary';
                if($globalPercentage >= 0 && $globalPercentage <= 50) $color = 'secondary';
                elseif($globalPercentage > 50 && $globalPercentage <= 75) $color = 'warning';
                else $color = 'success';
                @endphp
                <div class="d-flex align-items-center">
                  <div class="mr-3">{{ number_format($globalPercentage, 1, ',', '.') }}%</div>
                  <div style="width:100%">
                    <div class="progress">
                      <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $globalPercentage }}%" data-toggle="tooltip" title="{{ number_format($lists->sum('paid'), 0, ',', '.').' / '.number_format($lists->sum('nominal'), 0, ',', '.') }}" aria-valuenow="{{ $globalPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </div>
            </div>
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
        <div class="table-responsive">
            <table class="table table-bordered dataTable" id="dataTable" width="100%" cellspacing="0" role="grid" aria-describedby="dataTable_info" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Tanggungan SPP Per Bulan Lalu</th>
                        <th>Deposit SPP Per Bulan Lalu</th>
                        <th>Total Nominal SPP Bulan Ini</th>
                        <th>Total Potongan SPP (Di Awal Tapel)</th>
                        <th>Total Tanggungan SPP Bulan Ini</th>
                        <th>SPP Terbayar</th>
                        <th>Persentase SPP Terbayar</th>
                    </tr>
                </thead>
                <tbody id="tbody">
                    @foreach($lists as $list)
                    <tr>
                        <td class="text-nowrap">{{ $list['name'] }}</td>
                        <td>{{ number_format($list['last'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['deposit'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['nominal'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['deduction'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['bill'], 0, ',', '.') }}</td>
                        <td>{{ number_format($list['paid'], 0, ',', '.') }}</td>
                        @php
                        $percentage = ($list['percentage'] > 0) ? $list['percentage'] : 0;
                        $color = 'secondary';
                        if($percentage >= 0 && $percentage <= 25) $color = 'danger';
                        elseif($percentage > 25 && $percentage <= 50) $color = 'warning';
                        else $color = 'success';
                        @endphp
                        <td>
                          <div class="d-flex align-items-center">
                            <div class="mr-3">{{ number_format($percentage, 0, ',', '.') }}%</div>
                            <div style="width:100%">
                              <div class="progress">
                                <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                              </div>
                            </div>
                          </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
@include('template.footjs.kepegawaian.tooltip')
@endsection
