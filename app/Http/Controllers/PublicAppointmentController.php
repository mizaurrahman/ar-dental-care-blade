<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AppointmentBookingService;
use Illuminate\Http\Request;

class PublicAppointmentController extends Controller
{
    public function show(string $slug)
    {
        $doctor = User::where('appointment_slug', $slug)->firstOrFail();

        return view('public.appointment', compact('slug', 'doctor'));
    }

    public function store(string $slug, Request $request, AppointmentBookingService $booking)
    {
        $doctor = User::where('appointment_slug', $slug)->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string'],
            'email' => ['required', 'email'],
            'starts_at' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $appointment = $booking->book($doctor, $data);

        return redirect()
            ->route('appointment.public', $slug)
            ->with('confirmed_at', $appointment->starts_at->toIso8601String())
            ->with('status', 'Appointment confirmed. Confirmation sent by email and SMS.');
    }
}
