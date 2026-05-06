@extends('layouts.app')

@section('title', 'Recommendation History | StackWise AI')

@section('content')
    @php
        $recommendations = $recommendations ?? collect();
        $summary = $summary ?? [];
        $insights = $insights ?? null;
        $filterOptions = $filterOptions ?? [];
        $filters = $filters ?? [];
        $hasActiveFilters = $hasActiveFilters ?? false;
    @endphp

    <section class="mx-auto max-w-7xl space-y-8">
        <x-recommendation.history.page-hero
            title="Recommendation History"
            description="Review saved stack decisions, compare past recommendations, and reopen complete project reports."
        />

        <x-recommendation.history.summary-stats :summary="$summary" />

        <x-recommendation.history.filter-panel :filters="$filters" :filter-options="$filterOptions" />

        @if ($insights)
            <x-recommendation.history.insights-strip :insights="$insights" />
        @endif

        @if ($recommendations->isEmpty())
            <x-recommendation.history.empty-state :has-active-filters="$hasActiveFilters" />
        @else
            <x-recommendation.history.data-table :recommendations="$recommendations" />

            <div class="space-y-4 md:hidden">
                @foreach ($recommendations as $recommendation)
                    <x-recommendation.history.mobile-card :recommendation="$recommendation" />
                @endforeach
            </div>

            @if ($recommendations->hasPages())
                <div class="flex justify-center">
                    {{ $recommendations->links() }}
                </div>
            @endif
        @endif

        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <x-ui.button-link :href="route('recommendation.index')" variant="primary" class="w-full justify-center sm:w-auto">
                Generate new recommendation
            </x-ui.button-link>
            <x-ui.button-link :href="route('home')" variant="secondary" class="w-full justify-center sm:w-auto">
                Return home
            </x-ui.button-link>
        </div>
    </section>
@endsection
