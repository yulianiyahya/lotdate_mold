<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderLog extends Model
{
    protected $table = 'reminder_logs';

    public $timestamps = false;

    protected $fillable = [
        'mold_id',
        'email',
        'due_date',
        'sent_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'sent_at'  => 'datetime',
    ];

    public function mold(): BelongsTo
    {
        return $this->belongsTo(Mold::class, 'mold_id');
    }
}