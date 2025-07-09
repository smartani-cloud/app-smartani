<<<<<<< HEAD

@if( in_array((auth()->user()->role_id), array(1,2,8,11,12,13,18,25,26,29,50)))
<div class="sidebar-heading">
    Pembayaran Uang Sekolah
</div>
@if(in_array(auth()->user()->role_id,array(25,26,29,50)))
<li class="nav-item {{ request()->is('keuangan/pemindahan-transaksi*') ? 'active' : '' }}">
    <a class="nav-link {{ request()->is('keuangan/pemindahan-transaksi*') ? 'active' : '' }}" href="{{url('/keuangan/pemindahan-transaksi')}}">
        <i class="mdi mdi-arrow-left-right-bold"></i>
        <span>Pemindahan Transaksi</span>
    </a>
</li>
@endif
<li class="nav-item {{ request()->routeIs('bms*') ? 'active' : '' }}">
    <a class="nav-link {{ request()->routeIs('bms*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseBms" aria-expanded="{{ request()->routeIs('bms*') ? 'true' : 'false' }}" aria-controls="collapseBms">
      <i class="mdi mdi-briefcase-variant"></i>
      <span>BMS</span>
    </a>
    <div id="collapseBms" class="collapse {{ request()->routeIs('bms*') ? 'show' : '' }}" aria-labelledby="headingBms" data-parent="#accordionSidebar" style="">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">BMS</h6>
        <a class="collapse-item {{ request()->routeIs('bms.dasbor*') ? 'active' : '' }}" href="{{ route('bms.dasbor.index') }}">
          <i class="mdi mdi-table"></i>
          <span>Dashboard BMS</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('bms.status*') ? 'active' : '' }}" href="{{ route('bms.status.index') }}">
          <i class="mdi mdi-account-details"></i>
          <span>Status BMS</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('bms.va*') ? 'active' : '' }}" href="{{ route('bms.va.index') }}">
          <i class="mdi mdi-credit-card"></i>
          <span>Virtual Account BMS</span>
        </a>
        @if(in_array(Auth::user()->role->name,['pembinayys','ketuayys','dir','fam','am']))
        <a class="collapse-item {{ request()->routeIs('bms.nominal*') ? 'active' : '' }}" href="{{ route('bms.nominal.index') }}">
          <i class="mdi mdi-tag"></i>
          <span>Nominal BMS</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('bms.potongan*') ? 'active' : '' }}" href="{{ route('bms.potongan.index') }}">
          <i class="mdi mdi-ticket-percent"></i>
          <span>Potongan BMS</span>
        </a>
        @endif
        <hr class="sidebar-divider">
        <a class="collapse-item {{ request()->routeIs('bms.pembayaran*') ? 'active' : '' }}" href="{{ route('bms.pembayaran.index') }}">
          <i class="mdi mdi-book-arrow-left"></i>
          <span>Lap. Pembayaran BMS</span>
        </a>
      </div>
    </div>
</li>
<li class="nav-item {{ request()->routeIs('spp*') ? 'active' : '' }}">
    <a class="nav-link {{ request()->routeIs('spp*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSpp" aria-expanded="{{ request()->routeIs('spp*') ? 'true' : 'false' }}" aria-controls="collapseSpp">
      <i class="mdi mdi-cash"></i>
      <span>SPP</span>
    </a>
    <div id="collapseSpp" class="collapse {{ request()->routeIs('spp*') ? 'show' : '' }}" aria-labelledby="headingSpp" data-parent="#accordionSidebar" style="">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">SPP</h6>
        <a class="collapse-item {{ request()->routeIs('spp.dasbor*') ? 'active' : '' }}" href="{{ route('spp.dasbor.index') }}">
          <i class="mdi mdi-table"></i>
          <span>Dashboard SPP</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('spp.status*') ? 'active' : '' }}" href="{{ route('spp.status.index') }}">
          <i class="mdi mdi-account-details"></i>
          <span>Status SPP</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('spp.va*') ? 'active' : '' }}" href="{{ route('spp.va.index') }}">
          <i class="mdi mdi-credit-card"></i>
          <span>Virtual Account SPP</span>
        </a>
        @if(in_array(Auth::user()->role->name,['pembinayys','ketuayys','dir','fam','am']))
        <a class="collapse-item {{ request()->routeIs('spp.potongan*') ? 'active' : '' }}" href="{{ route('spp.potongan.index') }}">
          <i class="mdi mdi-ticket-percent"></i>
          <span>Potongan SPP</span>
        </a>
        @endif
        <hr class="sidebar-divider">
        <a class="collapse-item {{ request()->routeIs('spp.laporan*') ? 'active' : '' }}" href="{{ route('spp.laporan.index') }}">
          <i class="mdi mdi-list-status"></i>
          <span>Laporan SPP Siswa</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('spp.pembayaran*') ? 'active' : '' }}" href="{{ route('spp.pembayaran.index') }}">
          <i class="mdi mdi-book-arrow-left"></i>
          <span>Lap. Pembayaran SPP</span>
        </a>
		@if(in_array(Auth::user()->role->name,['faspv','akunspv']))
        <a class="collapse-item {{ request()->routeIs('spp.generator*') ? 'active' : '' }}" href="{{ route('spp.generator.index') }}">
          <i class="mdi mdi-cog-refresh"></i>
          <span>SPP Generator</span>
        </a>
        @endif
      </div>
    </div>
</li>
<hr class="sidebar-divider">
=======

