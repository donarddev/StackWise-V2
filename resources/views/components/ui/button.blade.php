@props([
    'variant' => 'primary',
    'type' => 'submit',
])

@php
    $variantClasses = [
        'primary' => 'inline-flex items-center justify-center rounded-full bg-emerald-400 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-950/20 transition duration-300 hover:-translate-y-0.5 hover:bg-emerald-300 focus:outline-none focus:ring-4 focus:ring-emerald-400/20',
        'secondary' => 'inline-flex items-center justify-center rounded-full border border-white/10 bg-white/5 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/20 transition duration-300 hover:-translate-y-0.5 hover:border-emerald-400/30 hover:bg-white/10 focus:outline-none focus:ring-4 focus:ring-teal-400/10',
    ];

    $buttonClasses = $variantClasses[$variant] ?? $variantClasses['primary'];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $buttonClasses]) }}>
    {{ $slot }}
</button>
