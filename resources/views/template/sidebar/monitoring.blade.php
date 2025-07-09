<<<<<<< HEAD
@extends('template.main.sidebar')

@section('brand-system')
Monitoring @endsection

@section('sidebar-menu')
      <li class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.index') }}">
          <i class="mdi mdi-view-dashboard"></i>
          <span>Beranda</span></a>
      </li>
      <hr class="sidebar-divider">
      @include('template.sidebar.monitoring.farmmanagement')
      @include('template.sidebar.monitoring.agriculturemonitor')
=======
@extends('template.main.sidebar')

@section('brand-system')
Monitoring @endsection

@section('sidebar-menu')
      <li class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.index') }}">
          <i class="mdi mdi-view-dashboard"></i>
          <span>Beranda</span></a>
      </li>
      <hr class="sidebar-divider">
      @include('template.sidebar.monitoring.farmmanagement')
      @include('template.sidebar.monitoring.agriculturemonitor')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection