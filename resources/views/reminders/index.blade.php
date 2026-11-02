@extends('layouts.app')

@section('title', 'Reminder Email')

@section('content')
    <h1 class="title">Reminder Email</h1>
    <p class="subtitle">Mold berstatus Warning atau Expired. Reminder otomatis dikirim setiap hari pukul 07:00, satu kali per siklus.</p>

    @if (session('success'))
        <div class="alert alert-success">{!! nl2br(e(session('success'))) !!}</div>
    @endif
    @if (session('error'))
        <div class="alert">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('reminders.send') }}" style="margin-bottom:16px;"
          onsubmit="return confirm('Kirim reminder sekarang untuk semua mold yang belum dikirimi?');">
        @csrf
        <button type="submit" class="btn">Kirim reminder sekarang</button>
    </form>

    <div class="card" style="padding:0; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse; font-size:14px;">
            <thead>
                <tr style="background:#f8fafc; text-align:left;">
                    <th style="padding:12px 16px;">Part Number</th>
                    <th style="padding:12px 16px;">PIC</th>
                    <th style="padding:12px 16px;">Jatuh Tempo</th>
                    <th style="padding:12px 16px;">Status</th>
                    <th style="padding:12px 16px;">Reminder</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($molds as $m)
                    <tr style="border-top:1px solid #e2e6ec;">
                        <td style="padding:12px 16px;"><b>{{ $m->part_number }}</b><div class="hint">{{ $m->part_name ?? '-' }}</div></td>
                        <td style="padding:12px 16px;">{{ $m->pic->name ?? '-' }}</td>
                        <td style="padding:12px 16px;">{{ $m->next_due_date->format('d/m/Y') }}</td>
                        <td style="padding:12px 16px;">{{ $m->status_label }}</td>
                        <td style="padding:12px 16px;">
                            @if ($m->reminder_sent_at)
                                Terkirim {{ $m->reminder_sent_at->format('d/m/Y H:i') }}
                            @else
                                Belum dikirim
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:40px; text-align:center;">Tidak ada mold berstatus Warning atau Expired.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection