@props(['items', 'title' => 'Why Not This?'])

<article class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/30">
    <h2 class="text-lg font-semibold text-white">{{ $title }}</h2>
    <ul class="mt-4 space-y-3 text-sm text-slate-300">
        @forelse ($items as $item)
            <li class="rounded-2xl border border-white/10 bg-slate-900/70 p-4">{{ $item }}</li>
        @empty
            <li class="rounded-2xl border border-white/10 bg-slate-900/70 p-4 text-slate-400">No exclusion notes were generated for this recommendation.</li>
        @endforelse
    </ul>
</article>