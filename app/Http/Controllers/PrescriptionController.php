<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Prescription;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $prescriptions = Prescription::with(['patient', 'items'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('prescribed_on')
            ->paginate(20);

        return view('doctor.prescriptions.index', compact('prescriptions'));
    }

    public function create(Request $request)
    {
        $appointment = null;

        if ($request->filled('appointment')) {
            $appointment = Appointment::with('patient')->findOrFail((int) $request->query('appointment'));
            abort_unless($appointment->user_id === $request->user()->id, 403);
        }

        return view('doctor.prescriptions.edit', [
            'prescription' => null,
            'appointment' => $appointment,
        ]);
    }

    public function store(Request $request, PrescriptionService $prescriptions)
    {
        $data = $this->validated($request, creating: true);
        $prescription = $prescriptions->create($request->user(), $data);

        return redirect()
            ->route('prescriptions.edit', $prescription)
            ->with('status', 'Prescription saved.');
    }

    public function edit(Request $request, Prescription $prescription)
    {
        abort_unless($prescription->user_id === $request->user()->id, 403);

        return view('doctor.prescriptions.edit', compact('prescription'));
    }

    public function update(Request $request, Prescription $prescription, PrescriptionService $prescriptions)
    {
        abort_unless($prescription->user_id === $request->user()->id, 403);

        $data = $this->validated($request, creating: false);
        $prescriptions->update($request->user(), $prescription, $data);

        return redirect()
            ->route('prescriptions.edit', $prescription)
            ->with('status', 'Prescription saved.');
    }

    public function shareEmail(Request $request, Prescription $prescription, PrescriptionService $prescriptions)
    {
        abort_unless($prescription->user_id === $request->user()->id, 403);

        $email = $request->input('email');
        $prescriptions->shareEmail($request->user(), $prescription, $email ?: null);

        return back()->with('status', 'Prescription emailed to patient.');
    }

    private function validated(Request $request, bool $creating): array
    {
        $rules = [
            'prescribed_on' => [$creating ? 'nullable' : 'sometimes', 'date'],
            'patient_age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'chief_complaints' => ['nullable', 'string'],
            'on_examination' => ['nullable', 'string'],
            'advice' => ['nullable', 'string'],
            'rx_text' => ['nullable', 'string'],
            'follow_up_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'items' => ['nullable', 'array'],
            'items.*.medicine_id' => ['nullable', 'integer'],
            'items.*.brand_name' => ['required_with:items', 'string', 'max:190'],
            'items.*.strength' => ['nullable', 'string', 'max:100'],
            'items.*.form' => ['nullable', 'string', 'max:100'],
            'items.*.dosage' => ['nullable', 'string', 'max:100'],
            'items.*.frequency' => ['nullable', 'string', 'max:100'],
            'items.*.duration' => ['nullable', 'string', 'max:100'],
            'items.*.instructions' => ['nullable', 'string', 'max:255'],
        ];

        if ($creating) {
            $rules['patient_id'] = ['nullable', 'integer'];
            $rules['patient'] = ['nullable', 'array'];
            $rules['patient.name'] = ['required_without:patient_id', 'string', 'max:120'];
            $rules['patient.phone'] = ['required_without:patient_id', 'string'];
            $rules['patient.email'] = ['nullable', 'email'];
            $rules['patient.age'] = ['nullable', 'integer', 'min:0', 'max:120'];
            $rules['appointment_id'] = ['nullable', 'integer'];
        }

        $data = $request->validate($rules);

        $hasText = filled(trim((string) ($data['rx_text'] ?? '')));
        $hasItems = ! empty($data['items']);

        if (! $hasText && ! $hasItems) {
            throw ValidationException::withMessages([
                'rx_text' => 'Add prescription text or at least one medicine.',
            ]);
        }

        return $data;
    }
}
