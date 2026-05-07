@props([
    'messages',
    'variant' => 'default',
])

@php
    $errorClasses = match ($variant) {
        'guest' => 'space-y-1 text-sm text-rose-400',
        default => 'space-y-1 text-sm text-red-600',
    };
@endphp

@if ($messages)
    <ul {{ $attributes->merge(['class' => $errorClasses]) }} role="alert">
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
