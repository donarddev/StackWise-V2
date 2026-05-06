@props(['summary', 'title' => 'Project Summary'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
            <p class="mt-1 text-sm text-slate-400">Core project information saved with the recommendation.</p>
        </div>
        <x-recommendation.confidence-badge :score="$summary['confidence_score'] ?? 0" />
    </div>

    <dl class="mt-5 space-y-3 text-sm text-slate-300">
        @foreach ($summary as $label => $value)
            <div class="flex items-start justify-between gap-4 border-b border-white/10 pb-3 last:border-b-0 last:pb-0">
                <dt class="text-slate-400">{{ str_replace('_', ' ', ucwords($label, '_')) }}</dt>
                <dd class="max-w-[60%] text-right text-white">{{ is_array($value) ? implode(', ', $value) : $value }}</dd>
            </div>
        @endforeach
    </dl>
</article>