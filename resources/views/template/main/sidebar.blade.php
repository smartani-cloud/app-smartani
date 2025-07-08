    <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('/'.explode('/',Request::path())[0]) }}">
        <div class="sidebar-brand-icon">
          <img src="{{ asset('img/logo/logomark.png') }}"> 
          <!--<i class="fas fa-smile-wink"></i>-->
        </div>
        <div class="sidebar-brand-text mx-2">
          <span class="brand-text text-dark"><img src="{{ asset('img/logo/logotype.png') }}"></span>
          <span class="brand-system text-uppercase">@yield('brand-system')</span>
        </div>
      </a>
      <hr class="sidebar-divider my-0">
	  @yield('sidebar-menu')
	  
      <div class="version mb-3" id="version-sista"></div>

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>
    </ul>
