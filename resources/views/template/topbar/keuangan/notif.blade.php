			<li class="nav-item dropdown no-arrow mx-1">
			  @php
			  $notifications = Auth::user()->notifikasi;
			  @endphp
        <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-bell fa-fw"></i>
				  @php $notifCount = $notifications->where('is_active',1)->count() @endphp
				  @if($notifCount > 0)
          <span class="badge badge-danger badge-counter">{{ $notifCount }}</span>
				  @endif
        </a>
        <div class="dropdown-list dropdown-menu notification dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
          <h6 class="dropdown-header">
            Notifikasi
          </h6>
				  @if(count($notifications) > 0)
				  <div class="alerts-container">
				    @foreach($notifications->sortByDesc('id')->all() as $n)
            <a class="dropdown-item d-flex align-items-center" href="{{ $n->link.'?notif_id='.$n->id }}">
              <div class="mr-3">
                <div class="icon-circle {{ $n->is_active == 1 ? ($n->kategori && $n->kategori->background ? $n->kategori->background : 'bg-primary') : 'bg-secondary' }}">
                  <i class="{{ $n->kategori && $n->kategori->icon ? $n->kategori->icon : 'fas fa-bell' }} text-white"></i>
                </div>
              </div>
              <div>
                <div class="small text-gray-500">{{ $n->created_at }}</div>
                  <span class="font-weight-bold">{{ $n->desc }}</span>
                </div>
              </a>
				    @endforeach
            <!-- <a class="dropdown-item text-center small text-gray-500" href="#">Tampilkan Semua</a> -->
				  </div>
				  @else
				  <div class="p-3 text-center">Anda tidak memiliki notifikasi apapun
				  </div>
				  @endif
        </div>
      </li>