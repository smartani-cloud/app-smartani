<div class="sidebar-heading">
        Manajemen Greenhouse
      </div>
      <li class="nav-item {{ request()->routeIs('greenhouse-list*') || request()->routeIs('planting-cycle*') || request()->routeIs('greenhouse-owner*') || request()->routeIs('irrigation-system*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('greenhouse-list*') || request()->routeIs('planting-cycle*') || request()->routeIs('greenhouse-owner*') || request()->routeIs('irrigation-system*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseGreenhouse" aria-expanded="{{ request()->routeIs('greenhouse-list*') || request()->routeIs('planting-cycle*') || request()->routeIs('greenhouse-owner*') || request()->routeIs('irrigation-system*') ? 'true' : 'false' }}" aria-controls="collapseGreenhouse">
          <i class="mdi mdi-hoop-house"></i>
          <span>Greenhouse</span>
        </a>
        <div id="collapseGreenhouse" class="collapse {{ request()->routeIs('greenhouse-list*') || request()->routeIs('planting-cycle*') || request()->routeIs('greenhouse-owner*') || request()->routeIs('irrigation-system*') ? 'show' : '' }}" aria-labelledby="headingGreenhouse" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Greenhouse</h6>
            <a class="collapse-item {{ request()->routeIs('greenhouse-list*') ? 'active' : '' }}" href="{{ route('greenhouse-list.index') }}">
              <i class="mdi mdi-view-list"></i>
              <span>Daftar Greenhouse</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('planting-cycle*') ? 'active' : '' }}" href="{{ route('planting-cycle.index') }}">
            <i class="mdi mdi-refresh"></i>
              <span>Siklus Tanam</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('greenhouse-owner*') ? 'active' : '' }}" href="{{ route('greenhouse-owner.index') }}">
              <i class="mdi mdi-home-account"></i>
              <span>Pemilik Greenhouse</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('irrigation-system*') ? 'active' : '' }}" href="{{ route('irrigation-system.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Sistem Irigasi</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('plant-list*') || request()->routeIs('plant-list*') || request()->routeIs('plant-category*') || request()->routeIs('plant-type*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('plant-list*') || request()->routeIs('plant-category*') || request()->routeIs('plant-type*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePlant" aria-expanded="{{ request()->routeIs('plant-list*') || request()->routeIs('plant-category*') || request()->routeIs('plant-type*') ? 'true' : 'false' }}" aria-controls="collapsePlant">
          <i class="mdi mdi-sprout"></i>
          <span>Tanaman</span>
        </a>
        <div id="collapsePlant" class="collapse {{ request()->routeIs('plant-list*') || request()->routeIs('plant-category*') || request()->routeIs('plant-type*') ? 'show' : '' }}" aria-labelledby="headingPlant" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tanaman</h6>
            <a class="collapse-item {{ request()->routeIs('plant-list*') ? 'active' : '' }}" href="{{ route('plant-list.index') }}">
              <i class="mdi mdi-view-list"></i>
              <span>Daftar Tanaman</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('plant-category*') ? 'active' : '' }}" href="{{ route('plant-category.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Kategori Tanaman</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('plant-type*') ? 'active' : '' }}" href="{{ route('plant-type.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Jenis Tanaman</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('harvest-category*') || request()->routeIs('harvest-quality*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('harvest-category*') || request()->routeIs('harvest-quality*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseHarvest" aria-expanded="{{ request()->routeIs('harvest-category*') || request()->routeIs('harvest-quality*') ? 'true' : 'false' }}" aria-controls="collapseHarvest">
          <i class="mdi mdi-basket"></i>
          <span>Panen</span>
        </a>
        <div id="collapseHarvest" class="collapse {{ request()->routeIs('harvest-category*') || request()->routeIs('harvest-quality*') ? 'show' : '' }}" aria-labelledby="headingHarvest" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Panen</h6>
            <a class="collapse-item {{ request()->routeIs('harvest-category*') ? 'active' : '' }}" href="{{ route('harvest-category.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Kategori Panen</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('harvest-quality*') ? 'active' : '' }}" href="{{ route('harvest-quality.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Kualitas Panen</span>
            </a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">