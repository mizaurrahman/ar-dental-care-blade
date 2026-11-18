@extends('layouts.guest')

@section('title', 'Verify — AR Dental Care')

@section('content')
    <div class="grid min-h-screen place-items-center bg-[#dfe7f3] p-4">
        <div class="w-full max-w-lg rounded-[28px] bg-white p-8 shadow-2xl">
            <div class="brand text-lg font-bold text-[var(--blue)]">AR Dental Care</div>
            <h1 class="mt-2 text-3xl font-extrabold text-[var(--navy)]">Verify account</h1>
            <p class="mt-2 text-sm text-[var(--muted)]">
                {{ $channel === 'email' ? 'Enter the email verification token from your inbox.' : 'Enter the OTP sent to your phone.' }}
            </p>

            @if (session('appointment_url'))
                <div class="mt-4 rounded-xl bg-emerald-50 px-3 py-3 text-sm text-emerald-800">
                    <div class="font-semibold">{{ session('clinic_name', 'Your clinic') }} appointment link is ready</div>
                    <div class="mt-1 break-all">{{ session('appointment_url') }}</div>
                    <div class="mt-1 text-xs text-emerald-700">After verify, open Profile or Dashboard for your QR code.</div>
                </div>
            @endif

            @if (session('status'))
                <div class="mt-4 alert-ok">{{ session('status') }}</div>
            @endif

            <form class="mt-6 space-y-4" method="POST" action="{{ route('verify') }}">
                @csrf
                <div>
                    <label class="label" for="channel">Channel</label>
                    <select id="channel" class="input" name="channel">
                        <option value="sms" @selected($channel === 'sms')>SMS OTP</option>
                        <option value="email" @selected($channel === 'email')>Email link/token</option>
                    </select>
                </div>
                <div>
                    <label class="label" for="email">Email</label>
                    <input id="email" class="input" name="email" value="{{ $email }}">
                </div>
                <div>
                    @include('partials.phone-input', ['id' => 'phone', 'name' => 'phone', 'label' => 'Phone', 'value' => $phone])
                </div>
                <div>
                    <label class="label" for="code">{{ $channel === 'email' ? 'Token' : 'OTP' }}</label>
                    <input id="code" class="input" name="code" required value="{{ $code }}">
                </div>
                @if (session('dev_otp'))
                    <div class="rounded-xl bg-blue-50 px-3 py-2 text-sm text-blue-700">Dev OTP: {{ session('dev_otp') }}</div>
                @endif
                @if ($errors->any())
                    <div class="rounded-xl bg-red-50 px-3 py-2 text-sm text-red-600">{{ $errors->first() }}</div>
                @endif
                <button class="btn-primary w-full" type="submit">Verify</button>
            </form>

            <form class="mt-3" method="POST" action="{{ route('verify.resend') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="phone" value="{{ $phone }}">
                <button class="btn-ghost w-full" type="submit">Resend verification</button>
            </form>

            <p class="mt-6 text-sm"><a class="text-[var(--blue)]" href="{{ route('login') }}">Back to login</a></p>
        </div>
    </div>
@endsection
