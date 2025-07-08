<div class="sidebar-heading">
    Penilaian <span class="badge badge-primary">Guru Mapel</span>
</div>
@php
$semester = App\Models\Kbm\Semester::where('id', session('semester_aktif'))->first();
$canManageBooks = false;
$kelasK13 = 0;
if($semester){
	$kelasK13 = $semester->jadwalPelajarans()->where([
		'teacher_id' => auth()->user()->pegawai->id
	])->whereHas('level.curricula',function($q)use($semester){
		$q->where([
			'semester_id' => $semester->id
		])->whereHas('kurikulum',function($q){
			$q->where('name','K13');
		});
	})->count();
	$canManageBooks = $semester->khatamTypes()->whereHas('type',function($q){
	    $q->where('name','Buku');
	})->whereHas('level.classes.jadwal',function($q){
		$q->where([
			'teacher_id' => auth()->user()->pegawai->id
		]);
	})->count() > 0 ? true : false;
}
$skbm = App\Models\Skbm\Skbm::aktif()->where('unit_id', auth()->user()->pegawai->unit_id)->first();
$mapelskbm = $skbm ? $skbm->detail()->where('employee_id', auth()->user()->pegawai->id)->pluck('subject_id') : null;
$mapelquran = $mapelskbm ? App\Models\Kbm\MataPelajaran::where([['subject_name', 'like', "%Qur'an%"], ['unit_id', auth()->user()->pegawai->unit_id]])->whereIn('id', $mapelskbm)->count() : 0;
$mapelnotquran = $mapelskbm ? App\Models\Kbm\MataPelajaran::where([['subject_name', 'not like', "%Qur'an%"], ['unit_id', auth()->user()->pegawai->unit_id]])->whereIn('id', $mapelskbm)->count() : 0;
$mapelpai = $mapelskbm ? App\Models\Kbm\MataPelajaran::where([['subject_name', 'like', "%Agama Islam%"], ['unit_id', auth()->user()->pegawai->unit_id]])->whereIn('id', $mapelskbm)->count() : 0;
@endphp
@if($kelasK13 > 0)
<li class="nav-item {{ (request()->is('kependidikan/penilaianmapel*')) ? 'active' : '' }}">
    <a class="nav-link {{ (request()->is('kependidikan/penilaianmapel*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseRapor" aria-expanded="true" aria-controls="collapseBootstrap">
        <i class="mdi mdi-file-document"></i>
        <span>LTS/Rapor K13</span>
    </a>
    <div id="collapseRapor" class="collapse {{ (request()->is('kependidikan/penilaianmapel*')) ? 'show' : '' }}" aria-labelledby="headingRapor" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            @if(auth()->user()->pegawai->unit_id != 1 &&(($mapelskbm && count($mapelskbm) >= 1) || $mapelpai > 0 || $mapelnotquran > 0))
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/nilaipengetahuan') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/nilaipengetahuan"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Pengetahuan</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/nilaiketerampilan') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/nilaiketerampilan"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Keterampilan</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/nilaisikap') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/nilaisikap"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Sikap</a>
			@endif
			@if($mapelquran > 0)
				@if($semester->tahunAjaran->academic_year_start >= '2022')
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.khataman*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.khataman.index') }}">
				  <i class="mdi mdi-book-arrow-left"></i>
				  <span>Khataman</span>
				</a>
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.quran*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.quran.index') }}">
				  <i class="mdi mdi-account-heart"></i>
				  <span>Hafalan Quran</span>
				</a>
				@else
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/nilaitilawah') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/nilaitilawah"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Tilawah</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/nilaihafalan') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/nilaihafalan"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Hafalan</a>
				@endif
			@endif
			@if((auth()->user()->pegawai->unit_id == 1 && $mapelquran > 0) || (auth()->user()->pegawai->unit_id != 1 && $mapelpai > 0))
				@if($semester->tahunAjaran->academic_year_start >= '2022')
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.hadits-doa.index') }}">
				  <i class="mdi mdi-shield-check"></i>
				  <span>Hadits & Doa</span>
				</a>
				@else
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/nilaihadits') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/nilaihadits"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Hadits dan Doa</a>
				@endif
            @endif
            <hr class="sidebar-divider">
            @if(auth()->user()->pegawai->unit_id != 1 &&(($mapelskbm && count($mapelskbm) >= 1) || $mapelpai > 0 || $mapelnotquran > 0))
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/kdsetting') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/kdsetting"><i class="mdi mdi-cog" aria-hidden="true"></i> Jumlah NH</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/rangepredikat') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/rangepredikat"><i class="mdi mdi-cog" aria-hidden="true"></i> Range Nilai Predikat</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/predikatpengetahuan') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/predikatpengetahuan"><i class="mdi mdi-cog" aria-hidden="true"></i> Predikat Pengetahuan</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/predikatketerampilan') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/predikatketerampilan"><i class="mdi mdi-cog" aria-hidden="true"></i> Predikat Keterampilan</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/indikator/mapel') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/indikator/mapel"><i class="mdi mdi-cog" aria-hidden="true"></i> Indikator Pengetahuan</a>
            @endif
			@if($mapelquran > 0)
				@if($semester->tahunAjaran->academic_year_start >= '2022')
				@if($canManageBooks)
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.khataman.deskripsi-buku*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.khataman.deskripsi-buku.index') }}">
				  <i class="mdi mdi-cog"></i>
				  <span>Deskripsi Buku</span>
				</a>
				@endif
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.deskripsi-khataman*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.deskripsi-khataman.index') }}">
				  <i class="mdi mdi-cog"></i>
				  <span>Deskripsi Khataman</span>
				</a>
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.deskripsi-quran*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.deskripsi-quran.index') }}">
				  <i class="mdi mdi-cog"></i>
				  <span>Deskripsi Quran</span>
				</a>
				@else
				<a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/targettahfidz') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/targettahfidz"><i class="mdi mdi-cog" aria-hidden="true"></i> Target Tahfidz</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/penilaianmapel/deschafal') ? 'active' : '' }}" href="/kependidikan/penilaianmapel/deschafal"><i class="mdi mdi-cog" aria-hidden="true"></i> Deskripsi Hafalan</a>
				@endif
            @endif
			@if((auth()->user()->pegawai->unit_id == 1 && $mapelquran > 0) || (auth()->user()->pegawai->unit_id != 1 && $mapelpai > 0))
				@if($semester->tahunAjaran->academic_year_start >= '2022')
				<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.deskripsi-hadits-doa*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.deskripsi-hadits-doa.index') }}">
				  <i class="mdi mdi-cog"></i>
				  <span>Deskripsi Hadits Doa</span>
				</a>
				@endif
			@endif
        </div>
    </div>
