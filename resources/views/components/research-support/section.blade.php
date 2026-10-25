@props([
    'recommendation',
    'researchSupportViewData' => null,
])

@php
    $researchSupport = $recommendation->researchSupport;
    $hasResearchSupport = $researchSupport !== null && $researchSupportViewData !== null;
    $submitLabel = $researchSupport !== null ? 'Regenerate Research Support' : 'Generate Research Support';
@endphp

<article id="research-support" class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">Research Support Assistant</h2>
            <p class="mt-1 max-w-3xl text-sm text-slate-400">
                Generate a formal academic research draft and publication discovery guidance from this recommendation.
            </p>
        </div>

        <x-ui.badge :tone="$hasResearchSupport ? 'amber' : 'teal'">
            {{ $hasResearchSupport ? 'AI-generated draft' : 'Not generated' }}
        </x-ui.badge>
    </div>

    <x-research-support.disclaimer class="mt-4" />

    <x-research-support.form
        :recommendation="$recommendation"
        :submit-label="$submitLabel"
        :research-support="$researchSupport"
    />

    @if ($researchSupport === null)
        <x-research-support.empty-state />
    @endif

    @if ($hasResearchSupport)
        <x-research-support.academic-output :view-data="$researchSupportViewData" />
    @endif
</article>
