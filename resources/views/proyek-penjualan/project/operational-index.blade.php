<<<<<<< HEAD
@extends('template.app.single-attribute-unit-crud-index')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Proposal Anggaran</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur {{ $active }}</li>
=======
@extends('template.app.single-attribute-unit-crud-index')

@section('sidebar')
@include('template.sidebar.proyek.proyek')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Beranda</a></li>
    <li class="breadcrumb-item"><a href="{{ route('project.index') }}">Proposal Anggaran</a></li>
    <li class="breadcrumb-item active" aria-current="page">Atur {{ $active }}</li>
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
@endsection