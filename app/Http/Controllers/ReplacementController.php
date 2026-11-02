<?php

namespace App\Http\Controllers;

use App\Models\Mold;
use App\Models\ReplacementApproval;
use App\Models\ReplacementHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReplacementController extends Controller
{
    /** Batas atas frekuensi per satuan. */
    private const MAX_FREQUENCY = ['Day' => 3650, 'Month' => 120, 'Year' => 30];

    public function create(Mold $mold)
    {
        $this->guard($mold);

        if ($this->hasPending($mold)) {
            return redirect()->route('molds.index')
                ->with('error', 'Mold ' . $mold->part_name . ' masih punya penggantian yang menunggu persetujuan.');
        }

        return view('replacements.create', [
            'mold'         => $mold,
            'approvers'    => $this->approvers(),
            'maxFrequency' => self::MAX_FREQUENCY[$mold->frequency_unit] ?? 30,
        ]);
    }

    public function store(Request $request, Mold $mold)
    {
        $this->guard($mold);

        if ($this->hasPending($mold)) {
            return redirect()->route('molds.index')
                ->with('error', 'Mold ' . $mold->part_name . ' masih punya penggantian yang menunggu persetujuan.');
        }

        $approvers = $this->approvers();

        if ($approvers->isEmpty()) {
            return back()->withInput()
                ->with('error', 'Belum ada akun Atasan aktif yang bisa menyetujui. Hubungi admin.');
        }

        $unit         = $mold->frequency_unit ?: 'Year';
        $maxFrequency = self::MAX_FREQUENCY[$unit] ?? 30;
        $photoRule    = ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'];

        $dateRule = ['required', 'date', 'before_or_equal:today'];
        if ($mold->last_replaced_date) {
            $dateRule[] = 'after_or_equal:' . $mold->last_replaced_date->toDateString();
        }

        $messages = [
            'required'                         => ':attribute wajib diisi.',
            'date'                             => ':attribute tidak valid.',
            'integer'                          => ':attribute harus berupa angka bulat.',
            'image'                            => ':attribute harus berupa gambar.',
            'mimes'                            => ':attribute harus JPG atau PNG.',
            'before_photo.max'                 => 'Ukuran foto sebelum maksimal 4 MB.',
            'after_photo.max'                  => 'Ukuran foto sesudah maksimal 4 MB.',
            'frequency_value.between'          => 'Frekuensi penggantian harus antara 1 dan ' . $maxFrequency . ' ' . strtolower(Mold::UNITS[$unit] ?? 'tahun') . '.',
            'replacement_date.before_or_equal' => 'Tanggal penggantian tidak boleh melewati hari ini.',
        ];

        if ($mold->last_replaced_date) {
            $messages['replacement_date.after_or_equal'] =
                'Tanggal penggantian tidak boleh sebelum penggantian terakhir (' . $mold->last_replaced_date->format('d/m/Y') . ').';
        }

        $data = $request->validate([
            'replacement_date' => $dateRule,
            'frequency_value'  => ['required', 'integer', 'between:1,' . $maxFrequency],
            'before_photo'     => $photoRule,
            'after_photo'      => $photoRule,
            'notes'            => ['nullable', 'string', 'max:1000'],
        ], $messages, [
            'replacement_date' => 'Tanggal penggantian',
            'frequency_value'  => 'Frekuensi penggantian',
            'before_photo'     => 'Foto sebelum',
            'after_photo'      => 'Foto sesudah',
        ]);

        $beforePath = $request->file('before_photo')->store('replacements', 'public');
        $afterPath  = $request->file('after_photo')->store('replacements', 'public');

        DB::transaction(function () use ($data, $mold, $approvers, $beforePath, $afterPath, $unit) {
            $history = ReplacementHistory::create([
                'mold_id'          => $mold->id,
                'old_due_date'     => $mold->next_due_date,
                // Satuan mengikuti mold (Day / Month / Year), bukan selalu tahun.
                'new_due_date'     => Mold::calculateDueDate($data['replacement_date'], (int) $data['frequency_value'], $unit),
                'replacement_date' => $data['replacement_date'],
                'frequency_value'  => $data['frequency_value'],
                'before_photo'     => $beforePath,
                'after_photo'      => $afterPath,
                'notes'            => $data['notes'] ?? null,
                'status'           => 'pending',
                'changed_by'       => auth()->id(),
            ]);

            foreach ($approvers->values() as $i => $approver) {
                ReplacementApproval::create([
                    'replacement_id' => $history->id,
                    'approver_id'    => $approver->id,
                    'level'          => $i + 1,
                    'status'         => 'pending',
                ]);
            }
        });

        return redirect()->route('history.index')
            ->with('success', 'Penggantian ' . $mold->part_name . ' tersimpan dan menunggu persetujuan.');
    }

    private function guard(Mold $mold): void
    {
        abort_unless(auth()->user()->isPic(), 403);
        abort_unless($mold->is_active, 404);
    }

    private function hasPending(Mold $mold): bool
    {
        return $mold->replacements()->where('status', 'pending')->exists();
    }

    private function approvers()
    {
        return User::where('role', 'Atasan')
            ->where('is_active', true)
            ->whereNotNull('approval_level')
            ->orderBy('approval_level')
            ->get();
    }
}