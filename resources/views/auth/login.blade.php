@extends('layouts.guest')

@section('title', 'Login — AR Dental Care')

@section('content')
    <div class="grid min-h-screen place-items-center p-3 sm:p-4">
        <div class="page-enter grid w-full max-w-5xl overflow-hidden rounded-[22px] bg-white shadow-2xl sm:rounded-[28px] md:grid-cols-2">
            <div class="relative hidden md:block">
                <div class="h-1/2 bg-cover bg-center" style="background-image: url(https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=1200&q=80)"></div>
                <div class="flex h-1/2 flex-col justify-center bg-[var(--blue)] p-10 text-white">
                    <div class="brand mb-4 text-2xl font-extrabold">AR Dental Care</div>
                    <div class="mb-2 h-16 w-1 rounded bg-white/70"></div>
                    <h2 class="text-3xl font-bold leading-tight">Welcome to AR Dental Care</h2>
                    <p class="mt-3 max-w-sm text-sm text-white/85">Appointments, prescriptions, and patient scheduling in one place for your dental clinic.</p>
                </div>
            </div>

            <div class="flex flex-col justify-center p-6 sm:p-8 md:p-12">
                <div class="brand mb-2 text-base font-bold text-[var(--blue)] sm:text-lg">AR Dental Care</div>
                <h1 class="text-3xl font-extrabold text-[var(--navy)] sm:text-4xl">Login</h1>
                <p class="mt-2 text-sm text-[var(--muted)]">Enter your credentials to login to your account</p>

                <form class="mt-6 space-y-4" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div>
                        <label class="label" for="login">Email or phone</label>
                        <input id="login" class="input" name="login" value="{{ old('login', 'doctor@ardental.local') }}" placeholder="doctor@ardental.local" autocomplete="username" required>
                    </div>
                    <div>
                        <label class="label" for="password">Password</label>
                        <div class="relative">
                            <input id="password" class="input pr-16" type="password" name="password" value="{{ old('password', 'password') }}" autocomplete="current-password" required>
                            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-[var(--muted)]" onclick="const i=document.getElementById('password'); const show=i.type==='password'; i.type=show?'text':'password'; this.textContent=show?'Hide':'Show';">Show</button>
                        </div>
                    </div>
                    @if ($errors->any())
                        <div class="rounded-xl bg-red-50 px-3 py-2 text-sm text-red-600">{{ $errors->first() }}</div>
                    @endif
                    <button class="btn-primary w-full" type="submit">Sign In</button>
                </form>

                <p class="mt-6 text-sm text-[var(--muted)]">
                    Don't have an account? <a class="font-semibold text-[var(--blue)]" href="{{ route('register') }}">Sign Up</a>
                </p>
                <p class="mt-3 rounded-xl bg-slate-50 p-3 text-xs text-[var(--muted)]">
                    Demo: <strong>doctor@ardental.local</strong> / <strong>password</strong>
                </p>
            </div>
        </div>
    </div>
@endsection
