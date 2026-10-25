{{--
    Preview-only layout for academic research support components.
--}}
<x-research-support.academic.paper-workspace
    title="A Formal Research Study on StackWise Portal"
    subtitle="AI-generated capstone draft for review and faculty revision."
    badge="Draft preview"
>
    <x-research-support.academic.academic-section :number="1" title="Abstract">
        <p class="whitespace-pre-wrap">
            {{ $abstract }}
        </p>
    </x-research-support.academic.academic-section>

    <x-research-support.academic.academic-section
        :number="2"
        title="Statement of the Problem"
        subtitle="General problem and specific research questions"
    >
        <x-research-support.academic.academic-list
            variant="definition"
            :entries="[
                ['term' => 'General Problem', 'description' => $statementGeneral],
            ]"
        />
        <x-research-support.academic.academic-list
            class="mt-4"
            variant="numbered"
            label="Specific Questions"
            :items="$statementSpecific"
        />
    </x-research-support.academic.academic-section>

    <x-research-support.academic.academic-section :number="3" title="Objectives">
        <x-research-support.academic.academic-list
            variant="definition"
            :entries="[
                ['term' => 'General Objective', 'description' => $objectivesGeneral],
            ]"
        />
        <x-research-support.academic.academic-list
            class="mt-4"
            variant="bullet"
            label="Specific Objectives"
            :items="$objectivesSpecific"
        />
    </x-research-support.academic.academic-section>
</x-research-support.academic.paper-workspace>

<x-research-support.academic.research-discovery
    class="mt-8"
    :keywords="$keywords"
    :search-query-groups="$searchQueryGroups"
    :journals="$journals"
    :open-access-links="$openAccessLinks"
    :publication-recommendation="$publicationRecommendation"
    :scopus-verification="$scopusVerification"
/>
