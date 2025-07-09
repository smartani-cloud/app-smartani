<<<<<<< HEAD
      @php
      $isTopManagements = in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur']);
      @endphp
      <div class="sidebar-heading">
        Penilaian Kinerja
      </div>
      <li class="nav-item {{ request()->routeIs('psc*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('psc*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePSC" aria-expanded="{{ request()->routeIs('psc*') ? 'true' : 'false' }}" aria-controls="collapsePSC">
          <i class="mdi mdi-star"></i>
          <span>Performance Scorecard</span>
        </a>
        <div id="collapsePSC" class="collapse {{ request()->routeIs('psc*')? 'show' : '' }}" aria-labelledby="headingPSC" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Performance Scorecard</h6>
            @if(Auth::user()->pegawai->position_id && (Auth::user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',2)->count() > 0 || Auth::user()->pegawai->jabatan->penilaiPsc()->count() > 0))
            <a class="collapse-item {{ request()->routeIs('psc.penilaian*') ? 'active' : '' }}" href="{{ route('psc.penilaian.index') }}">
              <i class="mdi mdi-star-plus"></i>
              <span>PSC Pegawai</span>
            </a>
            @endif
            @if(Auth::user()->pegawai->position_id && Auth::user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',3)->count() > 0)
            <a class="collapse-item {{ request()->routeIs('psc.laporan.pegawai*') ? 'active' : '' }}" href="{{ route('psc.laporan.pegawai.index') }}">
              <i class="mdi mdi-card-account-details-star"></i>
              <span>Laporan Prestasi Kerja</span>
            </a>
            @endif
            @if(!$isTopManagements)
            <a class="collapse-item {{ request()->routeIs('psc.laporan.saya*') ? 'active' : '' }}" href="{{ route('psc.laporan.saya.index') }}">
              <i class="mdi mdi-account-star"></i>
              <span>Laporan PSC Saya</span>
            </a>
            @endif
            @if(in_array(Auth::user()->role->name,['etl','etm']))
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('psc.peran*') ? 'active' : '' }}" href="{{ route('psc.peran.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Pemetaan Peran</span>
            </a>
            @if((in_array(Auth::user()->role->name,['etl','etm'])) || (Auth::user()->pegawai->position_id && Auth::user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1)->count() > 0))
            <a class="collapse-item {{ request()->routeIs('psc.aspek*') ? 'active' : '' }}" href="{{ route('psc.aspek.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Evaluasi</span>
            </a>
            @endif
            <a class="collapse-item {{ request()->routeIs('psc.utama*') ? 'active' : '' }}" href="{{ route('psc.utama.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Utama</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('psc.aspek.kunci*') ? 'active' : '' }}" href="{{ route('psc.aspek.kunci.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Kunci Aspek</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('psc.rentang*') ? 'active' : '' }}" href="{{ route('psc.rentang.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Rentang Nilai</span>
            </a>
            @elseif($isTopManagements || (!$isTopManagements && Auth::user()->pegawai->position_id && Auth::user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1)->count() > 0))
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('psc.aspek*') ? 'active' : '' }}" href="{{ route('psc.aspek.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Evaluasi</span>
            </a>
            @if($isTopManagements)
            <a class="collapse-item {{ request()->routeIs('psc.utama*') ? 'active' : '' }}" href="{{ route('psc.utama.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Utama</span>
            </a>
            @endif
            @endif
          </div>
        </div>
      </li>
      @if($isTopManagements)
      <li class="nav-item {{ request()->routeIs('iku*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('iku*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIKU" aria-expanded="{{ request()->routeIs('iku*') ? 'true' : 'false' }}" aria-controls="collapseIKU">
          <i class="mdi mdi-flag"></i>
          <span>Indikator Kinerja Utama</span>
        </a>
        <div id="collapseIKU" class="collapse {{ request()->routeIs('iku*')? 'show' : '' }}" aria-labelledby="headingIKU" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Indikator Kinerja Utama</h6>
            <a class="collapse-item {{ request()->routeIs('iku.pegawai*') ? 'active' : '' }}" href="{{ route('iku.pegawai.index') }}">
              <i class="mdi mdi-chart-bar"></i>
              <span>IKU Pegawai</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('iku.edukasi*') ? 'active' : '' }}" href="{{ route('iku.edukasi.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Edukasi</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('iku.layanan*') ? 'active' : '' }}" href="{{ route('iku.layanan.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Layanan</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('iku.persepsi*') ? 'active' : '' }}" href="{{ route('iku.persepsi.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Persepsi</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('iku.sasaran*') ? 'active' : '' }}" href="{{ route('iku.sasaran.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Sasaran</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('iku.aspek*') ? 'active' : '' }}" href="{{ route('iku.aspek.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek IKU</span>
            </a>
          </div>
        </div>
      </li>
      @else
      @if(Auth::user()->pegawai->position_id && (in_array(Auth::user()->role->name,['kepsek','etl']) || Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',3)->count() > 0 || Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1)->count() > 0))
      <li class="nav-item {{ request()->routeIs('iku*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('iku*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIKU" aria-expanded="{{ request()->routeIs('iku*') ? 'true' : 'false' }}" aria-controls="collapseIKU">
          <i class="mdi mdi-flag"></i>
          <span>Indikator Kinerja Utama</span>
        </a>
        <div id="collapseIKU" class="collapse {{ request()->routeIs('iku*')? 'show' : '' }}" aria-labelledby="headingIKU" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Indikator Kinerja Utama</h6>
            @if(in_array(Auth::user()->role->name,['kepsek','etl']))
            <a class="collapse-item {{ request()->routeIs('iku.pegawai*') ? 'active' : '' }}" href="{{ route('iku.pegawai.index') }}">
              <i class="mdi mdi-chart-bar"></i>
              <span>IKU Pegawai</span>
            </a>
            @endif
            @if(Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',3)->count() > 0)
            @php
            $categories = Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',3)->with('aspek.kategori')->get()->pluck('aspek.kategori')->pluck('name')->unique()->flatten()->toArray();
            @endphp
            @if(in_array('Edukasi',$categories))
            <a class="collapse-item {{ request()->routeIs('iku.edukasi*') ? 'active' : '' }}" href="{{ route('iku.edukasi.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Edukasi</span>
            </a>
            @endif
            @if(in_array('Layanan',$categories))
            <a class="collapse-item {{ request()->routeIs('iku.layanan*') ? 'active' : '' }}" href="{{ route('iku.layanan.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Layanan</span>
            </a>
            @endif
            @if(in_array('Persepsi',$categories))
            <a class="collapse-item {{ request()->routeIs('iku.persepsi*') ? 'active' : '' }}" href="{{ route('iku.persepsi.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Persepsi</span>
            </a>
            @endif
            @if(in_array('Sasaran',$categories))
            <a class="collapse-item {{ request()->routeIs('iku.sasaran*') ? 'active' : '' }}" href="{{ route('iku.sasaran.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Sasaran</span>
            </a>
            @endif
            @if(Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1)->count() > 0)
            <hr class="sidebar-divider">
            @endif
            @endif
            @if(Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1)->count() > 0)
            <a class="collapse-item {{ request()->routeIs('iku.aspek*') ? 'active' : '' }}" href="{{ route('iku.aspek.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek IKU</span>
            </a>
            @endif
          </div>
        </div>
      </li>
      @endif
      @endif
=======
      @php
      $isTopManagements = in_array(Auth::user()->role->name,['pembinayys','ketuayys','direktur']);
      @endphp
      <div class="sidebar-heading">
        Penilaian Kinerja
      </div>
      <li class="nav-item {{ request()->routeIs('psc*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('psc*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePSC" aria-expanded="{{ request()->routeIs('psc*') ? 'true' : 'false' }}" aria-controls="collapsePSC">
          <i class="mdi mdi-star"></i>
          <span>Performance Scorecard</span>
        </a>
        <div id="collapsePSC" class="collapse {{ request()->routeIs('psc*')? 'show' : '' }}" aria-labelledby="headingPSC" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Performance Scorecard</h6>
            @if(Auth::user()->pegawai->position_id && (Auth::user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',2)->count() > 0 || Auth::user()->pegawai->jabatan->penilaiPsc()->count() > 0))
            <a class="collapse-item {{ request()->routeIs('psc.penilaian*') ? 'active' : '' }}" href="{{ route('psc.penilaian.index') }}">
              <i class="mdi mdi-star-plus"></i>
              <span>PSC Pegawai</span>
            </a>
            @endif
            @if(Auth::user()->pegawai->position_id && Auth::user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',3)->count() > 0)
            <a class="collapse-item {{ request()->routeIs('psc.laporan.pegawai*') ? 'active' : '' }}" href="{{ route('psc.laporan.pegawai.index') }}">
              <i class="mdi mdi-card-account-details-star"></i>
              <span>Laporan Prestasi Kerja</span>
            </a>
            @endif
            @if(!$isTopManagements)
            <a class="collapse-item {{ request()->routeIs('psc.laporan.saya*') ? 'active' : '' }}" href="{{ route('psc.laporan.saya.index') }}">
              <i class="mdi mdi-account-star"></i>
              <span>Laporan PSC Saya</span>
            </a>
            @endif
            @if(in_array(Auth::user()->role->name,['etl','etm']))
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('psc.peran*') ? 'active' : '' }}" href="{{ route('psc.peran.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Pemetaan Peran</span>
            </a>
            @if((in_array(Auth::user()->role->name,['etl','etm'])) || (Auth::user()->pegawai->position_id && Auth::user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1)->count() > 0))
            <a class="collapse-item {{ request()->routeIs('psc.aspek*') ? 'active' : '' }}" href="{{ route('psc.aspek.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Evaluasi</span>
            </a>
            @endif
            <a class="collapse-item {{ request()->routeIs('psc.utama*') ? 'active' : '' }}" href="{{ route('psc.utama.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Utama</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('psc.aspek.kunci*') ? 'active' : '' }}" href="{{ route('psc.aspek.kunci.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Kunci Aspek</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('psc.rentang*') ? 'active' : '' }}" href="{{ route('psc.rentang.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Rentang Nilai</span>
            </a>
            @elseif($isTopManagements || (!$isTopManagements && Auth::user()->pegawai->position_id && Auth::user()->pegawai->jabatan->pscRoleTarget()->select('target_position_id')->where('pa_role_mapping_id',1)->count() > 0))
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('psc.aspek*') ? 'active' : '' }}" href="{{ route('psc.aspek.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Evaluasi</span>
            </a>
            @if($isTopManagements)
            <a class="collapse-item {{ request()->routeIs('psc.utama*') ? 'active' : '' }}" href="{{ route('psc.utama.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek Utama</span>
            </a>
            @endif
            @endif
          </div>
        </div>
      </li>
      @if($isTopManagements)
      <li class="nav-item {{ request()->routeIs('iku*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('iku*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIKU" aria-expanded="{{ request()->routeIs('iku*') ? 'true' : 'false' }}" aria-controls="collapseIKU">
          <i class="mdi mdi-flag"></i>
          <span>Indikator Kinerja Utama</span>
        </a>
        <div id="collapseIKU" class="collapse {{ request()->routeIs('iku*')? 'show' : '' }}" aria-labelledby="headingIKU" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Indikator Kinerja Utama</h6>
            <a class="collapse-item {{ request()->routeIs('iku.pegawai*') ? 'active' : '' }}" href="{{ route('iku.pegawai.index') }}">
              <i class="mdi mdi-chart-bar"></i>
              <span>IKU Pegawai</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('iku.edukasi*') ? 'active' : '' }}" href="{{ route('iku.edukasi.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Edukasi</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('iku.layanan*') ? 'active' : '' }}" href="{{ route('iku.layanan.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Layanan</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('iku.persepsi*') ? 'active' : '' }}" href="{{ route('iku.persepsi.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Persepsi</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('iku.sasaran*') ? 'active' : '' }}" href="{{ route('iku.sasaran.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Sasaran</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('iku.aspek*') ? 'active' : '' }}" href="{{ route('iku.aspek.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek IKU</span>
            </a>
          </div>
        </div>
      </li>
      @else
      @if(Auth::user()->pegawai->position_id && (in_array(Auth::user()->role->name,['kepsek','etl']) || Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',3)->count() > 0 || Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1)->count() > 0))
      <li class="nav-item {{ request()->routeIs('iku*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('iku*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIKU" aria-expanded="{{ request()->routeIs('iku*') ? 'true' : 'false' }}" aria-controls="collapseIKU">
          <i class="mdi mdi-flag"></i>
          <span>Indikator Kinerja Utama</span>
        </a>
        <div id="collapseIKU" class="collapse {{ request()->routeIs('iku*')? 'show' : '' }}" aria-labelledby="headingIKU" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Indikator Kinerja Utama</h6>
            @if(in_array(Auth::user()->role->name,['kepsek','etl']))
            <a class="collapse-item {{ request()->routeIs('iku.pegawai*') ? 'active' : '' }}" href="{{ route('iku.pegawai.index') }}">
              <i class="mdi mdi-chart-bar"></i>
              <span>IKU Pegawai</span>
            </a>
            @endif
            @if(Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',3)->count() > 0)
            @php
            $categories = Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',3)->with('aspek.kategori')->get()->pluck('aspek.kategori')->pluck('name')->unique()->flatten()->toArray();
            @endphp
            @if(in_array('Edukasi',$categories))
            <a class="collapse-item {{ request()->routeIs('iku.edukasi*') ? 'active' : '' }}" href="{{ route('iku.edukasi.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Edukasi</span>
            </a>
            @endif
            @if(in_array('Layanan',$categories))
            <a class="collapse-item {{ request()->routeIs('iku.layanan*') ? 'active' : '' }}" href="{{ route('iku.layanan.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Layanan</span>
            </a>
            @endif
            @if(in_array('Persepsi',$categories))
            <a class="collapse-item {{ request()->routeIs('iku.persepsi*') ? 'active' : '' }}" href="{{ route('iku.persepsi.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Persepsi</span>
            </a>
            @endif
            @if(in_array('Sasaran',$categories))
            <a class="collapse-item {{ request()->routeIs('iku.sasaran*') ? 'active' : '' }}" href="{{ route('iku.sasaran.index') }}">
              <i class="mdi mdi-flag-plus"></i>
              <span>IKU Sasaran</span>
            </a>
            @endif
            @if(Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1)->count() > 0)
            <hr class="sidebar-divider">
            @endif
            @endif
            @if(Auth::user()->pegawai->jabatan->ikuAspek()->select('iku_aspect_id')->where('pa_role_mapping_id',1)->count() > 0)
            <a class="collapse-item {{ request()->routeIs('iku.aspek*') ? 'active' : '' }}" href="{{ route('iku.aspek.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Aspek IKU</span>
            </a>
            @endif
          </div>
        </div>
      </li>
      @endif
      @endif
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
      <hr class="sidebar-divider">