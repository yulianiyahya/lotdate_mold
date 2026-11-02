<?php

namespace App\Http\Controllers;

use App\Models\ReplacementApproval;
use App\Models\ReplacementHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    public function index()
    {
        $this->guard();

        $items = ReplacementHistory::with(['mold', 'changer', 'approvals.approver'])
            ->where('status', 'pending')
            ->whereHas('approvals', function ($q) {
                $q->where('approver_id', auth()->id())->where('status', 'pending');
            })
            ->orderBy('created_at')
            ->get();

        return view('approvals.index', ['items' => $items]);
    }

    public function approve(ReplacementHistory $replacement)
    {
        $this->guard();

        $result = DB::transaction(function () use ($replacement) {
            $history = ReplacementHistory::with('mold')->lockForUpdate()->findOrFail($replacement->id);

            if ($history->status !== 'pending') {
                return ['error', 'Penggantian ini sudah diproses.'];
            }

            $found = $this->resolveTurn($history);
            if (is_string($found)) {
                return ['error', $found];
            }

            [$mine, $approvals] = $found;

            $mine->update([
                'status'   => 'approved',
                'acted_at' => now(),
            ]);

            // $mine adalah objek yang sama dengan di $approvals, jadi statusnya sudah ikut berubah.
            $remaining = $approvals->where('status', 'pending')->count();

            if ($remaining > 0) {
                return ['success', 'Disetujui. Menunggu persetujuan Atasan berikutnya.'];
            }

            $history->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $history->mold->update([
                'last_replaced_date' => $history->replacement_date,
                'next_due_date'      => $history->new_due_date,
                'frequency_value'    => $history->frequency_value,
                'reminder_sent_at'   => null,
            ]);

            return ['success', 'Penggantian ' . $history->mold->part_number . ' disetujui. Jatuh tempo mold diperbarui ke '
                . $history->new_due_date->format('d/m/Y') . ' dan reminder direset.'];
        });

        return redirect()->route('approvals.index')->with($result[0], $result[1]);
    }

    public function reject(Request $request, ReplacementHistory $replacement)
    {
        $this->guard();

        $data = $request->validate([
            'reject_reason' => ['required', 'string', 'min:3', 'max:500'],
        ], [
            'required' => ':attribute wajib diisi.',
            'min'      => ':attribute minimal :min karakter.',
            'max'      => ':attribute maksimal :max karakter.',
        ], [
            'reject_reason' => 'Alasan penolakan',
        ]);

        $result = DB::transaction(function () use ($replacement, $data) {
            $history = ReplacementHistory::with('mold')->lockForUpdate()->findOrFail($replacement->id);

            if ($history->status !== 'pending') {
                return ['error', 'Penggantian ini sudah diproses.'];
            }

            $found = $this->resolveTurn($history);
            if (is_string($found)) {
                return ['error', $found];
            }

            [$mine] = $found;

            $mine->update([
                'status'   => 'rejected',
                'note'     => $data['reject_reason'],
                'acted_at' => now(),
            ]);

            $history->update([
                'status'        => 'rejected',
                'reject_reason' => $data['reject_reason'],
            ]);

            return ['success', 'Penggantian ' . $history->mold->part_number . ' ditolak. Data mold tidak berubah.'];
        });

        return redirect()->route('approvals.index')->with($result[0], $result[1]);
    }

    /**
     * Pastikan user ini punya persetujuan pending dan gilirannya sudah tiba.
     * Mengembalikan [approval milik user, semua approval] atau string pesan error.
     */
    private function resolveTurn(ReplacementHistory $history): array|string
    {
        $approvals = $history->approvals()->orderBy('level')->lockForUpdate()->get();

        $mine = $approvals->first(function ($a) {
            return (int) $a->approver_id === (int) auth()->id() && $a->status === 'pending';
        });

        if (! $mine) {
            return 'Kamu tidak punya persetujuan yang menunggu untuk penggantian ini.';
        }

        $blocked = $approvals->where('level', '<', $mine->level)->contains(function ($a) {
            return $a->status !== 'approved';
        });

        if ($blocked) {
            return 'Belum giliranmu. Menunggu persetujuan Atasan level sebelumnya.';
        }

        return [$mine, $approvals];
    }

    private function guard(): void
    {
        abort_unless(auth()->user()->role === 'Atasan', 403);
    }
}