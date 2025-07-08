<div class="sidebar-heading">
    Penilaian
</div>
<li class="nav-item {{ (request()->is('kependidikan/penilaiankepsek*')) || request()->routeIs('mapel.keterampilan*') || request()->routeIs('penilaian.sikap*') || request()->routeIs('penilaian.tilawah*')  || request()->routeIs('penilaian.hafalan*')? 'active' : '' }}">
    <a class="nav-link {{ (request()->is('kependidikan/penilaiankepsek*')) || request()->routeIs('mapel.keterampilan*') || request()->routeIs('penilaian.sikap*') || request()->routeIs('penilaian.tilawah*') || request()->routeIs('penilaian.hafalan*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseRapor" aria-expanded="true" aria-controls="collapseBootstrap">
        <i class="mdi mdi-file-document"></i>
        <span>LTS/Rapor K13</span>
    </a>
    <div id="collapseRapor" class="collapse {{ (request()->is('kependidikan/penilaiankepsek*')) || request()->routeIs('mapel.pengetahuan*') ||  request()->routeIs('mapel.keterampilan*') || request()->routeIs('penilaian.sikap*') || request()->routeIs('penilaian.tilawah*') || request()->routeIs('penilaian.hafalan*') ? 'show' : '' }}" aria-labelledby="headingRapor" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            @if(auth()->user()->pegawai->unit_id != 1 && auth()->user()->role->name != 'wakasek')
            <a class="collapse-item {{ request()->routeIs('mapel.pengetahuan*') ? 'active' : '' }}" href="{{ route('mapel.pengetahuan.index') }}"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Pengetahuan</a>
			<a class="collapse-item {{ request()->routeIs('mapel.keterampilan*') ? 'active' : '' }}" href="{{ route('mapel.keterampilan.index') }}"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Keterampilan</a>
			<a class="collapse-item {{ request()->routeIs('penilaian.sikap*') ? 'active' : '' }}" href="{{ route('penilaian.sikap.index') }}"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Sikap</a>
            <a class="collapse-item {{ request()->routeIs('penilaian.tilawah*') ? 'active' : '' }}" href="{{ route('penilaian.tilawah.index') }}"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Tilawah</a>
			<a class="collapse-item {{ request()->routeIs('penilaian.hafalan*') ? 'active' : '' }}" href="{{ route('penilaian.hafalan.index') }}"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Hafalan</a>
			@endif
            <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiankepsek/pts') ? 'active' : '' }}" href="/kependidikan/penilaiankepsek/pts"><i class="mdi mdi-checkbox-marked-circle" aria-hidden="true"></i> Laporan TS</a>
            <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiankepsek/pas') ? 'active' : '' }}" href="/kependidikan/penilaiankepsek/pas"><i class="mdi mdi-checkbox-marked-circle" aria-hidden="true"></i> Rapor</a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiankepsek/tanggalrapor') ? 'active' : '' }}" href="/kependidikan/penilaiankepsek/tanggalrapor"><i class="mdi mdi-cog" aria-hidden="true"></i> Tanggal Rapor</a>
            @if(auth()->user()->pegawai->unit_id != 1)
            <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiankepsek/rangepredikat') ? 'active' : '' }}" href="/kependidikan/penilaiankepsek/rangepredikat"><i class="mdi mdi-cog" aria-hidden="true"></i> Range Nilai Predikat</a>
            @if(auth()->user()->role->name != 'wakasek')
            <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiankepsek/passwordverifikasi') ? 'active' : '' }}" href="/kependidikan/penilaiankepsek/passwordverifikasi"><i class="mdi mdi-cog" aria-hidden="true"></i> Password Verifikasi</a>
            @endif
            @endif
            @if(auth()->user()->pegawai->unit_id == 1)
            <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiankepsek/tk/aspekperkembangan') ? 'active' : '' }}" href="/kependidikan/penilaiankepsek/tk/aspekperkembangan"><i class="mdi mdi-cog" aria-hidden="true"></i> Aspek Perkembangan</a>
            @endif
        </div>
    </div>
