<?php

namespace App\Http\Controllers;

use App\Models\Mold;
use App\Models\ReplacementHistory;

class DashboardController extends Controller
{
    public function index()
    {
        // Mold aktif, sama seperti Daftar Mold. Status dihitung lewat accessor Mold::status.
        $molds = Mold::where('is_active', true)
            ->orderBy('next_due_date')
            ->get();

        $counts = [
            'total'   => $molds->count(),
            'expired' => $molds->where('status', 'expired')->count(),
            'warning' => $molds->where('status', 'warning')->count(),
            'normal'  => $molds->where('status', 'normal')->count(),
        ];

        $pendingApprovals = ReplacementHistory::where('status', 'pending')->count();

        // Mold paling dekat jatuh tempo (yang sudah expired otomatis di urutan atas)
        $upcoming = $molds->take(8);

        return view('dashboard', compact('counts', 'pendingApprovals', 'upcoming'));
    }
}