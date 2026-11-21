@extends('layouts.app')

@section('title', 'Riwayat Penggantian')

@section('content')
    <style>
        .ap-log { margin-top: 8px; display: flex; flex-direction: column; gap: 4px; }
        .ap-log-row { font-size: 12px; color: #6b7280; line-height: 1.4; }
        .ap-log-row b { color: #111827; font-weight: 600; }
        .ap-log-row .ok  { color: #16a34a; }
        .ap-log-row .bad { color: #dc2626; }
    </style>

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
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama part / no part / lokasi">

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
                        <th>Lokasi penggantian</th>
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
                            <td>{{ $h->replacement_location ?: '-' }}</td>
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
                            <td>
                                <span class="badge badge-{{ $cls }}">{{ $label }}</span>

                                @if ($h->approvals->isNotEmpty())
                                    <div class="ap-log">
                                        @foreach ($h->approvals->sortBy('level') as $a)
                                            <div class="ap-log-row">
                                                <b>{{ $a->approver->name ?? 'Atasan level ' . $a->level }}</b>
                                                @if ($a->status === 'approved')
                                                    <span class="ok">· Disetujui {{ $a->acted_at?->format('d/m/Y H:i') }} WIB</span>
                                                @elseif ($a->status === 'rejected')
                                                    <span class="bad">· Ditolak {{ $a->acted_at?->format('d/m/Y H:i') }} WIB</span>
                                                @else
                                                    <span>· Menunggu</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($h->status === 'rejected' && $h->reject_reason)
                                    <div class="ap-log-row" style="margin-top:6px;">Alasan: {{ $h->reject_reason }}</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="empty">Belum ada riwayat penggantian.</td></tr>
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