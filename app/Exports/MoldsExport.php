<?php

namespace App\Exports;

use App\Models\Mold;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MoldsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    private Collection $molds;
    private int $no = 0;

    public function __construct(?string $q = null, ?string $kategori = null, ?string $status = null)
    {
        $this->molds = Mold::query()
            ->where('is_active', true)
            ->when($q, function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('part_name', 'like', '%' . $q . '%')
                      ->orWhere('part_no', 'like', '%' . $q . '%');
                });
            })
            ->when($kategori, fn ($query) => $query->where('category', $kategori))
            ->orderBy('next_due_date')
            ->get()
            // Status dihitung dari accessor Mold::status, sama persis dengan Daftar Mold.
            ->when($status, fn ($c) => $c->filter(fn ($m) => $m->status === $status)->values());
    }

    public function collection(): Collection
    {
        return $this->molds;
    }

    public function title(): string
    {
        return 'Daftar Mold';
    }

    public function headings(): array
    {
        return [
            'No', 'Part name', 'Part no', 'Kategori', 'Tipe', 'Lokasi', 'Kebutuhan (pcs)',
            'Supplier', 'Frekuensi', 'Lead time', 'Penggantian terakhir',
            'Jatuh tempo', 'Sisa waktu', 'Status',
        ];
    }

    public function map($mold): array
    {
        $days = (int) now()->startOfDay()->diffInDays($mold->next_due_date, false);

        return [
            ++$this->no,
            $mold->part_name,
            implode(', ', $mold->part_no_list) ?: '-',
            $mold->category ?: '-',
            $mold->type ?: '-',
            $mold->location ?: '-',
            $mold->kebutuhan ?? '-',
            $mold->supplier ?: '-',
            'Setiap ' . $mold->frequency_label,
            $mold->lead_time_label,
            $mold->last_replaced_date?->format('d/m/Y') ?? '-',
            $mold->next_due_date->format('d/m/Y'),
            $days < 0 ? 'Terlambat ' . abs($days) . ' hari' : $days . ' hari lagi',
            $mold->status_label,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max($this->molds->count() + 1, 1);

        // Header merah
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E60012']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle("A1:N{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('DDDDDD');

        $sheet->getStyle("A2:N{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->freezePane('A2');

        // Warna sel status
        $colors = ['expired' => 'F8C9CD', 'warning' => 'FFF1BD', 'normal' => 'CDEBD5'];
        foreach ($this->molds->values() as $i => $mold) {
            $sheet->getStyle('N' . ($i + 2))->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $colors[$mold->status] ?? 'FFFFFF']],
            ]);
        }

        return [];
    }
}