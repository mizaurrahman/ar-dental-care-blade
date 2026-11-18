<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\Medicine;
use App\Models\Prescription;
use App\Services\PrescriptionService;
use App\Support\BdPhone;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PrescriptionEditor extends Component
{
    #[Locked]
    public ?int $prescriptionId = null;

    #[Locked]
    public ?int $appointmentId = null;

    #[Locked]
    public ?int $patientId = null;

    public string $patientName = '';

    public string $patientPhone = '';

    public string $patientEmail = '';

    public string $prescribedOn = '';

    public string $patientAge = '';

    public string $chiefComplaints = '';

    public string $onExamination = '';

    public string $advice = '';

    public string $rxText = '';

    public string $followUpDays = '';

    public array $items = [];

    public bool $showPicker = false;

    public bool $showPreview = false;

    public string $query = '';

    public array $results = [];

    public ?int $medicineId = null;

    public string $brand = '';

    public string $strength = '';

    public string $form = '';

    public string $dosage = '1+0+1';

    public string $frequency = 'After meal';

    public string $duration = '5 days';

    public string $instructions = '';

    public array $queue = [];

    public string $pickerFlash = '';

    public string $message = '';

    public string $shareUrl = '';

    public function mount(?int $prescriptionId = null, ?int $appointmentId = null): void
    {
        $this->prescriptionId = $prescriptionId;
        $this->prescribedOn = now()->toDateString();

        if ($prescriptionId) {
            $prescription = Prescription::with(['patient', 'items', 'user'])->findOrFail($prescriptionId);
            abort_unless($prescription->user_id === auth()->id(), 403);

            $this->fillFromPrescription($prescription);

            return;
        }

        if ($appointmentId) {
            $this->fillFromAppointment($appointmentId);
        }
    }

    public function updatedQuery(): void
    {
        $this->pickerFlash = '';
        $term = trim($this->query);
        if ($term === '') {
            $this->results = [];

            return;
        }

        $this->results = Medicine::query()
            ->where(function ($query) use ($term) {
                $query->where('brand_name', 'like', "%{$term}%")
                    ->orWhere('generic_name', 'like', "%{$term}%")
                    ->orWhere('company', 'like', "%{$term}%");
            })
            ->orderBy('brand_name')
            ->limit(25)
            ->get(['id', 'brand_name', 'generic_name', 'strength', 'form', 'company'])
            ->map(fn (Medicine $medicine) => [
                'id' => $medicine->id,
                'brand_name' => $medicine->brand_name,
                'generic_name' => $medicine->generic_name,
                'strength' => $medicine->strength,
                'form' => $medicine->form,
                'company' => $medicine->company,
            ])
            ->all();
    }

    public function selectMedicine(int $id): void
    {
        $medicine = collect($this->results)->firstWhere('id', $id);
        if (! $medicine) {
            return;
        }

        $this->medicineId = $medicine['id'];
        $this->brand = $medicine['brand_name'] ?? '';
        $this->strength = $medicine['strength'] ?? '';
        $this->form = $medicine['form'] ?? '';
        $this->query = $this->brand;
    }

    public function addToQueue(): void
    {
        $item = $this->currentItem();
        if (! $item) {
            return;
        }

        $this->queue[] = $item;
        $this->resetComposer();
        $this->pickerFlash = 'Added — search the next medicine';
    }

    public function removeQueued(int $index): void
    {
        unset($this->queue[$index]);
        $this->queue = array_values($this->queue);
    }

    public function applyAll(): void
    {
        $pending = $this->queue;
        if ($item = $this->currentItem()) {
            $pending[] = $item;
        }
        if ($pending === []) {
            return;
        }

        $this->items = array_values([...$this->items, ...$pending]);
        $this->queue = [];
        $this->showPicker = false;
        $this->resetComposer();
        $count = count($pending);
        $this->message = $count.' medicine'.($count === 1 ? '' : 's').' added to prescription.';
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function openPicker(): void
    {
        $this->showPicker = true;
        $this->pickerFlash = '';
    }

    public function closePicker(): void
    {
        $this->showPicker = false;
    }

    public function pendingCount(): int
    {
        return count($this->queue) + ($this->currentItem() ? 1 : 0);
    }

    public function save(PrescriptionService $prescriptions): void
    {
        $this->message = '';

        $rules = [
            'prescribedOn' => ['required', 'date'],
            'patientAge' => ['nullable', 'integer', 'min:0', 'max:120'],
            'followUpDays' => ['nullable', 'integer', 'min:0', 'max:365'],
            'chiefComplaints' => ['nullable', 'string'],
            'onExamination' => ['nullable', 'string'],
            'advice' => ['nullable', 'string'],
            'rxText' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.brand_name' => ['required', 'string', 'max:190'],
        ];

        if (! $this->prescriptionId && ! $this->patientId) {
            $rules['patientName'] = ['required', 'string', 'max:120'];
            $rules['patientPhone'] = ['required', 'string'];
            $rules['patientEmail'] = ['nullable', 'email'];
        }

        $this->validate($rules);

        if (trim($this->rxText) === '' && $this->items === []) {
            $this->addError('rxText', 'Add prescription text or at least one medicine.');

            return;
        }

        $payload = [
            'prescribed_on' => $this->prescribedOn,
            'patient_age' => $this->patientAge !== '' ? (int) $this->patientAge : null,
            'chief_complaints' => $this->chiefComplaints ?: null,
            'on_examination' => $this->onExamination ?: null,
            'advice' => $this->advice ?: null,
            'rx_text' => trim($this->rxText) !== '' ? $this->rxText : null,
            'follow_up_days' => $this->followUpDays !== '' ? (int) $this->followUpDays : null,
            'items' => $this->items,
        ];

        $user = auth()->user();

        if ($this->prescriptionId) {
            $existing = Prescription::findOrFail($this->prescriptionId);
            abort_unless($existing->user_id === $user->id, 403);
            $saved = $prescriptions->update($user, $existing, $payload);
            $this->fillFromPrescription($saved);
            $this->message = 'Prescription saved.';

            return;
        }

        if ($this->appointmentId) {
            $payload['appointment_id'] = $this->appointmentId;
        }

        if ($this->patientId) {
            $payload['patient_id'] = $this->patientId;
        } else {
            $payload['patient'] = [
                'name' => $this->patientName,
                'phone' => $this->patientPhone,
                'email' => $this->patientEmail ?: null,
                'age' => $this->patientAge !== '' ? (int) $this->patientAge : null,
            ];
        }

        $saved = $prescriptions->create($user, $payload);
        session()->flash('status', 'Prescription saved.');
        $this->redirect(route('prescriptions.edit', $saved), navigate: false);
    }

    public function openPreview(): void
    {
        $this->showPreview = true;
    }

    public function closePreview(): void
    {
        $this->showPreview = false;
    }

    public function shareEmail(PrescriptionService $prescriptions): void
    {
        if (! $this->prescriptionId) {
            return;
        }

        $prescription = Prescription::findOrFail($this->prescriptionId);
        abort_unless($prescription->user_id === auth()->id(), 403);
        $prescriptions->shareEmail(auth()->user(), $prescription, $this->patientEmail ?: null);
        $this->message = 'Prescription emailed to patient.';
    }

    public function render()
    {
        return view('livewire.prescription-editor', [
            'doctor' => auth()->user(),
        ]);
    }

    private function fillFromPrescription(Prescription $prescription): void
    {
        $this->prescriptionId = $prescription->id;
        $this->appointmentId = $prescription->appointment_id;
        $this->patientId = $prescription->patient_id;
        $this->patientName = $prescription->patient?->name ?? '';
        $this->patientPhone = BdPhone::subscriber($prescription->patient?->phone) ?? '';
        $this->patientEmail = $prescription->patient?->email ?? '';
        $this->prescribedOn = $prescription->prescribed_on?->toDateString() ?? now()->toDateString();
        $this->patientAge = $prescription->patient_age !== null ? (string) $prescription->patient_age : '';
        $this->chiefComplaints = $prescription->chief_complaints ?? '';
        $this->onExamination = $prescription->on_examination ?? '';
        $this->advice = $prescription->advice ?? '';
        $this->rxText = $prescription->rx_text ?? '';
        $this->followUpDays = $prescription->follow_up_days !== null ? (string) $prescription->follow_up_days : '';
        $this->shareUrl = $prescription->share_url;
        $this->items = $prescription->items->map(fn ($item) => [
            'medicine_id' => $item->medicine_id,
            'brand_name' => $item->brand_name,
            'strength' => $item->strength ?? '',
            'form' => $item->form ?? '',
            'dosage' => $item->dosage ?? '',
            'frequency' => $item->frequency ?? '',
            'duration' => $item->duration ?? '',
            'instructions' => $item->instructions ?? '',
        ])->all();
    }

    private function fillFromAppointment(int $appointmentId): void
    {
        $appointment = Appointment::with('patient')->findOrFail($appointmentId);
        abort_unless($appointment->user_id === auth()->id(), 403);

        $patient = $appointment->patient;
        abort_unless($patient !== null, 404);

        $this->appointmentId = $appointment->id;
        $this->patientId = $patient->id;
        $this->patientName = $patient->name ?? '';
        $this->patientPhone = BdPhone::subscriber($patient->phone) ?? '';
        $this->patientEmail = $patient->email ?? '';
        $this->patientAge = $patient->age !== null ? (string) $patient->age : '';
        $this->chiefComplaints = $appointment->reason ?? '';
        $this->prescribedOn = now()->toDateString();
    }

    private function currentItem(): ?array
    {
        $brand = trim($this->brand !== '' ? $this->brand : $this->query);
        if ($brand === '') {
            return null;
        }

        return [
            'medicine_id' => $this->medicineId,
            'brand_name' => $brand,
            'strength' => $this->strength,
            'form' => $this->form,
            'dosage' => $this->dosage,
            'frequency' => $this->frequency,
            'duration' => $this->duration,
            'instructions' => $this->instructions,
        ];
    }

    private function resetComposer(): void
    {
        $this->query = '';
        $this->results = [];
        $this->medicineId = null;
        $this->brand = '';
        $this->strength = '';
        $this->form = '';
        $this->dosage = '1+0+1';
        $this->frequency = 'After meal';
        $this->duration = '5 days';
        $this->instructions = '';
    }
}
