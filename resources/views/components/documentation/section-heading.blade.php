@props([
    'id',
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'scroll-mt-28 space-y-2']) }} id="{{ $id }}">
    <h2 class="text-2xl font-semibold text-white">{{ $title }}</h2>
    @if ($description)
        <p class="text-sm text-slate-400">{{ $description }}</p>
    @endif
</div>
