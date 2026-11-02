<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReplacementApproval extends Model
{
    protected $table = 'replacement_approvals';

    const UPDATED_AT = null;

    protected $fillable = [
        'replacement_id',
        'approver_id',
        'level',
        'status',
        'note',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    public function replacement(): BelongsTo
    {
        return $this->belongsTo(ReplacementHistory::class, 'replacement_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}