<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MoldTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'part_name',
            'part_no',
            'kategori',
            'spec',
            'tipe',
            'lokasi',
            'lokasi_per_tahun_expired',
            'kebutuhan',
            'supplier',
            'installed_by',
            'frekuensi_nilai',
            'frekuensi_satuan',
            'lead_time_nilai',
            'lead_time_satuan',
            'penggantian_terakhir',
        ];
    }

    public function array(): array
    {
        // Template kosong: hanya header, tanpa baris contoh
        return [];
    }
}