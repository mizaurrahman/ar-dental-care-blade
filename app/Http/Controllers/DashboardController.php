<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $selectedDate = $request->query('date', now()->toDateString());
        $day = Carbon::parse($selectedDate)->startOfDay();

        $stats = [
            'total_appointments' => Appointment::where('user_id', $user->id)->count(),
            'today_appointments' => Appointment::where('user_id', $user->id)->whereDate('starts_at', $day)->count(),
            'prescriptions' => Prescription::where('user_id', $user->id)->count(),
            'new_patients' => Patient::where('user_id', $user->id)->where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $upcoming = Appointment::with('patient')
            ->where('user_id', $user->id)
            ->where('starts_at', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('starts_at')
            ->limit(8)
            ->get();

        $patientsToday = Appointment::with('patient')
            ->where('user_id', $user->id)
            ->whereDate('starts_at', $day)
            ->orderBy('starts_at')
            ->get();

        $weekStart = now()->startOfWeek(Carbon::SUNDAY);
        $weekEnd = $weekStart->copy()->addWeek();
        $appointments = Appointment::query()
            ->where('user_id', $user->id)
            ->where('starts_at', '>=', $weekStart)
            ->where('starts_at', '<', $weekEnd)
            ->get(['starts_at']);

        $weekly = [];
        foreach ($appointments as $appointment) {
            $dow = (string) Carbon::parse($appointment->starts_at)->dayOfWeek;
            $weekly[$dow] = ($weekly[$dow] ?? 0) + 1;
        }

        $chart = [];
        for ($i = 0; $i < 7; $i++) {
            $chart[] = [
                'day' => $weekStart->copy()->addDays($i)->format('D'),
                'total' => (int) ($weekly[(string) $i] ?? 0),
            ];
        }

        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');

        return view('doctor.dashboard', compact(
            'user',
            'stats',
            'upcoming',
            'patientsToday',
            'chart',
            'selectedDate',
            'greeting',
        ));
    }
}
