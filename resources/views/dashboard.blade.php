@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .dash-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        .dash-card {
            display: block;
            background: #fff;
            border: 1px solid #eee;
            border-left: 6px solid #ccc;
            border-radius: 12px;
            padding: 18px 20px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
            transition: transform .12s, box-shadow .12s;
        }
        .dash-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 0, 0, .1); }
        .dash-card .dash-label { font-size: 13px; color: #666; font-weight: 600; }
        .dash-card .dash-num   { font-size: 34px; font-weight: 800; line-height: 1.2; margin-top: 6px; }
        .dash-card .dash-note  { font-size: 12px; color: #888; margin-top: 2px; }

        .dash-red    { border-left-color: #e60012; }
        .dash-yellow { border-left-color: #f5b301; }
        .dash-green  { border-left-color: #2e9e4f; }
        .dash-gray   { border-left-color: #555; }
        .dash-blue   { border-left-color: #2563eb; }

        .dash-red .dash-num    { color: #e60012; }
        .dash-yellow .dash-num { color: #c58f00; }
        .dash-green .dash-num  { color: #2e9e4f; }
    </style>

    <div class="page-head">
        <div>
            <h1 class="title">Dashboard</h1>
            <p class="subtitle">
                Ringkasan status mold per {{ now()->format('d/m/Y') }}.
                Halo, {{ auth()->user()->name }}.
            </p>
        </div>
        <div class="head-actions">
            <a href="{{ route('molds.index') }}" class="btn btn-outline">Lihat Daftar Mold</a>
        </div>
    </div>

    <div class="dash-cards">
        <a href="{{ route('molds.index') }}" class="dash-card dash-gray">
            <div class="dash-label">Total mold</div>
            <div class="dash-num">{{ $counts['total'] }}</div>
            <div class="dash-note">Mold aktif dipantau</div>
        </a>

        <a href="{{ route('molds.index', ['status' => 'expired']) }}" class="dash-card dash-red">
            <div class="dash-label">Expired</div>
            <div class="dash-num">{{ $counts['expired'] }}</div>
            <div class="dash-note">Sudah lewat jatuh tempo</div>
        </a>

        <a href="{{ route('molds.index', ['status' => 'warning']) }}" class="dash-card dash-yellow">
            <div class="dash-label">Warning</div>
            <div class="dash-num">{{ $counts['warning'] }}</div>
            <div class="dash-note">Masuk masa lead time</div>
        </a>

        <a href="{{ route('molds.index', ['status' => 'normal']) }}" class="dash-card dash-green">
            <div class="dash-label">Normal</div>
            <div class="dash-num">{{ $counts['normal'] }}</div>
            <div class="dash-note">Masih aman</div>
        </a>

        @if (auth()->user()->role === 'Atasan')
            <a href="{{ route('approvals.index') }}" class="dash-card dash-blue">
                <div class="dash-label">Menunggu persetujuan</div>
                <div class="dash-num">{{ $pendingApprovals }}</div>
                <div class="dash-note">Penggantian berstatus pending</div>
            </a>
        @else
            <a href="{{ route('history.index') }}" class="dash-card dash-blue">
                <div class="dash-label">Menunggu persetujuan</div>
                <div class="dash-num">{{ $pendingApprovals }}</div>
                <div class="dash-note">Lihat di Riwayat Penggantian</div>
            </a>
        @endif
    </div>

    <div class="card card-flush">
        <div class="toolbar" style="justify-content:space-between;">
            <strong>Mold paling dekat jatuh tempo</strong>
            <a href="{{ route('molds.index') }}" style="font-size:13px;">Lihat semua</a>
        </div>

        <div class="table-wrap" style="overflow-x:auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Part name</th>
                        <th>Part no</th>
                        <th>Kategori</th>
                        <th class="nowrap">Jatuh tempo</th>
                        <th class="nowrap">Sisa waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($upcoming as $mold)
                        @php
                            $days = (int) now()->startOfDay()->diffInDays($mold->next_due_date, false);
                        @endphp
                        <tr>
                            <td class="code">{{ $mold->part_name }}</td>
                            <td>
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
                            <td class="nowrap">{{ $mold->next_due_date->format('d/m/Y') }}</td>
                            <td class="nowrap">
                                @if ($days < 0)
                                    Terlambat {{ abs($days) }} hari
                                @else
                                    {{ $days }} hari lagi
                                @endif
                            </td>
                            <td><span class="badge badge-{{ $mold->status }}">{{ $mold->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Belum ada mold yang dipantau.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection