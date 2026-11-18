@extends('layouts.doctor')

@section('title', 'Appointments — AR Dental Care')

@section('content')
    <div class="card p-4 md:p-5">
        <h1 class="text-xl font-extrabold text-[var(--navy)] md:text-2xl">Appointments</h1>
        <p class="mb-4 text-sm text-[var(--muted)]">Manage scheduled visits and mark them completed or cancelled.</p>
        @if ($errors->any())
            <div class="mb-3 text-sm text-red-600">{{ $errors->first() }}</div>
        @endif
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b text-[var(--muted)]">
                    <tr>
                        <th class="py-3 pr-4">Patient</th>
                        <th class="py-3 pr-4">When</th>
                        <th class="py-3 pr-4">Reason</th>
                        <th class="py-3 pr-4">Status</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $appointment)
                        <tr class="border-b border-slate-100">
                            <td class="py-3 pr-4 font-semibold">
                                {{ $appointment->patient?->name }}
                                <div class="text-xs font-normal text-[var(--muted)]">{{ $appointment->patient?->phone }}</div>
                            </td>
                            <td class="py-3 pr-4">{{ $appointment->starts_at->format('M j, Y g:i A') }}</td>
                            <td class="py-3 pr-4">{{ $appointment->reason ?: '—' }}</td>
                            <td class="py-3 pr-4"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold">{{ $appointment->status }}</span></td>
                            <td class="py-3">
                                <div class="flex flex-wrap gap-2">
                                    <a
                                        href="{{ route('prescriptions.create', ['appointment' => $appointment->id]) }}"
                                        class="btn-primary"
                                    >Add prescription</a>
                                    @foreach (['completed' => 'Complete', 'no_show' => 'No show', 'cancelled' => 'Cancel'] as $status => $label)
                                        <form method="POST" action="{{ route('appointments.update', $appointment) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $status }}">
                                            <button type="submit" class="btn-soft status-{{ $status }}">{{ $label }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if ($appointments->isEmpty())
                <p class="py-8 text-center text-sm text-[var(--muted)]">No appointments yet.</p>
            @endif
        </div>
        <div class="mt-4">{{ $appointments->links() }}</div>
    </div>
@endsection
