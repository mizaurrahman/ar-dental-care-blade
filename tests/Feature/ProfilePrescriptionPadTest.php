<?php

namespace Tests\Feature;

use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Services\DoctorSetupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePrescriptionPadTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_shows_pad_preview_and_branding_controls(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($doctor)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Prescription pad preview')
            ->assertSee('Upload clinic logo')
            ->assertSee('Save prescription pad info')
            ->assertSee($doctor->clinic_name)
            ->assertSee($doctor->name);
    }

    public function test_updating_profile_updates_prescription_pad_content(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($doctor)
            ->put('/profile', [
                'name' => 'Dr. Updated',
                'email' => $doctor->email,
                'phone' => '1700000000',
                'clinic_name' => 'Smile Bright Dental',
                'specialty' => 'Prosthodontist',
                'qualifications' => 'BDS, FCPS',
                'bmdc_reg' => 'BMDC-9999',
                'clinic_address' => 'Gulshan, Dhaka',
                'clinic_phone' => '1711111111',
                'visiting_hours' => '9 AM – 5 PM',
                'appointment_slug' => $doctor->appointment_slug,
            ])
            ->assertRedirect();

        $doctor->refresh();
        $this->assertSame('Smile Bright Dental', $doctor->clinic_name);
        $this->assertSame('Prosthodontist', $doctor->specialty);

        $patient = Patient::create([
            'user_id' => $doctor->id,
            'name' => 'Karim Ahmed',
            'phone' => '+8801722223333',
        ]);
        $prescription = Prescription::create([
            'user_id' => $doctor->id,
            'patient_id' => $patient->id,
            'prescribed_on' => now()->toDateString(),
            'rx_text' => 'Tab. Napa',
            'status' => 'final',
            'share_token' => 'profile-pad-token-123456789012345',
        ]);

        $this->get('/rx/'.$prescription->share_token)
            ->assertOk()
            ->assertSee('Dr. Updated')
            ->assertSee('Smile Bright Dental')
            ->assertSee('Prosthodontist')
            ->assertSee('BDS, FCPS')
            ->assertSee('BMDC-9999')
            ->assertSee('Gulshan, Dhaka')
            ->assertSee('9 AM – 5 PM');

        $this->actingAs($doctor)
            ->get('/prescriptions/new')
            ->assertOk()
            ->assertSee('Dr. Updated')
            ->assertSee('Smile Bright Dental')
            ->assertSee('Prosthodontist');
    }

    public function test_doctor_can_upload_and_remove_clinic_logo(): void
    {
        Storage::fake('public');
        $doctor = $this->makeDoctor();

        $file = UploadedFile::fake()->image('clinic-logo.png', 200, 200);

        $this->actingAs($doctor)
            ->post('/profile/logo', ['logo' => $file])
            ->assertRedirect();

        $doctor->refresh();
        $this->assertNotNull($doctor->logo_path);
        Storage::disk('public')->assertExists($doctor->logo_path);

        $this->actingAs($doctor)
            ->get('/prescriptions/new')
            ->assertOk()
            ->assertSee($doctor->logo_url);

        $this->actingAs($doctor)
            ->delete('/profile/logo')
            ->assertRedirect();

        $doctor->refresh();
        $this->assertNull($doctor->logo_path);
    }

    public function test_doctor_can_upload_and_remove_avatar(): void
    {
        Storage::fake('public');
        $doctor = $this->makeDoctor();

        $file = UploadedFile::fake()->image('doctor-photo.jpg', 240, 240);

        $this->actingAs($doctor)
            ->post('/profile/avatar', ['avatar' => $file])
            ->assertRedirect();

        $doctor->refresh();
        $this->assertNotNull($doctor->avatar_path);
        Storage::disk('public')->assertExists($doctor->avatar_path);
        $this->assertStringContainsString('/storage/', (string) $doctor->avatar_url);

        $this->actingAs($doctor)
            ->delete('/profile/avatar')
            ->assertRedirect();

        $doctor->refresh();
        $this->assertNull($doctor->avatar_path);
        $this->assertNull($doctor->avatar_url);
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
            'qualifications' => 'B.D.S (D.U)',
            'bmdc_reg' => 'BMDC-1001',
            'clinic_address' => 'Bypass Road, Dhaka',
            'clinic_phone' => '+8801700000000',
            'visiting_hours' => '10:00 AM – 8:00 PM',
            'appointment_slug' => 'ar-dental-care',
        ], $overrides));

        app(DoctorSetupService::class)->seedDefaultAvailability($doctor);

        return $doctor;
    }
}
