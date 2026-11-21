<?php

namespace App\Imports;

use App\Models\Mold;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class MoldsImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skipped = 0;
    public array $errors = [];

    private const UNITS = [
        'day' => 'Day', 'hari' => 'Day',
        'month' => 'Month', 'bulan' => 'Month',
        'year' => 'Year', 'tahun' => 'Year',
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $row) {
            $line = $i + 2; // baris 1 = header

            if (! $row->filter(fn ($v) => $v !== null && $v !== '')->count()) {
                continue;
            }

            // Lewati baris contoh dari template lama
            if (trim((string) ($row['spec'] ?? '')) === 'Spec contoh'
                && trim((string) ($row['supplier'] ?? '')) === 'PT Contoh') {
                continue;
            }

            $freqUnit = self::UNITS[strtolower(trim((string) ($row['frekuensi_satuan'] ?? '')))] ?? null;
            $leadUnit = self::UNITS[strtolower(trim((string) ($row['lead_time_satuan'] ?? '')))] ?? null;

            $data = [
                'part_name'               => trim((string) ($row['part_name'] ?? '')),
                'part_no'                 => trim((string) ($row['part_no'] ?? '')),
                'category'                => trim((string) ($row['kategori'] ?? '')),
                'spec'                    => trim((string) ($row['spec'] ?? '')),
                'type'                    => trim((string) ($row['tipe'] ?? '')),
                'location'                => trim((string) ($row['lokasi'] ?? '')),
                'location_yearly_expired' => trim((string) ($row['lokasi_per_tahun_expired'] ?? '')),
                'kebutuhan'               => $row['kebutuhan'] ?? null,
                'supplier'                => trim((string) ($row['supplier'] ?? '')),
                'installed_by'            => trim((string) ($row['installed_by'] ?? '')),
                'frequency_value'         => $row['frekuensi_nilai'] ?? null,
                'frequency_unit'          => $freqUnit,
                'lead_time_value'         => $row['lead_time_nilai'] ?? null,
                'lead_time_unit'          => $leadUnit,
                'last_replaced_date'      => $this->parseDate($row['penggantian_terakhir'] ?? null),
            ];

            $validator = Validator::make($data, [
                'part_name'          => 'required|string|max:150',
                'category'           => 'nullable|in:Blow,Inject',
                'kebutuhan'          => 'nullable|integer|min:0',
                'frequency_value'    => 'required|integer|min:1',
                'frequency_unit'     => 'required|in:Day,Month,Year',
                'lead_time_value'    => 'required|integer|min:1',
                'lead_time_unit'     => 'required|in:Day,Month,Year',
                'last_replaced_date' => 'required|date|before_or_equal:today',
            ], [
                'category.in'                 => 'kategori harus Blow atau Inject.',
                'frequency_value.required'    => 'frekuensi_nilai wajib diisi (angka).',
                'frequency_unit.required'     => 'frekuensi_satuan harus Hari/Bulan/Tahun.',
                'frequency_unit.in'           => 'frekuensi_satuan harus Hari/Bulan/Tahun.',
                'lead_time_value.required'    => 'lead_time_nilai wajib diisi (angka).',
                'lead_time_unit.required'     => 'lead_time_satuan harus Hari/Bulan/Tahun.',
                'lead_time_unit.in'           => 'lead_time_satuan harus Hari/Bulan/Tahun.',
                'last_replaced_date.required' => 'penggantian_terakhir wajib diisi (dd/mm/yyyy).',
                'last_replaced_date.before_or_equal' => 'penggantian_terakhir tidak boleh melewati hari ini.',
            ]);

            if ($validator->fails()) {
                $this->skipped++;
                $this->errors[] = "Baris {$line}: " . implode(' ', $validator->errors()->all());
                continue;
            }

            if (Mold::where('part_name', $data['part_name'])->where('is_active', true)->exists()) {
                $this->skipped++;
                $this->errors[] = "Baris {$line}: part name \"{$data['part_name']}\" sudah ada.";
                continue;
            }

            foreach ($data as $k => $v) {
                if ($v === '') {
                    $data[$k] = null;
                }
            }

            // Aturan jatuh tempo sama persis dengan form Tambah/Edit
            $data['next_due_date'] = Mold::calculateDueDate(
                $data['last_replaced_date'],
                (int) $data['frequency_value'],
                $data['frequency_unit']
            );

            $data['pic_id']    = auth()->id();
            $data['is_active'] = true;

            Mold::create($data);
            $this->imported++;
        }
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject($value))->format('Y-m-d');
            }

            return Carbon::createFromFormat('d/m/Y', trim($value))->format('Y-m-d');
        } catch (\Throwable $e) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Throwable $e2) {
                return null;
            }
        }
    }
}