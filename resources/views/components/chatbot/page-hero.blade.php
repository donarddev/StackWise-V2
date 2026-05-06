@props([
    'badge' => 'Rule-Based Assistant Preview',
    'title',
    'description',
    'note',
])

<div class="relative overflow-hidden rounded-3xl border border-white/10 bg-slate-900/50 p-8 shadow-2xl shadow-slate-950/40 sm:p-10">
    <div class="pointer-events-none absolute -right-20 -top-28 h-60 w-60 rounded-full bg-emerald-400/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 -left-12 h-52 w-52 rounded-full bg-teal-400/10 blur-3xl"></div>

    <div class="relative max-w-4xl space-y-5">
        <x-ui.badge tone="teal">{{ $badge }}</x-ui.badge>

        <div class="space-y-3">
            <h1 class="text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                {{ $title }}
            </h1>
            <p class="text-base leading-7 text-slate-300 sm:text-lg">
                {{ $description }}
            </p>
        </div>

        <p class="rounded-2xl border border-white/10 bg-slate-950/40 px-4 py-3 text-sm leading-6 text-slate-400">
            {{ $note }}
        </p>
    </div>
</div>
