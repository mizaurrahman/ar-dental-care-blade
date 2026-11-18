@extends('layouts.doctor')

@section('title', 'Profile — AR Dental Care')

@section('content')
    <div class="grid gap-4 xl:grid-cols-[1.15fr_0.85fr]">
        <form class="card space-y-4 p-4 sm:p-5" method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div>
                <h1 class="text-xl font-extrabold text-[var(--navy)] sm:text-2xl">Profile & clinic settings</h1>
                <p class="mt-1 text-sm text-[var(--muted)]">These details and logo appear on every prescription pad and public booking page.</p>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50/60 px-3 py-2.5 text-sm text-slate-700">
                Change doctor/clinic info below, then upload a clinic logo on the right. Save to update your pad instantly.
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                @foreach ([
                    'name' => 'Doctor name',
                    'email' => 'Email',
                    'clinic_name' => 'Clinic name',
                    'specialty' => 'Specialty',
                    'bmdc_reg' => 'BMDC Reg No',
                    'appointment_slug' => 'Appointment slug',
                ] as $key => $label)
                    <div>
                        <label class="label" for="{{ $key }}">{{ $label }}</label>
                        <input id="{{ $key }}" class="input" name="{{ $key }}" value="{{ old($key, $user->{$key}) }}">
                    </div>
                @endforeach
                <div>
                    @include('partials.phone-input', ['id' => 'phone', 'name' => 'phone', 'label' => 'Phone', 'value' => old('phone', $user->phone)])
                </div>
                <div>
                    @include('partials.phone-input', ['id' => 'clinic_phone', 'name' => 'clinic_phone', 'label' => 'Clinic phone', 'value' => old('clinic_phone', $user->clinic_phone)])
                </div>
                <div class="md:col-span-2">
                    <label class="label" for="qualifications">Qualifications</label>
                    <textarea id="qualifications" class="input min-h-20" name="qualifications">{{ old('qualifications', $user->qualifications) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="label" for="clinic_address">Clinic address</label>
                    <input id="clinic_address" class="input" name="clinic_address" value="{{ old('clinic_address', $user->clinic_address) }}">
                </div>
                <div class="md:col-span-2">
                    <label class="label" for="visiting_hours">Visiting hours</label>
                    <input id="visiting_hours" class="input" name="visiting_hours" value="{{ old('visiting_hours', $user->visiting_hours) }}">
                </div>
            </div>
            @if ($errors->any())
                <div class="rounded-xl bg-red-50 px-3 py-2 text-sm text-red-600">{{ $errors->first() }}</div>
            @endif
            <button class="btn-primary" type="submit">Save prescription pad info</button>
        </form>

        <div class="space-y-4">
            <div class="card space-y-4 p-5">
                <div>
                    <h2 class="text-lg font-bold text-[var(--navy)]">Pad branding</h2>
                    <p class="mt-1 text-sm text-[var(--muted)]">Clinic logo is centered on the prescription pad. Doctor photo is used on the booking page.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-3">
                    <div class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">Current logo</div>
                    <div class="flex items-center justify-center rounded-xl bg-white p-4 ring-1 ring-slate-100">
                        <img
                            src="{{ $user->logo_url ?: asset('images/ar-dental-logo.svg') }}"
                            alt="Clinic logo"
                            class="h-24 w-24 object-contain"
                        >
                    </div>
                    <form class="mt-3" method="POST" action="{{ route('profile.logo') }}" enctype="multipart/form-data">
                        @csrf
                        <label class="label" for="logo">Upload clinic logo</label>
                        <input id="logo" class="input" type="file" name="logo" accept="image/png,image/jpeg,image/webp" onchange="this.form.submit()">
                        @error('logo')<div class="mt-2 text-sm text-red-600">{{ $message }}</div>@enderror
                    </form>
                    @if ($user->logo_url)
                        <form class="mt-2" method="POST" action="{{ route('profile.logo.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-ghost w-full">Remove logo (use default)</button>
                        </form>
                    @endif
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-3">
                    <div class="mb-2 text-xs font-bold uppercase tracking-wide text-slate-400">Doctor photo</div>
                    <div class="flex items-center gap-3">
                        <div class="grid h-20 w-20 place-items-center overflow-hidden rounded-2xl bg-white ring-1 ring-slate-100">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="" class="h-full w-full object-cover">
                            @else
                                <span class="text-sm font-bold text-slate-400">No photo</span>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                                @csrf
                                <label class="label" for="avatar">Upload doctor image</label>
                                <input id="avatar" class="input" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" onchange="this.form.submit()">
                                @error('avatar')<div class="mt-2 text-sm text-red-600">{{ $message }}</div>@enderror
                            </form>
                            @if ($user->avatar_url)
                                <form class="mt-2" method="POST" action="{{ route('profile.avatar.destroy') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-ghost">Remove photo</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card overflow-hidden p-0">
                <div class="border-b border-slate-100 px-5 py-3">
                    <h2 class="text-lg font-bold text-[var(--navy)]">Prescription pad preview</h2>
                    <p class="text-xs text-[var(--muted)]">Live preview from your saved profile settings</p>
                </div>
                <div class="p-3 sm:p-4">
                    @include('partials.prescription-pad', [
                        'editable' => false,
                        'doctor' => $user,
                        'patientName' => 'Patient name',
                        'patientAge' => '—',
                        'prescribedOn' => now()->toDateString(),
                        'chiefComplaints' => '',
                        'onExamination' => '',
                        'advice' => '',
                        'rxText' => "Write medicines here after saving profile.\nLogo and clinic details come from settings above.",
                        'followUpDays' => null,
                        'items' => [],
                    ])
                </div>
            </div>

            <div class="card p-5">
                <h2 class="mb-3 text-lg font-bold">Appointment link + QR</h2>
                <p class="break-all text-sm text-[var(--muted)]">{{ $user->appointment_url }}</p>
                @if ($user->appointment_url)
                    <div class="mt-4 inline-block w-[180px] rounded-2xl bg-white p-3 ring-1 ring-slate-200">
                        {!! \App\Support\QrSvg::render($user->appointment_url, 160) !!}
                    </div>
                @endif
                <button class="btn-primary mt-4" type="button" onclick="navigator.clipboard.writeText(@js($user->appointment_url))">Copy link</button>
            </div>
        </div>
    </div>
@endsection
