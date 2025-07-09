<<<<<<< HEAD
@if(auth()->user()->role->name == "kepsek")
&nbsp;
@elseif (auth()->user()->role->name == "guru")
<?php
$iswali = App\Models\Kbm\Kelas::where('teacher_id', auth()->user()->pegawai->id)->count();
?>
@if($iswali > 0)
@if(auth()->user()->pegawai->unit_id != 1)
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-users-cog fa-fw"></i>
    </a>
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow change-system animated--grow-in" aria-labelledby="messagesDropdown">
        <h6 class="dropdown-header">
            Pindah Hak Akses
        </h6>
        <a class="dropdown-item d-flex align-items-center" href="{{ url('/kependidikan') }}">
            <div class="mr-3">
                <i class="fas fa-book-reader text-primary"></i>
            </div>
            <div>
                <span class="font-weight-bold">Wali Kelas</span>
            </div>
        </a>
        <a class="dropdown-item d-flex align-items-center" href="{{ url('/kependidikan/dashboardmapel') }}">
            <div class="mr-3">
                <i class="fas fa-book-open text-warning"></i>
            </div>
            <div>
                <span class="font-weight-bold">Guru Mapel</span>
            </div>
        </a>
    </div>
</li>
@endif
@endif
=======
@if(auth()->user()->role->name == "kepsek")
&nbsp;
@elseif (auth()->user()->role->name == "guru")
<?php
$iswali = App\Models\Kbm\Kelas::where('teacher_id', auth()->user()->pegawai->id)->count();
?>
@if($iswali > 0)
@if(auth()->user()->pegawai->unit_id != 1)
<li class="nav-item dropdown no-arrow mx-1">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-users-cog fa-fw"></i>
    </a>
    <div class="dropdown-list dropdown-menu dropdown-menu-right shadow change-system animated--grow-in" aria-labelledby="messagesDropdown">
        <h6 class="dropdown-header">
            Pindah Hak Akses
        </h6>
        <a class="dropdown-item d-flex align-items-center" href="{{ url('/kependidikan') }}">
            <div class="mr-3">
                <i class="fas fa-book-reader text-primary"></i>
            </div>
            <div>
                <span class="font-weight-bold">Wali Kelas</span>
            </div>
        </a>
        <a class="dropdown-item d-flex align-items-center" href="{{ url('/kependidikan/dashboardmapel') }}">
            <div class="mr-3">
                <i class="fas fa-book-open text-warning"></i>
            </div>
            <div>
                <span class="font-weight-bold">Guru Mapel</span>
            </div>
        </a>
    </div>
</li>
@endif
@endif
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endif