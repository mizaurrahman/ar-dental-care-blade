@extends('layouts.doctor')

@section('title', 'Appointment hours — AR Dental Care')

@section('content')
    @php
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    @endphp
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-5">
            <h1 class="text-2xl font-extrabold text-[var(--navy)]">Appointment configuration</h1>
            <p class="mb-4 text-sm text-[var(--muted)]">Set weekly hours patients can use on your public appointment link.</p>
            @if ($errors->any())
                <div class="mb-3 text-sm text-red-600">{{ $errors->first() }}</div>
            @endif
            <form class="grid gap-3 sm:grid-cols-2" method="POST" action="{{ route('availability.rules.store') }}">
                @csrf
                <div>
                    <label class="label" for="day_of_week">Day</label>
                    <select id="day_of_week" class="input" name="day_of_week">
                        @foreach ($days as $i => $day)
                            <option value="{{ $i }}" @selected((int) old('day_of_week', 0) === $i)>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label" for="slot_minutes">Slot minutes</label>
                    <input id="slot_minutes" class="input" type="number" name="slot_minutes" value="{{ old('slot_minutes', 30) }}" min="10" max="120">
                </div>
                <div>
                    <label class="label" for="start_time">Start</label>
                    <input id="start_time" class="input" type="time" name="start_time" value="{{ old('start_time', '10:00') }}" required>
                </div>
                <div>
                    <label class="label" for="end_time">End</label>
                    <input id="end_time" class="input" type="time" name="end_time" value="{{ old('end_time', '14:00') }}" required>
                </div>
                <button class="btn-primary sm:col-span-2" type="submit">Add hours</button>
            </form>
            <div class="mt-5 space-y-2">
                @foreach ($rules as $rule)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                        <span>{{ $days[$rule->day_of_week] ?? 'Day' }} · {{ substr((string) $rule->start_time, 0, 5) }} – {{ substr((string) $rule->end_time, 0, 5) }} · {{ $rule->slot_minutes }}m</span>
                        <form method="POST" action="{{ route('availability.rules.destroy', $rule) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-rose-600" type="submit">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-5">
            <h2 class="text-xl font-bold">Day off / exceptions</h2>
            <form class="mt-4 grid gap-3" method="POST" action="{{ route('availability.exceptions.store') }}">
                @csrf
                <input type="hidden" name="is_closed" value="1">
                <div>
                    <label class="label" for="exception_date">Date</label>
                    <input id="exception_date" class="input" type="date" name="date" required value="{{ old('date') }}">
                </div>
                <div>
                    <label class="label" for="reason">Reason</label>
                    <input id="reason" class="input" name="reason" value="{{ old('reason') }}">
                </div>
                <button class="btn-primary" type="submit">Mark closed</button>
            </form>
            <div class="mt-5 space-y-2">
                @foreach ($exceptions as $exception)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm">
                        <span>{{ $exception->date?->toDateString() }}{{ $exception->reason ? ' · '.$exception->reason : '' }}</span>
                        <form method="POST" action="{{ route('availability.exceptions.destroy', $exception) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-rose-600" type="submit">Delete</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
