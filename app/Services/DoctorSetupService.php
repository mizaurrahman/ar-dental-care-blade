<?php

namespace App\Services;

use App\Models\AvailabilityRule;
use App\Models\User;
use Illuminate\Support\Str;

class DoctorSetupService
{
    public function uniqueAppointmentSlug(string $preferred): string
    {
        $base = Str::slug($preferred) ?: 'clinic';
        $slug = $base;
        $i = 1;
        while (User::where('appointment_slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    /**
     * Default weekly hours so a new doctor's public link works immediately.
     * Sat–Thu: 10:00–14:00 and 16:00–20:00 (common BD clinic pattern).
     */
    public function seedDefaultAvailability(User $doctor): void
    {
        if ($doctor->availabilityRules()->exists()) {
            return;
        }

        foreach ([0, 1, 2, 3, 4, 5, 6] as $day) {
            AvailabilityRule::create([
                'user_id' => $doctor->id,
                'day_of_week' => $day,
                'start_time' => '10:00',
                'end_time' => '14:00',
                'slot_minutes' => 30,
                'is_active' => true,
            ]);
            AvailabilityRule::create([
                'user_id' => $doctor->id,
                'day_of_week' => $day,
                'start_time' => '16:00',
                'end_time' => '20:00',
                'slot_minutes' => 30,
                'is_active' => true,
            ]);
        }
    }
}
