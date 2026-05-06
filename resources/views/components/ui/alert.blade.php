@props(['type' => 'success', 'message'])

@php
    $styles = match ($type) {
        'error' => 'border-rose-400/20 bg-rose-400/10 text-rose-100',
        'warning' => 'border-amber-400/20 bg-amber-400/10 text-amber-100',
        default => 'border-emerald-400/20 bg-emerald-400/10 text-emerald-100',
    };
@endphp

<div class="rounded-2xl border px-4 py-3 text-sm {{ $styles }}">
    {{ $message }}
</div>
