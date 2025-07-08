@extends('template.main.sidebar')

@section('brand-system')
Keuangan @endsection

@section('sidebar-menu')
@php
$role = Auth::user()->role->name;
@endphp
      <li class="nav-item {{ request()->routeIs('keuangan.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('keuangan.index') }}">
          <i class="mdi mdi-view-dashboard"></i>
          <span>Beranda</span>
        </a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Pengelolaan Keuangan
      </div>
	  @if(in_array($role,['admin','pembinayys','ketuayys','kepsek','direktur','etl','fam','faspv','ctl','am','akunspv']))
      <li class="nav-item {{ request()->routeIs('rkat*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('rkat.index') }}">
          <i class="mdi mdi-file-edit"></i>
          <span>RKAB</span>
        </a>
      </li>
	  @endif
	  @if(in_array($role,['admin','pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']))
      <li class="nav-item {{ request()->routeIs('apby*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('apby.index') }}">
          <i class="mdi mdi-cart"></i>
          <span>APBY</span>
        </a>
      </li>
	  @endif
	  <li class="nav-item {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') || request()->routeIs('kunci-ppa*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') || request()->routeIs('kunci-ppa*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsePpa" aria-expanded="{{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') || request()->routeIs('kunci-ppa*') ? 'true' : 'false' }}" aria-controls="collapsePpa">
          <i class="mdi mdi-inbox-arrow-up"></i>
          <span>PPA</span>
        </a>
        <div id="collapsePpa" class="collapse {{ request()->routeIs('ppa*') || request()->routeIs('proposal-ppa*') || request()->routeIs('kunci-ppa*') ? 'show' : '' }}" aria-labelledby="headingPpa" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">PPA</h6>
			@if(in_array($role,['admin','pembinayys','ketuayys','kepsek','wakasek','keu','direktur','etl','fam','faspv','fas','ctl','am','akunspv']))
            <a class="collapse-item {{ request()->routeIs('ppa*') ? 'active' : '' }}" href="{{ route('ppa.index') }}">
              <i class="mdi mdi-file-document"></i>
              <span>PPA</span>
            </a>
			@endif
            <a class="collapse-item {{ request()->routeIs('proposal-ppa*') ? 'active' : '' }}" href="{{ route('proposal-ppa.index') }}">
              <i class="mdi mdi-chat-plus"></i>
              <span>Proposal</span>
            </a>
			@if(in_array($role,['pembinayys','ketuayys','direktur','am']))
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('kunci-ppa*') ? 'active' : '' }}" href="{{ route('kunci-ppa.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Kunci PPA</span>
            </a>
			@endif
          </div>
        </div>
      </li>
	  @if(in_array($role,['admin','pembinayys','ketuayys','direktur','fam','faspv','fas','am','akunspv','keulsi']))
      <li class="nav-item {{ request()->routeIs('ppb*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('ppb.index') }}">
          <i class="mdi mdi-bank-transfer-out"></i>
          <span>PPB</span>
        </a>
      </li>
	  @endif
	  @if(in_array($role,['admin','pembinayys','ketuayys','kepsek','wakasek','keu','direktur','etl','fam','faspv','fas','ctl','am','akunspv']))
      <li class="nav-item {{ request()->routeIs('lppa*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('lppa.index') }}">
          <i class="mdi mdi-text-box-check"></i>
          <span>RPPA</span>
        </a>
      </li>
	  @endif
	  @if(in_array($role,['admin','pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']))
      <li class="nav-item {{ request()->routeIs('realisasi*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('realisasi.index') }}">
          <i class="mdi mdi-file-chart"></i>
          <span>Realisasi</span>
        </a>
      </li>
	  @endif
	  @if(in_array($role,['ketuayys']))
      <li class="nav-item {{ request()->routeIs('tahun-anggaran*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tahun-anggaran.index') }}">
          <i class="mdi mdi-calendar"></i>
          <span>Tahun Anggaran</span>
        </a>
      </li>
	  @endif
	  @if(in_array($role,['admin','pembinayys','ketuayys','direktur','fam','faspv','am','akunspv']))
      <hr class="sidebar-divider">
	  <div class="sidebar-heading">
        Pengelolaan Anggaran
      </div>
	  <li class="nav-item {{ request()->routeIs('keuangan.pratinjau-akun*') || request()->routeIs('keuangan.akun*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('keuangan.pratinjau-akun*') || request()->routeIs('keuangan.akun*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseAkun" aria-expanded="{{ request()->routeIs('keuangan.pratinjau-akun*') || request()->routeIs('keuangan.akun*') ? 'true' : 'false' }}" aria-controls="collapseAkun">
          <i class="mdi mdi-playlist-edit"></i>
          <span>Akun</span>
        </a>
        <div id="collapseAkun" class="collapse {{ request()->routeIs('keuangan.pratinjau-akun*') || request()->routeIs('keuangan.akun*') ? 'show' : '' }}" aria-labelledby="headingPpa" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Akun</h6>
            <a class="collapse-item {{ request()->routeIs('keuangan.pratinjau-akun*') ? 'active' : '' }}" href="{{ route('keuangan.pratinjau-akun.index') }}">
              <i class="mdi mdi-file-document"></i>
              <span>Pratinjau Akun</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('keuangan.akun*') ? 'active' : '' }}" href="{{ route('keuangan.akun.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Akun Anggaran</span>
            </a>
          </div>
        </div>
      </li>
	  @endif
      <hr class="sidebar-divider">
      @include('template.sidebar.keuangan.pembayaran')
@endsection