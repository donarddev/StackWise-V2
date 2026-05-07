@php
    $guestItems = [
        ['label' => 'Home', 'route' => 'home', 'active' => ['home']],
        ['label' => 'Documentation', 'route' => 'documentation.index', 'active' => ['documentation.index']],
        ['label' => 'About', 'route' => 'about', 'active' => ['about']],
        ['label' => 'Login', 'route' => 'login', 'active' => ['login']],
        ['label' => 'Register', 'route' => 'register', 'active' => ['register']],
    ];

    $authItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => ['dashboard']],
        [
            'label' => 'Generate',
            'route' => 'recommendation.create',
            'active' => ['recommendation.index', 'recommendation.create', 'recommendation.generate'],
        ],
        [
            'label' => 'History',
            'route' => 'recommendation.history',
            'active' => ['recommendation.history', 'recommendation.show'],
        ],
        ['label' => 'Documentation', 'route' => 'documentation.index', 'active' => ['documentation.index']],
        ['label' => 'Chatbot', 'route' => 'chatbot.index', 'active' => ['chatbot.index']],
    ];

    $linkBase = 'rounded-full px-4 py-2 text-sm transition';
    $linkActive = 'bg-white text-slate-950';
    $linkIdle = 'text-slate-300 hover:bg-white/10 hover:text-white';

    $authGenerate = 'rounded-full bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-200 ring-1 ring-inset ring-emerald-400/25 transition hover:bg-emerald-400/15 hover:text-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-400/35 focus:ring-offset-2 focus:ring-offset-slate-950';
    $guestLogin = 'rounded-full border border-white/15 bg-transparent px-4 py-2 text-sm text-slate-200 transition hover:border-emerald-400/50 hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400/35 focus:ring-offset-2 focus:ring-offset-slate-950';
    $guestRegister = 'rounded-full bg-gradient-to-r from-emerald-400 to-teal-500 px-4 py-2 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/15 transition hover:from-emerald-300 hover:to-teal-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/60 focus:ring-offset-2 focus:ring-offset-slate-950';
@endphp

<header
    x-data="{ open: false, userMenuOpen: false, showLogoutModal: false }"
    @keydown.escape.window="showLogoutModal = false"
    class="relative z-50 border-b border-white/10 bg-slate-950/70 backdrop-blur"
