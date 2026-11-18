<div>
    @if ($confirmedAt)
        <div class="grid min-h-screen place-items-center p-4">
            <div class="page-enter w-full max-w-md overflow-hidden rounded-[28px] bg-white shadow-2xl ring-1 ring-slate-200/80">
                <div class="relative h-28 overflow-hidden bg-[var(--navy)]">
                    <div class="flex h-full flex-col justify-between bg-gradient-to-br from-[var(--navy)] via-[var(--navy-soft)] to-[var(--blue)] px-6 py-5">
                        <div class="brand text-sm font-bold text-white/90">AR Dental Care</div>
                        <div class="text-lg font-extrabold text-white">{{ $doctor['clinic_name'] ?: 'Dental clinic' }}</div>
                    </div>
                </div>
                <div class="p-8 text-center">
                    <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-emerald-50 text-lg font-bold text-emerald-600">✓</div>
                    <h1 class="text-2xl font-extrabold text-[var(--navy)]">Appointment confirmed</h1>
                    <p class="mt-2 text-sm text-[var(--muted)]">Confirmation sent to your email and phone.</p>
                    <p class="mt-4 text-base font-semibold text-[var(--navy)]">{{ \Carbon\Carbon::parse($confirmedAt)->timezone(config('app.timezone'))->format('l, M j · g:i A') }}</p>
                    @if (!empty($doctor['name']))
                        <p class="mt-1 text-sm text-[var(--muted)]">with {{ $doctor['name'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="min-h-screen p-3 sm:p-5">
            <div class="page-enter mx-auto grid max-w-6xl gap-4 lg:grid-cols-[0.95fr_1.05fr] lg:gap-5">
                <aside class="overflow-hidden rounded-[28px] bg-white shadow-xl ring-1 ring-slate-200/70">
                    <div class="relative h-36 overflow-hidden bg-[var(--navy)] sm:h-40">
                        @if (!empty($doctor['logo_url']))
                            <img src="{{ $doctor['logo_url'] }}" alt="" class="absolute inset-0 h-full w-full scale-110 object-cover opacity-30 blur-xl" aria-hidden>
                            <div class="absolute inset-0 bg-gradient-to-br from-[var(--navy)]/85 via-[var(--navy-soft)]/75 to-[var(--blue)]/45"></div>
                            <div class="relative z-[1] flex h-full items-start justify-center px-8 pb-14 pt-6">
                                <img src="{{ $doctor['logo_url'] }}" alt="{{ $doctor['clinic_name'] }}" class="max-h-14 max-w-[65%] object-contain drop-shadow-lg sm:max-h-16">
                            </div>
                        @else
                            <div class="flex h-full flex-col bg-gradient-to-br from-[var(--navy)] via-[var(--navy-soft)] to-[var(--blue)] px-6 py-5 sm:px-7 sm:py-6">
                                <div class="brand text-sm font-bold text-white/90">AR Dental Care</div>
                            </div>
                        @endif
                    </div>
                    <div class="relative px-6 pb-7 pt-0 sm:px-7 md:px-8">
                        <div class="relative z-[1] -mt-11 mb-5 sm:-mt-12">
                            @if (!empty($doctor['avatar_url']))
                                <img src="{{ $doctor['avatar_url'] }}" alt="{{ $doctor['name'] }}" class="h-[88px] w-[88px] rounded-2xl object-cover shadow-lg ring-[3px] ring-white sm:h-24 sm:w-24">
                            @else
                                <div class="grid h-[88px] w-[88px] place-items-center rounded-2xl bg-[var(--blue-soft)] text-lg font-extrabold text-[var(--blue)] shadow-lg ring-[3px] ring-white sm:h-24 sm:w-24 sm:text-xl">{{ $this->initials() }}</div>
                            @endif
                        </div>
                        <div class="space-y-1.5">
                            <h1 class="text-2xl font-extrabold leading-snug text-[var(--navy)] sm:text-[1.75rem]">{{ $doctor['clinic_name'] ?: 'Book appointment' }}</h1>
                            <p class="text-base font-semibold text-slate-700">{{ $doctor['name'] ?: '…' }}</p>
                        </div>
                        @if (!empty($doctor['specialty']))
                            <p class="mt-3 text-sm font-medium text-[var(--blue)]">{{ $doctor['specialty'] }}</p>
                        @endif
                        @if (!empty($doctor['qualifications']))
                            <p class="mt-1.5 text-sm leading-relaxed text-[var(--muted)]">{{ $doctor['qualifications'] }}</p>
                        @endif
                        @if (!empty($doctor['bmdc_reg']))
                            <p class="mt-3 inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">BMDC {{ $doctor['bmdc_reg'] }}</p>
                        @endif
                        <div class="mt-6 space-y-3.5 border-t border-slate-100 pt-5 text-sm">
                            @if (!empty($doctor['clinic_address']))
                                <div class="grid grid-cols-[4.5rem_1fr] items-start gap-x-3">
                                    <div class="pt-0.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Clinic</div>
                                    <div class="min-w-0 break-words text-slate-700">{{ $doctor['clinic_address'] }}</div>
                                </div>
                            @endif
                            @if (!empty($doctor['visiting_hours']))
                                <div class="grid grid-cols-[4.5rem_1fr] items-start gap-x-3">
                                    <div class="pt-0.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Hours</div>
                                    <div class="min-w-0 break-words text-slate-700">{{ $doctor['visiting_hours'] }}</div>
                                </div>
                            @endif
                            @if (!empty($doctor['clinic_phone']))
                                <div class="grid grid-cols-[4.5rem_1fr] items-start gap-x-3">
                                    <div class="pt-0.5 text-[11px] font-bold uppercase tracking-wide text-slate-400">Phone</div>
                                    <div><a class="font-semibold text-[var(--blue)] hover:underline" href="tel:{{ $doctor['clinic_phone'] }}">{{ $doctor['clinic_phone'] }}</a></div>
                                </div>
                            @endif
                        </div>
                        <p class="mt-6 text-xs leading-relaxed text-[var(--muted)]">Pick an open day and free time — occupied slots are hidden automatically.</p>
                    </div>
                </aside>

                <section class="rounded-[22px] bg-white p-4 shadow-xl ring-1 ring-slate-200/70 sm:rounded-[28px] sm:p-7 md:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-xl font-extrabold text-[var(--navy)]">Select date & time</h2>
                            <p class="mt-1 text-sm text-[var(--muted)]">Only open days and free times are shown.</p>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-3">
                        <div class="mb-3 flex items-center justify-between">
                            <button type="button" class="btn-ghost" wire:click="shiftMonth(-1)">‹</button>
                            <div class="text-sm font-extrabold text-[var(--navy)]">{{ $monthLabel }}</div>
                            <button type="button" class="btn-ghost" wire:click="shiftMonth(1)">›</button>
                        </div>
                        <div class="mb-1 grid grid-cols-7 gap-1 text-center text-[11px] font-bold uppercase tracking-wide text-[var(--muted)]">
                            @foreach (['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'] as $weekday)
                                <div class="py-1">{{ $weekday }}</div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                            @foreach ($days as $day)
                                <button type="button" @disabled($day['disabled']) wire:key="day-{{ $day['key'] }}" wire:click="selectDate('{{ $day['key'] }}')" class="{{ $day['class'] }}" title="{{ $day['free'] ? $day['free'].' open slots' : 'No open slots' }}">
                                    {{ $day['label'] }}
                                    @if ($day['free'] > 0 && ! $day['selected'])
                                        <span class="absolute bottom-1 h-1 w-1 rounded-full bg-[var(--blue)]"></span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-[var(--muted)]">
                            <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-[var(--blue)]"></span> Open day</span>
                            <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-slate-300"></span> Unavailable</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <h3 class="text-sm font-bold text-[var(--navy)]">{{ $date ? 'Available times · '.$dateLabel : 'Pick a date' }}</h3>
                            <span class="text-xs text-[var(--muted)]" wire:loading wire:target="shiftMonth,selectDate">Updating…</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach ($daySlots as $slot)
                                <button type="button" wire:key="slot-{{ $slot['starts_at'] }}" wire:click="selectSlot('{{ $slot['starts_at'] }}')" class="btn-ghost w-full {{ $selected === $slot['starts_at'] ? 'is-selected' : '' }}">{{ $slot['label'] }}</button>
                            @endforeach
                        </div>
                        @if ($date && count($daySlots) === 0)
                            <p class="mt-3 text-sm text-[var(--muted)]">No free slots on this date — every open time is already taken.</p>
                        @endif
                    </div>

                    <form class="mt-6 space-y-3" wire:submit="book">
                        <div class="mb-1 text-sm font-bold text-[var(--navy)]">Your details</div>
                        <input class="input" required placeholder="Your name" wire:model="name">
                        @include('partials.phone-input', ['id' => 'booking-phone', 'label' => 'Phone', 'wireModel' => 'phone', 'value' => $phone, 'required' => true])
                        <input class="input" required type="email" placeholder="Email" wire:model="email">
                        <input class="input" placeholder="Reason (optional)" wire:model="reason">
                        @if ($errors->any())
                            <div class="alert-error">{{ $errors->first() }}</div>
                        @endif
                        <button class="btn-primary w-full" type="submit" @disabled(! $selected) wire:loading.attr="disabled" wire:target="book">
                            <span wire:loading.remove wire:target="book">Confirm appointment</span>
                            <span wire:loading wire:target="book">Confirming…</span>
                        </button>
                    </form>
                </section>
            </div>
        </div>
    @endif
</div>
