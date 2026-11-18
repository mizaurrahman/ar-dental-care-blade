<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with('patient')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('starts_at');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('date')) {
            $query->whereDate('starts_at', $request->query('date'));
        }

        $appointments = $query->paginate(30)->withQueryString();

        return view('doctor.appointments', compact('appointments'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        abort_unless($appointment->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(['scheduled', 'completed', 'cancelled', 'no_show'])],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $appointment->update($data);

        return back()->with('status', 'Appointment updated.');
    }
}
