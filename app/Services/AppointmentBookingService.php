<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Support\BdPhone;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AppointmentBookingService
{
    public function __construct(
        private SlotService $slots,
        private SmsService $sms,
    ) {}

    public function book(User $doctor, array $data): Appointment
    {
        $phone = BdPhone::normalize($data['phone'] ?? '');
        if (! $phone) {
            throw ValidationException::withMessages([
                'phone' => 'Enter a valid phone number.',
            ]);
        }

        $starts = Carbon::parse($data['starts_at']);
        $daySlots = $this->slots->slotsForDate($doctor, $starts->copy()->startOfDay());
        $match = collect($daySlots)->first(
            fn ($slot) => Carbon::parse($slot['starts_at'])->format('Y-m-d H:i') === $starts->format('Y-m-d H:i')
        );

        if (! $match) {
            throw ValidationException::withMessages([
                'starts_at' => 'Selected slot is no longer available.',
            ]);
        }

        $appointment = DB::transaction(function () use ($doctor, $data, $phone, $match, $starts) {
            $stillFree = collect($this->slots->slotsForDate($doctor, $starts->copy()->startOfDay()))
                ->contains(fn ($slot) => Carbon::parse($slot['starts_at'])->format('Y-m-d H:i') === $starts->format('Y-m-d H:i'));

            if (! $stillFree) {
                throw ValidationException::withMessages([
                    'starts_at' => 'This time was just taken. Please choose another available slot.',
                ]);
            }

            $taken = Appointment::query()
                ->where('user_id', $doctor->id)
                ->where('status', 'scheduled')
                ->where('starts_at', $match['starts_at'])
                ->lockForUpdate()
                ->exists();

            if ($taken) {
                throw ValidationException::withMessages([
                    'starts_at' => 'This time was just taken. Please choose another available slot.',
                ]);
            }

            $patient = $doctor->patients()->firstOrCreate(
                ['phone' => $phone],
                ['name' => $data['name'], 'email' => $data['email']]
            );
            $patient->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            return Appointment::create([
                'user_id' => $doctor->id,
                'patient_id' => $patient->id,
                'starts_at' => $match['starts_at'],
                'ends_at' => $match['ends_at'],
                'status' => 'scheduled',
                'reason' => $data['reason'] ?? null,
            ])->load('patient');
        });

        $when = Carbon::parse($appointment->starts_at)->format('D, M j Y g:i A');
        $msg = "Appointment confirmed at {$doctor->clinic_name} with {$doctor->name} on {$when}.";

        Mail::raw($msg, function ($message) use ($data, $doctor) {
            $message->to($data['email'])->subject('Appointment confirmed — '.$doctor->clinic_name);
        });

        if ($doctor->email) {
            Mail::raw(
                "New appointment: {$data['name']} ({$phone}) on {$when}",
                function ($message) use ($doctor) {
                    $message->to($doctor->email)->subject('New appointment — '.$doctor->clinic_name);
                }
            );
        }

        $this->sms->send($phone, $msg);

        return $appointment;
    }
}
