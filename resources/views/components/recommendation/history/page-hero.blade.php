@props([
    'title',
    'description',
])

<div class="relative overflow-hidden rounded-3xl border border-white/10 bg-slate-900/45 p-8 shadow-2xl shadow-slate-950/35 sm:p-10">
    <div class="pointer-events-none absolute -right-16 -top-24 h-56 w-56 rounded-full bg-emerald-400/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-20 -left-10 h-48 w-48 rounded-full bg-teal-400/10 blur-3xl"></div>

    <div class="relative max-w-4xl space-y-5">
        <x-ui.badge tone="teal">Decision Archive</x-ui.badge>

        <div class="space-y-3">
            <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                {{ $title }}
            </h1>
            <p class="text-base leading-7 text-slate-300 sm:text-lg">
                {{ $description }}
            </p>
        </div>
    </div>
</div>
