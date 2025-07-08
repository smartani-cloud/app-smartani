@extends('template.main.sidebar')

@section('brand-system')
Keuangan @endsection

@section('sidebar-menu')
<li class="nav-item {{ request()->routeIs('keuangan.index') ? 'active' : '' }}">
	<a class="nav-link" href="{{ route('keuangan.index') }}">
		<i class="mdi mdi-view-dashboard"></i>
		<span>Beranda</span>
	</a>
</li>
<hr class="sidebar-divider">
@include('template.sidebar.keuangan.pembayaran')

@endsection