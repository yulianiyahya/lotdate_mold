@extends('layouts.app')

@section('title', 'Kelola Atasan')

@section('content')
<style>
    .ka-card { background:#fff; border-radius:16px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .ka-bar { display:flex; gap:10px; margin-bottom:16px; }
    .ka-bar input { flex:1; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-family:inherit; font-size:14px; }
    .ka-btn { display:inline-block; padding:9px 18px; border-radius:8px; border:1px solid #e60012; background:#fff; color:#e60012; font-weight:600; font-size:14px; text-decoration:none; cursor:pointer; font-family:inherit; }
    .ka-btn.solid { background:#e60012; color:#fff; }
    .ka-table { width:100%; border-collapse:collapse; font-size:14px; }
    .ka-table th { text-align:left; padding:10px; font-size:12px; color:#666; border-bottom:1px solid #eee; }
    .ka-table td { padding:12px 10px; border-bottom:1px solid #f1f1f1; }
    .ka-actions { display:flex; gap:8px; }
    .ka-actions form { margin:0; }
    .ka-link { background:none; border:0; padding:0; color:#e60012; font-weight:600; font-size:13px; cursor:pointer; text-decoration:underline; font-family:inherit; }
    .ka-alert { padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:14px; }
    .ka-alert.ok { background:#e8f7ee; color:#146c3a; }
    .ka-alert.err { background:#fdeaea; color:#a00; }
</style>

<div class="page-head" style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px;">
    <div>
        <h1 class="title">Kelola Atasan</h1>
        <p class="subtitle">Tambah, ubah, atau hapus akun Atasan yang bisa menyetujui penggantian mold.</p>
    </div>
    <a href="{{ route('atasan.create') }}" class="ka-btn solid">+ Tambah Atasan</a>
</div>

@if (session('success')) <div class="ka-alert ok">{{ session('success') }}</div> @endif
@if (session('error'))   <div class="ka-alert err">{{ session('error') }}</div> @endif

<div class="ka-card">
    <form method="GET" class="ka-bar">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / username / email">
        <button type="submit" class="ka-btn">Cari</button>
    </form>

    <table class="ka-table">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $u)
                <tr>
                    <td>
                        {{ $u->name }}
                        @if ($u->id === auth()->id()) <small style="color:#888;">(kamu)</small> @endif
                    </td>
                    <td>{{ $u->username }}</td>
                    <td>{{ $u->email }}</td>
                    <td>
                        <div class="ka-actions">
                            <a href="{{ route('atasan.edit', $u) }}" class="ka-link">Ubah</a>

                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('atasan.destroy', $u) }}"
                                      onsubmit="return confirm('Hapus akun {{ $u->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ka-link">Hapus</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="text-align:center; color:#666; padding:28px;">Belum ada akun Atasan.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $items->links() }}</div>
</div>
@endsection