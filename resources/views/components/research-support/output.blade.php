@props(['researchSupport'])

@php
    $specificObjectives = $researchSupport->specific_objectives ?? [];
@endphp

<div class="mt-8 space-y-4">
    <div class="flex items-center justify-between gap-4">
        <h3 class="text-base font-semibold text-white">Generated Research Support</h3>
        <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-200">
            Saved draft
        </span>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <x-research-support.text-card title="Suggested Research Title" :content="$researchSupport->suggested_title" />
        <x-research-support.text-card title="Abstract" :content="$researchSupport->abstract" class="md:col-span-2 xl:col-span-3" />
        <x-research-support.text-card title="Introduction" :content="$researchSupport->introduction" class="md:col-span-2" />
        <x-research-support.text-card title="Statement of the Problem" :content="$researchSupport->statement_of_problem" class="md:col-span-2" />

        <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 md:col-span-2 xl:col-span-3">
            <h3 class="text-sm font-semibold text-white">Objectives</h3>

            @if (filled($researchSupport->general_objective))
                <div class="mt-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-200">General Objective</p>
                    <p class="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-300">{{ $researchSupport->general_objective }}</p>
                </div>
            @endif

            @if (count($specificObjectives) > 0)
                <div class="mt-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-200">Specific Objectives</p>
                    <ul class="mt-2 list-disc space-y-2 pl-5 text-sm text-slate-300">
                        @foreach ($specificObjectives as $objective)
                            <li>{{ $objective }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (! filled($researchSupport->general_objective) && count($specificObjectives) === 0)
                <p class="mt-3 text-sm text-slate-400">No suggestions generated yet.</p>
            @endif
        </div>

        <x-research-support.text-card title="Scope and Limitation" :content="$researchSupport->scope_and_limitation" />
        <x-research-support.text-card title="Significance of the Study" :content="$researchSupport->significance_of_study" />
        <x-research-support.text-card title="Suggested Methodology" :content="$researchSupport->suggested_methodology" class="md:col-span-2" />

        <x-research-support.list-card title="Related Keywords" :items="$researchSupport->related_keywords ?? []" />
        <x-research-support.list-card title="Suggested Search Queries" :items="$researchSupport->suggested_search_queries ?? []" />
        <x-research-support.list-card title="Suggested Journals / Publication Venues" :items="$researchSupport->suggested_journals ?? []" />

        <x-research-support.links-card
            :links="$researchSupport->open_access_links ?? []"
            class="md:col-span-2 xl:col-span-3"
        />

        <x-research-support.text-card
            title="Publication Recommendation"
            :content="$researchSupport->publication_recommendation"
            class="md:col-span-2 xl:col-span-3"
        />

        <x-research-support.text-card
            title="Scopus Verification Note"
            :content="$researchSupport->scopus_note"
            class="md:col-span-2 xl:col-span-3"
        />
    </div>
</div>
