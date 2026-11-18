<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\AppointmentBookingService;
use App\Services\SlotService;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class PublicBooking extends Component
{
    #[Locked]
    public string $slug = '';

    #[Locked]
    public int $doctorId = 0;

    public array $doctor = [];

    public string $month = '';

    public string $date = '';

    public ?string $selected = null;

    public array $availability = [];

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $reason = '';

    public ?string $confirmedAt = null;

    public function mount(string $slug): void
    {
        $doctor = User::where('appointment_slug', $slug)->firstOrFail();
        $this->slug = $slug;
        $this->doctorId = $doctor->id;
        $this->doctor = $this->present($doctor);
        $this->month = now()->startOfMonth()->toDateString();
        $this->confirmedAt = session('confirmed_at');
        $this->refreshSlots($doctor);
        $this->pickFirstDate();
    }

    public function shiftMonth(int $delta): void
    {
        $this->month = Carbon::parse($this->month)->addMonths($delta)->startOfMonth()->toDateString();
        $doctor = User::findOrFail($this->doctorId);
        $this->refreshSlots($doctor);

        if ($this->date === '' || empty($this->availability[$this->date])) {
            $this->date = '';
            $this->selected = null;
            $this->pickFirstDate();
        }
    }

    public function selectDate(string $date): void
    {
        if (empty($this->availability[$date])) {
            return;
        }

        $this->date = $date;
        $this->selected = null;
    }

    public function selectSlot(string $startsAt): void
    {
        $this->selected = $startsAt;
    }

    public function book(AppointmentBookingService $booking): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string'],
            'email' => ['required', 'email'],
            'selected' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $doctor = User::findOrFail($this->doctorId);

        try {
            $appointment = $booking->book($doctor, [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'starts_at' => $this->selected,
                'reason' => $this->reason !== '' ? $this->reason : null,
            ]);
        } catch (ValidationException $exception) {
            $this->refreshSlots($doctor);
            $this->selected = null;
            throw $exception;
        }

        $this->confirmedAt = $appointment->starts_at->toIso8601String();
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->doctor['name'] ?? '')) ?: [];
        $letters = '';
        foreach (array_slice(array_filter($parts), 0, 2) as $part) {
            $letters .= strtoupper(substr($part, 0, 1));
        }

        return $letters !== '' ? $letters : 'DR';
    }

    public function render()
    {
        return view('livewire.public-booking', [
            'days' => $this->calendarDays(),
            'daySlots' => $this->date !== '' ? ($this->availability[$this->date] ?? []) : [],
            'monthLabel' => $this->month !== '' ? Carbon::parse($this->month)->format('F Y') : '',
            'dateLabel' => $this->date !== '' ? Carbon::parse($this->date)->format('D, M j') : '',
        ]);
    }

    private function refreshSlots(User $doctor): void
    {
        $from = Carbon::parse($this->month)->startOfMonth();
        $to = $from->copy()->endOfMonth()->addDays(6);
        $this->availability = app(SlotService::class)->slotsForRange($doctor, $from, $to)->all();
    }

    private function pickFirstDate(): void
    {
        foreach ($this->availability as $day => $list) {
            if (count($list) > 0) {
                $this->date = $day;

                return;
            }
        }
    }

    private function calendarDays(): array
    {
        if ($this->month === '') {
            return [];
        }

        $month = Carbon::parse($this->month)->startOfMonth();
        $cursor = $month->copy()->startOfWeek(Carbon::SUNDAY);
        $end = $month->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        $today = now()->startOfDay();
        $days = [];

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $free = count($this->availability[$key] ?? []);
            $inMonth = $cursor->month === $month->month && $cursor->year === $month->year;
            $past = $cursor->copy()->startOfDay()->lt($today);
            $disabled = $past || ! $inMonth || $free === 0;
            $selected = $this->date === $key;

            $class = 'relative flex h-11 flex-col items-center justify-center rounded-xl text-sm font-semibold transition sm:h-10';
            if (! $inMonth) {
                $class .= ' text-slate-300';
            } elseif ($past || $free === 0) {
                $class .= ' cursor-not-allowed text-slate-300';
            }
            if ($selected) {
                $class .= ' bg-[var(--blue)] text-white shadow-md';
            } elseif (! $disabled && $free > 0) {
                $class .= ' bg-slate-50 text-[var(--navy)] hover:bg-blue-50 hover:text-[var(--blue)]';
            }

            $days[] = [
                'key' => $key,
                'label' => $cursor->format('j'),
                'free' => $free,
                'disabled' => $disabled,
                'selected' => $selected,
                'class' => $class,
            ];
            $cursor->addDay();
        }

        return $days;
    }

    private function present(User $doctor): array
    {
        return [
            'name' => $doctor->name,
            'clinic_name' => $doctor->clinic_name,
            'specialty' => $doctor->specialty,
            'qualifications' => $doctor->qualifications,
            'bmdc_reg' => $doctor->bmdc_reg,
            'clinic_address' => $doctor->clinic_address,
            'clinic_phone' => $doctor->clinic_phone,
            'visiting_hours' => $doctor->visiting_hours,
            'avatar_url' => $doctor->avatar_url,
            'logo_url' => $doctor->logo_url,
        ];
    }
}
