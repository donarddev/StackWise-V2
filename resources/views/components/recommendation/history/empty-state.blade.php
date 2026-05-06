@props([
    'hasActiveFilters' => false,
])

<div class="rounded-3xl border border-white/10 bg-slate-900/45 px-6 py-14 text-center shadow-xl shadow-slate-950/25">
    <h2 class="text-xl font-semibold text-white">No recommendation records found</h2>
    <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-400">
        Generate a new recommendation or adjust your filters to view saved project decisions.
    </p>
    <div class="mt-8 flex flex-wrap justify-center gap-3">
        <x-ui.button-link :href="route('recommendation.index')" variant="primary">
            Generate recommendation
        </x-ui.button-link>
        @if ($hasActiveFilters)
            <x-ui.button-link :href="route('recommendation.history')" variant="secondary">
                Clear filters
            </x-ui.button-link>
        @endif
    </div>
</div>
