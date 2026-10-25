@props([
    'title' => 'Research Paper Draft',
    'subtitle' => null,
    'badge' => null,
])

<div
    {{ $attributes->class([
        'relative min-w-0 overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b from-slate-900/80 via-slate-950/90 to-slate-950 shadow-2xl shadow-slate-950/50 ring-1 ring-inset ring-white/5',
    ]) }}
>
    <div
        class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-teal-400/40 to-transparent"
        aria-hidden="true"
    ></div>

    <div class="border-b border-white/10 bg-white/[0.02] px-5 py-5 sm:px-8 sm:py-6">
        <div class="mx-auto flex max-w-4xl flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 space-y-2">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-teal-300/90">
                    Academic manuscript workspace
                </p>
                <h3 class="font-serif text-xl font-semibold tracking-tight text-white sm:text-2xl">
                    {{ $title }}
                </h3>
                @if (filled($subtitle))
                    <p class="max-w-2xl text-sm leading-6 text-slate-400">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>

            @if (filled($badge))
                <x-ui.badge tone="teal" class="shrink-0 self-start">
                    {{ $badge }}
                </x-ui.badge>
            @endif
        </div>
    </div>

    <div class="px-5 py-8 sm:px-8 sm:py-10">
        <div class="mx-auto max-w-4xl space-y-10 font-serif sm:space-y-12">
            {{ $slot }}
        </div>
    </div>
</div>
