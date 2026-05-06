@props([
    'recommendations',
])

<div class="hidden overflow-hidden rounded-3xl border border-white/10 bg-slate-900/35 shadow-2xl shadow-slate-950/30 md:block">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-white/10 text-left text-sm">
            <thead class="sticky top-0 z-10 border-b border-white/10 bg-slate-900/95 text-xs uppercase tracking-wider text-slate-400 backdrop-blur">
                <tr>
                    <th class="px-4 py-3 font-semibold">Project</th>
                    <th class="px-4 py-3 font-semibold">Type</th>
                    <th class="px-4 py-3 font-semibold">Language</th>
                    <th class="px-4 py-3 font-semibold">Framework</th>
                    <th class="px-4 py-3 font-semibold">SDLC</th>
                    <th class="px-4 py-3 font-semibold">Confidence</th>
                    <th class="px-4 py-3 font-semibold">Date</th>
                    <th class="px-4 py-3 font-semibold">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10 bg-slate-950/30">
                @foreach ($recommendations as $recommendation)
                    @php
                        $stackLine =
                            $recommendation->recommended_language.
                            ' + '.
                            $recommendation->recommended_framework.
                            ' · '.
                            $recommendation->recommended_sdlc_model;
                    @endphp
                    <tr class="transition hover:bg-white/[0.04]">
                        <td class="max-w-[14rem] px-4 py-3 align-top">
                            <div class="font-medium text-white">{{ $recommendation->project_name }}</div>
                            <div class="mt-1 break-words text-xs text-slate-500">{{ $stackLine }}</div>
                        </td>
                        <td class="px-4 py-3 align-top text-slate-300">{{ $recommendation->project_type }}</td>
                        <td class="px-4 py-3 align-top">
                            <x-ui.badge tone="slate">{{ $recommendation->recommended_language }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <x-ui.badge tone="teal">{{ $recommendation->recommended_framework }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <x-ui.badge tone="slate">{{ $recommendation->recommended_sdlc_model }}</x-ui.badge>
                        </td>
                        <td class="px-4 py-3 align-top">
                            <x-ui.badges.confidence :score="$recommendation->confidence_score" />
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 align-top text-slate-400">
                            {{ $recommendation->created_at?->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 align-top">
                            <x-ui.button-link
                                :href="route('recommendation.show', $recommendation)"
                                variant="primary"
                                class="!px-4 !py-2 !text-xs !shadow-md"
                            >
                                View details
                            </x-ui.button-link>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
