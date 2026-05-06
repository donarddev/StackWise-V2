<x-app-layout>
    <section class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col gap-4 rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-3">
                <h1 class="text-3xl font-bold tracking-tight text-white sm:text-4xl">Dashboard Summary</h1>
                <p class="max-w-3xl text-slate-300">A simple snapshot of StackWise AI activity, ready for class presentation and quick review.</p>
            </div>
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
</x-app-layout>
