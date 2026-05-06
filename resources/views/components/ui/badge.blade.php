@props([
    'tone' => 'emerald',
])

@php
    $toneClasses = [
        'emerald' => 'border-emerald-400/20 bg-emerald-400/10 text-emerald-200',
        'teal' => 'border-teal-400/20 bg-teal-400/10 text-teal-200',
        'slate' => 'border-white/10 bg-white/5 text-slate-200',
        'amber' => 'border-amber-400/20 bg-amber-400/10 text-amber-200',
    ];

    $badgeClasses = $toneClasses[$tone] ?? $toneClasses['emerald'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium tracking-wide ' . $badgeClasses]) }}>
    {{ $slot }}
</span>