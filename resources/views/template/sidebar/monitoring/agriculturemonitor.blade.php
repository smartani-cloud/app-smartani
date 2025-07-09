<<<<<<< HEAD
      <div class="sidebar-heading">
        Monitoring Greenhouse
      </div>
      <li class="nav-item {{ request()->routeIs('sensor*') || request()->routeIs('plant-type*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('sensor*') || request()->routeIs('plant-type*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSensor" aria-expanded="{{ request()->routeIs('sensor*') || request()->routeIs('plant-type*') ? 'true' : 'false' }}" aria-controls="collapseSensor">
          <i class="mdi mdi-sprout"></i>
          <span>Sensor</span>
        </a>
        <div id="collapseSensor" class="collapse {{ request()->routeIs('sensor*') || request()->routeIs('plant-type*') ? 'show' : '' }}" aria-labelledby="headingSensor" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Sensor</h6>
            <a class="collapse-item {{ request()->routeIs('sensor*') ? 'active' : '' }}" href="{{ route('sensor.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Sensor</span>
            </a>
          </div>
        </div>
      </li>
=======
      <div class="sidebar-heading">
        Monitoring Greenhouse
      </div>
      <li class="nav-item {{ request()->routeIs('sensor*') || request()->routeIs('plant-type*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('sensor*') || request()->routeIs('plant-type*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSensor" aria-expanded="{{ request()->routeIs('sensor*') || request()->routeIs('plant-type*') ? 'true' : 'false' }}" aria-controls="collapseSensor">
          <i class="mdi mdi-sprout"></i>
          <span>Sensor</span>
        </a>
        <div id="collapseSensor" class="collapse {{ request()->routeIs('sensor*') || request()->routeIs('plant-type*') ? 'show' : '' }}" aria-labelledby="headingSensor" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Sensor</h6>
            <a class="collapse-item {{ request()->routeIs('sensor*') ? 'active' : '' }}" href="{{ route('sensor.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Sensor</span>
            </a>
          </div>
        </div>
      </li>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
      <hr class="sidebar-divider">