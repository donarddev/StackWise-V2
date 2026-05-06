@props(['label', 'value', 'helper' => null])

<article class="rounded-3xl border border-white/10 bg-white/5 p-5 shadow-xl shadow-slate-950/25">
    <p class="text-xs uppercase tracking-wider text-slate-400">{{ $label }}</p>
    <p class="mt-3 text-3xl font-bold text-white">{{ $value }}</p>
    @if ($helper)
        <p class="mt-2 text-sm text-slate-400">{{ $helper }}</p>
    @endif
</article>
