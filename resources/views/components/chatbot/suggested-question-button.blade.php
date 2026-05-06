@props([
    'question',
])

<button
    type="button"
    class="w-full rounded-2xl border border-white/10 bg-slate-900/70 px-4 py-3 text-left text-sm leading-snug text-slate-200 transition hover:border-emerald-400/30 hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-400/30"
    onclick="window.stackwiseSendChat(@js($question))"
>
    {{ $question }}
</button>
