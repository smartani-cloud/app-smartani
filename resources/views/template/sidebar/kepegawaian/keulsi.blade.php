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
      <li class="nav-item {{ request()->routeIs('saldo*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('saldo.index') }}">
          <i class="mdi mdi-wallet"></i>
          <span>Saldo Akun Anggaran</span>
        </a>
      </li>
      <hr class="sidebar-divider">
@endsection