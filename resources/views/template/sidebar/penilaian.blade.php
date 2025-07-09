<div class="sidebar-heading">
    Penilaian <span class="badge badge-primary">Wali Kelas</span>
</div>
@php
$iswali = App\Models\Kbm\Kelas::where('teacher_id', auth()->user()->pegawai->id)->first();
$semester = App\Models\Kbm\Semester::where('id', session('semester_aktif'))->first();
$kelasK13 = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
	$q->aktif();
})->whereHas('level.curricula',function($q)use($semester){
	$q->where([
        'semester_id' => $semester->id
	])->whereHas('kurikulum',function($q){
		$q->where('name','K13');
	});
})->first();
@endphp
@if($kelasK13)
<li class="nav-item {{ (request()->is('kependidikan/penilaian*')) && !request()->routeIs('kependidikan.penilaian.tk.nilai-elemen*') && !request()->routeIs('kependidikan.penilaian.tk.perkembangan*') && !request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') && !request()->routeIs('kependidikan.penilaian.lts.cetak*') && !request()->routeIs('kependidikan.penilaian.rapor.cetak*') && !request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') && !request()->routeIs('kependidikan.penilaian.tk.tujuan-elemen*') && !request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') && !request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') && !!request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'active' : '' }}">
    <a class="nav-link {{ (request()->is('kependidikan/penilaian*')) && !request()->routeIs('kependidikan.penilaian.tk.nilai-elemen*') && !request()->routeIs('kependidikan.penilaian.tk.perkembangan*') && !request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') && !request()->routeIs('kependidikan.penilaian.lts.cetak*') && !request()->routeIs('kependidikan.penilaian.rapor.cetak*') && !request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') && !request()->routeIs('kependidikan.penilaian.tk.tujuan-elemen*') && !request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') && !request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') && !!request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseRapor" aria-expanded="true" aria-controls="collapseBootstrap">
        <i class="mdi mdi-file-document"></i>
        <span>LTS/Rapor K13</span>
    </a>
    <div id="collapseRapor" class="collapse {{ (request()->is('kependidikan/penilaian*')) && !request()->routeIs('kependidikan.penilaian.tk.nilai-elemen*') && !request()->routeIs('kependidikan.penilaian.tk.perkembangan*') && !request()->routeIs('kependidikan.penilaian.tk.perkembangan*') && !request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') && !request()->routeIs('kependidikan.penilaian.lts.cetak*') && !request()->routeIs('kependidikan.penilaian.rapor.cetak*') && !request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') && !request()->routeIs('kependidikan.penilaian.tk.tujuan-elemen*') && !request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') && !request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') && !!request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'show' : '' }}" aria-labelledby="headingRapor" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <?php if ($kelasK13->unit_id == 1) { ?>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiantk/nilaiaspek') ? 'active' : '' }}" href="/kependidikan/penilaiantk/nilaiaspek"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Aspek</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiantk/nilaiindikator') ? 'active' : '' }}" href="/kependidikan/penilaiantk/nilaiindikator"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Indikator</a>
				@if($semester->tahunAjaran->academic_year_start >= '2022')
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.nilai-iklas.index') }}">
				  <i class="mdi mdi-star"></i>
				  <span>Nilai IKLaS</span>
				</a>
				@endif
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/kehadiran') ? 'active' : '' }}" href="/kependidikan/penilaian/kehadiran"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Kehadiran</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/ekstra') ? 'active' : '' }}" href="/kependidikan/penilaian/ekstra"><i class="mdi mdi-plus-circle"></i> Ekstrakurikuler</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/catatanwali') ? 'active' : '' }}" href="/kependidikan/penilaian/catatanwali"><i class="mdi mdi-plus-circle"></i> Catatan</a>
                <?php
                if ($semester->semester == "Genap") {
                ?>
                    <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/kenaikankelas') ? 'active' : '' }}" href="/kependidikan/penilaian/kenaikankelas"><i class="mdi mdi-plus-circle"></i> Kenaikan Kelas</a>
                <?php } ?>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/cetakpts') ? 'active' : '' }}" href="/kependidikan/penilaian/cetakpts"><i class="mdi mdi-printer"></i> Laporan TS</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/cetakpas') ? 'active' : '' }}" href="/kependidikan/penilaian/cetakpas"><i class="mdi mdi-printer"></i> Rapor</a>
                <hr class="sidebar-divider">
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiantk/descaspek') ? 'active' : '' }}" href="/kependidikan/penilaiantk/descaspek"><i class="mdi mdi-cog" aria-hidden="true"></i> Deskripsi Aspek</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiantk/indikator') ? 'active' : '' }}" href="/kependidikan/penilaiantk/indikator"><i class="mdi mdi-cog" aria-hidden="true"></i> Indikator Aspek</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaiantk/descindikator') ? 'active' : '' }}" href="/kependidikan/penilaiantk/descindikator"><i class="mdi mdi-cog" aria-hidden="true"></i> Deskripsi Indikator</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/deskripsiekstra') ? 'active' : '' }}" href="/kependidikan/penilaian/deskripsiekstra"><i class="mdi mdi-cog" aria-hidden="true"></i> Deskripsi Ekstra</a>
				@if($semester->tahunAjaran->academic_year_start >= '2022')
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.deskripsi.index') }}">
				  <i class="mdi mdi-cog"></i>
				  <span>Deskripsi IKLaS</span>
				</a>
				@endif
            <?php } else { ?>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/nilaisikap') ? 'active' : '' }}" href="/kependidikan/penilaian/nilaisikap"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Sikap</a>
				@if($semester->tahunAjaran->academic_year_start >= '2022')
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.nilai-iklas.index') }}">
				  <i class="mdi mdi-star"></i>
				  <span>Nilai IKLaS</span>
				</a>
				@else
				<a class="collapse-item {{ request()->routeIs('iklas*') ? 'active' : '' }}" href="/kependidikan/penilaian/iklas"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai IKLaS</a>
				@endif
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/kehadiran') ? 'active' : '' }}" href="/kependidikan/penilaian/kehadiran"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Kehadiran</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/ekstra') ? 'active' : '' }}" href="/kependidikan/penilaian/ekstra"><i class="mdi mdi-plus-circle"></i> Ekstrakurikuler</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/prestasi') ? 'active' : '' }}" href="/kependidikan/penilaian/prestasi"><i class="mdi mdi-plus-circle"></i> Prestasi</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/catatanwali') ? 'active' : '' }}" href="/kependidikan/penilaian/catatanwali"><i class="mdi mdi-plus-circle"></i> Catatan</a>
                <?php
                if ($semester->semester == "Genap") {
                ?>
                    <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/kenaikankelas') ? 'active' : '' }}" href="/kependidikan/penilaian/kenaikankelas"><i class="mdi mdi-plus-circle"></i> Kenaikan Kelas</a>
                <?php } ?>

                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/deskripsipts') ? 'active' : '' }}" href="/kependidikan/penilaian/deskripsipts"><i class="mdi mdi-plus-circle"></i> Deskripsi Laporan TS</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/cetakpts') ? 'active' : '' }}" href="/kependidikan/penilaian/cetakpts"><i class="mdi mdi-printer"></i> Laporan TS</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/cetakpas') ? 'active' : '' }}" href="/kependidikan/penilaian/cetakpas"><i class="mdi mdi-printer"></i> Rapor</a>
                <hr class="sidebar-divider">
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/predikatsikap') ? 'active' : '' }}" href="/kependidikan/penilaian/predikatsikap"><i class="mdi mdi-cog" aria-hidden="true"></i> Predikat Nilai Sikap</a>
                <a class="collapse-item {{ request()->routeIs('predikat.iklas*') ? 'active' : '' }}" href="{{ route('predikat.iklas.index') }}"><i class="mdi mdi-cog" aria-hidden="true"></i> Predikat Nilai IKLaS</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/deskripsiekstra') ? 'active' : '' }}" href="/kependidikan/penilaian/deskripsiekstra"><i class="mdi mdi-cog" aria-hidden="true"></i> Deskripsi Ekstra</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/descpts') ? 'active' : '' }}" href="/kependidikan/penilaian/descpts"><i class="mdi mdi-cog" aria-hidden="true"></i> Deskripsi Laporan TS</a>
				<?php if($semester->tahunAjaran->academic_year_start < '2022'){ ?>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/indikatoriklas') ? 'active' : '' }}" href="/kependidikan/penilaian/indikatoriklas"><i class="mdi mdi-cog" aria-hidden="true"></i> Indikator IKLaS</a>				
				<?php } ?>
            <?php } ?>
				@if($semester->tahunAjaran->academic_year_start >= '2022')
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.deskripsi.index') }}">
				  <i class="mdi mdi-cog"></i>
				  <span>Deskripsi IKLaS</span>
				</a>
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.indikator-kurikulum.index') }}">
				  <i class="mdi mdi-cog"></i>
				  <span>Indikator IKLaS</span>
				</a>
				@endif
        </div>
    </div>
