@props([
    'step' => null,
    'title',
    'description' => null,
])

<section {{ $attributes->merge(['class' => 'rounded-[2rem] border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-slate-950/30 ring-1 ring-inset ring-white/5 sm:p-8']) }}>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="space-y-2">
            @if ($step)
                <x-ui.badge tone="emerald">Step {{ $step }}</x-ui.badge>
            @endif

            <div class="space-y-2">
                <h2 class="text-2xl font-semibold tracking-tight text-white sm:text-3xl">
                    {{ $title }}
                </h2>

                @if ($description)
                    <p class="max-w-2xl text-sm leading-7 text-slate-300 sm:text-base">
                        {{ $description }}
                    </p>
                @endif
            </div>
        </div>

        {{ $aside ?? '' }}
    </div>

    <div class="mt-6">
        {{ $slot }}
    </div>
</section>
