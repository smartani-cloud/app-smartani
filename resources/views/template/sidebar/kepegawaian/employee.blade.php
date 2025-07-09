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
      @if(Auth::user()->role->name == 'sdms')
      <li class="nav-item {{ request()->routeIs('calon*') || request()->routeIs('spk*') || request()->routeIs('jenis-mitra*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') || request()->routeIs('universitas*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('calon*') || request()->routeIs('spk*') || request()->routeIs('jenis-mitra*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') || request()->routeIs('universitas*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseRekrutmen" aria-expanded="{{ request()->routeIs('calon*') || request()->routeIs('spk*') || request()->routeIs('jenis-mitra*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') || request()->routeIs('universitas*') ? 'true' : 'false' }}" aria-controls="collapseRekrutmen">
          <i class="mdi mdi-account-multiple-plus"></i>
          <span>Rekrutmen</span>
        </a>
        <div id="collapseRekrutmen" class="collapse {{ request()->routeIs('calon*') || request()->routeIs('spk*') || request()->routeIs('jenis-mitra*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') || request()->routeIs('universitas*') ? 'show' : '' }}" aria-labelledby="headingRekrutmen" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Rekrutmen</h6>
            <a class="collapse-item {{ request()->routeIs('calon*') ? 'active' : '' }}" href="{{ route('calon.index') }}">
              <i class="mdi mdi-account-plus"></i>
              <span>Calon Civitas</span>
            </a>
            @if(Auth::user()->role->name != 'sdms')
            <a class="collapse-item {{ request()->routeIs('spk*') ? 'active' : '' }}" href="{{ route('spk.index') }}">
              <i class="mdi mdi-file-document"></i>
              <span>Perjanjian Kerja</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('jenis-mitra*') ? 'active' : '' }}" href="{{ route('jenis-mitra.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Jenis Mitra</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('pendidikanterakhir*') ? 'active' : '' }}" href="{{ route('pendidikanterakhir.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Pendidikan Terakhir</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('programstudi*') ? 'active' : '' }}" href="{{ route('programstudi.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Program Studi</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('universitas*') ? 'active' : '' }}" href="{{ route('universitas.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Universitas</span>
            </a>
            @endif
          </div>
        </div>
      </li>
      @endif
	    <li class="nav-item {{ request()->routeIs('pelatihan.saya*') || request()->routeIs('pelatihan.sertifikat*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('pelatihan.saya*') || request()->routeIs('pelatihan.sertifikat*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePelatihan" aria-expanded="{{ request()->routeIs('pelatihan.saya*') || request()->routeIs('pelatihan.sertifikat*') ? 'true' : 'false' }}" aria-controls="collapsePelatihan">
          <i class="mdi mdi-clipboard-arrow-up"></i>
          <span>Pelatihan</span>
        </a>
        <div id="collapsePelatihan" class="collapse {{ request()->routeIs('pelatihan.saya*') || request()->routeIs('pelatihan.sertifikat*') ? 'show' : '' }}" aria-labelledby="headingPelatihan" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Pelatihan</h6>
            <a class="collapse-item {{ request()->routeIs('pelatihan.saya*') ? 'active' : '' }}" href="{{ route('pelatihan.saya.index') }}">
              <i class="mdi mdi-calendar-check"></i>
              <span>Pelatihan Saya</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('pelatihan.sertifikat*') ? 'active' : '' }}" href="{{ route('pelatihan.sertifikat.index') }}">
              <i class="mdi mdi-certificate"></i>
              <span>Sertifikat Kompetensi</span>
            </a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
      @include('template.sidebar.kepegawaian.penilaian_kinerja')
@endsection