</li>
@endif
@php
$kelasKurdeka = auth()->user()->pegawai->kelas()->whereHas('tahunAjaran',function($q){
	$q->aktif();
})->whereHas('level.curricula',function($q)use($semester){
	$q->where([
        'semester_id' => $semester->id
	])->whereHas('kurikulum',function($q){
		$q->where('name','Kurdeka');
	});
})->first();
@endphp
@if($kelasKurdeka)
<li class="nav-item {{ (request()->is('kependidikan/penilaian*')) && request()->routeIs('kependidikan.penilaian.tk.nilai-elemen*') || request()->routeIs('kependidikan.penilaian.tk.perkembangan*') || request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') || request()->routeIs('kependidikan.penilaian.lts.cetak*') || request()->routeIs('kependidikan.penilaian.rapor.cetak*') || request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-elemen*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') || request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') || request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'active' : '' }}">
	<a class="nav-link {{ (request()->is('kependidikan/penilaian*')) && request()->routeIs('kependidikan.penilaian.tk.nilai-elemen*') || request()->routeIs('kependidikan.penilaian.tk.perkembangan*') || request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') || request()->routeIs('kependidikan.penilaian.lts.cetak*') || request()->routeIs('kependidikan.penilaian.rapor.cetak*') || request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-elemen*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') || request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') || request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKurdeka" aria-expanded="{{ (request()->is('kependidikan/penilaian*')) && request()->routeIs('kependidikan.penilaian.iklas.nilai-iklasf*') || request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-elemen*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') || request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') || request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'true' : 'false' }}" aria-controls="collapseKurdeka">
	  <i class="mdi mdi-file-document"></i>
      <span>LTS/Rapor Kurdeka</span>
	</a>
	<div id="collapseKurdeka" class="collapse {{ (request()->is('kependidikan/penilaian*')) && request()->routeIs('kependidikan.penilaian.tk.nilai-elemen*') || request()->routeIs('kependidikan.penilaian.tk.perkembangan*') || request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') || request()->routeIs('kependidikan.penilaian.lts.cetak*') || request()->routeIs('kependidikan.penilaian.rapor.cetak*') || request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-elemen*') || request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') || request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') || request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'show' : '' }}" aria-labelledby="headingKurdeka" data-parent="#accordionSidebar" style="">
	  <div class="bg-white py-2 collapse-inner rounded">
		<h6 class="collapse-header">LTS/Rapor Kurdeka</h6>
		@if($kelasKurdeka->unit_id == 1)
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.tk.nilai-elemen*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.tk.nilai-elemen.index') }}">
		  <i class="mdi mdi-pinwheel"></i>
		  <span>Nilai Elemen</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.tk.perkembangan*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.tk.perkembangan.index') }}">
		  <i class="mdi mdi-file-check"></i>
		  <span>Perkembangan</span>
		</a>
		@endif
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.nilai-iklas*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.nilai-iklas.index') }}">
		  <i class="mdi mdi-star"></i>
		  <span>Nilai IKLaS</span>
		</a>
		<!-- old -->
		<a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/ekstra') ? 'active' : '' }}" href="/kependidikan/penilaian/ekstra"><i class="mdi mdi-plus-circle"></i> Ekstrakurikuler</a>
		<a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/prestasi') ? 'active' : '' }}" href="/kependidikan/penilaian/prestasi"><i class="mdi mdi-plus-circle"></i> Juara</a>
		<a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/kehadiran') ? 'active' : '' }}" href="/kependidikan/penilaian/kehadiran"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Kehadiran</a>
		<a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/catatanwali') ? 'active' : '' }}" href="/kependidikan/penilaian/catatanwali"><i class="mdi mdi-plus-circle"></i> Refleksi</a>
		@if($semester->semester == "Genap")
        <a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/kenaikankelas') ? 'active' : '' }}" href="/kependidikan/penilaian/kenaikankelas"><i class="mdi mdi-plus-circle"></i> Kenaikan Kelas</a>
		@endif
		<!-- end of old -->
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.lts.cetak*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.lts.cetak.index') }}">
		  <i class="mdi mdi-printer"></i>
		  <span>LTS</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.rapor.cetak*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.rapor.cetak.index') }}">
		  <i class="mdi mdi-printer"></i>
		  <span>Rapor</span>
		</a>
		<hr class="sidebar-divider">
		@if($kelasKurdeka->unit_id == 1)
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.tk.deskripsi-perkembangan*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.tk.deskripsi-perkembangan.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Des. Perkembangan</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.tk.tujuan-elemen*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.tk.tujuan-elemen.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Tujuan Elemen</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.tk.tujuan-pembelajaran*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.tk.tujuan-pembelajaran.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Tujuan Pembelajaran</span>
		</a>
		@endif
		<!-- old -->
		<a class="collapse-item {{ (Request::path()=='kependidikan/penilaian/deskripsiekstra') ? 'active' : '' }}" href="/kependidikan/penilaian/deskripsiekstra"><i class="mdi mdi-cog" aria-hidden="true"></i> Deskripsi Ekstra</a>
		<!-- end of old -->
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.deskripsi*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.deskripsi.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Deskripsi IKLaS</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.indikator-kurikulum.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Indikator IKLaS</span>
		</a>
	  </div>
	</div>
