<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Services\DoctorSetupService;
use App\Services\SlotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ClinicWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_doctor_can_login(): void
    {
        $this->makeDoctor();

        $this->post('/login', [
            'login' => 'doctor@ardental.local',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function test_authenticated_doctor_pages_render(): void
    {
        $this->makeDoctor();

        $this->post('/login', [
            'login' => 'doctor@ardental.local',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->get('/')->assertOk()->assertSee('AR Dental Care');
        $this->get('/appointments')->assertOk()->assertSee('Appointments');
        $this->get('/appointment-config')->assertOk()->assertSee('Appointment configuration');
        $this->get('/profile')->assertOk()->assertSee('Profile');
        $this->get('/prescriptions')->assertOk()->assertSee('Prescriptions');
        $this->get('/prescriptions/new')->assertOk()->assertSee('New prescription');
    }

    public function test_public_appointment_page_loads(): void
    {
        $this->makeDoctor();

        $this->get('/appointment/ar-dental-care')
            ->assertOk()
            ->assertSee('AR Dental Care')
            ->assertSee('Select date');
    }

    public function test_booking_creates_appointment(): void
    {
        $doctor = $this->makeDoctor();
        $date = now()->addDays(2)->startOfDay();
        $slots = app(SlotService::class)->slotsForDate($doctor, $date);
        $this->assertNotEmpty($slots);
        $slot = $slots[0];

        $this->post('/appointment/'.$doctor->appointment_slug, [
            'name' => 'Karim Ahmed',
            'phone' => '01722223333',
            'email' => 'karim@example.com',
            'starts_at' => $slot['starts_at'],
            'reason' => 'Cleaning',
        ])->assertRedirect(route('appointment.public', $doctor->appointment_slug));

        $this->assertDatabaseHas('appointments', [
            'user_id' => $doctor->id,
            'reason' => 'Cleaning',
            'status' => 'scheduled',
            'starts_at' => $slot['starts_at'],
        ]);
        $this->assertDatabaseHas('patients', [
            'user_id' => $doctor->id,
            'phone' => '+8801722223333',
            'name' => 'Karim Ahmed',
        ]);
    }

    public function test_appointments_list_links_to_prefilled_prescription(): void
    {
        $doctor = $this->makeDoctor();
        $patient = Patient::create([
            'user_id' => $doctor->id,
            'name' => 'Md Mizanur Rahman',
            'phone' => '+8801715650787',
            'email' => 'mizan@example.com',
            'age' => 22,
        ]);
        $appointment = Appointment::create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'starts_at' => now()->addDay()->setTime(10, 0),
            'ends_at' => now()->addDay()->setTime(10, 30),
            'status' => 'scheduled',
            'reason' => 'tooth pain',
        ]);

        $this->actingAs($doctor)
            ->get('/appointments')
            ->assertOk()
            ->assertSee('Add prescription')
            ->assertSee(route('prescriptions.create', ['appointment' => $appointment->id], false));

        $this->actingAs($doctor)
            ->get('/prescriptions/new?appointment='.$appointment->id)
            ->assertOk()
            ->assertSee('Md Mizanur Rahman')
            ->assertSee('tooth pain')
            ->assertSee('1715650787');
    }

    public function test_doctor_cannot_open_prescription_from_another_doctors_appointment(): void
    {
        $owner = $this->makeDoctor([
            'email' => 'owner@example.com',
            'phone' => '+8801711111111',
            'appointment_slug' => 'owner-clinic',
        ]);
        $intruder = $this->makeDoctor([
            'name' => 'Intruder',
            'email' => 'intruder@example.com',
            'phone' => '+8801722222222',
            'appointment_slug' => 'intruder-clinic',
        ]);
        $patient = Patient::create([
            'user_id' => $owner->id,
            'name' => 'Secret Patient',
            'phone' => '+8801733333333',
        ]);
        $appointment = Appointment::create([
            'user_id' => $owner->id,
            'patient_id' => $patient->id,
            'starts_at' => now()->addDay()->setTime(11, 0),
            'ends_at' => now()->addDay()->setTime(11, 30),
            'status' => 'scheduled',
            'reason' => 'Checkup',
        ]);

        $this->actingAs($intruder)
            ->get('/prescriptions/new?appointment='.$appointment->id)
            ->assertForbidden();
    }

    public function test_doctor_cannot_update_another_doctors_prescription(): void
    {
        $owner = $this->makeDoctor([
            'email' => 'owner@example.com',
            'phone' => '+8801711111111',
            'appointment_slug' => 'owner-clinic',
        ]);
        $intruder = $this->makeDoctor([
            'name' => 'Intruder',
            'email' => 'intruder@example.com',
            'phone' => '+8801722222222',
            'appointment_slug' => 'intruder-clinic',
        ]);

        $patient = Patient::create([
            'user_id' => $owner->id,
            'name' => 'Secret Patient',
            'phone' => '+8801733333333',
        ]);

        $prescription = Prescription::create([
            'user_id' => $owner->id,
            'patient_id' => $patient->id,
            'prescribed_on' => now()->toDateString(),
            'chief_complaints' => 'Toothache',
            'status' => 'final',
            'share_token' => Str::random(40),
        ]);

        $this->actingAs($intruder)
            ->put('/prescriptions/'.$prescription->id, [
                'chief_complaints' => 'Stolen',
                'items' => [
                    ['brand_name' => 'Napa', 'dosage' => '1+0+1'],
                ],
            ])
            ->assertForbidden();

        $this->assertSame('Toothache', $prescription->fresh()->chief_complaints);
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
