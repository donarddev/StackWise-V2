@props([
    'role',
    'content',
])

@php
    $isUser = $role === 'user';
@endphp

<div class="flex w-full {{ $isUser ? 'justify-end' : 'justify-start' }}">
    <div
        class="max-w-[min(100%,32rem)] rounded-2xl px-4 py-3 text-sm leading-6 shadow-md {{ $isUser ? 'rounded-br-md bg-emerald-400 text-slate-950' : 'rounded-bl-md border border-white/10 bg-slate-900/80 text-slate-200' }}"
    >
        <p class="mb-1.5 text-[11px] font-semibold uppercase tracking-wider {{ $isUser ? 'text-slate-900/70' : 'text-emerald-200/90' }}">
            {{ $isUser ? 'You' : 'StackWise Assistant' }}
        </p>
        <p class="whitespace-pre-wrap break-words">{{ $content }}</p>
    </div>
</div>
