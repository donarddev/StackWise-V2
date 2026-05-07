@props([
    'status',
    'variant' => 'default',
])

@php
    $statusClasses = match ($variant) {
        'guest' => 'rounded-xl border border-emerald-400/25 bg-emerald-400/10 px-3 py-2 text-sm font-medium text-emerald-200',
        default => 'text-sm font-medium text-green-600',
    };
@endphp

@if ($status)
    <div {{ $attributes->merge(['class' => $statusClasses]) }} role="status">
        {{ $status }}
    </div>
@endif
