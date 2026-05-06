@props([
    'step',
    'title',
    'description',
])

<article {{ $attributes->merge(['class' => 'group relative h-full overflow-hidden rounded-3xl border border-white/10 bg-white/5 p-5 shadow-xl shadow-slate-950/20 transition duration-300 hover:-translate-y-1 hover:border-teal-400/20 hover:bg-white/10']) }}>
    <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-teal-400/40 to-transparent"></div>

    <div class="flex items-center justify-between gap-3">
        <x-ui.badge tone="teal">Step {{ $step }}</x-ui.badge>
        <span class="h-10 w-10 rounded-2xl border border-white/10 bg-slate-950/40"></span>
    </div>

    <div class="mt-5 space-y-3">
        <h3 class="text-lg font-semibold text-white">
            {{ $title }}
        </h3>

        <p class="text-sm leading-7 text-slate-300">
            {{ $description }}
        </p>
    </div>
</article>