</li>
@endif
<?php
if ($iswali->level_id == 8 || $iswali->level_id == 11 || $iswali->level_id == 14) {
?>
    <li class="nav-item {{ (request()->is('kependidikan/ijazah*') || request()->is('kependidikan/skhb*') || request()->is('kependidikan/refijazah*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/ijazah*') || request()->is('kependidikan/skhb*') || request()->is('kependidikan/refijazah*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIjazah" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="mdi mdi-file-star"></i>
            <span>Ijazah</span>
        </a>
        <div id="collapseIjazah" class="collapse {{ (request()->is('kependidikan/ijazah*') || request()->is('kependidikan/skhb*') || request()->is('kependidikan/refijazah*')) ? 'show' : '' }}" aria-labelledby="headingIjazah" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ (Request::path()=='kependidikan/refijazah') ? 'active' : '' }}" href="/kependidikan/refijazah"><i class="mdi mdi-printer" aria-hidden="true"></i> Referensi Ijazah</a>
                <hr class="sidebar-divider">
                @if($iswali->level_id == 8)
                <a class="collapse-item {{ (Request::path()=='kependidikan/skhb/arsip') ? 'active' : '' }}" href="/kependidikan/skhb/arsip"><i class="mdi mdi-cog" aria-hidden="true"></i> Arsip SKHB</a>
                @endif
                <a class="collapse-item {{ (Request::path()=='kependidikan/ijazah/arsip') ? 'active' : '' }}" href="/kependidikan/ijazah/arsip"><i class="mdi mdi-cog" aria-hidden="true"></i> Arsip Ijazah</a>
            </div>
        </div>
    </li>
    <li class="nav-item {{ (Request::path()=='kependidikan/sertifiklas/cetak') ? 'active' : '' }}">
        <a class="nav-link" href="/kependidikan/sertifiklas/cetak">
          <i class="mdi mdi-file-certificate-outline"></i>
          <span>Sertifikat IKLaS</span>
        </a>
    </li>
    <!-- <li class="nav-item {{ (request()->is('kependidikan/sertifiklas*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/sertifiklas*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSertif" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="mdi mdi-file-certificate-outline"></i>
            <span>Sertifikat IKLaS</span>
        </a>
        <div id="collapseSertif" class="collapse {{ (request()->is('kependidikan/sertifiklas*')) ? 'show' : '' }}" aria-labelledby="headingSertif" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ (Request::path()=='kependidikan/sertifiklas/nilai') ? 'active' : '' }}" href="/kependidikan/sertifiklas/nilai"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai IKLaS</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/sertifiklas/cetak') ? 'active' : '' }}" href="/kependidikan/sertifiklas/cetak"><i class="mdi mdi-printer" aria-hidden="true"></i> Sertifikat IKLaS</a>
            </div>
        </div>
    </li> -->
