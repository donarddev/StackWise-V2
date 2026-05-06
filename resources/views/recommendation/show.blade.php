@extends('layouts.app')

@section('title', 'Recommendation Details | StackWise AI')

@section('content')
    @php
        $report = $recommendationReport;
        $summary = $report['project_summary'];
        $mainRecommendation = $report['main_recommendation'];
        $explanation = $report['explanation'];
    @endphp

    <section class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col gap-4 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Recommendation Details</h1>
                    <x-recommendation.confidence-badge :score="$mainRecommendation['confidence_score']" />
                </div>
                <p class="max-w-3xl text-slate-300">
                    A full breakdown of the saved recommendation record for presentation, review, and class discussion.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:min-w-[24rem]">
                <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-wider text-slate-400">Record ID</p>
                    <p class="mt-2 text-2xl font-bold text-white">#{{ $recommendation->id }}</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">
                    <p class="text-xs uppercase tracking-wider text-slate-400">Generated</p>
                    <p class="mt-2 text-2xl font-bold text-white">{{ $recommendation->created_at?->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="xl:col-span-1">
                <x-recommendation.summary-card :summary="$summary" />
            </div>

            <div class="xl:col-span-2">
                <x-recommendation.main-result-card :main-recommendation="$mainRecommendation" :explanation="$explanation" title="Saved Recommendation" />
            </div>

            <div class="xl:col-span-3">
                <x-recommendation.alternative-stacks-table :alternative-stacks="$report['alternative_stacks']" />
            </div>

            <div class="xl:col-span-2">
                <x-recommendation.why-not-card :items="$report['why_not_this']" />
            </div>

            <div class="xl:col-span-1">
                <x-recommendation.risk-analysis-card :risks="$report['risk_analysis']" />
            </div>

            <div class="xl:col-span-2">
                <x-recommendation.skill-gap-table :skills="$report['skill_gap_analysis']" />
            </div>

            <div class="xl:col-span-3">
                <x-recommendation.roadmap-section :roadmap="$report['project_roadmap']" />
            </div>

            <div class="xl:col-span-3">
                <x-recommendation.feedback-section :feedbacks="$report['feedback']" />
            </div>
        </div>

        <x-feedback.form :recommendation-id="$recommendation->id" :success-message="session('feedback_success')" />

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('recommendation.history') }}" class="rounded-full bg-emerald-400 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300">
                Back to History
            </a>
            <a href="{{ route('recommendation.index') }}" class="rounded-full border border-white/15 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                Generate New Recommendation
            </a>
        </div>
    </section>
@endsection