@props(['recommendations'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-white">Recent Recommendations</h2>
            <p class="mt-1 text-sm text-slate-400">A quick review of the most recent saved results.</p>
        </div>
        <a href="{{ route('recommendation.history') }}" class="rounded-full border border-white/15 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/10">
            View History
        </a>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                <thead class="bg-slate-900/80 text-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Project</th>
                        <th class="px-4 py-3 font-semibold">Language</th>
                        <th class="px-4 py-3 font-semibold">Framework</th>
                        <th class="px-4 py-3 font-semibold">SDLC Model</th>
                        <th class="px-4 py-3 font-semibold">Confidence</th>
                        <th class="px-4 py-3 font-semibold">Date</th>
                        <th class="px-4 py-3 font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-slate-950/40">
                    @forelse ($recommendations as $recommendation)
                        <tr>
                            <td class="px-4 py-3 text-white">{{ $recommendation->project_name }}</td>
                            <td class="px-4 py-3">{{ $recommendation->recommended_language }}</td>
                            <td class="px-4 py-3">{{ $recommendation->recommended_framework }}</td>
                            <td class="px-4 py-3">{{ $recommendation->recommended_sdlc_model }}</td>
                            <td class="px-4 py-3"><x-ui.badges.confidence :score="$recommendation->confidence_score" /></td>
                            <td class="px-4 py-3">{{ $recommendation->generated_at }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('recommendation.show', $recommendation) }}" class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-4 py-2 text-xs font-semibold text-emerald-200 transition hover:bg-emerald-400/20">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">No recommendations generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</article>
