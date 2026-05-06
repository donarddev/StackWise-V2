@props([
    'items' => [],
])

<div class="rounded-3xl border border-white/10 bg-slate-900/40 p-6 shadow-xl shadow-slate-950/25">
    <h2 class="text-lg font-semibold text-white">What this assistant can help with</h2>
    <ul class="mt-4 space-y-3">
        @foreach ($items as $item)
            <li class="flex gap-3 text-sm text-slate-300">
                <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-emerald-400/30 bg-emerald-400/10 text-xs font-bold text-emerald-200">✓</span>
                <span class="leading-snug">{{ $item }}</span>
            </li>
        @endforeach
    </ul>
</div>
