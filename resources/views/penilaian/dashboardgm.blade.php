@extends('template.main.master')

@section('title')
Dasbor
@endsection

@section('sidebar')
@include('template.sidebar.gurumapel')
@endsection

@section('topbarpenilaian')
@include('template.topbar.kependidikan')
@endsection

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="./">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
</div>

<div class="row mb-3">
    <!-- Earnings (Monthly) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Earnings (Monthly)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
                        <div class="mt-2 mb-0 text-muted text-xs">
                            <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                            <span>Since last month</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Earnings (Annual) Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Sales</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">650</div>
                        <div class="mt-2 mb-0 text-muted text-xs">
                            <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span>
                            <span>Since last years</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-brand-green"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- New User Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">New User</div>
                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">366</div>
                        <div class="mt-2 mb-0 text-muted text-xs">
                            <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                            <span>Since last month</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Pending Requests Card Example -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">Pending Requests</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                        <div class="mt-2 mb-0 text-muted text-xs">
                            <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 1.10%</span>
                            <span>Since yesterday</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Area Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Monthly Recap Report</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Dropdown Header:</div>
                        <a class="dropdown-item" href="#">Action</a>
                        <a class="dropdown-item" href="#">Another action</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Products Sold</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle btn btn-brand-green-dark btn-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Month <i class="fas fa-chevron-down"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">Select Periode</div>
                        <a class="dropdown-item" href="#">Today</a>
                        <a class="dropdown-item" href="#">Week</a>
                        <a class="dropdown-item active" href="#">Month</a>
                        <a class="dropdown-item" href="#">This Year</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="small text-gray-500">Oblong T-Shirt
                        <div class="small float-right"><b>600 of 800 Items</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="small text-gray-500">Gundam 90'Editions
                        <div class="small float-right"><b>500 of 800 Items</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-brand-green" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="small text-gray-500">Rounded Hat
                        <div class="small float-right"><b>455 of 800 Items</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-brand-green" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="small text-gray-500">Indomie Goreng
                        <div class="small float-right"><b>400 of 800 Items</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="small text-gray-500">Remote Control Car Racing
                        <div class="small float-right"><b>200 of 800 Items</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                        <div class="progress-bar bg-brand-green-dark" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
                <a class="m-0 small text-brand-green-dark card-link" href="#">View More <i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
    </div>
    <!-- Invoice Example -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-brand-green">Invoice</h6>
                <a class="m-0 float-right btn btn-brand-green-dark btn-sm" href="#">View More <i class="fas fa-chevron-right"></i></a>
            </div>
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Item</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><a href="#">RA0449</a></td>
                            <td>Udin Wayang</td>
                            <td>Nasi Padang</td>
                            <td><span class="badge badge-brand-green">Finished</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-brand-green">Detail</a></td>
                        </tr>
                        <tr>
                            <td><a href="#">RA5324</a></td>
                            <td>Jaenab Bajigur</td>
                            <td>Gundam 90' Edition</td>
                            <td><span class="badge badge-warning">Shipping</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-brand-green">Detail</a></td>
                        </tr>
                        <tr>
                            <td><a href="#">RA8568</a></td>
                            <td>Rivat Mahesa</td>
                            <td>Oblong T-Shirt</td>
                            <td><span class="badge badge-danger">Pending</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-brand-green">Detail</a></td>
                        </tr>
                        <tr>
                            <td><a href="#">RA1453</a></td>
                            <td>Indri Junanda</td>
                            <td>Hat Rounded</td>
                            <td><span class="badge badge-info">Processing</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-brand-green">Detail</a></td>
                        </tr>
                        <tr>
                            <td><a href="#">RA1998</a></td>
                            <td>Udin Cilok</td>
                            <td>Baby Powder</td>
                            <td><span class="badge badge-brand-green">Delivered</span></td>
                            <td><a href="#" class="btn btn-sm btn-outline-brand-green">Detail</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer"></div>
        </div>
    </div>
    <!-- Message From Customer-->
    <div class="col-xl-4 col-lg-5 ">
        <div class="card">
            <div class="card-header py-4 bg-brand-green d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-light">Message From Customer</h6>
            </div>
            <div>
                <div class="customer-message align-items-center">
                    <a class="font-weight-bold" href="#">
                        <div class="text-truncate message-title text-brand-green-dark">Hi there! I am wondering if you can help me with a
                            problem I've been having.</div>
                        <div class="small text-gray-500 message-time font-weight-bold">Udin Cilok · 58m</div>
                    </a>
                </div>
                <div class="customer-message align-items-center">
                    <a href="#">
                        <div class="text-truncate message-title text-brand-green-dark">But I must explain to you how all this mistaken idea
                        </div>
                        <div class="small text-gray-500 message-time">Nana Haminah · 58m</div>
                    </a>
                </div>
                <div class="customer-message align-items-center">
                    <a class="font-weight-bold" href="#">
                        <div class="text-truncate message-title text-brand-green-dark">Lorem ipsum dolor sit amet, consectetur adipiscing elit
                        </div>
                        <div class="small text-gray-500 message-time font-weight-bold">Jajang Cincau · 25m</div>
                    </a>
                </div>
                <div class="customer-message align-items-center">
                    <a class="font-weight-bold" href="#">
                        <div class="text-truncate message-title text-brand-green-dark">At vero eos et accusamus et iusto odio dignissimos
                            ducimus qui blanditiis
                        </div>
                        <div class="small text-gray-500 message-time font-weight-bold">Udin Wayang · 54m</div>
                    </a>
                </div>
                <div class="card-footer text-center">
                    <a class="m-0 small text-brand-green card-link" href="#">View More <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Row-->
@endsection

@section('footjs')
<!-- Plugins and scripts required by this view-->
<script src="{{asset('vendor/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('js/demo/chart-area-demo.js')}}"></script>
@endsection