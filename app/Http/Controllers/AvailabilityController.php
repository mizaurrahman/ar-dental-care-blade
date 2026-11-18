<?php

namespace App\Http\Controllers;

use App\Models\AvailabilityException;
use App\Models\AvailabilityRule;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $rules = $request->user()->availabilityRules()->orderBy('day_of_week')->orderBy('start_time')->get();
        $exceptions = $request->user()->availabilityExceptions()->orderBy('date')->get();

        return view('doctor.appointment-config', compact('rules', 'exceptions'));
    }

    public function storeRule(Request $request)
    {
        $data = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_minutes' => ['nullable', 'integer', 'min:10', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $request->user()->availabilityRules()->create([
            ...$data,
            'slot_minutes' => $data['slot_minutes'] ?? 30,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return back()->with('status', 'Hours added.');
    }

    public function destroyRule(Request $request, AvailabilityRule $rule)
    {
        abort_unless($rule->user_id === $request->user()->id, 403);
        $rule->delete();

        return back()->with('status', 'Hours removed.');
    }

    public function storeException(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'is_closed' => ['nullable', 'boolean'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'reason' => ['nullable', 'string', 'max:190'],
        ]);

        $request->user()->availabilityExceptions()->updateOrCreate(
            ['date' => $data['date']],
            [
                'is_closed' => $data['is_closed'] ?? true,
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'reason' => $data['reason'] ?? null,
            ]
        );

        return back()->with('status', 'Exception saved.');
    }

    public function destroyException(Request $request, AvailabilityException $exception)
    {
        abort_unless($exception->user_id === $request->user()->id, 403);
        $exception->delete();

        return back()->with('status', 'Exception removed.');
    }
}