</li>
@endif
@php
$kelasKurdeka = 0;
if($semester){
	$kelasKurdeka = $semester->jadwalPelajarans()->where([
		'teacher_id' => auth()->user()->pegawai->id
	])->whereHas('level.curricula',function($q)use($semester){
		$q->where([
			'semester_id' => $semester->id
		])->whereHas('kurikulum',function($q){
			$q->where('name','Kurdeka');
		});
	})->count();
}
@endphp
@if($kelasKurdeka > 0)
<li class="nav-item {{ request()->routeIs('kependidikan.penilaian.mapel.formatif*') || request()->routeIs('kependidikan.penilaian.mapel.sumatif*') || request()->routeIs('kependidikan.penilaian.mapel.nilai-akhir*') || request()->routeIs('kependidikan.penilaian.mapel.capaian-kompetensi*') || request()->routeIs('kependidikan.penilaian.mapel.khataman*') || request()->routeIs('kependidikan.penilaian.mapel.quran*') || request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') || request()->routeIs('kependidikan.penilaian.mapel.jumlah-tpf*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-tps*') || request()->routeIs('kependidikan.penilaian.khataman.deskripsi-buku*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-khataman*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-quran*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-hadits-doa*') ? 'active' : '' }}">
	<a class="nav-link {{ request()->routeIs('kependidikan.penilaian.mapel.formatif*') || request()->routeIs('kependidikan.penilaian.mapel.sumatif*') || request()->routeIs('kependidikan.penilaian.mapel.nilai-akhir*') || request()->routeIs('kependidikan.penilaian.mapel.capaian-kompetensi*') || request()->routeIs('kependidikan.penilaian.mapel.khataman*') || request()->routeIs('kependidikan.penilaian.mapel.quran*') || request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') || request()->routeIs('kependidikan.penilaian.mapel.jumlah-tpf*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-tps*') || request()->routeIs('kependidikan.penilaian.khataman.deskripsi-buku*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-khataman*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-quran*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-hadits-doa*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKurdeka" aria-expanded="{{ request()->routeIs('kependidikan.penilaian.mapel.formatif*') || request()->routeIs('kependidikan.penilaian.mapel.sumatif*') || request()->routeIs('kependidikan.penilaian.mapel.nilai-akhir*') || request()->routeIs('kependidikan.penilaian.mapel.capaian-kompetensi*') || request()->routeIs('kependidikan.penilaian.mapel.khataman*') || request()->routeIs('kependidikan.penilaian.mapel.quran*') || request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') || request()->routeIs('kependidikan.penilaian.mapel.jumlah-tpf*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-tps*') || request()->routeIs('kependidikan.penilaian.khataman.deskripsi-buku*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-khataman*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-quran*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-hadits-doa*') ? 'true' : 'false' }}" aria-controls="collapseKurdeka">
	  <i class="mdi mdi-file-document"></i>
      <span>LTS/Rapor Kurdeka</span>
	</a>
	<div id="collapseKurdeka" class="collapse {{ request()->routeIs('kependidikan.penilaian.mapel.formatif*') || request()->routeIs('kependidikan.penilaian.mapel.sumatif*') || request()->routeIs('kependidikan.penilaian.mapel.nilai-akhir*') || request()->routeIs('kependidikan.penilaian.mapel.capaian-kompetensi*') || request()->routeIs('kependidikan.penilaian.mapel.khataman*') || request()->routeIs('kependidikan.penilaian.mapel.quran*') || request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') || request()->routeIs('kependidikan.penilaian.mapel.jumlah-tpf*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-tps*') || request()->routeIs('kependidikan.penilaian.khataman.deskripsi-buku*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-khataman*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-quran*') || request()->routeIs('kependidikan.penilaian.mapel.deskripsi-hadits-doa*') ? 'show' : '' }}" aria-labelledby="headingKurdeka" data-parent="#accordionSidebar" style="">
	  <div class="bg-white py-2 collapse-inner rounded">
		<h6 class="collapse-header">LTS/Rapor Kurdeka</h6>
		@if($mapelskbm && count($mapelskbm) >= 1)
		@if(auth()->user()->pegawai->unit_id != 1)
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
		@endif
		@if($mapelquran > 0)
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.khataman*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.khataman.index') }}">
		  <i class="mdi mdi-book-arrow-left"></i>
		  <span>Khataman</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.quran*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.quran.index') }}">
		  <i class="mdi mdi-account-heart"></i>
		  <span>Hafalan Quran</span>
		</a>
		@endif
		@if((auth()->user()->pegawai->unit_id == 1 && $mapelquran > 0) || (auth()->user()->pegawai->unit_id != 1 && $mapelpai > 0))
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.hadits-doa*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.hadits-doa.index') }}">
		  <i class="mdi mdi-shield-check"></i>
		  <span>Hadits & Doa</span>
		</a>
		@endif
		@if($mapelskbm && count($mapelskbm) >= 1)
		@if(auth()->user()->pegawai->unit_id != 1)
		<hr class="sidebar-divider">
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.jumlah-tpf*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.jumlah-tpf.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Jumlah TP Formatif</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.deskripsi-tps*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.deskripsi-tps.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Deskripsi TP Sumatif</span>
		</a>
		@endif
		@endif
		@if($mapelquran > 0)
		@if($canManageBooks)
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.khataman.deskripsi-buku*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.khataman.deskripsi-buku.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Deskripsi Buku</span>
		</a>
		@endif
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.deskripsi-khataman*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.deskripsi-khataman.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Deskripsi Khataman</span>
		</a>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.deskripsi-quran*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.deskripsi-quran.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Deskripsi Quran</span>
		</a>
		@endif
		@if((auth()->user()->pegawai->unit_id == 1 && $mapelquran > 0) || (auth()->user()->pegawai->unit_id != 1 && $mapelpai > 0))
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.mapel.deskripsi-hadits-doa*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.mapel.deskripsi-hadits-doa.index') }}">
		  <i class="mdi mdi-cog"></i>
		  <span>Deskripsi Hadits Doa</span>
		</a>
		@endif
	  </div>
	</div>
