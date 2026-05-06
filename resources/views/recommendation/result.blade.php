@extends('layouts.app')

@section('title', 'Recommendation Result | StackWise AI')

@section('content')
    @php
        $summary = $recommendation['project_summary'];
        $mainRecommendation = $recommendation['main_recommendation'];
        $explanation = $recommendation['explanation'];
    @endphp

    <section class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col gap-4 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Recommendation Result</h1>
                <p class="max-w-3xl text-slate-300">This report explains the suggested stack in a way that is simple to present in class and easy to extend later.</p>
            </div>

            <x-recommendation.confidence-badge :score="$mainRecommendation['confidence_score']" />
        </div>

        @if (session('feedback_success'))
            <div class="rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                {{ session('feedback_success') }}
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="xl:col-span-1">
                <x-recommendation.summary-card :summary="$summary" />
            </div>

            <div class="xl:col-span-2">
                <x-recommendation.main-result-card :main-recommendation="$mainRecommendation" :explanation="$explanation" />
            </div>

            <div class="xl:col-span-3">
                <x-recommendation.alternative-stacks-table :alternative-stacks="$recommendation['alternative_stacks']" />
            </div>

            <div class="xl:col-span-2">
                <x-recommendation.why-not-card :items="$recommendation['why_not_this']" />
            </div>

            <div class="xl:col-span-1">
                <x-recommendation.risk-analysis-card :risks="$recommendation['risk_analysis']" />
            </div>

            <div class="xl:col-span-2">
                <x-recommendation.skill-gap-table :skills="$recommendation['skill_gap_analysis']" />
            </div>

            <div class="xl:col-span-3">
                <x-recommendation.roadmap-section :roadmap="$recommendation['project_roadmap']" />
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('recommendation.index') }}" class="rounded-full bg-emerald-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                Generate Another Recommendation
            </a>
            <a href="{{ route('recommendation.history') }}" class="rounded-full border border-white/15 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                View History
            </a>
            <a href="{{ route('home') }}" class="rounded-full border border-white/15 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                Return Home
            </a>
        </div>

        @if ($recommendationRecord)
            <x-feedback.form :recommendation-id="$recommendationRecord->id" :success-message="session('feedback_success')" />
        @endif

        @if (! empty($recommendation['feedback'] ?? []))
            <x-recommendation.feedback-section :feedbacks="$recommendation['feedback']" title="Saved Feedback" />
        @endif
    </section>
@endsection