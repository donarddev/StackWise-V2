@props([
    'summary' => [],
])

@php
    $avg = $summary['average_confidence'] ?? null;
    $avgDisplay = $avg !== null ? $avg.'%' : '—';
    $lang = $summary['most_recommended_language'] ?? null;
    $fw = $summary['most_recommended_framework'] ?? null;
@endphp

<div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
    <article class="rounded-2xl border border-white/10 bg-slate-900/55 p-4 shadow-lg shadow-slate-950/25">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Saved records</p>
        <p class="mt-2 text-2xl font-semibold text-white">{{ $summary['saved_records'] ?? 0 }}</p>
        <p class="mt-1 text-xs text-slate-500">Matching current filters</p>
    </article>
    <article class="rounded-2xl border border-white/10 bg-slate-900/55 p-4 shadow-lg shadow-slate-950/25">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Average confidence</p>
        <p class="mt-2 text-2xl font-semibold text-white">{{ $avgDisplay }}</p>
        <p class="mt-1 text-xs text-slate-500">Across visible results</p>
    </article>
    <article class="rounded-2xl border border-white/10 bg-slate-900/55 p-4 shadow-lg shadow-slate-950/25">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Most recommended language</p>
        <p class="mt-2 truncate text-lg font-semibold text-emerald-100">{{ $lang ?? '—' }}</p>
    </article>
    <article class="rounded-2xl border border-white/10 bg-slate-900/55 p-4 shadow-lg shadow-slate-950/25">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Most recommended framework</p>
        <p class="mt-2 truncate text-lg font-semibold text-emerald-100">{{ $fw ?? '—' }}</p>
    </article>
    <article class="rounded-2xl border border-emerald-400/20 bg-emerald-400/5 p-4 shadow-lg shadow-emerald-950/10 sm:col-span-2 xl:col-span-1">
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-400">Current page</p>
        <p class="mt-2 text-2xl font-semibold text-white">{{ $summary['current_page'] ?? 1 }}</p>
    </article>
</div>
