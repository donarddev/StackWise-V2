@php
    $navigationItems = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Dashboard', 'route' => 'dashboard.index'],
        ['label' => 'Recommendation', 'route' => 'recommendation.index'],
        ['label' => 'Recommendation History', 'route' => 'recommendation.history'],
        ['label' => 'Documentation', 'route' => 'documentation.index'],
        ['label' => 'Chatbot', 'route' => 'chatbot.index'],
        ['label' => 'About', 'route' => 'about'],
    ];
@endphp

<header x-data="{ open: false }" class="border-b border-white/10 bg-slate-950/70 backdrop-blur">
    <div class="mx-auto w-full max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-400/15 text-sm font-semibold text-emerald-300 ring-1 ring-inset ring-emerald-400/25">
                    SW
                </span>
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

            <nav class="hidden items-center gap-2 md:flex">
                @foreach ($navigationItems as $item)
                    <a
                        href="{{ route($item['route']) }}"
                        class="rounded-full px-4 py-2 text-sm transition {{ request()->routeIs($item['route']) ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>

        <nav x-show="open" x-cloak class="mt-4 grid gap-2 md:hidden">
            @foreach ($navigationItems as $item)
                <a
                    href="{{ route($item['route']) }}"
                    class="rounded-2xl border border-white/10 px-4 py-3 text-sm transition {{ request()->routeIs($item['route']) ? 'bg-white text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>