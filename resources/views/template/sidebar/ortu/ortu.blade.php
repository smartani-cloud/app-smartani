@extends('template.main.psb.sidebar')

@section('sidebar-menu')
<li class="nav-item {{ (Request::path()=='psb/index') ? 'active' : '' }}">
    <a class="nav-link" href="/psb/index">
        <i class="mdi mdi-view-dashboard"></i>
        <span>Beranda</span>
    </a>
</li>
<hr class="sidebar-divider">
<div class="sidebar-heading">
    Pendaftaran
    {{-- {{dd(auth()->user()->orangtua->siswas)}} --}}
</div>
<li class="nav-item {{ (Request::path()=='kependidikan') ? 'active' : '' }}">
    <a class="nav-link" href="{{route('psb.siswa.create')}}">
        <i class="mdi mdi-view-dashboard"></i>
        <span>Pendaftaran Calon Siswa</span>
    </a>
</li>
<hr class="sidebar-divider">
<div class="sidebar-heading">
    Calon Siswa
    {{-- {{dd(auth()->user()->orangtua->siswas)}} --}}
</div>
@foreach( auth()->user()->orangtua->calonSiswa as $index => $anak )
<li class="nav-item {{ (request()->is('/psb/calon-siswa/'.$anak->student_nickname.'*')) ? 'active' : '' }}">
    <a class="nav-link" href="/psb/calon-siswa/{{$anak->student_nickname}}">
        <i class="fas fa-fw fa-user"></i>
        <span>{{ $anak->student_nickname }}</span>
    </a>
</li>
@endforeach
<hr class="sidebar-divider">
<div class="sidebar-heading">
    Siswa
</div>
@foreach( auth()->user()->orangtua->siswas as $index => $anak )
<li class="nav-item {{ (request()->is('/psb/siswa/'.$anak->student_nickname.'*')) ? 'active' : '' }}">
    <a class="nav-link" href="/psb/siswa/{{$anak->student_nickname}}">
        <i class="fas fa-fw fa-user"></i>
        <span>{{ $anak->student_nickname }}</span>
    </a>
</li>
@endforeach
    
<hr class="sidebar-divider">

@endsection