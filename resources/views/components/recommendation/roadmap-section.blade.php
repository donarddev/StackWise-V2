@props(['roadmap', 'title' => 'Suggested Project Roadmap'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
            <p class="mt-1 text-sm text-slate-400">A presentation-friendly delivery plan for the project.</p>
        </div>
        <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-200">6 phases</span>
    </div>

    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($roadmap as $step)
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                <p class="text-xs uppercase tracking-wider text-emerald-200">{{ $step['phase'] }}</p>
                <p class="mt-2 text-base font-semibold text-white">{{ $step['task'] }}</p>
                <p class="mt-2 text-sm text-slate-300">{{ $step['description'] }}</p>

                @if (! empty($step['estimated_focus']))
                    <p class="mt-3 text-xs font-semibold uppercase tracking-wider text-slate-400">
                        Estimated focus: {{ $step['estimated_focus'] }}%
                    </p>
                @endif

                @if (! empty($step['objectives']))
                    <div class="mt-4 space-y-3 text-sm text-slate-300">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-200">Objectives</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach ($step['objectives'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-200">Deliverables</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach (($step['deliverables'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-200">Priorities</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach (($step['priorities'] ?? []) as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 text-sm text-slate-400">No roadmap steps were generated for this recommendation.</div>
        @endforelse
    </div>
</article>