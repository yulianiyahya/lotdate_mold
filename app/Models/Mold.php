<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mold extends Model
{
    protected $table = 'molds';

    public const UNITS = ['Day' => 'Hari', 'Month' => 'Bulan', 'Year' => 'Tahun'];

    protected $fillable = [
        'part_name', 'part_no', 'category', 'type', 'location',
        'installed_by', 'supplier', 'kebutuhan', 'pic_id',
        'frequency_value', 'frequency_unit',
        'lead_time_value', 'lead_time_unit',
        'last_replaced_date', 'next_due_date',
        'reminder_sent_at', 'is_active',
    ];

    protected $casts = [
        'last_replaced_date' => 'date',
        'next_due_date'      => 'date',
        'reminder_sent_at'   => 'datetime',
        'is_active'          => 'boolean',
        'kebutuhan'          => 'integer',
    ];

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function replacements(): HasMany
    {
        return $this->hasMany(ReplacementHistory::class, 'mold_id');
    }

    /**
     * Year  : 31 Desember pada (tahun penggantian + frekuensi), aturan lama tetap.
     * Month : tambah bulan dari tanggal penggantian.
     * Day   : tambah hari dari tanggal penggantian.
     */
    public static function calculateDueDate($replacedDate, int $value, string $unit = 'Year'): Carbon
    {
        $date = Carbon::parse($replacedDate)->startOfDay();

        return match ($unit) {
            'Day'   => $date->addDays($value),
            'Month' => $date->addMonthsNoOverflow($value),
            default => Carbon::create($date->year + $value, 12, 31)->startOfDay(),
        };
    }

    /** Tanggal mulai masa warning = jatuh tempo dikurangi lead time. */
    public function getWarningStartAttribute(): Carbon
    {
        $due = $this->next_due_date->copy()->startOfDay();
        $n   = (int) $this->lead_time_value;

        return match ($this->lead_time_unit) {
            'Day'   => $due->subDays($n),
            'Year'  => $due->subYearsNoOverflow($n),
            default => $due->subMonthsNoOverflow($n),
        };
    }

    public function getStatusAttribute(): string
    {
        $today = Carbon::today();

        if ($today->gt($this->next_due_date->copy()->startOfDay())) {
            return 'expired';
        }

        return $today->gte($this->warning_start) ? 'warning' : 'normal';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'expired' => 'Expired',
            'warning' => 'Warning',
            default   => 'Normal',
        };
    }

    public function getFrequencyLabelAttribute(): string
    {
        return $this->frequency_value . ' ' . self::UNITS[$this->frequency_unit];
    }

    public function getLeadTimeLabelAttribute(): string
    {
        return $this->lead_time_value . ' ' . self::UNITS[$this->lead_time_unit];
    }

    /** part_no bisa berisi beberapa nomor, dipisah koma / titik koma / baris baru. */
    public function getPartNoListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/[,;\n]+/', (string) $this->part_no))));
    }

    /** Ekspresi SQL tanggal mulai warning, untuk filter status di query. */
    public static function warningStartSql(): string
    {
        return "CASE lead_time_unit
                    WHEN 'Day'  THEN DATE_SUB(next_due_date, INTERVAL lead_time_value DAY)
                    WHEN 'Year' THEN DATE_SUB(next_due_date, INTERVAL lead_time_value YEAR)
                    ELSE DATE_SUB(next_due_date, INTERVAL lead_time_value MONTH)
                END";
    }
}