@props([
    'alt' => 'StackWise AI Logo',
])

<img
    src="{{ asset('images/StackWise_Logo.png') }}"
    alt="{{ $alt }}"
    {{ $attributes->merge(['class' => 'object-contain']) }}
/>
