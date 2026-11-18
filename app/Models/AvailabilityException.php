<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AvailabilityException extends Model
{
    protected $fillable = [
        'user_id', 'date', 'is_closed', 'start_time', 'end_time', 'reason',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_closed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
