@props([
    'number',
    'title',
    'subtitle' => null,
])

<section {{ $attributes->class(['scroll-mt-24']) }}>
    <div class="flex gap-4 sm:gap-5">
        <div
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-teal-400/25 bg-teal-400/10 font-sans text-sm font-bold tabular-nums text-teal-200 shadow-sm shadow-teal-950/30 sm:h-10 sm:w-10"
            aria-hidden="true"
        >
            {{ $number }}
        </div>

        <div class="min-w-0 flex-1 space-y-4">
            <header class="space-y-1 border-b border-white/10 pb-4">
                <h4 class="font-sans text-base font-semibold tracking-tight text-white sm:text-lg">
                    {{ $title }}
                </h4>
                @if (filled($subtitle))
                    <p class="font-sans text-sm leading-6 text-slate-400">
                        {{ $subtitle }}
                    </p>
                @endif
            </header>

            <div class="break-words font-serif text-sm leading-7 text-slate-300 sm:text-[0.95rem] sm:leading-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</section>