>
    <div class="mx-auto w-full max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center gap-3">
                <img
                    src="{{ asset('images/StackWise_Logo.png') }}"
                    alt="StackWise AI Logo"
                    class="h-12 w-12 shrink-0 object-contain"
                    width="48"
                    height="48"
                />
                <div>
                    <p class="text-sm font-semibold tracking-wide text-white">StackWise AI</p>
                    <p class="text-xs text-slate-400">Decision support for student projects</p>
                </div>
            </a>

            <button
                type="button"
                class="inline-flex items-center rounded-full border border-white/10 px-4 py-2 text-sm text-slate-300 transition hover:bg-white/10 hover:text-white md:hidden"
                @click="open = ! open"
                :aria-expanded="open.toString()"
                aria-label="Toggle navigation"
            >
                Menu
            </button>

            @auth
                <nav class="hidden flex-wrap items-center justify-end gap-2 md:flex">
                    @foreach ($authItems as $item)
                        @php
                            $isActive = request()->routeIs(...$item['active']);
                        @endphp
                        @if ($item['route'] === 'recommendation.create' && ! $isActive)
                            <a href="{{ route($item['route']) }}" class="{{ $authGenerate }}">
                                {{ $item['label'] }}
                            </a>
                        @else
                            <a
                                href="{{ route($item['route']) }}"
                                class="{{ $linkBase }} {{ $isActive ? $linkActive : $linkIdle }}"
                            >
                                {{ $item['label'] }}
                            </a>
                        @endif
                    @endforeach

                    <form x-ref="logoutForm" method="POST" action="{{ route('logout') }}" class="hidden">
                        @csrf
                    </form>

                    <div class="relative">
                        <button
                            type="button"
                            class="group inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-3 py-2 text-left text-sm text-slate-200 transition hover:bg-white/10"
                            @click="userMenuOpen = ! userMenuOpen"
                            :aria-expanded="userMenuOpen.toString()"
                            aria-label="Open user menu"
                        >
                            <x-ui.user-avatar :name="auth()->user()->name" size="sm" />
                            <div class="hidden min-w-0 sm:block">
                                <p class="max-w-[12rem] truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                                <p class="max-w-[12rem] truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                            </div>
                            <svg class="h-4 w-4 text-slate-400 transition group-hover:text-slate-200" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div
                            x-show="userMenuOpen"
                            x-cloak
                            @click.away="userMenuOpen = false"
                            class="absolute right-0 mt-3 w-72 overflow-hidden rounded-3xl border border-white/10 bg-slate-900/95 shadow-2xl shadow-slate-950/60 ring-1 ring-white/5 backdrop-blur"
                        >
                            <div class="flex items-center gap-3 border-b border-white/10 px-4 py-4">
                                <x-ui.user-avatar :name="auth()->user()->name" size="md" />
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        Member since {{ auth()->user()->created_at?->format('M Y') }}
                                        <span class="mx-2 text-slate-600">•</span>
                                        <span class="inline-flex items-center gap-1 text-emerald-200/90">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                                            Active
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="p-2">
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10">
                                    <span class="text-slate-400">Profile</span>
                                </a>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10">
                                    <span class="text-slate-400">Settings</span>
                                </a>
                                <a href="{{ route('recommendation.history') }}" class="flex items-center gap-3 rounded-2xl px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10">
                                    <span class="text-slate-400">Recommendation History</span>
                                </a>
                            </div>

                            <div class="border-t border-white/10 p-2">
                                <button
                                    type="button"
                                    class="w-full rounded-2xl px-3 py-2 text-left text-sm font-semibold text-rose-200 transition hover:bg-rose-500/15"
                                    @click="userMenuOpen = false; showLogoutModal = true"
                                >
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </nav>
            @endauth

            @guest
                <nav class="hidden flex-wrap items-center justify-end gap-2 md:flex">
                    @foreach ($guestItems as $item)
                        @php
                            $isActive = request()->routeIs(...$item['active']);
                        @endphp

                        @if ($item['route'] === 'login')
                            <a href="{{ route($item['route']) }}" class="{{ $guestLogin }}">
                                {{ $item['label'] }}
                            </a>
                        @elseif ($item['route'] === 'register')
                            <a href="{{ route($item['route']) }}" class="{{ $guestRegister }}">
                                {{ $item['label'] }}
                            </a>
                        @else
                            <a
                                href="{{ route($item['route']) }}"
                                class="{{ $linkBase }} {{ $isActive ? $linkActive : $linkIdle }}"
                            >
                                {{ $item['label'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            @endguest
        </div>

        @auth
            <nav x-show="open" x-cloak class="mt-4 grid gap-2 md:hidden">
                <div class="rounded-3xl border border-white/10 bg-slate-900/60 p-4">
                    <div class="flex items-center gap-3">
                        <x-ui.user-avatar :name="auth()->user()->name" size="md" />
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-white">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                            <p class="mt-1 text-[11px] text-slate-500">Member since {{ auth()->user()->created_at?->format('M Y') }}</p>
                        </div>
                    </div>
                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        <a href="{{ route('profile.edit') }}" class="rounded-2xl border border-white/10 px-4 py-2 text-sm text-slate-200 transition hover:bg-white/10">
                            Profile
                        </a>
                        <a href="{{ route('recommendation.history') }}" class="rounded-2xl border border-white/10 px-4 py-2 text-sm text-slate-200 transition hover:bg-white/10">
                            History
                        </a>
                    </div>
                </div>

                @foreach ($authItems as $item)
                    @php
                        $isActive = request()->routeIs(...$item['active']);
                    @endphp
                    @if ($item['route'] === 'recommendation.create' && ! $isActive)
                        <a
                            href="{{ route($item['route']) }}"
                            class="rounded-2xl bg-emerald-400/10 px-4 py-3 text-sm font-semibold text-emerald-200 ring-1 ring-inset ring-emerald-400/25 transition hover:bg-emerald-400/15 hover:text-emerald-100"
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        <a
                            href="{{ route($item['route']) }}"
                            class="rounded-2xl border border-white/10 px-4 py-3 text-sm transition {{ $isActive ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach

                <form method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>

                <button
                    type="button"
                    class="rounded-2xl border border-white/10 px-4 py-3 text-left text-sm text-slate-300 transition hover:bg-white/10 hover:text-white"
                    @click="showLogoutModal = true"
                >
                    Logout
                </button>
            </nav>
        @endauth

        @guest
            <nav x-show="open" x-cloak class="mt-4 grid gap-2 md:hidden">
                @foreach ($guestItems as $item)
                    @php
                        $isActive = request()->routeIs(...$item['active']);
                    @endphp

                    @if ($item['route'] === 'login')
                        <a
                            href="{{ route($item['route']) }}"
                            class="rounded-2xl border border-white/15 px-4 py-3 text-sm font-medium text-slate-200 transition hover:border-emerald-400/50 hover:bg-white/10 hover:text-white"
                        >
                            {{ $item['label'] }}
                        </a>
                    @elseif ($item['route'] === 'register')
                        <a
                            href="{{ route($item['route']) }}"
                            class="rounded-2xl bg-gradient-to-r from-emerald-400 to-teal-500 px-4 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/15 transition hover:from-emerald-300 hover:to-teal-400"
                        >
                            {{ $item['label'] }}
                        </a>
                    @else
                        <a
                            href="{{ route($item['route']) }}"
                            class="rounded-2xl border border-white/10 px-4 py-3 text-sm transition {{ $isActive ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>
        @endguest
    </div>

    @auth
        <div
            x-show="showLogoutModal"
            x-cloak
            class="fixed inset-0 z-[60] flex items-center justify-center px-4 py-8"
            aria-live="polite"
        >
            <div
                class="absolute inset-0 bg-slate-950/75 backdrop-blur-sm"
                aria-hidden="true"
                @click="showLogoutModal = false"
            ></div>

            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="logout-modal-title"
                class="relative w-full max-w-md overflow-hidden rounded-3xl border border-white/10 bg-slate-900/90 shadow-2xl shadow-slate-950/60 ring-1 ring-white/5 backdrop-blur"
                @click.away="showLogoutModal = false"
            >
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald-400/40 to-transparent"></div>

                <div class="p-6 sm:p-7">
                    <div class="flex items-start justify-between gap-4">
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300/70">
                                Confirmation
                            </p>
                            <h2 id="logout-modal-title" class="text-xl font-semibold tracking-tight text-white">
                                Are you sure you want to log out?
                            </h2>
                        </div>

                        <button
                            type="button"
                            class="rounded-full border border-white/10 px-3 py-2 text-sm text-slate-300 transition hover:bg-white/10 hover:text-white"
                            @click="showLogoutModal = false"
                            aria-label="Close logout confirmation"
                        >
                            Close
                        </button>
                    </div>

                    <p class="mt-4 text-sm leading-6 text-slate-300">
                        You will need to log in again to access your StackWise workspace.
                    </p>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-full border border-white/15 bg-transparent px-5 py-3 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400/35 focus:ring-offset-2 focus:ring-offset-slate-950"
                            @click="showLogoutModal = false"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center rounded-full bg-rose-500/15 px-5 py-3 text-sm font-semibold text-rose-200 ring-1 ring-inset ring-rose-400/25 transition hover:bg-rose-500/20 hover:text-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-400/50 focus:ring-offset-2 focus:ring-offset-slate-950"
                            @click="$refs.logoutForm?.submit()"
                        >
                            Log out
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endauth
</header>
