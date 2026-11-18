<?php

namespace App\Http\Controllers;

use App\Models\Prescription;

class PublicPrescriptionController extends Controller
{
    public function show(string $token)
    {
        $prescription = Prescription::with(['items', 'patient', 'user'])
            ->where('share_token', $token)
            ->firstOrFail();

        $doctor = [
            'name' => $prescription->user->name,
            'clinic_name' => $prescription->user->clinic_name,
            'specialty' => $prescription->user->specialty,
            'qualifications' => $prescription->user->qualifications,
            'bmdc_reg' => $prescription->user->bmdc_reg,
            'clinic_address' => $prescription->user->clinic_address,
            'clinic_phone' => $prescription->user->clinic_phone,
            'visiting_hours' => $prescription->user->visiting_hours,
            'logo_url' => $prescription->user->logo_url,
        ];

        return view('public.prescription', compact('prescription', 'doctor'));
    }
}
