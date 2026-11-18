<div class="space-y-3">
    <div class="no-print flex flex-wrap items-center justify-between gap-2">
        <div>
            <a href="{{ route('prescriptions.index') }}" class="text-sm font-semibold text-[var(--blue)] hover:underline">← Back</a>
            <h1 class="text-xl font-extrabold text-[var(--navy)] md:text-2xl">{{ $prescriptionId ? 'Edit prescription' : 'New prescription' }}</h1>
        </div>
        <div class="toolbar">
            <button type="button" class="btn-ghost" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                <span wire:loading.remove wire:target="save">Save</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
            <button type="button" class="btn-soft" wire:click="openPreview">Preview</button>
            @if ($prescriptionId)
                @if ($shareUrl)
                    <a class="btn-ghost" target="_blank" rel="noopener" href="{{ $shareUrl }}">Open share view</a>
                @endif
                <button type="button" class="btn-ghost" onclick="window.print()">Print</button>
                <button type="button" class="btn-ghost" wire:click="shareEmail" wire:loading.attr="disabled" wire:target="shareEmail">Email</button>
                @if ($shareUrl)
                    <a class="btn-ghost" target="_blank" rel="noopener" href="https://wa.me/?text={{ urlencode('Your prescription from '.auth()->user()->clinic_name.': '.$shareUrl) }}">WhatsApp</a>
                    <button type="button" class="btn-soft" onclick="navigator.clipboard.writeText(@js($shareUrl)); this.textContent='Copied';">Copy link</button>
                @endif
            @endif
        </div>
    </div>

    @if ($message)
        <div class="no-print alert-ok">{{ $message }}</div>
    @endif
    @if ($errors->any())
        <div class="no-print alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="no-print card grid gap-2 p-3 sm:grid-cols-2 md:grid-cols-4">
        <input class="input" placeholder="Patient name" wire:model="patientName">
        <div>
            @include('partials.phone-input', ['id' => 'patientPhone', 'label' => false, 'wireModel' => 'patientPhone', 'value' => $patientPhone])
        </div>
        <input class="input" placeholder="Email" wire:model="patientEmail">
        <input class="input" placeholder="Age" wire:model="patientAge">
    </div>

    <div class="fade-up">
        @include('partials.prescription-pad', [
            'editable' => true,
            'doctor' => $doctor,
            'patientName' => $patientName,
            'patientAge' => $patientAge,
            'prescribedOn' => $prescribedOn,
            'chiefComplaints' => $chiefComplaints,
            'onExamination' => $onExamination,
            'advice' => $advice,
            'rxText' => $rxText,
            'followUpDays' => $followUpDays,
            'items' => $items,
        ])
    </div>

    @if ($showPicker)
        <div class="modal-backdrop fixed inset-0 z-50 grid place-items-end bg-slate-900/45 p-0 sm:place-items-center sm:p-3" wire:click="closePicker">
            <div class="modal-panel flex max-h-[94vh] w-full max-w-4xl flex-col overflow-hidden rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" wire:click.stop>
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <div>
                        <h2 class="text-lg font-bold text-[var(--navy)]">Build medicine list</h2>
                        <p class="text-xs text-[var(--muted)]">Add several medicines, then apply them all to the Rx</p>
                    </div>
                    <button type="button" class="btn-ghost" wire:click="closePicker">Close</button>
                </div>
                <div class="grid min-h-0 flex-1 overflow-hidden lg:grid-cols-[1.15fr_0.85fr]">
                    <div class="min-h-0 overflow-auto border-b border-slate-100 p-4 lg:border-b-0 lg:border-r">
                        <label class="label">Search catalog</label>
                        <input class="input" placeholder="Type brand or generic (e.g. Napa, Amoxicillin)" wire:model.live.debounce.250ms="query" wire:keydown.enter.prevent="addToQueue">
                        <div class="mt-2 max-h-36 overflow-auto rounded-xl border border-slate-200">
                            <div class="p-2.5 text-sm text-[var(--muted)]" wire:loading wire:target="query">Searching…</div>
                            @foreach ($results as $medicine)
                                <button type="button" wire:key="med-{{ $medicine['id'] }}" class="flex w-full border-b border-slate-100 px-3 py-2 text-left text-sm hover:bg-blue-50 {{ $medicineId === $medicine['id'] ? 'bg-blue-50' : '' }}" wire:click="selectMedicine({{ $medicine['id'] }})">
                                    <span>
                                        <strong>{{ $medicine['brand_name'] }}</strong> {{ $medicine['strength'] }} {{ $medicine['form'] }}
                                        <div class="text-[11px] text-[var(--muted)]">{{ $medicine['generic_name'] }} · {{ $medicine['company'] }}</div>
                                    </span>
                                </button>
                            @endforeach
                            @if ($query !== '' && count($results) === 0)
                                <div class="p-2.5 text-sm text-[var(--muted)]" wire:loading.remove wire:target="query">No match — fill the fields below as free text.</div>
                            @endif
                        </div>
                        <div class="mt-3 grid gap-2 sm:grid-cols-2">
                            <div>
                                <label class="label">Brand</label>
                                <input class="input" wire:model="brand" placeholder="{{ $query }}">
                            </div>
                            <div>
                                <label class="label">Strength</label>
                                <input class="input" wire:model="strength">
                            </div>
                            <div>
                                <label class="label">Form</label>
                                <input class="input" wire:model="form">
                            </div>
                            <div>
                                <label class="label">Dosage</label>
                                <input class="input" wire:model="dosage">
                            </div>
                            <div>
                                <label class="label">Frequency</label>
                                <input class="input" wire:model="frequency">
                            </div>
                            <div>
                                <label class="label">Duration</label>
                                <input class="input" wire:model="duration">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="label">Instructions</label>
                                <input class="input" wire:model="instructions">
                            </div>
                        </div>
                        @if ($pickerFlash)
                            <div class="mt-2 text-xs font-semibold text-emerald-600">{{ $pickerFlash }}</div>
                        @endif
                        <div class="toolbar mt-3">
                            <button type="button" class="btn-primary" wire:click="addToQueue">+ Add to list</button>
                            <span class="text-[11px] text-[var(--muted)]">Enter also adds</span>
                        </div>
                    </div>
                    <div class="flex min-h-0 flex-col bg-slate-50/80 p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <h3 class="text-sm font-bold text-[var(--navy)]">Rx queue</h3>
                            <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-bold text-[var(--blue)]">{{ count($queue) }} medicine{{ count($queue) === 1 ? '' : 's' }}</span>
                        </div>
                        <div class="min-h-0 flex-1 space-y-2 overflow-auto">
                            @forelse ($queue as $idx => $item)
                                <div class="rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm" wire:key="queue-{{ $idx }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <div class="text-xs font-bold text-[var(--blue)]">#{{ $idx + 1 }}</div>
                                            <div class="text-sm font-bold">{{ $item['brand_name'] }} {{ $item['strength'] }} {{ $item['form'] }}</div>
                                            <div class="text-[11px] text-slate-600">{{ collect([$item['dosage'], $item['frequency'], $item['duration']])->filter()->implode(' · ') }}</div>
                                        </div>
                                        <button type="button" class="btn-danger-text" wire:click="removeQueued({{ $idx }})">Remove</button>
                                    </div>
                                </div>
                            @empty
                                <div class="grid h-40 place-items-center rounded-xl border border-dashed border-slate-300 bg-white/70 px-4 text-center text-sm text-[var(--muted)]">
                                    Your medicine stack appears here — add one, then the next, like building a list.
                                </div>
                            @endforelse
                        </div>
                        <div class="mt-3 flex gap-2 border-t border-slate-200 pt-3">
                            <button type="button" class="btn-primary flex-1" wire:click="applyAll" @disabled($this->pendingCount() === 0)>
                                Apply {{ $this->pendingCount() ?: '' }} to prescription
                            </button>
                            <button type="button" class="btn-ghost" wire:click="closePicker">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($showPreview)
        <div class="modal-backdrop fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 p-3 sm:p-6" wire:click="closePreview">
            <div class="modal-panel mx-auto max-w-4xl space-y-3" wire:click.stop>
                <div class="no-print flex flex-wrap items-center justify-between gap-2 rounded-2xl bg-white px-4 py-3 shadow">
                    <div>
                        <div class="text-sm font-bold text-[var(--navy)]">Prescription preview</div>
                        <div class="text-xs text-[var(--muted)]">Patient-facing pad view</div>
                    </div>
                    <div class="toolbar">
                        @if ($shareUrl)
                            <a class="btn-ghost" target="_blank" rel="noopener" href="{{ $shareUrl }}">Open public link</a>
                        @endif
                        <button type="button" class="btn-ghost" onclick="window.print()">Print</button>
                        <button type="button" class="btn-primary" wire:click="closePreview">Close</button>
                    </div>
                </div>
                @include('partials.prescription-pad', [
                    'readOnly' => true,
                    'doctor' => $doctor,
                    'patientName' => $patientName,
                    'patientAge' => $patientAge !== '' ? $patientAge : null,
                    'prescribedOn' => $prescribedOn,
                    'chiefComplaints' => $chiefComplaints,
                    'onExamination' => $onExamination,
                    'advice' => $advice,
                    'rxText' => $rxText,
                    'followUpDays' => $followUpDays !== '' ? $followUpDays : null,
                    'items' => $items,
                ])
            </div>
        </div>
    @endif
</div>
