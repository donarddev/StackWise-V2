@extends('layouts.app')

@section('title', 'Dashboard | StackWise AI')

@section('content')
    <section class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.6fr)] lg:items-stretch">
            <div class="flex flex-col justify-between gap-4 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.35em] text-emerald-300/70">Workspace</p>
                    <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">
                        Welcome back, {{ auth()->user()->name }}
                    </h1>
                    <p class="max-w-3xl text-slate-300">
                        Your personalized StackWise AI workspace—recent decisions, saved recommendations, and quick actions in one place.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('recommendation.create') }}" class="inline-flex items-center justify-center rounded-full bg-emerald-400 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-950/20 transition hover:bg-emerald-300">
                        Generate recommendation
                    </a>
                    <a href="{{ route('recommendation.history') }}" class="inline-flex items-center justify-center rounded-full border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/20 transition hover:bg-white/10">
                        View history
                    </a>
                </div>
            </div>

            <aside class="rounded-3xl border border-white/10 bg-slate-900/50 p-6 shadow-2xl shadow-slate-950/30 ring-1 ring-inset ring-white/5">
                <div class="flex items-start gap-4">
                    <x-ui.user-avatar :name="auth()->user()->name" size="lg" />
                    <div class="min-w-0">
                        <p class="truncate text-lg font-semibold text-white">{{ auth()->user()->name }}</p>
                        <p class="truncate text-sm text-slate-300">{{ auth()->user()->email }}</p>
                        <p class="mt-2 text-xs text-slate-500">
                            Member since {{ auth()->user()->created_at?->format('M d, Y') }}
                        </p>
                        <div class="mt-3 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-medium text-emerald-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                            Active session
                        </div>
                    </div>
                </div>

                <div class="mt-5 grid gap-2">
                    <a href="{{ route('profile.edit') }}" class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Manage profile & settings
                    </a>
                </div>
            </aside>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($statistics as $statistic)
                <x-ui.stat-card :label="$statistic['label']" :value="$statistic['value']" :helper="$statistic['helper']" />
            @endforeach
        </div>

        <x-dashboard.recent-recommendations-table :recommendations="$recentRecommendations" />

        @if (empty($recentRecommendations))
            <x-ui.alert type="warning" message="No recommendations generated yet." />
        @endif

        @if (($statistics[5]['value'] ?? 0) === 0)
            <x-ui.alert type="warning" message="No feedback submitted yet." />
        @endif
    </section>
@endsection
