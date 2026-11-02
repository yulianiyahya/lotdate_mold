<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReplacementHistory extends Model
{
    protected $table = 'replacement_history';

    const UPDATED_AT = null;

    protected $fillable = [
        'mold_id',
        'old_due_date',
        'new_due_date',
        'replacement_date',
        'frequency_value',
        'before_photo',
        'after_photo',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'reject_reason',
        'changed_by',
    ];

    protected $casts = [
        'old_due_date'     => 'date',
        'new_due_date'     => 'date',
        'replacement_date' => 'date',
        'approved_at'      => 'datetime',
    ];

    public function mold(): BelongsTo
    {
        return $this->belongsTo(Mold::class, 'mold_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(ReplacementApproval::class, 'replacement_id');
    }
}