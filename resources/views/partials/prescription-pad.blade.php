@php
    $editable = $editable ?? false;
    $field = function ($item, $key) {
        if (is_array($item)) {
            return $item[$key] ?? '';
        }

        return $item->{$key} ?? '';
    };

    $clinicName = $doctor['clinic_name'] ?? $doctor->clinic_name ?? 'AR Dental Care';
    $doctorName = $doctor['name'] ?? $doctor->name ?? '';
    $specialty = $doctor['specialty'] ?? $doctor->specialty ?? '';
    $qualifications = $doctor['qualifications'] ?? $doctor->qualifications ?? '';
    $bmdc = $doctor['bmdc_reg'] ?? $doctor->bmdc_reg ?? '';
    $address = $doctor['clinic_address'] ?? $doctor->clinic_address ?? '';
    $phone = $doctor['clinic_phone'] ?? $doctor->clinic_phone ?? '';
    $hours = $doctor['visiting_hours'] ?? $doctor->visiting_hours ?? '';
    $logo = $doctor['logo_url'] ?? $doctor->logo_url ?? null;
    $logoSrc = $logo ?: asset('images/ar-dental-logo.svg');
@endphp

<div class="rx-pad">
    <div class="rx-pad__header">
        <div class="rx-pad__doctor rx-pad__doctor--left">
            <div class="rx-pad__doctor-name">{{ $doctorName }}</div>
            @if ($specialty)
                <div class="rx-pad__doctor-specialty">{{ $specialty }}</div>
            @endif
            @if ($qualifications)
                <div class="rx-pad__doctor-meta">{{ $qualifications }}</div>
            @endif
            @if ($bmdc)
                <div class="rx-pad__doctor-meta">BMDC: {{ $bmdc }}</div>
            @endif
        </div>

        <div class="rx-pad__brand">
            <img src="{{ $logoSrc }}" alt="{{ $clinicName }}" class="rx-pad__logo">
            <div class="rx-pad__clinic">{{ $clinicName }}</div>
        </div>

        <div class="rx-pad__doctor rx-pad__doctor--right">
            <div class="rx-pad__doctor-name">{{ $clinicName }}</div>
            @if ($address)
                <div class="rx-pad__doctor-meta">{{ $address }}</div>
            @endif
            @if ($phone)
                <div class="rx-pad__doctor-meta">Mobile: {{ $phone }}</div>
            @endif
        </div>
    </div>

    <div class="rx-pad__patient">
        <div class="rx-pad__patient-field rx-pad__patient-field--name">
            <span>Name:</span>
            @if ($editable)
                <input class="rx-pad__line-input" wire:model="patientName" placeholder="Patient name">
            @else
                <span class="rx-pad__dots">{{ $patientName ?: '' }}</span>
            @endif
        </div>
        <div class="rx-pad__patient-field rx-pad__patient-field--age">
            <span>Age:</span>
            @if ($editable)
                <input class="rx-pad__line-input" wire:model="patientAge" placeholder="—">
            @else
                <span class="rx-pad__dots">{{ $patientAge !== '' && $patientAge !== null ? $patientAge : '' }}</span>
            @endif
        </div>
        <div class="rx-pad__patient-field rx-pad__patient-field--date">
            <span>Date:</span>
            @if ($editable)
                <input type="date" class="rx-pad__line-input rx-pad__line-input--date" wire:model="prescribedOn">
            @else
                <span class="rx-pad__dots">{{ $prescribedOn ?: '' }}</span>
            @endif
        </div>
    </div>

    <div class="rx-pad__body">
        <div class="rx-pad__watermark" aria-hidden="true">
            <img src="{{ $logoSrc }}" alt="">
        </div>

        <div class="rx-pad__notes">
            <div class="rx-pad__note">
                <div class="rx-pad__note-label">C/C</div>
                @if ($editable)
                    <textarea class="rx-pad__note-input" wire:model="chiefComplaints" rows="3"></textarea>
                @else
                    <div class="rx-pad__note-text">{{ $chiefComplaints !== '' ? $chiefComplaints : '' }}</div>
                @endif
            </div>
            <div class="rx-pad__note">
                <div class="rx-pad__note-label">O/E</div>
                @if ($editable)
                    <textarea class="rx-pad__note-input" wire:model="onExamination" rows="3"></textarea>
                @else
                    <div class="rx-pad__note-text">{{ $onExamination !== '' ? $onExamination : '' }}</div>
                @endif
            </div>
            <div class="rx-pad__note">
                <div class="rx-pad__note-label">Adv</div>
                @if ($editable)
                    <textarea class="rx-pad__note-input" wire:model="advice" rows="3"></textarea>
                @else
                    <div class="rx-pad__note-text">{{ $advice !== '' ? $advice : '' }}</div>
                @endif
            </div>
        </div>

        <div class="rx-pad__rx">
            <div class="rx-pad__rx-head">
                <div class="rx-pad__rx-mark">℞</div>
                @if ($editable)
                    <button type="button" class="btn-soft no-print shrink-0" wire:click="openPicker">+ Add medicine</button>
                @endif
            </div>

            @php
                $rxTextValue = $rxText ?? '';
            @endphp

            @if ($editable)
                <textarea
                    class="rx-pad__note-input rx-pad__rx-text"
                    rows="8"
                    placeholder="Write prescription text here (medicines optional)…"
                    wire:model="rxText"
                ></textarea>
                @error('rxText')
                    <div class="mt-2 text-sm text-red-600">{{ $message }}</div>
                @enderror
            @elseif (filled(trim((string) $rxTextValue)))
                <div class="rx-pad__rx-text-display">{{ $rxTextValue }}</div>
            @endif

            <div class="rx-pad__meds">
                @if ($editable)
                    @foreach ($items as $idx => $item)
                        <div class="rx-pad__med" wire:key="item-{{ $idx }}-{{ $item['brand_name'] }}">
                            <div class="rx-pad__med-main">
                                <div class="rx-pad__med-name">{{ $idx + 1 }}. {{ $item['brand_name'] }} {{ $item['strength'] ?? '' }} {{ $item['form'] ?? '' }}</div>
                                <div class="rx-pad__med-meta">{{ collect([$item['dosage'] ?? '', $item['frequency'] ?? '', $item['duration'] ?? ''])->filter()->implode(' · ') }}</div>
                                @if (! empty($item['instructions']))
                                    <div class="rx-pad__med-note">{{ $item['instructions'] }}</div>
                                @endif
                            </div>
                            <button type="button" class="btn-danger-text no-print" wire:click="removeItem({{ $idx }})">Remove</button>
                        </div>
                    @endforeach
                @else
                    @foreach ($items as $idx => $item)
                        <div class="rx-pad__med">
                            <div class="rx-pad__med-main">
                                <div class="rx-pad__med-name">{{ $idx + 1 }}. {{ $field($item, 'brand_name') }} {{ $field($item, 'strength') }} {{ $field($item, 'form') }}</div>
                                <div class="rx-pad__med-meta">{{ collect([$field($item, 'dosage'), $field($item, 'frequency'), $field($item, 'duration')])->filter()->implode(' · ') }}</div>
                                @if ($field($item, 'instructions'))
                                    <div class="rx-pad__med-note">{{ $field($item, 'instructions') }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if (! filled(trim((string) $rxTextValue)) && count($items) === 0)
                        <p class="text-sm text-slate-400">No prescription written.</p>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="rx-pad__follow">
        <span>Follow up:</span>
        @if ($editable)
            <input class="rx-pad__follow-input" wire:model="followUpDays">
            <span>days later</span>
        @else
            <span class="rx-pad__dots rx-pad__dots--short">{{ $followUpDays !== '' && $followUpDays !== null ? $followUpDays.' days later' : '' }}</span>
        @endif
    </div>

    <div class="rx-pad__footer">
        <div class="rx-pad__footer-col">
            <div class="rx-pad__footer-label">Chamber</div>
            <div>{{ $address ?: '—' }}</div>
        </div>
        <div class="rx-pad__footer-col rx-pad__footer-col--center">
            <div class="rx-pad__footer-label">Contact</div>
            <div>{{ $phone ?: '—' }}</div>
        </div>
        <div class="rx-pad__footer-col rx-pad__footer-col--right">
            <div class="rx-pad__footer-label">Visiting hours</div>
            <div>{{ $hours ?: '—' }}</div>
        </div>
    </div>
</div>
