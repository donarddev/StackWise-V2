@props(['viewData'])

<div class="mt-8 space-y-8">
    <div class="space-y-2">
        <h3 class="font-sans text-base font-semibold text-white">Section A — Research Paper Workspace</h3>
        <p class="font-sans text-sm text-slate-400">
            Full generated study in one continuous academic manuscript preview.
        </p>
    </div>

    <x-research-support.academic.paper :paper="$viewData['paper']" />

    <div class="space-y-2 pt-2">
        <h3 class="font-sans text-base font-semibold text-white">Section B — Research Discovery &amp; Publication Assistant</h3>
        <p class="font-sans text-sm text-slate-400">
            Keywords, search tools, venues, and indexing reminders—kept separate from the formal paper.
        </p>
    </div>

    <x-research-support.academic.research-discovery
        :keywords="$viewData['discovery']['keywords']"
        :search-query-groups="$viewData['discovery']['searchQueryGroups']"
        :journals="$viewData['discovery']['journals']"
        :open-access-links="$viewData['discovery']['openAccessLinks']"
        :publication-recommendation="$viewData['discovery']['publicationRecommendation']"
        :scopus-verification="$viewData['discovery']['scopusVerification']"
        :open-access-disclaimer="$viewData['discovery']['openAccessDisclaimer']"
    />
</div>
