@props([
    'filters' => ['search' => '', 'category' => 'all'],
])

@php
    $search = $filters['search'] ?? '';
    $active = $filters['category'] ?? 'all';

    $linkParams = [];
    if ($search !== '') {
        $linkParams['search'] = $search;
    }

    $tabs = [
        ['key' => 'all', 'label' => 'All topics'],
        ['key' => 'languages', 'label' => 'Languages'],
        ['key' => 'frameworks', 'label' => 'Frameworks'],
        ['key' => 'sdlc_models', 'label' => 'SDLC models'],
    ];
@endphp

<div class="flex flex-wrap gap-2">
    @foreach ($tabs as $tab)
        @php
            $params = $linkParams;
            $hash = '';

            if ($tab['key'] === 'all') {
                // preserve search only
            } elseif ($active === 'all') {
                $hash = match ($tab['key']) {
                    'languages' => '#doc-languages',
                    'frameworks' => '#doc-frameworks',
                    'sdlc_models' => '#doc-sdlc',
                    default => '',
                };
            } else {
                $params['category'] = $tab['key'];
            }

            $isActive = $active === $tab['key'];
        @endphp
        <a
            href="{{ route('documentation.index', $params) }}{{ $hash }}"
            class="rounded-full px-4 py-2 text-sm font-medium transition {{ $isActive ? 'bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-950/20' : 'border border-white/10 bg-slate-900/50 text-slate-200 hover:border-emerald-400/25 hover:bg-slate-900/80' }}"
        >
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
