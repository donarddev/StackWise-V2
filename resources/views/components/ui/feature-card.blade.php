@props([
    'title',
    'description',
    'badge' => null,
    'badgeTone' => 'emerald',
    'compact' => false,
])

<article {{ $attributes->merge(['class' => 'group relative h-full overflow-hidden rounded-3xl border border-white/10 bg-slate-900/60 p-5 shadow-xl shadow-slate-950/25 transition duration-300 hover:-translate-y-1 hover:border-emerald-400/20 hover:bg-slate-900/80']) }}>
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald-400/35 to-transparent"></div>

    <div class="flex items-start justify-between gap-3">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl border border-emerald-400/15 bg-emerald-400/10 text-emerald-200 ring-1 ring-inset ring-white/5">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-300 shadow-[0_0_18px_rgba(52,211,153,0.65)]"></span>
        </div>

        @if ($badge)
            <x-ui.badge :tone="$badgeTone">{{ $badge }}</x-ui.badge>
        @endif
    </div>

    <div class="mt-5 space-y-3">
        <h3 class="{{ $compact ? 'text-base' : 'text-lg' }} font-semibold text-white">
            {{ $title }}
        </h3>

        <p class="{{ $compact ? 'text-sm leading-6' : 'text-sm leading-7' }} text-slate-300">
            {{ $description }}
        </p>
    </div>
</article>