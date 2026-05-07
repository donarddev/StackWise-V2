@props([
    'disabled' => false,
    'variant' => 'default',
])

@php
    $variantClasses = match ($variant) {
        'guest' => 'rounded-xl border-white/10 bg-slate-950/55 py-2.5 text-white shadow-sm placeholder:text-slate-500 focus:border-emerald-400/60 focus:ring-2 focus:ring-emerald-400/25',
        default => 'rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
    };
@endphp

<input @disabled($disabled) {{ $attributes->merge(['class' => $variantClasses]) }}>
