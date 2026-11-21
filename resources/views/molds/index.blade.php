@extends('layouts.app')

@section('title', 'Daftar Mold')

@section('content')
    <style>
        .table-wrap .table { min-width: 2240px; }

        .table .col-name    { min-width: 240px; }
        .table .col-partno  { min-width: 170px; }
        .table .col-spec    { min-width: 170px; }
        .table .col-type    { min-width: 170px; }
        .table .col-locexp  { min-width: 170px; }
        .table .col-supplier{ min-width: 180px; }
        .table .col-aksi    { min-width: 170px; }

        .table .nowrap { white-space: nowrap; }

        .table .actions { display: flex; flex-direction: column; gap: 6px; align-items: stretch; }
        .table .actions form { display: block; margin: 0; }
        .table .actions .btn { width: 100%; white-space: nowrap; text-align: center; }

        /* ===== Modal konfirmasi hapus ===== */
        .confirm-dialog {
            border: 0;
            padding: 0;
            border-radius: 20px;
            width: min(420px, calc(100vw - 32px));
            background: #fff;
            color: #111827;
            box-shadow: 0 24px 60px rgba(17, 24, 39, .28);
            overflow: hidden;
        }
        .confirm-dialog[open] { animation: confirm-pop .18s ease-out; }
        .confirm-dialog::backdrop {
            background: rgba(17, 24, 39, .45);
            backdrop-filter: blur(3px);
        }
        .confirm-dialog[open]::backdrop { animation: confirm-fade .18s ease-out; }

        @keyframes confirm-pop {
            from { opacity: 0; transform: translateY(8px) scale(.96); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes confirm-fade { from { opacity: 0; } to { opacity: 1; } }

        .confirm-body { padding: 28px 28px 8px; text-align: center; }
        .confirm-icon {
            width: 56px; height: 56px; margin: 0 auto 16px;
            border-radius: 50%;
            background: #fee2e2; color: #dc2626;
            display: flex; align-items: center; justify-content: center;
        }
        .confirm-icon svg { width: 28px; height: 28px; }
        .confirm-title { margin: 0 0 8px; font-size: 20px; font-weight: 700; }
        .confirm-text  { margin: 0; font-size: 14px; line-height: 1.6; color: #6b7280; }
        .confirm-name {
            display: inline-block; margin-top: 12px; padding: 6px 12px;
            border-radius: 10px; background: #f3f4f6;
            font-weight: 600; font-size: 14px; color: #111827;
            max-width: 100%; word-break: break-word;
        }

        .confirm-actions { display: flex; gap: 10px; padding: 20px 28px 28px; }
        .confirm-actions button {
            flex: 1; padding: 11px 16px; border-radius: 12px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            border: 1px solid #e5e7eb; background: #fff; color: #111827;
            transition: background .15s, transform .05s;
        }
        .confirm-actions button:hover  { background: #f9fafb; }
        .confirm-actions button:active { transform: scale(.98); }
        .confirm-actions .confirm-danger {
            background: #dc2626; border-color: #dc2626; color: #fff;
        }
        .confirm-actions .confirm-danger:hover { background: #b91c1c; border-color: #b91c1c; }
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
                <a href="{{ route('molds.import.create') }}" class="btn btn-outline">Impor Excel</a>
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
                        <th class="col-spec">Spec</th>
                        <th class="col-type">Tipe</th>
                        <th class="nowrap">Lokasi</th>
                        <th class="col-locexp">Lokasi per tahun expired</th>
                        <th class="nowrap">Kebutuhan</th>
                        <th class="col-supplier">Supplier</th>
                        <th class="nowrap">Frekuensi</th>
                        <th class="nowrap">Lead time</th>
                        <th>Penggantian terakhir</th>
                        <th class="nowrap">Jatuh tempo</th>
                        <th class="nowrap">Sisa waktu</th>
                        <th>Status</th>
                        @if (auth()->user()->isPic())
                            <th class="col-aksi">Aksi</th>
                        @endif
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
                            <td class="col-spec">{{ $mold->spec ?: '-' }}</td>
                            <td class="col-type">{{ $mold->type ?: '-' }}</td>
                            <td class="nowrap">{{ $mold->location ?: '-' }}</td>
                            <td class="col-locexp">{{ $mold->location_yearly_expired ?: '-' }}</td>
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
                            @if (auth()->user()->isPic())
                                <td class="col-aksi">
                                    <div class="actions">
                                        <a href="{{ route('molds.edit', $mold) }}" class="btn btn-sm">Detail</a>
                                        <a href="{{ route('replacements.create', $mold) }}" class="btn btn-sm">Catat penggantian</a>

                                        <form method="POST" action="{{ route('molds.destroy', $mold) }}"
                                              class="js-delete-form" data-name="{{ $mold->part_name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ auth()->user()->isPic() ? 16 : 15 }}" class="empty">Belum ada mold yang sesuai.</td></tr>
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

    @if (auth()->user()->isPic())
        <dialog id="confirmDelete" class="confirm-dialog" aria-labelledby="confirmTitle">
            <div class="confirm-body">
                <div class="confirm-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/>
                        <path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <path d="M10 11v6M14 11v6"/>
                    </svg>
                </div>
                <h2 class="confirm-title" id="confirmTitle">Hapus mold ini?</h2>
                <p class="confirm-text">Mold tidak akan tampil lagi di daftar. Riwayat penggantiannya tetap tersimpan.</p>
                <span class="confirm-name" id="confirmName"></span>
            </div>
            <div class="confirm-actions">
                <button type="button" id="confirmCancel">Batal</button>
                <button type="button" id="confirmOk" class="confirm-danger">Ya, hapus</button>
            </div>
        </dialog>

        <script>
            (function () {
                const dialog = document.getElementById('confirmDelete');
                const nameEl = document.getElementById('confirmName');
                const okBtn  = document.getElementById('confirmOk');
                const cancel = document.getElementById('confirmCancel');
                let pendingForm = null;

                document.querySelectorAll('.js-delete-form').forEach(function (form) {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        pendingForm = form;
                        nameEl.textContent = form.dataset.name;
                        dialog.showModal();
                    });
                });

                okBtn.addEventListener('click', function () {
                    if (pendingForm) {
                        okBtn.disabled = true;
                        okBtn.textContent = 'Menghapus...';
                        pendingForm.submit();
                    }
                });

                cancel.addEventListener('click', function () { dialog.close(); });

                // Klik di luar kotak = tutup
                dialog.addEventListener('click', function (e) {
                    const r = dialog.getBoundingClientRect();
                    const inside = e.clientX >= r.left && e.clientX <= r.right &&
                                   e.clientY >= r.top  && e.clientY <= r.bottom;
                    if (!inside) dialog.close();
                });

                dialog.addEventListener('close', function () { pendingForm = null; });
            })();
        </script>
    @endif
@endsection