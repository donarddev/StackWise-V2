@props([
    'title',
    'description',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-white/10 bg-white/5 p-4 transition duration-300 hover:border-teal-400/20 hover:bg-white/10']) }}>
    <div class="flex items-start gap-3">
        <span class="mt-1 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-teal-400/20 bg-teal-400/10 text-xs font-semibold text-teal-200">•</span>
        <div class="space-y-1">
            <h3 class="text-sm font-semibold text-white">{{ $title }}</h3>
            <p class="text-sm leading-6 text-slate-300">{{ $description }}</p>
        </div>
    </div>
</div>
