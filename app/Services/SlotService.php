<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AvailabilityException;
use App\Models\AvailabilityRule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SlotService
{
    public function slotsForDate(User $doctor, Carbon $date): array
    {
        $exception = AvailabilityException::query()
            ->where('user_id', $doctor->id)
            ->whereDate('date', $date->toDateString())
            ->first();

        if ($exception?->is_closed) {
            return [];
        }

        $windows = [];

        if ($exception && $exception->start_time && $exception->end_time) {
            $windows[] = [
                'start' => $exception->start_time,
                'end' => $exception->end_time,
                'slot_minutes' => 30,
            ];
        } else {
            $rules = AvailabilityRule::query()
                ->where('user_id', $doctor->id)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('is_active', true)
                ->get();

            foreach ($rules as $rule) {
                $windows[] = [
                    'start' => $rule->start_time,
                    'end' => $rule->end_time,
                    'slot_minutes' => $rule->slot_minutes,
                ];
            }
        }

        // Only scheduled appointments block a slot (cancelled/no_show free it again)
        $existingAppointments = Appointment::query()
            ->where('user_id', $doctor->id)
            ->whereDate('starts_at', $date->toDateString())
            ->where('status', 'scheduled')
            ->get(['starts_at', 'ends_at']);

        $slots = [];
        foreach ($windows as $window) {
            $startRaw = substr((string) $window['start'], 0, 5);
            $endRaw = substr((string) $window['end'], 0, 5);
            $cursor = Carbon::parse($date->toDateString().' '.$startRaw);
            $end = Carbon::parse($date->toDateString().' '.$endRaw);
            $minutes = max(5, (int) $window['slot_minutes']);

            while ($cursor->copy()->addMinutes($minutes)->lte($end)) {
                $slotEnd = $cursor->copy()->addMinutes($minutes);
                if ($cursor->lt(now())) {
                    $cursor->addMinutes($minutes);
                    continue;
                }

                $overlap = $existingAppointments->contains(function ($appt) use ($cursor, $slotEnd) {
                    return $cursor->lt($appt->ends_at) && $slotEnd->gt($appt->starts_at);
                });

                if (!$overlap) {
                    $slots[] = [
                        'starts_at' => $cursor->format('Y-m-d H:i:s'),
                        'ends_at' => $slotEnd->format('Y-m-d H:i:s'),
                        'label' => $cursor->format('h:i A'),
                    ];
                }

                $cursor->addMinutes($minutes);
            }
        }

        return $slots;
    }


    public function slotsForRange(User $doctor, Carbon $from, Carbon $to): Collection
    {
        $result = collect();
        $day = $from->copy()->startOfDay();
        while ($day->lte($to)) {
            $result->put($day->toDateString(), $this->slotsForDate($doctor, $day->copy()));
            $day->addDay();
        }

        return $result;
    }
}
