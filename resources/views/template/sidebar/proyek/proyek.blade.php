@extends('template.main.sidebar')

@section('sidebar-menu')
      <li class="nav-item {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard.index') }}">
          <i class="mdi mdi-view-dashboard"></i>
          <span>Beranda</span></a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Manajemen Proyek
      </div>
      <li class="nav-item {{ request()->routeIs('proposal.*') || request()->routeIs('operational.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('proposal.*') || request()->routeIs('operational.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseProject" aria-expanded="{{ request()->routeIs('proposal.*') || request()->routeIs('operational.*') ? 'true' : 'false' }}" aria-controls="collapseProject">
          <i class="mdi mdi-tray-full"></i>
          <span>Proyek</span>
        </a>
        <div id="collapseProject" class="collapse {{ request()->routeIs('proposal.*') || request()->routeIs('operational.*') ? 'show' : '' }}" aria-labelledby="headingProject" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Proyek</h6>
            <a class="collapse-item {{ request()->routeIs('proposal.*') ? 'active' : '' }}" href="{{ route('proposal.index') }}">
              <i class="mdi mdi-file"></i>
              <span>Proposal Anggaran</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('operational.*') ? 'active' : '' }}" href="{{ route('operational.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Operasional</span>
            </a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Manajemen Keuangan
      </div>
      <li class="nav-item {{ request()->routeIs('report.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('report.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseReport" aria-expanded="{{ request()->routeIs('report.*') ? 'true' : 'false' }}" aria-controls="collapseReport">
          <i class="mdi mdi-bank-transfer"></i>
          <span>Laporan Keuangan</span>
        </a>
        <div id="collapseReport" class="collapse {{ request()->routeIs('report.*') ? 'show' : '' }}" aria-labelledby="headingReport" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Laporan Keuangan</h6>
            <a class="collapse-item {{ request()->routeIs('report.monthly.*') ? 'active' : '' }}" href="{{ route('report.monthly.index') }}">
              <i class="mdi mdi-calendar"></i>
              <span>Laporan Bulanan</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('report.project.*') ? 'active' : '' }}" href="{{ route('report.project.index') }}">
              <i class="mdi mdi-file"></i>
              <span>Laporan Proyek</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('outcome.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('outcome.index') }}">
          <i class="mdi mdi-bank-transfer-out"></i>
          <span>Pengeluaran</span></a>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Manajemen Stok
      </div>
      <li class="nav-item {{ request()->routeIs('material-stock.*') || request()->routeIs('material.*') || request()->routeIs('supplier.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('material-stock.*') || request()->routeIs('material.*') || request()->routeIs('supplier.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseMaterial" aria-expanded="{{ request()->routeIs('material-stock.*') || request()->routeIs('material.*') || request()->routeIs('supplier.*') ? 'true' : 'false' }}" aria-controls="collapseMaterial">
          <i class="mdi mdi-fruit-citrus"></i>
          <span>Bahan Baku</span>
        </a>
        <div id="collapseMaterial" class="collapse {{ request()->routeIs('material-stock.*') || request()->routeIs('material.*') || request()->routeIs('supplier.*') ? 'show' : '' }}" aria-labelledby="headingMaterial" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Bahan Baku</h6>
            <a class="collapse-item {{ request()->routeIs('material-stock.*') ? 'active' : '' }}" href="{{ route('material-stock.index') }}">
              <i class="mdi mdi-fridge-bottom"></i>
              <span>Stok Bahan Baku</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('material.*') ? 'active' : '' }}" href="{{ route('material.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Bahan Baku</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('supplier.*') ? 'active' : '' }}" href="{{ route('supplier.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Pemasok</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('premix-stock.*') || request()->routeIs('premix.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('premix-stock.*') || request()->routeIs('premix.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapsepremix" aria-expanded="{{ request()->routeIs('premix-stock.*') || request()->routeIs('premix.*') ? 'true' : 'false' }}" aria-controls="collapsepremix">
          <i class="mdi mdi-food-variant"></i>
          <span>Premix</span>
        </a>
        <div id="collapsepremix" class="collapse {{ request()->routeIs('premix-stock.*') || request()->routeIs('premix.*') ? 'show' : '' }}" aria-labelledby="headingpremix" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Premix</h6>
            <a class="collapse-item {{ request()->routeIs('premix-stock.*') ? 'active' : '' }}" href="{{ route('premix-stock.index') }}">
              <i class="mdi mdi-fridge-top"></i>
              <span>Stok Premix</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('premix.*') ? 'active' : '' }}" href="{{ route('premix.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Premix</span>
            </a>
          </div>
        </div>
      </li>
      <li class="nav-item {{ request()->routeIs('product-stock.*') || request()->routeIs('product.*') || request()->routeIs('product-price.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('product-stock.*') || request()->routeIs('product.*') || request()->routeIs('product-price.*') ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseProduct" aria-expanded="{{ request()->routeIs('product-stock.*') || request()->routeIs('product.*') || request()->routeIs('product-price.*') ? 'true' : 'false' }}" aria-controls="collapseProduct">
          <i class="mdi mdi-package-variant-closed"></i>
          <span>Produk</span>
        </a>
        <div id="collapseProduct" class="collapse {{ request()->routeIs('product-stock.*') || request()->routeIs('product.*') || request()->routeIs('product-price.*') ? 'show' : '' }}" aria-labelledby="headingProduct" data-parent="#accordionSidebar" style="">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Produk</h6>
            <a class="collapse-item {{ request()->routeIs('product-stock.*') ? 'active' : '' }}" href="{{ route('product-stock.index') }}">
              <i class="mdi mdi-warehouse"></i>
              <span>Stok Produk</span>
            </a>
            <hr class="sidebar-divider">
            <a class="collapse-item {{ request()->routeIs('product.*') ? 'active' : '' }}" href="{{ route('product.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Produk</span>
            </a>
            <a class="collapse-item {{ request()->routeIs('product-price.*') ? 'active' : '' }}" href="{{ route('product-price.index') }}">
              <i class="mdi mdi-cog"></i>
              <span>Harga Produk</span>
            </a>
          </div>
        </div>
      </li>
      <hr class="sidebar-divider">
      <div class="sidebar-heading">
        Manajemen Penjualan
      </div>
      <li class="nav-item {{ request()->routeIs('pos*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pos.index') }}">
          <i class="mdi mdi-cash-register"></i>
          <span>Penjualan</span>
        </a>
      </li>
      <li class="nav-item {{ request()->routeIs('buyer*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('buyer.index') }}">
          <i class="mdi mdi-home-account"></i>
          <span>Pembeli</span>
        </a>
      </li>
      <li class="nav-item {{ request()->routeIs('tax.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('tax.index') }}">
          <i class="mdi mdi-percent-outline"></i>
          <span>Pajak Penjualan</span></a>
      </li>
@endsection