@props([
    'name' => '',
    'size' => 'md', // sm|md|lg
])

@php
    $normalized = trim((string) $name);
    $parts = preg_split('/\s+/', $normalized) ?: [];
    $first = $parts[0] ?? '';
    $last = count($parts) > 1 ? $parts[count($parts) - 1] : '';
    $initials = strtoupper(mb_substr($first, 0, 1).mb_substr($last, 0, 1));
    if ($initials === '') {
        $initials = 'U';
    }

    $sizes = [
        'sm' => 'h-9 w-9 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-12 w-12 text-base',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->merge(['class' => "grid place-items-center rounded-full bg-gradient-to-br from-emerald-400/25 via-teal-400/10 to-sky-400/15 ring-1 ring-inset ring-white/10 text-white font-semibold {$sizeClass}"]) }}>
    {{ $initials }}
</div>

