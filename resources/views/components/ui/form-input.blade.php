@props([
    'name',
    'value' => '',
    'type' => 'text',
    'placeholder' => null,
    'min' => null,
])

<input
    id="{{ $name }}"
    name="{{ $name }}"
    type="{{ $type }}"
    value="{{ $value }}"
    @if (! is_null($min)) min="{{ $min }}" @endif
    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
    {{ $attributes->merge(['class' => 'block w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white placeholder:text-slate-500 shadow-sm outline-none transition duration-200 focus:border-teal-400 focus:ring-4 focus:ring-teal-400/10']) }}
>
