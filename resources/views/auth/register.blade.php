@extends('layouts.guest')

@section('title', 'Sign up — AR Dental Care')

@section('content')
    <div class="grid min-h-screen place-items-center p-3 sm:p-4">
        <div class="page-enter grid w-full max-w-5xl overflow-hidden rounded-[22px] bg-white shadow-2xl sm:rounded-[28px] md:grid-cols-2">
            <div class="hidden bg-[var(--blue)] p-10 text-white md:flex md:flex-col md:justify-center">
                <div class="brand text-2xl font-extrabold">AR Dental Care</div>
                <h2 class="mt-6 text-3xl font-bold">Register your own clinic</h2>
                <p class="mt-3 text-white/85">Every doctor gets a private dashboard, clinic profile, and a unique appointment link with QR code.</p>
            </div>
            <div class="max-h-[min(92vh,920px)] overflow-auto p-5 sm:p-8 md:p-10">
                <h1 class="text-2xl font-extrabold text-[var(--navy)] sm:text-3xl">Sign Up</h1>
                <p class="mt-1 text-sm text-[var(--muted)]">Create your clinic account — not shared with other doctors.</p>

                <form class="mt-5 space-y-3" method="POST" action="{{ route('register') }}" id="register-form">
                    @csrf
                    <input type="hidden" name="mode" id="mode" value="{{ old('mode', 'email') }}">
                    <div class="flex gap-2">
                        <button type="button" id="mode-email" class="rounded-full px-4 py-2 text-sm font-semibold" onclick="setMode('email')">Email</button>
                        <button type="button" id="mode-phone" class="rounded-full px-4 py-2 text-sm font-semibold" onclick="setMode('phone')">Phone</button>
                    </div>

                    <div>
                        <label class="label" for="name">Doctor full name</label>
                        <input id="name" class="input" name="name" required value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="label" for="clinic_name">Clinic name</label>
                        <input id="clinic_name" class="input" name="clinic_name" required placeholder="e.g. Smile Bright Dental" value="{{ old('clinic_name') }}">
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label" for="specialty">Specialty</label>
                            <input id="specialty" class="input" name="specialty" value="{{ old('specialty', 'Oral and Dental Surgeon') }}">
                        </div>
                        <div>
                            <label class="label" for="appointment_slug">Appointment link slug (optional)</label>
                            <input id="appointment_slug" class="input" name="appointment_slug" placeholder="auto from clinic name" value="{{ old('appointment_slug') }}">
                        </div>
                    </div>
                    <div>
                        <label class="label" for="clinic_address">Clinic address (optional)</label>
                        <input id="clinic_address" class="input" name="clinic_address" value="{{ old('clinic_address') }}">
                    </div>
                    <div>
                        @include('partials.phone-input', ['id' => 'clinic_phone', 'name' => 'clinic_phone', 'label' => 'Clinic phone (optional)', 'value' => old('clinic_phone')])
                    </div>
                    <div id="email-field">
                        <label class="label" for="email">Login email</label>
                        <input id="email" class="input" type="email" name="email" value="{{ old('email') }}">
                    </div>
                    <div id="phone-field" hidden>
                        @include('partials.phone-input', ['id' => 'phone', 'name' => 'phone', 'label' => 'Phone', 'value' => old('phone')])
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label" for="password">Password</label>
                            <input id="password" class="input" type="password" name="password" required minlength="6">
                        </div>
                        <div>
                            <label class="label" for="password_confirmation">Confirm password</label>
                            <input id="password_confirmation" class="input" type="password" name="password_confirmation" required>
                        </div>
                    </div>
                    @if ($errors->any())
                        <div class="alert-error">{{ $errors->first() }}</div>
                    @endif
                    <button class="btn-primary w-full" type="submit">Create clinic account</button>
                </form>

                <p class="mt-5 text-sm text-[var(--muted)]">
                    Already have an account? <a class="font-semibold text-[var(--blue)]" href="{{ route('login') }}">Login</a>
                </p>
            </div>
        </div>
    </div>
    <script>
        function setMode(mode) {
            document.getElementById('mode').value = mode;
            const email = mode === 'email';
            document.getElementById('email-field').hidden = !email;
            document.getElementById('phone-field').hidden = email;
            document.getElementById('email').required = email;
            document.getElementById('phone').required = !email;
            document.getElementById('mode-email').className = 'rounded-full px-4 py-2 text-sm font-semibold ' + (email ? 'bg-[var(--blue)] text-white' : 'bg-slate-100');
            document.getElementById('mode-phone').className = 'rounded-full px-4 py-2 text-sm font-semibold ' + (!email ? 'bg-[var(--blue)] text-white' : 'bg-slate-100');
        }
        setMode(@json(old('mode', 'email')));
    </script>
@endsection
