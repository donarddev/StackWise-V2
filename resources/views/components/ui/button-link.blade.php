@props([
    'href',
    'variant' => 'primary',
])

@php
    $variantClasses = [
        'primary' => 'inline-flex items-center justify-center rounded-full bg-emerald-400 px-5 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-950/20 transition duration-300 hover:-translate-y-0.5 hover:bg-emerald-300',
        'secondary' => 'inline-flex items-center justify-center rounded-full border border-white/10 bg-white/5 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/20 transition duration-300 hover:-translate-y-0.5 hover:border-emerald-400/30 hover:bg-white/10',
    ];

    $buttonClasses = $variantClasses[$variant] ?? $variantClasses['primary'];
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $buttonClasses]) }}>
    {{ $slot }}
</a>