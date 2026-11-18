<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\DoctorSetupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_allows_public_booking_and_points_to_sitemap(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Allow: /')
            ->assertSee('Allow: /appointment/')
            ->assertSee('Disallow: /dashboard')
            ->assertSee('Disallow: /rx/')
            ->assertSee('Sitemap: '.url('/sitemap.xml'));
    }

    public function test_sitemap_includes_verified_clinic_booking_pages(): void
    {
        $doctor = $this->makeDoctor();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(route('home'), false)
            ->assertSee(route('appointment.public', $doctor->appointment_slug), false)
            ->assertSee(route('appointment.book', $doctor->appointment_slug), false)
            ->assertDontSee(url('/login'), false)
            ->assertSee('<urlset', false);
    }

    public function test_public_booking_page_has_seo_meta_and_structured_data(): void
    {
        $doctor = $this->makeDoctor([
            'clinic_name' => 'Smile Bright Dental',
            'specialty' => 'Orthodontist',
            'clinic_address' => 'Gulshan, Dhaka',
        ]);

        $this->get('/appointment/'.$doctor->appointment_slug)
            ->assertOk()
            ->assertSee('<meta name="description"', false)
            ->assertSee('content="index,follow"', false)
            ->assertSee('rel="canonical"', false)
            ->assertSee('og:title', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('Smile Bright Dental')
            ->assertSee('Orthodontist')
            ->assertSee('"@type":"Dentist"', false);
    }

    public function test_private_doctor_pages_are_noindex(): void
    {
        $doctor = $this->makeDoctor();

        $this->actingAs($doctor)
            ->get('/dashboard')
            ->assertOk()
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
