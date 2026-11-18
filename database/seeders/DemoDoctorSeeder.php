<?php

namespace Database\Seeders;

use App\Models\AvailabilityRule;
use App\Models\User;
use App\Services\DoctorSetupService;
use Illuminate\Database\Seeder;

class DemoDoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctor = User::updateOrCreate(
            ['email' => 'doctor@ardental.local'],
            [
                'name' => 'Dr. Rahman',
                'phone' => '+8801700000000',
                'password' => 'password',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'clinic_name' => 'AR Dental Care',
                'specialty' => 'Oral and Dental Surgeon',
                'qualifications' => 'B.D.S (D.U), P.G.T (Oral and Maxillofacial Surgery)',
                'bmdc_reg' => 'BMDC-1001',
                'clinic_address' => 'Bypass Road, Dhaka, Bangladesh',
                'clinic_phone' => '+8801700000000',
                'visiting_hours' => '10:00 AM – 2:00 PM & 4:00 PM – 8:00 PM',
                'appointment_slug' => 'ar-dental-care',
            ]
        );

        AvailabilityRule::where('user_id', $doctor->id)->delete();
        app(DoctorSetupService::class)->seedDefaultAvailability($doctor);
    }
}
