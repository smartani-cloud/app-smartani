<<<<<<< HEAD
@extends('template.main.sidebar')

@section('brand-system')
Keuangan @endsection

@section('sidebar-menu')
      <li class="nav-item {{ request()->routeIs('keuangan.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('keuangan.index') }}">
          <i class="mdi mdi-view-dashboard"></i>
          <span>Beranda</span></a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Pengelolaan Keuangan
      </div>
      <li class="nav-item {{ request()->routeIs('ppb*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('ppb.index') }}">
          <i class="mdi mdi-bank-transfer-out"></i>
          <span>PPB</span>
        </a>
      </li>
      <hr class="sidebar-divider">
=======
@extends('template.main.sidebar')

@section('brand-system')
Keuangan @endsection

@section('sidebar-menu')
      <li class="nav-item {{ request()->routeIs('keuangan.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('keuangan.index') }}">
          <i class="mdi mdi-view-dashboard"></i>
          <span>Beranda</span></a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Pengelolaan Keuangan
      </div>
      <li class="nav-item {{ request()->routeIs('ppb*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('ppb.index') }}">
          <i class="mdi mdi-bank-transfer-out"></i>
          <span>PPB</span>
        </a>
      </li>
      <hr class="sidebar-divider">
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection