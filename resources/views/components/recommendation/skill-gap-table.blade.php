@props(['skills', 'title' => 'Skill Gap Analysis'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                <thead class="bg-slate-900/80 text-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Skill</th>
                        <th class="px-4 py-3 font-semibold">Required Level</th>
                        <th class="px-4 py-3 font-semibold">User Level</th>
                        <th class="px-4 py-3 font-semibold">Gap Level</th>
                        <th class="px-4 py-3 font-semibold">Suggestion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-slate-950/40">
                    @forelse ($skills as $skillGap)
                        <tr>
                            <td class="px-4 py-3 text-white">{{ $skillGap['skill'] }}</td>
                            <td class="px-4 py-3">{{ $skillGap['required_level'] }}</td>
                            <td class="px-4 py-3">{{ $skillGap['user_level'] }}</td>
                            <td class="px-4 py-3">{{ $skillGap['gap_level'] }}</td>
                            <td class="px-4 py-3">{{ $skillGap['suggestion'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No skill gap items were generated for this recommendation.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</article>