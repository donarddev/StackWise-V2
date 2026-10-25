@props(['paper'])

@php
    $sectionNumber = 0;
    $nextSection = static function () use (&$sectionNumber): int {
        $sectionNumber++;

        return $sectionNumber;
    };
@endphp

<x-research-support.academic.paper-workspace
    :title="$paper['workspaceTitle']"
    :subtitle="$paper['workspaceSubtitle']"
    badge="AI-generated draft"
>
    <x-research-support.academic.title-page :title-page="$paper['titlePage']" />

    @if (filled($paper['abstract']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Abstract">
            <p class="whitespace-pre-wrap">{{ $paper['abstract'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['introduction']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Introduction">
            <div class="space-y-4">
                @foreach (preg_split("/\n\s*\n/", trim($paper['introduction'])) ?: [] as $paragraph)
                    @if (filled(trim($paragraph)))
                        <p class="whitespace-pre-wrap">{{ trim($paragraph) }}</p>
                    @endif
                @endforeach
            </div>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['statementOfProblem']['general']) || count($paper['statementOfProblem']['specific']) > 0)
        <x-research-support.academic.academic-section
            :number="$nextSection()"
            title="Statement of the Problem"
            subtitle="General problem and specific research questions"
        >
            <x-research-support.academic.academic-list
                variant="definition"
                :entries="filled($paper['statementOfProblem']['general']) ? [['term' => 'General Problem', 'description' => $paper['statementOfProblem']['general']]] : []"
            />
            <x-research-support.academic.academic-list
                class="mt-4"
                variant="numbered"
                label="Specific Questions"
                :items="$paper['statementOfProblem']['specific']"
            />
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['objectives']['general']) || count($paper['objectives']['specific']) > 0)
        <x-research-support.academic.academic-section :number="$nextSection()" title="Objectives">
            <x-research-support.academic.academic-list
                variant="definition"
                :entries="filled($paper['objectives']['general']) ? [['term' => 'General Objective', 'description' => $paper['objectives']['general']]] : []"
            />
            <x-research-support.academic.academic-list
                class="mt-4"
                variant="bullet"
                label="Specific Objectives"
                :items="$paper['objectives']['specific']"
            />
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['scopeAndLimitations']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Scope and Limitations">
            <p class="whitespace-pre-wrap">{{ $paper['scopeAndLimitations'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (count($paper['significanceEntries']) > 0)
        <x-research-support.academic.academic-section :number="$nextSection()" title="Significance of the Study">
            <x-research-support.academic.academic-list
                variant="definition"
                :entries="$paper['significanceEntries']"
            />
        </x-research-support.academic.academic-section>
    @endif

    @if (count($paper['rrlGroups']) > 0)
        <x-research-support.academic.academic-section
            :number="$nextSection()"
            title="Review of Related Literature"
            subtitle="Suggested research directions — verify all citations before use"
        >
            <x-research-support.academic.academic-list variant="grouped" :groups="$paper['rrlGroups']" />
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['conceptualFramework']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Conceptual Framework">
            <p class="whitespace-pre-wrap">{{ $paper['conceptualFramework'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['methodology']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Methodology">
            <p class="whitespace-pre-wrap">{{ $paper['methodology'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['technologyStackJustification']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Technology Stack Justification">
            <p class="whitespace-pre-wrap">{{ $paper['technologyStackJustification'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['riskAnalysisSummary']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Risk Analysis Summary">
            <p class="whitespace-pre-wrap">{{ $paper['riskAnalysisSummary'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['skillGapSummary']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Skill Gap Summary">
            <p class="whitespace-pre-wrap">{{ $paper['skillGapSummary'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['roadmapSummary']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Project Roadmap Summary">
            <p class="whitespace-pre-wrap">{{ $paper['roadmapSummary'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['conclusion']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Conclusion">
            <p class="whitespace-pre-wrap">{{ $paper['conclusion'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (filled($paper['recommendations']))
        <x-research-support.academic.academic-section :number="$nextSection()" title="Recommendations">
            <p class="whitespace-pre-wrap">{{ $paper['recommendations'] }}</p>
        </x-research-support.academic.academic-section>
    @endif

    @if (count($paper['referenceGroups']) > 0)
        <x-research-support.academic.academic-section
            :number="$nextSection()"
            title="References"
            subtitle="Placeholder and search guidance only — replace with verified sources"
        >
            <x-research-support.academic.academic-list variant="grouped" :groups="$paper['referenceGroups']" />
        </x-research-support.academic.academic-section>
    @endif
</x-research-support.academic.paper-workspace>
