<div class="sidebar-heading">
    Belajar Mengajar
</div>
    <!-- <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-book-open"></i>
            <span>Penilaian</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>Belajar Mengajar</span>
        </a>
    </li> -->
    @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','etl','etm','am']))
    <li class="nav-item {{ request()->routeIs('skbm*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('skbm*') ? 'active' : '' }}" href="{{ route('skbm.index') }}">
            <i class="mdi mdi-file-account"></i>
            <span>SKBM</span>
        </a>
    </li>
    @endif
    {{-- @if( in_array((auth()->user()->role_id), array(1,12,13,15,16,18,25,26,29,30,31,2,3)))
    <li class="nav-item {{ (request()->is('kependidikan/kbm/siswa*')) ? 'active' : '' }}">
        <a class="nav-link" href="/kependidikan/kbm/siswa">
            <i class="fas fa-fw fa-users"></i>
            <span>Siswa</span>
        </a>
    </li>
    @endif --}}

    @if( in_array((auth()->user()->role_id), array(25,26)))
    <li class="nav-item {{ (request()->is('kependidikan/kbm/siswa/aktif*')) ? 'active' : '' }}">
        <a class="nav-link" href="/kependidikan/kbm/siswa/aktif">
            <i class="fas fa-fw fa-users"></i>
            <span>Siswa</span>
        </a>
    </li>
    @endif
    @if( in_array((auth()->user()->role_id), array(1,2,3,7,9,11,12,13,15,16,18,29,30,31)))
    <li class="nav-item {{ (request()->is('kependidikan/kbm/siswa*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/kbm/siswa*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSiswaData" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="fas fa-fw fa-users"></i>
            <span>Siswa</span>
        </a>
        <div id="collapseSiswaData" class="collapse {{ (request()->is('kependidikan/kbm/siswa*')) ? 'show' : '' }}" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/siswa/aktif*') ? 'active' : '' }}" href="/kependidikan/kbm/siswa/aktif">
                    <i class="mdi mdi-account-check" aria-hidden="true"></i>
                    <span>Aktif</span>
                </a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/siswa/alumni*') ? 'active' : '' }}" href="/kependidikan/kbm/siswa/alumni">
                    <i class="mdi mdi-account-cancel" aria-hidden="true"></i>
                    <span>Alumni</span>
                </a>
            </div>
        </div>
    </li>
    @endif

    @if( in_array((auth()->user()->role_id), array(1,5,11,12,13,15,16,2,3,29,30)))
    <li class="nav-item {{ (request()->is('kependidikan/kbm/kelas*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/kbm/kelas*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKelas" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>Kelas</span>
        </a>
        <div id="collapseKelas" class="collapse {{ (request()->is('kependidikan/kbm/kelas*')) ? 'show' : '' }}" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if( in_array((auth()->user()->role_id), array(1,11,12,13,16,2)))
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/kelas/daftar-kelas*') ? 'active' : '' }}" href="/kependidikan/kbm/kelas/daftar-kelas">
                    <i class="mdi mdi-format-list-bulleted-square" aria-hidden="true"></i>
                    <span>Daftar Kelas</span>
                </a>
                @endif
                @if( in_array((auth()->user()->role_id), array(1,11,12,13,15,16,2,3,29,30)))
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/kelas/pengajuan-kelas*') ? 'active' : '' }}" href="/kependidikan/kbm/kelas/pengajuan-kelas">
                    <i class="mdi mdi-format-list-checks" aria-hidden="true"></i>
                    <span>Pengajuan Kelas</span>
                </a>
                @endif
                @php
                $iswali = App\Models\Kbm\Kelas::where('teacher_id', auth()->user()->pegawai->id)->count();
                $sidebarWali = auth()->user()->pegawai->position_id == '5' ? true : false;
                @endphp
                @if(in_array((auth()->user()->role_id), array(1)) || ($sidebarWali && $iswali > 0))
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/kelas/kelas-diampu*') ? 'active' : '' }}" href="/kependidikan/kbm/kelas/kelas-diampu">
                    <i class="mdi mdi-google-classroom" aria-hidden="true"></i>
                    <span>Siswa Kelas Ku</span>
                </a>
                @endif
                @if( in_array((auth()->user()->role_id), array(1,11,12,13,16,2)))
                <hr class="sidebar-divider">
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/kelas/nama-kelas*') ? 'active' : '' }}" href="/kependidikan/kbm/kelas/nama-kelas">
                    <i class="mdi mdi-cog" aria-hidden="true"></i> 
                    <span>Nama Kelas</span>
                </a>
                @if((in_array(auth()->user()->role_id, array(2)) && auth()->user()->pegawai->unit_id == 4) || in_array(auth()->user()->role_id, array(11,12,13)))
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/kelas/jurusan*') ? 'active' : '' }}" href="/kependidikan/kbm/kelas/jurusan">
                    <i class="mdi mdi-cog" aria-hidden="true"></i> 
                    <span>Jurusan</span>
                </a>
                @endif
                @endif
            </div>
        </div>
    </li>
    @endif
    @if( in_array((auth()->user()->role_id), array(1,11,12,13,15,16,2,3,5)))
    <li class="nav-item {{ (request()->is('kependidikan/kbm/pelajaran*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/kbm/pelajaran*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePelajaran" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="fas fa-fw fa-book-open"></i>
            <span>Pelajaran</span>
        </a>
        <div id="collapsePelajaran" class="collapse {{ (request()->is('kependidikan/kbm/pelajaran*')) ? 'show' : '' }}" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if( in_array((auth()->user()->role_id), array(1,11,12,13,15,16,2,3,5)))
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/pelajaran/jadwal-pelajaran*') ? 'active' : '' }}" href="/kependidikan/kbm/pelajaran/jadwal-pelajaran">
                    <i class="mdi mdi-calendar-clock" aria-hidden="true"></i>
                    <span>Jadwal Pelajaran</span>
                </a>
                @endif
                <hr class="sidebar-divider">
                @if( in_array((auth()->user()->role_id), array(1,11,12,13,15,16,2)))
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/pelajaran/kelompok-mata-pelajaran*') ? 'active' : '' }}" href="/kependidikan/kbm/pelajaran/kelompok-mata-pelajaran">
                    <i class="mdi mdi-cog" aria-hidden="true"></i> 
                    <span>Kel. Mata Pelajaran</span>
                </a>
                @endif
                @if( in_array((auth()->user()->role_id), array(1,11,12,13,15,16,2,5)))
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/pelajaran/mata-pelajaran*') ? 'active' : '' }}" href="/kependidikan/kbm/pelajaran/mata-pelajaran">
                    <i class="mdi mdi-cog" aria-hidden="true"></i> 
                    <span>Mata Pelajaran</span>
                </a>
                @endif
                @if( in_array((auth()->user()->role_id), array(1,11,12,13,16,2,3)))
                <a class="collapse-item {{ (Request::path()=='kependidikan/kbm/pelajaran/waktu-pelajaran*') ? 'active' : '' }}" href="/kependidikan/kbm/pelajaran/waktu-pelajaran">
                    <i class="mdi mdi-cog" aria-hidden="true"></i> 
                    <span>Jam Pelajaran</span>
                </a>
                @endif
            </div>
        </div>
    </li>
    @endif
    @if( in_array((auth()->user()->role_id), array(1,11,12,13,15,16)))
    <li class="nav-item {{ (request()->is('kependidikan/kbm/tahun-ajaran*')) ? 'active' : '' }}">
        <a class="nav-link" href="/kependidikan/kbm/tahun-ajaran">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Tahun Pelajaran</span>
        </a>
    </li>
    @endif
<hr class="sidebar-divider">