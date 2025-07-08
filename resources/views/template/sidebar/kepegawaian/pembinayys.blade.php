@extends('template.main.sidebar')

@section('brand-system')
Kepegawaian @endsection

@section('sidebar-menu')
      <li class="nav-item {{ request()->routeIs('kepegawaian.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kepegawaian.index') }}">
          <i class="mdi mdi-view-dashboard"></i>
          <span>Beranda</span></a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Manajemen Civitas
      </div>
      <li class="nav-item {{ request()->routeIs('pegawai*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('pegawai*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseCivitas" aria-expanded="{{ request()->routeIs('pegawai*') ? 'true' : 'false' }}" aria-controls="collapseCivitas">
          <i class="mdi mdi-tree"></i>
          <span>Civitas</span>
        </a>
        <div id="collapseCivitas" class="collapse {{ request()->routeIs('pegawai*') ? 'show' : '' }}" aria-labelledby="headingCivitas" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Civitas</h6>
            <a class="collapse-item {{ request()->routeIs('pegawai*') && !isset($category) ? 'active' : '' }}" href="{{ route('pegawai.index') }}">
              <i class="mdi mdi-account-group"></i>
              <span>Semua</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('pegawai*') && isset($category) && $category == 'pegawai' ? 'active' : '' }}" href="{{ route('pegawai.index',['category' => 'pegawai']) }}">
              <i class="mdi mdi-account"></i>
              <span>Pegawai</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('pegawai*') && isset($category) && $category == 'mitra' ? 'active' : '' }}" href="{{ route('pegawai.index',['category' => 'mitra']) }}">
              <i class="mdi mdi-handshake"></i>
              <span>Mitra</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('pegawai*') && isset($category) && $category == 'yayasan' ? 'active' : '' }}" href="{{ route('pegawai.index',['category' => 'yayasan']) }}">
              <i class="mdi mdi-domain"></i>
              <span>Yayasan</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('calon*') || request()->routeIs('spk*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('calon*') || request()->routeIs('spk*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseRekrutmen" aria-expanded="{{ request()->routeIs('calon*') || request()->routeIs('spk*') ? 'true' : 'false' }}" aria-controls="collapseRekrutmen">
          <i class="mdi mdi-account-multiple-plus"></i>
          <span>Rekrutmen</span>
        </a>
        <div id="collapseRekrutmen" class="collapse {{ request()->routeIs('calon*') || request()->routeIs('spk*') ? 'show' : '' }}" aria-labelledby="headingRekrutmen" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Rekrutmen</h6>
            <a class="collapse-item {{ request()->routeIs('calon*') ? 'active' : '' }}" href="{{ route('calon.index') }}">
              <i class="mdi mdi-account-plus"></i>
              <span>Calon Civitas</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('spk*') ? 'active' : '' }}" href="{{ route('spk.index') }}">
              <i class="mdi mdi-file-document"></i>
              <span>Perjanjian Kerja</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('struktural*') || request()->routeIs('nonstruktural*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('struktural*') || request()->routeIs('nonstruktural*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePenempatan" aria-expanded="{{ request()->routeIs('struktural*') || request()->routeIs('nonstruktural*') ? 'true' : 'false' }}" aria-controls="collapsePenempatan">
          <i class="mdi mdi-account-switch"></i>
          <span>Penempatan</span>
        </a>
        <div id="collapsePenempatan" class="collapse {{ request()->routeIs('struktural*') || request()->routeIs('nonstruktural*') ? 'show' : '' }}" aria-labelledby="headingPenempatan" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Penempatan</h6>
            <a class="collapse-item {{ request()->routeIs('struktural*') ? 'active' : '' }}" href="{{ route('struktural.index') }}">
              <i class="mdi mdi-account-tie"></i>
              <span>Struktural</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('nonstruktural*') ? 'active' : '' }}" href="{{ route('nonstruktural.index') }}">
              <i class="mdi mdi-account"></i>
              <span>Nonstruktural</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('pelatihan.materi*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pelatihan.materi.index') }}">
          <i class="mdi mdi-clipboard-arrow-up"></i>
          <span>Pelatihan</span>
        </a>
      </li>
      <li class="nav-item {{ request()->routeIs('evaluasi*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('evaluasi.index') }}">
          <i class="mdi mdi-account-switch"></i>
          <span>Evaluasi Civitas</span>
        </a>
      </li>
      <li class="nav-item {{ request()->routeIs('phk*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('phk.index') }}">
          <i class="mdi mdi-account-multiple-minus"></i>
          <span>Putus Hubungan Kerja</span>
        </a>
      </li>
      <hr class="sidebar-divider">
      @include('template.sidebar.kepegawaian.penilaian_kinerja')
@endsection