</li>
@endif
<?php
if ($mapelskbm && count($mapelskbm) >= 1 && $mapelquran == 0) {
?>
    <li class="nav-item {{ (request()->is('kependidikan/ijazahmapel*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/ijazahmapel*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIjazah" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="mdi mdi-file-star"></i>
            <span>Ijazah</span>
        </a>
        <div id="collapseIjazah" class="collapse {{ (request()->is('kependidikan/ijazahmapel*')) ? 'show' : '' }}" aria-labelledby="headingIjazah" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ (Request::path()=='kependidikan/ijazahmapel/nilaipraktek') ? 'active' : '' }}" href="/kependidikan/ijazahmapel/nilaipraktek"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Praktek</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/ijazahmapel/nilaiusp') ? 'active' : '' }}" href="/kependidikan/ijazahmapel/nilaiusp"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai USP</a>
            </div>
        </div>
    </li>
<?php
}
if ($mapelnotquran > 0 && $mapelquran > 0) {
?>
    <li class="nav-item {{ (request()->is('kependidikan/ijazahmapel*')) ? 'active' : '' }}">
        <a class="nav-link {{ (request()->is('kependidikan/ijazahmapel*')) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseIjazah" aria-expanded="true" aria-controls="collapseBootstrap">
            <i class="mdi mdi-file-star"></i>
            <span>Ijazah</span>
        </a>
        <div id="collapseIjazah" class="collapse {{ (request()->is('kependidikan/ijazahmapel*')) ? 'show' : '' }}" aria-labelledby="headingIjazah" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ (Request::path()=='kependidikan/ijazahmapel/nilaipraktek') ? 'active' : '' }}" href="/kependidikan/ijazahmapel/nilaipraktek"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai Praktek</a>
                <a class="collapse-item {{ (Request::path()=='kependidikan/ijazahmapel/nilaiusp') ? 'active' : '' }}" href="/kependidikan/ijazahmapel/nilaiusp"><i class="mdi mdi-plus-circle" aria-hidden="true"></i> Nilai USP</a>
            </div>
        </div>
    </li>
