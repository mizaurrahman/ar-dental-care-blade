<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Support\BdPhone;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PrescriptionService
{
    public function create(User $user, array $data): Prescription
    {
        return DB::transaction(function () use ($user, $data) {
            if (! empty($data['patient_id'])) {
                $patient = Patient::where('user_id', $user->id)->findOrFail($data['patient_id']);
            } else {
                $phone = BdPhone::normalize($data['patient']['phone'] ?? '');
                if (! $phone) {
                    throw ValidationException::withMessages([
                        'patientPhone' => 'Invalid phone number.',
                        'patient.phone' => 'Invalid phone number.',
                    ]);
                }

                $patient = $user->patients()->create([
                    'name' => $data['patient']['name'],
                    'phone' => $phone,
                    'email' => $data['patient']['email'] ?? null,
                    'age' => $data['patient']['age'] ?? ($data['patient_age'] ?? null),
                ]);
            }

            $prescription = $user->prescriptions()->create([
                'patient_id' => $patient->id,
                'appointment_id' => $data['appointment_id'] ?? null,
                'prescribed_on' => $data['prescribed_on'] ?? now()->toDateString(),
                'patient_age' => $data['patient_age'] ?? $patient->age,
                'chief_complaints' => $data['chief_complaints'] ?? null,
                'on_examination' => $data['on_examination'] ?? null,
                'advice' => $data['advice'] ?? null,
                'rx_text' => $data['rx_text'] ?? null,
                'follow_up_days' => $data['follow_up_days'] ?? null,
                'status' => 'final',
                'share_token' => Str::random(40),
            ]);

            $this->syncItems($prescription, $data['items'] ?? []);

            return $prescription->load(['patient', 'items', 'user']);
        });
    }

    public function update(User $user, Prescription $prescription, array $data): Prescription
    {
        abort_unless($prescription->user_id === $user->id, 403);

        DB::transaction(function () use ($prescription, $data) {
            $prescription->update(collect($data)->only([
                'prescribed_on',
                'patient_age',
                'chief_complaints',
                'on_examination',
                'advice',
                'rx_text',
                'follow_up_days',
            ])->all());

            if (array_key_exists('items', $data)) {
                $this->syncItems($prescription, $data['items']);
            }
        });

        return $prescription->fresh()->load(['patient', 'items', 'user']);
    }

    public function shareEmail(User $user, Prescription $prescription, ?string $email = null): string
    {
        abort_unless($prescription->user_id === $user->id, 403);
        $prescription->loadMissing('patient');

        $email = $email ?: $prescription->patient?->email;
        if (! $email) {
            throw ValidationException::withMessages(['email' => 'Patient email is required.']);
        }

        $url = $prescription->share_url;
        Mail::raw(
            "Your prescription from {$user->clinic_name} is ready.\nView it here: {$url}",
            function ($message) use ($email, $user) {
                $message->to($email)->subject('Your prescription — '.$user->clinic_name);
            }
        );

        return $url;
    }

    private function syncItems(Prescription $prescription, array $items): void
    {
        $prescription->items()->delete();

        foreach (array_values($items) as $index => $item) {
            $medicineId = $item['medicine_id'] ?? null;
            if ($medicineId && ! Medicine::whereKey($medicineId)->exists()) {
                $medicineId = null;
            }

            $prescription->items()->create([
                'medicine_id' => $medicineId ?: null,
                'brand_name' => $item['brand_name'],
                'strength' => $item['strength'] ?? null,
                'form' => $item['form'] ?? null,
                'dosage' => $item['dosage'] ?? null,
                'frequency' => $item['frequency'] ?? null,
                'duration' => $item['duration'] ?? null,
                'instructions' => $item['instructions'] ?? null,
                'sort_order' => $index,
            ]);
        }
    }
}