@if( in_array((auth()->user()->role_id), array(1,2,8,11,12,13,18,25,26,29,50)))
<div class="sidebar-heading">
    Pembayaran Uang Sekolah
</div>
@if(in_array(auth()->user()->role_id,array(25,26,29,50)))
<li class="nav-item {{ request()->is('keuangan/pemindahan-transaksi*') ? 'active' : '' }}">
    <a class="nav-link {{ request()->is('keuangan/pemindahan-transaksi*') ? 'active' : '' }}" href="{{url('/keuangan/pemindahan-transaksi')}}">
        <i class="mdi mdi-arrow-left-right-bold"></i>
        <span>Pemindahan Transaksi</span>
    </a>
</li>
@endif
<li class="nav-item {{ request()->routeIs('bms*') ? 'active' : '' }}">
    <a class="nav-link {{ request()->routeIs('bms*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseBms" aria-expanded="{{ request()->routeIs('bms*') ? 'true' : 'false' }}" aria-controls="collapseBms">
      <i class="mdi mdi-briefcase-variant"></i>
      <span>BMS</span>
    </a>
    <div id="collapseBms" class="collapse {{ request()->routeIs('bms*') ? 'show' : '' }}" aria-labelledby="headingBms" data-parent="#accordionSidebar" style="">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">BMS</h6>
        <a class="collapse-item {{ request()->routeIs('bms.dasbor*') ? 'active' : '' }}" href="{{ route('bms.dasbor.index') }}">
          <i class="mdi mdi-table"></i>
          <span>Dashboard BMS</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('bms.status*') ? 'active' : '' }}" href="{{ route('bms.status.index') }}">
          <i class="mdi mdi-account-details"></i>
          <span>Status BMS</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('bms.va*') ? 'active' : '' }}" href="{{ route('bms.va.index') }}">
          <i class="mdi mdi-credit-card"></i>
          <span>Virtual Account BMS</span>
        </a>
        @if(in_array(Auth::user()->role->name,['pembinayys','ketuayys','dir','fam','am']))
        <a class="collapse-item {{ request()->routeIs('bms.nominal*') ? 'active' : '' }}" href="{{ route('bms.nominal.index') }}">
          <i class="mdi mdi-tag"></i>
          <span>Nominal BMS</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('bms.potongan*') ? 'active' : '' }}" href="{{ route('bms.potongan.index') }}">
          <i class="mdi mdi-ticket-percent"></i>
          <span>Potongan BMS</span>
        </a>
        @endif
        <hr class="sidebar-divider">
        <a class="collapse-item {{ request()->routeIs('bms.pembayaran*') ? 'active' : '' }}" href="{{ route('bms.pembayaran.index') }}">
          <i class="mdi mdi-book-arrow-left"></i>
          <span>Lap. Pembayaran BMS</span>
        </a>
      </div>
    </div>
</li>
<li class="nav-item {{ request()->routeIs('spp*') ? 'active' : '' }}">
    <a class="nav-link {{ request()->routeIs('spp*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSpp" aria-expanded="{{ request()->routeIs('spp*') ? 'true' : 'false' }}" aria-controls="collapseSpp">
      <i class="mdi mdi-cash"></i>
      <span>SPP</span>
    </a>
    <div id="collapseSpp" class="collapse {{ request()->routeIs('spp*') ? 'show' : '' }}" aria-labelledby="headingSpp" data-parent="#accordionSidebar" style="">
      <div class="bg-white py-2 collapse-inner rounded">
        <h6 class="collapse-header">SPP</h6>
        <a class="collapse-item {{ request()->routeIs('spp.dasbor*') ? 'active' : '' }}" href="{{ route('spp.dasbor.index') }}">
          <i class="mdi mdi-table"></i>
          <span>Dashboard SPP</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('spp.status*') ? 'active' : '' }}" href="{{ route('spp.status.index') }}">
          <i class="mdi mdi-account-details"></i>
          <span>Status SPP</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('spp.va*') ? 'active' : '' }}" href="{{ route('spp.va.index') }}">
          <i class="mdi mdi-credit-card"></i>
          <span>Virtual Account SPP</span>
        </a>
        @if(in_array(Auth::user()->role->name,['pembinayys','ketuayys','dir','fam','am']))
        <a class="collapse-item {{ request()->routeIs('spp.potongan*') ? 'active' : '' }}" href="{{ route('spp.potongan.index') }}">
          <i class="mdi mdi-ticket-percent"></i>
          <span>Potongan SPP</span>
        </a>
        @endif
        <hr class="sidebar-divider">
        <a class="collapse-item {{ request()->routeIs('spp.laporan*') ? 'active' : '' }}" href="{{ route('spp.laporan.index') }}">
          <i class="mdi mdi-list-status"></i>
          <span>Laporan SPP Siswa</span>
        </a>
        <a class="collapse-item {{ request()->routeIs('spp.pembayaran*') ? 'active' : '' }}" href="{{ route('spp.pembayaran.index') }}">
          <i class="mdi mdi-book-arrow-left"></i>
          <span>Lap. Pembayaran SPP</span>
        </a>
		@if(in_array(Auth::user()->role->name,['faspv','akunspv']))
        <a class="collapse-item {{ request()->routeIs('spp.generator*') ? 'active' : '' }}" href="{{ route('spp.generator.index') }}">
          <i class="mdi mdi-cog-refresh"></i>
          <span>SPP Generator</span>
        </a>
        @endif
      </div>
    </div>
</li>
<hr class="sidebar-divider">
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endif