@props(['score'])

@php
    $score = (int) $score;

    $tone = match (true) {
        $score >= 80 => 'border-emerald-400/20 bg-emerald-400/15 text-emerald-200',
        $score >= 60 => 'border-sky-400/20 bg-sky-400/15 text-sky-200',
        default => 'border-amber-400/20 bg-amber-400/15 text-amber-200',
    };
@endphp

<span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $tone }}">
    {{ $score }}% Confidence
</span>
