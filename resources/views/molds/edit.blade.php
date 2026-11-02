@extends('layouts.app')

@section('title', 'Edit mold')

@section('content')
    <div class="breadcrumb"><a href="{{ route('molds.index') }}">Daftar Mold</a> / Edit mold</div>
    <h1 class="title">Edit mold {{ $mold->part_name }}</h1>
    <p class="subtitle">Ubah data mold. Jatuh tempo dihitung ulang otomatis saat disimpan.</p>

    <div class="card form-card">
        @include('molds._form', ['action' => route('molds.update', $mold), 'mold' => $mold])

        <div class="danger-zone">
            <form method="POST" action="{{ route('molds.destroy', $mold) }}"
                  onsubmit="return confirm('Nonaktifkan mold {{ $mold->part_name }}? Mold tidak akan tampil di daftar.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Nonaktifkan mold</button>
            </form>
        </div>
    </div>
@endsection