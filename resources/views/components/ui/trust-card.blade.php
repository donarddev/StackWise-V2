@props([
    'title',
    'description',
])

<article {{ $attributes->merge(['class' => 'rounded-2xl border border-white/10 bg-slate-900/60 p-5 shadow-lg shadow-slate-950/20 transition duration-300 hover:-translate-y-0.5 hover:border-emerald-400/20 hover:bg-slate-900/80']) }}>
    <div class="space-y-3">
        <div class="flex h-10 w-10 items-center justify-center rounded-2xl border border-emerald-400/15 bg-emerald-400/10 text-emerald-200">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-300"></span>
        </div>

        <div class="space-y-2">
            <h3 class="text-base font-semibold text-white">{{ $title }}</h3>
            <p class="text-sm leading-6 text-slate-300">{{ $description }}</p>
        </div>
    </div>
</article>
