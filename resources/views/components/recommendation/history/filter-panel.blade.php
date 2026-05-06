@props([
    'filters' => [],
    'filterOptions' => [],
])

@php
    $f = $filters;
    $opts = $filterOptions;
@endphp

<div class="rounded-3xl border border-white/10 bg-slate-900/40 p-5 shadow-xl shadow-slate-950/25">
    <form method="get" action="{{ route('recommendation.history') }}" class="space-y-4">
        <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
            <div class="min-w-0 xl:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-200" for="history-search">Search</label>
                <input
                    id="history-search"
                    name="search"
                    type="search"
                    value="{{ $f['search'] ?? '' }}"
                    placeholder="Search project, language, framework, or SDLC model..."
                    autocomplete="off"
                    class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                />
            </div>

            <div class="min-w-0">
                <label class="mb-2 block text-sm font-medium text-slate-200" for="history-sort">Sort</label>
                <select
                    id="history-sort"
                    name="sort"
                    class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                >
                    <option value="latest" @selected(($f['sort'] ?? 'latest') === 'latest')>Latest first</option>
                    <option value="oldest" @selected(($f['sort'] ?? '') === 'oldest')>Oldest first</option>
                    <option value="confidence_desc" @selected(($f['sort'] ?? '') === 'confidence_desc')>Highest confidence</option>
                    <option value="confidence_asc" @selected(($f['sort'] ?? '') === 'confidence_asc')>Lowest confidence</option>
                </select>
            </div>

            <div class="min-w-0">
                <label class="mb-2 block text-sm font-medium text-slate-200" for="history-project-type">Project type</label>
                <select
                    id="history-project-type"
                    name="project_type"
                    class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                >
                    <option value="">All types</option>
                    @foreach ($opts['project_types'] ?? [] as $type)
                        <option value="{{ $type }}" @selected(($f['project_type'] ?? null) === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0">
                <label class="mb-2 block text-sm font-medium text-slate-200" for="history-language">Language</label>
                <select
                    id="history-language"
                    name="language"
                    class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                >
                    <option value="">All languages</option>
                    @foreach ($opts['languages'] ?? [] as $lang)
                        <option value="{{ $lang }}" @selected(($f['language'] ?? null) === $lang)>{{ $lang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0">
                <label class="mb-2 block text-sm font-medium text-slate-200" for="history-framework">Framework</label>
                <select
                    id="history-framework"
                    name="framework"
                    class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                >
                    <option value="">All frameworks</option>
                    @foreach ($opts['frameworks'] ?? [] as $framework)
                        <option value="{{ $framework }}" @selected(($f['framework'] ?? null) === $framework)>{{ $framework }}</option>
                    @endforeach
                </select>
            </div>

            <div class="min-w-0">
                <label class="mb-2 block text-sm font-medium text-slate-200" for="history-sdlc">SDLC model</label>
                <select
                    id="history-sdlc"
                    name="sdlc_model"
                    class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                >
                    <option value="">All SDLC models</option>
                    @foreach ($opts['sdlc_models'] ?? [] as $sdlc)
                        <option value="{{ $sdlc }}" @selected(($f['sdlc_model'] ?? null) === $sdlc)>{{ $sdlc }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3 lg:col-span-2 xl:col-span-1">
                <div class="min-w-0">
                    <label class="mb-2 block text-sm font-medium text-slate-200" for="history-conf-min">Min confidence %</label>
                    <input
                        id="history-conf-min"
                        name="confidence_min"
                        type="number"
                        min="0"
                        max="100"
                        value="{{ $f['confidence_min'] ?? '' }}"
                        placeholder="0"
                        class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                    />
                </div>
                <div class="min-w-0">
                    <label class="mb-2 block text-sm font-medium text-slate-200" for="history-conf-max">Max confidence %</label>
                    <input
                        id="history-conf-max"
                        name="confidence_max"
                        type="number"
                        min="0"
                        max="100"
                        value="{{ $f['confidence_max'] ?? '' }}"
                        placeholder="100"
                        class="w-full min-w-0 rounded-2xl border border-white/10 bg-slate-950/60 px-4 py-3 text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                    />
                </div>
            </div>
        </div>

        @error('confidence_max')
            <x-ui.alert type="error" :message="$message" />
        @enderror

        <div class="flex flex-wrap gap-3">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-full bg-emerald-400 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-950/20 transition hover:bg-emerald-300"
            >
                Apply filters
            </button>
            <x-ui.button-link :href="route('recommendation.history')" variant="secondary">
                Clear filters
            </x-ui.button-link>
        </div>
    </form>
</div>
