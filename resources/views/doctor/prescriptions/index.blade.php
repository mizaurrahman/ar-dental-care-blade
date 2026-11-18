@extends('layouts.doctor')

@section('title', 'Prescriptions — AR Dental Care')

@section('content')
    <div class="card p-4 md:p-5">
        <div class="mb-3 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-extrabold text-[var(--navy)] md:text-2xl">Prescriptions</h1>
                <p class="text-sm text-[var(--muted)]">Create pad-style prescriptions and share with patients.</p>
            </div>
            <a href="{{ route('prescriptions.create') }}" class="btn-primary">New prescription</a>
        </div>
        <div class="space-y-2">
            @forelse ($prescriptions as $rx)
                <a href="{{ route('prescriptions.edit', $rx) }}" class="flex items-center justify-between rounded-xl border border-slate-100 px-3 py-2.5 transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-blue-50/40">
                    <div>
                        <div class="font-semibold">{{ $rx->patient?->name }}</div>
                        <div class="text-xs text-[var(--muted)]">{{ $rx->prescribed_on?->format('M j, Y') }} · {{ $rx->items->count() }} medicines</div>
                    </div>
                    <span class="text-sm font-semibold text-[var(--blue)]">Open</span>
                </a>
            @empty
                <p class="py-8 text-center text-sm text-[var(--muted)]">No prescriptions yet.</p>
            @endforelse
        </div>
        <div class="mt-4">{{ $prescriptions->links() }}</div>
    </div>
@endsection
