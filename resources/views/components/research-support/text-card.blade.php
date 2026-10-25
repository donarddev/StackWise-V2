@props([
    'title',
    'content' => null,
    'emptyMessage' => 'No content generated yet.',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-white/10 bg-slate-900/70 p-4']) }}>
    <h3 class="text-sm font-semibold text-white">{{ $title }}</h3>

    @if (filled($content))
        <div class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-300">{{ $content }}</div>
    @else
        <p class="mt-3 text-sm text-slate-400">{{ $emptyMessage }}</p>
    @endif
</div>
