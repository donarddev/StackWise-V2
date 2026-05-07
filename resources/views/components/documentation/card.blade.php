@props(['item'])

@php
    $kind = match ($item['category'] ?? '') {
        'language' => 'language',
        'framework' => 'framework',
        default => 'sdlc',
    };

    $advantages = $item['advantages'] ?? [];
    $topAdvantages = array_slice($advantages, 0, 2);
    $drawbacks = $item['disadvantages'] ?? $item['limitations'] ?? [];
    $keyDrawback = $drawbacks[0] ?? null;
    $labels = $item['best_fit_labels'] ?? [];
    $difficulty = $item['difficulty'] ?? '—';
    $referenceSources = $item['reference_sources'] ?? [];
    $shortDescription = $item['short_description'] ?? $item['description'] ?? '';

    $categoryLabel = match ($kind) {
        'language' => 'Language',
        'framework' => 'Framework',
        default => 'SDLC',
    };
@endphp

<article
    class="flex h-full flex-col rounded-2xl border border-white/10 bg-slate-900/45 p-5 shadow-md shadow-slate-950/25 transition duration-200 hover:-translate-y-0.5 hover:border-emerald-400/25 hover:shadow-emerald-950/20"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0 space-y-1">
            <h3 class="text-lg font-semibold tracking-tight text-white">{{ $item['name'] }}</h3>
            @if ($kind === 'framework' && ! empty($item['related_language']))
                <p class="text-xs text-slate-400">
                    Related language:
                    <span class="font-medium text-emerald-200/90">{{ $item['related_language'] }}</span>
                </p>
            @endif
        </div>
        <div class="flex flex-wrap items-center justify-end gap-2">
            <x-ui.badge tone="slate">{{ $categoryLabel }}</x-ui.badge>
            <x-ui.badge tone="emerald">{{ $difficulty }}</x-ui.badge>
        </div>
    </div>

    @if ($labels !== [])
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach ($labels as $label)
                <x-ui.badge tone="teal">{{ $label }}</x-ui.badge>
            @endforeach
        </div>
    @endif

    <p class="mt-3 line-clamp-3 text-sm leading-6 text-slate-300">
        {{ $shortDescription }}
    </p>

    <dl class="mt-4 space-y-3 text-sm">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Best for</dt>
            <dd class="mt-1 text-slate-200">{{ $item['best_for'] }}</dd>
        </div>

        @if ($topAdvantages !== [])
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Top advantages</dt>
                <dd class="mt-2 space-y-1.5">
                    @foreach ($topAdvantages as $advantage)
                        <div class="rounded-lg border border-white/5 bg-slate-950/50 px-3 py-2 text-slate-200">{{ $advantage }}</div>
                    @endforeach
                </dd>
            </div>
        @endif

        @if ($keyDrawback)
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Key limitation</dt>
                <dd class="mt-1 rounded-lg border border-amber-400/15 bg-amber-400/5 px-3 py-2 text-amber-100/90">{{ $keyDrawback }}</dd>
            </div>
        @endif

        <div>
            <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Recommended when</dt>
            <dd class="mt-1 text-slate-200">{{ $item['recommended_when'] ?? '—' }}</dd>
        </div>

        <div>
            <dt class="text-xs font-medium uppercase tracking-wider text-slate-500">Avoid when</dt>
            <dd class="mt-1 text-slate-200">{{ $item['avoid_when'] ?? '—' }}</dd>
        </div>
    </dl>

    @if ($referenceSources !== [])
        <p class="mt-4 text-xs text-slate-500">
            References:
            <span class="text-slate-400">{{ implode(', ', $referenceSources) }}</span>
        </p>
    @endif

    @if (! empty($item['recommendation_note']))
        <p class="mt-4 rounded-xl border border-emerald-400/20 bg-emerald-400/5 px-3 py-2 text-xs leading-5 text-emerald-100/95">
            {{ $item['recommendation_note'] }}
        </p>
    @endif

    @if ($kind === 'language' && ! empty($item['common_frameworks']))
        <div class="mt-4">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Common frameworks</p>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ($item['common_frameworks'] as $fw)
                    <span class="rounded-full border border-white/10 bg-slate-950/60 px-3 py-1 text-xs text-slate-200">{{ $fw }}</span>
                @endforeach
            </div>
        </div>
    @elseif ($kind === 'sdlc' && ! empty($item['example_project']))
        <div class="mt-4">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Example project</p>
            <p class="mt-2 text-sm text-slate-200">{{ $item['example_project'] }}</p>
        </div>
    @endif

    <details class="mt-auto pt-4">
        <summary class="cursor-pointer list-none text-sm font-medium text-emerald-200/90 outline-none marker:hidden [&::-webkit-details-marker]:hidden">
            <span class="border-b border-dashed border-emerald-400/40 pb-0.5">More details</span>
        </summary>
        <div class="mt-3 space-y-4 text-sm">
            @if ($advantages !== [])
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">All advantages</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-slate-200">
                        @foreach ($advantages as $advantage)
                            <li>{{ $advantage }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($drawbacks !== [])
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Tradeoffs</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5 text-slate-200">
                        @foreach ($drawbacks as $drawback)
                            <li>{{ $drawback }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </details>
</article>