</li>
<li class="nav-item {{ request()->routeIs('kependidikan.penilaian.mapel.formatif*') || request()->routeIs('kependidikan.penilaian.mapel.sumatif*') || request()->routeIs('kependidikan.penilaian.mapel.nilai-akhir*') || request()->routeIs('kependidikan.penilaian.mapel.capaian-kompetensi*') || request()->routeIs('kependidikan.penilaian.mapel.khataman*') || request()->routeIs('kependidikan.penilaian.mapel.quran*') || request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') || request()->routeIs('kependidikan.penilaian.lts.cetak*') || request()->routeIs('kependidikan.penilaian.rapor.cetak*') || request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') || request()->routeIs('kependidikan.penilaian.tk.elemen-capaian*') ? 'active' : '' }}">
	<a class="nav-link {{ request()->routeIs('kependidikan.penilaian.mapel.formatif*') || request()->routeIs('kependidikan.penilaian.mapel.sumatif*') || request()->routeIs('kependidikan.penilaian.mapel.nilai-akhir*') || request()->routeIs('kependidikan.penilaian.mapel.capaian-kompetensi*') || request()->routeIs('kependidikan.penilaian.mapel.khataman*') || request()->routeIs('kependidikan.penilaian.mapel.quran*') || request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') || request()->routeIs('kependidikan.penilaian.lts.cetak*') || request()->routeIs('kependidikan.penilaian.rapor.cetak*') || request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') || request()->routeIs('kependidikan.penilaian.tk.elemen-capaian*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKurdeka" aria-expanded="{{ request()->routeIs('kependidikan.penilaian.iklas.nilai-iklasf*') || request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') ? 'true' : 'false' }}" aria-controls="collapseKurdeka">
	  <i class="mdi mdi-file-document"></i>
      <span>LTS/Rapor Kurdeka</span>
	</a>
	<div id="collapseKurdeka" class="collapse {{ request()->routeIs('kependidikan.penilaian.mapel.formatif*') || request()->routeIs('kependidikan.penilaian.mapel.sumatif*') || request()->routeIs('kependidikan.penilaian.mapel.nilai-akhir*') || request()->routeIs('kependidikan.penilaian.mapel.capaian-kompetensi*') || request()->routeIs('kependidikan.penilaian.mapel.khataman*') || request()->routeIs('kependidikan.penilaian.mapel.quran*') || request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') || request()->routeIs('kependidikan.penilaian.lts.cetak*') || request()->routeIs('kependidikan.penilaian.rapor.cetak*') || request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') || request()->routeIs('kependidikan.penilaian.tk.elemen-capaian*') ? 'show' : '' }}" aria-labelledby="headingKurdeka" data-parent="#accordionSidebar" style="">
	  <div class="bg-white py-2 collapse-inner rounded">
		<h6 class="collapse-header">LTS/Rapor Kurdeka</h6>
		@if(auth()->user()->pegawai->unit_id == 1)
		@else
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.formatif*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.formatif.index') }}">
		  <i class="mdi mdi-lightbulb-on"></i>
		  <span>Nilai Formatif</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.sumatif*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.sumatif.index') }}">
		  <i class="mdi mdi-checkbox-marked-circle"></i>
		  <span>Nilai Sumatif</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.nilai-akhir*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.nilai-akhir.index') }}">
		  <i class="mdi mdi-file-percent"></i>
		  <span>Nilai Akhir Rapor</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.capaian-kompetensi*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.capaian-kompetensi.index') }}">
		  <i class="mdi mdi-file-check"></i>
		  <span>Capaian Kompetensi</span>
		</a>
		@endif
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.khataman*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.khataman.index') }}">
		  <i class="mdi mdi-book-arrow-left"></i>
		  <span>Khataman</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.quran*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.quran.index') }}">
		  <i class="mdi mdi-account-heart"></i>
		  <span>Hafalan Quran</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.hadits-doa.index') }}">
		  <i class="mdi mdi-shield-check"></i>
		  <span>Hadits & Doa</span>
		</a>
        <a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.lts.cetak*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.lts.cetak.index') }}">
          <i class="mdi mdi-printer"></i>
          <span>LTS</span>
        </a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.rapor.cetak*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.rapor.cetak.index') }}">
		  <i class="mdi mdi-printer"></i>
		  <span>Rapor</span>
		</a>
		<hr class="sidebar-divider">
		<a class="collapse-item {{ (Request::path()=='kependidikan/penilaiankepsek/tanggalrapor') ? 'active' : '' }}" href="/kependidikan/penilaiankepsek/tanggalrapor"><i class="mdi mdi-cog" aria-hidden="true"></i> Tanggal Rapor</a>
		@if(auth()->user()->pegawai->unit_id == 1)
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.tk.deskripsi-perkembangan.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Des. Perkembangan</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.tk.tujuan-pembelajaran.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Tujuan Pembelajaran</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.tk.elemen-capaian*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.tk.elemen-capaian.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Elemen Capaian</span>
		</a>
		@endif
	  </div>
	</div>
</li>

