@extends('layouts.app')

@section('meta_robots', 'noindex,nofollow')

@section('body')
    @php
        $user = auth()->user();
        $links = [
            ['href' => route('dashboard'), 'label' => 'Home', 'icon' => '▦', 'active' => request()->routeIs('dashboard')],
            ['href' => route('prescriptions.index'), 'label' => 'Prescriptions', 'icon' => '℞', 'active' => request()->routeIs('prescriptions.*')],
            ['href' => route('appointments.index'), 'label' => 'Appointments', 'icon' => '◷', 'active' => request()->routeIs('appointments.*')],
            ['href' => route('availability.index'), 'label' => 'Hours', 'icon' => '⚙', 'active' => request()->routeIs('availability.*')],
            ['href' => route('profile.edit'), 'label' => 'Profile', 'icon' => '☺', 'active' => request()->routeIs('profile.*')],
        ];
    @endphp
    <div class="doctor-shell min-h-screen pb-[4.75rem] md:pb-0 md:p-4">
        <div class="mx-auto flex max-w-[1400px] md:gap-4">
            <aside class="no-print relative z-40 sticky top-4 hidden h-[calc(100vh-2rem)] w-[4.5rem] shrink-0 md:block">
                <div class="doctor-rail group/rail absolute inset-y-0 left-0 flex w-[4.5rem] flex-col overflow-hidden rounded-[24px] bg-[var(--navy)] py-4 text-white shadow-[0_18px_40px_rgba(15,27,45,0.28)] transition-[width] duration-200 ease-out hover:w-52 hover:shadow-[0_22px_50px_rgba(15,27,45,0.38)] focus-within:w-52">
                    <div class="mb-6 flex h-10 items-center gap-3 px-3">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-blue-400 to-blue-700 text-xs font-extrabold shadow-lg">AR</div>
                        <span class="nav-rail-label truncate text-sm font-bold">AR Dental Care</span>
                    </div>
                    <nav class="flex flex-1 flex-col gap-1.5 px-2">
                        @foreach ($links as $link)
                            <a href="{{ $link['href'] }}"
                               aria-label="{{ $link['label'] }}"
                               class="nav-dot flex h-10 items-center gap-3 rounded-xl px-2 text-base {{ $link['active'] ? 'bg-[var(--blue)] text-white shadow-[0_8px_18px_rgba(47,111,237,0.45)]' : 'text-white/65 hover:bg-white/10 hover:text-white' }}">
                                <span class="grid h-6 w-6 shrink-0 place-items-center" aria-hidden>{{ $link['icon'] }}</span>
                                <span class="nav-rail-label truncate text-sm font-semibold">{{ $link['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                    <form method="POST" action="{{ route('logout') }}" class="mt-auto px-2">
                        @csrf
                        <button type="submit" class="nav-dot flex h-10 w-full items-center gap-3 rounded-xl px-2 text-white/65 hover:bg-white/10 hover:text-white" aria-label="Logout">
                            <span class="grid h-6 w-6 shrink-0 place-items-center" aria-hidden>⎋</span>
                            <span class="nav-rail-label truncate text-sm font-semibold">Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="min-w-0 flex-1 px-3 pt-3 md:px-0 md:pt-0">
                <header class="no-print mb-3 flex items-center justify-between gap-3 rounded-[18px] border border-white/70 bg-white/90 px-3 py-2.5 shadow-sm backdrop-blur sm:px-4">
                    <div class="min-w-0">
                        <div class="brand truncate text-base font-extrabold tracking-tight text-[var(--navy)] sm:text-lg">AR Dental Care</div>
                        <div class="truncate text-[10px] font-medium uppercase tracking-[0.14em] text-[var(--muted)] sm:text-[11px]">
                            {{ $user->clinic_name ?: 'Clinic management' }}
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2">
                        <div class="hidden text-right sm:block">
                            <div class="max-w-[10rem] truncate text-sm font-semibold leading-tight md:max-w-none">{{ $user->name }}</div>
                            <div class="truncate text-[11px] text-[var(--muted)]">{{ $user->specialty ?: 'Doctor' }}</div>
                        </div>
                        <div class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-[var(--navy)] text-sm font-bold text-white ring-2 ring-blue-100">
                            @if ($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="md:hidden">
                            @csrf
                            <button type="submit" class="btn-ghost">Out</button>
                        </form>
                    </div>
                </header>

                @if (session('status'))
                    <div class="no-print alert-ok mb-3">{{ session('status') }}</div>
                @endif

                <div class="page-enter">
                    @yield('content')
                </div>
            </div>
        </div>

        <nav class="no-print mobile-tabbar fixed inset-x-0 bottom-0 z-40 border-t border-slate-200/80 bg-white/95 px-2 pt-1.5 backdrop-blur md:hidden">
            <div class="mx-auto flex max-w-lg items-stretch justify-between gap-0.5">
                @foreach ($links as $link)
                    <a href="{{ $link['href'] }}" class="flex min-w-0 flex-1 flex-col items-center gap-0.5 rounded-xl px-1 py-1.5 text-[10px] font-semibold {{ $link['active'] ? 'text-[var(--blue)]' : 'text-slate-500' }}">
                        <span class="text-base leading-none" aria-hidden>{{ $link['icon'] }}</span>
                        <span class="truncate">{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
@endsection
