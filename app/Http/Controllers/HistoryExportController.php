<?php

namespace App\Http\Controllers;

use App\Exports\ReplacementsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HistoryExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $export = new ReplacementsExport(
            $request->query('q'),
            $request->query('jenis'),
            $request->query('tahun')
        );

        return Excel::download($export, 'riwayat-penggantian-' . now()->format('Y-m-d') . '.xlsx');
    }
}