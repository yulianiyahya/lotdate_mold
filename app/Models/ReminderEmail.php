<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderEmail extends Model
{
    protected $table = 'reminder_emails';

    protected $fillable = [
        'name',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}