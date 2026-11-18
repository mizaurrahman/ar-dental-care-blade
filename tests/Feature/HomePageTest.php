<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DoctorSetupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_public_and_links_to_login(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('AR Dental Care')
            ->assertSee('width=device-width', false)
            ->assertSee('viewport-fit=cover', false)
            ->assertSee(route('login'), false)
            ->assertSee('>Login<', false)
            ->assertSee('home-nav-cta', false)
            ->assertSee('home-cta', false)
            ->assertSee('content="index,follow"', false)
            ->assertSee('home-hero', false);
    }

    public function test_authenticated_users_see_dashboard_cta_on_home(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($doctor)
            ->get('/')
            ->assertOk()
            ->assertSee(route('dashboard'), false)
            ->assertSee('Open dashboard');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
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
