@extends('template.app.code-reference-crud-index')

@section('sidebar')
@include('template.sidebar.monitoring')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reference.index') }}">Referensi</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur {{ $active }}</li>

@endsection