<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    protected $fillable = [
        'user_id', 'patient_id', 'appointment_id', 'prescribed_on', 'patient_age',
        'chief_complaints', 'on_examination', 'advice', 'rx_text', 'follow_up_days',
        'status', 'share_token',
    ];

    protected $appends = ['share_url'];

    protected function casts(): array
    {
        return [
            'prescribed_on' => 'date',
        ];
    }

    public function getShareUrlAttribute(): string
    {
        $frontend = rtrim(config('app.url', 'http://127.0.0.1:8001'), '/');

        return $frontend.'/rx/'.$this->share_token;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class)->orderBy('sort_order');
    }
}
