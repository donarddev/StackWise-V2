<div {{ $attributes->merge(['class' => 'space-y-5']) }}>
    <a
        href="{{ route('google.redirect') }}"
        class="group inline-flex w-full items-center justify-center gap-3 rounded-xl border border-white/15 bg-slate-950/40 px-4 py-3 text-sm font-semibold text-slate-100 shadow-lg shadow-slate-950/20 transition hover:border-emerald-300/50 hover:bg-slate-900/80 hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-400/50 focus:ring-offset-2 focus:ring-offset-slate-900"
    >
        <span class="flex h-7 w-7 items-center justify-center rounded-full border border-white/10 bg-white text-sm font-bold text-slate-950 shadow-sm">
            G
        </span>
        <span>{{ __('Continue with Google') }}</span>
    </a>

    <div class="relative">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-white/10"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-slate-900 px-3 text-xs font-medium uppercase tracking-[0.2em] text-slate-500">
                {{ __('or continue with email') }}
            </span>
        </div>
    </div>
</div>
