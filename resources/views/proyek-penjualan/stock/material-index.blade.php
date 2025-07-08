@extends('template.app.single-attribute-unit-crud-index')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('stock.index') }}">Stok Bahan Baku</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur {{ $active }}</li>
@endsection