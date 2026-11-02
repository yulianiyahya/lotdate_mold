<?php

namespace App\Exports;

use App\Models\ReplacementHistory;
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

class ReplacementsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    private Collection $items;
    private int $no = 0;

    private const STATUS = [
        'pending'  => ['Menunggu Approval', 'FFF1BD'],
        'approved' => ['Disetujui',         'CDEBD5'],
        'rejected' => ['Ditolak',           'F8C9CD'],
    ];

    public function __construct(?string $q = null, ?string $jenis = null, ?string $tahun = null)
    {
        // Filter sama persis dengan HistoryController@index
        $query = ReplacementHistory::with(['mold', 'changer']);

        if ($q) {
            $query->whereHas('mold', function ($m) use ($q) {
                $m->where(function ($w) use ($q) {
                    $w->where('part_name', 'like', '%' . $q . '%')
                      ->orWhere('part_no', 'like', '%' . $q . '%');
                });
            });
        }

        if ($jenis) {
            $query->whereHas('mold', fn ($m) => $m->where('category', $jenis));
        }

        if ($tahun) {
            $query->whereYear('replacement_date', $tahun);
        }

        $this->items = $query->orderByDesc('replacement_date')
            ->orderByDesc('id')
            ->get();
    }

    public function collection(): Collection
    {
        return $this->items;
    }

    public function title(): string
    {
        return 'Riwayat Penggantian';
    }

    public function headings(): array
    {
        return [
            'No', 'Tanggal', 'Part name', 'Part no', 'Jenis', 'PIC',
            'Jatuh tempo berikutnya', 'Foto sebelum', 'Foto sesudah', 'Status',
        ];
    }

    public function map($h): array
    {
        $mold = $h->mold;
        $partNo = $mold ? (implode(', ', $mold->part_no_list ?? []) ?: '-') : '-';

        return [
            ++$this->no,
            $h->replacement_date?->format('d/m/Y') ?? '-',
            $mold->part_name ?? '-',
            $partNo,
            $mold->category ?? '-',
            $h->changer->name ?? '-',
            $h->new_due_date?->format('d/m/Y') ?? '-',
            $h->before_photo ? asset('storage/' . $h->before_photo) : '-',
            $h->after_photo ? asset('storage/' . $h->after_photo) : '-',
            self::STATUS[$h->status][0] ?? $h->status,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max($this->items->count() + 1, 1);

        // Header merah
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E60012']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getStyle("A1:J{$lastRow}")->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('DDDDDD');

        $sheet->getStyle("A2:J{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->freezePane('A2');

        // Warna sel status
        foreach ($this->items->values() as $i => $h) {
            $sheet->getStyle('J' . ($i + 2))->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => self::STATUS[$h->status][1] ?? 'FFFFFF'],
                ],
            ]);
        }

        return [];
    }
}