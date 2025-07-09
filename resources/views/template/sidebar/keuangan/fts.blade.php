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
      <li class="nav-item {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePenempatan" aria-expanded="{{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') ? 'true' : 'false' }}" aria-controls="collapsePenempatan">
          <i class="mdi mdi-inbox-arrow-up"></i>
          <span>PPA</span>
        </a>
        <div id="collapsePenempatan" class="collapse {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') ? 'show' : '' }}" aria-labelledby="headingPenempatan" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">PPA</h6>
            <a class="collapse-item {{ request()->routeIs('ppa*') ? 'active' : '' }}" href="{{ route('ppa.index') }}">
              <i class="mdi mdi-file-document"></i>
              <span>PPA</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('proposal-ppa*') ? 'active' : '' }}" href="{{ route('proposal-ppa.index') }}">
              <i class="mdi mdi-chat-plus"></i>
              <span>Proposal</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('lppa*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('lppa.index') }}">
          <i class="mdi mdi-text-box-check"></i>
          <span>RPPA</span>
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
          <span>Beranda</span>
		</a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Pengelolaan Keuangan
      </div>
      <li class="nav-item {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePenempatan" aria-expanded="{{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') ? 'true' : 'false' }}" aria-controls="collapsePenempatan">
          <i class="mdi mdi-inbox-arrow-up"></i>
          <span>PPA</span>
        </a>
        <div id="collapsePenempatan" class="collapse {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') ? 'show' : '' }}" aria-labelledby="headingPenempatan" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">PPA</h6>
            <a class="collapse-item {{ request()->routeIs('ppa*') ? 'active' : '' }}" href="{{ route('ppa.index') }}">
              <i class="mdi mdi-file-document"></i>
              <span>PPA</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('proposal-ppa*') ? 'active' : '' }}" href="{{ route('proposal-ppa.index') }}">
              <i class="mdi mdi-chat-plus"></i>
              <span>Proposal</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('lppa*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('lppa.index') }}">
          <i class="mdi mdi-text-box-check"></i>
          <span>RPPA</span>
        </a>
      </li>
      <hr class="sidebar-divider">
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection