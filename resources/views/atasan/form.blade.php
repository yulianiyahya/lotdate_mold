@extends('layouts.app')

@section('title', $user->exists ? 'Ubah Atasan' : 'Tambah Atasan')

@section('content')
<style>
    .kf-card { background:#fff; border-radius:16px; padding:24px; max-width:560px; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .kf-group { margin-bottom:16px; }
    .kf-group label { display:block; margin-bottom:6px; font-size:13px; font-weight:600; }
    .kf-group input { width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-family:inherit; font-size:14px; box-sizing:border-box; }
    .kf-err { color:#c00; font-size:12px; margin-top:4px; }
    .kf-hint { color:#888; font-size:12px; margin-top:4px; }
    .kf-actions { display:flex; gap:10px; margin-top:8px; }
    .kf-btn { padding:10px 20px; border-radius:8px; border:1px solid #e60012; background:#fff; color:#e60012; font-weight:600; font-size:14px; text-decoration:none; cursor:pointer; font-family:inherit; }
    .kf-btn.solid { background:#e60012; color:#fff; }
</style>

<div class="page-head" style="margin-bottom:24px;">
    <div>
        <h1 class="title">{{ $user->exists ? 'Ubah Atasan' : 'Tambah Atasan' }}</h1>
        <p class="subtitle">
            {{ $user->exists ? 'Perbarui data akun Atasan.' : 'Buat akun Atasan baru.' }}
        </p>
    </div>
</div>

<div class="kf-card">
    <form method="POST"
          action="{{ $user->exists ? route('atasan.update', $user) : route('atasan.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="kf-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <div class="kf-err">{{ $message }}</div> @enderror
        </div>

        <div class="kf-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required>
            @error('username') <div class="kf-err">{{ $message }}</div> @enderror
        </div>

        <div class="kf-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email') <div class="kf-err">{{ $message }}</div> @enderror
        </div>

        <div class="kf-group">
            <label for="password">Password {{ $user->exists ? 'baru' : '' }}</label>
            <input type="password" id="password" name="password" {{ $user->exists ? '' : 'required' }} autocomplete="new-password">
            @if ($user->exists) <div class="kf-hint">Kosongkan jika tidak ingin mengubah password.</div> @endif
            @error('password') <div class="kf-err">{{ $message }}</div> @enderror
        </div>

        <div class="kf-group">
            <label for="password_confirmation">Konfirmasi password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
        </div>

        <div class="kf-actions">
            <button type="submit" class="kf-btn solid">Simpan</button>
            <a href="{{ route('atasan.index') }}" class="kf-btn">Batal</a>
        </div>
    </form>
</div>
@endsection