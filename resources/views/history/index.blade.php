@extends('layouts.app')

@section('title', 'Riwayat Penggantian')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="title">Riwayat Penggantian</h1>
            <p class="subtitle">Semua penggantian lot date mold beserta PIC dan status persetujuan.</p>
        </div>
        <div class="head-actions">
            <a href="{{ route('history.export', request()->query()) }}" class="btn btn-outline">Unduh Excel</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card card-flush">
        <form method="GET" action="{{ route('history.index') }}" class="toolbar">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama part / no part">

            <select name="jenis" onchange="this.form.submit()">
                <option value="">Semua jenis</option>
                @foreach ($types as $t)
                    <option value="{{ $t }}" {{ request('jenis') === $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>

            <select name="tahun" onchange="this.form.submit()">
                <option value="">Semua tahun</option>
                @foreach ($years as $y)
                    <option value="{{ $y }}" {{ (string) request('tahun') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode mold</th>
                        <th>Jenis</th>
                        <th>PIC</th>
                        <th>Jatuh tempo berikutnya</th>
                        <th>Foto</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $h)
                        @php
                            $map = [
                                'pending'  => ['pending', 'Menunggu Approval'],
                                'approved' => ['normal',  'Disetujui'],
                                'rejected' => ['expired', 'Ditolak'],
                            ];
                            [$cls, $label] = $map[$h->status] ?? ['pending', $h->status];
                        @endphp
                        <tr>
                            <td class="code">{{ $h->replacement_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="code">{{ $h->mold->part_name ?? '-' }}</div>
                                <small style="color:#6b7280;">{{ $h->mold->part_no ?? '' }}</small>
                            </td>
                            <td><span class="pill">{{ $h->mold->category ?? '-' }}</span></td>
                            <td>{{ $h->changer->name ?? '-' }}</td>
                            <td>{{ $h->new_due_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="thumbs">
                                    @foreach (['before_photo', 'after_photo'] as $col)
                                        @if ($h->$col)
                                            <a href="{{ asset('storage/' . $h->$col) }}" target="_blank">
                                                <img class="thumb" src="{{ asset('storage/' . $h->$col) }}" alt="{{ $col === 'before_photo' ? 'Sebelum' : 'Sesudah' }}">
                                            </a>
                                        @else
                                            -
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                            <td><span class="badge badge-{{ $cls }}">{{ $label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">Belum ada riwayat penggantian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-foot">
            <span>Menampilkan {{ $items->firstItem() ?? 0 }}–{{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} penggantian</span>
            {{ $items->links('vendor.pagination.lotdate') }}
        </div>
    </div>
@endsection