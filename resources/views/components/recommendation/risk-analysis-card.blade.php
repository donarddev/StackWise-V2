@props(['risks', 'title' => 'Risk Analysis'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
    <div class="mt-4 space-y-3">
        @forelse ($risks as $risk)
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                <div class="flex items-center justify-between gap-3">
                    <p class="font-semibold text-white">{{ $risk['risk_title'] }}</p>
                    <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-200">{{ $risk['impact_level'] }}</span>
                </div>
                <p class="mt-2 text-sm text-slate-300">{{ $risk['explanation'] }}</p>
                <p class="mt-3 text-sm text-slate-400"><span class="font-semibold text-slate-200">Solution:</span> {{ $risk['suggested_solution'] }}</p>
            </div>
        @empty
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 text-sm text-slate-400">No risk items were generated for this recommendation.</div>
        @endforelse
    </div>
</article>