@props([
    'label',
    'question',
])

<button
    type="button"
    class="rounded-full border border-white/10 bg-slate-900/60 px-3 py-1.5 text-xs font-medium text-slate-200 transition hover:border-emerald-400/35 hover:bg-emerald-400/10 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400/25"
    onclick="window.stackwiseFillChat(@js($question))"
>
    {{ $label }}
</button>
