@props([
    'summary' => ['languages' => 0, 'frameworks' => 0, 'sdlc_models' => 0],
])

<div class="grid gap-3 sm:grid-cols-3">
    <div class="rounded-2xl border border-white/10 bg-slate-900/40 px-4 py-4 shadow-md shadow-slate-950/25">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Programming languages</p>
        <p class="mt-2 text-2xl font-semibold text-white">{{ $summary['languages'] }}</p>
    </div>
    <div class="rounded-2xl border border-white/10 bg-slate-900/40 px-4 py-4 shadow-md shadow-slate-950/25">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-400">Frameworks</p>
        <p class="mt-2 text-2xl font-semibold text-white">{{ $summary['frameworks'] }}</p>
    </div>
    <div class="rounded-2xl border border-white/10 bg-slate-900/40 px-4 py-4 shadow-md shadow-slate-950/25">
        <p class="text-xs font-medium uppercase tracking-wider text-slate-400">SDLC models</p>
        <p class="mt-2 text-2xl font-semibold text-white">{{ $summary['sdlc_models'] }}</p>
    </div>
</div>
