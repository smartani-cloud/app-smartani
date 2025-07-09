<<<<<<< HEAD
<div class="sidebar-heading">
    Penilaian
</div>
<li class="nav-item {{ request()->routeIs('penilaian.ikuEdukasi.persen*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('penilaian.ikuEdukasi.persen') }}">
      <i class="mdi mdi-file-percent"></i>
      <span>IKU Edukasi</span>
  </a>
</li>
{{--
<li class="nav-item {{ request()->routeIs('penilaian.ikuEdukasi.unit*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('penilaian.ikuEdukasi.unit') }}">
        <i class="mdi mdi-trophy"></i>
        <span>Ledger Unit</span>
    </a>
</li>
--}}
@php $menuName = 'Ledger'; @endphp
<li class="nav-item {{ request()->routeIs('penilaian.ikuEdukasi.unit*') || request()->routeIs('penilaian.ikuEdukasi.kelas*') ? 'active' : '' }}">
    <a class="nav-link {{ request()->routeIs('penilaian.ikuEdukasi.unit*') || request()->routeIs('penilaian.ikuEdukasi.kelas*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapse{{ $menuName }}" aria-expanded="{{ request()->routeIs('penilaian.ikuEdukasi.unit*') || request()->routeIs('penilaian.ikuEdukasi.kelas*') ? 'true' : 'false' }}" aria-controls="collapse{{ $menuName }}">
        <i class="mdi mdi-trophy"></i>
        <span>{{ $menuName }}</span>
    </a>
    <div id="collapse{{ $menuName }}" class="collapse {{ request()->routeIs('penilaian.ikuEdukasi.unit*') || request()->routeIs('penilaian.ikuEdukasi.kelas*') ? 'show' : '' }}" aria-labelledby="heading{{ $menuName }}" data-parent="#accordionSidebar" style="">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Ledger</h6>
            <a class="collapse-item {{ request()->routeIs('penilaian.ikuEdukasi.unit*') ? 'active' : '' }}" href="{{ route('penilaian.ikuEdukasi.unit') }}">
              <i class="mdi mdi-office-building"></i>
              <span>Unit</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('penilaian.ikuEdukasi.kelas*') ? 'active' : '' }}" href="{{ route('penilaian.ikuEdukasi.kelas') }}">
              <i class="mdi mdi-book-education"></i>
              <span>Kelas</span>
            </a>
        </div>
    </div>
</li>
=======
<div class="sidebar-heading">
    Penilaian
</div>
<li class="nav-item {{ request()->routeIs('penilaian.ikuEdukasi.persen*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('penilaian.ikuEdukasi.persen') }}">
      <i class="mdi mdi-file-percent"></i>
      <span>IKU Edukasi</span>
  </a>
</li>
{{--
<li class="nav-item {{ request()->routeIs('penilaian.ikuEdukasi.unit*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('penilaian.ikuEdukasi.unit') }}">
        <i class="mdi mdi-trophy"></i>
        <span>Ledger Unit</span>
    </a>
</li>
--}}
@php $menuName = 'Ledger'; @endphp
<li class="nav-item {{ request()->routeIs('penilaian.ikuEdukasi.unit*') || request()->routeIs('penilaian.ikuEdukasi.kelas*') ? 'active' : '' }}">
    <a class="nav-link {{ request()->routeIs('penilaian.ikuEdukasi.unit*') || request()->routeIs('penilaian.ikuEdukasi.kelas*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapse{{ $menuName }}" aria-expanded="{{ request()->routeIs('penilaian.ikuEdukasi.unit*') || request()->routeIs('penilaian.ikuEdukasi.kelas*') ? 'true' : 'false' }}" aria-controls="collapse{{ $menuName }}">
        <i class="mdi mdi-trophy"></i>
        <span>{{ $menuName }}</span>
    </a>
    <div id="collapse{{ $menuName }}" class="collapse {{ request()->routeIs('penilaian.ikuEdukasi.unit*') || request()->routeIs('penilaian.ikuEdukasi.kelas*') ? 'show' : '' }}" aria-labelledby="heading{{ $menuName }}" data-parent="#accordionSidebar" style="">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Ledger</h6>
            <a class="collapse-item {{ request()->routeIs('penilaian.ikuEdukasi.unit*') ? 'active' : '' }}" href="{{ route('penilaian.ikuEdukasi.unit') }}">
              <i class="mdi mdi-office-building"></i>
              <span>Unit</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('penilaian.ikuEdukasi.kelas*') ? 'active' : '' }}" href="{{ route('penilaian.ikuEdukasi.kelas') }}">
              <i class="mdi mdi-book-education"></i>
              <span>Kelas</span>
            </a>
        </div>
    </div>
</li>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
<hr class="sidebar-divider">