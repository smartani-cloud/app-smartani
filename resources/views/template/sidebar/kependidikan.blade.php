<<<<<<< HEAD
@extends('template.main.sidebar')

@section('brand-system')
Kependidikan @endsection

@section('sidebar-menu')
<li class="nav-item {{ (Request::path()=='kependidikan') ? 'active' : '' }}">
    <a class="nav-link" href="/kependidikan">
        <i class="mdi mdi-view-dashboard"></i>
        <span>Beranda</span>
    </a>
</li>
<hr class="sidebar-divider">
@include('template.sidebar.psb')
@include('template.sidebar.kbm')
<?php if (auth()->user()->role->name == 'kepsek' || auth()->user()->role->name == 'wakasek') { ?>
    @include('template.sidebar.penilaiankepsek')
<?php } elseif (auth()->user()->role->name == 'guru') { ?>
    <?php
    $iswali = App\Models\Kbm\Kelas::where('teacher_id', auth()->user()->pegawai->id)->count();
    $sidebarWali = auth()->user()->pegawai->position_id == '5' ? true : false;
    ?>
    @if($sidebarWali)
    @if($iswali > 0)
    @include('template.sidebar.penilaian')
    @else
    @include('template.sidebar.penilaianmapel')
    @endif
    @else
    @include('template.sidebar.penilaianmapel')
    @endif
<?php } elseif(in_array(Auth::user()->role->name, ['pembinayys','ketuayys','direktur','etl','etm'])){ ?>
    @include('template.sidebar.penilaianmanajemen')
<?php } ?>

=======
@extends('template.main.sidebar')

@section('brand-system')
Kependidikan @endsection

@section('sidebar-menu')
<li class="nav-item {{ (Request::path()=='kependidikan') ? 'active' : '' }}">
    <a class="nav-link" href="/kependidikan">
        <i class="mdi mdi-view-dashboard"></i>
        <span>Beranda</span>
    </a>
</li>
<hr class="sidebar-divider">
@include('template.sidebar.psb')
@include('template.sidebar.kbm')
<?php if (auth()->user()->role->name == 'kepsek' || auth()->user()->role->name == 'wakasek') { ?>
    @include('template.sidebar.penilaiankepsek')
<?php } elseif (auth()->user()->role->name == 'guru') { ?>
    <?php
    $iswali = App\Models\Kbm\Kelas::where('teacher_id', auth()->user()->pegawai->id)->count();
    $sidebarWali = auth()->user()->pegawai->position_id == '5' ? true : false;
    ?>
    @if($sidebarWali)
    @if($iswali > 0)
    @include('template.sidebar.penilaian')
    @else
    @include('template.sidebar.penilaianmapel')
    @endif
    @else
    @include('template.sidebar.penilaianmapel')
    @endif
<?php } elseif(in_array(Auth::user()->role->name, ['pembinayys','ketuayys','direktur','etl','etm'])){ ?>
    @include('template.sidebar.penilaianmanajemen')
<?php } ?>

>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection