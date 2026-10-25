@props([
    'name',
    'scope' => null,
    'whyItFits' => null,
    'recommendedFor' => null,
    'indexingStatus' => 'Needs manual verification',
    'publicationType' => 'Journal',
    'fitScore' => null,
    'indexingTone' => 'amber',
])

<article {{ $attributes->class([
    'flex flex-col gap-4 rounded-2xl border border-white/10 bg-slate-900/70 p-4 shadow-lg shadow-slate-950/20 ring-1 ring-inset ring-white/5 transition hover:border-teal-400/20 hover:ring-teal-400/10',
]) }}>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0 flex-1 space-y-2">
            <h4 class="text-sm font-semibold leading-snug text-white">
                {{ $name }}
            </h4>
            @if (filled($publicationType))
                <x-ui.badge tone="slate">{{ $publicationType }}</x-ui.badge>
            @endif
        </div>

        @if ($fitScore !== null && $fitScore !== '')
            <span
                class="inline-flex shrink-0 items-center rounded-lg border border-emerald-400/25 bg-emerald-400/10 px-2.5 py-1 font-sans text-xs font-semibold tabular-nums text-emerald-200"
            >
                {{ $fitScore }}% fit
            </span>
        @endif
    </div>

    <dl class="grid gap-3 font-sans text-xs">
        @if (filled($scope))
            <div class="space-y-1">
                <dt class="font-semibold uppercase tracking-wider text-slate-500">Field / scope</dt>
                <dd class="text-sm leading-6 text-slate-300">{{ $scope }}</dd>
            </div>
        @endif

        @if (filled($whyItFits))
            <div class="space-y-1">
                <dt class="font-semibold uppercase tracking-wider text-slate-500">Why it fits</dt>
                <dd class="text-sm leading-6 text-slate-300">{{ $whyItFits }}</dd>
            </div>
        @endif

        @if (filled($recommendedFor))
            <div class="space-y-1">
                <dt class="font-semibold uppercase tracking-wider text-slate-500">Recommended for</dt>
                <dd class="text-sm leading-6 text-slate-300">{{ $recommendedFor }}</dd>
            </div>
        @endif

        <div class="space-y-1">
            <dt class="font-semibold uppercase tracking-wider text-slate-500">Indexing status</dt>
            <dd>
                <x-ui.badge :tone="$indexingTone">
                    {{ $indexingStatus }}
                </x-ui.badge>
            </dd>
        </div>
    </dl>
</article>
