<?php

namespace App\Http\Controllers;

use App\Exports\MoldsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MoldExportController extends Controller
{
    public function __invoke(Request $request)
    {
        $export = new MoldsExport(
            $request->query('q'),
            $request->query('kategori'),
            $request->query('status')
        );

        return Excel::download($export, 'daftar-mold-' . now()->format('Y-m-d') . '.xlsx');
    }
}