<?php
}
?>
@if($mapelquran > 0 && $canManageBooks)
<li class="nav-item {{ request()->routeIs('kependidikan.penilaian.khataman.buku*') ? 'active' : '' }}">
	<a class="nav-link {{ request()->routeIs('kependidikan.penilaian.khataman.buku*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseKhataman" aria-expanded="{{ request()->routeIs('kependidikan.penilaian.khataman.buku*') ? 'true' : 'false' }}" aria-controls="collapseKhataman">
	  <i class="mdi mdi-book-arrow-left"></i>
      <span>Khataman</span>
	</a>
	<div id="collapseKhataman" class="collapse {{ request()->routeIs('kependidikan.penilaian.khataman.buku*') ? 'show' : '' }}" aria-labelledby="headingKhataman" data-parent="#accordionSidebar" style="">
	  <div class="bg-white py-2 collapse-inner rounded">
		<h6 class="collapse-header">Khataman</h6>
		<a class="collapse-item {{ request()->routeIs('kependidikan.penilaian.khataman.buku*') ? 'active' : '' }}" href="{{ route('kependidikan.penilaian.khataman.buku.index') }}">
		  <i class="mdi mdi-book-multiple"></i>
		  <span>Buku</span>
		</a>
	  </div>
	</div>
</li>
@endif
<hr class="sidebar-divider">