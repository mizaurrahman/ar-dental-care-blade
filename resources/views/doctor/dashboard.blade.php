@extends('layouts.doctor')

@section('title', 'Dashboard — AR Dental Care')

@section('content')
    <div class="space-y-3">
        <div class="fade-up">
            <h1 class="text-xl font-extrabold leading-tight text-[var(--navy)] sm:text-2xl md:text-3xl">{{ $greeting }}, {{ $user->name }}</h1>
            <p class="mt-1 text-sm text-[var(--muted)]">Here is what is happening at your clinic today.</p>
        </div>

        @php
            $cards = [
                ['label' => 'Total Appointments', 'value' => $stats['total_appointments'], 'color' => 'bg-violet-100 text-violet-700'],
                ['label' => "Today's Appointments", 'value' => $stats['today_appointments'], 'color' => 'bg-rose-100 text-rose-700'],
                ['label' => 'Prescriptions', 'value' => $stats['prescriptions'], 'color' => 'bg-amber-100 text-amber-700'],
                ['label' => 'New Patients', 'value' => $stats['new_patients'], 'color' => 'bg-emerald-100 text-emerald-700'],
            ];
        @endphp
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($cards as $i => $card)
                <div class="card fade-up p-4">
                    <div class="mb-2 inline-flex rounded-lg px-2 py-0.5 text-[11px] font-bold {{ $card['color'] }}">{{ $card['label'] }}</div>
                    <div class="text-2xl font-extrabold tracking-tight">{{ $card['value'] }}</div>
                </div>
            @endforeach
        </div>

        <div class="grid gap-3 xl:grid-cols-[1.4fr_0.8fr]">
            <div class="card fade-up p-4">
                <h2 class="mb-2 text-base font-bold">Your patients this week</h2>
                @include('partials.week-chart', ['chart' => $chart])
            </div>
            <div class="card fade-up p-4">
                <h2 class="mb-2 text-base font-bold">Calendar</h2>
                <form method="GET" action="{{ route('dashboard') }}">
                    <input type="date" name="date" class="input" value="{{ $selectedDate }}" onchange="this.form.submit()">
                </form>
                <div class="mt-3 space-y-2">
                    @forelse ($patientsToday->take(5) as $appointment)
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 transition hover:bg-blue-50">
                            <div>
                                <div class="text-sm font-semibold">{{ $appointment->patient?->name }}</div>
                                <div class="text-[11px] text-[var(--muted)]">{{ $appointment->reason ?: 'Consultation' }}</div>
                            </div>
                            <div class="text-xs font-bold text-[var(--blue)]">{{ $appointment->starts_at->format('g:i A') }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--muted)]">No patients on this date.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid gap-3 lg:grid-cols-2">
            <div class="card fade-up p-4">
                <h2 class="mb-2 text-base font-bold">Upcoming appointments</h2>
                <div class="space-y-2">
                    @forelse ($upcoming as $appointment)
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <div>
                                <div class="text-sm font-semibold">{{ $appointment->patient?->name }}</div>
                                <div class="text-[11px] text-[var(--muted)]">{{ $appointment->starts_at->format('M j, g:i A') }}</div>
                            </div>
                            <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[11px] font-semibold text-[var(--blue)]">{{ $appointment->status }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--muted)]">No upcoming appointments.</p>
                    @endforelse
                </div>
                <a href="{{ route('appointments.index') }}" class="mt-3 inline-block text-sm font-semibold text-[var(--blue)] hover:underline">View all →</a>
            </div>

            <div class="card fade-up p-4">
                <h2 class="mb-2 text-base font-bold">Appointment link & QR</h2>
                @if ($user->clinic_name)
                    <p class="mb-1 text-sm font-semibold text-[var(--navy)]">{{ $user->clinic_name }}</p>
                @endif
                <p class="break-all text-xs text-[var(--muted)]">{{ $user->appointment_url }}</p>
                @if ($user->appointment_url)
                    <div class="mt-3 inline-flex w-[140px] rounded-2xl bg-white p-2.5 ring-1 ring-slate-200">
                        {!! \App\Support\QrSvg::render($user->appointment_url, 120) !!}
                    </div>
                @endif
                <div class="toolbar mt-3">
                    <button type="button" class="btn-primary" onclick="navigator.clipboard.writeText(@js($user->appointment_url))">Copy link</button>
                    <a class="btn-ghost" href="{{ route('profile.edit') }}">Profile settings</a>
                </div>
            </div>
        </div>
    </div>
@endsection
