        <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
          <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
          <ul class="navbar-nav ml-auto">
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="img-profile" style="max-width: 60px">
                  <img src="/img/avatar/default.png" class="avatar-img rounded-circle">
                </div>
                <span class="ml-2 d-none d-lg-inline text-white small">{{auth()->user()->username}}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in dropdown-profile" aria-labelledby="userDropdown">
                <div class="px-4 py-2 d-flex align-items-center">
                  <div class="mr-3">
                    <div class="avatar-med">
                      <img src="/img/avatar/default.png" class="avatar-img rounded-circle">
                    </div>
                  </div>
                  <div>
                    <h5 class="font-weight-bold mb-1">{{auth()->user()->username}}</h5>
                  </div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('psb.profil') }}">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profil Saya
                </a>
                <a class="dropdown-item" href="{{ route('psb.password.get') }}">
                  {{-- <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> --}}
                  <i class="fas fa-user-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                  Ubah Password
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Keluar
                </a>
              </div>
            </li>
          </ul>
        </nav>