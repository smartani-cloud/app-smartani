@if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','cspv','fam','faspv','es','sek','keu','lay']))
<div class="sidebar-heading">
    Penerimaan Siswa Baru
</div>
    @if(!in_array(Auth::user()->role->name,['am','aspv']))
    <li class="nav-item {{ request()->routeIs('kependidikan.psb..dashboard*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('kependidikan.psb..dashboard*') ? 'active' : '' }}" href="{{route('kependidikan.psb..dashboard')}}">
            <i class="mdi mdi-file-account"></i>
            <span>Dashboard</span>
        </a>
    </li>
    <li class="nav-item {{ (request()->is('kependidikan/infopsb/chart*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/infopsb/chart*')) ? 'active' : '' }}" href="{{route('kependidikan.infopsb.chart')}}">
            <i class="mdi mdi-chart-bar"></i>
            <span>Chart</span>
        </a>
    </li>
	@endif
    <li class="nav-item {{ (request()->is('kependidikan/psb*') && !request()->routeIs('kependidikan.psb.ortu*') && !request()->routeIs('kependidikan.psb.kunci*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/psb*') && !request()->routeIs('kependidikan.psb.ortu*') && !request()->routeIs('kependidikan.psb.kunci*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSiswa" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="fas fa-fw fa-chalkboard-teacher"></i>
            <span>Calon Siswa</span>
        </a>
        <div id="collapseSiswa" class="collapse {{ (request()->is('kependidikan/psb*') && !request()->routeIs('kependidikan.psb.ortu*') && !request()->routeIs('kependidikan.psb.kunci*')) ? 'show' : '' }}" aria-labelledby="headingBootstrap" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','cspv','fam','faspv','es','sek','keu','lay']))
                <a class="collapse-item {{ (request()->is('kependidikan/psb/formulir-terisi*')) ? 'active' : '' }}" href="/kependidikan/psb/formulir-terisi">
                    <i class="mdi mdi-file-account" aria-hidden="true"></i>
                    <span>Formulir Terisi</span>
                </a>
                @endif
                @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','cspv','fam','faspv','es','sek','keu','lay']))
                <a class="collapse-item {{ (request()->is('kependidikan/psb/saving-seat*')) ? 'active' : '' }}" href="/kependidikan/psb/saving-seat">
                    <i class="mdi mdi-cash" aria-hidden="true"></i>
                    <span>Biaya Observasi</span>
                </a>
                @endif
                @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','cspv','fam','faspv','es','sek','keu','lay']))
                <a class="collapse-item {{ (request()->is('kependidikan/psb/wawancara*')) ? 'active' : '' }}" href="/kependidikan/psb/wawancara">
                    <i class="mdi mdi-comment" aria-hidden="true"></i>
                    <span>Wawancara</span>
                </a>
                @endif
                @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','cspv','fam','faspv','es','sek','keu','lay']))
                <a class="collapse-item {{ (request()->is('kependidikan/psb/diterima*')) ? 'active' : '' }}" href="/kependidikan/psb/diterima">
                    <i class="mdi mdi-account-check" aria-hidden="true"></i>
                    <span>Diterima</span>
                </a>
                @endif
                @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','fam','faspv','es','keu']))
                <a class="collapse-item {{ (request()->is('kependidikan/psb/belum-lunas*')) ? 'active' : '' }}" href="/kependidikan/psb/belum-lunas">
                    <i class="mdi mdi-timer-sand" aria-hidden="true"></i>
                    <span>Belum Lunas</span>
                </a>
                @endif
                @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','fam','faspv','es','keu']))
                <a class="collapse-item {{ (request()->is('kependidikan/psb/sudah-lunas*')) ? 'active' : '' }}" href="/kependidikan/psb/sudah-lunas">
                    <i class="mdi mdi-cash-multiple" aria-hidden="true"></i>
                    <span>Sudah Lunas</span>
                </a>
                @endif
                @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','cspv','fam','faspv','es','sek','keu','lay']))
                <a class="collapse-item {{ (request()->is('kependidikan/psb/peresmian-siswa*')) ? 'active' : '' }}" href="/kependidikan/psb/peresmian-siswa">
                    <i class="mdi mdi-bank-check" aria-hidden="true"></i>
                    <span>Peresmian Siswa</span>
                </a>
                @endif
                @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','cspv','es','sek','keu','lay']))
                <hr class="sidebar-divider">
                    <a class="collapse-item {{ (request()->is('kependidikan/psb/dicadangkan*')) ? 'active' : '' }}" href="/kependidikan/psb/dicadangkan">
                        <i class="mdi mdi-account-remove" aria-hidden="true"></i>
                        <span>Dicadangkan</span>
                    </a>
                    @endif
                    @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','kepsek','wakasek','am','aspv','ctl','ctm','cspv','fam','faspv','keu']))
                    <a class="collapse-item {{ (request()->is('kependidikan/psb/kelas/batal-daftar-ulang*')) ? 'active' : '' }}" href="/kependidikan/psb/batal-daftar-ulang">
                        <i class="mdi mdi-currency-usd-off" aria-hidden="true"></i>
                        <span>Batal Daftar Ulang</span>
                    </a>
                    @endif
            </div>
        </div>
    </li>
    @if(in_array(Auth::user()->role->name,['sek','lay','ketuayys','pembinayys','direktur','am','aspv','cspv']))
    <li class="nav-item {{ request()->routeIs('kependidikan.psb.ortu*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('kependidikan.psb.ortu*') ? 'active' : '' }}" href="{{route('kependidikan.psb.ortu.index')}}">
            <i class="mdi mdi-home-heart"></i>
            <span>Orang Tua</span>
        </a>
    </li>
    @endif
    @if(in_array(Auth::user()->role->name,['ketuayys','pembinayys','direktur','ctl']))
    <li class="nav-item {{ request()->routeIs('kependidikan.psb.kunci*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('kependidikan.psb.kunci*') ? 'active' : '' }}" href="{{route('kependidikan.psb.kunci.index')}}">
              <i class="mdi mdi-cog"></i>
              <span>Kunci PSB</span>
        </a>
    </li>
    @endif
<hr class="sidebar-divider">
@endif