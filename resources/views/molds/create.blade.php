@extends('layouts.app')

@section('title', 'Tambah mold')

@section('content')
    <div class="breadcrumb"><a href="{{ route('molds.index') }}">Daftar Mold</a> / Tambah mold</div>
    <h1 class="title">Tambah mold</h1>
    <p class="subtitle">Daftarkan mold baru ke sistem monitoring. Form yang sama dipakai untuk Edit data mold.</p>

    <div class="card form-card">
        @include('molds._form', ['action' => route('molds.store'), 'mold' => null])
    </div>
@endsection