@props([
    'variant' => 'default',
])

@php
    $buttonClasses = match ($variant) {
        'guest' => 'inline-flex items-center justify-center rounded-xl border border-transparent bg-gradient-to-r from-emerald-400 to-teal-500 px-5 py-2.5 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:from-emerald-300 hover:to-teal-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/60 focus:ring-offset-2 focus:ring-offset-slate-900 disabled:opacity-50',
        default => 'inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900',
    };
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => $buttonClasses]) }}>
    {{ $slot }}
</button>
