@extends('layouts.app')

@section('title', 'Daftar Mold')

@section('content')
    <style>
        .table-wrap .table { min-width: 1900px; }

        .table .col-name    { min-width: 240px; }
        .table .col-partno  { min-width: 170px; }
        .table .col-type    { min-width: 170px; }
        .table .col-supplier{ min-width: 180px; }
        .table .col-aksi    { min-width: 170px; }

        .table .nowrap { white-space: nowrap; }

        .table .actions { display: flex; flex-direction: column; gap: 6px; align-items: stretch; }
        .table .actions form { display: block; margin: 0; }
        .table .actions .btn { width: 100%; white-space: nowrap; text-align: center; }
    </style>

    <div class="page-head">
        <div>
            <h1 class="title">Daftar Mold</h1>
            <p class="subtitle" style="margin-bottom:12px;">Semua mold yang dipantau sistem, per {{ now()->format('d/m/Y') }}.</p>
            <div class="legend">
                <span><i class="dot dot-red"></i><b>Expired</b> = Merah</span>
                <span><i class="dot dot-yellow"></i><b>Warning</b> = Kuning</span>
                <span><i class="dot dot-green"></i><b>Normal</b> = Hijau</span>
            </div>
        </div>
        <div class="head-actions">
            <a href="{{ route('molds.export', request()->query()) }}" class="btn btn-outline">Unduh Excel</a>
            @if (auth()->user()->isPic())
                <a href="{{ route('molds.create') }}" class="btn btn-primary">+ Tambah mold</a>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    <div class="card card-flush">
        <form method="GET" action="{{ route('molds.index') }}" class="toolbar">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari part name / part no">

            <select name="kategori" onchange="this.form.submit()">
                <option value="">Semua kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c }}" {{ request('kategori') === $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach (['expired' => 'Expired', 'warning' => 'Warning', 'normal' => 'Normal'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>

        <div class="table-wrap" style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th class="col-name">Part name</th>
                        <th class="col-partno">Part no</th>
                        <th>Kategori</th>
                        <th class="col-type">Tipe</th>
                        <th class="nowrap">Lokasi</th>
                        <th class="nowrap">Kebutuhan</th>
                        <th class="col-supplier">Supplier</th>
                        <th class="nowrap">Frekuensi</th>
                        <th class="nowrap">Lead time</th>
                        <th>Penggantian terakhir</th>
                        <th class="nowrap">Jatuh tempo</th>
                        <th class="nowrap">Sisa waktu</th>
                        <th>Status</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($molds as $mold)
                        @php
                            $days = (int) now()->startOfDay()->diffInDays($mold->next_due_date, false);
                        @endphp
                        <tr>
                            <td class="code col-name">{{ $mold->part_name }}</td>
                            <td class="col-partno">
                                @forelse ($mold->part_no_list as $no)
                                    <div>{{ $no }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td>
                                @if ($mold->category)
                                    <span class="pill">{{ $mold->category }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="col-type">{{ $mold->type ?: '-' }}</td>
                            <td class="nowrap">{{ $mold->location ?: '-' }}</td>
                            <td class="nowrap">{{ $mold->kebutuhan !== null ? $mold->kebutuhan . ' pcs' : '-' }}</td>
                            <td class="col-supplier">{{ $mold->supplier ?: '-' }}</td>
                            <td class="nowrap">Setiap {{ $mold->frequency_label }}</td>
                            <td class="nowrap">{{ $mold->lead_time_label }}</td>
                            <td class="nowrap">{{ $mold->last_replaced_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="nowrap">{{ $mold->next_due_date->format('d/m/Y') }}</td>
                            <td class="nowrap">
                                @if ($days < 0)
                                    Terlambat {{ abs($days) }} hari
                                @else
                                    {{ $days }} hari lagi
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $mold->status }}">{{ $mold->status_label }}</span></td>
                            <td class="col-aksi">
                                <div class="actions">
                                    @if (auth()->user()->isPic())
                                        <a href="{{ route('molds.edit', $mold) }}" class="btn btn-sm">Detail</a>
                                        <a href="{{ route('replacements.create', $mold) }}" class="btn btn-sm">Catat penggantian</a>

                                        <form method="POST" action="{{ route('molds.destroy', $mold) }}"
                                              onsubmit="return confirm(@js('Hapus mold ' . $mold->part_name . '? Mold tidak akan tampil lagi di daftar.'))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="14" class="empty">Belum ada mold yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-foot">
            <span>
                Menampilkan {{ $molds->firstItem() ?? 0 }}–{{ $molds->lastItem() ?? 0 }} dari {{ $molds->total() }} mold
            </span>
            {{ $molds->links('vendor.pagination.lotdate') }}
        </div>
    </div>
@endsection