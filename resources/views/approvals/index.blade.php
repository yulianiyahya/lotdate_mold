@extends('layouts.app')

@section('title', 'Persetujuan')

@section('content')
    <h1 class="title">Persetujuan</h1>
    <p class="subtitle">Periksa foto dan data penggantian, lalu setujui atau tolak. Persetujuan dilakukan berurutan.</p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert">{{ $errors->first() }}</div>
    @endif

    @forelse ($items as $h)
        @php
            $mine    = $h->approvals->firstWhere('approver_id', auth()->id());
            $blocker = $h->approvals->where('level', '<', $mine->level)->first(fn ($a) => $a->status !== 'approved');
            $chain   = $h->approvals->sortBy('level');
        @endphp

        <div class="card approval-card">
            <div class="approval-head">
                <div>
                    <h2>{{ $h->mold->part_number }}</h2>
                    <div class="hint">{{ $h->mold->part_name ?? '-' }}</div>
                </div>
                <span class="badge badge-pending">Menunggu Approval</span>
            </div>

            <div class="approval-meta">
                <div><span>Jenis mold</span><b>{{ $h->mold->type ?? '-' }}</b></div>
                <div><span>Diajukan oleh</span><b>{{ $h->changer->name ?? '-' }}</b></div>
                <div><span>Tanggal penggantian</span><b>{{ $h->replacement_date->format('d/m/Y') }}</b></div>
                <div><span>Frekuensi</span><b>Setiap {{ $h->frequency_value }} tahun</b></div>
                <div><span>Jatuh tempo lama</span><b>{{ $h->old_due_date->format('d/m/Y') }}</b></div>
                <div><span>Jatuh tempo baru</span><b>{{ $h->new_due_date->format('d/m/Y') }}</b></div>
            </div>

            <div class="photos">
                @foreach (['before_photo' => 'Foto sebelum', 'after_photo' => 'Foto sesudah'] as $col => $caption)
                    <figure>
                        <figcaption>{{ $caption }}</figcaption>
                        @if ($h->$col)
                            <a href="{{ asset('storage/' . $h->$col) }}" target="_blank">
                                <img src="{{ asset('storage/' . $h->$col) }}" alt="{{ $caption }}">
                            </a>
                        @else
                            <div class="photo-empty">Tidak ada foto</div>
                        @endif
                    </figure>
                @endforeach
            </div>

            @if ($h->notes)
                <div class="notes-box"><b>Catatan PIC:</b> {{ $h->notes }}</div>
            @endif

            <div class="chain">
                @foreach ($chain as $a)
                    <div class="approver">
                        <span class="step {{ $a->status === 'approved' ? 'step-done' : ($a->level > $mine->level || $a->status !== 'pending' ? 'step-wait' : '') }}">{{ $a->level }}</span>
                        <div>
                            {{ $a->approver->name ?? '-' }}{{ $a->approver_id === auth()->id() ? ' (kamu)' : '' }}
                            <div class="state {{ $a->status === 'approved' ? 'ok' : '' }}">
                                @if ($a->status === 'approved')
                                    Disetujui {{ $a->acted_at?->format('d/m/Y H:i') }}
                                @else
                                    Menunggu
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($blocker)
                <div class="waiting">Belum giliranmu. Menunggu persetujuan {{ $blocker->approver->name ?? 'Atasan level ' . $blocker->level }} terlebih dahulu.</div>
            @else
                <div class="action-row">
                    <form method="POST" action="{{ route('approvals.approve', $h) }}"
                          onsubmit="return confirm('Setujui penggantian {{ $h->mold->part_number }}?');">
                        @csrf
                        <button type="submit" class="btn btn-approve">Setujui</button>
                    </form>

                    <details class="reject-box" {{ $errors->has('reject_reason') && old('_rid') == $h->id ? 'open' : '' }}>
                        <summary class="btn btn-reject">Tolak</summary>
                        <form method="POST" action="{{ route('approvals.reject', $h) }}">
                            @csrf
                            <input type="hidden" name="_rid" value="{{ $h->id }}">
                            <textarea name="reject_reason" placeholder="Tulis alasan penolakan (wajib)" required>{{ old('_rid') == $h->id ? old('reject_reason') : '' }}</textarea>
                            <button type="submit" class="btn btn-reject">Kirim penolakan</button>
                        </form>
                    </details>
                </div>
            @endif
        </div>
    @empty
        <div class="card" style="padding:40px; text-align:center;">
            <b>Tidak ada penggantian yang menunggu persetujuan.</b>
            <p class="hint" style="margin:6px 0 0;">Pengajuan baru dari PIC akan muncul di sini.</p>
        </div>
    @endforelse
@endsection