<?php

namespace Tests\Feature;

use App\Livewire\PrescriptionEditor;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Services\DoctorSetupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PrescriptionRxTextTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_can_save_prescription_with_text_only(): void
    {
        $doctor = $this->makeDoctor();

        Livewire::actingAs($doctor)
            ->test(PrescriptionEditor::class)
            ->set('patientName', 'Karim Ahmed')
            ->set('patientPhone', '1722223333')
            ->set('rxText', "1. Cap. Amoxicillin 500mg\n1+0+1 × 5 days")
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('prescriptions', [
            'user_id' => $doctor->id,
            'rx_text' => "1. Cap. Amoxicillin 500mg\n1+0+1 × 5 days",
        ]);

        $prescription = Prescription::where('user_id', $doctor->id)->first();
        $this->assertNotNull($prescription);
        $this->assertCount(0, $prescription->items);
    }

    public function test_prescription_requires_text_or_medicine(): void
    {
        $doctor = $this->makeDoctor();

        Livewire::actingAs($doctor)
            ->test(PrescriptionEditor::class)
            ->set('patientName', 'Karim Ahmed')
            ->set('patientPhone', '1722223333')
            ->set('rxText', '')
            ->set('items', [])
            ->call('save')
            ->assertHasErrors(['rxText']);
    }

    public function test_public_rx_shows_free_text(): void
    {
        $doctor = $this->makeDoctor();
        $patient = Patient::create([
            'user_id' => $doctor->id,
            'name' => 'Karim Ahmed',
            'phone' => '+8801722223333',
        ]);
        $prescription = Prescription::create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'prescribed_on' => now()->toDateString(),
            'rx_text' => 'Tab. Napa 500mg — 1+0+1 for 3 days',
            'status' => 'final',
            'share_token' => 'rx-text-token-12345678901234567890',
        ]);

        $this->get('/rx/'.$prescription->share_token)
            ->assertOk()
            ->assertSee('Tab. Napa 500mg — 1+0+1 for 3 days');
    }

    private function makeDoctor(array $overrides = []): User
    {
        $doctor = User::create(array_merge([
            'name' => 'Dr. Rahman',
            'email' => 'doctor@ardental.local',
            'phone' => '+8801700000000',
            'password' => 'password',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'clinic_name' => 'AR Dental Care',
            'specialty' => 'Oral and Dental Surgeon',
            'appointment_slug' => 'ar-dental-care',
        ], $overrides));

        app(DoctorSetupService::class)->seedDefaultAvailability($doctor);

        return $doctor;
    }
}
