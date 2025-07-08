@extends('template.main.sidebar')

@section('brand-system')
Kependidikan @endsection

@section('sidebar-menu')
<li class="nav-item {{ (Request::path()=='kependidikan') ? 'active' : '' }}">
	<a class="nav-link" href="#">
		<i class="mdi mdi-view-dashboard"></i>
		<span>Beranda</span>
	</a>
</li>
<hr class="sidebar-divider">
@include('template.sidebar.kbm')
@include('template.sidebar.penilaianmapel')
@endsection