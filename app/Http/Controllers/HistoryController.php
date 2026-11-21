<?php

namespace App\Http\Controllers;

use App\Models\Mold;
use App\Models\ReplacementHistory;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = ReplacementHistory::with(['mold', 'changer', 'approvals.approver']);

        // Cari berdasarkan nama part, nomor part, atau lokasi penggantian
        if ($request->filled('q')) {
            $keyword = $request->q;

            $query->where(function ($outer) use ($keyword) {
                $outer->whereHas('mold', function ($m) use ($keyword) {
                    $m->where(function ($w) use ($keyword) {
                        $w->where('part_name', 'like', '%' . $keyword . '%')
                          ->orWhere('part_no', 'like', '%' . $keyword . '%');
                    });
                })->orWhere('replacement_location', 'like', '%' . $keyword . '%');
            });
        }

        // Filter jenis = category mold (Blow / Inject)
        if ($request->filled('jenis')) {
            $query->whereHas('mold', function ($m) use ($request) {
                $m->where('category', $request->jenis);
            });
        }

        // Filter tahun dari tanggal penggantian
        if ($request->filled('tahun')) {
            $query->whereYear('replacement_date', $request->tahun);
        }

        $items = $query->orderByDesc('replacement_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Opsi jenis diambil dari data mold yang ada, jadi selalu sama dengan Daftar Mold
        $types = Mold::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $years = ReplacementHistory::query()
            ->selectRaw('YEAR(replacement_date) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y');

        return view('history.index', [
            'items' => $items,
            'types' => $types,
            'years' => $years,
        ]);
    }
}