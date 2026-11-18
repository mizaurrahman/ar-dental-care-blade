<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DoctorSetupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobilePublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_include_mobile_viewport_and_responsive_shell(): void
    {
        $doctor = $this->makeDoctor();

        $booking = $this->get('/appointment/'.$doctor->appointment_slug)->assertOk();
        $booking->assertSee('width=device-width', false);
        $booking->assertSee('viewport-fit=cover', false);
        $booking->assertSee('theme-color', false);
        $booking->assertSee('Select date');
        $booking->assertSee('sm:grid-cols', false);

        $home = $this->get('/')->assertOk();
        $home->assertSee('width=device-width', false);
        $home->assertSee('home-hero', false);
        $home->assertSee('min-h-[100dvh]', false);
        $home->assertSee(route('login'), false);

        $login = $this->get('/login')->assertOk();
        $login->assertSee('width=device-width', false);
        $login->assertSee('min-h-screen');
        $login->assertSee('content="noindex,nofollow"', false);
    }

    public function test_doctor_shell_keeps_mobile_tabbar_markup(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($doctor)
            ->get('/prescriptions')
            ->assertOk()
            ->assertSee('mobile-tabbar', false)
            ->assertSee('md:hidden', false)
            ->assertSee('Prescriptions')
            ->assertSee('content="noindex,nofollow"', false);
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
