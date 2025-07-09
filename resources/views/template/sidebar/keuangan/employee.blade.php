<<<<<<< HEAD
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
      <div class="sidebar-heading">
        Pengelolaan Keuangan
      </div>
	  <li class="nav-item {{ request()->routeIs('proposal-ppa.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('proposal-ppa.index') }}">
          <i class="mdi mdi-chat-plus"></i>
          <span>Proposal PPA</span>
        </a>
      </li>
      <hr class="sidebar-divider">
      @include('template.sidebar.keuangan.pembayaran')
=======
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
      <div class="sidebar-heading">
        Pengelolaan Keuangan
      </div>
	  <li class="nav-item {{ request()->routeIs('proposal-ppa.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('proposal-ppa.index') }}">
          <i class="mdi mdi-chat-plus"></i>
          <span>Proposal PPA</span>
        </a>
      </li>
      <hr class="sidebar-divider">
      @include('template.sidebar.keuangan.pembayaran')
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection