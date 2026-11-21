@extends('layouts.app')

@section('title', 'Persetujuan')

@section('content')
    <style>
        /* ===== Daftar ===== */
        .ap-count { display: inline-block; margin-left: 8px; padding: 2px 10px; border-radius: 999px; background: #fee2e2; color: #b91c1c; font-size: 13px; font-weight: 700; vertical-align: middle; }
        .ap-row { cursor: pointer; transition: background .12s; }
        .ap-row:hover { background: #fafafa; }
        .ap-row small { color: #6b7280; }
        .ap-turn { font-size: 12px; font-weight: 600; }
        .ap-turn.me { color: #16a34a; }
        .ap-turn.wait { color: #6b7280; }
        .ap-open {
            padding: 6px 14px; border-radius: 10px; font-size: 12px; font-weight: 600; cursor: pointer;
            border: 1px solid #e5e7eb; background: #fff; color: #111827; white-space: nowrap;
        }
        .ap-open:hover { background: #f9fafb; }
        .ap-empty-search { display: none; padding: 28px; text-align: center; color: #6b7280; font-size: 14px; }

        .ap-empty { padding: 56px 24px; text-align: center; }
        .ap-empty .ic {
            width: 64px; height: 64px; margin: 0 auto 14px; border-radius: 50%;
            background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center;
        }
        .ap-empty .ic svg { width: 30px; height: 30px; }

        /* ===== Dialog umum ===== */
        .ap-dialog {
            border: 0; padding: 0; border-radius: 20px; width: min(440px, calc(100vw - 32px));
            background: #fff; color: #111827; box-shadow: 0 24px 60px rgba(17,24,39,.28); overflow: hidden;
        }
        .ap-dialog[open] { animation: ap-pop .18s ease-out; }
        .ap-dialog::backdrop { background: rgba(17,24,39,.5); backdrop-filter: blur(3px); }
        @keyframes ap-pop { from { opacity: 0; transform: translateY(8px) scale(.97); } to { opacity: 1; transform: none; } }
        .ap-dialog-body { padding: 28px 28px 8px; text-align: center; }
        .ap-dialog-icon {
            width: 56px; height: 56px; margin: 0 auto 16px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .ap-dialog-icon svg { width: 28px; height: 28px; }
        .ap-dialog-icon.green { background: #dcfce7; color: #16a34a; }
        .ap-dialog-icon.red { background: #fee2e2; color: #dc2626; }
        .ap-dialog h3 { margin: 0 0 8px; font-size: 20px; }
        .ap-dialog p { margin: 0; font-size: 14px; line-height: 1.6; color: #6b7280; }
        .ap-dialog-name {
            display: inline-block; margin-top: 12px; padding: 6px 12px; border-radius: 10px;
            background: #f3f4f6; font-weight: 600; font-size: 14px; color: #111827;
        }
        .ap-dialog textarea {
            width: 100%; min-height: 96px; margin-top: 14px; padding: 12px; border-radius: 12px;
            border: 1px solid #d1d5db; font: inherit; font-size: 14px; resize: vertical; box-sizing: border-box; text-align: left;
        }
        .ap-dialog textarea:focus { outline: 2px solid #fca5a5; border-color: #dc2626; }
        .ap-dialog .err { margin-top: 6px; color: #dc2626; font-size: 12px; text-align: left; }
        .ap-dialog-actions { display: flex; gap: 10px; padding: 20px 28px 28px; }
        .ap-dialog-actions button {
            flex: 1; padding: 11px 16px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer;
            border: 1px solid #e5e7eb; background: #fff; color: #111827;
        }
        .ap-dialog-actions button:hover { background: #f9fafb; }
        .ap-dialog-actions .go-green { background: #16a34a; border-color: #16a34a; color: #fff; }
        .ap-dialog-actions .go-green:hover { background: #15803d; }
        .ap-dialog-actions .go-red { background: #dc2626; border-color: #dc2626; color: #fff; }
        .ap-dialog-actions .go-red:hover { background: #b91c1c; }

        /* ===== Dialog detail ===== */
        .ap-detail-dialog {
            width: min(980px, calc(100vw - 32px)); max-height: 92vh; overflow-y: auto; overflow-x: hidden;
        }
        .ap-detail-head {
            position: sticky; top: 0; z-index: 2; background: #fff;
            display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;
            padding: 22px 26px; border-bottom: 1px solid #f1f2f4;
        }
        .ap-detail-head h2 { margin: 0 0 4px; font-size: 20px; }
        .ap-head-right { display: flex; align-items: center; gap: 12px; }
        .ap-close {
            width: 34px; height: 34px; border-radius: 50%; border: 0; background: #f3f4f6; cursor: pointer;
            font-size: 18px; line-height: 1; color: #374151;
        }
        .ap-close:hover { background: #e5e7eb; }
        .ap-sub { color: #6b7280; font-size: 13px; }
        .ap-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .ap-chip { display: inline-block; padding: 4px 10px; border-radius: 999px; background: #f3f4f6; font-size: 12px; color: #374151; }

        .ap-section { padding: 20px 26px; border-bottom: 1px solid #f1f2f4; }
        .ap-title { margin: 0 0 14px; font-size: 13px; font-weight: 700; color: #6b7280; letter-spacing: .04em; text-transform: uppercase; }
        .ap-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px 24px; }
        .ap-field span { display: block; font-size: 12px; color: #6b7280; margin-bottom: 3px; }
        .ap-field b { font-size: 14px; color: #111827; word-break: break-word; }

        .ap-compare { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        .ap-date-box { flex: 1; min-width: 200px; padding: 14px 16px; border-radius: 14px; border: 1px solid #e5e7eb; background: #fff; }
        .ap-date-box.new { border-color: #bbf7d0; background: #f0fdf4; }
        .ap-date-box > span { display: block; font-size: 12px; color: #6b7280; margin-bottom: 4px; }
        .ap-date-box b { font-size: 20px; color: #111827; }
        .ap-date-box small { display: block; margin-top: 8px; font-size: 12px; color: #6b7280; }
        .ap-arrow { font-size: 22px; color: #9ca3af; }
        .ap-shift { display: inline-block; margin-top: 10px; font-size: 12px; color: #6b7280; }

        .ap-photos { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; }
        .ap-photo { margin: 0; }
        .ap-photo figcaption { font-size: 12px; color: #6b7280; margin-bottom: 6px; }
        .ap-photo button {
            display: block; width: 100%; padding: 0; border: 1px solid #e5e7eb; border-radius: 14px;
            background: #111827; cursor: zoom-in; overflow: hidden; position: relative;
        }
        .ap-photo img { display: block; width: 100%; height: 280px; object-fit: contain; }
        .ap-photo .zoom {
            position: absolute; right: 10px; bottom: 10px; padding: 4px 10px; border-radius: 999px;
            background: rgba(17,24,39,.75); color: #fff; font-size: 12px;
        }
        .ap-photo-empty {
            height: 280px; display: flex; align-items: center; justify-content: center;
            border: 1px dashed #d1d5db; border-radius: 14px; color: #9ca3af; font-size: 13px;
        }

        .ap-note { padding: 14px 16px; border-radius: 12px; background: #fffbeb; border: 1px solid #fde68a; font-size: 14px; line-height: 1.6; }
        .ap-note b { display: block; font-size: 12px; color: #92400e; margin-bottom: 4px; }

        .ap-timeline { display: flex; flex-direction: column; }
        .ap-step { display: flex; gap: 14px; position: relative; padding-bottom: 18px; }
        .ap-step:last-child { padding-bottom: 0; }
        .ap-step:not(:last-child)::before { content: ''; position: absolute; left: 15px; top: 32px; bottom: 0; width: 2px; background: #e5e7eb; }
        .ap-dot { flex: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; background: #111827; color: #fff; }
        .ap-dot.done { background: #16a34a; }
        .ap-dot.wait { background: #d1d5db; color: #374151; }
        .ap-dot.bad  { background: #dc2626; }
        .ap-step-name { font-size: 14px; font-weight: 600; }
        .ap-step-state { font-size: 12px; color: #6b7280; margin-top: 2px; }
        .ap-step-state.ok { color: #16a34a; }
        .ap-step-state.bad { color: #dc2626; }

        .ap-foot {
            position: sticky; bottom: 0; z-index: 2; background: #fff; border-top: 1px solid #f1f2f4;
            padding: 16px 26px;
        }
        .ap-waiting { padding: 14px 16px; border-radius: 12px; background: #f3f4f6; color: #4b5563; font-size: 14px; }
        .ap-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .ap-btn {
            padding: 11px 22px; border-radius: 12px; font-size: 14px; font-weight: 600; cursor: pointer;
            border: 1px solid #e5e7eb; background: #fff; color: #111827; transition: background .15s, transform .05s;
        }
        .ap-btn:active { transform: scale(.98); }
        .ap-btn.approve { background: #16a34a; border-color: #16a34a; color: #fff; }
        .ap-btn.approve:hover { background: #15803d; border-color: #15803d; }
        .ap-btn.reject { background: #fff; border-color: #dc2626; color: #dc2626; }
        .ap-btn.reject:hover { background: #fef2f2; }

        .ap-lightbox { width: min(1000px, calc(100vw - 24px)); background: #111827; }
        .ap-lightbox img { display: block; width: 100%; max-height: 82vh; object-fit: contain; }
        .ap-lightbox-bar { display: flex; justify-content: space-between; align-items: center; padding: 12px 18px; color: #fff; font-size: 14px; }
        .ap-lightbox-bar button { background: rgba(255,255,255,.15); border: 0; color: #fff; padding: 6px 14px; border-radius: 999px; cursor: pointer; }
    </style>

    <h1 class="title">
        Persetujuan
        @if ($items->count() > 0)
            <span class="ap-count">{{ $items->count() }} menunggu</span>
        @endif
    </h1>
    <p class="subtitle">Klik salah satu pengajuan untuk melihat detail, foto, dan alur persetujuan. Persetujuan dilakukan berurutan.</p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    @if ($items->isEmpty())
        <div class="card ap-empty">
            <div class="ic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
            </div>
            <b>Tidak ada penggantian yang menunggu persetujuan.</b>
            <p class="hint" style="margin:6px 0 0;">Pengajuan baru dari PIC akan muncul di sini.</p>
        </div>
    @else
        {{-- ===================== DAFTAR ===================== --}}
        <div class="card card-flush">
            <div class="toolbar">
                <input type="text" id="apSearch" placeholder="Cari nama mold, part no, lokasi, atau PIC">
            </div>

            <div class="table-wrap" style="overflow-x:auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Diajukan</th>
                            <th>Mold</th>
                            <th>Lokasi penggantian</th>
                            <th>Tanggal penggantian</th>
                            <th>Diajukan oleh</th>
                            <th>Jatuh tempo baru</th>
                            <th>Giliran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $h)
                            @php
                                $mold    = $h->mold;
                                $mine    = $h->approvals->firstWhere('approver_id', auth()->id());
                                $blocker = $h->approvals->where('level', '<', $mine->level)->first(fn ($a) => $a->status !== 'approved');
                                $search  = strtolower(implode(' ', [
                                    $mold->part_name, implode(' ', $mold->part_no_list), $h->replacement_location, $h->changer->name ?? '',
                                ]));
                            @endphp
                            <tr class="ap-row js-open-detail" data-target="detail-{{ $h->id }}" data-search="{{ $search }}">
                                <td class="nowrap">{{ $h->created_at?->format('d/m/Y') ?? '-' }}<br><small>{{ $h->created_at?->format('H:i') }} WIB</small></td>
                                <td>
                                    <div class="code">{{ $mold->part_name ?? '-' }}</div>
                                    <small>{{ $mold->category ?? '' }}{{ $mold->type ? ' · ' . $mold->type : '' }}</small>
                                </td>
                                <td>{{ $h->replacement_location ?: '-' }}</td>
                                <td class="nowrap">{{ $h->replacement_date->format('d/m/Y') }}</td>
                                <td>{{ $h->changer->name ?? '-' }}</td>
                                <td class="nowrap">{{ $h->new_due_date->format('d/m/Y') }}</td>
                                <td>
                                    @if ($blocker)
                                        <span class="ap-turn wait">Menunggu {{ $blocker->approver->name ?? 'level ' . $blocker->level }}</span>
                                    @else
                                        <span class="ap-turn me">Giliranmu</span>
                                    @endif
                                </td>
                                <td><button type="button" class="ap-open">Lihat detail</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="ap-empty-search" id="apNoResult">Tidak ada pengajuan yang cocok.</div>
            </div>

            <div class="table-foot">
                <span>Menampilkan {{ $items->count() }} pengajuan yang menunggu</span>
            </div>
        </div>

        {{-- ===================== DETAIL (satu dialog per pengajuan) ===================== --}}
        @foreach ($items as $h)
            @php
                $mold    = $h->mold;
                $mine    = $h->approvals->firstWhere('approver_id', auth()->id());
                $blocker = $h->approvals->where('level', '<', $mine->level)->first(fn ($a) => $a->status !== 'approved');
                $chain   = $h->approvals->sortBy('level');

                $unitLabel = strtolower(\App\Models\Mold::UNITS[$mold->frequency_unit] ?? 'Tahun');

                // Status mold setelah disetujui (aturan sama dengan Mold::getStatusAttribute)
                $newDue = $h->new_due_date->copy()->startOfDay();
                $lead   = (int) $mold->lead_time_value;
                $warnAt = match ($mold->lead_time_unit) {
                    'Day'   => $newDue->copy()->subDays($lead),
                    'Year'  => $newDue->copy()->subYearsNoOverflow($lead),
                    default => $newDue->copy()->subMonthsNoOverflow($lead),
                };
                $today    = now()->startOfDay();
                $afterKey = $today->gt($newDue) ? 'expired' : ($today->gte($warnAt) ? 'warning' : 'normal');
                $afterLbl = ['expired' => 'Expired', 'warning' => 'Warning', 'normal' => 'Normal'][$afterKey];

                $shift = (int) $h->old_due_date->diffInDays($h->new_due_date, false);
            @endphp

            <dialog id="detail-{{ $h->id }}" class="ap-dialog ap-detail-dialog">
                <div class="ap-detail-head">
                    <div>
                        <h2>{{ $mold->part_name ?? '-' }}</h2>
                        <div class="ap-sub">
                            Diajukan oleh <b>{{ $h->changer->name ?? '-' }}</b>
                            pada {{ $h->created_at?->format('d/m/Y H:i') ?? '-' }} WIB
                        </div>
                        <div class="ap-chips">
                            @if ($mold->category) <span class="ap-chip">{{ $mold->category }}</span> @endif
                            @if ($mold->type) <span class="ap-chip">{{ $mold->type }}</span> @endif
                            @if (count($mold->part_no_list)) <span class="ap-chip">Part no: {{ implode(', ', $mold->part_no_list) }}</span> @endif
                        </div>
                    </div>
                    <div class="ap-head-right">
                        <span class="badge badge-pending">Menunggu Approval</span>
                        <button type="button" class="ap-close js-close-detail" aria-label="Tutup">×</button>
                    </div>
                </div>

                <div class="ap-section">
                    <h3 class="ap-title">Data penggantian</h3>
                    <div class="ap-grid">
                        <div class="ap-field"><span>Tanggal penggantian</span><b>{{ $h->replacement_date->format('d/m/Y') }}</b></div>
                        <div class="ap-field"><span>Lokasi penggantian</span><b>{{ $h->replacement_location ?: '-' }}</b></div>
                        <div class="ap-field"><span>Frekuensi</span><b>Setiap {{ $h->frequency_value }} {{ $unitLabel }}</b></div>
                        <div class="ap-field"><span>Lokasi mold saat ini</span><b>{{ $mold->location ?: '-' }}</b></div>
                        <div class="ap-field"><span>Supplier</span><b>{{ $mold->supplier ?: '-' }}</b></div>
                        <div class="ap-field"><span>Kebutuhan</span><b>{{ $mold->kebutuhan !== null ? $mold->kebutuhan . ' pcs' : '-' }}</b></div>
                        <div class="ap-field"><span>Lead time</span><b>{{ $mold->lead_time_label }}</b></div>
                        <div class="ap-field"><span>Penggantian terakhir (sebelumnya)</span><b>{{ $mold->last_replaced_date?->format('d/m/Y') ?? '-' }}</b></div>
                    </div>
                </div>

                <div class="ap-section">
                    <h3 class="ap-title">Perubahan jatuh tempo</h3>
                    <div class="ap-compare">
                        <div class="ap-date-box">
                            <span>Jatuh tempo lama</span>
                            <b>{{ $h->old_due_date->format('d/m/Y') }}</b>
                            <small><span class="badge badge-{{ $mold->status }}">{{ $mold->status_label }}</span></small>
                        </div>
                        <div class="ap-arrow">→</div>
                        <div class="ap-date-box new">
                            <span>Jatuh tempo baru</span>
                            <b>{{ $h->new_due_date->format('d/m/Y') }}</b>
                            <small><span class="badge badge-{{ $afterKey }}">{{ $afterLbl }}</span> setelah disetujui</small>
                        </div>
                    </div>
                    <div class="ap-shift">Bergeser {{ $shift >= 0 ? '+' : '' }}{{ number_format($shift, 0, ',', '.') }} hari dari jatuh tempo lama. Reminder email direset setelah disetujui.</div>
                </div>

                <div class="ap-section">
                    <h3 class="ap-title">Foto penggantian</h3>
                    <div class="ap-photos">
                        @foreach (['before_photo' => 'Foto sebelum', 'after_photo' => 'Foto sesudah'] as $col => $caption)
                            <figure class="ap-photo">
                                <figcaption>{{ $caption }}</figcaption>
                                @if ($h->$col)
                                    <button type="button" class="js-zoom"
                                            data-src="{{ asset('storage/' . $h->$col) }}"
                                            data-caption="{{ $caption }} · {{ $mold->part_name }}">
                                        {{-- src diisi saat dialog dibuka, supaya halaman tidak memuat semua foto sekaligus --}}
                                        <img data-src="{{ asset('storage/' . $h->$col) }}" alt="{{ $caption }}">
                                        <span class="zoom">Perbesar</span>
                                    </button>
                                @else
                                    <div class="ap-photo-empty">Tidak ada foto</div>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                </div>

                @if ($h->notes)
                    <div class="ap-section">
                        <div class="ap-note"><b>Catatan dari PIC</b>{{ $h->notes }}</div>
                    </div>
                @endif

                <div class="ap-section">
                    <h3 class="ap-title">Alur persetujuan</h3>
                    <div class="ap-timeline">
                        @foreach ($chain as $a)
                            @php
                                $isMe = $a->approver_id === auth()->id();
                                $dot  = $a->status === 'approved' ? 'done' : ($a->status === 'rejected' ? 'bad' : ($a->level > $mine->level ? 'wait' : ''));

                                if ($a->status === 'approved') {
                                    $stateClass = 'ok';
                                    $stateText  = 'Disetujui ' . $a->acted_at?->format('d/m/Y H:i') . ' WIB';
                                } elseif ($a->status === 'rejected') {
                                    $stateClass = 'bad';
                                    $stateText  = 'Ditolak ' . $a->acted_at?->format('d/m/Y H:i') . ' WIB' . ($a->note ? ': ' . $a->note : '');
                                } elseif ($a->level < $mine->level) {
                                    $stateClass = '';
                                    $stateText  = 'Menunggu';
                                } elseif ($isMe) {
                                    $stateClass = '';
                                    $stateText  = 'Menunggu keputusanmu';
                                } else {
                                    $stateClass = '';
                                    $stateText  = 'Menunggu giliran';
                                }
                            @endphp
                            <div class="ap-step">
                                <span class="ap-dot {{ $dot }}">{{ $a->level }}</span>
                                <div>
                                    <div class="ap-step-name">{{ $a->approver->name ?? '-' }}{{ $isMe ? ' (kamu)' : '' }}</div>
                                    <div class="ap-step-state {{ $stateClass }}">{{ $stateText }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="ap-foot">
                    @if ($blocker)
                        <div class="ap-waiting">Belum giliranmu. Menunggu persetujuan {{ $blocker->approver->name ?? 'Atasan level ' . $blocker->level }} terlebih dahulu.</div>
                    @else
                        <div class="ap-actions">
                            <form method="POST" action="{{ route('approvals.approve', $h) }}" class="js-approve-form" data-name="{{ $mold->part_name }}">
                                @csrf
                                <button type="submit" class="ap-btn approve">Setujui</button>
                            </form>
                            <button type="button" class="ap-btn reject js-reject-btn"
                                    data-id="{{ $h->id }}"
                                    data-action="{{ route('approvals.reject', $h) }}"
                                    data-name="{{ $mold->part_name }}">Tolak</button>
                        </div>
                    @endif
                </div>
            </dialog>
        @endforeach
    @endif

    {{-- Dialog setujui --}}
    <dialog id="approveDialog" class="ap-dialog">
        <div class="ap-dialog-body">
            <div class="ap-dialog-icon green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"/></svg>
            </div>
            <h3>Setujui penggantian?</h3>
            <p>Jatuh tempo mold akan diperbarui (jika kamu approver terakhir) dan reminder email direset.</p>
            <span class="ap-dialog-name" id="approveName"></span>
        </div>
        <div class="ap-dialog-actions">
            <button type="button" id="approveCancel">Batal</button>
            <button type="button" id="approveOk" class="go-green">Ya, setujui</button>
        </div>
    </dialog>

    {{-- Dialog tolak --}}
    <dialog id="rejectDialog" class="ap-dialog">
        <form method="POST" id="rejectForm" action="">
            @csrf
            <input type="hidden" name="_rid" id="rejectRid">
            <div class="ap-dialog-body">
                <div class="ap-dialog-icon red">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
                </div>
                <h3>Tolak penggantian?</h3>
                <p>Data mold tidak akan berubah. Alasan penolakan akan terlihat oleh PIC.</p>
                <span class="ap-dialog-name" id="rejectName"></span>
                <textarea name="reject_reason" id="rejectReason" placeholder="Tulis alasan penolakan (wajib, minimal 3 karakter)" required maxlength="500"></textarea>
                @error('reject_reason') <div class="err">{{ $message }}</div> @enderror
            </div>
            <div class="ap-dialog-actions">
                <button type="button" id="rejectCancel">Batal</button>
                <button type="submit" id="rejectOk" class="go-red">Kirim penolakan</button>
            </div>
        </form>
    </dialog>

    {{-- Pratinjau foto --}}
    <dialog id="zoomDialog" class="ap-dialog ap-lightbox">
        <div class="ap-lightbox-bar"><span id="zoomCaption"></span><button type="button" id="zoomClose">Tutup</button></div>
        <img id="zoomImg" src="" alt="">
    </dialog>

    <script>
        (function () {
            function backdropClose(dialog) {
                dialog.addEventListener('click', function (e) {
                    const r = dialog.getBoundingClientRect();
                    const inside = e.clientX >= r.left && e.clientX <= r.right &&
                                   e.clientY >= r.top  && e.clientY <= r.bottom;
                    if (!inside) dialog.close();
                });
            }

            // ----- Buka detail dari daftar -----
            function openDetail(id) {
                const dialog = document.getElementById(id);
                if (!dialog) return;
                dialog.querySelectorAll('img[data-src]').forEach(function (img) {
                    if (!img.src) img.src = img.dataset.src;
                });
                dialog.showModal();
                dialog.scrollTop = 0;
            }

            document.querySelectorAll('.js-open-detail').forEach(function (row) {
                row.addEventListener('click', function () { openDetail(row.dataset.target); });
            });
            document.querySelectorAll('.ap-detail-dialog').forEach(function (d) {
                backdropClose(d);
                d.querySelector('.js-close-detail').addEventListener('click', function () { d.close(); });
            });

            // ----- Pencarian di daftar -----
            const search = document.getElementById('apSearch');
            if (search) {
                const rows = document.querySelectorAll('.js-open-detail');
                const none = document.getElementById('apNoResult');
                search.addEventListener('input', function () {
                    const q = search.value.trim().toLowerCase();
                    let shown = 0;
                    rows.forEach(function (row) {
                        const ok = !q || row.dataset.search.indexOf(q) !== -1;
                        row.style.display = ok ? '' : 'none';
                        if (ok) shown++;
                    });
                    none.style.display = shown === 0 ? 'block' : 'none';
                });
            }

            // ----- Setujui -----
            const approveDialog = document.getElementById('approveDialog');
            const approveName   = document.getElementById('approveName');
            const approveOk     = document.getElementById('approveOk');
            let approveForm = null;

            document.querySelectorAll('.js-approve-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    approveForm = form;
                    approveName.textContent = form.dataset.name;
                    approveDialog.showModal();
                });
            });
            approveOk.addEventListener('click', function () {
                if (!approveForm) return;
                approveOk.disabled = true;
                approveOk.textContent = 'Memproses...';
                approveForm.submit();
            });
            document.getElementById('approveCancel').addEventListener('click', function () { approveDialog.close(); });
            approveDialog.addEventListener('close', function () { approveForm = null; });
            backdropClose(approveDialog);

            // ----- Tolak -----
            const rejectDialog = document.getElementById('rejectDialog');
            const rejectForm   = document.getElementById('rejectForm');
            const rejectName   = document.getElementById('rejectName');
            const rejectRid    = document.getElementById('rejectRid');
            const rejectReason = document.getElementById('rejectReason');
            const rejectOk     = document.getElementById('rejectOk');

            function openReject(btn, reason) {
                rejectForm.action = btn.dataset.action;
                rejectRid.value = btn.dataset.id;
                rejectName.textContent = btn.dataset.name;
                rejectReason.value = reason || '';
                rejectDialog.showModal();
                rejectReason.focus();
            }

            document.querySelectorAll('.js-reject-btn').forEach(function (btn) {
                btn.addEventListener('click', function () { openReject(btn, ''); });
            });
            rejectForm.addEventListener('submit', function () {
                rejectOk.disabled = true;
                rejectOk.textContent = 'Mengirim...';
            });
            document.getElementById('rejectCancel').addEventListener('click', function () { rejectDialog.close(); });
            backdropClose(rejectDialog);

            // Kalau validasi alasan gagal, buka lagi dialog detail dan dialog tolak dengan isian sebelumnya
            @if ($errors->has('reject_reason') && old('_rid'))
                const failedBtn = document.querySelector('.js-reject-btn[data-id="{{ old('_rid') }}"]');
                if (failedBtn) {
                    openDetail('detail-{{ old('_rid') }}');
                    openReject(failedBtn, @json(old('reject_reason')));
                }
            @endif

            // ----- Pratinjau foto -----
            const zoomDialog = document.getElementById('zoomDialog');
            document.querySelectorAll('.js-zoom').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.getElementById('zoomImg').src = btn.dataset.src;
                    document.getElementById('zoomCaption').textContent = btn.dataset.caption;
                    zoomDialog.showModal();
                });
            });
            document.getElementById('zoomClose').addEventListener('click', function () { zoomDialog.close(); });
            backdropClose(zoomDialog);
        })();
    </script>
@endsection 