@props([
    'insights',
])

@if ($insights)
    <div class="rounded-2xl border border-white/10 bg-slate-900/40 px-4 py-4 shadow-md shadow-slate-950/20 sm:px-6">
        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Quick insights</p>
        <div class="mt-3 grid gap-4 sm:grid-cols-3">
            <div class="min-w-0">
                <p class="text-xs text-slate-400">Most common stack</p>
                <p class="mt-1 text-sm font-medium leading-snug text-white">{{ $insights['most_common_stack'] ?? '—' }}</p>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-400">Highest confidence</p>
                @if (! empty($insights['highest_confidence']))
                    <p class="mt-1 text-sm font-medium leading-snug text-white">
                        {{ $insights['highest_confidence']['project'] }}
                        <span class="text-emerald-200/90">({{ $insights['highest_confidence']['score'] }}%)</span>
                    </p>
                @else
                    <p class="mt-1 text-sm text-slate-500">—</p>
                @endif
            </div>
            <div class="min-w-0">
                <p class="text-xs text-slate-400">Latest recommendation</p>
                @if (! empty($insights['latest']))
                    <p class="mt-1 text-sm font-medium leading-snug text-white">
                        {{ $insights['latest']['project'] }}
                        <span class="block text-xs font-normal text-slate-400">{{ $insights['latest']['date']->format('M d, Y') }}</span>
                    </p>
                @else
                    <p class="mt-1 text-sm text-slate-500">—</p>
                @endif
            </div>
        </div>
    </div>
@endif
