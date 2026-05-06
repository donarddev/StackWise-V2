@props([
    'brandHeading',
    'brandDescription',
    'brandHighlights' => [],
    'panelTitle',
    'panelSubtitle' => null,
    'panelEyebrow' => 'Authentication',
])

<div class="mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-6 sm:px-6 lg:px-8">
    <div class="grid w-full gap-6 lg:grid-cols-[1.05fr_0.95fr]">
        <section class="relative hidden overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-slate-950/40 lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400"></div>

            <div class="flex items-start justify-between gap-6">
                <div class="flex items-center gap-3">
                    <x-application-logo class="h-11 w-auto text-emerald-300" />
                    <div>
                        <p class="text-sm font-semibold tracking-[0.2em] text-emerald-300/90">STACKWISE AI</p>
                        <p class="text-xs text-slate-400">Decision support for student projects</p>
                    </div>
                </div>

                <a href="{{ route('home') }}" class="inline-flex items-center rounded-full border border-white/10 bg-slate-950/60 px-3 py-2 text-xs font-medium text-slate-300 transition hover:bg-white/10 hover:text-white">
                    Back to Home
                </a>
            </div>

            <div class="max-w-xl space-y-5">
                <span class="inline-flex rounded-full border border-emerald-400/20 bg-emerald-400/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-emerald-200/90">
                    Academic planning, simplified
                </span>
                <div class="space-y-4">
                    <h1 class="text-4xl font-bold tracking-tight text-white">{{ $brandHeading }}</h1>
                    <p class="max-w-lg text-sm leading-7 text-slate-300">{{ $brandDescription }}</p>
                </div>
            </div>

            <div class="grid gap-3 pt-8">
                @foreach ($brandHighlights as $highlight)
                    <div class="flex gap-4 rounded-2xl border border-white/10 bg-slate-950/50 p-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-400/10 text-sm font-semibold text-emerald-300 ring-1 ring-inset ring-emerald-400/20">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <p class="font-medium text-white">{{ $highlight['title'] }}</p>
                            <p class="text-sm leading-6 text-slate-400">{{ $highlight['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="flex w-full items-center justify-center">
            <div class="w-full max-w-lg rounded-[2rem] border border-white/10 bg-slate-900/85 p-6 shadow-2xl shadow-slate-950/50 backdrop-blur-xl sm:p-8">
                <div class="mb-6 flex items-start justify-between gap-4 lg:hidden">
                    <div class="flex items-center gap-3">
                        <x-application-logo class="h-10 w-auto text-emerald-300" />
                        <div>
                            <p class="text-xs font-semibold tracking-[0.2em] text-emerald-300/90">STACKWISE AI</p>
                            <p class="text-xs text-slate-400">Decision support for student projects</p>
                        </div>
                    </div>

                    <a href="{{ route('home') }}" class="text-sm text-slate-300 transition hover:text-white">
                        Back to Home
                    </a>
                </div>

                <div class="mb-6 space-y-2">
                    <p class="text-xs uppercase tracking-[0.35em] text-emerald-300/70">{{ $panelEyebrow }}</p>
                    <h2 class="text-3xl font-semibold tracking-tight text-white">{{ $panelTitle }}</h2>
                    @if ($panelSubtitle)
                        <p class="text-sm leading-6 text-slate-400">{{ $panelSubtitle }}</p>
                    @endif
                </div>

                {{ $slot }}
            </div>
        </section>
    </div>
</div>
