<<<<<<< HEAD
@extends('template.main.sidebar')

@section('brand-system')
Kepegawaian @endsection

@section('sidebar-menu')
      <!--
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>
      <hr class="sidebar-divider">
	    -->
      <div class="sidebar-heading mt-3">
        Manajemen Pegawai
      </div>
	    <li class="nav-item {{ request()->routeIs('kepegawaian.index') || request()->routeIs('pegawai*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pegawai.index') }}">
          <i class="mdi mdi-account-group"></i>
          <span>Pegawai</span>
        </a>
      </li>
      <li class="nav-item {{ request()->routeIs('calon*') || request()->routeIs('evaluasi*') || request()->routeIs('spk*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('calon*') || request()->routeIs('evaluasi*') || request()->routeIs('spk*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseRekrutmen" aria-expanded="{{ request()->routeIs('calon*') || request()->routeIs('evaluasi*') || request()->routeIs('spk*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') ? 'true' : 'false' }}" aria-controls="collapseRekrutmen">
          <i class="mdi mdi-account-multiple-plus"></i>
          <span>Rekrutmen</span>
        </a>
        <div id="collapseRekrutmen" class="collapse {{ request()->routeIs('calon*') || request()->routeIs('evaluasi*') || request()->routeIs('spk*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') ? 'show' : '' }}" aria-labelledby="headingRekrutmen" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Rekrutmen</h6>
            <a class="collapse-item {{ request()->routeIs('calon*') ? 'active' : '' }}" href="{{ route('calon.index') }}">
              <i class="mdi mdi-account-plus"></i>
              <span>Calon PTT</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('spk*') ? 'active' : '' }}" href="{{ route('spk.index') }}">
              <i class="mdi mdi-file-document"></i>
              <span>Perjanjian Kerja</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('pendidikanterakhir*') ? 'active' : '' }}" href="{{ route('pendidikanterakhir.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Pendidikan Terakhir</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('programstudi*') ? 'active' : '' }}" href="{{ route('programstudi.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Program Studi</span>
            </a>
            <!--
            <a class="collapse-item {{ request()->routeIs('programstudi*') ? 'active' : '' }}" href="{{ route('programstudi.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Universitas</span>
            </a> -->
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePenempatan" aria-expanded="false" aria-controls="collapsePenempatan">
          <i class="mdi mdi-account-switch"></i>
          <span>Penempatan</span>
        </a>
        <div id="collapsePenempatan" class="collapse {{ request()->routeIs('struktural*') || request()->routeIs('nonstruktural*') || request()->routeIs('skbm*') ? 'show' : '' }}" aria-labelledby="headingPenempatan" data-parent="#accordionSidebar" style="">
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
            <a class="collapse-item {{ request()->routeIs('skbm*') ? 'active' : '' }}" href="{{ route('skbm.index') }}">
              <i class="mdi mdi-file-account"></i>
              <span>SKBM</span>
            </a>
            <!--
            <hr class="sidebar-divider">
            <a class="collapse-item" href="buttons.html">
              <i class="mdi mdi-cog"></i>
              <span>Klasifikasi</span>
            </a>-->
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('pelatihan.materi*') || request()->routeIs('pelatihan.sertifikat*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('pelatihan.materi*') || request()->routeIs('pelatihan.sertifikat*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePelatihan" aria-expanded="false" aria-controls="collapsePelatihan">
          <i class="mdi mdi-clipboard-arrow-up"></i>
          <span>Pelatihan</span>
        </a>
        <div id="collapsePelatihan" class="collapse {{ request()->routeIs('pelatihan.materi*') || request()->routeIs('pelatihan.sertifikat*') ? 'show' : '' }}" aria-labelledby="headingPelatihan" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Pelatihan</h6>
            <a class="collapse-item {{ request()->routeIs('pelatihan.materi*') ? 'active' : '' }}" href="{{ route('pelatihan.materi.index') }}">
              <i class="mdi mdi-view-list"></i>
              <span>Pelatihan</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('pelatihan.sertifikat*') ? 'active' : '' }}" href="{{ route('pelatihan.sertifikat.index') }}">
              <i class="mdi mdi-certificate"></i>
              <span>Sertifikat Kompetensi</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('evaluasi.index') }}" aria-expanded="false" aria-controls="collapsePenempatan">
          <i class="mdi mdi-account-switch"></i>
          <span>Evaluasi PTT</span>
        </a>
      </li>
      <li class="nav-item {{ request()->routeIs('phk*') || request()->routeIs('alasanphk*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('phk*') || request()->routeIs('alasanphk*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePHK" aria-expanded="{{ request()->routeIs('phk*') || request()->routeIs('alasanphk*') ? 'true' : 'false' }}" aria-controls="collapsePHK">
          <i class="mdi mdi-account-multiple-minus"></i>
          <span>Putus Hubungan Kerja</span>
        </a>
        <div id="collapsePHK" class="collapse {{ request()->routeIs('phk*') || request()->routeIs('alasanphk*') ? 'show' : '' }}" aria-labelledby="headingPHK" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Putus Hubungan Kerja</h6>
            <a class="collapse-item {{ request()->routeIs('phk*') ? 'active' : '' }}" href="{{ route('phk.index') }}">
              <i class="mdi mdi-account-minus"></i>
              <span>Pengajuan PHK</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('alasanphk*') ? 'active' : '' }}" href="{{ route('alasanphk.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Alasan PHK</span>
            </a>
          </div>
        </div>
      </li><!--
      <hr class="sidebar-divider">
	    <div class="sidebar-heading">
        Penilaian Kinerja
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePSC" aria-expanded="false" aria-controls="collapsePSC">
          <i class="mdi mdi-star"></i>
          <span>Performance Scorecard</span>
        </a>
        <div id="collapsePSC" class="collapse" aria-labelledby="headingPSC" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Performance Scorecard</h6>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-star-plus"></i>
              <span>Penilaian Kinerja</span>
            </a>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-card-account-details-star"></i>
              <span>Laporan Prestasi Kerja</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item" href="buttons.html">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Evaluasi</span>
            </a>
            <a class="collapse-item" href="buttons.html">
              <i class="mdi mdi-cog"></i>
              <span>Rentang Nilai</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIKU" aria-expanded="false" aria-controls="collapseIKU">
          <i class="mdi mdi-flag"></i>
          <span>Indikator Kinerja Utama</span>
        </a>
        <div id="collapseIKU" class="collapse" aria-labelledby="headingIKU" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Indikator Kinerja Utama</h6>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Edukasi</span>
            </a>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Persepsi</span>
            </a>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Sasaran</span>
            </a>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Strategis</span>
            </a>
          </div>
        </div>
      </li>
      -->
      <hr class="sidebar-divider">
=======
@extends('template.main.sidebar')

@section('brand-system')
Kepegawaian @endsection

@section('sidebar-menu')
      <!--
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>
      <hr class="sidebar-divider">
	    -->
      <div class="sidebar-heading mt-3">
        Manajemen Pegawai
      </div>
	    <li class="nav-item {{ request()->routeIs('kepegawaian.index') || request()->routeIs('pegawai*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pegawai.index') }}">
          <i class="mdi mdi-account-group"></i>
          <span>Pegawai</span>
        </a>
      </li>
      <li class="nav-item {{ request()->routeIs('calon*') || request()->routeIs('evaluasi*') || request()->routeIs('spk*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('calon*') || request()->routeIs('evaluasi*') || request()->routeIs('spk*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseRekrutmen" aria-expanded="{{ request()->routeIs('calon*') || request()->routeIs('evaluasi*') || request()->routeIs('spk*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') ? 'true' : 'false' }}" aria-controls="collapseRekrutmen">
          <i class="mdi mdi-account-multiple-plus"></i>
          <span>Rekrutmen</span>
        </a>
        <div id="collapseRekrutmen" class="collapse {{ request()->routeIs('calon*') || request()->routeIs('evaluasi*') || request()->routeIs('spk*') || request()->routeIs('pendidikanterakhir*') || request()->routeIs('programstudi*') ? 'show' : '' }}" aria-labelledby="headingRekrutmen" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Rekrutmen</h6>
            <a class="collapse-item {{ request()->routeIs('calon*') ? 'active' : '' }}" href="{{ route('calon.index') }}">
              <i class="mdi mdi-account-plus"></i>
              <span>Calon PTT</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('spk*') ? 'active' : '' }}" href="{{ route('spk.index') }}">
              <i class="mdi mdi-file-document"></i>
              <span>Perjanjian Kerja</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('pendidikanterakhir*') ? 'active' : '' }}" href="{{ route('pendidikanterakhir.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Pendidikan Terakhir</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('programstudi*') ? 'active' : '' }}" href="{{ route('programstudi.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Program Studi</span>
            </a>
            <!--
            <a class="collapse-item {{ request()->routeIs('programstudi*') ? 'active' : '' }}" href="{{ route('programstudi.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Universitas</span>
            </a> -->
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePenempatan" aria-expanded="false" aria-controls="collapsePenempatan">
          <i class="mdi mdi-account-switch"></i>
          <span>Penempatan</span>
        </a>
        <div id="collapsePenempatan" class="collapse {{ request()->routeIs('struktural*') || request()->routeIs('nonstruktural*') || request()->routeIs('skbm*') ? 'show' : '' }}" aria-labelledby="headingPenempatan" data-parent="#accordionSidebar" style="">
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
            <a class="collapse-item {{ request()->routeIs('skbm*') ? 'active' : '' }}" href="{{ route('skbm.index') }}">
              <i class="mdi mdi-file-account"></i>
              <span>SKBM</span>
            </a>
            <!--
            <hr class="sidebar-divider">
            <a class="collapse-item" href="buttons.html">
              <i class="mdi mdi-cog"></i>
              <span>Klasifikasi</span>
            </a>-->
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('pelatihan.materi*') || request()->routeIs('pelatihan.sertifikat*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('pelatihan.materi*') || request()->routeIs('pelatihan.sertifikat*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePelatihan" aria-expanded="false" aria-controls="collapsePelatihan">
          <i class="mdi mdi-clipboard-arrow-up"></i>
          <span>Pelatihan</span>
        </a>
        <div id="collapsePelatihan" class="collapse {{ request()->routeIs('pelatihan.materi*') || request()->routeIs('pelatihan.sertifikat*') ? 'show' : '' }}" aria-labelledby="headingPelatihan" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Pelatihan</h6>
            <a class="collapse-item {{ request()->routeIs('pelatihan.materi*') ? 'active' : '' }}" href="{{ route('pelatihan.materi.index') }}">
              <i class="mdi mdi-view-list"></i>
              <span>Pelatihan</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('pelatihan.sertifikat*') ? 'active' : '' }}" href="{{ route('pelatihan.sertifikat.index') }}">
              <i class="mdi mdi-certificate"></i>
              <span>Sertifikat Kompetensi</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('evaluasi.index') }}" aria-expanded="false" aria-controls="collapsePenempatan">
          <i class="mdi mdi-account-switch"></i>
          <span>Evaluasi PTT</span>
        </a>
      </li>
      <li class="nav-item {{ request()->routeIs('phk*') || request()->routeIs('alasanphk*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('phk*') || request()->routeIs('alasanphk*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePHK" aria-expanded="{{ request()->routeIs('phk*') || request()->routeIs('alasanphk*') ? 'true' : 'false' }}" aria-controls="collapsePHK">
          <i class="mdi mdi-account-multiple-minus"></i>
          <span>Putus Hubungan Kerja</span>
        </a>
        <div id="collapsePHK" class="collapse {{ request()->routeIs('phk*') || request()->routeIs('alasanphk*') ? 'show' : '' }}" aria-labelledby="headingPHK" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Putus Hubungan Kerja</h6>
            <a class="collapse-item {{ request()->routeIs('phk*') ? 'active' : '' }}" href="{{ route('phk.index') }}">
              <i class="mdi mdi-account-minus"></i>
              <span>Pengajuan PHK</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('alasanphk*') ? 'active' : '' }}" href="{{ route('alasanphk.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Alasan PHK</span>
            </a>
          </div>
        </div>
      </li><!--
      <hr class="sidebar-divider">
	    <div class="sidebar-heading">
        Penilaian Kinerja
      </div>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePSC" aria-expanded="false" aria-controls="collapsePSC">
          <i class="mdi mdi-star"></i>
          <span>Performance Scorecard</span>
        </a>
        <div id="collapsePSC" class="collapse" aria-labelledby="headingPSC" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Performance Scorecard</h6>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-star-plus"></i>
              <span>Penilaian Kinerja</span>
            </a>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-card-account-details-star"></i>
              <span>Laporan Prestasi Kerja</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item" href="buttons.html">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Evaluasi</span>
            </a>
            <a class="collapse-item" href="buttons.html">
              <i class="mdi mdi-cog"></i>
              <span>Rentang Nilai</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIKU" aria-expanded="false" aria-controls="collapseIKU">
          <i class="mdi mdi-flag"></i>
          <span>Indikator Kinerja Utama</span>
        </a>
        <div id="collapseIKU" class="collapse" aria-labelledby="headingIKU" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Indikator Kinerja Utama</h6>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Edukasi</span>
            </a>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Persepsi</span>
            </a>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Sasaran</span>
            </a>
            <a class="collapse-item" href="alerts.html">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Strategis</span>
            </a>
          </div>
        </div>
      </li>
      -->
      <hr class="sidebar-divider">
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection