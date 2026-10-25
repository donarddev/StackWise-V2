@props([
    'variant' => 'bullet',
    'items' => [],
    'groups' => [],
    'entries' => [],
    'emptyMessage' => 'No content available.',
    'label' => null,
])

@php
    $hasBulletOrNumbered = count($items) > 0;
    $hasGroups = count($groups) > 0;
    $hasEntries = count($entries) > 0;
    $hasContent = match ($variant) {
        'grouped' => $hasGroups,
        'definition' => $hasEntries,
        default => $hasBulletOrNumbered,
    };
@endphp

<div {{ $attributes->class(['space-y-3']) }}>
    @if (filled($label))
        <p class="font-sans text-xs font-semibold uppercase tracking-wider text-emerald-200/90">
            {{ $label }}
        </p>
    @endif

    @if ($hasContent)
        @if ($variant === 'numbered')
            <ol class="list-decimal space-y-2.5 pl-5 font-serif text-sm leading-7 text-slate-300 marker:text-teal-300/80 sm:pl-6">
                @foreach ($items as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ol>
        @elseif ($variant === 'definition')
            <dl class="space-y-4">
                @foreach ($entries as $entry)
                    @if (filled($entry['term'] ?? null) || filled($entry['description'] ?? null))
                        <div class="rounded-2xl border border-white/10 bg-slate-900/50 px-4 py-3 ring-1 ring-inset ring-white/5">
                            @if (filled($entry['term'] ?? null))
                                <dt class="font-sans text-xs font-semibold uppercase tracking-wider text-teal-200/90">
                                    {{ $entry['term'] }}
                                </dt>
                            @endif
                            @if (filled($entry['description'] ?? null))
                                <dd class="mt-2 font-serif text-sm leading-7 text-slate-300">
                                    {{ $entry['description'] }}
                                </dd>
                            @endif
                        </div>
                    @endif
                @endforeach
            </dl>
        @elseif ($variant === 'grouped')
            <div class="space-y-5">
                @foreach ($groups as $group)
                    @if (filled($group['label'] ?? null) || count($group['items'] ?? []) > 0)
                        <div class="rounded-2xl border border-white/10 bg-slate-900/40 p-4 ring-1 ring-inset ring-white/5">
                            @if (filled($group['label'] ?? null))
                                <p class="font-sans text-xs font-semibold uppercase tracking-wider text-emerald-200/90">
                                    {{ $group['label'] }}
                                </p>
                            @endif
                            @if (count($group['items'] ?? []) > 0)
                                <ul @class([
                                    'mt-3 list-disc space-y-2 pl-5 font-serif text-sm leading-7 text-slate-300',
                                    'mt-0' => blank($group['label'] ?? null),
                                ])>
                                    @foreach ($group['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <ul class="list-disc space-y-2.5 pl-5 font-serif text-sm leading-7 text-slate-300 marker:text-teal-400/70 sm:pl-6">
                @foreach ($items as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        @endif
    @else
        <p class="rounded-2xl border border-dashed border-white/10 bg-slate-900/30 px-4 py-3 font-sans text-sm text-slate-500">
            {{ $emptyMessage }}
        </p>
    @endif
</div>
