@extends('layouts.app')

@section('title', 'Documentation Explorer | StackWise AI')

@section('content')
    @php
        $filters = $filters ?? ['search' => '', 'category' => 'all'];
        $summary = $summary ?? ['languages' => 0, 'frameworks' => 0, 'sdlc_models' => 0];
        $hasResults = $hasResults ?? true;
    @endphp

    <section class="mx-auto max-w-7xl space-y-8">
        <x-documentation.page-hero
            title="Explore languages, frameworks, and SDLC models"
            description="Use this guide to understand the technologies and process models that StackWise AI may recommend for your project."
            :cta-href="route('recommendation.index')"
        />

        <x-documentation.category-summary :summary="$summary" />

        <div class="space-y-3">
            <p class="text-sm font-medium text-slate-200">Jump or filter</p>
            <x-documentation.quick-nav :filters="$filters" />
        </div>

        <x-documentation.filter-panel :filters="$filters" />

        @if (! $hasResults)
            <x-documentation.empty-state />
        @else
            @if ($languages !== [])
                <section class="space-y-4">
                    <x-documentation.section-heading
                        id="doc-languages"
                        title="Programming languages"
                        description="Student-friendly language notes and common uses."
                    />

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($languages as $language)
                            <x-documentation.card :item="$language" />
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($frameworks !== [])
                <section class="space-y-4">
                    <x-documentation.section-heading
                        id="doc-frameworks"
                        title="Frameworks"
                        description="Popular frameworks and where they fit best."
                    />

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($frameworks as $framework)
                            <x-documentation.card :item="$framework" />
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($sdlcModels !== [])
                <section class="space-y-4">
                    <x-documentation.section-heading
                        id="doc-sdlc"
                        title="SDLC models"
                        description="Development process models explained in simple language."
                    />

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($sdlcModels as $model)
                            <x-documentation.card :item="$model" />
                        @endforeach
                    </div>
                </section>
            @endif
        @endif
    </section>
@endsection
