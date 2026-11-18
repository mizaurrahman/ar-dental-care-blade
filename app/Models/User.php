<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'phone', 'password',
        'email_verified_at', 'phone_verified_at',
        'clinic_name', 'specialty', 'qualifications', 'bmdc_reg',
        'clinic_address', 'clinic_phone', 'visiting_hours',
        'avatar_path', 'logo_path', 'appointment_slug',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = ['appointment_url', 'avatar_url', 'logo_url', 'is_verified'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getIsVerifiedAttribute(): bool
    {
        return ($this->email && $this->email_verified_at)
            || ($this->phone && $this->phone_verified_at);
    }

    public function getAppointmentUrlAttribute(): ?string
    {
        if (!$this->appointment_slug) {
            return null;
        }

        $frontend = rtrim(config('app.url', 'http://127.0.0.1:8001'), '/');

        return $frontend.'/appointment/'.$this->appointment_slug;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path ? url('storage/'.$this->avatar_path) : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? url('storage/'.$this->logo_path) : null;
    }

    public function availabilityRules(): HasMany
    {
        return $this->hasMany(AvailabilityRule::class);
    }

    public function availabilityExceptions(): HasMany
    {
        return $this->hasMany(AvailabilityException::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }
}
