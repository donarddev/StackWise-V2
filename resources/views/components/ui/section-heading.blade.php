@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'center' => false,
])

<div {{ $attributes->merge(['class' => $center ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl']) }}>
    @if ($eyebrow)
        <x-ui.badge tone="teal">{{ $eyebrow }}</x-ui.badge>
    @endif

    <div class="mt-4 space-y-4">
        <h2 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">
            {{ $title }}
        </h2>

        @if ($description)
            <p class="text-base leading-7 text-slate-300 sm:text-lg">
                {{ $description }}
            </p>
        @endif
    </div>
</div>