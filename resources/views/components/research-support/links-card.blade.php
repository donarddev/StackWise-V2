@props([
    'title' => 'Open Access Paper Search Links',
    'links' => [],
    'emptyMessage' => 'No suggestions generated yet.',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-white/10 bg-slate-900/70 p-4']) }}>
    <h3 class="text-sm font-semibold text-white">{{ $title }}</h3>
    <p class="mt-1 text-xs text-slate-400">Search links only. No automatic paper downloads.</p>

    @if (count($links) > 0)
        <ul class="mt-3 space-y-2 text-sm">
            @foreach ($links as $link)
                <li>
                    @if (filter_var($link, FILTER_VALIDATE_URL))
                        <a
                            href="{{ $link }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="break-all text-emerald-300 underline decoration-emerald-400/40 underline-offset-4 transition hover:text-emerald-200"
                        >
                            {{ $link }}
                        </a>
                    @else
                        <span class="text-slate-300">{{ $link }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @else
        <p class="mt-3 text-sm text-slate-400">{{ $emptyMessage }}</p>
    @endif
</div>
