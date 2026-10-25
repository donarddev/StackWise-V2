@props([
    'label',
    'url',
    'source' => null,
    'description' => null,
])

<article {{ $attributes->class([
    'group flex flex-col gap-3 rounded-2xl border border-white/10 bg-slate-900/70 p-4 ring-1 ring-inset ring-white/5 transition hover:border-emerald-400/25 hover:bg-slate-900/90',
]) }}>
    <div class="flex flex-wrap items-start justify-between gap-2">
        <h4 class="text-sm font-semibold text-white">
            {{ $label }}
        </h4>

        @if (filled($source))
            <span class="rounded-full border border-white/10 bg-white/5 px-2.5 py-0.5 font-sans text-[0.65rem] font-medium uppercase tracking-wider text-slate-400">
                {{ $source }}
            </span>
        @endif
    </div>

    @if (filled($description))
        <p class="text-sm leading-6 text-slate-400">
            {{ $description }}
        </p>
    @endif

    @if (filled($url))
        <a
            href="{{ $url }}"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 self-start rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-3 py-2 font-sans text-xs font-medium text-emerald-200 transition hover:border-emerald-400/40 hover:bg-emerald-400/15 hover:text-emerald-100"
        >
            <span>Open search</span>
            <svg class="h-3.5 w-3.5 shrink-0 opacity-80" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
            </svg>
        </a>
    @endif
</article>
