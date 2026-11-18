@extends('layouts.app')

@section('title', 'AR Dental Care — Appointments & prescriptions')
@section('meta_description', 'AR Dental Care helps clinics manage appointments, availability, and pad-style prescriptions in one place.')
@section('canonical', route('home'))
@section('body_class', 'home-page')

@section('json_ld')
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Dentist',
    'name' => 'AR Dental Care',
    'url' => route('home'),
    'description' => 'Dental clinic software for appointments, availability, and prescriptions.',
    'logo' => asset('images/ar-dental-logo.svg'),
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
@endsection

@section('body')
    <div class="home-hero relative isolate min-h-[100dvh] overflow-hidden text-white">
        <div
            class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('https://images.unsplash.com/photo-1629909613654-28e377c37b09?auto=format&fit=crop&w=2000&q=80')"
            aria-hidden="true"
        ></div>
        <div class="home-hero__veil absolute inset-0" aria-hidden="true"></div>

        <header class="relative z-10 flex items-center justify-between gap-3 px-4 pb-2 pt-[max(1rem,env(safe-area-inset-top))] sm:px-6 lg:px-10">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 text-white no-underline hover:text-white">
                <img src="{{ asset('images/ar-dental-logo.svg') }}" alt="" class="h-9 w-9 sm:h-10 sm:w-10" width="40" height="40">
                <span class="brand text-base font-extrabold tracking-tight sm:text-lg">AR Dental Care</span>
            </a>
            @auth
                <a href="{{ route('dashboard') }}" class="btn-primary shrink-0 px-4 py-2.5 text-sm">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-primary shrink-0 px-4 py-2.5 text-sm">Login</a>
            @endauth
        </header>

        <main class="relative z-10 flex min-h-[calc(100dvh-4.5rem)] flex-col justify-end px-4 pb-[max(2rem,env(safe-area-inset-bottom))] pt-16 sm:px-6 sm:pb-12 lg:justify-center lg:px-10 lg:pb-16">
            <div class="mx-auto w-full max-w-3xl lg:mx-0">
                <p class="home-fade brand mb-3 text-2xl font-extrabold leading-none tracking-tight text-white sm:text-4xl md:text-5xl lg:text-6xl">
                    AR Dental Care
                </p>
                <h1 class="home-fade home-fade--delay-1 max-w-xl text-lg font-semibold leading-snug text-white/95 sm:text-2xl md:text-3xl">
                    Appointments and prescriptions, built for your clinic.
                </h1>
                <p class="home-fade home-fade--delay-2 mt-3 max-w-md text-sm leading-relaxed text-white/80 sm:mt-4 sm:text-base">
                    Schedule patients, set availability, and share pad-style prescriptions from one calm workspace.
                </p>

                <div class="home-fade home-fade--delay-3 mt-7 flex flex-col gap-3 sm:mt-8 sm:flex-row sm:items-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary w-full px-6 py-3.5 text-base sm:w-auto">
                            Open dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary w-full px-6 py-3.5 text-base sm:w-auto">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="home-secondary-cta w-full px-6 py-3.5 text-center text-base sm:w-auto">
                            Create account
                        </a>
                    @endauth
                </div>
            </div>
        </main>
    </div>
@endsection
