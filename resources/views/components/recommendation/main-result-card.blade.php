@props(['mainRecommendation', 'explanation', 'title' => 'Main Recommendation'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
            <p class="mt-1 text-sm text-slate-400">The strongest stack match based on the project details.</p>
        </div>

        <x-recommendation.confidence-badge :score="$mainRecommendation['confidence_score'] ?? 0" />
    </div>

    <div class="mt-5 grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-slate-900/70 p-4 ring-1 ring-inset ring-white/10">
            <p class="text-xs uppercase tracking-wider text-slate-400">Programming Language</p>
            <p class="mt-2 text-lg font-semibold text-white">{{ $mainRecommendation['language'] ?? '-' }}</p>
        </div>
        <div class="rounded-2xl bg-slate-900/70 p-4 ring-1 ring-inset ring-white/10">
            <p class="text-xs uppercase tracking-wider text-slate-400">Framework</p>
            <p class="mt-2 text-lg font-semibold text-white">{{ $mainRecommendation['framework'] ?? '-' }}</p>
        </div>
        <div class="rounded-2xl bg-slate-900/70 p-4 ring-1 ring-inset ring-white/10">
            <p class="text-xs uppercase tracking-wider text-slate-400">SDLC Model</p>
            <p class="mt-2 text-lg font-semibold text-white">{{ $mainRecommendation['sdlc_model'] ?? '-' }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-center">
            <p class="text-xs uppercase tracking-wider text-emerald-200">Confidence Score</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $mainRecommendation['confidence_score'] ?? 0 }}%</p>
        </div>
    </div>

    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-sm text-emerald-100">
            <p class="font-semibold text-white">Language Reason</p>
            <p class="mt-2">{{ $explanation['language_reason'] ?? '-' }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-sm text-emerald-100">
            <p class="font-semibold text-white">Framework Reason</p>
            <p class="mt-2">{{ $explanation['framework_reason'] ?? '-' }}</p>
        </div>
        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 p-4 text-sm text-emerald-100">
            <p class="font-semibold text-white">SDLC Reason</p>
            <p class="mt-2">{{ $explanation['sdlc_reason'] ?? '-' }}</p>
        </div>
    </div>
</article>