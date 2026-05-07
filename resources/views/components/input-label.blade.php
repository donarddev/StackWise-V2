@props([
    'value',
    'variant' => 'default',
])

@php
    $labelClasses = match ($variant) {
        'guest' => 'block text-sm font-medium text-slate-300',
        default => 'block text-sm font-medium text-gray-700',
    };
@endphp

<label {{ $attributes->merge(['class' => $labelClasses]) }}>
    {{ $value ?? $slot }}
</label>
