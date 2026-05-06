@props(['checked' => false, 'disabled' => false])

<input @checked($checked) @disabled($disabled) {{ $attributes->merge(['type' => 'checkbox', 'class' => 'h-4 w-4 rounded border-white/15 bg-slate-950/70 text-emerald-400 shadow-sm focus:ring-2 focus:ring-emerald-400/25 focus:ring-offset-0']) }}>
