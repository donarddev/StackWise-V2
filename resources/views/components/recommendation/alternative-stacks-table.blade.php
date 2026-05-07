@props(['alternativeStacks', 'title' => 'Alternative Technology Stacks'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
            <p class="mt-1 text-sm text-slate-400">Options that could also fit the project with different trade-offs.</p>
        </div>
        <p class="text-sm text-slate-400">Compared by fit, strength, and limitation</p>
    </div>

    <div class="mt-4 overflow-hidden rounded-2xl border border-white/10">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-white/10 text-left text-sm text-slate-300">
                <thead class="bg-slate-900/80 text-slate-200">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Language</th>
                        <th class="px-4 py-3 font-semibold">Framework</th>
                        <th class="px-4 py-3 font-semibold">Best For</th>
                        <th class="px-4 py-3 font-semibold">Score</th>
                        <th class="px-4 py-3 font-semibold">Limitation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10 bg-slate-950/40">
                    @forelse ($alternativeStacks as $alternativeStack)
                        <tr>
                            <td class="px-4 py-3 text-white">{{ $alternativeStack['language'] }}</td>
                            <td class="px-4 py-3">{{ $alternativeStack['framework'] }}</td>
                            <td class="px-4 py-3">{{ $alternativeStack['best_for'] }}</td>
                            <td class="px-4 py-3">{{ $alternativeStack['score'] }}%</td>
                            <td class="px-4 py-3">
                                <p>{{ $alternativeStack['limitation'] }}</p>

                                @if (! empty($alternativeStack['tradeoffs']))
                                    <details class="mt-3 rounded-xl border border-white/10 bg-slate-900/60 p-3">
                                        <summary class="cursor-pointer text-xs font-semibold uppercase tracking-wider text-slate-300">
                                            Trade-offs
                                        </summary>

                                        <div class="mt-3 space-y-3 text-sm text-slate-300">
                                            <div class="grid gap-3 md:grid-cols-2">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-200">Advantages</p>
                                                    <ul class="mt-2 list-disc space-y-1 pl-5">
                                                        @foreach (($alternativeStack['tradeoffs']['advantages'] ?? []) as $item)
                                                            <li>{{ $item }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold uppercase tracking-wider text-rose-200">Disadvantages</p>
                                                    <ul class="mt-2 list-disc space-y-1 pl-5">
                                                        @foreach (($alternativeStack['tradeoffs']['disadvantages'] ?? []) as $item)
                                                            <li>{{ $item }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>

                                            <dl class="grid gap-2 rounded-xl border border-white/10 bg-slate-950/40 p-3 text-xs text-slate-200 sm:grid-cols-2">
                                                <div class="flex items-start justify-between gap-3">
                                                    <dt class="text-slate-400">Scalability</dt>
                                                    <dd class="text-right">{{ $alternativeStack['tradeoffs']['scalability'] ?? '-' }}</dd>
                                                </div>
                                                <div class="flex items-start justify-between gap-3">
                                                    <dt class="text-slate-400">Maintenance</dt>
                                                    <dd class="text-right">{{ $alternativeStack['tradeoffs']['maintenance'] ?? '-' }}</dd>
                                                </div>
                                                <div class="flex items-start justify-between gap-3">
                                                    <dt class="text-slate-400">Learning curve</dt>
                                                    <dd class="text-right">{{ $alternativeStack['tradeoffs']['learning_curve'] ?? '-' }}</dd>
                                                </div>
                                                <div class="flex items-start justify-between gap-3">
                                                    <dt class="text-slate-400">Dev speed</dt>
                                                    <dd class="text-right">{{ $alternativeStack['tradeoffs']['development_speed'] ?? '-' }}</dd>
                                                </div>
                                                <div class="flex items-start justify-between gap-3 sm:col-span-2">
                                                    <dt class="text-slate-400">Cost</dt>
                                                    <dd class="text-right">{{ $alternativeStack['tradeoffs']['cost'] ?? '-' }}</dd>
                                                </div>
                                            </dl>
                                        </div>
                                    </details>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">No alternative stacks available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</article>