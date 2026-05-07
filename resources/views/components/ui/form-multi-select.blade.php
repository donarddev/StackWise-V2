@props([
    'name',
    'options' => [],
    'placeholder' => null,
])

@php
    $selected = old($name, []);
    if (is_string($selected)) {
        $selected = array_filter(array_map('trim', explode(',', $selected)));
    }
    if (! is_array($selected)) {
        $selected = [];
    }
@endphp

<select
    id="{{ $name }}"
    name="{{ $name }}[]"
    multiple
    {{ $attributes->merge(['class' => 'block w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white shadow-sm outline-none transition duration-200 focus:border-teal-400 focus:ring-4 focus:ring-teal-400/10']) }}
>
    @if ($placeholder)
        <option disabled value="">{{ $placeholder }}</option>
    @endif

    @foreach ($options as $option)
        <option value="{{ $option['value'] }}" @selected(in_array($option['value'], $selected, true))>
            {{ $option['label'] }}
        </option>
    @endforeach
</select>