@if(auth()->user()->pegawai->unit_id != 1)
<li class="nav-item {{ (request()->is('kependidikan/ijazahkepsek*')) ? 'active' : '' }}">
    <a class="nav-link {{ (request()->is('kependidikan/ijazakepsek*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIjazah" aria-expanded="true" aria-controls="collapseBootstrap">
        <i class="mdi mdi-file-star"></i>
        <span>Ijazah</span>
    </a>
    <div id="collapseIjazah" class="collapse {{ (request()->is('kependidikan/ijazahkepsek*')) ? 'show' : '' }}" aria-labelledby="headingIjazah" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item {{ (Request::path()=='kependidikan/ijazahkepsek/refijazah') ? 'active' : '' }}" href="/kependidikan/ijazahkepsek/refijazah"><i class="mdi mdi-checkbox-marked-circle" aria-hidden="true"></i> Referensi Ijazah</a>
        </div>
    </div>
</li>

<!--<li class="nav-item {{ (Request::path()=='kependidikan/sertifiklaskepsek') ? 'active' : '' }}">-->
<!--    <a class="nav-link" href="/kependidikan/sertifiklaskepsek">-->
<!--      <i class="mdi mdi-file-certificate-outline"></i>-->
<!--      <span>Sertifikat IKLaS</span>-->
<!--  </a>-->
<!--</li>-->

<li class="nav-item {{ request()->routeIs('penilaian.ikuEdukasi.persen*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('penilaian.ikuEdukasi.persen') }}">
      <i class="mdi mdi-file-percent"></i>
      <span>IKU Edukasi</span>
  </a>
</li>

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
@endif

<!--<li class="nav-item {{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') || request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') || (request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*')) ? 'active' : '' }}">-->
<!--	<a class="nav-link {{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') || request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') || (request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIklas" aria-expanded="{{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') || request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') || (request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*')) ? 'true' : 'false' }}" aria-controls="collapseIklas">-->
<!--	  <i class="mdi mdi-file-document"></i>-->
<!--      <span>IKLaS</span>-->
<!--	</a>-->
<!--	<div id="collapseIklas" class="collapse {{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') || request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') || (request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*')) ? 'show' : '' }}" aria-labelledby="headingIklas" data-parent="#accordionSidebar" style="">-->
<!--	  <div class="bg-white py-2 collapse-inner rounded">-->
<!--		<h6 class="collapse-header">IKLaS</h6>-->
<!--		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.kurikulum.index') }}">-->
<!--		  <i class="mdi mdi-file"></i>-->
<!--		  <span>Kurikulum IKLaS</span>-->
<!--		</a>-->
<!--		<hr class="sidebar-divider">-->
<!--		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.kompetensi.index') }}">-->
<!--		  <i class="mdi mdi-cog"></i>-->
<!--		  <span>Kompetensi</span>-->
<!--		</a>-->
<!--		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.indikator.index') }}">-->
<!--		  <i class="mdi mdi-cog"></i>-->
<!--		  <span>Indikator</span>-->
<!--		</a>-->
<!--	  </div>-->
<!--	</div>-->
<!--</li>-->
<!--<li class="nav-item {{ request()->routeIs('kependidikan.penilaian.khataman.capaian*') || request()->routeIs('kependidikan.penilaian.khataman.buku*') ? 'active' : '' }}">-->
<!--	<a class="nav-link {{ request()->routeIs('kependidikan.penilaian.khataman.capaian*') || request()->routeIs('kependidikan.penilaian.khataman.buku*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKhataman" aria-expanded="{{ request()->routeIs('kependidikan.penilaian.khataman.capaian*') || request()->routeIs('kependidikan.penilaian.khataman.buku*') ? 'true' : 'false' }}" aria-controls="collapseKhataman">-->
<!--	  <i class="mdi mdi-book-arrow-left"></i>-->
<!--      <span>Khataman</span>-->
<!--	</a>-->
<!--	<div id="collapseKhataman" class="collapse {{ request()->routeIs('kependidikan.penilaian.khataman.capaian*') || request()->routeIs('kependidikan.penilaian.khataman.buku*') ? 'show' : '' }}" aria-labelledby="headingKhataman" data-parent="#accordionSidebar" style="">-->
<!--	  <div class="bg-white py-2 collapse-inner rounded">-->
<!--		<h6 class="collapse-header">Khataman</h6>-->
<!--		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.khataman.capaian*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.khataman.capaian.index') }}">-->
<!--		  <i class="mdi mdi-book-cog"></i>-->
<!--		  <span>Capaian Khatam</span>-->
<!--		</a>-->
<!--		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.khataman.buku*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.khataman.buku.index') }}">-->
<!--		  <i class="mdi mdi-book-multiple"></i>-->
<!--		  <span>Buku</span>-->
<!--		</a>-->
<!--	  </div>-->
<!--	</div>-->
<!--</li>-->

<li class="nav-item {{ request()->routeIs('kependidikan.penilaian.kurikulum*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('kependidikan.penilaian.kurikulum.index') }}">
      <i class="mdi mdi-book-settings"></i>
      <span>Kurikulum</span>
  </a>
</li>
<hr class="sidebar-divider">