<?php } ?>
	@if($iswali && in_array($iswali->unit->name,['SD','SMP','SMA']))
    <li class="nav-item {{ request()->routeIs('penilaian.ikuEdukasi*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('penilaian.ikuEdukasi.kelas') }}">
          <i class="mdi mdi-trophy"></i>
          <span>Ledger Kelas</span>
        </a>
    </li>
	@endif
<li class="nav-item {{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') || request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') || (request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*')) ? 'active' : '' }}">
	<a class="nav-link {{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') || request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') || (request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIklas" aria-expanded="{{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') || request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') || (request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*')) ? 'true' : 'false' }}" aria-controls="collapseIklas">
	  <i class="mdi mdi-file-document"></i>
      <span>IKLaS</span>
	</a>
	<div id="collapseIklas" class="collapse {{ request()->routeIs('kependidikan.penilaian.iklas.kurikulum*') || request()->routeIs('kependidikan.penilaian.iklas.kompetensi*') || (request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*')) ? 'show' : '' }}" aria-labelledby="headingIklas" data-parent="#accordionSidebar" style="">
	  <div class="bg-white py-2 collapse-inner rounded">
		<h6 class="collapse-header">IKLaS</h6>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.iklas.indikator*') && !request()->routeIs('kependidikan.penilaian.iklas.indikator-kurikulum*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.iklas.indikator.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Indikator</span>
		</a>
	  </div>
	</div>
</li>
<hr class="sidebar-divider">