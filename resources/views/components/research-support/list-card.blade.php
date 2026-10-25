@props([
    'title',
    'items' => [],
    'emptyMessage' => 'No suggestions generated yet.',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-white/10 bg-slate-900/70 p-4']) }}>
    <h3 class="text-sm font-semibold text-white">{{ $title }}</h3>

    @if (count($items) > 0)
        <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-slate-300">
            @foreach ($items as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @else
        <p class="mt-3 text-sm text-slate-400">{{ $emptyMessage }}</p>
    @endif
</div>
