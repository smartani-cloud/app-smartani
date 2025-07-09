      <div class="sidebar-heading">
        Monitoring Greenhouse
      </div>
      <li class="nav-item {{ request()->routeIs('sensor-feedback*') || request()->routeIs('sensor-list*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('sensor-feedback*') || request()->routeIs('sensor-list*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSensor" aria-expanded="{{ request()->routeIs('sensor-feedback*') || request()->routeIs('sensor-list*') ? 'true' : 'false' }}" aria-controls="collapseSensor">
          <i class="mdi mdi-sprout"></i>
          <span>Sensor</span>
        </a>
        <div id="collapseSensor" class="collapse {{ request()->routeIs('sensor-feedback*') || request()->routeIs('sensor-list*') ? 'show' : '' }}" aria-labelledby="headingSensor" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Sensor</h6>
            <a class="collapse-item {{ request()->routeIs('sensor-feedback*') ? 'active' : '' }}" href="{{ route('sensor-feedback.index') }}">
              <i class="mdi mdi-comment-check"></i>
              <span>Sensor Feedback</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('sensor-list*') ? 'active' : '' }}" href="{{ route('sensor-list.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Sensor</span>
            </a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">