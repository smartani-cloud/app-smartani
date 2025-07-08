        <nav class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top">
          <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
          <ul class="navbar-nav ml-auto">
            <!--
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-1 small" placeholder="Apa yang Anda cari?" aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-brand-purple" type="button">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter">3+</span>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  Alerts Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-primary">
                      <i class="fas fa-file-alt text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 12, 2019</div>
                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-success">
                      <i class="fas fa-donate text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 7, 2019</div>
                    $290.29 has been deposited into your account!
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-warning">
                      <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 2, 2019</div>
                    Spending Alert: We've noticed unusually high spending for your account.
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
              </div>
            </li>
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <span class="badge badge-warning badge-counter">2</span>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                  Message Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="{{ asset('img/man.png') }}" style="max-width: 60px" alt="">
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate">Hi there! I am wondering if you can help me with a problem I've been
                      having.</div>
                    <div class="small text-gray-500">Indra · 58m</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="{{ asset('img/boy.png') }}" style="max-width: 60px" alt="">
                    <div class="status-indicator bg-default"></div>
                  </div>
                  <div>
                    <div class="text-truncate">Am I a good boy? The reason I ask is because someone told me that people
                      say this to all dogs, even if they aren't good...</div>
                    <div class="small text-gray-500">Nafiu · 2w</div>
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
              </div>
            </li>
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-tasks fa-fw"></i>
                <span class="badge badge-primary badge-counter">3</span>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                  Task
                </h6>
                <a class="dropdown-item align-items-center" href="#">
                  <div class="mb-3">
                    <div class="small text-gray-500">Design Button
                      <div class="small float-right"><b>50%</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                      <div class="progress-bar bg-success" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </a>
                <a class="dropdown-item align-items-center" href="#">
                  <div class="mb-3">
                    <div class="small text-gray-500">Make Beautiful Transitions
                      <div class="small float-right"><b>30%</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                      <div class="progress-bar bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </a>
                <a class="dropdown-item align-items-center" href="#">
                  <div class="mb-3">
                    <div class="small text-gray-500">Create Pie Chart
                      <div class="small float-right"><b>75%</b></div>
                    </div>
                    <div class="progress" style="height: 12px;">
                      <div class="progress-bar bg-danger" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">View All Taks</a>
              </div>
            </li>-->
            <!-- if(request()->is('kependidikan*'))
            include('template.topbar.gurumapel')
            endif -->
            @if(Auth::user()->role->name == 'keulsi')
      			<li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-walking fa-fw"></i>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow change-role animated--grow-in" aria-labelledby="changeSystemDropdown">
                <h6 class="dropdown-header">
                  Beralih Unit
                </h6>
                @php
                $color = ['warning','brand-green','brand-purple','info','brand-green-dark',
                'danger'];
                $i = 0;
                @endphp
                @foreach(Auth::user()->pegawai->units()->orderBy('unit_id')->get() as $u)
                @if(Auth::user()->pegawai->unit_id == $u->unit->id)
                <a class="dropdown-item d-flex align-items-center disabled" href="#">
                  <i class="fas fa-school text-{{ $color[$i] }} mr-2"></i>
                  <div>
                    <span class="font-weight-normal">{{ $u->unit->name }}</span><span class="text-success ml-2">•</span>
                  </div>
                </a>
                @else
                <a class="dropdown-item d-flex align-items-center" href="{{ route('akun.unit.change', ['unit' => $u->unit->name]) }}">
                  <i class="fas fa-school text-{{ $color[$i] }} mr-2"></i>
                  <div>
                    <span class="font-weight-bold">{{ $u->unit->name }}</span>
                  </div>
                </a>
                @endif
                @php
                if($i == (count($color)-1)) $i = 0;
                else $i++;
                @endphp
                @endforeach
              </div>
            </li>
			      @endif
            @if(Auth::user()->pegawai->units()->has('jabatans')->with('jabatans')->get()->pluck('jabatans')->flatten()->count() > 1)
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-walking fa-fw"></i>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow change-role animated--grow-in" aria-labelledby="changeSystemDropdown">
                <h6 class="dropdown-header">
                  Beralih Peran
                </h6>
                @php
                $color = ['warning','brand-green','brand-purple','info','brand-green-dark',
                'danger'];
                $i = 0;
                @endphp
                @foreach(Auth::user()->pegawai->units()->orderBy('unit_id')->get() as $u)
                @if($u->jabatans()->count() > 0)
                <a class="dropdown-item disabled" href="#">{{ $u->unit->name }}
                </a>
                @foreach($u->jabatans()->orderBy('position_id')->get() as $j)
                @if(Auth::user()->pegawai->unit_id == $u->unit->id && Auth::user()->pegawai->position_id == $j->id)
                <a class="dropdown-item d-flex align-items-center disabled" href="#">
                  <i class="fas fa-user-check text-{{ $color[$i] }} mr-2"></i>
                  <div>
                    <span class="font-weight-normal">{{ $j->name }}</span><span class="text-success ml-2">•</span>
                  </div>
                </a>
                @else
                <a class="dropdown-item d-flex align-items-center" href="{{ route('akun.role.change', ['unit' => $u->unit->name, 'position' => Crypt::encryptString($j->id)])}}">
                  <i class="fas fa-user-check text-{{ $color[$i] }} mr-2"></i>
                  <div>
                    <span class="font-weight-bold">{{ $j->name }}</span>
                  </div>
                </a>
                @endif
                @php
                if($i == (count($color)-1)) $i = 0;
                else $i++;
                @endphp
                @endforeach
                @endif
                @endforeach
              </div>
            </li>
            @endif
			      @if(Auth::user()->role->name != 'keulsi')
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-exchange-alt fa-fw"></i>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow change-system animated--grow-in" aria-labelledby="changeSystemDropdown">
                <h6 class="dropdown-header">
                  Pindah Sistem
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('kepegawaian.index') }}">
                  <div class="mr-3">
                    <i class="zmdi zmdi-accounts-list-alt zmdi-hc-2x text-brand-blue"></i>
                  </div>
                  <div>
                    <span class="font-weight-bold">HR</span>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="{{ route('dashboard.index') }}">
                  <div class="mr-3">
                    <i class="zmdi zmdi-local-florist zmdi-hc-2x text-brand-green-dark"></i>
                  </div>
                  <div>
                    <span class="font-weight-bold">Monitoring</span>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <i class="zmdi zmdi-balance-wallet zmdi-hc-2x text-warning"></i>
                  </div>
                  <div>
                    <span class="font-weight-bold">Finance</span>
                  </div>
                </a>
              </div>
            </li>
      			@endif
      			@if(request()->is('keuangan*') && in_array(Auth::user()->role->name,['kepsek','ketuayys','direktur','fam','faspv','etl','ctl','am','ftm','keulsi']))
            @include('template.topbar.keuangan.notif')
            @endif
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="img-profile" style="max-width: 60px">
                  <img src="{{ asset(Auth::user()->pegawai->showPhoto) }}" class="avatar-img rounded-circle">
                </div>
                <span class="ml-2 d-none d-lg-inline text-white small">{{ Auth::user()->pegawai->nickname }}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in dropdown-profile" aria-labelledby="userDropdown">
                <div class="px-4 py-2 d-flex align-items-center">
                  <div class="mr-3">
                    <div class="avatar-med">
                      <img src="{{ asset(Auth::user()->pegawai->showPhoto) }}" class="avatar-img rounded-circle">
                    </div>
                  </div>
                  <div>
                    <h5 class="font-weight-bold mb-1">{{ Auth::user()->pegawai->name }}</h5>
                    <h6 class="mb-0"><i class="mdi mdi-office-building mr-2 text-brand-blue-dark"></i>{{ Auth::user()->pegawai->unit->name }}</h6>
                  </div>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('akun.index') }}">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profil Saya
                </a>
                <!--
                <a class="dropdown-item" href="{{ route('ubahsandi.index') }}">
                  <i class="fas fa-lock fa-sm fa-fw mr-2 text-gray-400"></i>
                  Ubah Sandi
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Pengaturan
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  Activity Log
                </a>-->
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="javascript:void(0);" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Keluar
                </a>
              </div>
            </li>
          </ul>
        </nav>