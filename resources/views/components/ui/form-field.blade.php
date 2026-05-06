@props([
    'name',
    'label',
    'hint' => null,
    'required' => false,
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <div class="flex items-center justify-between gap-3">
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-100">
            {{ $label }}
            @if ($required)
                <span class="ml-1 text-emerald-300">*</span>
            @endif
        </label>
    </div>

    {{ $slot }}

    @if ($hint)
        <p class="text-xs leading-5 text-slate-400">{{ $hint }}</p>
    @endif

    <x-ui.form-error :messages="$errors->get($name)" />
</div>
