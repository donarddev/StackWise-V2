@props([
    'keywords' => [],
    'searchQueryGroups' => [],
    'journals' => [],
    'openAccessLinks' => [],
    'publicationRecommendation' => [],
    'scopusVerification' => ['intro' => '', 'steps' => []],
    'openAccessDisclaimer' => 'Search links only. No automatic paper downloads.',
])

<aside
    {{ $attributes->class([
        'rounded-3xl border border-teal-400/15 bg-gradient-to-br from-teal-950/40 via-slate-950/80 to-slate-950 p-5 shadow-xl shadow-slate-950/40 ring-1 ring-inset ring-teal-400/10 sm:p-6',
    ]) }}
    aria-label="Research discovery and publication guidance"
>
    <div class="flex flex-col gap-3 border-b border-white/10 pb-5 sm:flex-row sm:items-start sm:justify-between">
        <div class="space-y-1">
            <p class="font-sans text-xs font-semibold uppercase tracking-[0.2em] text-teal-300/90">
                Publication discovery
            </p>
            <h3 class="font-sans text-lg font-semibold text-white">
                Research Discovery &amp; Publication Assistant
            </h3>
            <p class="max-w-2xl font-sans text-sm leading-6 text-slate-400">
                Search guidance, venue suggestions, and indexing reminders—separate from the formal paper draft.
            </p>
        </div>

        <x-ui.badge tone="teal" class="shrink-0 self-start">
            Verify externally
        </x-ui.badge>
    </div>

    <div class="mt-6 space-y-8">
        <section class="space-y-4">
            <header class="space-y-1">
                <h4 class="font-sans text-sm font-semibold text-white">Related Keywords</h4>
                <p class="font-sans text-xs text-slate-500">Use these to refine literature and venue searches.</p>
            </header>

            @if (count($keywords) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach ($keywords as $keyword)
                        <x-ui.badge tone="emerald">{{ $keyword }}</x-ui.badge>
                    @endforeach
                </div>
            @else
                <p class="rounded-2xl border border-dashed border-white/10 bg-slate-900/30 px-4 py-3 font-sans text-sm text-slate-500">
                    No keywords provided.
                </p>
            @endif
        </section>

        <section class="space-y-4">
            <header class="space-y-1">
                <h4 class="font-sans text-sm font-semibold text-white">Suggested Search Queries</h4>
                <p class="font-sans text-xs text-slate-500">Categorized queries for major academic discovery platforms.</p>
            </header>

            @if (count($searchQueryGroups) > 0)
                <div class="space-y-4">
                    @foreach ($searchQueryGroups as $group)
                        <div class="rounded-2xl border border-white/10 bg-slate-900/50 p-4 ring-1 ring-inset ring-white/5">
                            <p class="font-sans text-xs font-semibold uppercase tracking-wider text-teal-200/90">
                                {{ $group['category'] }}
                            </p>
                            <ul class="mt-3 space-y-2">
                                @foreach ($group['queries'] as $query)
                                    <li class="rounded-lg border border-white/10 bg-slate-950/60 px-3 py-2 font-mono text-xs leading-5 text-slate-200">
                                        {{ $query }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="rounded-2xl border border-dashed border-white/10 bg-slate-900/30 px-4 py-3 font-sans text-sm text-slate-500">
                    No search queries provided.
                </p>
            @endif
        </section>

        <section class="space-y-4">
            <header class="space-y-1">
                <h4 class="font-sans text-sm font-semibold text-white">Suggested Journals / Publication Venues</h4>
                <p class="font-sans text-xs text-slate-500">Confirm scope, ethics requirements, and indexing before submission.</p>
            </header>

            @if (count($journals) > 0)
                <div class="grid gap-3 lg:grid-cols-2">
                    @foreach ($journals as $journal)
                        <x-research-support.academic.journal-card
                            :name="$journal['name']"
                            :scope="$journal['scope']"
                            :why-it-fits="$journal['whyItFits']"
                            :recommended-for="$journal['recommendedFor']"
                            :indexing-status="$journal['indexingStatus']"
                            :publication-type="$journal['publicationType']"
                            :fit-score="$journal['fitScore']"
                            indexing-tone="amber"
                        />
                    @endforeach
                </div>
            @else
                <p class="rounded-2xl border border-dashed border-white/10 bg-slate-900/30 px-4 py-3 font-sans text-sm text-slate-500">
                    No journal suggestions provided.
                </p>
            @endif
        </section>

        <section class="space-y-4">
            <header class="space-y-1">
                <h4 class="font-sans text-sm font-semibold text-white">Open Access Paper Search Links</h4>
                <p class="font-sans text-xs font-medium text-amber-200/90">{{ $openAccessDisclaimer }}</p>
            </header>

            @if (count($openAccessLinks) > 0)
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($openAccessLinks as $link)
                        <x-research-support.academic.search-link-card
                            :label="$link['label']"
                            :url="$link['url']"
                            :source="$link['source']"
                            :description="$link['description']"
                        />
                    @endforeach
                </div>
            @else
                <p class="rounded-2xl border border-dashed border-white/10 bg-slate-900/30 px-4 py-3 font-sans text-sm text-slate-500">
                    No open access links provided.
                </p>
            @endif
        </section>

        @if (count($publicationRecommendation) > 0)
            <section class="space-y-4">
                <header class="space-y-1">
                    <h4 class="font-sans text-sm font-semibold text-white">Publication Recommendation</h4>
                    <p class="font-sans text-xs text-slate-500">Venue fit guidance for this project type and maturity level.</p>
                </header>

                <div class="space-y-3">
                    @foreach ($publicationRecommendation as $section)
                        <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/5 p-4 ring-1 ring-inset ring-emerald-400/10">
                            <p class="font-sans text-xs font-semibold uppercase tracking-wider text-emerald-200/90">
                                {{ $section['title'] }}
                            </p>

                            @if (filled($section['body']))
                                <p class="mt-2 font-sans text-sm leading-7 text-slate-300">
                                    {{ $section['body'] }}
                                </p>
                            @endif

                            @if (count($section['items']) > 0)
                                <ul class="mt-3 list-disc space-y-2 pl-5 font-sans text-sm leading-6 text-slate-300">
                                    @foreach ($section['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if (filled($scopusVerification['intro']) || count($scopusVerification['steps']) > 0)
            <section class="space-y-3">
                <h4 class="font-sans text-sm font-semibold text-white">Scopus Verification Note</h4>

                @if (filled($scopusVerification['intro']))
                    <p class="font-sans text-sm leading-7 text-amber-100/90">
                        {{ $scopusVerification['intro'] }}
                    </p>
                @endif

                @if (count($scopusVerification['steps']) > 0)
                    <ol class="list-decimal space-y-2 pl-5 font-sans text-sm leading-7 text-slate-300 marker:text-amber-300/80">
                        @foreach ($scopusVerification['steps'] as $step)
                            <li>{{ $step }}</li>
                        @endforeach
                    </ol>
                @endif
            </section>
        @endif
    </div>
</aside>
