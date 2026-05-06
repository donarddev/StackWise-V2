@props([
    'recommendation',
])

@php
    $r = $recommendation;
    $stack =
        $r->recommended_language.' + '.$r->recommended_framework.' · '.$r->recommended_sdlc_model;
@endphp

<article class="rounded-2xl border border-white/10 bg-slate-900/50 p-5 shadow-md shadow-slate-950/25 transition hover:border-emerald-400/20">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0">
            <h3 class="text-base font-semibold text-white">{{ $r->project_name }}</h3>
            <p class="mt-1 text-xs text-slate-400">{{ $r->project_type }}</p>
        </div>
        <x-ui.badges.confidence :score="$r->confidence_score" />
    </div>

    <p class="mt-3 text-sm text-slate-300">
        <span class="text-slate-500">Stack:</span>
        {{ $stack }}
    </p>

    <p class="mt-2 text-xs text-slate-500">{{ $r->created_at?->format('M d, Y') }}</p>

    <div class="mt-4">
        <x-ui.button-link :href="route('recommendation.show', $r)" variant="primary" class="w-full justify-center text-center sm:w-auto">
            View details
        </x-ui.button-link>
    </div>
</article>
