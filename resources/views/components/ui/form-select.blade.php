@props([
    'name',
    'placeholder' => null,
    'options' => [],
])

<select
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge(['class' => 'block w-full rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white shadow-sm outline-none transition duration-200 focus:border-teal-400 focus:ring-4 focus:ring-teal-400/10']) }}
>
    @if ($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach ($options as $option)
        <option value="{{ $option['value'] }}" @selected(old($name) === $option['value'])>
            {{ $option['label'] }}
        </option>
    @endforeach
</select>
