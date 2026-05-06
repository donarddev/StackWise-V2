@props([
    'name',
    'value' => '',
    'placeholder' => null,
    'rows' => 5,
])

<textarea
    id="{{ $name }}"
    name="{{ $name }}"
    rows="{{ $rows }}"
    @if ($placeholder) placeholder="{{ $placeholder }}" @endif
    {{ $attributes->merge(['class' => 'block w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white placeholder:text-slate-500 shadow-sm outline-none transition duration-200 focus:border-teal-400 focus:ring-4 focus:ring-teal-400/10']) }}
>{{ $value }}</textarea>
