@props([
    'badge' => null,
    'title',
    'description' => null,
])

<header {{ $attributes->merge(['class' => 'space-y-5']) }}>
    @if ($badge)
        <x-ui.badge tone="teal">{{ $badge }}</x-ui.badge>
    @endif

    <div class="space-y-4">
        <h1 class="max-w-4xl text-4xl font-semibold tracking-tight text-white sm:text-5xl lg:text-6xl">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="max-w-3xl text-base leading-7 text-slate-300 sm:text-lg">
                {{ $description }}
            </p>
        @endif
    </div>
</header>
