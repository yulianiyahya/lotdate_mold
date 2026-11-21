<?php

namespace App\Http\Controllers;

use App\Exports\MoldTemplateExport;
use App\Imports\MoldsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MoldImportController extends Controller
{
    public function create()
    {
        abort_unless(auth()->user()->isPic(), 403);

        return view('molds.import');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->isPic(), 403);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'File harus berformat .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        $import = new MoldsImport();
        Excel::import($import, $request->file('file'));

        return redirect()
            ->route('molds.import.create')
            ->with('success', "{$import->imported} mold berhasil diimpor, {$import->skipped} baris dilewati.")
            ->with('import_errors', $import->errors);
    }

    public function template()
    {
        abort_unless(auth()->user()->isPic(), 403);

        return Excel::download(new MoldTemplateExport(), 'template-import-mold.xlsx');
    }
}